<?php
require('inc/essentials.php'); // Include essential functions
require('inc/db_config.php');   // Include database configuration
adminlogin();                   // Check admin login status
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Dashboard</title>
    <?php
    require('inc/link.php'); // Include necessary CSS and JS files
   // Fetch shutdown status from settings
$is_shutdown = mysqli_fetch_assoc(mysqli_query($con, "SELECT `shutdown` FROM `settings`"));

// Fetch booking statistics
$current_bookings = mysqli_fetch_assoc(mysqli_query($con, " SELECT 
        COUNT(CASE WHEN booking_status = 'booked' AND arrival = 0 THEN 1 END) AS 'new_bookings',
        COUNT(CASE WHEN booking_status = 'canceled' AND refund = 0 THEN 1 END) AS 'refund_bookings'
    FROM `booking_order`
"));

// Fetch unread user queries count
$unread_queries = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(sr_no) 
    AS 'count' FROM `user_queries` WHERE `seen` = 0"));

// Fetch unread reviews count
$unread_reviews = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(sr_no) 
    AS 'count' FROM `rating_review` WHERE `seen` = 0"));

// Fetch current users statistics from the `users` table
$current_users = mysqli_fetch_assoc(mysqli_query($con, "SELECT 
        COUNT(*) AS 'total',  
        COUNT(CASE WHEN status = 1 THEN 1 END) AS 'active',  
        COUNT(CASE WHEN status = 0 THEN 1 END) AS 'inactive',  
        COUNT(CASE WHEN `is_varified` = 0 THEN 1 END) AS 'unverified'  
    FROM `users`  
"));
   
   
   ?>
</head>

<body class="bg-light">
    <!-- Header Section -->
    <?php
    require('inc/header.php'); // Include the admin header
    ?>

    <!-- Main Content Area -->
    <div class="container-fluid">
        <div class="row">
            <!-- Dashboard Content -->
            <div class="col-lg-10 ms-auto p-4">
                <div class="row">
                    <div class="col-lg-12">
                        <!-- Dashboard Title and Status -->
                        <div class="d-flex align-items-center justify-content-between mb-4">
                            <h3>DASHBOARD</h3>
                            <?php
                            if($is_shutdown['shutdown'])
                            {
                                echo <<<data
                                 <h6 class="badge bg-danger py-2 px-3 rounded">Shutdown Mode is Active</h6>
                                data;
                            }
                            ?>

                        </div>

                        <!-- Booking Metrics Cards -->
                        <div class="row mb-4">
                            <!-- New Bookings -->
                            <div class="col-md-3 mb-4">
                                <a href="new_bookings.php" class="text-decoration-none">
                                    <div class="card text-center p-3 shadow-sm border-success">
                                        <h6 class="text-success">New Bookings</h6>
                                        <h1 class="mt-2 mb-0 text-dark"><?php echo $current_bookings['new_bookings'] ?></h1>
                                    </div>
                                </a>
                            </div>

                            <!-- Refund Bookings -->
                            <div class="col-md-3 mb-4">
                                <a href="refund_bookings.php" class="text-decoration-none">
                                    <div class="card text-center p-3 shadow-sm border-warning">
                                        <h6 class="text-warning">Refund Bookings</h6>
                                        <h1 class="mt-2 mb-0 text-dark"><?php echo $current_bookings['refund_bookings'] ?></h1>
                                    </div>
                                </a>
                            </div>

                            <!-- User Queries -->
                            <div class="col-md-3 mb-4">
                                <a href="user_queries.php" class="text-decoration-none">
                                    <div class="card text-center p-3 shadow-sm border-info">
                                        <h6 class="text-info">User Queries</h6>
                                        <h1 class="mt-2 mb-0 text-dark"><?php echo $unread_queries['count'] ?></h1>
                                    </div>
                                </a>
                            </div>

                            <!-- Ratings and Reviews -->
                            <div class="col-md-3 mb-4">
                                <a href="rate_review.php" class="text-decoration-none">
                                    <div class="card text-center p-3 shadow-sm border-secondary">
                                        <h6 class="text-secondary">Ratings and Reviews</h6>
                                        <h1 class="mt-2 mb-0 text-dark"><?php echo $unread_reviews['count'] ?></h1>
                                    </div>
                                </a>
                            </div>
                        </div>

                        <!-- Analytics Section -->
                        <!-- <div class="d-flex align-items-center justify-content-between mb-3">
                            <h5>Bookings Analytics</h5>
                            <select class="form-select shadow-none bg-light w-auto" onchange="bookings_analytics(this.value)">
                                <option value="1">Past 30 days</option>
                                <option value="2">Past 90 days</option>
                                <option value="3">Past 1 year</option>
                                <option value="4">All time</option>
                            </select>
                        </div> -->

                        <!-- Total Bookings, Active Bookings, Cancelled Bookings -->
                        <div class="row mb-4">
                            <div class="col-md-3 mb-4">
                                <div class="card text-center p-3 shadow-sm border-primary">
                                    <h6 class="text-primary">Total Bookings</h6>
                                    <h1 class="mt-2 mb-0 text-dark" id="total_bookings">0</h1>
                                    <h4 class="mt-2 mb-0" id="total_amt">रु.0</h4>
                                </div>
                            </div>

                            <div class="col-md-3 mb-4">
                                <div class="card text-center p-3 shadow-sm border-success">
                                    <h6 class="text-success">Active Bookings</h6>
                                    <h1 class="mt-2 mb-0 text-dark" id="active_bookings">0</h1>
                                    <h4 class="mt-2 mb-0" id="active_amount">रु.0</h4>
                                </div>
                            </div>

                            <div class="col-md-3 mb-4">
                                <div class="card text-center p-3 shadow-sm border-danger">
                                    <h6 class="text-danger" >Cancelled Bookings</h6>
                                    <h1 class="mt-2 mb-0 text-dark" id="cancelled_bookings">0</h1>
                                    <h4 class="mt-2 mb-0" id="cancelled_amount">रु.0</h4>
                                </div>
                            </div>
                        </div>

                        <!-- User Analytics Section -->
                        <!-- <div class="d-flex align-items-center justify-content-between mb-3">
                            <h5>User, Queries, Reviews Analytics</h5>
                            <select class="form-select shadow-none bg-light w-auto" onchange="users_analytics(this.value)">
                                <option value="1">Past 30 days</option>
                                <option value="2">Past 90 days</option>
                                <option value="3">Past 1 year</option>
                                <option value="4">All time</option>
                            </select>
                        </div> -->

                        <!-- <div class="row mb-3">
                            <div class="col-md-3 mb-4">
                                <div class="card text-center p-3 shadow-sm border-success">
                                    <h6 class="text-primary">New Registrations</h6>
                                    <h1 class="mt-2 mb-0 text-dark" id="total_new_reg">0</h1>
                                </div>
                            </div> -->

                            <!-- <div class="col-md-3 mb-4">
                                <div class="card text-center p-3 shadow-sm border-primary">
                                    <h6 class="text-primary">Queries</h6>
                                    <h1 class="mt-2 mb-0 text-dark">0</h1>
                                </div>
                            </div> -->
<!-- 
                            <div class="col-md-3 mb-4">
                                <div class="card text-center p-3 shadow-sm border-primary">
                                    <h6 class="text-primary">Reviews</h6>
                                    <h1 class="mt-2 mb-0 text-dark" id="total_reviews">0</h1>
                                </div>
                            </div>
                        </div> -->
                        <h5>Users</h5>
                        <div class="row mb-3">
                            <div class="col-md-3 mb-4">
                                <div class="card text-center p-3 shadow-sm border-success">
                                    <h6 class="text-primary">Total </h6>
                                    <h1 class="mt-2 mb-0 text-dark"><?php echo $current_users['total']; ?></h1>
                                </div>
                            </div>

                            <div class="col-md-3 mb-4">
                                <div class="card text-center p-3 shadow-sm text-success border-success">
                                    <h6 class="text-success">Active</h6>
                                    <h1 class="mt-2 mb-0 text-dark"><?php echo $current_users[ 'active']; ?></h1>
                                </div>
                            </div>

                            <div class="col-md-3 mb-4">
                                <div class="card text-center p-3 shadow-sm text-warning border-warning">
                                    <h6 class="text-warning ">Inactive</h6>
                                    <h1 class="mt-2 mb-0 text-dark"><?php echo $current_users[ 'inactive']; ?></h1>
                                </div>
                            </div>

                            <div class="col-md-3 mb-4">
                                <div class="card text-center p-3 shadow-sm text-danger">
                                    <h6 class="text-danger ">Unverified</h6>
                                    <h1 class="mt-2 mb-0 text-dark"><?php echo $current_users['unverified']; ?></h1>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Include necessary scripts -->
    <?php
    require('inc/script.php');
    ?>
    <script src="inc/dashboard.js">

    </script>
</body>

</html>
