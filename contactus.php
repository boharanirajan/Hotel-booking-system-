<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us page</title>

    <?php require('inc/link.php'); ?>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

</head>

<body class="bg-Light">
    <!-- ------------------header----------- -->
    <?php
    require('inc/header.php');

    ?>

 <div class="my-5 px-4">
        <h2 class="fw-bold text-center h-font">OUR CONTACT US</h2>

        <div class="h-line bg-dark mb-5"></div>
       

        <div class="container">
            <div class="row">

                <!-- Map and Contact Info Section -->
                <div class="col-lg-6 col-md-6 mb-5 px-4">
                    <div class="bg-white rounded shadow p-4">

                        <!-- Google Maps Embed -->
                        <iframe class="w-100 rounded mb-4" height="400"
                            src="<?php echo $contact_r['iFrame']; ?>"
                            loading="lazy" referrerpolicy="no-referrer-when-downgrade">
                        </iframe>

                        <!-- Address Section -->
                        <h5>Address</h5>
                        <a href="https://maps.app.goo.gl/6ZNaaX7tjVaXQMZE8" target="_blank"
                            class="d-inline-block text-decoration-none text-dark mb-2">
                            <i class="bi bi-geo-alt-fill"></i> <?php echo $contact_r['address']; ?>
                        </a>

                        <!-- Call Us Section -->
                        <h5 class="mt-4">Call us</h5>
                        <a href="tel:+9779847119089" class="d-inline-block mb-2 text-decoration-none text-dark">
                            <i class="bi bi-telephone"></i> +<?php echo $contact_r['pn1']; ?>
                        </a>

                        <!-- Email Section -->
                        <h5 class="mt-4">Email</h5>
                        <a href="mailto:nirajanbohara66@gmail.com" class="d-inline-block mb-2 text-decoration-none text-dark">
                            <i class="bi bi-envelope"></i> <?php echo $contact_r['email']; ?>
                        </a>

                        <!-- Follow Us Section -->
                        <h5 class="mt-4">Follow Us</h5>
                        <a href="<?php echo $contact_r['tw']; ?>" class="d-inline-block text-dark fs-5 me-2">
                            <i class="bi bi-twitter me-1"></i>
                        </a>
                        <a href="<?php echo $contact_r['fb']; ?>" class="d-inline-block text-dark fs-5 me-2">
                            <i class="bi bi-facebook me-1"></i>
                        </a>
                        <a href="<?php echo $contact_r['lnk']; ?>" class="d-inline-block text-dark fs-5 me-2">
                            <i class="bi bi-linkedin me-1"></i>
                        </a>
                        <a href="<?php echo $contact_r['insta']; ?>" class="d-inline-block text-dark fs-5">
                            <i class="bi bi-instagram me-1"></i>
                        </a>

                    </div>
                </div>

                <!-- Facilities Section -->
                <div class="col-lg-6 col-md-6 px-4">
                    <div class="bg-white rounded shadow p-4">
                        <form method="post">
                            <h5>Send a message</h5>
                            <div class="mt-3">
                                <label class="form-label" style="font-weight:500">Name</label>
                                <input name="name" type="text" class="form-control shadow-none" required>
                            </div>
                            <div class="mt-3">
                                <label class="form-label" style="font-weight:500">Email</label>
                                <input name="email" type="email" class="form-control shadow-none" required>
                            </div>
                            <div class="mt-3">
                                <label class="form-label" style="font-weight:500">Subject</label>
                                <input name="subject" type="text" class="form-control shadow-none" required>
                            </div>
                            <div class="mt-3">
                                <label class="form-label" style="font-weight:500">Message</label>
                                <textarea name="message" class="form-control shadow-none" rows="5" style="resize: none;" required></textarea>
                            </div>
                            <button name="send" type="submit" class="btn text-white custom-bg mt-3">Send</button>
                        </form>
                    </div>
                </div>

            </div>
        </div>
        

        <!-- PHP script to handle form submission -->
        <?php
        if (isset($_POST['send'])) {
            // Sanitize the form data
            $frm_data = filteration($_POST);

            // SQL query with placeholders for prepared statement
            $q = "INSERT INTO `user_queries` (`name`, `email`, `subject`, `message`) 
            VALUES (?, ?, ?, ?)";

            // Values to bind to the placeholders
            $values = [$frm_data['name'], $frm_data['email'], $frm_data['subject'], 
            $frm_data['message']];

            // Use the insert function to execute the query with binding
            $res = insert($q, $values, 'ssss');

            // Show appropriate feedback to the user based on the result
            if ($res == 1) {
                alert('success', 'Message submitted successfully');
            } else {
                alert('error', 'Cannot submit data! Try again later.');
            }
        }
        ?>
        <!-- Alert Section: Appears in the top-right corner -->
        
        </div>


        <script>
            
        </script>

        <!-- ------Footer------- -->
        <?php require('inc/footer.php');
        ?>
    


</body>

</html>