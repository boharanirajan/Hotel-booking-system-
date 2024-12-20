
// Add room
let add_room_form = document.getElementById('add_room_form');
add_room_form.addEventListener('submit', function(e) {
    e.preventDefault();
    add_room();
});

function add_room() {
    const data = new FormData();
    data.append('add_room', '');
    data.append('name', add_room_form.elements['name'].value);
    data.append('area', add_room_form.elements['area'].value);
    data.append('price', add_room_form.elements['price'].value);
    data.append('quantity', add_room_form.elements['quantity'].value);
    data.append('adult', add_room_form.elements['adult'].value);
    data.append('children', add_room_form.elements['children'].value);
    data.append('description', add_room_form.elements['description'].value);

    // Convert NodeList to array for features and facilities
    let features = Array.from(add_room_form.elements['feature']);
    let facilities = Array.from(add_room_form.elements['facility']);

    let selectedFeatures = features.filter(element => element.checked).map(element => element.value);
    let selectedFacilities = facilities.filter(element => element.checked).map(element => element.value);

    data.append('feature', JSON.stringify(selectedFeatures));
    data.append('facility', JSON.stringify(selectedFacilities));

    let xhr = new XMLHttpRequest();
    xhr.open("POST", 'ajax/rooms.php', true);

    xhr.onload = function() {
        const myModal = document.getElementById('add-room');
        const modal = bootstrap.Modal.getInstance(myModal);
        modal.hide();

        console.log('Response:', this.responseText);

        if (this.responseText.trim() == '1') {
            alert('success', 'New room added');
            add_room_form.reset();
            get_all_rooms();
        } else {
            alert('error', 'Server Down or Room already exists');
        }
    };

    xhr.send(data);
}

