<script>
    let general_data, contactus_data;
    const contacts_s_form = document.getElementById('contact_s_form');
    const general_s_form = document.getElementById('general_s_form');
    let team_s_form = document.getElementById('team_s_form');
    let member_name_inp = document.getElementById('member_name_inp');
    let member_picture_inp = document.getElementById('member_picture_inp');

    // Fetch general settings
    // Fetch general settings
    function get_general() {
        const display_site_title = document.getElementById('display_site_title');
        const display_site_about = document.getElementById('display_site_about');

        const xhr = new XMLHttpRequest();
        xhr.open("POST", 'ajax/setting_curd.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

        xhr.onload = function() {
            if (xhr.status === 200) {
                try {
                    const response = JSON.parse(this.responseText);
                    if (response.success !== undefined && response.success === false) {
                        console.error("Error:", response.message);
                    } else {
                        display_site_title.innerText = response.site_title;
                        display_site_about.innerText = response.site_about;

                        // Populate modal input fields
                        document.getElementById('site_title_inp').value = response.site_title;
                        document.getElementById('site_about_inp').value = response.site_about;
                    }
                } catch (e) {
                    console.error("Error parsing response:", e);
                }
            } else {
                console.error("Request failed with status:", xhr.status);
            }
        };

        xhr.onerror = function() {
            console.error("Request failed due to a network error.");
        };

        xhr.send('get_general');
    }

    // Event listener for form submission
    document.getElementById('general_s_form').addEventListener('submit', function(e) {
        e.preventDefault();
        upd_general(); // Call your update function
    });



    // Load initial data on page load
    window.onload = function() {
        get_general();
    };


    // Handle form submission
    general_s_form.addEventListener('submit', function(e) {
        e.preventDefault();
        upd_general();
    });


    // Update general settings
    // Update general settings
        function upd_general() {
            const site_title_val = document.getElementById('site_title_inp').value;
            const site_about_val = document.getElementById('site_about_inp').value;

            let xhr = new XMLHttpRequest();
            xhr.open("POST", 'ajax/setting_curd.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onload = function() {
                if (xhr.status === 200) {
                    const myModal = document.getElementById('general-s');
                    const modal = bootstrap.Modal.getInstance(myModal);
                    modal.hide();

                    if (xhr.responseText == 1) {
                        alert('success', 'Changes saved successfully!');
                        get_general(); // Reload the settings
                    } else {
                        alert('error', 'No changes were made.');
                    }
                } else {
                    console.error('Error:', xhr.statusText);
                }
            };
            xhr.send(`site_title=${encodeURIComponent(site_title_val)}&site_about=${encodeURIComponent(site_about_val)}&action=upd_general`);
        }



// Update shutdown settings
      function upd_shutdown() {
        const shutdown_toggle = document.getElementById('shutdown-toggle');
        const shutdown_val = shutdown_toggle.checked ? 1 : 0;

        let xhr = new XMLHttpRequest();
        xhr.open("POST", 'ajax/setting_curd.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onload = function() {
            if (xhr.status === 200) {
                if (xhr.responseText == 1) {
                    alert('success', shutdown_toggle.checked ? 'Shutdown mode is now ON!' : 'Shutdown mode is now OFF!');
                } else {
                    alert('error', 'Failed to update shutdown settings.');
                }
                get_general();  // Reload the settings
            } else {
                console.error('Error:', xhr.statusText);
            }
        };
        xhr.send(`action=upd_shutdown&upd_shutdown=${shutdown_val}`);
    }

    // Fetch contact settings
    function get_contacts() {
        const contacts_p_id = ['address', 'gmap', 'pn1', 'pn2', 'email', 'tw', 'fb', 'lnk', 'insta'];
        const iframe = document.getElementById('iframe');
        let xhr = new XMLHttpRequest();
        xhr.open("POST", 'ajax/setting_curd.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onload = function() {
            if (xhr.status === 200) {
                contactus_data = JSON.parse(xhr.responseText);
                contactus_data = Object.values(contactus_data);
                for (let i = 0; i < contacts_p_id.length; i++) {
                    document.getElementById(contacts_p_id[i]).innerText = contactus_data[i + 1];
                }
                iframe.src = contactus_data[10];
                contacts_inp(contactus_data);
            } else {
                console.error('Error:', xhr.statusText);
            }
        };
        xhr.send('action=get_contacts');
    }

    // Fill contact inputs
    function contacts_inp(contactus_data) {
        const contacts_inp_id = ['address_inp', 'gmap_inp', 'pn1_inp', 'pn2_inp', 'email_inp', 'tw_inp', 'fb_inp', 'lnk_inp', 'insta_inp', 'iframe_inp'];
        for (let i = 0; i < contacts_inp_id.length; i++) {
            document.getElementById(contacts_inp_id[i]).value = contactus_data[i + 1];
        }
    }

    // Handle contact form submission
    contacts_s_form.addEventListener('submit', function(e) {
        e.preventDefault();
        upd_contacts();
    });

    // Update contact settings
    function upd_contacts() {
        const index = ['address', 'gmap', 'pn1', 'pn2', 'email', 'tw', 'fb', 'lnk', 'insta', 'iframe'];
        const contacts_inp_id = ['address_inp', 'gmap_inp', 'pn1_inp', 'pn2_inp', 'email_inp', 'tw_inp', 'fb_inp', 'lnk_inp', 'insta_inp', 'iframe_inp'];

        let data_str = index.map((key, i) => {
            return `${key}=${encodeURIComponent(document.getElementById(contacts_inp_id[i]).value)}`;
        }).join('&') + '&action=upd_contacts';

        let xhr = new XMLHttpRequest();
        xhr.open("POST", 'ajax/setting_curd.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onload = function() {
            if (xhr.status === 200) {
                const myModal = document.getElementById('contact-s');
                const modal = bootstrap.Modal.getInstance(myModal);
                modal.hide();

                if (xhr.responseText == 1) {
                    alert('success', 'Changes saved successfully!');
                } else {
                    alert('error', 'An error occurred while saving changes');
                    get_contacts(); // Refresh data to ensure the form shows the latest info
                }
            } else {
                console.error('Error:', xhr.statusText);
            }
        };
        xhr.send(data_str);
    }

    team_s_form.addEventListener('submit', function(e) {
        e.preventDefault();
        add_member();
    });

    function add_member() {
        const data = new FormData();
        data.append('name', member_name_inp.value);
        data.append('picture', member_picture_inp.files[0]);
        data.append('add_member', '');

        const xhr = new XMLHttpRequest();
        xhr.open("POST", 'ajax/setting_curd.php', true);

        xhr.onload = function() {
            const myModal = document.getElementById('team-s');
            const modal = bootstrap.Modal.getInstance(myModal);
            modal.hide();

            if (xhr.status === 200) {
                switch (xhr.responseText) {
                    case 'inv_img':
                        alert('error', 'Only JPG, PNG, and WEBP images are allowed.');
                        break;
                    case 'inv_size':
                        alert('error', 'Image should be less than 2 MB.');
                        break;
                    case 'upd_failed':
                        alert('error', 'Image upload failed. Server may be down.');
                        break;
                    default:
                        alert('success', 'New member added!');
                        member_name_inp.value = '';
                        member_picture_inp.value = '';
                        get_member();
                }
            } else {
                console.error('Error:', xhr.statusText);
            }
        };
        xhr.send(data);
    }

    // Fetch team members
    function get_member() {
        const xhr = new XMLHttpRequest();
        xhr.open("POST", 'ajax/setting_curd.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onload = function() {
            if (xhr.status === 200) {
                document.getElementById('team-data').innerHTML = xhr.responseText;
            } else {
                console.error('Error:', xhr.statusText);
            }
        };
        xhr.send('get_member');
    }

    // delete
    // Remove team member
    function rem_member(val) {
        const xhr = new XMLHttpRequest();
        xhr.open("POST", 'ajax/setting_curd.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onload = function() {
            if (xhr.status === 200) {
                if (xhr.responseText == 1) {
                    alert('success', 'Member removed.');
                    get_member();
                } else {
                    alert('error', 'Server down!');
                }
            } else {
                console.error('Error:', xhr.statusText);
            }
        };
        xhr.send('rem_member=' + val);
    }

    // Load initial data
    document.addEventListener('DOMContentLoaded', function() {
        get_general();
        get_contacts();
        get_member();
    });

    window.onload = function() {
        get_general();
        get_contacts();
        get_member();
        document.getElementById('shutdown-toggle').addEventListener('change', upd_shutdown);
    }
</script>