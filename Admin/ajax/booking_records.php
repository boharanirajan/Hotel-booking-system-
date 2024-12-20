<?php
require('../inc/essentials.php'); // Include the file with helper functions
require('../inc/db_config.php');  // Include the database configuration file

if (isset($_POST['get_bookings'])) {

    $frm_data = filteration($_POST); // Sanitize the input data

    // Pagination parameters
    $limit = 5; // Set limit to show one record per page
    $page = $frm_data['page']; // Get the current page number
    $start = ($page - 1) * $limit; // Calculate the starting row for the query

    // SQL query to retrieve bookings with the necessary JOIN and search conditions
    $query = "SELECT bo.*, bd.* 
              FROM `booking_order` bo 
              INNER JOIN `booking_details` bd ON bo.booking_id = bd.booking_id
              WHERE ((bo.booking_status = 'booked' AND bo.arrival = '1') 
              OR (bo.booking_status='canceled' AND bo.refund=1) 
              OR (bo.booking_status='Payment failed'))
              AND (bo.order_id LIKE ? OR bd.phone_no LIKE ? OR bd.user_name LIKE ?) 
              ORDER BY bo.booking_id DESC";

    // Prepare search parameter with wildcards for LIKE queries
    $searchParam = "%{$frm_data['search']}%";
    $params = [$searchParam, $searchParam, $searchParam]; // Parameters for prepared statement
    $types = 'sss'; // Three strings for the search parameters

    // Fetch all results (without pagination)
    $res = select($query, $params, $types); // Call the select function with the query and parameters

    // Add the LIMIT clause for pagination
    $limit_query = $query . " LIMIT $start, $limit";
    $limit_res = select($limit_query, $params, $types); // Fetch the paginated results

    $i = $start + 1; // Initialize row counter
    $table_data = ""; // Initialize the table data variable
    $total_rows = mysqli_num_rows($res); // Get the total number of rows

    // Check if the result set is empty
    if ($total_rows == 0) {
        // If no data found, return an empty table and pagination
        $output = json_encode(['table_data' => "<b>No bookings found</b>", 'pagination' => '']);
        echo $output;
        exit; // Stop further processing
    }

    // Process each row and generate table rows
    while ($data = mysqli_fetch_assoc($limit_res)) {
        $date = date("d-m-Y", strtotime($data['datetime'])); // Format the booking date
        $checkin = date("d-m-Y", strtotime($data['check_in'])); // Format the check-in date
        $checkout = date("d-m-Y", strtotime($data['check_out'])); // Format the check-out date

        // Determine status background class based on booking status
        if ($data['booking_status'] == 'booked') {
            $status_bg = 'bg-success';
        } elseif ($data['booking_status'] == 'canceled') {
            $status_bg = 'bg-danger';
        } else {
            $status_bg = 'bg-warning text-dark';
        }

        // Generate table row data with booking details
        $table_data .= "
            <tr>
                <td>$i</td>
                <td>
                    <span class='badge bg-primary'>Order ID: {$data['order_id']}</span><br>
                    <b>Name:</b> {$data['user_name']}<br>
                    <b>Phone No:</b> {$data['phone_no']}
                </td>
                <td>
                    <b>Room:</b> {$data['room_name']}<br>
                    <b>Price:</b> रु.{$data['price']}
                </td>
                <td>
                    <b>Paid:</b> {$data['trans_amount']}<br>
                    <b>Date:</b> $date
                </td>
                <td>
                    <span class='badge $status_bg'>{$data['booking_status']}</span>
                </td>
                <td>
                    <button onclick='download({$data['booking_id']})' type='button'
                     class='btn text-white btn-danger fw-bold shadow-none'>
                       
                        <i class='bi bi-file-earmark-pdf'></i>
                    </button>
                </td>
            </tr>
        ";
        $i++;
    }

    // Generate pagination if the total rows exceed the limit
    $pagination = "";
    if ($total_rows > $limit) {
        $total_pages = ceil($total_rows / $limit); // Calculate total pages
        
        // Add 'First' page button
        if ($page != 1) {
            $pagination .= "<li class='page-item'><button onclick='change_page(1)' class='page-link'>First</button></li>";
        }

        // Add 'Prev' button
        $disabled = ($page == 1) ? "disabled" : "";
        $prev = $page - 1;
        $pagination .= "<li class='page-item $disabled'><button onclick='change_page($prev)' class='page-link shadow-none'>Prev</button></li>";

        // Add 'Next' button
        $disabled = ($page == $total_pages) ? "disabled" : "";
        $next = $page + 1;
        $pagination .= "<li class='page-item $disabled'><button onclick='change_page($next)' class='page-link'>Next</button></li>";

        // Add 'Last' page button
        if ($page != $total_pages) {
            $pagination .= "<li class='page-item'><button onclick='change_page($total_pages)' class='page-link'>Last</button></li>";
        }
    }

    // Output the generated table rows and pagination
    $output = json_encode(["table_data" => $table_data, "pagination" => $pagination]);
    echo $output;
}
?>
