<?php
require('../inc/essentials.php'); // Include the file with the select() and update() functions
require('../inc/db_config.php');  // Database configuration file

// Add feature
if (isset($_POST['add_feature'])) {
    $frm_data = filteration($_POST);
    $q = "INSERT INTO `feature` (`name`) VALUES (?)";
    $values = [$frm_data['name']];
    $res = insert($q, $values, 's');
    echo $res;  // Return 1 if successful, 0 if failed
}

// Get features
if (isset($_POST['get_feature'])) {
    $i = 1;
    $res = selectAll('feature');
    while ($row = mysqli_fetch_assoc($res)) {
        echo <<<DATA
        <tr>
            <td>{$i}</td>
            <td>{$row['name']}</td>
            <td>
                <button onclick="rem_feature({$row['id']})" type="button" 
                class="btn btn-danger btn-sm shadow-none">
                <i class="bi bi-trash-fill"></i> Delete
                </button>
            </td>  
        </tr>
        DATA;
        $i++;
    }
}

// Remove Feature
// Remove Feature
// Remove Feature
if (isset($_POST['rem_feature'])) {
    $frm_data = filteration($_POST);
    $values = [$frm_data['rem_feature']];

    

    // Check if the feature is associated with any room
    $check_q = select('SELECT * FROM `rooms_feature` WHERE `feature_id` = ?', $values, 'i');

    // Debug: Check if the query returned results
    if (mysqli_num_rows($check_q) == 0) {
       

        // If feature is not associated with a room, proceed to delete it
        $q = "DELETE FROM `feature` WHERE `id` = ?";
        $delete_res = delete($q, $values, 'i');

        // Debug: Output the delete query result
        echo $delete_res ? '1' : '0';  // '1' for success, '0' for failure
    } else {
        echo 'room_added';  // Feature is added in a room, cannot delete
    }
}


// add facility 
if (isset($_POST['add_facility'])) {
    $frm_data = filteration($_POST);
    
    // Upload image
    $img_r = uploadSVGImage($_FILES['icon'], FACILITY_FOLDER);
    
    // Check image upload response
    if ($img_r == 'inv_img') {
        error_log("Image upload error: Invalid image format.");
        echo 'inv_img'; // Send specific error code
        return;
    } elseif ($img_r == 'inv_size') {
        error_log("Image upload error: Size exceeds 2MB.");
        echo 'inv_size'; // Send specific error code
        return;
    } elseif ($img_r == 'upd_failed') {
        error_log("Image upload error: Server issue.");
        echo 'upd_failed'; // Send specific error code
        return;
    }

    // Insert into database
    $q = "INSERT INTO `facility` (`icon`, `name`, `description`) VALUES (?, ?, ?)";
    $values = [$img_r, $frm_data['name'], $frm_data['description']];
    $res = insert($q, $values, 'sss');

    // Log the result of the insertion
    error_log("Insert result: " . ($res ? 'Success' : 'Failed'));
    echo $res ? '1' : '0';  // Return '1' if success, '0' if failed
}



// Get facility
if (isset($_POST['get_facility'])) {
    $i = 1;
    $res = selectAll('facility');
    while ($row = mysqli_fetch_assoc($res)) {
        $path = FACILITY_IMG_PATH; // Assuming this constant is defined somewhere
        echo <<<DATA
        <tr>
            <td>$i</td>
            <td><img src="{$path}{$row['icon']}" width="100px"></td>
            <td>{$row['name']}</td>
            <td>{$row['description']}</td>
            <td>
                <button onclick="rem_facility({$row['id']})" type="button" 
                class="btn btn-danger btn-sm shadow-none">
                <i class="bi bi-trash-fill"></i> Delete
                </button>
            </td>  
        </tr>
        DATA;
        $i++;
    }
}
// Remove Facility
// Remove Facility
if (isset($_POST['rem_facility'])) { 
    $frm_data = filteration($_POST);
    $values = [$frm_data['rem_facility']];

    // Check if the facility is associated with any room
    $check_q = select('SELECT * FROM `rooms_facilities` WHERE `facility_id` = ?', $values, 'i');

    if (mysqli_num_rows($check_q) == 0) {
        // Fetch the facility data (icon to be deleted)
        $res = select('SELECT `icon` FROM `facility` WHERE `id` = ?', $values, 'i');
        if (mysqli_num_rows($res) > 0) {
            $img = mysqli_fetch_assoc($res);

            // Attempt to delete the image
            if (deleteImage($img['icon'], FACILITY_FOLDER)) {
                // If image deletion is successful, proceed to delete the facility
                $q = "DELETE FROM `facility` WHERE `id` = ?";
                $delete_res = delete($q, $values, 'i');
                echo $delete_res ? '1' : '0';  // '1' for success, '0' for failure
            } else {
                echo '0';  // Failed to delete the image
            }
        } else {
            echo 'facility_not_found';  // Facility not found in the database
        }
    } else {
        echo 'room_added';  // Facility is added in a room, cannot delete
    }
}





?>
