<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking</title>

    <?php require('inc/link.php'); ?>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="CSS/style.css">
</head>

<body class="bg-light">
    <!-- Header -->
    <?php
    require('inc/header.php');

    if (!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
        redirect('index.php');
    }
    ?>

    <div class="container">
        <div class="row">
            <div class="col-12 my-5 px-4">
                <h2 class="fw-bold">BOOKINGS</h2>
                <div style="font-size: 14px;">
                    <a href="index.php" class="text-secondary text-decoration-none">Home</a>
                    <span class="text-secondary">&gt;</span>
                    <a href="bookings.php" class="text-secondary text-decoration-none">Bookings</a>
                </div>
            </div>

            <!-- Booking Details Section -->
            <?php
            $query = "SELECT bo.*, bd.* 
                      FROM `booking_order` bo 
                      INNER JOIN `booking_details` bd ON bo.booking_id = bd.booking_id
                      WHERE ((bo.booking_status = 'booked') 
                      OR (bo.booking_status = 'canceled' AND bo.refund = 1) 
                      OR (bo.booking_status = 'Payment failed'))
                      AND bo.user_id = ?
                      ORDER BY bo.booking_id DESC";

            $result = select($query, [$_SESSION['uid']], 'i');

            while ($data = mysqli_fetch_assoc($result)) {
                $date = date("d-m-Y", strtotime($data['datetime']));
                $checkin = date("d-m-Y", strtotime($data['check_in']));
                $checkout = date("d-m-Y", strtotime($data['check_out']));

                $status_bg = "";
                $btn = "";

                if ($data['booking_status'] == 'booked') {
                    $status_bg = "bg-success";
                    if ($data['arrival'] == 1) {
                        $btn = "<a href='generate_pdf.php?id={$data['booking_id']}' class='btn text-white btn-dark shadow-none'>
                                    Download PDF <i class='bi bi-file-earmark-pdf'></i></a>";

                        if ($data['rate_review'] == 0) {
                            $btn .= "<button onclick='review_room($data[booking_id],$data[room_id])' data-bs-toggle='modal' data-bs-target='#reviewModal'
                                        type='button' class='btn text-white btn-dark shadow-none ms-2'>
                                        Rate & Review
                                      </button>";
                        }
                    } else {
                        $btn = "<button type='button' onclick='cancel_booking({$data['booking_id']})' 
                        class='btn text-white btn-danger shadow-none '>
                                    Cancel</button>";
                    }
                } else if ($data['booking_status'] == 'canceled') {
                    $status_bg = "bg-danger";
                    if ($data['refund'] == 0) {
                        $btn = "<span class='badge bg-primary'>Refund in process!</span>";
                    } else {
                        $btn = "<a href='generate_pdf.php?id={$data['booking_id']}' class='btn text-white btn-dark shadow-none'>
                                    Download PDF <i class='bi bi-file-earmark-pdf'></i></a>";
                    }
                } else {
                    $status_bg = "bg-warning";
                    $btn = "<a href='generate_pdf.php?id={$data['booking_id']}' class='btn text-white btn-dark shadow-none'>
                                Download PDF <i class='bi bi-file-earmark-pdf'></i></a>";
                }

                echo <<<bookings
                <div class='col-md-4 mb-4'>
                    <div class='bg-white p-3 rounded shadow-sm'>
                        <h5 class='fw-bold'>{$data['room_name']}</h5> 
                        <p><strong>Price:</strong> रु.{$data['price']} per night</p>
                        <p><strong>Check-in:</strong> {$checkin}</p>
                        <p><strong>Check-out:</strong> {$checkout}</p>
                        <p><strong>Booking Date:</strong> {$date}</p>
                        <span class='badge $status_bg mb-3'>{$data['booking_status']}</span><br>
                        $btn
                    </div>
                </div>
                bookings;
            }
            ?>
        </div>
    </div>
    <!-- Review and Rating Modal -->
    <div class="modal fade" id="reviewModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="review_form">
                    <div class="modal-header">
                        <h5 class="modal-title d-flex align-items-center">
                            <i class="bi bi-chat-left-heart-fill fs-3 me-2"></i>Review and Rating
                        </h5>
                        <button type="reset" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Rating</label>
                            <select class="form-select shadow-none" name="rating">
                                <option value="4">Excellent</option>
                                <option value="3">Very Good</option>
                                <option value="2">Good</option>
                                <option value="1">Bad</option>
                            </select>
                        </div>
                        <div class="mb-4">
                            <label class="form-label">Review</label>
                            <textarea class="form-control shadow-none" rows="3" name="review" required></textarea>
                        </div>
                        <input type="hidden" name="booking_id">
                        <input type="hidden" name="room_id">
                        <div class="text-end">
                            <button type="submit" class="btn btn-dark shadow-none">Submit</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <?php
    if (isset($_GET['cancel_status'])) {
        alert('success', 'Booking Cancelled!');
    } else if (isset($_GET['review_status'])) {
        alert('success', 'Thank you for your Rating and Review!');
    }
    ?>

    <!--- Footer -->
    <?php require('inc/footer.php'); ?>

    <script>
        function cancel_booking(id) {
            if (confirm('Are you sure to cancel booking?')) {
                let xhr = new XMLHttpRequest();
                xhr.open("POST", 'ajax/cancel_bookings.php', true);
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                xhr.onload = function() {
                    if (this.responseText == 1) {
                        window.location.href = "bookings.php?cancel_status=true";
                    } else {
                        alert('error', 'Cancellation failed');
                    }
                };
                xhr.send('cancel_booking&id=' + id);
            }
        }

        // Review and Rating Functionality
        let review_form = document.getElementById('review_form');
        function review_room(bid, rid) {
            if (review_form) {
                review_form.elements['booking_id'].value = bid;
                review_form.elements['room_id'].value = rid;
            }
        }
        if (review_form) {
            review_form.addEventListener('submit', function(e) {
                e.preventDefault();

                // Create FormData object and append the form data
                let data = new FormData(review_form);
                data.append('review_form', 'true'); // To check POST condition in PHP

                // Create a new AJAX request
                let xhr = new XMLHttpRequest();
                xhr.open("POST", 'ajax/review_room.php', true);

                xhr.onload = function() {
                    if (this.responseText == 0) {
                        let loginModal = document.getElementById('loginModal');
                        let modal = bootstrap.Modal.getInstance(loginModal);
                        modal.hide();
                        alert('error', "Rating & Review Failed!");
                    } else {
                        window.location.href = 'bookings.php?review_status=true';
                    }
                };

                xhr.send(data);
            });
        }
    </script>
</body>

</html>