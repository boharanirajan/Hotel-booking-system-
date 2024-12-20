
// Get booking details
function get_bookings(search='') {
    let xhr = new XMLHttpRequest();
    xhr.open("POST", 'ajax/refund_bookings.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

    // Optional: Show loading indicator before sending the request
    document.getElementById('table-data').innerHTML = "<tr><td colspan='5' class='text-center'>Loading...</td></tr>";

    xhr.onload = function() {
        // Check if the request was successful
        if (xhr.status === 200) {
            document.getElementById('table-data').innerHTML = this.responseText; // Ensure the ID matches
        } else {
            // Handle errors, e.g., server issues or bad requests
            document.getElementById('table-data').innerHTML = "<tr><td colspan='5' class='text-center'>Error loading bookings. Please try again later.</td></tr>";
        }
    };

    xhr.onerror = function() {
        // Handle network errors
        document.getElementById('table-data').innerHTML = "<tr><td colspan='5' class='text-center'>Network error. Please check your connection.</td></tr>";
    };

    // Send the request with the parameter
    xhr.send('get_bookings&search='+search);
}

// Call the function when the page loads
window.onload = function() {
    get_bookings();
};




// booking refunds
function refund_booking(id)
{
    if (confirm("Are you sure you want to refund this booking?")) {
        let data = new FormData();
        data.append('booking_id', id);
        data.append('refund_booking', '');  // Flag to trigger the user removal in the PHP script

        let xhr = new XMLHttpRequest();
        xhr.open("POST", 'ajax/refund_bookings.php', true);  // Corrected file path to match your setup

        xhr.onload = function() {
            if (this.responseText.trim() == '1') {
                alert('success', 'Money refund!');
                get_bookings();  
            } else {
                alert('error', 'Server Down!');
            }
        };

        xhr.send(data);  // Send data to server
    }
}

















// Function to search user via AJAX
function search_user(username) {
    let xhr = new XMLHttpRequest();
    xhr.open("POST", 'ajax/users.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

    xhr.onload = function() {
        document.getElementById('users-data').innerHTML = this.responseText;
    };

    // Corrected the parameter sending format
    xhr.send('search_user=' + encodeURIComponent(username)); // Ensure the username is properly encoded
}


window.onload = function() {
    get_bookings();
}
