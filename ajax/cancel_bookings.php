<?php
require('../Admin/inc/essentials.php');
require('../Admin/inc/db_config.php');
session_start();

if (!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
    redirect('index.php');
}

if (isset($_POST['cancel_booking'])) {
    $frm_data = filteration($_POST);
    $query = "UPDATE `booking_order` SET `booking_status` = ?, `refund` = ? 
              WHERE `booking_id` = ? AND `user_id` = ?";
    $values = ['canceled', 0, $frm_data['id'], $_SESSION['uid']];
    $result = update($query, $values, 'siii');

    if ($result) {
        echo 1; // Success
    } else {
        echo 0; // Failure
    }
}
?>
