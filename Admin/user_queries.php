<?php
require('inc/essentials.php'); // Ensure this file is included only once
require('inc/db_config.php');
require('inc/script.php');
adminlogin(); // Check if the user is logged in

session_regenerate_id(true); // Regenerate session ID for security

// Mark as read 
if (isset($_GET['seen'])) {
    $frn_data = filteration($_GET);
    if ($frn_data['seen'] != 'all') {
        $q = "UPDATE `user_queries` SET `seen`=? WHERE `sr_no`=?";
        $values = [1, $frn_data['seen']];
        if (Update($q, $values, 'ii')) {
            alert('success', 'Mark as read');
        } else {
            alert('error', 'Operation Failed');
        }
    } else {
        // Mark all as read
        $q = "UPDATE `user_queries` SET `seen`=?";
        $values = [1];
        if (Update($q, $values, 'i')) {
            alert('success', 'All marked as read');
        } else {
            alert('error', 'Operation Failed');
        }
    }
}

// Delete 
if (isset($_GET['del'])) {
    $frn_data = filteration($_GET);
    if ($frn_data['del'] != 'all') {
        $q = "DELETE FROM `user_queries` WHERE `sr_no`=?";
        $values = [$frn_data['del']];
        if (delete($q, $values, 'i')) {
            alert('success', 'Data deleted!');
        } else {
            alert('error', 'Operation Failed');
        }
    } else {
        // Delete all
        $q = "DELETE FROM `user_queries`";
        if (mysqli_query($con, $q)) {
            alert('success', 'All data deleted!');
        } else {
            alert('error', 'Operation Failed');
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - User Queries</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <?php require('inc/link.php'); ?>
</head>

<body class="bg-light">
    <!-- Header -->
    <?php require('inc/header.php'); ?>

    <div class="container-fluid">
        <div class="row">
            <!-- Main Content -->
            <div class="col-lg-10 ms-auto p-4 overflow-hidden">
                <h3>USER QUERIES</h3>
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body">

                        <div class="text-end mb-4 ">
                            <a href="?seen=all" class="btn btn-dark rounded-pill shadow-none btn-sm">
                                <i class="bi bi-check-circle"></i> Mark all Read</a>
                            <a href="?del=all" class="btn btn-danger rounded-pill shadow-none btn-sm">
                                <i class="bi bi-trash3-fill"></i> Delete all </a>
                        </div>

                        <div class="table-responsive-md" style="height: 450px; overflow-y:scroll;">
                            <table class="table table-hover border">
                                <thead class="sticky-top bg-dark text-light">
                                    <tr>
                                        <th scope="col" class="table-dark">S.N</th>
                                        <th scope="col" class="table-dark">Name</th>
                                        <th scope="col" class="table-dark">Email</th>
                                        <th scope="col" class="table-dark" width="15%">Subject</th>
                                        <th scope="col" class="table-dark" width="25%">Message</th>
                                        <th scope="col" class="table-dark">Date</th>
                                        <th scope="col" class="table-dark">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $q = "SELECT * FROM `user_queries` ORDER BY `user_queries`.`sr_no` DESC";
                                    $data = mysqli_query($con, $q);
                                    $i = 1;
                                    while ($row = mysqli_fetch_assoc($data)) {
                                        $seen = '';
                                        if ($row['seen'] != 1) {
                                            $seen = "<a href='?seen=$row[sr_no]' class='btn btn-sm rounded-pill btn-primary'>Mark as read</a>";
                                        }
                                        $seen .= "<a href='?del=$row[sr_no]' class='btn btn-sm rounded-pill btn-danger'>Delete</a>";
                                        echo <<<query
                                    <tr>
                                        <td>$i</td>
                                        <td>$row[name]</td>
                                        <td>$row[email]</td>
                                        <td>$row[subject]</td>
                                        <td>$row[message]</td>
                                        <td>$row[date]</td>
                                        <td>$seen</td>
                                    </tr>
                                    query;
                                        $i++;
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php require('inc/script.php');
    require('inc/script_s.php');
    ?>

</body>

</html>