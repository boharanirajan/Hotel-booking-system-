<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/Exception.php';
require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';

$mail = new PHPMailer(true);

try {
    // Server settings
    //$mail->SMTPDebug = SMTP::DEBUG_SERVER;  // Enable verbose debug output
    $mail->isSMTP();                        // Send using SMTP
    $mail->Host       = 'smtp.gmail.com';   // Set the SMTP server to send through
    $mail->SMTPAuth   = true;               // Enable SMTP authentication
    $mail->Username   = 'nirajanbohara731@gmail.com';  // SMTP username (corrected)
    $mail->Password   = 'dfns gmjd xikn vibp'; // SMTP password (use app-specific password)
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;  // Enable implicit TLS encryption
    $mail->Port       = 465;                // TCP port to connect to

    //Recipients
    $mail->setFrom('nirajanbohara731@gmail.com', 'Nirajan');  // Corrected sender email
    $mail->addAddress('nirajanbohara66@gmail.com');  // Add a recipient (corrected)

    // Content
    $mail->isHTML(true);  // Set email format to HTML
    $mail->Subject = 'Testing mail';
    $mail->Body    = 'This is the HTML message body <b>in bold!</b>';

    $mail->send();
    echo 'Message has been sent';
} catch (Exception $e) {
    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
}
