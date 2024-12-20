<?php
require('../Admin/inc/essentials.php');
require('../Admin/inc/db_config.php');


// Include PHPMailer classes
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;


// Enable error reporting for debugging


function sendmail($email, $name, $token) {
 require 'PHPMailer/Exception.php';
 require 'PHPMailer/PHPMailer.php';
 require 'PHPMailer/SMTP.php';
    $mail = new PHPMailer(true);

    try {
        // SMTP server configuration
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'nirajanbohara731@gmail.com'; // Replace with your email
        $mail->Password   = 'fnsiberxxtufliqb'; // Replace with app-specific password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port       = 465;

        // Email sender and recipient settings
        $mail->setFrom('nirajanbohara731@gmail.com', 'Nirajan'); // Replace with your name
        $mail->addAddress($email, $name);

        // Email content
        $mail->isHTML(true);
        $mail->Subject = 'Account verification link';
        $mail->Body = 'Click the link to confirm your email: <br>
            <a href="' . SITE_URL . 'email_confirm.php?email=' . urlencode($email) . 
            '&token=' . urlencode($token) . '">Confirm Email</a>';

        $mail->send();
        return true; // Return true if email is sent
    } catch (Exception $e) {
        return false;
    }
  
}


if (isset($_POST['register'])) {
    $data = filteration($_POST);

    // Check if passwords match
    if ($data['pass'] != $data['cpass']) {
        echo 'pass_mismatch';
        exit();
    }

    // Check if user exists by email or phone number
    $u_exit = select(
        "SELECT * FROM `users` WHERE `email`=? OR `phone_no`=? LIMIT 1",
        [$data['email'], $data['phonenum']],
        "ss"
    );

    if (mysqli_num_rows($u_exit) != 0) {
        $u_exit_fetch = mysqli_fetch_assoc($u_exit);
        echo ($u_exit_fetch['email'] == $data['email']) ? 'email_already' : 'phone_already';
        exit();
    }

    // Upload user image
    $img = uploadUserImage($_FILES['profile']);
    if ($img == 'inv_img') {
        echo 'inv_img';
        exit();
    } else if ($img == 'upd_failed') {
        echo 'upd_failed';
        exit();
    }

    // Generate a token for email confirmation
    $token = bin2hex(random_bytes(16)); 

    // Send email confirmation link
    if (!sendmail($data['email'], $data['name'], $token)) {
        echo 'mail_failed';
        exit();
    }

    // Hash the password for security
    $enc_pass = password_hash($data['pass'], PASSWORD_DEFAULT);

    // Insert user data into the database
    $query = "INSERT INTO `users`(`name`, `email`, `address`, `phone_no`, `dob`, `profile`, `password`, `token`) 
              VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $values = [
        $data['name'],
        $data['email'],
        $data['address'],
        $data['phonenum'],
        $data['dob'],
        $img,  // Image file name
        $enc_pass,
        $token  // Confirmation token
    ];

    if (insert($query, $values, 'ssssssss') === true) {
        echo 1;  // Success
    } else {
        echo 'ins_failed';  // Insert failed
        error_log("Database Insertion Error:"); // Log DB error
    }
}
?>
