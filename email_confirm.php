<?php
require('Admin/inc/essentials.php');
require('Admin/inc/db_config.php');

// Check if the required parameters are present in the URL
if (isset($_GET['type']) && $_GET['type'] === 'email_confirm' && isset($_GET['email']) && isset($_GET['token'])) {
    // Sanitize and filter the input data
    $data = filteration($_GET);

    // Debugging: Log the received data
    error_log("Received data: " . json_encode($data));

    // Select user based on email and token
    $query = select("SELECT * FROM `users` WHERE `email`=? AND `token`=?", 
        [$data['email'], $data['token']], 'ss');

    // Check if the query returned a user
    if ($query && mysqli_num_rows($query) == 1) {
        $fetch = mysqli_fetch_assoc($query);

        // Debugging: Log the fetched user data
        error_log("Fetched user: " . json_encode($fetch));

        // Check if the email is already verified
        if ($fetch['is_varified'] == 1) {
            echo "<script>alert('Email already verified!');</script>";
        } else {
            // Update the user's verification status
            $update = update("UPDATE `users` SET `is_varified`=1 WHERE `id`=?", [$fetch['id']], 'i');

            if ($update) {
                echo "<script>alert('Email verified successfully!');</script>";
            } else {
                echo "<script>alert('Email verification failed! Please try again later.');</script>";
            }
        }
    } else {
        // Invalid link or user not found
        echo "<script>alert('Invalid link or user not found!');</script>";
    }
} else {
    // Missing required parameters
    echo "<script>alert('Missing required parameters!');</script>";
}

// Redirect to the index page after processing
echo "<script>setTimeout(function() { window.location.href = 'index.php'; }, 2000);</script>";
?>
