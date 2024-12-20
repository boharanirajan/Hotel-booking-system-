<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Room Details Page</title>

    <?php require('inc/link.php'); ?>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="CSS/style.css">
</head>

<body class="bg-light">
    <!-- ------------------Header----------- -->
    <?php require('inc/header.php'); ?>

    <?php
    
    // Check if 'id' is set in the query string, otherwise redirect
    if (!isset($_GET['id'])) {
        redirect('room.php');
    }

    // Filter the incoming GET data
    $data = filteration($_GET);

    // Select the room data where id matches, status is active (1), and remove flag is not set (0)
    $room_res = select("SELECT * FROM `rooms` WHERE `id`=? AND `status`=? AND `remove`=?", [$data['id'], 1, 0], 'iii');

    // Check if the room exists, otherwise redirect
    if (mysqli_num_rows($room_res) == 0) {
        redirect('room.php');
    }

    // Fetch the room data as an associative array
    $room_data = mysqli_fetch_assoc($room_res);
    ?>

    <div class="container">
        <div class="row">
            <div class="col-12 my-5 px-4">
                <h2 class="fw-bold"><?php echo $room_data['name']; ?></h2>
                <div style="font-size: 14px;">
                    <a href="index.php" class="text-secondary text-decoration-none">Home</a>
                    <span class="text-secondary">></span>
                    <a href="room.php" class="text-secondary text-decoration-none">Rooms</a>
                </div>
            </div>

            <!-- Image Carousel Section -->
            <div class="col-lg-7 col-md-12 px-4">
                <div id="roomcarousel" class="carousel slide" data-bs-ride="carousel">
                    <div class="carousel-inner">
                        <?php
                        $img_q = mysqli_query($con, "SELECT * FROM `rooms_images` WHERE `room_id`='$room_data[id]'");

                        if (mysqli_num_rows($img_q) > 0) {
                            $active_class = 'active';  // Set the first carousel item as active
                            while ($img_res = mysqli_fetch_assoc($img_q)) {
                                $img_path = ROOMS_IMG_PATH . $img_res['image'];
                                echo "<div class='carousel-item $active_class'>
                                        <img src='$img_path' class='d-block w-100' alt='Room Image'>
                                      </div>";
                                $active_class = '';  // Remove active class after the first image
                            }
                        } else {
                            // Default image when no images are found in the database
                            $room_img = ROOMS_IMG_PATH . "thumbnail.jpg";
                            echo "<div class='carousel-item active'>
                                    <img src='$room_img' class='d-block w-100' alt='Room Image'>
                                  </div>";
                        }
                        ?>
                    </div>
                </div>
            </div>

            <!-- Room Details Section -->
            <div class="col-lg-5 col-md-12 px-4">
                <div class="card mb-4 border-0 shadow-sm">
                    <div class="card-body">
                        <?php
                        echo "<h4>रु. {$room_data['price']} per night</h4>";
                             // Rating Query
                        $rating_q = "SELECT AVG(rating) AS avg_rating 
                        FROM rating_review 
                        WHERE room_id = '$room_data[id]' 
                        ORDER BY `sr_no` DESC 
                        LIMIT 20";
                        $rating_res = mysqli_query($con, $rating_q);

                        $rating_data = "";
                        $rating_fetch = mysqli_fetch_assoc($rating_res);

                        if ($rating_fetch['avg_rating'] != null) {
                       

                        for ($i = 0; $i <$rating_fetch['avg_rating']; $i++) {
                            $rating_data .= "<i class='bi bi-star-fill text-warning'></i>";
                        }
                        }


                        echo "<div class='mb-3'>
                                $rating_data
                              </div>";

                        // Get room features
                        $fea_q = mysqli_query($con, "SELECT f.name FROM `feature` f 
                                                     INNER JOIN `rooms_feature` rfea 
                                                     ON f.id = rfea.feature_id 
                                                     WHERE rfea.room_id = '$room_data[id]'");
                        $feature_data = "";
                        while ($fea_row = mysqli_fetch_assoc($fea_q)) {
                            $feature_data .= "<span class='badge bg-light text-dark text-wrap lh-base me-1 mb-1'>
                                               {$fea_row['name']}
                                              </span>";
                        }

                        // Get room facilities
                        $fac_q = mysqli_query($con, "SELECT f.name FROM `facility` f 
                                                     INNER JOIN `rooms_facilities` rfac 
                                                     ON f.id = rfac.facility_id 
                                                     WHERE rfac.room_id = '$room_data[id]'");
                        $facilities_data = "";
                        while ($fac_row = mysqli_fetch_assoc($fac_q)) {
                            $facilities_data .= "<span class='badge bg-light text-dark text-wrap lh-base me-1 mb-1'>
                                                 {$fac_row['name']}
                                                </span>";
                        }

                        // Display features
                        echo "<div class='features mb-4'>
                                <h6 class='mb-1'>Features</h6>
                                $feature_data
                              </div>";

                        // Display facilities
                        echo "<div class='facilities mb-3'>
                                <h6 class='mb-1'>Facilities</h6>
                                $facilities_data
                              </div>";

                        // Display guests
                        echo "<div class='mb-3'>
                                <h6 class='mb-1'>Guests</h6>
                                <span class='badge bg-light text-dark text-wrap lh-base'>{$room_data['adult']} Adult</span>
                                <span class='badge bg-light text-dark text-wrap lh-base'>{$room_data['children']} Children</span>
                              </div>";

                        // Display area
                        echo "<div class='features mb-4'>
                                <h6 class='mb-1'>Area</h6>
                                <span class='badge bg-light text-dark text-wrap lh-base me-1 mb-1'>
                                  {$room_data['area']} sq. ft
                                </span>
                              </div>";
                                // button shutdown not appear
                        $book_btn=" ";
                        if(!$settings_r['shutdown'])
                        {  
                            $login = 0;
                            if (isset($_SESSION['login']) && $_SESSION['login'] == true) {
                                $login = 1;
                            }
                            // Book now button
                            echo "<button onclick='checkloginbook($login,$room_data[id])'
                             class='btn w-100 text-white custom-bg shadow-none mb-2'>Book Now</button>";
                        }    
                       
                        ?>
                    </div>
                </div>
            </div>

            <div class="col-12  mt-4 px-4">
                <div class="mb-4">
                    <h5>Description</h5>
                    <p>
                        <?php
                        echo $room_data['description']
                        ?>
                    </p>
                </div>

                <button id="recommended_rooms_button" class="btn btn-primary mt-4">
    Recommendation
</button>

<div id="rooms_data" class="row mt-5">
    <!-- Rooms will be displayed here -->
</div>





    
                </div>
            </div>
                <!-- <div>
                    <h5 class="mb-3">Reviews & Ratings</h5>
                    <div>
                        <div class="profile d-flex align-items-center mb-3">
                            <img src="Facilities/wifi.jpg" width="30px" alt="Profile Image">
                            <h6 class="m-0 ms-2">Random user1</h6>
                        </div>
                         Testimonial Paragraph -->
                        <!-- <p>
                            It looks like you're using the coverflow effect in Swiper, which creates a 3D-like transition between slides. The JavaScript you've written
                            seems correct, but there's a small typo in the class name.
                        </p> -->

                        <!-- Rating Section -->
                        <!-- <div class="rating">
                            <i class="bi bi-star-fill text-warning"></i>
                            <i class="bi bi-star-fill text-warning"></i>
                            <i class="bi bi-star-fill text-warning"></i>
                            <i class="bi bi-star-fill text-warning"></i>
                        </div> -->
                    <!-- </div>
                </div> --> 
            </div>
        </div>
    </div>

    <!-- ------Footer------- -->
   

    <script>
  function fetch_rooms(query = "") {
    let xhr = new XMLHttpRequest();
    xhr.open("GET", `recommendation_algorithm.php?fetch_rooms&query=${encodeURIComponent(query)}`, true);

    xhr.onload = function () {
        document.getElementById('rooms_data').innerHTML = this.responseText;
    };

    xhr.send();
}

// Event listener for button click (example: "Recommended Rooms" button)
document.getElementById("recommended_rooms_button").addEventListener("click", function() {
    fetch_rooms();
});
</script>
</body>

</html>