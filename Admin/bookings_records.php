<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Bookings Records</title>

    <?php require('inc/link.php'); // Include necessary CSS and JavaScript files ?>
</head>
<body class="bg-light">
    <!-- Header- -->
    <?php require('inc/header.php'); ?>

    <div class="container-fluid">
        <div class="row">
            <!-- Main Content -->
            <div class="col-lg-10 ms-auto p-4 overflow-hidden">
                <h3>Bookings Records</h3>
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <!-- Search Input -->
                        <div class="text-end mb-4">
                            <input type="text" name="search" id="search_input" oninput="get_bookings(this.value, 1)" class="form-control shadow-none w-25 ms-auto" placeholder="Type to search">
                        </div>

                        <!-- Bookings Table -->
                        <div class="table-responsive">
                            <table class="table table-hover border" >
                                <thead class="sticky-top bg-dark text-light">
                                    <tr>
                                        <th scope="col" class="table-dark">S.N</th>
                                        <th scope="col" class="table-dark">User Details</th>
                                        <th scope="col" class="table-dark">Room Details</th>
                                        <th scope="col" class="table-dark">Booking Details</th>
                                        <th scope="col" class="table-dark">Status</th>
                                        <th scope="col" class="table-dark">Action</th>
                                    </tr>
                                </thead>
                                <tbody id="table-data">
                                    <!-- Booking records will be loaded here via JavaScript -->
                                </tbody>
                            </table>
                        </div>
                        <!-- Pagination -->
                        <nav>
                            <ul class="pagination mt-3" id="table-pagination">
                                <!-- Pagination buttons will be loaded here via JavaScript -->
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php require('inc/script.php'); ?>
   <script src="inc/booking_records.js"></script>
</body>
</html>