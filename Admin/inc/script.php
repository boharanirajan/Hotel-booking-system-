
<!-- bootstrap script -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>


<!-- alert function -->
<script>
function alert(type, msg, position = 'body') {
    let bs_class = (type === 'success') ? 'alert-success' : 'alert-danger';
    let element = document.createElement('div');

    element.innerHTML = `
    <div class="alert ${bs_class} alert-dismissible fade show custom-alert" role="alert">
        <strong>${msg}</strong> 
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>`;
    
    // Append the alert to the desired position
    if (position === 'body') {
        document.body.appendChild(element); // Append to body if position is 'body'
    } else {
        let targetElement = document.getElementById(position);
        if (targetElement) {
            targetElement.appendChild(element); // Append to the specified element by ID
        }
    }

    // Set timeout to remove the alert after 2 seconds
    setTimeout(remAlert, 2000);

    function remAlert() {
        if (element) {
            element.remove();
        }
    }
}




   // active the tab e.g home 
   function setActive() {
    let navbar = document.getElementById('dashboard-menu');
    let a_tags = navbar.getElementsByTagName('a');
    for (let i = 0; i < a_tags.length; i++) {
        let file = a_tags[i].href.split('/').pop(); // Get the file name from the href
        let file_name = file.split('.')[0]; // Remove the extension

        // Check if the current URL contains the file name
        if (document.location.href.indexOf(file_name) >= 0) {
            a_tags[i].classList.add('active'); // Add 'active' class
        } else {
            a_tags[i].classList.remove('active'); // Optionally remove 'active' class if not matching
        }
    }
}

// Call the function when the DOM is fully loaded
document.addEventListener('DOMContentLoaded', setActive);

</script>