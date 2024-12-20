
/// Get booking details
function get_bookings(search='') {
    let xhr = new XMLHttpRequest();
    xhr.open("POST", 'new_bookings_mail.php', true);
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


// Assign room number

// let assign_room_form = document.getElementById('assign_room_form');

// function assign_room(id) {
//     assign_room_form.elements['booking_id'].value = id; // Set the booking_id value in the form
// }

//     assign_room_form.addEventListener('submit', function (e) {
//     e.preventDefault(); // Prevent the default form submission

//     let data = new FormData();
//     data.append('room_no', assign_room_form.elements['room_no'].value);
//     data.append('booking_id', assign_room_form.elements['booking_id'].value);
//     data.append('assign_room', true); // Ensure this is included

//     const xhr = new XMLHttpRequest();
//     xhr.open("POST", 'new_bookings_mail.php', true); // Prepare the AJAX request

//     xhr.onload = function () {
//         var myModal = document.getElementById('assign-room');
//         if (myModal) {
//             var modal = bootstrap.Modal.getInstance(myModal);
//             if (modal) {
//                 modal.hide(); // Hide the modal after submission
//             }
//         }

//         console.log("Raw Server Response:", this.responseText); // Log the raw response

//         try {
//             const response = JSON.parse(this.responseText); // Parse the JSON response

//             console.log("Parsed Response:", response); // Check the parsed response

//             // Handle the parsed response based on status and message
//             if (response && response.status && response.message) {
//                 if (response.status === 'success') {
//                     alert('success', response.message); // Show success message using custom alert
//                     assign_room_form.reset(); // Reset the form
//                 } else if (response.status === 'error') {
//                     alert('error', response.message); // Show error message using custom alert
//                 }
//             } else {
//                 alert('error', 'Unexpected response structure from server.'); // Handle unexpected response format
//             }
//         } catch (error) {
//             console.error("Error parsing response:", error); // Log parsing errors
//             showAlert('error', 'Error parsing server response. Please try again.'); // Handle JSON parsing errors
//         }
//     };

//     xhr.onerror = function() {
//         alert('error', 'An error occurred while processing the request.'); // Handle request errors
//     };

//     xhr.send(data); // Send the form data
// });

let assign_room_form = document.getElementById('assign_room_form');

function assign_room(id) {
        assign_room_form.elements['booking_id'].value = id; // Set the booking_id value in the form
    }

    assign_room_form.addEventListener('submit', function (e) {
        e.preventDefault(); // Prevent the default form submission

        let data = new FormData();
        data.append('room_no', assign_room_form.elements['room_no'].value);
        data.append('booking_id', assign_room_form.elements['booking_id'].value);
        data.append('assign_room', true); // Ensure this is included

        const xhr = new XMLHttpRequest();
        xhr.open("POST", 'new_bookings_mail.php', true); // Prepare the AJAX request

        xhr.onload = function () {
            var myModal = document.getElementById('assign-room');
            if (myModal) {
                var modal = bootstrap.Modal.getInstance(myModal);
                if (modal) {
                    modal.hide(); // Hide the modal after submission
                }
            }

            console.log("Raw Server Response:", this.responseText); // Log the raw response

            try {
                const response = JSON.parse(this.responseText); // Parse the JSON response

                console.log("Parsed Response:", response); // Check the parsed response

                // Handle the parsed response based on status and message
                if (response && response.status && response.message) {
                    if (response.status === 'success') {
                        alert('success', response.message); // Show success message using custom alert
                        assign_room_form.reset(); // Reset the form
                    } else if (response.status === 'error') {
                        alert('error', response.message); // Show error message using custom alert
                    }
                } else {
                    alert('error', 'Unexpected response structure from server.'); // Handle unexpected response format
                }
            } catch (error) {
                console.error("Error parsing response:", error); // Log parsing errors
                alert('error', 'Error parsing server response. Please try again.'); // Handle JSON parsing errors
            }
        };

        xhr.onerror = function() {
            alert('error', 'An error occurred while processing the request.'); // Handle request errors
        };

        xhr.send(data); // Send the form data
    });

// booking cancel
// function cancel_booking(id)
// {
//     if (confirm("Are you sure you want to cancel this booking?")) {
//         let data = new FormData();
//         data.append('booking_id', id);
//         data.append('cancel_booking', '');  // Flag to trigger the user removal in the PHP script

//         let xhr = new XMLHttpRequest();
//         xhr.open("POST", 'new_bookings_mail.php', true);  // Corrected file path to match your setup

//         xhr.onload = function() {
//             if (this.responseText.trim() == '1') {
//                 alert('success', 'Booking cancelled!');
//                 get_bookings();  
//             } else {
//                 alert('error', 'Server Down!');
//             }
//         };

//         xhr.send(data);  // Send data to server
//     }
// }

function cancel_booking(id) {
    if (confirm("Are you sure you want to cancel this booking?")) {
        let data = new FormData();
        data.append('booking_id', id);
        data.append('cancel_booking', true);  // Flag to trigger the cancellation in the PHP script

        let xhr = new XMLHttpRequest();
        xhr.open("POST", 'new_bookings_mail.php', true);  // Corrected file path to match your setup

        // Handle the server response
        xhr.onload = function() {
            try {
                const response = JSON.parse(this.responseText); // Parse the JSON response
                console.log("Parsed Response:", response);

                if (response.status === 'success') {
                    alert('success', 'Booking cancelled and email sent!');
                    get_bookings();  // Reload the bookings after cancellation
                } else {
                    alert('error', response.message || 'An error occurred during cancellation.');
                }
            } catch (error) {
                console.error("Error parsing response:", error);
                alert('error', 'Failed to process the cancellation request.');
            }
        };

        // Handle errors during the request
        xhr.onerror = function() {
            alert('error', 'An error occurred while communicating with the server.');
        };

        xhr.send(data);  // Send the cancellation request to the server
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
