<?php
require('inc/essentials.php'); // Ensure this file is included only once
require('inc/db_config.php');
adminlogin(); // Check if the user is logged in

session_regenerate_id(true); // Regenerate session ID for security
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - New Bookings</title>

    <?php require('inc/link.php'); ?>
</head>

<body class="bg-light">
    <!-- Header --->
    <?php require('inc/header.php'); ?>

    <div class="container-fluid">
        <div class="row">
            <!--- Main Content -->
            <div class="col-lg-10 ms-auto p-4 overflow-hidden">
                <h3>New Bookings</h3>
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body">
                    <div class="card-body">                        
                         <div class="text-end mb-4">
                            <input type="text" name="search" oninput="get_bookings(this.value)" class="form-control shadow-none w-25 ms-auto"
                                placeholder="Type to search">
                           </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover border ">
                                <thead class="sticky-top bg-dark text-light">
                                    <tr>
                                        <th scope="col" class="table-dark">S.N</th>
                                        <th scope="col" class="table-dark">Users Details</th>
                                        <th scope="col" class="table-dark">Room Details</th>
                                        <th scope="col" class="table-dark">Bookings Details</th>
                                        <th scope="col" class="table-dark">Action</th>

                                    </tr>
                                </thead>
                                <tbody id="table-data">
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<!-- Assign Room Number Modal -->
<div class="modal fade" id="assign-room" data-bs-backdrop="static" data-bs-keyboard="true" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form id="assign_room_form">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Assign Room</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Room Number</label>
                        <input type="text" name="room_no" class="form-control shadow-none" required>
                    </div>
                    <span class="badge rounded-pill bg-light text-dark mb-3 text-wrap lh-base">
                        Note: Assign room number only when the guest has arrived!
                    </span>
                    <input type="hidden" name="booking_id">
                </div>
                <div class="modal-footer">
                    <button type="reset" class="btn text-secondary shadow-none" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn custom-bg text-white shadow-none">Submit</button>
                </div>
            </div>
        </form>
    </div>
</div>

    <?php require('inc/script.php'); ?>

<script src="inc/new_bookings.js"></script>
</body>

</html>
