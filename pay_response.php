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

// Function to regenerate session
function regenerate_session($uid) {
    $user_q = select("SELECT * FROM `users` WHERE `id` = ? LIMIT 1", [$uid], 'i');
    $user_fetch = mysqli_fetch_assoc($user_q);

    if ($user_fetch) {
        $_SESSION['login'] = true;
        $_SESSION['uid'] = $user_fetch['id'];
        $_SESSION['uname'] = $user_fetch['name'];
        $_SESSION['upic'] = $user_fetch['profile'];
        $_SESSION['uphone'] = $user_fetch['phone_no'];
    }
}

// Handle the transaction update based on Khalti response
if (isset($_POST['pidx'])) {
    // Perform Khalti payment lookup
    $curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_URL => 'https://a.khalti.com/api/v2/epayment/lookup/',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => json_encode(['pidx' => $_POST['pidx']]),
        CURLOPT_HTTPHEADER => [
            'Authorization: Key 9ef51430bd8243f7a6fa0e1a9b932653', // Replace with your actual secret key
            'Content-Type: application/json',
        ],
    ]);

    $response = curl_exec($curl);
    if (curl_errno($curl)) {
        echo "Curl error: " . curl_error($curl);
        exit;
    }

    $decoded_response = json_decode($response, true);
    if ($decoded_response === null) {
        echo "Failed to decode JSON response. Original response: " . $response;
        exit;
    }

    if (isset($decoded_response['state']) && $decoded_response['state']['name'] === "Completed") {
        $booking_status = 'booked';
        $transaction_id = $decoded_response['transaction_id'] ?? '';
        $trans_amount = $decoded_response['amount'] ?? 0;
        $trans_status = 'TXN_Success';
        $trans_res_message = $decoded_response['merchant']['name'] ?? 'Payment Successful';
    } else {
        $booking_status = 'payment failed';
        $transaction_id = $_POST['transaction_id'] ?? '';
        $trans_amount = $_POST['trans_amount'] ?? 0;
        $trans_status = 'TXN_Failed';
        $trans_res_message = $decoded_response['state']['name'] ?? 'Transaction Failed';
    }

    global $con;

    $upd_query = "UPDATE `booking_order` SET `booking_status` = ?, `transaction_id` = ?, `trans_amount` = ?, `trans_status` = ?, `trans_res_message` = ? WHERE `order_id` = ?";
    $stmt = mysqli_prepare($con, $upd_query);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "ssssss", $booking_status, $transaction_id, $trans_amount, $trans_status, $trans_res_message, $_POST['order_id']);
        if (mysqli_stmt_execute($stmt)) {
            if ($booking_status === 'booked') {
                regenerate_session($_SESSION['uid']);
            }
        } else {
            echo "Error executing statement: " . mysqli_stmt_error($stmt);
        }
        mysqli_stmt_close($stmt);
    } else {
        echo "Error preparing statement: " . mysqli_error($con);
    }

    if ($booking_status === 'payment failed') {
        redirect('pay_status.php?order=' . $_POST['order_id']);
        exit;
    }

    curl_close($curl);
}
?>
