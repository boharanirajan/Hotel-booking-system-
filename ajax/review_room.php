<?php
require('../Admin/inc/essentials.php');
require('../Admin/inc/db_config.php');
session_start();

if (!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
    redirect('index.php');
}

// review and rating 
if (isset($_POST['review_form'])) {
    $frm_data = filteration($_POST);
    
    // Check if the user is logged in
    if (isset($_SESSION['uid'])) {
        $upd_query = "UPDATE `booking_order` SET `rate_review` = ? 
                      WHERE `booking_id` = ? AND `user_id` = ?";
        $upd_values = [1, $frm_data['booking_id'], $_SESSION['uid']];
        $upd_result = update($upd_query, $upd_values, 'iii');

        // Insert rating and review
        $ins_query = "INSERT INTO `rating_review`(`booking_id`, `room_id`, `user_id`, 
                      `rating`, `review`) VALUES (?,?,?,?,?)";
        $ins_values = [
            $frm_data['booking_id'], 
            $frm_data['room_id'], 
            $_SESSION['uid'], 
            $frm_data['rating'], 
            $frm_data['review']
        ];
        $ins_result = insert($ins_query, $ins_values, 'iiiis');

        // Check if both update and insert queries were successful
        if ($upd_result && $ins_result) {
            echo 1; // Success
        } else {
            echo 0; // Failure
        }
    } else {
        echo 0; // User not logged in
    }
}
?>
