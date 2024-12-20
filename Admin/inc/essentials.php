<?php
// Define the base URL of the site
define('SITE_URL', 'http://127.0.0.1/HBS/');

// Define the path for images
define('ABOUT_IMG_PATH', SITE_URL . 'image/about/');
define('FACILITY_IMG_PATH', SITE_URL . 'image/Facilities/');
define('ROOMS_IMG_PATH', SITE_URL . 'image/rooms/');
define('USER_IMG_PATH', SITE_URL . 'image/user/');

// Define the backend upload path for images
define('UPLOAD_IMAGE_PATH', $_SERVER['DOCUMENT_ROOT'] . '/HBS/image/');

// Define folder names for organization
define('ABOUT_FOLDER', 'about/');
define('FACILITY_FOLDER', 'Facilities/');

// define folder names for room stored 
define('ROOMS_FOLDER', 'rooms/');

// define flolder names for user image stored
define('USER_FOLDER', 'user/');

// set Email and user name


 function adminlogin() {
    session_start();
    if (!isset($_SESSION['adminlogin']) || $_SESSION['adminlogin'] !== true) {
        // User is not logged in, redirect to index.php
        echo "<script>
            window.location.href='index.php';
        </script>";
        exit(); // Ensure no further code is executed after redirection
    }
}

function redirect($url) {
    echo "<script type='text/javascript'>
            window.location.href = '$url';
          </script>";
}

function alert($type, $msg) {
    $bs_class = ($type == "success") ? "alert-success" : "alert-danger";
    echo <<<alert
    <div class="alert $bs_class alert-dismissible fade show custom-alert" role="alert">
        <strong>$msg</strong> 
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
alert;
}
function uploadImage($image, $folder) {
    $valid_mime = ['image/jpeg', 'image/png','image/jpg'];  // Remove 'webp' if not needed
    $img_mime = $image['type'];

    if (!in_array($img_mime, $valid_mime)) {
        return 'inv_img';  // Invalid image mime type
    } elseif (($image['size'] / 1024 / 1024) > 2) {
        return 'inv_size';  // Invalid size greater than 2MB
    } else {
        $ext = pathinfo($image['name'], PATHINFO_EXTENSION);
        $rname = 'IMG_' . random_int(11111, 99999) . '.' . $ext;
        $img_path = UPLOAD_IMAGE_PATH . $folder . $rname;

        if (move_uploaded_file($image['tmp_name'], $img_path)) {
            return $rname;  // Image uploaded successfully
        } else {
            return 'upd_failed';  // Upload failed
        }
    }
}


function deleteImage($image, $folder) {
    $img_path = UPLOAD_IMAGE_PATH . $folder . $image;
    if (file_exists($img_path) && unlink($img_path)) {
        return true;
    } else {
        return false;
    }
}
function uploadSVGImage($image, $folder) {
    $valid_mime = ['image/jpeg', 'image/png', 'image/jpg', 'image/svg+xml'];
    $img_mime = $image['type'];

    // Check if MIME type is valid
    if (!in_array($img_mime, $valid_mime)) {
        return 'inv_img'; // Return a specific error code
    }

    // Check file size
    if ($image['size'] > 2 * 1024 * 1024) { // Size greater than 2MB
        return 'inv_size'; // Return a specific error code
    }

    // Get file extension and generate a new filename
    $ext = pathinfo($image['name'], PATHINFO_EXTENSION);
    $rname = 'IMG_' . random_int(11111, 99999) . '.' . $ext;
    $img_path = UPLOAD_IMAGE_PATH . $folder . $rname;

    // Check if folder exists and is writable
    if (!is_dir(UPLOAD_IMAGE_PATH . $folder) || !is_writable(UPLOAD_IMAGE_PATH . $folder)) {
        return 'Upload directory is not writable.'; // Handle this error separately if needed
    }

    // Attempt to move the uploaded file
    if (move_uploaded_file($image['tmp_name'], $img_path)) {
        return $rname; // Image uploaded successfully
    } else {
        return 'upd_failed'; // Return a specific error code for server issues
    }
}

// user image upload 
function uploadUserImage($image)
{
    $valid_mime = ['image/jpeg', 'image/png'];  // Valid image MIME types
    $img_mime = $image['type'];

    // Check if the uploaded image is of a valid type
    if (!in_array($img_mime, $valid_mime)) {
        return 'inv_img';  // Invalid image mime type
    }

    // Generate a random name for the image file
    $rname = 'IMG_' . random_int(11111, 99999) . ".jpeg";  // Random file name
    $img_path = UPLOAD_IMAGE_PATH . USER_FOLDER . $rname;  // Destination path

    // Move the uploaded file to the destination path
    if (move_uploaded_file($image['tmp_name'], $img_path)) {
        return $rname;  // Return the new file name on success
    } else {
        return 'upd_failed';  // Upload failed
    }
}






?>
