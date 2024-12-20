<?php
require('inc/essentials.php'); // Ensure this file is included only once
require('inc/db_config.php');
require('inc/script.php');
adminlogin(); // Check if the user is logged in

session_regenerate_id(true); // Regenerate session ID for security
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Feature & Facilities</title>
   
    <?php require('inc/link.php'); ?>
</head>

<body class="bg-light">
    <!-- Header -->
    <?php require('inc/header.php'); ?>

    <div class="container-fluid">
        <div class="row">
            <!-- Main Content -->
            <div class="col-lg-10 ms-auto p-4 overflow-hidden">
                <h3>Feature & Facilities</h3>
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <!-- Feature section -->
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <h5 class="card-title m-0">Features</h5>
                            <button type="button" class="btn btn-dark shadow-none btn-sm" data-bs-toggle="modal"
                                data-bs-target="#feature-s">
                                <i class="bi bi-plus-circle-fill"></i> Add
                            </button>
                        </div>
                        <!-- show feature table -->
                        <div class="table-responsive-md" style="height: 350px; overflow-y:scroll;">
                            <table class="table table-hover border">
                                <thead class="sticky-top bg-dark text-light">
                                    <tr>
                                        <th scope="col"class="table-dark">S.N</th>
                                        <th scope="col"class="table-dark">Name</th>
                                        <th scope="col"class="table-dark">Action</th>
                                    </tr>
                                </thead>
                                <tbody id="features-data">
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <!-- Facility section -->
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <h5 class="card-title m-0">Facilities</h5>
                            <button type="button" class="btn btn-dark shadow-none btn-sm" data-bs-toggle="modal"
                                data-bs-target="#facility-s">
                                <i class="bi bi-plus-circle-fill"></i> Add
                            </button>
                        </div>
                        <!-- show facilities table -->
                        <div class="table-responsive-md" style="height: 350px; overflow-y:scroll;">
                            <table class="table table-hover border">
                                <thead class="sticky-top bg-dark text-light">
                                    <tr>
                                        <th scope="col"class="table-dark">S.N</th>
                                        <th scope="col"class="table-dark">Icon</th>
                                        <th scope="col"class="table-dark">Name</th>
                                        <th scope="col"class="table-dark" width="40%" >Description</th>
                                        <th scope="col"class="table-dark">Action</th>
                                    </tr>
                                </thead>
                                <tbody id="facilities-data">
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- Feature Modal -->
    <div class="modal fade" id="feature-s" data-bs-backdrop="static" data-bs-keyboard="true" tabindex="-1"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form id="feature_s_form">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Add Feature</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Name</label>
                            <input type="text" name="feature_name" class="form-control shadow-none" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="reset" class="btn text-secondary shadow-none" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn custom-bg text-white shadow-none">Submit</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Facilities Modal -->
    <div class="modal fade" id="facility-s" data-bs-backdrop="static" data-bs-keyboard="true" tabindex="-1"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form id="facility_s_form">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Add Facility</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Name</label>
                            <input type="text" name="facility_name" class="form-control shadow-none" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Icon</label>
                            <input type="file" name="facility_icon" accept=".jpg,.png,.svg,.jpeg"
                                class="form-control shadow-none" required>
                        </div>

                        <div class=" mb-3">
                            <label class="form-label">Description </label>
                            <textarea name="facility_description" class="form-control shadow-none" rows="3"></textarea>
                        </div>
                        
                    </div>
                    <div class="modal-footer">
                        <button type="reset" class="btn text-secondary shadow-none" data-bs-dismiss="modal">
                            Cancel</button>
                        <button type="submit" class="btn custom-bg text-white shadow-none">Submit</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <?php require('inc/script.php'); ?>
    <script 
    src="inc/feature_facilities.js">

    </script>

ll

</body>

</html>