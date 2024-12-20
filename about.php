<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>About Page</title>
  <?php require('inc/link.php'); ?>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
  <link rel="stylesheet" href="CSS/style.css">

  <style>
    .box {
      border-top-color: var(--teal) !important;
    }

    /* Justify text */
    .justify-text {
      text-align: justify;
    }



    /* Ensure the image fits perfectly within its container */
    .img-fluid {
      width: 100%;
      /* Take full width of the container */
      height: auto;
      /* Maintain aspect ratio */
    }
  </style>
</head>

<body class="bg-Light">
  <!-- Header -->
  <?php require('inc/header.php'); ?>

  <div class="my-5 px-4">
    <h2 class="fw-bold text-center h-font">ABOUT US</h2>
    <div class="h-line bg-dark mb-5"></div>
  </div>

  <div class="container">
    <!-- Image section -->
    <div class="row mb-4">
      <div class="col-12">
        <img src="image/about/hotel system.jpg" class="img-fluid" alt="Hotel Management System">
      </div>
    </div>

    <!-- Content section -->
    <div class="row">
      <div class="col-12">
        <h3 class="mb-3">Hotel Management System</h3>
        <p class="justify-text">
          A hotel management system is a comprehensive software solution designed to streamline hotel
          operations, enhance guest experiences, and improve overall management efficiency. Key features
          of an HMS include reservation management, which allows hotels to handle online bookings seamlessly,
          manage room availability, and accommodate group reservations. Front desk operations are optimized
          with efficient check-in and check-out processes, guest registration, and room assignments. The
          system also includes payment processing capabilities, ensuring secure transactions through
          integrated payment gateways and support for various payment methods. An essential aspect of an HMS
          is customer relationship management, which enables hotels to create detailed guest profiles,
          manage loyalty programs effectively. This fosters personalized experiences that enhance guest
          satisfaction. Reporting and analytics tools provide valuable insights into occupancy rates,
          revenue management, and operational performance, helping hotels make informed business decisions.
          The HMS also supports multi-channel distribution, integrating with online travel agencies to
          maximize visibility and avoid overbookings. Additional features may include event and conference
          management capabilities, staff management tools, and mobile access for staff and guests.
          By incorporating these features, a hotel management system not only improves operational
          efficiency but also enhances the overall guest experience, driving customer loyalty and revenue
          growth.
        </p>

      </div>
    </div>
  </div>


  <div class="container mt-5">
    <div class="row">
      <div class="col-lg-3 col-md-6 mb-4 px-4">
        <div class="bg-white rounded shadow p-4 border-top-4 text-center box">
          <img src="image/image6.jpg" width="70px" alt="Rooms Icon">
          <h4>100+ ROOMS</h4>
        </div>
      </div>

      <div class="col-lg-3 col-md-6 mb-4 px-4">
        <div class="bg-white rounded shadow p-4 border-top-4 text-center box">
          <img src="image/image6.jpg" width="70px" alt="Customers Icon">
          <h4>200+ CUSTOMERS</h4>
        </div>
      </div>

      <div class="col-lg-3 col-md-6 mb-4 px-4">
        <div class="bg-white rounded shadow p-4 border-top-4 text-center box">
          <img src="image/image6.jpg" width="70px" alt="Reviews Icon">
          <h4>150+ REVIEWS</h4>
        </div>
      </div>
      <div class="col-lg-3 col-md-6 mb-4 px-4">
        <div class="bg-white rounded shadow p-4 border-top-4 text-center box">
          <img src="image/image6.jpg" width="70px" alt="Staff Icon">
          <h4>200+ STAFFS</h4>
        </div>
      </div>
    </div>
  </div>

  <!-- Management Team -->
  <h3 class="my-5 fw-bold h-font text-center">Management Team</h3>
  <div class="container px-4">
    <!-- Swiper -->
    <div class="swiper mySwiper">
      <div class="swiper-wrapper mb-5">
        <?php
        $about_q = selectAll('team_details');
        $path = ABOUT_IMG_PATH;
        while ($row = mysqli_fetch_assoc($about_q)) {
          echo <<<data
                        <div class="swiper-slide bg-white text-center overflow-hidden rounded">
                            <img src="$path$row[picture]" class="w-100" alt="Team Member">
                            <h5 class="mt-2">$row[name]</h5>
                        </div>
                    data;
        }
        ?>
      </div>
      <div class="swiper-pagination"></div>
    </div>
  </div>

  <!-- Footer -->
  <?php require('inc/footer.php'); ?>

  <!-- Swiper JS -->
  <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

  <!-- Initialize Swiper -->
  <script>
    var swiper = new Swiper(".mySwiper", {
      slidesPerView: 3,
      spaceBetween: 40,
      pagination: {
        el: ".swiper-pagination",
      },
      breakpoints: {
        320: {
          slidesPerView: 1,
        },
        640: {
          slidesPerView: 2,
        },
        768: {
          slidesPerView: 3,
        },
      }
    });
  </script>
</body>

</html>