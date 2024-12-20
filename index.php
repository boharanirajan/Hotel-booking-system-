<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Home page</title>

  <?php require('inc/link.php'); ?>

  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
  <link rel="stylesheet" href="CSS/style.css">
  <style>
    /* Set height for swiper container */
    .swiper-size {
      height: 400px;
      /* Adjust the height of the entire slider */
    }

    /* Set the height for the images */
    .custom-slider-image {
      height: 100%;
      /* Let the image fill the swiper container */
      object-fit: cover;
      /* Maintain aspect ratio, fill the space, crop if necessary */
    }

    /* Responsive design: smaller height on mobile */
    @media (max-width: 768px) {
      .swiper-size {
        height: 250px;
        /* Adjust height for smaller screens */
      }
    }
  </style>
</head>

<body class="bg-Light">
  <!-- ------------------header----------- -->
  <?php require('inc/header.php'); ?>

  <!-- Swiper/slider -->
  <div class="container-fluid px-lg-4 mt-4 ">
    <div class="swiper mySwiper swiper-size">
      <div class="swiper-wrapper">
        <div class="swiper-slide">
          <img src="image/image5.jpg" class="w-100 d-block custom-slider-image" />
        </div>
        <div class="swiper-slide">
          <img src="image/image6.jpg" class="w-100 d-block" />
        </div>
        <div class="swiper-slide">
          <img src="image/image7.jpg" class="w-100 d-block custom-slider-image" />
        </div>
        <div class="swiper-slide">
          <img src="image/image8.jpg" class="w-100 d-block custom-slider-image" />
        </div>
        <div class="swiper-slide">
          <img src="image/image9.jpg" class="w-100 d-block custom-slider-image" />
        </div>
        <div class="swiper-slide">
          <img src="image/image1.jpg" class="w-100 d-block custom-slider-image" />
        </div>
        <div class="swiper-slide">
          <img src="image/image10.jpg" class="w-100 d-block" />
        </div>
        <div class="swiper-slide">
          <img src="image/image2.jpg" class="w-100 d-block custom-slider-image" />
        </div>
        <div class="swiper-slide">
          <img src="image/image3.jpg" class="w-100 d-block custom-slider-image" />
        </div>
        <div class="swiper-slide">
          <img src="image/image4.jpg" class="w-100 d-block custom-slider-image" />
        </div>
      </div>
    </div>
  </div>

  

 <!-- --------- OUR ROOMS ------------------------- -->
 <h2 class="mt-5 pt-4 text-center fm-bold h-font"> ROOMS</h2>
 <div class="container">
  <div class="row">
    <?php
    // Fetch active rooms that are not removed, ordered by average rating (high to low), limited to 3
    $room_res = select("SELECT r.*, AVG(rr.rating) AS avg_rating 
                        FROM `rooms` r 
                        LEFT JOIN `rating_review` rr ON r.id = rr.room_id 
                        WHERE r.status = ? AND r.remove = ? 
                        GROUP BY r.id 
                        ORDER BY avg_rating DESC 
                        LIMIT 3", [1, 0], 'ii');

    while ($room_data = mysqli_fetch_assoc($room_res)) {
      // Get features of the room
      $fea_q = mysqli_query($con, "SELECT f.name FROM `feature` f INNER JOIN `rooms_feature` rfea ON f.id = rfea.feature_id WHERE rfea.room_id = '$room_data[id]'");
      $feature_data = "";
      while ($fea_row = mysqli_fetch_assoc($fea_q)) {
        $feature_data .= "<span class='badge bg-light text-dark text-wrap lh-base me-1 mb-1'>$fea_row[name]</span>";
      }

      // Get facilities of the room
      $fac_q = mysqli_query($con, "SELECT f.name FROM `facility` f INNER JOIN `rooms_facilities` rfac ON f.id = rfac.facility_id WHERE rfac.room_id = '$room_data[id]'");
      $facilities_data = "";
      while ($fac_row = mysqli_fetch_assoc($fac_q)) {
        $facilities_data .= "<span class='badge bg-light text-dark text-wrap lh-base me-1 mb-1'>$fac_row[name]</span>";
      }

      // Get Thumbnail image for the room
      $room_thumb = ROOMS_IMG_PATH . "thumbnail.jpg"; // Default thumbnail
      $thum_q = mysqli_query($con, "SELECT * FROM `rooms_images` WHERE `room_id` = '$room_data[id]' AND `thumb` = '1'");
      if (mysqli_num_rows($thum_q) > 0) {
        $thum_res = mysqli_fetch_assoc($thum_q);
        $room_thumb = ROOMS_IMG_PATH . $thum_res['image']; // Custom thumbnail
      }

      // Check if the website is not in shutdown mode for the booking button
      $book_btn = "";
      if (!$settings_r['shutdown']) {
        $login = 0;
        if (isset($_SESSION['login']) && $_SESSION['login'] == true) {
          $login = 1;
        }
        $book_btn = "<button onclick='checkloginbook($login, $room_data[id])' class='btn btn-sm text-white custom-bg shadow-none'>Book now</button>";
      }

      // Fetch room rating (average)
      $rating_q = "SELECT AVG(rating) AS avg_rating FROM rating_review WHERE room_id = '$room_data[id]'";
      $rating_res = mysqli_query($con, $rating_q);
      $rating_data = "";
      $rating_fetch = mysqli_fetch_assoc($rating_res);

      if ($rating_fetch['avg_rating'] != null) {
        $avg_rating = $rating_fetch['avg_rating'];
        $full_stars = floor($avg_rating);
        $half_star = ($avg_rating - $full_stars) >= 0.5 ? 1 : 0;
        $empty_stars = 5 - $full_stars - $half_star;

        $rating_data = "<div class='rating mb-4'>
                        <h6 class='mb-1'>Rating</h6>
                        <span class='badge rounded bg-light'>";
        
        for ($i = 0; $i < $full_stars; $i++) {
          $rating_data .= "<i class='bi bi-star-fill text-warning'></i>";
        }
        if ($half_star) {
          $rating_data .= "<i class='bi bi-star-half text-warning'></i>";
        }
        for ($i = 0; $i < $empty_stars; $i++) {
          $rating_data .= "<i class='bi bi-star text-muted'></i>";
        }
        $rating_data .= "</span></div>";
      }

      // Output room card
      echo <<<data
        <div class="col-lg-4 col-md-6 my-3">
          <div class="card border-0 shadow" style="max-width: 350px; margin: auto;">
            <img src="$room_thumb" class="card-img-top" alt="Simple Room">
            <div class="card-body">
              <h5 class="card-title">$room_data[name]</h5>
              <h6 class="mb-4">रु. $room_data[price] per night</h6>
              <div class="features mb-2">
                <h6 class="mb-1">Features</h6>
                $feature_data
              </div>
              <div class="facilities mb-2">
               <h6 class="mb-1">Facilities</h6>
                $facilities_data
              </div>
              <div class="guests mb-2">
                <h6 class="mb-1">Guests</h6>
                <span class="badge bg-light text-dark text-wrap lh-base">$room_data[adult] Adult</span>
                <span class="badge bg-light text-dark text-wrap lh-base">$room_data[children] Children</span>
              </div>
              $rating_data
              <div class="d-flex justify-content-evenly mb-2">
                $book_btn
                <a href='room_details.php?id={$room_data['id']}' class='btn btn-sm btn-outline-dark shadow-none'>More details</a>
              </div>
            </div>
          </div>
        </div>
      data;
    }
    ?>
  </div>
</div>

  <!-- More Rooms link -->
  <div class="col-lg-12 text-center mt-5">
    <a href="room.php" class="btn btn-sm btn-outline-dark rounded-0 fm-bold shadow-none"> More Rooms</a>
  </div>

  

  <!-- --------- OUR ROOMS END ------------------------- -->

  <!-- --------- password reset modal ------------------------- -->
  <div class="modal fade" id="recoveryModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <form id="recovery_form">
          <div class="modal-header">
            <h5 class="modal-title d-flex align-items-center">
              <i class="bi bi-shield-lock fs-3 me-2"></i>Set up New Password
            </h5>
          </div>
          <div class="modal-body">
            <div class="mb-4">
              <label class="form-label">New Password</label>
              <input type="password" name="pass" class="form-control shadow-none" required>
              <!-- Hidden inputs for email and token -->
              <input type="hidden" name="email"> <!-- Missing email input added here -->
              <input type="hidden" name="token">
            </div>
            <div class="mb-2 text-end">
              <button type="button" class="btn shadow-none mb-2" data-bs-dismiss="modal">
                Cancel
              </button>
              <button type="submit" class="btn btn-dark shadow-none">Submit</button>
            </div>
          </div>
        </form>
      </div>
    </div>
</div>




  <!-- -------------- Footer ---------------------- -->
  <?php require('inc/footer.php'); ?>

                <!-- recovery_form -->
  <?php 
if (isset($_GET['type']) && $_GET['type'] === 'account_recovery'
 && isset($_GET['email']) && isset($_GET['token'])) {
  // Sanitize and filter the input data
  $data = filteration($_GET);
  $t_date = date('Y-m-d'); 

  // Debugging: Log the received data
  error_log("Received data: " . json_encode($data));

  // Select user based on email and token
  $query = select("SELECT * FROM `users` WHERE `email` = ? AND `token` = ? AND `t_expire` = ? LIMIT 1", 
        [$data['email'], $data['token'], $t_date], 
        'sss');

    // If the query returns a valid user
    if (mysqli_num_rows($query) == 1) {

        // Sanitize the email and token for safe output in JS
        $email = htmlspecialchars($data['email'], ENT_QUOTES, 'UTF-8');
        $token = htmlspecialchars($data['token'], ENT_QUOTES, 'UTF-8');

        // Output JavaScript to display the modal and populate it with data
        echo <<<showModal
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                console.log("Opening modal with email: '$email' and token: '$token'");
                
                var myModal = document.getElementById('recoveryModal');
                
                if (myModal) {
                    var emailInput = myModal.querySelector("input[name='email']");
                    var tokenInput = myModal.querySelector("input[name='token']");
                    
                    // Ensure the inputs exist before setting values
                    if (emailInput && tokenInput) {
                        emailInput.value = "$email";
                        tokenInput.value = "$token";

                        // Show the modal using Bootstrap's modal instance
                        let modal = bootstrap.Modal.getOrCreateInstance(myModal);
                        modal.show();
                    } else {
                        console.error("Email or token input fields not found in the modal.");
                    }
                } else {
                    console.error("Recovery modal not found in the DOM.");
                }
            });
        </script>
        showModal;

        // Display success alert
        echo <<<showModal
        <script>
            alert('success', 'Valid link! You can reset your password.');
            // You can also show your modal here as before
        </script>
        showModal;
    } else {
        // Display error alert if no user found or link is invalid
       
        echo <<<showModal
        <script>
            alert("error", "Invalid or expired link!");
            // You can also show your modal here as before
        </script>
        showModal;
       
    }
}
?>



 

  <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
  
  <script>
    // Initialize Swiper
   // Initialize Swiper
  var swiper = new Swiper('.mySwiper', {
    loop: true,
    pagination: {
      el: '.swiper-pagination',
      clickable: true,
    },
    navigation: {
      nextEl: '.swiper-button-next',
      prevEl: '.swiper-button-prev',
    },
    autoplay: {
      delay: 2000, // Set delay for 2 seconds
      disableOnInteraction: false, // Continue autoplay after user interaction
    },
  });
    
    
    
    
    // recovery account 
   document.addEventListener('DOMContentLoaded', function() {
  let recovery_form = document.getElementById('recovery_form');

    if (recovery_form) {
        recovery_form.addEventListener('submit', (e) => {
            e.preventDefault();  // Prevent default form submission behavior

            // Create FormData object to hold form data
            let data = new FormData();
            data.append('email', recovery_form.elements['email'].value);
            data.append('token', recovery_form.elements['token'].value);
            data.append('pass', recovery_form.elements['pass'].value);
            data.append('recover_user', '');  

            // Hide the modal after submission
            let recoveryModal = document.getElementById('recoveryModal');
            let modal = bootstrap.Modal.getInstance(recoveryModal);
            modal.hide();

            // Create XMLHttpRequest for AJAX call
            let xhr = new XMLHttpRequest();
            xhr.open("POST", 'login.php', true);  // Ensure this is the correct endpoint

            // Handle response from the server
            xhr.onload = function() {
                if (this.responseText == 'failed') {
                    alert('error', "Account reset failed!");
                } else {
                    alert('success', "Account reset successfully");
                    recovery_form.reset();
                }
            };

            // Handle errors
            xhr.onerror = function() {
                alert('error', "An error occurred during the request.");
            };

            // Send the form data
            xhr.send(data);
        });
    } else {
        console.error('Element with ID "recovery_form" not found.');
    }
});


  </script>
</body>

</html>