<?php
require('Admin/inc/db_config.php');
require('Admin/inc/essentials.php');

date_default_timezone_set("Asia/Kathmandu");
session_start();

// Check if user is logged in
if (!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
    redirect('room.php');
    exit;
}

// Handle booking and payment request
if (isset($_POST['pay_now'])) {
    // Generate order ID and get user and room information
    $ORDER_ID = 'ORD' . $_SESSION['uid'] . random_int(11111, 99999);
    $CUST_ID = $_SESSION['uid'];
    $TAMOUNT = $_SESSION['room']['payment']; // Amount to be paid
   

    $frm_data = filteration($_POST);

    // Insert booking into `booking_order` table
    $query1 = "INSERT INTO `booking_order` (`user_id`, `room_id`, `check_in`, `check_out`, 
                `arrival`,  `booking_status`, `order_id`, `transaction_id`, 
                `trans_amount`, `trans_status`, `trans_res_message`, `datetime`) 
                VALUES (?, ?, ?, ?, ?, ?,  ?, ?, ?, ?, ?, NOW())";
    
    $result1 = insert($query1, [
        $CUST_ID, 
        $_SESSION['room']['id'], 
        $frm_data['check_in'],
        $frm_data['check_out'],
        0,
        'pending', // Initial booking status
        $ORDER_ID,
        '', // Empty transaction ID for now
        0,  // Amount (set to 0 initially; updated upon success)
        'pending', // Transaction status as pending
        'Waiting for payment' // Initial status message
    ], 'iisssssssss');

    if ($result1) {
        // Get the booking ID of the last inserted booking
        global $con;
        $booking_id = mysqli_insert_id($con);

        // Generate a unique transaction ID
        $transaction_id = 'TRANS' . strtoupper(uniqid());

        // Assume payment is successful (you can replace this with actual payment processing logic)
        $payment_success = true; // Change this based on your payment gateway response

        // Update the booking order with the transaction ID and status
        $update_query = "UPDATE `booking_order` SET `transaction_id` = ?, `trans_amount` = ?, 
                         `trans_status` = ?, `trans_res_message` = ? WHERE `order_id` = ?";
        
        if ($payment_success) {
            // If payment is successful
            $update_result = insert($update_query, [
                $transaction_id,
                $TAMOUNT,
                'success',
                'Payment received',
                $ORDER_ID
            ], 'sssss');

            // Insert booking details into `booking_details` table
            $query2 = "INSERT INTO `booking_details` (`booking_id`,`user_id`, `room_name`, `price`, 
                        `total_pay`, `user_name`, `phone_no`, `address`) 
                        VALUES (?, ?,?, ?, ?, ?, ?, ?)";
            
            $result2 = insert($query2, [
                $booking_id,
                $CUST_ID,
                $_SESSION['room']['name'],
                $_SESSION['room']['price'],
                $TAMOUNT,
              
                $frm_data['name'],
                $frm_data['phone_no'],
                $frm_data['address']
            ], 'iisissss');

            if ($result2) {
                // Update booking status to "booked"
                $final_update_query = "UPDATE `booking_order` SET `booking_status` = 'booked' WHERE `order_id` = ?";
                insert($final_update_query, [$ORDER_ID], 's');

                // Redirect to pay_status.php with success message
                redirect('pay_status.php?order=' . $ORDER_ID . '&status=success');
                exit;
            } else {
                echo 'Failed to insert booking details.';
            }
        } else {
            // If payment failed
            $update_result = insert($update_query, [
                $transaction_id,
                $TAMOUNT,
                'failed',
                'Payment failed',
                $ORDER_ID
            ], 'sssss');

            // Update booking status to "booking failed"
            $final_update_query = "UPDATE `booking_order` SET `booking_status` = 'booking failed' WHERE `order_id` = ?";
            insert($final_update_query, [$ORDER_ID], 's');

            // Redirect to pay_status.php with failure message
            redirect('pay_status.php?order=' . $ORDER_ID . '&status=failed');
            exit;
        }
    } else {
        echo 'Failed to create booking order.';
    }
}
?>
