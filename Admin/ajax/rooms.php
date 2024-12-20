<?php
require('../inc/essentials.php'); // Include the file with the select() and update() functions
require('../inc/db_config.php');


// ADD ROOM
if (isset($_POST['add_room'])) {
    $features = filteration(json_decode($_POST['feature'])); // Fix typo, should match the form 'feature'
    $facilities = filteration(json_decode($_POST['facility'])); // Correct variable name
    $frm_data = filteration($_POST);

    // Insert room details
    $q1 = "INSERT INTO `rooms`(`name`, `area`, `price`, `quantity`, `adult`, `children`, `description`
    ) VALUES (?,?,?,?,?,?,?)";

    $values = [
        $frm_data['name'],
        $frm_data['area'],
        $frm_data['price'],
        $frm_data['quantity'],
        $frm_data['adult'],
        $frm_data['children'],
        $frm_data['description']
    ];

    // If room insertion is successful
    if (insert($q1, $values, 'siiiiis')) {
        $flag = 1;
    }

    // Get the last inserted room ID
    $room_id = mysqli_insert_id($con);

    // Insert facilities
    $q2 = "INSERT INTO `rooms_facilities`(`room_id`, `facility_id`) VALUES (?,?)";

    if ($stmt = mysqli_prepare($con, $q2)) {
        foreach ($facilities as $f) {
            mysqli_stmt_bind_param($stmt, 'ii', $room_id, $f);
            mysqli_stmt_execute($stmt);
        }
        mysqli_stmt_close($stmt);
    } else {
        $flag = 0;
        die('query cannot be prepared - insert facilities');
    }

    // Insert features
    $q3 = "INSERT INTO `rooms_feature`( `room_id`, `feature_id`) VALUES (?,?)";

    if ($stmt = mysqli_prepare($con, $q3)) {
        foreach ($features as $f) {
            mysqli_stmt_bind_param($stmt, 'ii', $room_id, $f);
            mysqli_stmt_execute($stmt);
        }
        mysqli_stmt_close($stmt);
    } else {
        $flag = 0;
        die('query cannot be prepared - insert features');
    }

    // Final response
    if ($flag) {
        echo 1;  // Success
    } else {
        echo 0;  // Error
    }
}

/// Get room details 
if (isset($_POST['get_rooms'])) {
    $frm_data = filteration($_POST);
    $res1 = select("SELECT * FROM `rooms` WHERE `id`=?", [$frm_data['get_room']], 'i');
    $res2 = select("SELECT * FROM `rooms_feature` WHERE `room_id`=?", [$frm_data['get_room']], 'i');
    $res3 = select("SELECT * FROM `rooms_facilities` WHERE `room_id`=?", [$frm_data['get_room']], 'i');

    $roomdata = mysqli_fetch_assoc($res1);
    $features = [];
    $facilities = [];  // Initialize the facilities array

    if (mysqli_num_rows($res2) > 0) {
        while ($row = mysqli_fetch_assoc($res2)) {
            array_push($features, $row['feature_id']);  // Correct 'feature_id' spelling
        }
    }

    if (mysqli_num_rows($res3) > 0) {
        while ($row = mysqli_fetch_assoc($res3)) {
            array_push($facilities, $row['facility_id']);
        }
    }

    $data = ["roomdata" => $roomdata, "features" => $features, "facilities" => $facilities];
    $data = json_encode($data);
    echo $data;
}

