<!-- ------Footer------- -->
<div class="container-fluid  bg-white mt-5">
  <div class="row">
    <div class="col-lg-4 p-4">
      <h3 class="h-font fm-bold fs-3 mb-2"> <?php echo$settings_r['site_title'] ?></h3>
      <p><?php echo$settings_r['site_about'] ?></p>
    </div>
    <div class="col-lg-4 p-4">
      <h5 class="mb-3">Links</h5>
      <a href="index.php" class="d-inline-block mb-2 text-dark text-decoration-none">Home</a><br>
      <a href="room.php" class="d-inline-block mb-2 text-dark text-decoration-none">Rooms</a><br>
      <a href="facilities.php" class="d-inline-block mb-2 text-dark text-decoration-none">Facilities</a><br>
      <a href="contactus.php" class="d-inline-block mb-2 text-dark text-decoration-none">Contact us</a><br>
      <a href="about.php" class="d-inline-block mb-2 text-dark text-decoration-none">About</a>

    </div>
    <div class="col-lg-4 p-4">
      <h5 class="mb-3">Follow us</h5>
      <a href="<?php echo $contact_r['tw'] ?>" class="inline-block mb-2 text-dark text-decoration-none">
        <i class="bi bi-twitter-x me-1"></i> Twitter </a>
      <br>
      <a href="<?php echo $contact_r['fb'] ?>" class="inline-block mb-2 text-dark text-decoration-none">
        <i class="bi bi-facebook me-1"></i> Fackbook
      </a>
      <br>
      <a href="<?php echo $contact_r['lnk']; ?>" class="inline-block mb-2 text-dark text-decoration-none">
          <i class="bi bi-linkedin"></i> LinkedIn
      </a>
      <br>
      <a href="<?php echo $contact_r['insta']; ?>" class="inline-block text-dark text-decoration-none">
          <i class="bi bi-instagram"></i> Instagram
      </a>

    </div>
  </div>
</div>
<!-- bootstrap script -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

 <script src="inc/script.js"></script>
<script>
  // Simplified setActive function without using navbar variable
function setActive() {
  document.querySelectorAll('nav a').forEach(link => {
    let file = link.href.split('/').pop();
    let file_name = file.split('.')[0];
    if (document.location.href.includes(file_name)) {
      link.classList.add('active');
    }
  });
}
setActive();

</script>


<script>
  // registration form 
  let register_form = document.getElementById('register_form');
register_form.addEventListener('submit', (e) => {
    e.preventDefault(); // Prevent default form submission

    // Collect form data
    let data = new FormData();
    data.append('name', register_form.elements['name'].value);
    data.append('email', register_form.elements['email'].value);
    data.append('phonenum', register_form.elements['phonenum'].value);
    data.append('address', register_form.elements['address'].value);
    data.append('dob', register_form.elements['dob'].value);
    data.append('pass', register_form.elements['pass'].value);
    data.append('cpass', register_form.elements['cpass'].value);
    data.append('profile', register_form.elements['profile'].files[0]); // Profile image
    data.append('register', 'true');

    // Hide modal after submission
    let modalElement = document.getElementById('registerModal');
    let modalInstance = bootstrap.Modal.getInstance(modalElement);
    modalInstance.hide();

    // AJAX request to the server
    let xhr = new XMLHttpRequest();
    xhr.open("POST", 'login.php', true);

    xhr.onload = function () {
        console.log(this.responseText); // Debugging response

        try {
            let response = JSON.parse(this.responseText);

            if (response.status === 'error') {
                // Handle various error messages
                switch (response.message) {
                    case 'pass_invalid':
                        alert('danger', "Password must be storng!");
                        break;
                    case 'pass_mismatch':
                        alert('danger', "Passwords do not match.");
                        break;
                    case 'email_already':
                        alert('danger', "This email is already registered.");
                        break;
                    case 'phone_already':
                        alert('danger', "This phone number is already registered.");
                        break;
                    case 'inv_img':
                        alert('danger', "Only JPG and PNG images are allowed.");
                        break;
                    case 'upd_failed':
                        alert('danger', "Image upload failed.");
                        break;
                    case 'mail_failed':
                        alert('danger', "Failed to send confirmation email.");
                        break;
                    case 'ins_failed':
                        alert('danger', "Registration failed. Please try again.");
                        break;
                    default:
                        alert('danger', "An unknown error occurred.");
                }
            } else if (response.status === 'success') {
                alert('success', "Registration successful! ");//  add A confirmation link has been sent to your email.
                register_form.reset(); // Reset the form on success
            } else {
                alert('danger', "An unexpected error occurred.");
            }
        } catch (error) {
            alert('danger', "Error parsing server response: " + error.message);
        }
    };

    // Send the form data
    xhr.send(data);
});



