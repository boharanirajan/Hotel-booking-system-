
<?php
require('../inc/essentials.php'); // Include the file with the select() and update() functions
require('../inc/db_config.php'); // Database configuration file
// require('../inc/link.php');


// Fetch general settings
if (isset($_POST['get_general'])) {
    $q = "SELECT * FROM `settings` WHERE `sr_no` = ?";
    $values = [1];
    $res = select($q, $values, "i");
    $data = mysqli_fetch_assoc($res);
    echo json_encode(['success' => true, 'site_title' => $data['site_title'], 'site_about' => $data['site_about']]);
}



// Update general settings
if (isset($_POST['action']) && $_POST['action'] == 'upd_general') {
    $frm_data = filteration($_POST);
    $q = "UPDATE settings SET site_title = ?, site_about = ? WHERE sr_no = ?";
    $values = [$frm_data['site_title'], $frm_data['site_about'], 1];
    $res = update($q, $values, 'ssi');
    echo $res; // Returns 1 if successful
}

// Update shutdown settings
if (isset($_POST['action']) && $_POST['action'] == 'upd_shutdown') {
    $shutdown_status = $_POST['upd_shutdown'];
    $q = "UPDATE settings SET shutdown = ? WHERE sr_no = ?";
    $values = [$shutdown_status, 1];
    $res = update($q, $values, 'ii');
    echo $res; // Returns 1 if successful
}







// Fetch contact us settings
if (isset($_POST['action']) && $_POST['action'] == 'get_contacts') {
    $q = "SELECT * FROM `contact_details` WHERE `sr_no` = ?";
    $values = [1];
    $res = select($q, $values, "i");
    
    if ($res) {
        $data = mysqli_fetch_assoc($res);
        echo json_encode($data);
    } else {
        echo json_encode(['error' => 'Failed to fetch contact settings']);
    }
}
// Update contact settings
if (isset($_POST['action']) && $_POST['action'] == 'upd_contacts') {
    $frm_data = filteration($_POST);
    $q = "UPDATE `contact_details` SET `address`=?, `gmap`=?, `pn1`=?, `pn2`=?, `email`=?, `tw`=?, `fb`=?, `lnk`=?, `insta`=?, `iFrame`=? WHERE `sr_no`=?";
    $values = [$frm_data['address'], $frm_data['gmap'], $frm_data['pn1'], $frm_data['pn2'], $frm_data['email'], $frm_data['tw'], $frm_data['fb'], $frm_data['lnk'], $frm_data['insta'], $frm_data['iframe'], 1];

    $res = update($q, $values, "ssssssssssi");
    echo $res; // Should return 1 on success
}
// add member
if (isset($_POST['add_member'])) {
    $frm_data = filteration($_POST);
    $img_r = uploadImage($_FILES['picture'], ABOUT_FOLDER);
    
    if ($img_r == 'inv_img' || $img_r == 'inv_size' || $img_r == 'upd_failed') {
        echo $img_r;
    } else {
        $q = "INSERT INTO `team_details`(`name`, `picture`) VALUES (?, ?)";
        $values = [$frm_data['name'], $img_r];
        $res = insert($q, $values, 'ss');
        echo $res ? '1' : '0';  // Return '1' if success, '0' if failed
    }
}

//  get member
if (isset($_POST['get_member'])) {
    $res = selectAll('team_details');
    while ($row = mysqli_fetch_assoc($res)) {
        $path = ABOUT_IMG_PATH;
        echo <<<data
        <div class="col-md-2 mb-4">
            <div class="card bg-dark text-white">
                <img src="{$path}{$row['picture']}" class="card-img">
                <div class="card-img-overlay text-end">
                    <button onclick="rem_member({$row['sr_no']})" type="button" class="btn btn-danger btn-sm shadow-none">
                        <i class="bi bi-trash-fill"></i> Delete
                    </button>
                </div>
                <p class="card-text text-center px-3 py-2">{$row['name']}</p>
            </div>
        </div>
      data;

    }
}


// delete member
if (isset($_POST['rem_member'])) {
    $frm_data = filteration($_POST);
    $values = [$frm_data['rem_member']];
    
    $q = "SELECT * FROM `team_details` WHERE `sr_no` = ?";
    $res = select($q, $values, 'i');
    $img = mysqli_fetch_assoc($res);
    
    if (deleteImage($img['picture'], ABOUT_FOLDER)) {
        $q = "DELETE FROM `team_details` WHERE `sr_no` = ?";
        $res = delete($q, $values, 'i');
        echo $res ? '1' : '0';  // Return '1' if success, '0' if failed
    } else {
        echo '0';  // Return '0' if image deletion failed
    }
}

?>
