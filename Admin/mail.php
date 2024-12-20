<?php
require('inc/essentials.php'); // Include the file with the select() and update() functions
require('inc/db_config.php');
date_default_timezone_set("Asia/Kathmandu");

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


// get all users
if (isset($_POST['get_users'])) {
    $res = selectAll('users');  // Assuming selectAll is a predefined function to get all users
    $i = 1;  // Used for row numbering
    $path = USER_IMG_PATH;  // Define your image path constant
    $data = "";

    while ($row = mysqli_fetch_assoc($res)) {
    
        $status = "<button onclick='toggleStatus($row[id], 0)' class='btn btn-success btn-sm shadow-none'>Active</button>";
        // Check if the user is verified
        $verified = "<span class='badge bg-warning'><i class='bi bi-x-lg'></i></span>";
        if ($row['is_varified']) {
            $verified = "<span class='badge bg-success'><i class='bi bi-check-lg'></i></span>";
        }

        // Status toggle button
        $status = "<button onclick='toggleStatus($row[id], 0)' class='btn btn-success btn-sm shadow-none'>Active</button>";
        if (!$row['status']) {
            $status = "<button onclick='toggleStatus($row[id], 1)' class='btn btn-danger btn-sm shadow-none'>Inactive</button>";
        }

          

        // Format date
        $date = date("Y-m-d", strtotime($row['datetime']));

        // Append the row's data to $data
        $data .= "
        <tr>
            <td>$i</td>
            <td>
                <img src='{$path}{$row['profile']}' width='55px'>
                <br>
                {$row['name']}
            </td>
            <td>{$row['email']}</td>
            <td>{$row['phone_no']}</td>
            <td>{$row['address']}</td>
            <td>{$row['dob']}</td>
            <td>$verified</td>
            <td>$status</td>
            <td>$date</td>
            <td>
             <button type='button' onclick='remove_users($row[id])' 
        class='btn btn-danger shadow-none btn-sm'>
        <i class='bi bi-trash'></i>
        </button>
    <button type='button' onclick='verify_users($row[id])' 
        class='btn btn-success shadow-none btn-sm'>
        <i class='bi bi-check-circle'></i>
      </button>
            </td>
        </tr>";
        
        $i++;
    }

    // Output the constructed table rows
    echo $data;
}




/// Handle the AJAX request for toggling user status
if (isset($_POST['toggleStatus'])) {
    $frm_data = filteration($_POST);  // Assuming filteration is your custom sanitization function
    $q = "UPDATE `users` SET `status`=? WHERE `id`=?";
    $v = [$frm_data['value'], $frm_data['toggleStatus']];  // Bind the parameters: new status value and user ID

    // Assuming update() is a predefined function that executes the query with the parameters
    if (update($q, $v, 'ii')) {
        echo '1';  // Success response
    } else {
        echo '0';  // Failure response
    }
}





// search users 

