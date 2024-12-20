<?php 
require('Admin/inc/essentials.php'); // Include the file with helper functions
require('Admin/inc/db_config.php');  // Include the database configuration file
require('Admin/inc/vendor/autoload.php'); // Ensure TCPDF is autoloaded

session_start();
if (!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
    redirect('index.php');
}

if (isset($_GET['id'])) {
    $frm_data = filteration($_GET);

    // SQL query to retrieve bookings
    $query = "SELECT bo.*, bd.*, uc.email
              FROM `booking_order` bo 
              INNER JOIN `booking_details` bd ON bo.booking_id = bd.booking_id
              INNER JOIN `users` uc ON bo.user_id = uc.id
              WHERE ((bo.booking_status = 'booked' AND bo.arrival = '1') 
              OR (bo.booking_status = 'canceled' AND bo.refund = 1) 
              OR (bo.booking_status = 'Payment failed'))
              AND bo.booking_id = '{$frm_data['id']}'";

    $res = mysqli_query($con, $query);

    // Check if query executed successfully
    if (!$res) {
        die('Query Error: ' . mysqli_error($con));
    }

    $total_rows = mysqli_num_rows($res); // Get the total number of rows

    // Check if the result set is empty
    if ($total_rows == 0) {
        // Output an informative message and redirect if no rows are found
        echo "No data found for the specified booking ID.";
        exit; // Stop further processing
    }

    $data = mysqli_fetch_assoc($res);

    // Ensure that the price is set and is a valid number
    $price = (isset($data['price']) && is_numeric($data['price'])) ? $data['price'] : 0;

    // Format the dates
    $date = date("h:i A d-m-Y", strtotime($data['datetime'])); // Booking date
    $checkin = date("d-m-Y", strtotime($data['check_in'])); // Check-in date
    $checkout = date("d-m-Y", strtotime($data['check_out'])); // Check-out date

    // Generate the table data for the booking receipt
    $table_data = "
        <h2>Booking Receipt</h2>
        <table border='1' cellpadding='10' cellspacing='0'>
            <tr> 
                <td><strong>Order ID:</strong> {$data['order_id']}</td>
                <td><strong>Booking Date:</strong> $date</td>
            </tr>
            <tr>
                <td colspan='2'><strong>Status:</strong> {$data['booking_status']}</td>
            </tr>
            <tr>
                <td><strong>Room:</strong> {$data['room_name']}</td>
                <td><strong>Room Number:</strong> {$data['room_no']}</td>
            </tr>
            <tr>
                <td><strong>Price:</strong> Rs.{$price}</td>
                <td><strong>Username:</strong> {$data['user_name']}</td>
            </tr>
            <tr>
                <td><strong>Check-in:</strong> {$checkin}</td>
                <td><strong>Check-out:</strong> {$checkout}</td>
            </tr>
            <tr>
                <td colspan='2'><strong>Email:</strong> {$data['email']}</td>
            </tr>
        </table>";

    // Create a new PDF document instance
    $pdf = new TCPDF();
    $pdf->AddPage();
    $pdf->SetFont("helvetica", "B", 16);
    $pdf->Cell(0, 15, "Hotel Booking Receipt", 0, 1, "C");
    $pdf->SetFont("helvetica", "", 12);
    $pdf->writeHTML($table_data, true, false, true, false, '');
    
    // Output the PDF as a file download
    $pdf->Output("booking_receipt_{$data['order_id']}.pdf", "D");
} else {
    // Redirect if no ID is provided
    redirect('bookings.php');
}
?>
