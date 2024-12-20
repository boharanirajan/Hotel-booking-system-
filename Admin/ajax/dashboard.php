<?php
require('../inc/essentials.php'); // Include the file with helper functions
require('../inc/db_config.php');  // Include the database configuration file

if (isset($_POST['bookings_analytics'])) {

    $condition = "";
    $frm_data = filteration($_POST); // Sanitize the input data
    
    if($frm_data['periods'] == 1) {
        // Last 30 days
        $condition = "WHERE `datetime` BETWEEN NOW() - INTERVAL 30 DAY AND NOW()";
    } else if($frm_data['periods'] == 2) {
        // Last 90 days
        $condition = "WHERE `datetime` BETWEEN NOW() - INTERVAL 90 DAY AND NOW()";
    } else if($frm_data['periods'] == 3) {
        // Last 1 year
        $condition = "WHERE `datetime` BETWEEN NOW() - INTERVAL 1 YEAR AND NOW()";
    }
    
    $result = mysqli_fetch_assoc(mysqli_query($con, "SELECT 
        COUNT(booking_id) AS 'total_bookings',                          
        SUM(CASE WHEN booking_status != 'pending' AND booking_status != 'payment failed' THEN trans_amount END)
         AS 'total_amt',
        
        COUNT(CASE WHEN booking_status = 'booked' AND arrival = 1 THEN 1 END) AS 'active_bookings', 
        SUM(CASE WHEN booking_status = 'booked' AND arrival = 1 THEN trans_amount END) AS 'active_amount', 
        
        COUNT(CASE WHEN booking_status = 'canceled' AND refund = 1 THEN 1 END) AS 'cancelled_bookings', 
        SUM(CASE WHEN booking_status = 'canceled' AND refund = 1 THEN trans_amount END) AS 'cancelled_amount' 
        FROM `booking_order` $condition
    "));
    
    echo json_encode($result); // Convert result to JSON format and output
}

//users_analytics
  if (isset($_POST['users_analytics'])) {

    $condition = "";
    $frm_data = filteration($_POST); // Sanitize the input data
    
    if($frm_data['periods'] == 1) {
        // Last 30 days
        $condition = "WHERE `datetime` BETWEEN NOW() - INTERVAL 30 DAY AND NOW()";
    } else if($frm_data['periods'] == 2) {
        // Last 90 days
        $condition = "WHERE `datetime` BETWEEN NOW() - INTERVAL 90 DAY AND NOW()";
    } else if($frm_data['periods'] == 3) {
        // Last 1 year
        $condition = "WHERE `datetime` BETWEEN NOW() - INTERVAL 1 YEAR AND NOW()";
    }
    
    // Fetch total reviews
    $total_reviews = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(sr_no) as 'count' FROM `rating_review` $condition"));
    
    // Fetch total new registrations
    $total_new_reg = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(id) as 'count' FROM `users` $condition"));
    
    // Prepare the response data
    $output = [
        'total_reviews' => $total_reviews['count'],
        'total_new_reg' => $total_new_reg['count']
    ];

    // Convert result to JSON format and output
    echo json_encode($output);
}

?>
