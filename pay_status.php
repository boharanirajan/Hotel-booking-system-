<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Status</title>
    <?php require('inc/link.php'); ?>
    <link rel="stylesheet" href="CSS/style.css">
</head>

<body class="bg-light">
    <?php require('inc/header.php'); ?>
    <div class="container">
        <h2 class="fw-bold">Booking Status</h2>
        <?php
        

        // Check if user is logged in
        if (!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
            redirect('index.php');
            exit;
        }

        // Filter GET parameters and validate
        $frn_data = filteration($_GET);
        if (!isset($frn_data['order'])) {
            redirect('index.php');
            exit;
        }

        // Prepare the SQL query to fetch booking information
        $booking_q = "SELECT bo.booking_id, bo.room_id, bo.order_id, bo.transaction_id, bo.trans_amount, bo.trans_status, bo.trans_res_message, 
             bo.datetime, bo.rate_review, bd.room_name, bd.user_name, bd.phone_no 
            FROM `booking_order` bo 
            INNER JOIN `booking_details` bd ON bo.booking_id = bd.booking_id 
            WHERE bo.order_id = ? AND bo.user_id = ?";
        
        $booking_res = select($booking_q, [$frn_data['order'], $_SESSION['uid']], 'si');

        // Check if there is any booking found
        if (mysqli_num_rows($booking_res) == 0) {
            echo "<p>No booking found for this order ID.</p>";
            exit;
        }

        // Display booking details
        while ($booking_data = mysqli_fetch_assoc($booking_res)) {
            echo "
    <div class='card mb-4'>
        <div class='card-body'>
            <h5 class='card-title'>Order ID: " . htmlspecialchars($booking_data['order_id']) . "</h5>
            <p class='card-text'>Booking ID: <strong>" . htmlspecialchars($booking_data['booking_id']) . "</strong></p>
            <p class='card-text'>Room ID: <strong>" . htmlspecialchars($booking_data['room_id']) . "</strong></p>
            <p class='card-text'>Room Name: <strong>" . htmlspecialchars($booking_data['room_name']) . "</strong></p>
            <p class='card-text'>Username: <strong>" . htmlspecialchars($booking_data['user_name']) . "</strong></p>
            <p class='card-text'>Phone No: <strong>" . htmlspecialchars($booking_data['phone_no']) . "</strong></p>
            <p class='card-text'>Status: <strong>" . htmlspecialchars($booking_data['trans_status']) . "</strong></p>
            <p class='card-text'>Amount Paid: " . htmlspecialchars($booking_data['trans_amount']) . "</p>
            <p class='card-text'>Message: " . htmlspecialchars($booking_data['trans_res_message']) . "</p>
            <p class='card-text'>Booking Date: " . htmlspecialchars($booking_data['datetime']) . "</p>
        </div>
    </div>";
        }

        // Display success or failure message
        if (isset($frn_data['status'])) {
            if ($frn_data['status'] === 'success') {
                echo "<p class='alert alert-success'>Your payment was successful!</p>";
            } elseif ($frn_data['status'] === 'failed') {
                echo "<p class='alert alert-danger'>Your payment failed. Please try again.</p>";
            }
        }
        ?>
    </div>
    <?php require('inc/footer.php'); ?>
</body>

</html>
