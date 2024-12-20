<?php
require('Admin/inc/essentials.php');
require('Admin/inc/db_config.php');
date_default_timezone_set("Asia/Kathmandu");

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function sendmail($email, $token, $type)
{
    if ($type == "email_confirm") {
        $page = 'email_confirm.php';
        $subject = "Account Verification";
        $content = "confirm your email";
    }
    
    else 
    {
        $page = 'index.php';
        $subject = "Account Reset Link";
        $content = "confirm your account"; 
    }

    require 'PHPMailer/Exception.php';
    require 'PHPMailer/PHPMailer.php';
    require 'PHPMailer/SMTP.php';

    $mail = new PHPMailer(true);

    try {
        // SMTP server configuration
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
        $mail->Subject = $subject; // Use the actual subject variable, not a string
        $mail->Body = 'Click the link to confirm your email: <br>
        <a href="' . SITE_URL . $page . '?type=' . $type . '&email=' . $email . '&token=' . $token . '">Confirm Email</a>';
    

        $mail->send();
        return true; // Success
    } catch (Exception $e) {
        error_log('Mail Error: ' . $e->getMessage());
        return false;
    }
}


// register form

if (isset($_POST['register'])) {
    // Sanitize input data
    $data = filteration($_POST);

    // Function to validate password strength
    function validatePassword($password) {
        // Ensure the password contains:
        // - At least 8 characters
        // - One uppercase letter
        // - One lowercase letter
        // - One digit
        // - One special character
        $pattern = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/';
        return preg_match($pattern, $password);
    }

    // Validate password strength
    if (!validatePassword($data['pass'])) {
        echo json_encode(['status' => 'error', 'message' => 'pass_invalid']);
        exit();
    }

    // Check for password mismatch
    if ($data['pass'] !== $data['cpass']) {
        echo json_encode(['status' => 'error', 'message' => 'pass_mismatch']);
        exit();
    }

    // Check if user already exists
    $u_exit = select(
        "SELECT * FROM `users` WHERE `email` = ? OR `phone_no` = ? LIMIT 1",
        [$data['email'], $data['phonenum']],
        "ss"
    );

    if (mysqli_num_rows($u_exit) !== 0) {
        $existing_user = mysqli_fetch_assoc($u_exit);
        $error_type = ($existing_user['email'] === $data['email']) ? 'email_already' : 'phone_already';
        echo json_encode(['status' => 'error', 'message' => $error_type]);
        exit();
    }

    // Upload user image
    $img = uploadUserImage($_FILES['profile']);
    if ($img === 'inv_img' || $img === 'upd_failed') {
        echo json_encode(['status' => 'error', 'message' => $img]); // Return specific error
        exit();
    }

    // Generate email confirmation token
    $token = bin2hex(random_bytes(16));

    // if (!sendmail($data['email'], $token, "email_confirm")) {
    //     echo json_encode(['status' => 'error', 'message' => 'mail_failed']);
    //     exit();
    // }

    // Hash the password securely
   // $hashed_password = password_hash($data['pass'], PASSWORD_BCRYPT);
   // Store the password in plain text (not recommended)
     $plain_password = $data['pass']; // Password is not hashed
   
    // Prepare the SQL query for user insertion
    $query = "INSERT INTO `users` (`name`, `email`, `phone_no`, `profile`, `address`, `dob`, 
    `password`, `token`) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = mysqli_prepare($con, $query);

    if ($stmt) {
        // Bind parameters and execute the statement
        mysqli_stmt_bind_param($stmt, "ssssssss", $data['name'], $data['email'], 
            $data['phonenum'], $img, $data['address'], $data['dob'], $plain_password, $token);

        if (mysqli_stmt_execute($stmt)) {
            $inserted_id = mysqli_insert_id($con); // Retrieve the inserted user ID
            echo json_encode(['status' => 'success', 'id' => $inserted_id, 'name' => $data['name']]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'ins_failed']);
            error_log("Insert Error: " . mysqli_error($con)); // Log error details
        }

        mysqli_stmt_close($stmt); // Close the statement
    } else {
        echo json_encode(['status' => 'error', 'message' => 'ins_failed']);
        error_log("Statement Preparation Error: " . mysqli_error($con)); // Log error details
    }
}


// Login form
if (isset($_POST['login'])) {

    // Filter and sanitize input
    $data = filteration($_POST);
   
    // Check if user exists (by email or phone number)
    $u_exists = select(
        "SELECT * FROM users WHERE email = ? OR phone_no = ? LIMIT 1",
        [$data['email_mob'], $data['email_mob']],
        "ss"
    );

    // If no user is found, return 'inv_email_mob'
    if (mysqli_num_rows($u_exists) == 0) {
        echo 'inv_email_mob';
    } else {
        // Fetch user data
        $u_fetch = mysqli_fetch_assoc($u_exists);

        // Check if the user's email is verified
        if ($u_fetch['is_varified'] == 0) {
            echo 'not_verified';
        }
        // Check if the user's account is inactive
        else if ($u_fetch['status'] == 0) {
            echo 'inactive';
        }
        // Check if the password is valid
        else {
            if ($data['pass']!==$u_fetch['password']) {
                echo 'invalid_pass';
            } else {
                // Start session and store user data
                session_start();
                $_SESSION['login'] = true;
                $_SESSION['uid'] = $u_fetch['id'];
                $_SESSION['uname'] = $u_fetch['name'];
                $_SESSION['upic'] = $u_fetch['profile'];
                $_SESSION['uphone'] = $u_fetch['phone_no'];
                $_SESSION['uemail']=$u_fetch['email'];
                echo 1; // Success
            }
        }
    }
}


// Forgot password form
if (isset($_POST['forgot_pass'])) {

    // Filter and sanitize input
    $data = filteration($_POST);

    // Check if user exists (by email)
    $u_exists = select(
        "SELECT * FROM `users` WHERE `email` = ? LIMIT 1", 
        [$data['email']],  
        "s"
    );

    // If no user is found, return 'inv_email'
    if (mysqli_num_rows($u_exists) == 0) {
        echo 'inv_email';
    } else {
        $u_fetch = mysqli_fetch_assoc($u_exists);

        // Check if the user's email is verified
        if ($u_fetch['is_varified'] == 0) {
            echo 'not_verified';
        }
        // Check if the user's account is inactive
        else if ($u_fetch['status'] == 0) {
            echo 'inactive';
        } else {
            // Generate token and send reset email
            $token = bin2hex(random_bytes(16));
            if (!sendmail($data['email'], $token, 'account_recovery')) {
                echo 'mail_failed';
            } else {
                $date = date("Y-m-d"); // Correct date format
                // Update the user's token and expiration date in the database
                $query = mysqli_query($con, "UPDATE `users` SET `token`='$token',
                 `t_expire`='$date' WHERE id='$u_fetch[id]'");

                if ($query) {
                    echo 1; // Success
                } else {
                    echo 'upd_failed';
                }
            }
        }
    }
}


// Recovery password form
if (isset($_POST['recover_user'])) {

    // Filter and sanitize input
    $data = filteration($_POST);

    $query = "UPDATE users SET `password`=?, `token`=?, `t_expire`=? 
    WHERE `email`=? AND `token`=?";

    $values=[$data['pass'],null,null,$data['email'],$data['token']];

    if(update($query,$values,'sssss')){

        echo 1;
    }
    else
    {
        echo 'failed';
    }


}