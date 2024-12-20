<?php
require('../Admin/inc/essentials.php');
require('../Admin/inc/db_config.php');

session_start();

if (isset($_GET['fetch_rooms'])) {
    $query = isset($_GET['query']) ? "%" . $_GET['query'] . "%" : "%%";

    // Fetch rooms filtered by query
    $room_res = select(
        "SELECT r.* FROM `rooms` r
        LEFT JOIN `rooms_feature` rf ON r.id = rf.room_id
        LEFT JOIN `feature` f ON rf.feature_id = f.id
        LEFT JOIN `rooms_facilities` rfac ON r.id = rfac.room_id
        LEFT JOIN `facility` fac ON rfac.facility_id = fac.id
        WHERE (r.name LIKE ? OR f.name LIKE ? OR fac.name LIKE ?)
        AND r.status = ? AND r.remove = ? GROUP BY r.id",
        [$query, $query, $query, 1, 0],
        'sssii'
    );

    $count_rooms = 0;
    $output = "";

    while ($room_data = mysqli_fetch_assoc($room_res)) {
        // Fetch room features
        $fea_q = mysqli_query($con, "SELECT f.name FROM `feature` f INNER JOIN `rooms_feature` rfea ON f.id = rfea.feature_id WHERE rfea.room_id = '$room_data[id]'");
        $feature_data = "";
        while ($fea_row = mysqli_fetch_assoc($fea_q)) {
            $feature_data .= "<span class='badge bg-light text-dark text-wrap lh-base me-1 mb-1'>$fea_row[name]</span> ";
        }

        // Fetch room facilities
        $fac_q = mysqli_query($con, "SELECT f.name FROM `facility` f INNER JOIN `rooms_facilities` rfac ON f.id = rfac.facility_id WHERE rfac.room_id = '$room_data[id]'");
        $facilities_data = "";
        while ($fac_row = mysqli_fetch_assoc($fac_q)) {
            $facilities_data .= "<span class='badge bg-light text-dark text-wrap lh-base me-1 mb-1'>$fac_row[name]</span> ";
        }

        // Get room thumbnail image
        $room_thumb = ROOMS_IMG_PATH . "thumbnail.jpg";
        $thum_q = mysqli_query($con, "SELECT * FROM `rooms_images` WHERE `room_id` = '$room_data[id]' AND `thumb` = '1'");
        if (mysqli_num_rows($thum_q) > 0) {
            $thum_res = mysqli_fetch_assoc($thum_q);
            $room_thumb = ROOMS_IMG_PATH . $thum_res['image'];
        }

        // Booking button logic
        $book_btn = "";
        if (!isset($settings_r['shutdown']) || !$settings_r['shutdown']) {
            $login = 0;
            if (isset($_SESSION['login']) && $_SESSION['login'] == true) {
                $login = 1;
            }
            $book_btn = "<button onclick='checkloginbook($login, $room_data[id])' class='btn btn-sm w-100 text-white custom-bg shadow-none mb-2'>Book now</button>";
        }

        // Append room card to output
        $output .= "
        <div class='col-md-6'>
            <div class='card mb-4 border-0 shadow'>
                <img src='$room_thumb' class='card-img-top'>
                <div class='card-body'>
                    <h5 class='card-title fw-bold'>{$room_data['name']}</h5>
                    <p class='card-text'>
                        <strong class='d-block' style='font-size: 1.1rem;'>Features:</strong> 
                        <span style='font-size: 0.95rem;'>$feature_data</span><br>
                        
                        <strong class='d-block mt-2' style='font-size: 1.1rem;'>Facilities:</strong> 
                        <span style='font-size: 0.95rem;'>$facilities_data</span><br>
                        
                        <strong class='d-block mt-2' style='font-size: 1.1rem;'>Guests:</strong> 
                        <span style='font-size: 1rem; font-weight: bold;'>Adults: {$room_data['adult']}, Children: {$room_data['children']}</span><br>
                        
                        <strong class='d-block mt-2' style='font-size: 1.1rem;'>Price:</strong> 
                        <span style='font-size: 1.2rem; font-weight: bold; color: #000;'>रु. {$room_data['price']} per night</span>
                    </p>
                    $book_btn
                    <a href='room_details.php?id={$room_data['id']}' class='btn btn-sm btn-outline-dark shadow-none'>More details</a>
                </div>
            </div>
        </div>";
        
        $count_rooms++;
    }

    if ($count_rooms > 0) {
        echo $output;
    } else {
        echo "<h3 class='text-center'>No rooms to show!</h3>";
    }
}
?>
