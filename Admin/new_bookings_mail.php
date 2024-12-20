<?php
require('inc/essentials.php'); // Include the file with the select() and update() functions
require('inc/db_config.php');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Function to send email
function sendMail($email, $subject, $body)
{
    require 'PHPMailer/Exception.php';
    require 'PHPMailer/PHPMailer.php';
    require 'PHPMailer/SMTP.php';

    $mail = new PHPMailer(true);

    try {
        // SMTP configuration
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'nirajanbohara731@gmail.com';
        $mail->Password   = 'dfns gmjd xikn vibp'; // Replace with your actual password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port       = 465;

        // Email settings
        $mail->setFrom('nirajanbohara731@gmail.com', 'Nirajan');
        $mail->addAddress($email);
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $body;

        $mail->send();
        return true; // Email sent successfully
    } catch (Exception $e) {
        error_log('Mail Error: ' . $e->getMessage()); // Log error for debugging
        return false; // Email sending failed
    }
}


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



// if (isset($_POST['assign_room'])) {
//     $frm_data = filteration($_POST); // Sanitize incoming data

//     // SQL query to update booking order and details
//     $query = "
//     UPDATE `booking_order` bo 
//     INNER JOIN `booking_details` bd 
//     ON bo.booking_id = bd.booking_id
//     SET bo.arrival = ?, bo.rate_review =?, bd.room_no = ?
//     WHERE bo.booking_id = ?"; // Include the WHERE clause

//     // Prepare values for the query
//     $values = [1,0, $frm_data['room_no'], $frm_data['booking_id']];
    
//     // Execute the update function and check the result
//     $res = update($query, $values, 'iisi'); // Assuming 'i' for integer and 's' for string types

//     header('Content-Type: application/json'); // Set content type to JSON

//     // Prepare response based on the result of the database update
//     if ($res == 2) {
//         echo json_encode(['status' => 'success', 'message' => 'Room assigned successfully! Booking finalized.']);
//     } else {
//         echo json_encode(['status' => 'error', 'message' => 'Failed to assign room. Please try again.']); // Return failure message
//     }
//     exit; // End the script after sending the response
// }




// Handle room assignment request
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
    $values = [1, 0, $frm_data['room_no'], $frm_data['booking_id']];

    // Execute the update function and check the result
    $res = update($query, $values, 'iisi'); // Assuming 'i' for integer and 's' for string types

    if ($res == 2) {
        // Room assigned successfully, now send the confirmation email
        $booking_id = $frm_data['booking_id'];
        
        // Fetch booking details
        $query_booking = "SELECT * FROM `booking_details` WHERE `booking_id` = ?";
        $booking_res = select($query_booking, [$booking_id], 'i');
        
        if ($booking_res && mysqli_num_rows($booking_res) > 0) {
            $booking = mysqli_fetch_assoc($booking_res);
            $user_id = $booking['user_id'];
            
            // Fetch user details for email
            $query_user = "SELECT * FROM `users` WHERE `id` = ?";
            $user_res = select($query_user, [$user_id], 'i');
            
            if ($user_res && mysqli_num_rows($user_res) > 0) {
                $user = mysqli_fetch_assoc($user_res);
                $email = $user['email'];
                $name = $user['name'];
                
                
                $subject = "Room Assigned for Your Booking";
                $body = "Dear {$user['name']},<br><br>
                        Your room has been successfully assigned.<br><br>
                        Booking Details:<br>
                        Room Name: {$booking['room_name']}<br>
                        Price: {$booking['price']}<br>
                        Total Pay: {$booking['total_pay']}<br>
                       Room Number: {$frm_data['room_no']}<br>
                        User Name: {$user['name']}<br>
                        Phone No: {$user['phone_no']}<br>
                        Address: {$user['address']}<br><br>
                        Thank you for booking with us!";
    
                // Send confirmation email
                if (sendMail($email, $subject, $body)) {
                    echo json_encode(['status' => 'success', 'message' => 'Room assigned successfully and confirmation email sent!']);
                } else {
                    echo json_encode(['status' => 'error', 'message' => 'Room assigned, but email failed to send.']);
                }
            } else {
                echo json_encode(['status' => 'error', 'message' => 'User not found for booking details.']);
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Booking details not found.']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to assign room. Please try again.']);
    }
    exit;
}




// //  cancel_booking
// if (isset($_POST['cancel_booking'])) {

//     $frm_data = filteration($_POST);
    
//     $query = "UPDATE `booking_order` SET `booking_status`=?, `refund`=? WHERE booking_id=?";
//     $values = ['canceled', 0, $frm_data['booking_id']];
    
//     $res = update($query, $values, 'sii');
//     echo $res; // You may want to return a success or error message based on the $res value
// }


// Handle the cancel booking request
// Handle booking cancellation
if (isset($_POST['cancel_booking'])) {
    $frm_data = filteration($_POST); // Sanitize input
    $booking_id = $frm_data['booking_id'];

    // Fetch booking details
    $query = select("SELECT * FROM `booking_order` WHERE `booking_id`=?", [$booking_id], 'i');
    if ($query && mysqli_num_rows($query) == 1) {
        $booking = mysqli_fetch_assoc($query);

        // Update the booking status to canceled
        $update = update("UPDATE `booking_order` SET `booking_status`=?, `refund`=? WHERE `booking_id`=?", ['canceled', 0, $booking_id], 'sii');
        if ($update) {
            // Send cancellation email to the user
            $user_id = $booking['user_id'];
            $user_query = select("SELECT `email`, `name` FROM `users` WHERE `id`=?", [$user_id], 'i');
            if ($user_query && mysqli_num_rows($user_query) == 1) {
                $user = mysqli_fetch_assoc($user_query);
                $email = $user['email'];
                $name  = $user['name'];

                // Prepare the cancellation email
                $subject = "Booking Canceled";
                $body = "Dear $name,<br><br>Your booking with ID $booking_id has been successfully canceled.<br><br>Thank you for your understanding.";

                if (sendMail($email, $subject, $body)) {
                    echo json_encode(['status' => 'success', 'message' => 'Booking canceled and email sent!']);
                } else {
                    echo json_encode(['status' => 'error', 'message' => 'Booking canceled, but failed to send email!']);
                }
            } else {
                echo json_encode(['status' => 'error', 'message' => 'User not found!']);
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Booking cancellation failed!']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Booking not found!']);
    }
}
?>