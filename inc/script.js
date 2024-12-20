
// Alert function with auto-removal of the alert after 2 seconds
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
    setTimeout(remAlert, 3000);

    function remAlert() {
        if (element) {
            element.remove();
        }
    }
}