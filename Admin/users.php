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
    <title>Admin Panel - Users</title>

    <?php require('inc/link.php'); ?>
</head>

<body class="bg-light">
    <!-- Header -->
    <?php require('inc/header.php'); ?>

    <div class="container-fluid">
        <div class="row">
            <!-- Main Content -->
            <div class="col-lg-10 ms-auto p-4 overflow-hidden">
                <h3>USERS</h3>
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body">
                                                
                         <div class="text-end mb-4">
                            <input type="text" name="search" oninput="search_user(this.value)" class="form-control shadow-none w-25 ms-auto"
                                placeholder="Type to search">
                        </div>


                        </div>
                        <!--- show users  table -->
                        <div class="table-responsive" >
                            <table class="table table-hover border text-center" style="min-width: 1200px;">
                                <thead class="sticky-top bg-dark text-light">
                                    <tr>
                                        <th scope="col" class="table-dark">S.N</th>
                                        <th scope="col" class="table-dark">Name</th>
                                        <th scope="col" class="table-dark">Email</th>
                                        <th scope="col" class="table-dark">Phone No</th>
                                        <th scope="col" class="table-dark">Location</th>
                                        <th scope="col" class="table-dark">DOB</th>
                                        <th scope="col" class="table-dark">Verified</th>
                                        <th scope="col" class="table-dark">Status</th>
                                        <th scope="col" class="table-dark">Date</th>
                                        <th scope="col" class="table-dark">Action</th>
                                    </tr>
                                </thead>
                                <tbody id="users-data">
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <?php require('inc/script.php'); ?>

    <script src="inc/users.js"></script>

       

</body>

</html>