<?php
require('Admin/inc/db_config.php');
require('Admin/inc/essentials.php');
require('inc/link.php');
session_start();
$contact_q = "SELECT * FROM `contact_details` WHERE  `sr_no`=?";
$values = [1];
$contact_r = mysqli_fetch_assoc(select($contact_q, $values, 'i'));


$settings_q = "SELECT * FROM `settings` WHERE  `sr_no`=?";
$values = [1];
$settings_r = mysqli_fetch_assoc(select($settings_q, $values, 'i'));

if($settings_r['shutdown'])
{
    echo <<<alertbar
    <div class="bg-danger text-center p-2 fw-bold">
         <i class="bi bi-exclamation-triangle-fill"></i>
        Bookings are temporarily closed!
    </div>
    alertbar;
}


?>
<style>
    .custom-alert {
        position: fixed;
        top: 80px;
        right: 25px;
        z-index: 12;

    }
</style>

<!-- navbar.......... -->
<nav id="nav-bar" class="navbar navbar-expand-lg navbar-light bg-light bg-white px-lg-3 py-2 shadow-sm stacky-top">
    <div class="container-fluid">
        <a class="navbar-brand me-5 fw-bold fs-3 h-font" href="index.php">Hotel Management System</a>
        <button class="navbar-toggler shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link   me-2" aria-current="page" href="index.php">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link me-2" href="room.php">Rooms</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link me-2" href="facilities.php">Facilities</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link me-2" href="contactus.php">Contact us</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link " href="about.php">About</a>
                </li>
            </ul>
            <div class="d-flex">
                <?php
               if (isset($_SESSION['login']) && $_SESSION['login'] == true) {
                $path = USER_IMG_PATH;
                echo <<<data
                <div class="btn-group">
                    <button type="button" class="btn btn-outline-dark shadow-none dropdown-toggle" data-bs-toggle="dropdown" data-bs-display="static" aria-expanded="false">
                        <img src="{$path}{$_SESSION['upic']}" style="width:25px; height:25px;" class="me-1">
                        {$_SESSION['uname']}
                    </button>
                    <ul class="dropdown-menu dropdown-menu-lg-end">
                        <li><a class="dropdown-item" href="profile.php">Profile<a/></li>
                        <li><a class="dropdown-item" href="bookings.php">Bookings<a/></li>
                         <li><a class="dropdown-item" href="logout.php">Logout<a/></li>
                    </ul>
                </div>
                data;
            }
              else
              {
                echo <<<data
                <button type="button" class="btn btn-outline-dark shadow none me-lg-3 me-2" data-bs-toggle="modal" data-bs-target="#loginModal">
                    Login
                </button>
                <button type="button" class="btn btn-outline-dark shadow none " data-bs-toggle="modal" data-bs-target="#registerModal">
                    Register
                </button>
                data;
              }
            

                ?>
                
            </div>
        </div>
    </div>
</nav>


<!-- Login Modal -->
<div class="modal fade" id="loginModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="login_form">
                <div class="modal-header">
                    <h5 class="modal-title d-flex align-items-center">
                        <i class="bi bi-person-fill fs-3 me-2"></i>User Login
                    </h5>
                    <button type="reset" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Email/ Mobile Number</label>
                        <input type="text" name="email_mob" class="form-control shadow-none" required>
                    </div>
                    <div class="mb-4">
                        <label class="form-label">Password</label>
                        <input type="password" class="form-control shadow-none" name="pass" required>
                    </div>
                    <div class="d-flex align-items-center justify-content-between">
                        <button type="submit" class="btn btn-dark shadow-none">Login</button>
                        <button type="button" class="btn text-secondary text-decoration-none
                         shadow none p-0"data-bs-dismiss="modal" data-bs-toggle="modal" data-bs-target="#forgotModal">
                         Forgot password?
                    </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Register Modal -->
<div class="modal fade" id="registerModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-lg modal-dialog">
        <div class="modal-content">
            <form id="register_form" enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title d-flex align-items-center">
                        <i class="bi bi-person-fill fs-3 me-2"></i>User Registration
                    </h5>
                    <button type="reset" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <span class="badge bg-light text-dark mb-3 text-wrap lh-base">
                        <!-- Note: Your details match with your ID (National ID, passport, driving license, etc.) that will be required during check-in. -->
                    </span>
                    <div class="container">
                        <div class="row">
                            <div class="col-md-6 ps-0 mb-3">
                                <label class="form-label">Name</label>
                                <input type="text" id="name" name="name" class="form-control shadow-none" required>
                            </div>
                            <div class="col-md-6 p-0 mb-3">
                                <label class="form-label">Email </label>
                                <input type="email" id="email" name="email" class="form-control shadow-none" required>
                            </div>
                            <div class="col-md-6 ps-0 mb-3">
                                <label class="form-label">Phone Number</label>
                                <input type="text" id="phonenum" name="phonenum" class="form-control shadow-none" required>
                            </div>
                            <div class="col-md-6 p-0 mb-3">
                                <label class="form-label">Picture </label>
                                <input type="file" id="profile" name="profile" accept=".jpg,.jpeg,.png" class="form-control shadow-none" required>
                            </div>
                            <div class="col-md-6 p-0 mb-3">
                                <label class="form-label">Address </label>
                                <textarea id="address" name="address" class="form-control shadow-none" rows="1" required></textarea>
                            </div>
                            <div class="col-md-6 p-0 mb-3">
                                <label class="form-label">Date of birth </label>
                                <input type="date" id="dob" name="dob" class="form-control shadow-none" required>
                            </div>
                            <div class="col-md-6 ps-0">
                                <label class="form-label">Password</label>
                                <input type="password" id="pass" name="pass" class="form-control shadow-none" required>
                            </div>
                            <div class="col-md-6 p-0">
                                <label class="form-label">Confirm password</label>
                                <input type="password" id="cpass" name="cpass" class="form-control shadow-none" required>
                            </div>
                        </div>
                    </div>
                    <div class="text-center my-1 mt-2">
                        <button type="submit" class="btn btn-dark shadow-none">Register</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Forgot Modal -->
<div class="modal fade" id="forgotModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="forgot_form">
                <div class="modal-header">
                    <h5 class="modal-title d-flex align-items-center">
                        <i class="bi bi-person-fill fs-3 me-2"></i>Forgot password
                    </h5>
                    
                </div>
                <div class="modal-body">
                <span class="badge bg-light text-dark mb-3 text-wrap lh-base">
                        Note: A link will be sent your email to reset your password
                    </span>
                    <div class="mb-4">
                    
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control shadow-none" required>
                    </div>
                    
                    <div class="mb-2 text-end">
                        
                        <button type="button" class="btn shadow none p-0 mb-2"
                         data-bs-toggle="modal" data-bs-target="#loginModal"data-bs-dismiss="modal">
                        Cancel
                    </button>
                    <button type="submit" class="btn btn-dark shadow-none">SEND LINK</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>


</script>