// Get rooms 
function get_all_rooms() {
    let xhr = new XMLHttpRequest();
    xhr.open("POST", 'ajax/rooms.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

    xhr.onload = function() {
        document.getElementById('room-data').innerHTML = this.responseText;
    };

    xhr.send('get_all_rooms');
}

// Edit room details 
function edit_details(id) {
    let edit_room_form = document.getElementById('edit_room_form');
    let xhr = new XMLHttpRequest();
    xhr.open("POST", 'ajax/rooms.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

    xhr.onload = function() {
        let data = JSON.parse(this.responseText);
        edit_room_form.elements['name'].value = data.roomdata.name;
        edit_room_form.elements['area'].value = data.roomdata.area;
        edit_room_form.elements['price'].value = data.roomdata.price;
        edit_room_form.elements['quantity'].value = data.roomdata.quantity;
        edit_room_form.elements['adult'].value = data.roomdata.adult;
        edit_room_form.elements['children'].value = data.roomdata.children;
        edit_room_form.elements['description'].value = data.roomdata.description;
        edit_room_form.elements['room_id'].value = data.roomdata.id;

        edit_room_form.elements['feature'].forEach(element => {
            element.checked = data.features.includes(Number(element.value));
        });

        edit_room_form.elements['facility'].forEach(element => {
            element.checked = data.facilities.includes(Number(element.value));
        });
    };

    xhr.send('get_rooms=1&get_room=' + id);
}

// Submit edited room data
let edit_room_form = document.getElementById('edit_room_form');
edit_room_form.addEventListener('submit', function(e) {
    e.preventDefault();
    submit_edit_room();
});

function submit_edit_room() {
    let data = new FormData();
    data.append('edit_room', '');
    data.append('room_id', edit_room_form.elements['room_id'].value);
    data.append('name', edit_room_form.elements['name'].value);
    data.append('area', edit_room_form.elements['area'].value);
    data.append('price', edit_room_form.elements['price'].value);
    data.append('quantity', edit_room_form.elements['quantity'].value);
    data.append('adult', edit_room_form.elements['adult'].value);
    data.append('children', edit_room_form.elements['children'].value);
    data.append('description', edit_room_form.elements['description'].value);

    let feature = [];
    edit_room_form.elements['feature'].forEach(element => {
        if (element.checked) {
            feature.push(element.value); // Push the value of the checked element
        }
    });

    let facility = [];
    edit_room_form.elements['facility'].forEach(element => {
        if (element.checked) {
            facility.push(element.value); // Push the value of the checked element
        }
    });

    data.append('feature', JSON.stringify(feature));
    data.append('facility', JSON.stringify(facility));

    let xhr = new XMLHttpRequest();
    xhr.open("POST", 'ajax/rooms.php', true);

    xhr.onload = function() {
        const myModal = document.getElementById('edit-room');
        const modal = bootstrap.Modal.getInstance(myModal);
        modal.hide();

        console.log('Response:', this.responseText);

        // Compare the response with the string "1"
        if (this.responseText.trim() === "1") {
            alert('success', 'Room data updated');
            edit_room_form.reset();
            get_all_rooms();
        } else {
            alert('error', 'Server Down or Room already exists');
        }
    };

    xhr.send(data);
}



// Toggle status
function toggleStatus(id, val) {
    let xhr = new XMLHttpRequest();
    xhr.open("POST", 'ajax/rooms.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

    xhr.onload = function() {
        if (this.responseText.trim() === '1') {
            alert('success', 'Status toggled successfully!');
            get_all_rooms();
        } else {
            alert('error', 'Server error or status not updated!');
        }
    };

    xhr.send('toggleStatus=' + id + '&value=' + val);
}

let add_image_form = document.getElementById('add_image_form');

add_image_form.addEventListener('submit', function(e) {
    e.preventDefault();
    add_image();

});

function add_image() {
    let data = new FormData();
    data.append('image', add_image_form.elements['image'].files[0]);
    data.append('room_id', add_image_form.elements['room_id'].value);
    data.append('add_image', ' ');

    let xhr = new XMLHttpRequest();
    xhr.open("POST", 'ajax/rooms.php', true);

    xhr.onload = function() {
        if (this.responseText == 'inv_img') {
            alert('error', 'Only JPG, WEBP, PNG images are allowed!','image-alert');
        } else if (this.responseText == 'inv_size') {
            alert('error', 'Image should be less than 2MB','image-alert' );
        } else if (this.responseText == 'upd_failed') {
            alert('error', 'Image upload failed. Server Down','image-alert');
        } else {
            alert('success', ' New image added!','image-alert');
            add_image_form.reset();
        }
    }
    xhr.send(data);

}




function room_images(id, rname) {
    // Set modal title
    document.querySelector("#room-images .modal-title").innerText = rname;

    // Select the form element
    const add_image_form = document.querySelector('#add_image_form');

    // Check if form exists
    if (add_image_form) {
        // Set room_id value in the form
        add_image_form.elements['room_id'].value = id;
        add_image_form.elements['image'].value = ''; // Reset image input

        // Create XMLHttpRequest to get images
        let xhr = new XMLHttpRequest();
        xhr.open("POST", 'ajax/rooms.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

        xhr.onload = function() {
            // Update the room image data section with the response
            document.getElementById('room-image-data').innerHTML = this.responseText;
        };

        // Correctly send the request with proper parameters
        xhr.send('get_room_images=' + id);
    } else {
        console.error("Form element not found in DOM.");
    }
}

// remove remove 
function rem_image(img_id, room_id) {
let data = new FormData();
data.append('image_id', img_id);
data.append('room_id', room_id); // Changed img_id to room_id
data.append('rem_image', ' ');

let xhr = new XMLHttpRequest();
xhr.open("POST", 'ajax/rooms.php', true);

xhr.onload = function() {
if (this.responseText == 1) {
    alert('success', 'Image removed!','image-alert');
    room_images(room_id, document.querySelector("#room-images .modal-title").innerText);
} else {
    alert('error', 'Image removal failed!','image-alert'); // Changed 'success' to 'error'
    add_image_form.reset();
}
}
xhr.send(data);
}

// thumb image 
function thumb_image(img_id, room_id) {
let data = new FormData();
data.append('image_id', img_id);
data.append('room_id', room_id);  // Correctly sets room_id
data.append('thumb_image', 'true');  // Better to provide a meaningful value

let xhr = new XMLHttpRequest();
xhr.open("POST", 'ajax/rooms.php', true);

xhr.onload = function() {
if (this.status === 200) {  // Check for a successful status code
    if (this.responseText.trim() == '1') {
        alert('success', 'Image thumbnail changed!','image-alert');
        // Reload room images after successful thumbnail update
        room_images(room_id, document.querySelector("#room-images .modal-title").innerText);
    } else {
        alert('error', 'Thumbnail update failed!','image-alert');
        if (typeof add_image_form !== 'undefined') {
            add_image_form.reset();  // Reset form only if defined
        }
    }
} else {
    alert('error', 'Server returned an error: ' + this.status);
}
};

xhr.onerror = function() {
console.error('Request failed:', xhr.responseText);
alert('error', 'There was a problem with the request.');
};

xhr.send(data);
}


// remove rooms
function remove_room(room_id) {
if(confirm("Are you are, you want to delete this room?"))
{
let data = new FormData();
data.append('room_id', room_id);
data.append('remove_room', ' '); 
let xhr = new XMLHttpRequest();
xhr.open("POST", 'ajax/rooms.php', true);

xhr.onload = function() {
    if (this.responseText == 1) {
        alert('success', 'Room remove sucessfully!','image-alert');
      get_all_rooms();
       
    } 
    else {
        alert('error', 'Room remove failed','image-alert');         
} 
};

xhr.send(data);
}
}



window.onload = function() {
    get_all_rooms();
}
