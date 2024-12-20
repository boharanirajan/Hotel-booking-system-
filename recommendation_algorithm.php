<?php
require('Admin/inc/essentials.php');
require('Admin/inc/db_config.php');
session_start();

// Ensure the user is logged in
if (!isset($_SESSION['uid'])) {
    die("Please login first.");
}

$uid = $_SESSION['uid'];

if (isset($_GET['fetch_rooms'])) {
    // SQL query to fetch rooms from the user's past bookings
    $stmt = $con->prepare("
        SELECT 
            r.id, r.name, r.price, r.adult, r.children,
            (SELECT image FROM `rooms_images` WHERE room_id = r.id AND thumb = 1 LIMIT 1) AS room_thumb
        FROM `booking_details` b
        INNER JOIN `rooms` r ON b.room_name = r.name
        WHERE b.user_id = ? 
        GROUP BY r.id
    ");
    $stmt->bind_param("i", $uid);
    $stmt->execute();
    $result = $stmt->get_result();

    $output = "";
    $count_rooms = 0;

    while ($room_data = $result->fetch_assoc()) {
        // Set room thumbnail or fallback to default
        $room_thumb = $room_data['room_thumb'] 
            ? ROOMS_IMG_PATH . $room_data['room_thumb'] 
            : ROOMS_IMG_PATH . "thumbnail.jpg";

        // Build room card output
        $output .= "
        <div class='col-md-3'>
            <div class='card mb-4 border-0 shadow'>
                <img src='$room_thumb' class='card-img-top'>
                <div class='card-body'>
                    <h5 class='card-title fw-bold'>{$room_data['name']}</h5>
                    <p class='card-text'>
                        <strong class='d-block mt-2' style='font-size: 1.1rem;'>Guests:</strong> 
                        <span style='font-size: 1rem; font-weight: bold;'>Adults: {$room_data['adult']}, Children: {$room_data['children']}</span><br>
                        
                        <strong class='d-block mt-2' style='font-size: 1.1rem;'>Price:</strong> 
                        <span style='font-size: 1.2rem; font-weight: bold; color: #000;'>रु. {$room_data['price']} per night</span>
                    </p>
                    <a href='room_details.php?id={$room_data['id']}' class='btn btn-sm btn-outline-dark shadow-none'>More details</a>
                </div>
            </div>
        </div>";
        $count_rooms++;
    }

    // If no rooms found
    echo $count_rooms > 0 ? $output : "<h3 class='text-center'>No past booked rooms found!</h3>";
}
?>