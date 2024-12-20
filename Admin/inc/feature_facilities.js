
// Add Feature
feature_s_form.addEventListener('submit', function(e) {
    e.preventDefault();
    add_feature();
});

function add_feature() {
    const data = new FormData();
    data.append('name', feature_s_form.elements['feature_name'].value);
    data.append('add_feature', '');

    const xhr = new XMLHttpRequest();
    xhr.open("POST", 'ajax/feature_facilities.php', true);

    xhr.onload = function() {
        const myModal = document.getElementById('feature-s');
        const modal = bootstrap.Modal.getInstance(myModal);
        modal.hide();

        if (this.responseText == 1) {
            alert('success', 'New feature added');
            feature_s_form.reset(); // Clear the form
            get_feature();
        } else {
            alert('error', 'Server Down or Feature already exists');
        }
    };
    xhr.send(data);
}

// Get Features
function get_feature() {
    const xhr = new XMLHttpRequest();
    xhr.open("POST", 'ajax/feature_facilities.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onload = function() {
        if (xhr.status === 200) {
            document.getElementById('features-data').innerHTML = xhr.responseText;
        } else {
            console.error('Error:', xhr.statusText);
        }
    };
    xhr.send('get_feature');
}

// Remove Feature
function rem_feature(val) {
    const xhr = new XMLHttpRequest();
    xhr.open("POST", 'ajax/feature_facilities.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onload = function() {
        if (xhr.status === 200) {
            const response = xhr.responseText.trim();
            console.log("Raw response:", response);  // Debug: Output the raw response

            if (response === '1') {
                alert('success', 'Feature removed.');
                get_feature(); // Refresh feature list
            } else if (response === 'room_added') {
                alert('error', 'Feature is added in a room, cannot delete.');
            } else if (response === 'facility_not_found') {
                alert('error', 'Feature not found in the database.');
            } else {
                alert('error', 'Server down or unexpected error occurred.');
            }
        } else {
            console.error('Error:', xhr.statusText);
        }
    };
    xhr.send('rem_feature=' + val);
}


// Add Facility
facility_s_form.addEventListener('submit', function(e) {
    e.preventDefault();
    add_facility();
});

function add_facility() {
 const data = new FormData();
const facilityName = facility_s_form.elements['facility_name'].value;
const facilityIcon = facility_s_form.elements['facility_icon'].files[0];
const facilityDescription = facility_s_form.elements['facility_description'].value;

// Check if a file is selected
if (!facilityIcon) {
    alert('error', 'Please select an image file.');
    return;
}

data.append('name', facilityName);
data.append('icon', facilityIcon);
data.append('description', facilityDescription);
data.append('add_facility', '');

const xhr = new XMLHttpRequest();
xhr.open("POST", 'ajax/feature_facilities.php', true);

xhr.onload = function() {
    const myModal = document.getElementById('facility-s');
    const modal = bootstrap.Modal.getInstance(myModal);
    modal.hide();

    console.log('Server response:', this.responseText); // Log the server response

    switch (this.responseText) {
        case '1':
            alert('success', 'New facility added');
            get_facility();
            facility_s_form.reset(); // Reset the form after successful addition
            break;
        case 'inv_img':
            alert('error', 'Only SVG, PNG, and JPEG images are allowed.');
            break;
        case 'inv_size':
            alert('error', 'Image should be less than 2MB.');
            break;
        case 'upd_failed':
            alert('error', 'Image upload failed. Server down!');
            break;
        default:
            alert('error', 'Unknown error occurred.');
            console.error('Unexpected response:', this.responseText);
    }
};

xhr.send(data);
}



// Get Facility
function get_facility() {
const xhr = new XMLHttpRequest();
xhr.open("POST", 'ajax/feature_facilities.php', true);
xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

xhr.onload = function() {
    if (xhr.status === 200) {
        document.getElementById('facilities-data').innerHTML = xhr.responseText; // Display retrieved facilities
    } else {
        console.error('Error:', xhr.statusText);
    }
};

xhr.send('get_facility');
}
// Remove Facility aa
function rem_facility(val) {
    const xhr = new XMLHttpRequest();
    xhr.open("POST", 'ajax/feature_facilities.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onload = function() {
        if (xhr.status === 200) {
            const response = xhr.responseText.trim();
            if (response === '1') {
                alert('success', 'Facility removed.');
                get_facility(); // Refresh facility list
            } else if (response === 'room_added') {
                alert('error', 'Facility is added in a room, cannot delete.');
            } else if (response === 'facility_not_found') {
                alert('error', 'Facility not found in the database.');
            } else {
                alert('error', 'Server down or unexpected error occurred.');
            }
        } else {
            console.error('Error:', xhr.statusText);
        }
    };
    xhr.send('rem_facility=' + val);
}

window.onload = function() {
get_feature();
get_facility();
};

window.onload = function() {
    get_feature();
    get_facility();
}
