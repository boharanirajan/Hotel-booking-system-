<?php
require_once('inc/essentials.php'); // Ensure this file is included only once
require('inc/script.php');

adminlogin(); // Check if the user is logged in

session_regenerate_id(true); // Regenerate session ID for security
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Setting</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <?php require_once('inc/link.php'); ?>

    <style>

    </style>
</head>

<body class="bg-light">

    <!-- Header -->
    <?php require('inc/header.php'); ?>



    <!-- Main Content Area -->

    <div class="container-fluid" id="maincontent">
        <div class="row">
            <div class="col-lg-10 ms-auto p-4 overflow-hidden">
                <h3 class="mb-4">Setting</h3>

                <!-- General Setting Section -->
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <h5 class="card-title m-0">General Setting</h5>
                            <button type="button" class="btn btn-dark shadow-none btn-sm" data-bs-toggle="modal" data-bs-target="#general-s">Edit</button>
                        </div>
                        <h6 class="card-subtitle mb-1 fw-bold">Site Title</h6>
                        <p class="card-text" id="display_site_title"></p>
                        <h6 class="card-subtitle mb-1 fw-bold">About Us</h6>
                        <p class="card-text" id="display_site_about"></p>
                    </div>
                </div>

                <!-- General Setting Modal -->
                <div class="modal fade" id="general-s" data-bs-backdrop="static" data-bs-keyboard="true" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <form id="general_s_form">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">General Setting</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="mb-3">
                                        <label class="form-label">Site Title</label>
                                        <input type="text" id="site_title_inp" name="site_title_inp" class="form-control shadow-none" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">About Us</label>
                                        <textarea id="site_about_inp" name="site_about_inp" class="form-control shadow-none" rows="6" required></textarea>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn text-secondary shadow-none" data-bs-dismiss="modal">Cancel</button>
                                    <button type="submit" class="btn custom-bg text-white shadow-none">Submit</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>


                <!-- Shutdown Setting Section -->
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <h5 class="card-title m-0">Shutdown Website</h5>
                            <div class="form-check form-switch">
                                <input id="shutdown-toggle" class="form-check-input" type="checkbox">
                            </div>
                        </div>
                        <p class="card-text">
                            No customers will be allowed to book hotel rooms when shutdown mode is turned on.
                        </p>
                    </div>
                </div>

                <!-- Contact Setting Section -->
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <h5 class="card-title m-0">Contact Setting</h5>
                            <button type="button" class="btn btn-dark shadow-none btn-sm" data-bs-toggle="modal" data-bs-target="#contact-s">Edit</button>
                        </div>
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="mb-4">
                                    <h6 class="card-subtitle mb-1 fw-bold">Address</h6>
                                    <p class="card-text" id="address"></p>
                                </div>
                                <div class="mb-4">
                                    <h6 class="card-subtitle mb-1 fw-bold">Google Map</h6>
                                    <p class="card-text" id="gmap"></p>
                                </div>
                                <div class="mb-4">
                                    <h6 class="card-subtitle mb-1 fw-bold">Phone Numbers</h6>
                                    <p class="card-text mb-1">
                                        <i class="bi bi-telephone"></i>
                                        <span id="pn1"> </span>
                                    </p>
                                    <p class="card-text">
                                        <i class="bi bi-telephone"></i>
                                        <span id="pn2"> </span>
                                    </p>
                                </div>
                                <div class="mb-4">
                                    <h6 class="card-subtitle mb-1 fw-bold">E-mail</h6>
                                    <p class="card-text" id="email"></p>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="mb-4">
                                    <h6 class="card-subtitle mb-1 fw-bold">Social Links</h6>
                                    <p class="card-text mb-1">
                                        <i class="bi bi-twitter me-1"></i>
                                        <span id="tw"> </span>
                                    </p>
                                    <p class="card-text mb-1">
                                        <i class="bi bi-facebook me-1"></i>
                                        <span id="fb"> </span>
                                    </p>
                                    <p class="card-text mb-1">
                                        <i class="bi bi-linkedin me-1"></i>
                                        <span id="lnk"> </span>
                                    </p>
                                    <p class="card-text mb-1">
                                        <i class="bi bi-instagram me-1"></i>
                                        <span id="insta"> </span>
                                    </p>
                                </div>
                                <div class="mb-4">
                                    <h6 class="card-subtitle mb-1 fw-bold">iFrame</h6>
                                    <iframe id="iframe" class="border p-2 w-100" frameborder="0"></iframe>
                                </div>
                            </div>
                        </div>

                        <!-- Contact Details Modal -->
                        <div class="modal fade" id="contact-s" data-bs-backdrop="static" data-bs-keyboard="true" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                            <div class="modal-dialog modal-lg">
                                <form id="contact_s_form">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Contacts Setting</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="container-fluid p-0">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="mb-3">
                                                            <label class="form-label">Address</label>
                                                            <input type="text" name="address" id="address_inp" class="form-control shadow-none" required>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label">Google Map</label>
                                                            <input type="text" name="gmap" id="gmap_inp" class="form-control shadow-none" required>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label">Phone Numbers</label>
                                                            <div class="input-group mb-3">
                                                                <span class="input-group-text"><i class="bi bi-telephone"></i></span>
                                                                <input type="number" name="pn1" id="pn1_inp" class="form-control" required>
                                                            </div>
                                                            <div class="input-group mb-3">
                                                                <span class="input-group-text"><i class="bi bi-telephone"></i></span>
                                                                <input type="number" name="pn2" id="pn2_inp" class="form-control" required>
                                                            </div>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label">Email</label>
                                                            <input type="email" name="email" id="email_inp" class="form-control shadow-none" required>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="mb-3">
                                                            <label class="form-label">Social Links</label>
                                                            <div class="input-group mb-3">
                                                                <span class="input-group-text"><i class="bi bi-twitter"></i></span>
                                                                <input type="text" name="tw" id="tw_inp" class="form-control" required>
                                                            </div>
                                                            <div class="input-group mb-3">
                                                                <span class="input-group-text"><i class="bi bi-facebook me-1"></i></span>
                                                                <input type="text" name="fb" id="fb_inp" class="form-control" required>
                                                            </div>
                                                            <div class="input-group mb-3">
                                                                <span class="input-group-text"><i class="bi bi-linkedin me-1"></i></span>
                                                                <input type="text" name="lnk" id="lnk_inp" class="form-control" required>
                                                            </div>
                                                            <div class="input-group mb-3">
                                                                <span class="input-group-text"><i class="bi bi-instagram me-1"></i></span>
                                                                <input type="text" name="insta" id="insta_inp" class="form-control" required>
                                                            </div>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label">IFrame Src</label>
                                                            <input type="text" name="iframe" id="iframe_inp" class="form-control shadow-none" required>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" onclick="contacts_inp(contactus_data)" class="btn text-secondary shadow-none" data-bs-dismiss="modal">Cancel</button>
                                            <button type="submit" class="btn custom-bg text-white shadow-none">Submit</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Management Team Section -->
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <h5 class="card-title m-0">Management Team</h5>
                            <button type="button" class="btn btn-dark shadow-none btn-sm" data-bs-toggle="modal" data-bs-target="#team-s">
                                <i class="bi bi-plus-circle-fill"></i> Add
                            </button>
                        </div>
                        <div class="row" id="team-data">
                            <!-- Team members will be loaded here -->
                            <div class="col-md-2 mb-3">

                            </div>
                        </div>
                    </div>

                    <!-- Management Team Modal -->
                    <div class="modal fade" id="team-s" data-bs-backdrop="static" data-bs-keyboard="true" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <form id="team_s_form">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Add Team Member</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label class="form-label">Name</label>
                                            <input type="text" id="member_name_inp" class="form-control shadow-none" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Picture</label>
                                            <input type="file" id="member_picture_inp" accept=".jpg,.png,.webp,.jpeg" class="form-control shadow-none" required>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn text-secondary shadow-none" data-bs-dismiss="modal">Cancel</button>
                                        <button type="submit" class="btn custom-bg text-white shadow-none">Submit</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End of Main Content Area -->





    <?php require('inc/script.php');
    require('inc/script_s.php');
    ?>




</body>

</html>