// edit data submit
if (isset($_POST['edit_room'])) {
    $features = filteration(json_decode($_POST['feature'])); // Fix typo, should match the form 'feature'
    $facilities = filteration(json_decode($_POST['facility'])); // Correct variable name
    $frm_data = filteration($_POST);
    $flag = 0;
    $q = "UPDATE `rooms` SET `name`=?,`area`=?,`price`=?,`quantity`=?,`adult`=?,
    `children`=?,`description`=? WHERE `id`=?";
    $values =
        $values = [
            $frm_data['name'],
            $frm_data['area'],
            $frm_data['price'],
            $frm_data['quantity'],
            $frm_data['adult'],
            $frm_data['children'],
            $frm_data['description'],
            $frm_data['room_id']
        ];
    if (update($q, $values, 'siiiiisi')) {
        $flag = 1;
    }
    $del_feature = delete("DELETE FROM `rooms_feature` WHERE `room_id`=?", [$frm_data['room_id']], 'i');
    $del_facility = delete("DELETE FROM `rooms_facilities` WHERE `room_id`=?", [$frm_data['room_id']], 'i');

    if (!($del_feature && $del_facility)) {
        $flag = 0;
    }


    // Insert facilities
    $q2 = "INSERT INTO `rooms_facilities`(`room_id`, `facility_id`) VALUES (?,?)";

    if ($stmt = mysqli_prepare($con, $q2)) {
        foreach ($facilities as $f) {
            mysqli_stmt_bind_param($stmt, 'ii', $frm_data['room_id'], $f);
            mysqli_stmt_execute($stmt);
        }
        mysqli_stmt_close($stmt);
        $flag = 1;
    } else {
        $flag = 0;
        die('query cannot be prepared - insert facilities');
    }

    // Insert features
    $q3 = "INSERT INTO `rooms_feature`( `room_id`, `feature_id`) VALUES (?,?)";

    if ($stmt = mysqli_prepare($con, $q3)) {
        foreach ($features as $f) {
            mysqli_stmt_bind_param($stmt, 'ii', $frm_data['room_id'], $f);
            mysqli_stmt_execute($stmt);
        }
        $flag = 1;
        mysqli_stmt_close($stmt);
    } else {
        $flag = 0;
        die('query cannot be prepared - insert features');
    }

    // Final response
    if ($flag) {
        echo 1;  // Success
    } else {
        echo 0;  // Error
    }
}

// get all rooms 
if (isset($_POST['get_all_rooms'])) {
    $res = select("SELECT * FROM `rooms` WHERE `remove` = ?", [0], 'i');

    $i = 1;

    $data = "";
    while ($row = mysqli_fetch_assoc($res)) {
        if ($row['status'] == 1) {
            $status = "<button onclick='toggleStatus($row[id],0)' class='btn btn-success btn-sm shadow-none'>Active</button>";
        } else {
            $status = "<button onclick='toggleStatus($row[id],1)' class='btn btn-warning btn-sm shadow-none'>Inactive</button>";
        }

        $data .= "
        <tr class='align-middle'>
            <td>$i</td>
            <td>$row[name]</td>
            <td>$row[area] sq. ft</td>
            <td>
                <span class='badge rounded-pill bg-light text-dark'>
                    Adult: $row[adult]
                </span> <br/>
                <span class='badge rounded-pill bg-light text-dark'>
                    Children: $row[children]
                </span>
            </td>
            <td>रु$row[price]</td>
            <td>$row[quantity]</td>
            <td>$status</td>
            <td>
              <button type='button' onclick='edit_details($row[id])' class='btn btn-primary shadow-none btn-sm' data-bs-toggle='modal'
               data-bs-target='#edit-room'>
             <i class='bi bi-pencil-square'></i>
            </button>
           <button type='button' onclick='room_images($row[id], \"$row[name]\")' 
           class='btn btn-info shadow-none btn-sm' data-bs-toggle='modal' data-bs-target='#room-images'>
                <i class='bi bi-images'></i>
            </button>
            <button type='button' onclick='remove_room($row[id])'class='btn btn-danger shadow-none btn-sm'>
                <i class='bi bi-trash'></i>
            </button>
     </td>
        </tr>
    ";
        $i++;
    }
    echo $data;
}
//  toggleStatus
if (isset($_POST['toggleStatus'])) {
    $frm_data = filteration($_POST);
    $q = "UPDATE `rooms` SET `status`=? WHERE `id`=?";
    $v = [$frm_data['value'], $frm_data['toggleStatus']];

    if (update($q, $v, 'ii')) {
        echo '1';  // Success
    } else {
        echo '0';  // Failure
    }
}
// add images

if (isset($_POST['add_image'])) {
    $frm_data = filteration($_POST);
    $img_r = uploadImage($_FILES['image'], ROOMS_FOLDER);

    if ($img_r == 'inv_img') {
        echo 'inv_img';
    } else if ($img_r == 'inv_size') {
        echo 'inv_size';
    } else if ($img_r == 'upd_failed') {
        echo 'upd_failed';
    } else {
        $q = "INSERT INTO `rooms_images` (`room_id`, `image`) VALUES (?, ?)";
        $values = [$frm_data['room_id'], $img_r];
        $res = insert($q, $values, 'is');

        // Return '1' on success, '0' on failure
        if ($res) {
            echo '1';  // Success
        } else {
            echo '0';  // Failure
        }
    }
}