// Login form
let loginForm = document.getElementById('login_form');

loginForm.addEventListener('submit', (e) => {
  e.preventDefault();  // Prevent default form submission behavior

  // Create FormData object to hold form data
  let data = new FormData(loginForm);
  data.append('login', '');  // Append 'login' key

  // Hide the modal after submission
  let loginModal = document.getElementById('loginModal');
  let modal = bootstrap.Modal.getInstance(loginModal);
  modal.hide();

  // Create XMLHttpRequest for AJAX call
  let xhr = new XMLHttpRequest();
  xhr.open("POST", 'login.php', true);

  // Handle response from the server
  xhr.onload = function() {
    if (this.responseText == 'inv_email_mob') {
      alert('error', "Invalid Email or Mobile number");
    } else if (this.responseText == 'not_verified') {
      alert('error', "Email is not verified");
    } else if (this.responseText == 'inactive') {
      alert('error', "Account Suspended! Please contact admin.");
    } else if (this.responseText == 'invalid_pass') {
      alert('error', "Incorrect Password");
    } else if (this.responseText == '1') {  // Check for success response '1'
      // Redirect to a new page after successful login
      let fileurl= window.location.href.split('/').pop().split('?').shift();
      if(fileurl='room_details.php')
         {
           window.location=window.location.href;
         }
         else
         {
         
         window.location=window.location.pathname;
         }

    } else {
      alert('error', "An unexpected error occurred. Please try again later.");
    }
  };

  // Send the form data
  xhr.send(data);
});





// forgot from 
let forgot_form = document.getElementById('forgot_form');

forgot_form.addEventListener('submit', (e) => {
  e.preventDefault();  // Prevent default form submission behavior

  // Create FormData object to hold form data
  let data = new FormData(forgot_form);
  data.append('email', forgot_form.elements['email'].value);
  data.append('forgot_pass', '');  // Append 'forgot_pass' key

  // Hide the modal after submission
  let forgotModal = document.getElementById('forgotModal');
  let modal = bootstrap.Modal.getInstance(forgotModal);
  modal.hide();

  // Create XMLHttpRequest for AJAX call
  let xhr = new XMLHttpRequest();
  xhr.open("POST", 'login.php', true);  // Corrected file name if this is specific to forgot password

  // Handle response from the server
  xhr.onload = function() {
    if (this.responseText == 'inv_email') {
      alert('error', "Invalid Email");
    } else if (this.responseText == 'not_verified') {
      alert('error', "Email is not verified");
    } else if (this.responseText == 'inactive') {
      alert('error', "Account Suspended! Please contact admin.");
    } else if (this.responseText == 'mail_failed') {
      alert('error', "Cannot send email. Server down");
    } else if (this.responseText == 'upd_failed') {
      alert('error', "Account recovery failed. Server down");
    } else {
      alert('success', "Reset link sent to email");
      forgot_form.reset();
    }
  };

  // Send the form data
  xhr.send(data);
});

// check login user after book know 
    function checkloginbook(status,room_id)
    {
      if(status)
    {
      window.location.href='confirm_booking.php?id='+room_id;
    }
    else{
      var loginModal = new bootstrap.Modal(document.getElementById('loginModal'));
      loginModal.show();
    }
    }


</script>
