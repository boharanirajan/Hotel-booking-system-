<?php
require('../Admin/inc/essentials.php');
require('../Admin/inc/db_config.php');

if (isset($_POST['check_availability'])) {
    $frm_data = filteration($_POST);
    $status = "";
    $result = "";

    // Check-in and check-out validation
    $today_date = new DateTime(date("Y-m-d"));
    $checkin_date = new DateTime($frm_data['check_in']);
    $checkout_date = new DateTime($frm_data['check_out']);

    // Validate dates
    if ($checkin_date >= $checkout_date) {
        $status = $checkin_date == $checkout_date ? 'check_in_out_equal' : 'check_out_earlier';
        $result = json_encode(["status" => $status]);
    } else if ($checkin_date < $today_date) {
        $status = 'check_in_earlier';
        $result = json_encode(["status" => $status]);
    }

    // Check booking availability if status is blank, else return the error
    if ($status !== '') {
        echo $result;
        exit();
    }

    session_start();

    // Get the user ID from the session
    $user_id = $_SESSION['uid']; // Adjusted to match the session variable naming

    // Check if the user has already made a booking for the same room and dates
    $existing_booking_query = "SELECT COUNT(*) AS `existing_booking` 
                               FROM `booking_order` 
                               WHERE `user_id` = ? 
                               AND `room_id` = ? 
                               AND `check_in` = ? 
                               AND `check_out` = ?";
    $existing_values = [$user_id, $_SESSION['room']['id'], $frm_data['check_in'], $frm_data['check_out']];
    $existing_booking = mysqli_fetch_assoc(select($existing_booking_query, $existing_values, 'iiss'));

    if ($existing_booking['existing_booking'] > 0) {
        // User already has a booking for the same room and date range
        $status = 'already_booked';
        $result = json_encode(['status' => $status, 'message' => 'You have already booked this room for the selected dates.']);
        echo $result;
        exit();
    }

    // Check for overlapping bookings
    $overlap_query = "SELECT COUNT(*) AS `total_bookings` 
                      FROM `booking_order` 
                      WHERE `booking_status` = ? 
                      AND `room_id` = ? 
                      AND `check_out` > ? 
                      AND `check_in` < ?";
    $values = ['booked', $_SESSION['room']['id'], $frm_data['check_in'], $frm_data['check_out']];
    $overlap_result = mysqli_fetch_assoc(select($overlap_query, $values, 'siss'));

    // Check if the room is available based on the bookings
    if ($overlap_result['total_bookings'] > 0) {
        $status = 'unavailable';
        $result = json_encode(['status' => $status, 'message' => 'No rooms available for the selected dates.']);
        echo $result;
        exit();
    }

    /// Calculate payment and days if room is available
    $count_days = date_diff($checkin_date, $checkout_date)->days;
    $payment = $_SESSION['room']['price'] * $count_days;
    $_SESSION['room']['payment'] = $payment;
    $_SESSION['room']['available'] = true;

    $result = json_encode([
        "status" => 'available',
        "days" => $count_days,
        "payment" => $payment
    ]);

    echo $result;
}
?>
