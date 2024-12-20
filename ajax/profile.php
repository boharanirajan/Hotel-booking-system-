<?php
require('../Admin/inc/essentials.php');
require('../Admin/inc/db_config.php');

date_default_timezone_set("Asia/kathmandu");

if (isset($_POST['info_form'])) {
    $frm_data = filteration($_POST);
    session_start();

    // Checking for duplicate phone numbers
    $u_exit = select(
        "SELECT * FROM `users` WHERE `phone_no` = ? AND `id` != ? LIMIT 1",
        [$frm_data['phonenum'], $_SESSION['uid']],
        "ss"
    );

    if (mysqli_num_rows($u_exit) != 0) {
        echo 'phone_already';
        exit();
    }

    // Update user information
    $query = "UPDATE `users` SET `name` = ?, `phone_no` = ?, `address` = ?, `dob` = ? WHERE `id` = ?";
    $values = [$frm_data['name'], $frm_data['phonenum'], $frm_data['address'], $frm_data['dob'], $_SESSION['uid']];

    if (update($query, $values, 'sssss')) {
        echo 1;
    } else {
        echo 0;
    }
}




// profile change
session_start();
header('Content-Type: application/json');

if (isset($_POST['info_form'])) {
    $frm_data = filteration($_POST);

    // Checking for duplicate phone numbers
    $u_exit = select(
        "SELECT * FROM `users` WHERE `phone_no` = ? AND `id` != ? LIMIT 1",
        [$frm_data['phonenum'], $_SESSION['uid']],
        "ss"
    );

    if (mysqli_num_rows($u_exit) != 0) {
        echo json_encode(['status' => 'error', 'message' => 'phone_already']);
        exit();
    }

    // Update user information
    $query = "UPDATE `users` SET `name` = ?, `phone_no` = ?, `address` = ?, `dob` = ? WHERE `id` = ?";
    $values = [$frm_data['name'], $frm_data['phonenum'], $frm_data['address'], $frm_data['dob'], $_SESSION['uid']];

    if (update($query, $values, 'sssss')) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error']);
    }
}

if (isset($_POST['profile_form'])) {
    // Upload user image
    $img = uploadUserImage($_FILES['profile']);
    
    if ($img == 'inv_img') {
        echo json_encode(['status' => 'error', 'message' => 'inv_img']);
        exit();
    } else if ($img == 'upd_failed') {
        echo json_encode(['status' => 'error', 'message' => 'upd_failed']);
        exit();
    }

    // Fetch existing image and delete...
    $u_exit = select("SELECT `profile` FROM `users` WHERE `id` = ? LIMIT 1", [$_SESSION['uid']], "i");
    if ($u_exit && mysqli_num_rows($u_exit) > 0) {
        $u_fetch = mysqli_fetch_assoc($u_exit);
        deleteImage($u_fetch['profile'], USER_FOLDER);
    }

    // Update user profile image in the database
    $query = "UPDATE `users` SET `profile` = ? WHERE `id` = ?";
    $values = [$img, $_SESSION['uid']];

    if (update($query, $values, 'si')) {
        $_SESSION['upic'] = $img;
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error']);
    }
    exit();
}


// // pasword change
// if (isset($_POST['pass_form'])) {
//     $frm_data = filteration($_POST);
//     session_start();

//     // Check if the new password matches the confirm password
//     if ($frm_data['new_pass'] !== $frm_data['confirm_pass']) {
//         echo 'mismatch';
//         exit();
//     }

//     // Update user password in the database
//     $query = "UPDATE `users` SET `password` = ? WHERE `id` = ? LIMIT 1";
//     $values = [$frm_data['new_pass'], $_SESSION['uid']];

//     if (update($query, $values, 'si')) {
//         echo 1; // Success
//     } else {
//         echo 0; // Failed
//     }
// }



?>