// get_room_images
if (isset($_POST['get_room_images'])) {
    $frm_data = filteration($_POST);
    $res = select(
        "SELECT * FROM `rooms_images` WHERE `room_id` = ?",
        [$frm_data['get_room_images']],
        'i'
    );
    $path = ROOMS_IMG_PATH;

    // Initialize the variable at the beginning
    $thumb_btn = ""; // Default value

    // Check if any images are returned
    if ($res && mysqli_num_rows($res) > 0) {
        while ($row = mysqli_fetch_assoc($res)) {
            // Reset $thumb_btn for each row
            $thumb_btn = ""; 

            if ($row['thumb'] == 1) {
                $thumb_btn = "<i class='bi bi-check-lg text-light bg-success px-2 py-1 rounded fs-5'></i>";
            }
            else
            {
                $thumb_btn ="<button onclick='thumb_image({$row['sr_no']}, {$row['room_id']})' class='btn btn-secondary
                 btn-sm shadow-none'><i class='bi bi-check-lg'></i>
                    </button>"; 
            }

            echo <<<data
            <tr class='align-middle'>
                <td><img src='{$path}{$row['image']}' class='img-fluid'></td>
                <td>{$thumb_btn}</i></td>
                <td>
                    <button onclick='rem_image({$row['sr_no']}, {$row['room_id']})' class='btn btn-danger btn-sm shadow-none'>
                        <i class='bi bi-trash'></i>
                    </button>
                </td>
            </tr>
            data;            
        }
    } 
}




// rem image from sever 
if (isset($_POST['rem_image'])) {
    $frm_data = filteration($_POST);
    $values = [$frm_data['image_id'], $frm_data['room_id']];

    $pre_q = "SELECT * FROM `rooms_images` WHERE `sr_no`=? AND `room_id`=?";
    $res = select($pre_q, $values, 'ii');
    $img = mysqli_fetch_assoc($res);

    // Check if an image was found
    if ($img) {
        // Attempt to delete the image file
        if (deleteImage($img['image'], ROOMS_FOLDER)) {
            $q = "DELETE FROM `rooms_images` WHERE `sr_no`=? AND `room_id`=?";
            $res = delete($q, $values, 'ii');
            echo $res; // Return the result of the delete operation
        } else {
            echo 0; // Image deletion failed
        }
    } else {
        echo 0; // No image found for the provided identifiers
    }
}



// Thumb image 
if (isset($_POST['thumb_image'])) {
    $frm_data = filteration($_POST);

    // Reset all images' thumb status for the given room
    $pre_q = "UPDATE `rooms_images` SET `thumb` = ? WHERE `room_id` = ?";
    $pre_v = [0, $frm_data['room_id']];
    $pre_res = update($pre_q, $pre_v, 'ii');

    // Set the selected image as the thumbnail
    $q = "UPDATE `rooms_images` SET `thumb` = ? WHERE `sr_no` = ? AND `room_id` = ?";
    $v = [1, $frm_data['image_id'], $frm_data['room_id']];  // Set thumb to 1
    $res = update($q, $v, 'iii');

    echo $res;
}

//  remove room
if (isset($_POST['remove_room'])) {
    $frm_data = filteration($_POST);
    $res1=select("SELECT * FROM `rooms_images` WHERE `room_id`=?",[$frm_data['room_id']],'i');

    while($row=mysqli_fetch_assoc($res1))
    {
        deleteImage($row['image'],ROOMS_FOLDER);
    }

    $res2=delete("DELETE FROM `rooms_images` WHERE  `room_id`=?",[$frm_data['room_id']],'i');
    $res3=delete("DELETE FROM `rooms_feature` WHERE `room_id`=?",[$frm_data['room_id']],'i');
    $res4=delete("DELETE FROM `rooms_facilities`WHERE  `room_id`=?",[$frm_data['room_id']],'i');
    $res5=delete("UPDATE  `rooms` SET `remove`=? WHERE `id`=?",[1,$frm_data['room_id']],'ii');

    if($res2|| $res3|| $res4||$res5)
    {
        echo 1;
    }
    else{
        echo 0;
    }
    

}

