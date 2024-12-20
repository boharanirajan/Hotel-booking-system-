// Fetch booking details from the server
function bookings_analytics(periods = 1) {
    let xhr = new XMLHttpRequest();
    xhr.open("POST", 'ajax/dashboard.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

    xhr.onload = function() {
        if (this.status === 200) {
            try {
                // Parse the JSON response from the server
                let data = JSON.parse(this.responseText);
                
                // Update the UI with the fetched data
                document.getElementById('total_bookings').textContent = data.total_bookings;
                document.getElementById('total_amt').textContent = 'रु.' + data.total_amt;
                document.getElementById('active_bookings').textContent = data.active_bookings;
                document.getElementById('active_amount').textContent = 'रु.' + data.active_amount;
                document.getElementById('cancelled_bookings').textContent = data.cancelled_bookings;
                document.getElementById('cancelled_amount').textContent = 'रु.' + data.cancelled_amount;
            } catch (e) {
                console.error("Error parsing JSON:", e, this.responseText);
            }
        }
    };

    // Send the request with the period parameter
    xhr.send('bookings_analytics&periods=' + periods);
}

function users_analytics(periods = 1) {
    let xhr = new XMLHttpRequest();
    xhr.open("POST", 'ajax/dashboard.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

    xhr.onload = function() {
        if (this.status === 200) {
            try {
                // Parse the JSON response from the server
                let data = JSON.parse(this.responseText);
                
                // Update the UI with the fetched data
                document.getElementById('total_new_reg').textContent = data.total_new_reg;
                document.getElementById('total_reviews').textContent = data.total_reviews;
                
            } catch (e) {
                console.error("Error parsing JSON:", e, this.responseText);
            }
        }
    };

    // Send the request with the period parameter
    xhr.send('users_analytics&periods=' + periods);
}

// Call the function to load analytics when the page is loaded
window.onload = function() {
    bookings_analytics(); // Calls booking analytics (assuming it's already implemented)
    users_analytics(); // Calls user analytics
};
