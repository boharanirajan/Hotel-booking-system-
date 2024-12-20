


// Get  all users
function get_users() {
    let xhr = new XMLHttpRequest();
    xhr.open("POST", 'mail.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

    xhr.onload = function() {
        document.getElementById('users-data').innerHTML = this.responseText;
    };

    xhr.send('get_users');
}


window.onload = function() {
    get_users();
    
}

// Toggle status using AJAX
function toggleStatus(id, val) {
    let xhr = new XMLHttpRequest();
    xhr.open("POST", 'mail.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

    xhr.onload = function() {
        if (this.responseText.trim() === '1') {
            alert('success', 'Status toggled successfully!');
            get_users();  // Assuming get_users() refreshes the user list after status is toggled
        } else {
            alert('error', 'Server error or status not updated!');
        }
    };

    // Send the user ID and new status value to the PHP backend
    xhr.send('toggleStatus=' + id + '&value=' + val);
}



// // Function to remove user via AJAX
// function remove_users(user_id) {
//     if (confirm("Are you sure you want to delete this user?")) {
//         let data = new FormData();
//         data.append('user_id', user_id);
//         console.log(user_id);
//         data.append('remove_user', '');  // Flag to trigger the user removal in the PHP script

//         let xhr = new XMLHttpRequest();
//         xhr.open("POST", 'mail.php', true);  // Corrected file path to match your setup

//         xhr.onload = function() {
//             if (this.responseText.trim() == '1') {
//                 alert('success', 'User removed successfully!');
//                 get_users();  // Assuming get_users() refreshes the user list after deletion
//             } else {
//                 alert('error', 'User removal failed!');
//             }
//         };

//         xhr.send(data);  // Send data to server
//     }
// }

// user verification
function verify_users(user_id) {
    if (confirm("Are you sure you want to verify this user?")) {
        let data = new FormData();
        data.append('user_id', user_id);
        data.append('verify_user', '');

        let xhr = new XMLHttpRequest();
        xhr.open("POST", 'mail.php', true);

        xhr.onload = function() {
            const response = JSON.parse(this.responseText); // Parse JSON response
            if (response.status === 'success') {
                alert('success', response.message); // Success message
                get_users(); // Refresh user list
            } else {
                alert('error', response.message); // Error message
            }
        };

        xhr.send(data);
    }
}

// Function to remove user via AJAX
function remove_users(user_id) {
    if (confirm("Are you sure you want to delete this user?")) {
        let data = new FormData();
        data.append('user_id', user_id);
        console.log(user_id);
        data.append('remove_user', '');  // Flag to trigger the user removal in the PHP script

        let xhr = new XMLHttpRequest();
        xhr.open("POST", 'mail.php', true);  // Ensure the correct file path is used

        xhr.onload = function() {
            if (this.responseText.trim() == '1') {
                alert('success', 'User removed successfully!');
                get_users();  // Assuming get_users() refreshes the user list after deletion
            } else {
                alert('error', 'User removal failed!');
            }
        };

        xhr.send(data);  // Send data to server
    }
}




// Function to search user via AJAX
function search_user(username) {
    let xhr = new XMLHttpRequest();
    xhr.open("POST", 'mail.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

    xhr.onload = function() {
        document.getElementById('users-data').innerHTML = this.responseText;
    };

    // Corrected the parameter sending format
    xhr.send('search_user=' + encodeURIComponent(username)); // Ensure the username is properly encoded
}



