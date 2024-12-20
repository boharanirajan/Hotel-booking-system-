<?php
require('Admin/inc/essentials.php');  // Include essential functions
require('Admin/inc/db_config.php');   // Include database configuration

// Start the session at the top of the script
session_start();

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get user input and trim any extra spaces
    $input = trim($_POST['recommendation']);
    $output = "";  // Initialize the output variable to store the recommendations

    // Check if the input is not empty
    if (!empty($input)) {
        // Corrected SQL Query for matching room name, features, or facilities
        $query = "
        SELECT r.id, r.name, r.price, r.adult, r.children, 
            GROUP_CONCAT(DISTINCT f.name SEPARATOR ', ') AS features, 
            GROUP_CONCAT(DISTINCT fa.name SEPARATOR ', ') AS facilities 
        FROM rooms r
        LEFT JOIN rooms_feature rf ON r.id = rf.room_id
        LEFT JOIN feature f ON rf.feature_id = f.id
        LEFT JOIN rooms_facilities rfa ON r.id = rfa.room_id
        LEFT JOIN facility fa ON rfa.facility_id = fa.id
        WHERE r.status = 1 AND r.remove = 0 
            AND (
                f.name LIKE ? OR 
                fa.name LIKE ? OR
                r.name LIKE ?
            )
        GROUP BY r.id";

        // Prepare and execute the query, using wildcards for partial matches
        $input_wildcard = '%' . $input . '%';  // Wildcard search for the user input
        if ($stmt = $con->prepare($query)) {  // Prepare the query
            $stmt->bind_param('sss', $input_wildcard, $input_wildcard, $input_wildcard);  // Bind parameters
            $stmt->execute();  // Execute the query
            $result = $stmt->get_result();  // Get the query result

            // Check if any rooms were found that match the search criteria
            if ($result->num_rows > 0) {
                // Loop through each room and build the recommendation card
                while ($row = $result->fetch_assoc()) {
                    // Get the room's thumbnail image
                    $room_thumb = ROOMS_IMG_PATH . "thumbnail.jpg";
                    $thum_q = mysqli_query($con, "SELECT * FROM `rooms_images` WHERE `room_id` = '{$row['id']}' AND `thumb` = '1'");
                    
                    if (mysqli_num_rows($thum_q) > 0) {
                        $thum_res = mysqli_fetch_assoc($thum_q);
                        $room_thumb = ROOMS_IMG_PATH . $thum_res['image'];  // Update thumbnail image path
                    }

                    // Prepare feature and facility data for the room
                    $feature_data = !empty($row['features']) ? $row['features'] : 'No features available';
                    $facilities_data = !empty($row['facilities']) ? $row['facilities'] : 'No facilities available';

                    // Booking button logic (if website is not in shutdown mode)
                    $book_btn = "<a href='book_room.php?id={$row['id']}' class='btn btn-sm btn-dark shadow-none'>Book Now</a>";

                    // Append room card to output
                    $output .= "
                    <div class='card mb-4 border-0 shadow'>
                        <div class='row g-0 p-3 align-items-center'>
                            <div class='col-md-5 md-lg-0 mb-md-0 mb-3 px-0'>
                                <img src='$room_thumb' class='img-fluid rounded'>
                            </div>
                            <div class='col-md-5 px-lg-3 px-md-3 px-0'>
                                <h5 class='mb-3'>{$row['name']}</h5>
                                <div class='features mb-4'>
                                    <h6 class='mb-1'>Features</h6>
                                    <p>$feature_data</p>
                                </div>
                                <div class='facilities mb-3'>
                                    <h6 class='mb-1'>Facilities</h6>
                                    <p>$facilities_data</p>
                                </div>
                                <div class='guests'>
                                    <h6 class='mb-1'>Guests</h6>
                                    <span class='badge bg-light text-dark text-wrap lh-base'>{$row['adult']} Adult</span>
                                    <span class='badge bg-light text-dark text-wrap lh-base'>{$row['children']} Children</span>
                                </div>
                            </div>
                            <div class='col-md-2 text-center'>
                                <h6 class='mb-4'>रु. {$row['price']} per night</h6>
                                $book_btn
                                <a href='room_details.php?id={$row['id']}' class='btn btn-sm btn-outline-dark shadow-none'>More details</a>
                            </div>
                        </div>
                    </div>";
                }
            } else {
                // If no rooms are found, display this message
                $output = "<h5 class='text-center'>No matching rooms found!</h5>";
            }

            // Close the prepared statement
            $stmt->close();
        } else {
            // Query preparation failed
            $output = "<h5 class='text-center'>Error in executing the search query. Please try again later.</h5>";
        }
    } else {
        // If the input is empty, prompt the user to enter a keyword
        $output = "<h5 class='text-center'>Please enter a keyword to get recommendations!</h5>";
    }

    // Output the final response (either recommendation cards or no results message)
    echo $output;
}
?>
