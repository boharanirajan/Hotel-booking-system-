<?php
require('../inc/essentials.php'); // Include the file with the select() and update() functions
require('../inc/db_config.php');

// get bookings 
if (isset($_POST['get_bookings'])) {

    $frm_data = filteration($_POST);

    // SQL query to retrieve bookings
    $query = "SELECT bo.*, bd.* 
    FROM `booking_order` bo 
    INNER JOIN `booking_details` bd ON bo.booking_id = bd.booking_id
    WHERE (bo.order_id LIKE ? OR bd.phone_no LIKE ? OR bd.user_name LIKE ?) 
    AND (bo.booking_status = ? AND bo.arrival = ?)
    ORDER BY bo.booking_id ASC";

// Prepare parameters
$searchParam = "%{$frm_data['search']}%";
$params = [$searchParam, $searchParam, $searchParam, "booked", 0];
$types = 'ssssi'; // Three strings, two integers

/// Call the select function
$res = select($query, $params, $types); // Ensure correct argument count

    $i = 1; // Initialize row counter
    $table_data = ""; // Initialize the table data variable

    // Check if the result set is empty
    if (mysqli_num_rows($res) == 0) {
        echo "<b>No data found!<b>";
        exit; // Exit if no data is found
    }

    // Check if any bookings were found
    if (mysqli_num_rows($res) > 0) {
        while ($data = mysqli_fetch_assoc($res)) { // Fetch the data
            $date = date("d-m-Y", strtotime($data['datetime'])); // Format date
            $checkin = date("d-m-Y", strtotime($data['check_in'])); // Format check-in date
            $checkout = date("d-m-Y", strtotime($data['check_out'])); // Format check-out date

            // Generate table row data
            $table_data .= "
                <tr>
                    <td>$i</td>
                    <td>
                        <span class='badge bg-primary'>Order ID: {$data['order_id']}</span>
                        <br>
                        <b>Name:</b> {$data['user_name']}
                        <br>
                        <b>Phone No:</b> {$data['phone_no']}
                    </td>
                    <td>
                        <b>Room:</b> {$data['room_name']}
                        <br>
                        <b>Price:</b> रु.{$data['price']}
                    </td>
                    <td>
                        <b>Check In:</b> $checkin
                        <br>
                        <b>Check Out:</b> $checkout
                        <br>
                        <b>Paid:</b> {$data['trans_amount']} <!-- Ensure the correct field name -->
                        <br>
                        <b>Date:</b> $date
                    </td>
                    <td>
                        <button onclick='assign_room({$data['booking_id']})' type='button' class='mb-2 btn text-white btn-sm fw-bold custom-bg shadow-none' data-bs-toggle='modal' data-bs-target='#assign-room'> 
                            <i class='bi bi-check2-square'></i> Assign Room
                        </button>
                        <br>
                        <button onclick='cancel_booking({$data['booking_id']})' type='button' class='btn text-white btn-danger fw-bold shadow-none'> 
                            <i class='bi bi-trash-square'></i> Cancel Booking
                        </button>
                    </td>
                </tr>
            ";
            $i++;
        }
    } else {
        // No bookings found
        $table_data .= "<tr><td colspan='5' class='text-center'>No bookings found.</td></tr>";
    }

    // Output the generated table rows
    echo $table_data; // Send the constructed table rows to the front-end
}



// assign form
if (isset($_POST['assign_room'])) {
    $frm_data = filteration($_POST); // Sanitize incoming data

    // SQL query to update booking order and details
    $query = "
    UPDATE `booking_order` bo 
    INNER JOIN `booking_details` bd 
    ON bo.booking_id = bd.booking_id
    SET bo.arrival = ?, bo.rate_review =?, bd.room_no = ?
    WHERE bo.booking_id = ?"; // Include the WHERE clause

    // Prepare values for the query
    $values = [1,0, $frm_data['room_no'], $frm_data['booking_id']];
    
    // Execute the update function and check the result
    $res = update($query, $values, 'iisi'); // Assuming 'i' for integer and 's' for string types

    header('Content-Type: application/json'); // Set content type to JSON

    // Prepare response based on the result of the database update
    if ($res == 2) {
        echo json_encode(['status' => 'success', 'message' => 'Room assigned successfully! Booking finalized.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to assign room. Please try again.']); // Return failure message
    }
    exit; // End the script after sending the response
}


//  cancel_booking
if (isset($_POST['cancel_booking'])) {

    $frm_data = filteration($_POST);
    
    $query = "UPDATE `booking_order` SET `booking_status`=?, `refund`=? WHERE booking_id=?";
    $values = ['canceled', 0, $frm_data['booking_id']];
    
    $res = update($query, $values, 'sii');
    echo $res; // You may want to return a success or error message based on the $res value
}



?>