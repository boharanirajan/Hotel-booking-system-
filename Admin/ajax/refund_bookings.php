<?php
require('../inc/essentials.php'); // Include the file with the select() and update() functions
require('../inc/db_config.php');

if (isset($_POST['get_bookings']))
 {

    $frm_data = filteration($_POST);

    // SQL query to retrieve bookings
    $query = "SELECT bo.*, bd.* 
    FROM `booking_order` bo 
    INNER JOIN `booking_details` bd ON bo.booking_id = bd.booking_id
    WHERE (bo.order_id LIKE ? OR bd.phone_no LIKE ? OR bd.user_name LIKE ?) 
    AND (bo.booking_status = ? AND bo.refund = ?)
    ORDER BY bo.booking_id ASC";

// Prepare parameters
$searchParam = "%{$frm_data['search']}%";
$params = [$searchParam, $searchParam, $searchParam, "canceled", 0];
$types = 'ssssi'; // Three strings, two integers

// Call the select function
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
                    <br><b>Name:</b> {$data['user_name']}
                    <br><b>Phone No:</b> {$data['phone_no']}
                </td>
                <td>
                    <b>Room:</b> {$data['room_name']}
                    <br><b>Check In:</b> $checkin
                    <br><b>Check Out:</b> $checkout
                    <br><b>Date:</b> $date
                    <br><b>रु.{$data['trans_amount']}</b> <!-- Ensure correct field -->
                </td>
                <td>
                    <button onclick='refund_booking({$data['booking_id']})' type='button' 
                    class='btn text-white btn-success fw-bold shadow-none'>
                        <i class='bi bi-cash-stack'></i> Refund
                    </button>
                </td>
            </tr>
        ";
        $i++;
    }

    echo $table_data;
}

 }


//  cancel_booking
if (isset($_POST['refund_booking'])) {

    $frm_data = filteration($_POST);
    
    $query = "UPDATE `booking_order` SET  `refund`=? WHERE booking_id=?";
    $values = [ 1, $frm_data['booking_id']];
    
    $res = update($query, $values, 'ii');
    echo $res; // You may want to return a success or error message based on the $res value
}



?>