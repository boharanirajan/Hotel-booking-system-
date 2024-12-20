<?php
require('../inc/essentials.php'); // Include the file with the select() and update() functions
require('../inc/db_config.php');

// get all users
if (isset($_POST['get_users'])) {
    $res = selectAll('users');  // Assuming selectAll is a predefined function to get all users
    $i = 1;  // Used for row numbering
    $path = USER_IMG_PATH;  // Define your image path constant
    $data = "";

    while ($row = mysqli_fetch_assoc($res)) {
    
      

        $status = "<button onclick='toggleStatus($row[id], 0)' class='btn btn-success btn-sm shadow-none'>Active</button>";
        // Check if the user is verified
        $verified = "<span class='badge bg-warning'><i class='bi bi-x-lg'></i></span>";
        if ($row['is_varified']) {
            $verified = "<span class='badge bg-success'><i class='bi bi-check-lg'></i></span>";
        }

        // Status toggle button
        $status = "<button onclick='toggleStatus($row[id], 0)' class='btn btn-success btn-sm shadow-none'>Active</button>";
        if (!$row['status']) {
            $status = "<button onclick='toggleStatus($row[id], 1)' class='btn btn-danger btn-sm shadow-none'>Inactive</button>";
        }

          

        // Format date
        $date = date("Y-m-d", strtotime($row['datetime']));

        // Append the row's data to $data
        $data .= "
        <tr>
            <td>$i</td>
            <td>
                <img src='{$path}{$row['profile']}' width='55px'>
                <br>
                {$row['name']}
            </td>
            <td>{$row['email']}</td>
            <td>{$row['phone_no']}</td>
            <td>{$row['address']}</td>
            <td>{$row['dob']}</td>
            <td>$verified</td>
            <td>$status</td>
            <td>$date</td>
            <td>
             <button type='button' onclick='remove_users($row[id])' 
        class='btn btn-danger shadow-none btn-sm'>
        <i class='bi bi-trash'></i>
        </button>
    <button type='button' onclick='verify_users($row[id])' 
        class='btn btn-success shadow-none btn-sm'>
        <i class='bi bi-check-circle'></i>
      </button>
            </td>
        </tr>";
        
        $i++;
    }

    // Output the constructed table rows
    echo $data;
}




// Handle the AJAX request for toggling user status
if (isset($_POST['toggleStatus'])) {
    $frm_data = filteration($_POST);  // Assuming filteration is your custom sanitization function
    $q = "UPDATE `users` SET `status`=? WHERE `id`=?";
    $v = [$frm_data['value'], $frm_data['toggleStatus']];  // Bind the parameters: new status value and user ID

    // Assuming update() is a predefined function that executes the query with the parameters
    if (update($q, $v, 'ii')) {
        echo '1';  // Success response
    } else {
        echo '0';  // Failure response
    }
}

// // Handle the AJAX request for removing a user
if (isset($_POST['remove_user'])) {
    $frm_data = filteration($_POST);  // Assuming filteration is your sanitization function
    $res = delete("DELETE FROM `users` WHERE `id`=?", [$frm_data['user_id']], 'i');  // Assuming delete() is the method you use for database deletion

    if ($res) {
        echo 1;  // Success response
    } else {
        echo 0;  // Failure response
    }
}



if (isset($_POST['verify_user'])) {
    $frm_data = filteration($_POST);
    $res = update("UPDATE `users` SET `is_varified`=1 WHERE `id`=?", [$frm_data['user_id']], 'i');

    echo $res ? 1 : 0;
}
// search users 

if (isset($_POST['search_user'])) {   
    $frm_data = filteration($_POST);
    $search_term = "%" . $frm_data['search_user'] . "%"; // Format the search term

    // Prepare the statement
    $query = "SELECT * FROM `users` WHERE `name` LIKE ?";
    $stmt = mysqli_prepare($con, $query);
    
    if (!$stmt) {
        die('Query preparation failed: ' . mysqli_error($con)); // Check for errors in preparation
    }

    // Bind the parameter
    mysqli_stmt_bind_param($stmt, 's', $search_term); 
    // Execute the statement
    if (!mysqli_stmt_execute($stmt)) {
        die('Query execution failed: ' . mysqli_stmt_error($stmt)); // Check for errors in execution
    }
    
    // Get the result set
    $result = mysqli_stmt_get_result($stmt); 
    if (!$result) {
        die('Getting result failed: ' . mysqli_stmt_error($stmt)); // Check for errors in fetching results
    }

    $i = 1; // Row numbering
    $path = USER_IMG_PATH; // Image path constant
    $data = "";

    // Check if any rows are returned
    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            // Delete button for user
            // $del_btn = "<button type='button' onclick='remove_users($row[id])' class='btn btn-danger shadow-none btn-sm'>
            //             <i class='bi bi-trash'></i>
            //             </button>";

            // Check if the user is verified
            $verified = "<span class='badge bg-warning'><i class='bi bi-x-lg'></i></span>";
            if ($row['is_varified']) {
                $verified = "<span class='badge bg-success'><i class='bi bi-check-lg'></i></span>";
            }

            // Status toggle button
            $status = "<button onclick='toggleStatus($row[id], 0)' class='btn btn-success btn-sm shadow-none'>Active</button>";
            if (!$row['status']) {
                $status = "<button onclick='toggleStatus($row[id], 1)' class='btn btn-danger btn-sm shadow-none'>Inactive</button>";
            }

            // Format date
            $date = date("Y-m-d", strtotime($row['datetime']));

            // Append the row's data to $data
            $data .= "
            <tr>
                <td>$i</td>
                <td>
                    <img src='{$path}{$row['profile']}' width='55px'>
                    <br>
                    {$row['name']}
                </td>
                <td>{$row['email']}</td>
                <td>{$row['phone_no']}</td>
                <td>{$row['address']}</td>
                <td>{$row['dob']}</td>
                <td>$verified</td>
                <td>$status</td>
                <td>$date</td>
                        <td>  <button type='button' onclick='remove_users($row[id])' 
                class='btn btn-danger shadow-none btn-sm'>
                <i class='bi bi-trash'></i>
                </button>
            <button type='button' onclick='verify_users($row[id])' 
                class='btn btn-success shadow-none btn-sm'>
                <i class='bi bi-check-circle'></i>
            </button></td>
                    </tr>";
            
            $i++;
        }
    } else {
        $data .= "<tr><td colspan='10' class='text-center'>No users found.</td></tr>"; // Handle no results
    }

    // Output the constructed table rows
    echo $data;
}

?>