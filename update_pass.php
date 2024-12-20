<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Update</title>
</head>
<body>
    <?php
        require('Admin/inc/essentials.php');
        require('Admin/inc/db_config.php');

        if (isset($_GET['email']) && isset($_GET['token'])) {
            date_default_timezone_set("Asia/Kathmandu");

            $data = filteration($_GET);
            $t_date = date('Y-m-d');

            // Using prepared statements to avoid SQL injection
            $query = "SELECT * FROM `users` WHERE `email` = ? AND `token` = ? AND `t_expire` = ?";
            $stmt = $con->prepare($query);
            $stmt->bind_param('sss', $data['email'], $data['token'], $t_date);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result) {
                if (mysqli_num_rows($result) == 1) {
                    echo "
                    <form action='update_password.php' method='POST'>
                        <label for='new_pass'>Enter New Password:</label>
                        <input type='password' name='new_pass' required>
                        
                        <label for='confirm_pass'>Confirm New Password:</label>
                        <input type='password' name='confirm_pass' required>
                        
                        <input type='hidden' name='email' value='" . htmlspecialchars($data['email']) . "'>
                        <input type='hidden' name='token' value='" . htmlspecialchars($data['token']) . "'>
                        
                        <button type='submit'>Update Password</button>
                    </form>
                    ";
                } else {
                    echo "<script>
                        alert('Invalid or expired link!');
                        window.location.href = 'index.php';
                    </script>";
                }
            } else {
                echo "<script>
                    alert('Server down, please try again!');
                    window.location.href = 'index.php';
                </script>";
            }
        }
    ?>
</body>
</html>