if (isset($_POST['search_user'])) {   
    $frm_data = filteration($_POST);
    $search_term = "%" . $frm_data['search_user'] . "%"; // Format the search term

    // Prepare the statement
    $query = "SELECT * FROM `users` WHERE `name` LIKE ?";
    $stmt = mysqli_prepare($con, $query);
    
    if (!$stmt) {
        die('Query preparation failed: ' . mysqli_error($con)); // Check for errors in preparation
    }

    // Bind the parameter
    mysqli_stmt_bind_param($stmt, 's', $search_term); 
    // Execute the statement
    if (!mysqli_stmt_execute($stmt)) {
        die('Query execution failed: ' . mysqli_stmt_error($stmt)); // Check for errors in execution
    }
    
    // Get the result set
    $result = mysqli_stmt_get_result($stmt); 
    if (!$result) {
        die('Getting result failed: ' . mysqli_stmt_error($stmt)); // Check for errors in fetching results
    }

    $i = 1; // Row numbering
    $path = USER_IMG_PATH; // Image path constant
    $data = "";

    // Check if any rows are returned
    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            // Delete button for user
            // $del_btn = "<button type='button' onclick='remove_users($row[id])' class='btn btn-danger shadow-none btn-sm'>
            //             <i class='bi bi-trash'></i>
            //             </button>";

            // Check if the user is verified
            $verified = "<span class='badge bg-warning'><i class='bi bi-x-lg'></i></span>";
            if ($row['is_varified']) {
                $verified = "<span class='badge bg-success'><i class='bi bi-check-lg'></i></span>";
            }

            // Status toggle button
            $status = "<button onclick='toggleStatus($row[id], 0)' class='btn btn-success btn-sm shadow-none'>Active</button>";
            if (!$row['status']) {
                $status = "<button onclick='toggleStatus($row[id], 1)' class='btn btn-danger btn-sm shadow-none'>Inactive</button>";
            }

            // Format date
            $date = date("Y-m-d", strtotime($row['datetime']));

            // Append the row's data to $data
            $data .= "
            <tr>
                <td>$i</td>
                <td>
                    <img src='{$path}{$row['profile']}' width='55px'>
                    <br>
                    {$row['name']}
                </td>
                <td>{$row['email']}</td>
                <td>{$row['phone_no']}</td>
                <td>{$row['address']}</td>
                <td>{$row['dob']}</td>
                <td>$verified</td>
                <td>$status</td>
                <td>$date</td>
                        <td>  <button type='button' onclick='remove_users($row[id])' 
                class='btn btn-danger shadow-none btn-sm'>
                <i class='bi bi-trash'></i>
                </button>
            <button type='button' onclick='verify_users($row[id])' 
                class='btn btn-success shadow-none btn-sm'>
                <i class='bi bi-check-circle'></i>
            </button></td>
                    </tr>";
            
            $i++;
        }
    } else {
        $data .= "<tr><td colspan='10' class='text-center'>No users found.</td></tr>"; // Handle no results
    }

    // Output the constructed table rows
    echo $data;
}










//Handle the verified user request
if (isset($_POST['verify_user'])) {
    $frm_data = filteration($_POST); // Sanitize input
    $user_id = $frm_data['user_id'];

    // Fetch user details
    $query = select("SELECT * FROM `users` WHERE `id`=?", [$user_id], 'i');
    if ($query && mysqli_num_rows($query) == 1) {
        $user = mysqli_fetch_assoc($query);

        // Check if the user is already verified
        if ($user['is_varified'] == 1) {
            // User already verified
            echo json_encode(['status' => 'error', 'message' => 'User already verified!']);
            exit;
        }

        // Update the user's verification status
        $update = update("UPDATE `users` SET `is_varified`=1 WHERE `id`=?", [$user_id], 'i');
        if ($update) {
            // Prepare and send the verification email
            $email = $user['email'];
            $name  = $user['name'];
            $subject = "Account Verified";
            $body    = "Dear $name,<br><br>Your account has been successfully verified.<br><br>Thank you!";

            if (sendMail($email, $subject, $body)) {
                // Success response
                echo json_encode(['status' => 'success', 'message' => 'Email verified successfully and confirmation sent!']);
            } else {
                // Failure in sending confirmation email
                echo json_encode(['status' => 'error', 'message' => 'Email verified, but confirmation email failed to send!']);
            }
        } else {
            // Failure in verification process
            echo json_encode(['status' => 'error', 'message' => 'Email verification failed! Please try again.']);
        }
    } else {
        // User not found or invalid
        echo json_encode(['status' => 'error', 'message' => 'Invalid user or user not found!']);
    }
} 

// Handle the user removal request

if (isset($_POST['remove_user'])) {
    $frm_data = filteration($_POST); // Sanitize input
    $user_id = $frm_data['user_id'];

    // Fetch user details
    $query = select("SELECT `email`, `name` FROM `users` WHERE `id`=?", [$user_id], 'i');
    if (!$query || mysqli_num_rows($query) == 0) {
        echo 0; // User not found
        exit;
    }

    $user = mysqli_fetch_assoc($query);
    $email = $user['email'];
    $name = $user['name'];

    // Delete user from database
    $res = delete("DELETE FROM `users` WHERE `id`=?", [$user_id], 'i');
    if ($res) {
        // Send rejection email
        $subject = "Account Rejected";
        $body = "Dear $name,<br><br>Your account has been rejected and deleted from our system.<br><br>Thank you.";
        
        // Email sending failure does not block deletion, so continue even if email fails
        sendMail($email, $subject, $body);
        
        echo 1; // Success
    } else {
        echo 0; // Deletion failed
    }
}

?>

