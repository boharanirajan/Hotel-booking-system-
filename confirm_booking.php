<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirm Booking</title>

    <?php require('inc/link.php'); ?>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="CSS/style.css">
</head>

<body class="bg-light">
    <!-- Header -->
    <?php require('inc/header.php'); ?>

    <?php
    // Check if room ID is set in the URL, shutdown status, and user login status
    if (!isset($_GET['id']) || $settings_r['shutdown'] == true) {
        redirect('room.php');
    }

    // Redirect to login page if the user is not logged in
    if (!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
        redirect('login.php');
    }

    // Filter and get room and user data
    $data = filteration($_GET);

    // Select room data where ID matches, status is active (1), and remove flag is not set (0)
    $room_res = select("SELECT * FROM `rooms` WHERE `id`=? AND `status`=? AND `remove`=?", [$data['id'], 1, 0], 'iii');

    // Check if room exists, otherwise redirect
    if (mysqli_num_rows($room_res) == 0) {
        redirect('room.php');
    }

    // Fetch room data
    $room_data = mysqli_fetch_assoc($room_res);

    // Set session for room details
    $_SESSION['room'] = [
        "id" => $room_data['id'],
        "name" => $room_data['name'],
        "price" => $room_data['price'],
        "payment" => null,
        "available" => false
    ];

    // Get user data
    $user_res = select("SELECT * FROM `users` WHERE `id`=? LIMIT 1", [$_SESSION['uid']], "i");
    if ($user_res && mysqli_num_rows($user_res) > 0) {
        $user_data = mysqli_fetch_assoc($user_res);
    } else {
        redirect('room.php');
    }
    ?>

    <div class="container">
        <div class="row">
            <div class="col-12 my-5 px-4">
                <h2 class="fw-bold">CONFIRM BOOKING</h2>
                <div style="font-size: 14px;">
                    <a href="index.php" class="text-secondary text-decoration-none">Home</a>
                    <span class="text-secondary">&gt;</span>
                    <a href="room.php" class="text-secondary text-decoration-none">Rooms</a>
                    <span class="text-secondary">&gt;</span>
                    <a href="#" class="text-secondary text-decoration-none">Confirm</a>
                </div>
            </div>

            <!-- Room booking Image and Details Section -->
            <div class="col-lg-7 col-md-12 px-4">
                <?php
                // Get Thumbnail image
                $room_thumb = ROOMS_IMG_PATH . "thumbnail.jpg";
                $thumb_res = select("SELECT * FROM `rooms_images` WHERE `room_id`=? AND `thumb`=?", [$room_data['id'], 1], 'ii');

                if (mysqli_num_rows($thumb_res) > 0) {
                    $thumb_data = mysqli_fetch_assoc($thumb_res);
                    $room_thumb = ROOMS_IMG_PATH . $thumb_data['image'];
                }

                echo <<<data
                    <div class="card p-3 shadow-sm rounded">
                        <img src="$room_thumb" class="img-fluid rounded mb-3">
                        <h5>{$room_data['name']}</h5>
                        <h5>रु. {$room_data['price']} per night</h5>
                    </div>
                data;
                ?>
            </div>

            <!-- Booking Details form --->
            <div class="col-lg-5 col-md-12 px-4">
                <div class="card mb-4 border-0 shadow-sm">
                    <div class="card-body">
                        <form id="booking-form" action="pay_now.php" method="POST">
                            <h6 class="mb-3">Booking details</h6>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label" for="name">Name</label>
                                    <input type="text" id="name" value="<?php echo htmlspecialchars($user_data['name']); ?>" name="name" class="form-control shadow-none" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label" for="phone_no">Phone Number</label>
                                    <input type="number" id="phone_no" value="<?php echo htmlspecialchars($user_data['phone_no']); ?>" name="phone_no" class="form-control shadow-none" required>
                                </div>
                                <div class="col-md-12 mb-3">
                                    <label class="form-label" for="address">Address</label>
                                    <textarea id="address" name="address" class="form-control shadow-none" rows="1" required><?php echo htmlspecialchars($user_data['address']); ?></textarea>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label" for="check_in">Check-in</label>
                                    <input type="date" id="check_in" name="check_in" onchange="check_availability()" class="form-control shadow-none" required>
                                </div>
                                <div class="col-md-6 mb-4">
                                    <label class="form-label" for="check_out">Check-out</label>
                                    <input type="date" id="check_out" name="check_out" onchange="check_availability()" class="form-control shadow-none" required>
                                </div>
                                <div class="col-12">
                                    <div class="spinner-border text-info mb-3 d-none" id="info_loader" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                    <h6 class="mb-2 text-danger d-none" id="pay-info">Provide check-in and check-out date!</h6>
                                    <button type="submit" name="pay_now" id="pay_now" class="btn w-100 text-white custom-bg shadow-none" disabled>
                                        Book Now</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>

        <!-- Footer --->
    <?php require('inc/footer.php'); ?>
   

   <script>
    let bookingForm = document.getElementById('booking-form');
    let infoLoader = document.getElementById('info_loader');
    let payInfo = document.getElementById('pay-info');
    let payNowButton = document.getElementById('pay_now');

    // Set today's date for check-in and check-out validation
    const today = new Date().toISOString().split('T')[0];
    bookingForm.elements['check_in'].setAttribute('min', today);
    bookingForm.elements['check_out'].setAttribute('min', today);

    // Ensure check-in is earlier than check-out
    bookingForm.elements['check_in'].addEventListener('change', function () {
        bookingForm.elements['check_out'].setAttribute('min', this.value);
    });

    function check_availability() {
        let checkInVal = bookingForm.elements['check_in'].value;
        let checkOutVal = bookingForm.elements['check_out'].value;

        // Disable the button initially
        payNowButton.setAttribute('disabled', true);

        if (checkInVal !== '' && checkOutVal !== '') {
            payInfo.classList.add('d-none');
            payInfo.classList.replace('text-dark', 'text-danger');
            infoLoader.classList.remove('d-none');

            let data = new FormData();
            data.append('check_availability', '');
            data.append('check_in', checkInVal);
            data.append('check_out', checkOutVal);

            // Create XMLHttpRequest for AJAX call
            let xhr = new XMLHttpRequest();
            xhr.open("POST", 'ajax/confirm_booking.php', true);

            // Handle response from the server
            xhr.onload = function () {
                let response = JSON.parse(this.responseText);

                // Display relevant messages based on the response status
                switch (response.status) {
                    case 'check_in_out_equal':
                        payInfo.innerText = "You cannot check-out on the same day!";
                        break;
                    case 'check_out_earlier':
                        payInfo.innerText = "Check-out date is earlier than check-in date!";
                        break;
                    case 'check_in_earlier':
                        payInfo.innerText = "Check-in date is earlier than today's date!";
                        break;
                    case 'unavailable':
                        payInfo.innerText = response.message || "Rooms not available for this check-in date!";
                        break;
                    case 'already_booked':
                        payInfo.innerText = response.message || "You have already booked this room for the selected dates.";
                        break;
                    default:
                        payInfo.innerHTML = "No. of Days: " + response.days + "<br> Total Amount to pay: रु. " + response.payment;
                        payInfo.classList.replace('text-danger', 'text-dark');
                        payNowButton.removeAttribute('disabled');
                        break;
                }

                payInfo.classList.remove('d-none');
                infoLoader.classList.add('d-none');
            };

            xhr.send(data);
        } else {
            payInfo.innerText = "Please provide both check-in and check-out dates!";
            payInfo.classList.remove('d-none');
            payInfo.classList.replace('text-dark', 'text-danger');
        }
    }
</script>


</body>

</html>
