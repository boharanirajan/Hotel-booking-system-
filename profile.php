<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>

    <?php

    require('inc/link.php');
    ?>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="CSS/style.css">
</head>

<body class="bg-light">
    <!-- Header -->
    <?php require('inc/header.php'); ?>

    <?php
    // Check user login status
    if (!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
        redirect('index.php');
        exit; // Ensure no further code runs after redirect
    }

    // At this point, the user is logged in, proceed to fetch user data
    $u_exists = select("SELECT * FROM `users` WHERE `id` = ? LIMIT 1", [$_SESSION['uid']], 's');

    if (mysqli_num_rows($u_exists) == 0) {
        redirect('index.php');
        exit;
    }

    $u_fetch = mysqli_fetch_assoc($u_exists);
    ?>

    <div class="container">
        <div class="row">

            <div class="col-12 my-5 px-4">
                <h2 class="fw-bold">Profile</h2>
                <div style="font-size: 14px;">
                    <a href="index.php" class="text-secondary text-decoration-none">Home</a>
                    <span class="text-secondary">></span>
                    <a href="profile.php" class="text-secondary text-decoration-none">Profile</a>
                </div>
            </div>

            <div class="col-12 mb-5 px-4">
                <div class="bg-white p-3 p-md-4 rounded shadow-none">
                    <form id="info_form">
                        <h5 class="mb-3 fw-bold">Basic Information</h5>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Name</label>
                                <input type="text" id="name" name="name" value="<?php echo $u_fetch['name']; ?>" class="form-control shadow-none" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Phone Number</label>
                                <input type="number" id="phonenum" name="phonenum" value="<?php echo $u_fetch['phone_no']; ?>" class="form-control shadow-none" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Address</label>
                                <textarea id="address" name="address" class="form-control shadow-none" rows="1" required><?php echo $u_fetch['address']; ?></textarea>
                            </div>
                            <div class="col-md-6 mb-4">
                                <label class="form-label">Date of Birth</label>
                                <input type="date" id="dob" name="dob" value="<?php echo $u_fetch['dob']; ?>" class="form-control shadow-none" required>
                            </div>
                        </div>

                        <button type="submit" class="btn text-white custom-bg shadow-none">Save Changes</button>
                    </form>
                </div>
            </div>

            <!-- Profile Change -->
            <div class="col-md-4 mb-5 px-4">
                <div class="bg-white p-3 p-md-4 rounded shadow-none">
                    <form id="profile_form">
                        <h5 class="mb-3 fw-bold">Picture</h5>
                        <img src="<?php echo USER_IMG_PATH . $u_fetch['profile'] ?>" class="img-fluid">

                        <label class="form-label">New picture </label>
                        <input type="file" id="profile" name="profile" accept=".jpg,.jpeg,.png"
                            class="form-control shadow-none mb-2" required>

                        <button type="submit" class="btn text-white custom-bg shadow-none">Save changes</button>
                    </form>
                </div>
            </div>
            <!-- <div class="col-md-8 mb-5 px-4">
                <div class="bg-white p-3 p-md-4 rounded shadow-none">
                    <form id="paas_form">
                        <h5 class="mb-3 fw-bold">Change Password</h5>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">New Password</label>
                                <input type="password" name="new_pass" class="form-control shadow-none" required>
                            </div>
                            <div class="col-md-6 mb-4">
                                <label class="form-label">Confirm Password</label>
                                <input type="password" name="confirm_pass" class="form-control shadow-none" required>
                            </div>
                        </div>

                        <button type="submit" class="btn text-white custom-bg shadow-none">Save changes</button>
                    </form>
                </div>
            </div> -->

        </div>

    </div>
    </div>

    <!-- Footer -->
    <?php require('inc/footer.php'); ?>

    <script>
        let info_form = document.getElementById('info_form');
        info_form.addEventListener('submit', function(e) {
            e.preventDefault(); // Prevent default form submission behavior

            let data = new FormData();
            data.append('info_form', '');
            data.append('name', info_form.elements['name'].value);
            data.append('phonenum', info_form.elements['phonenum'].value);
            data.append('address', info_form.elements['address'].value);
            data.append('dob', info_form.elements['dob'].value);

            // Create XMLHttpRequest for AJAX call
            let xhr = new XMLHttpRequest();
            xhr.open("POST", 'ajax/profile.php', true);

            // Handle response from the server
            xhr.onload = function() {
                if (this.responseText === 'phone_already') {
                    alert('error', "Phone number is already registered");
                } else if (this.responseText === '0') {
                    alert('error', "No changes made!");
                } else {
                    alert('success', "Changes saved");
                }
            };

            // Send the form data
            xhr.send(data);
        });



        let profile_form = document.getElementById('profile_form');
        profile_form.addEventListener('submit', function(e) {
            e.preventDefault();

            let data = new FormData();
            data.append('profile_form', '');
            data.append('profile', profile_form.elements['profile'].files[0]);

            let xhr = new XMLHttpRequest();
            xhr.open("POST", 'ajax/profile.php', true);

            xhr.onload = function() {
                try {
                    const response = JSON.parse(this.responseText);
                    if (response.status === 'error') {
                        alert('error', response.message);
                    } else {
                        window.location.href = window.location.pathname;
                    }
                } catch (error) {
                    console.error('Error parsing response:', error);
                    alert('An unexpected error occurred.');
                }
            };

            xhr.send(data);
        });


//         let pass_form = document.getElementById('pass_form');

// pass_form.addEventListener('submit', function(e) {
//     e.preventDefault();

//     let new_pass = pass_form.elements['new_pass'].value;
//     let confirm_pass = pass_form.elements['confirm_pass'].value;

//     //// Check if new password matches confirm password
//     if (new_pass !== confirm_pass) {
//         alert('error', 'Passwords do not match');
//         return false; // Stop form submission
//     }

//     let data = new FormData();
//     data.append('pass_form', ''); // Indicate form submission
//     data.append('new_pass', new_pass); // Append new password
//     data.append('confirm_pass', confirm_pass); // Append confirm password

//     let xhr = new XMLHttpRequest();
//     xhr.open("POST", 'ajax/profile.php', true);

//     xhr.onload = function() {
//         if (this.responseText === 'mismatch') {
//             alert('error', "Passwords do not match");
//         } else if (this.responseText === '0') {
//             alert('error', "Update failed!");
//         } else {
//             alert('success', "Changes saved successfully");
//         }
//     }

//     // Send the form data
//     xhr.send(data);
// });

    </script>

</body>

</html>