/// Fetch booking details from the server
function get_bookings(search = '', page = 1) {
    let xhr = new XMLHttpRequest();
    xhr.open("POST", 'ajax/booking_records.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

    // Display loading message before receiving data
    document.getElementById('table-data').innerHTML = "<tr><td colspan='5' class='text-center'>Loading...</td></tr>";

    xhr.onload = function() {
        // Parse the JSON response from the server
        let data = JSON.parse(this.responseText);
        if (xhr.status === 200) {
            // Update table data and pagination
            document.getElementById('table-data').innerHTML = data.table_data;
            document.getElementById('table-pagination').innerHTML = data.pagination;
        } else {
            // Handle any errors
            document.getElementById('table-data').innerHTML = "<tr><td colspan='5' class='text-center'>Error loading bookings. Please try again later.</td></tr>";
        }
    };

    xhr.onerror = function() {
        // Handle network errors
        document.getElementById('table-data').innerHTML = "<tr><td colspan='5' class='text-center'>Network error. Please check your connection.</td></tr>";
    };

    // Send the search and page parameters to the server
    xhr.send('get_bookings&search=' + search + '&page=' + page);
}

// Call the function to load bookings when the page is loaded
window.onload = function() {
    get_bookings();
};

// Change the page based on user input
function change_page(page) {
    get_bookings(document.getElementById('search_input').value, page);
}

// download function 
function download(id)
{
window.location.href='generate_pdf.php?gen_pdf&id='+id;
}