<!-- db connection -->
 <?php
 require('inc/db_config.php');

 require('inc/essentials.php');

  session_start();


//  if (!isset($_SESSION['adminlogin']) || $_SESSION['adminlogin']!== true) {
//  //     // User is not logged in, redirect to the login page
//    redirect('dashboard.php');
// // //    // echo "<script>window.location.href='dashboard.php';</script>";
//   }
 ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login panel</title>
    <?php
    require('inc/link.php');
    ?>
    <style>
        div.login-form{
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 400px;
        
        }
    </style>
</head>
<body class="bg-light">
  <!-- admin form -->
 <div class="login-form text-center rounded bg-white shadow overflow-hidden">
    <form method="POST" >
        <h4 class="bg-dark text-white py-3">ADMIN LOGIN PANEL</h4>
        <div class="p-4">
            <div class="mb-3">  
            <input type="text" name="admin_name" class="form-control shadow-none text-center" required placeholder="Admin Name">
            </div>
            <div class="mb-4">
            <input type="password" name="admin_pass" class="form-control shadow-none text-center"  required="Password">
            </div>
             <button name="login" class=" btn text-white custom-bg shadow-none" type="submit"> Login</button>
        </div>
    </form>
 </div>
  <!-- admin submit php -->
 <?php
if(isset($_POST['login']))
{  
   // Filter the POST data
   $frm_data = filteration($_POST);
    
   // Prepare the SQL query with placeholders
   $query = "SELECT * FROM `admin` WHERE `admin_name` = ? AND `admin_pass` = ?";
   
   // Values to bind to the placeholders
   $values = [$frm_data['admin_name'], $frm_data['admin_pass']];
   
   // Call the select function to execute the query
   $res = select($query, $values, "ss");
    
   if ( $res->num_rows ==1) 
   {
       // Fetch the result row
       $row = mysqli_fetch_assoc($res);
   
       //session_start();
   
       // Set session variables
       $_SESSION['adminlogin'] = true;
       $_SESSION['adminid'] = $row['sr_no']; // Use $row, not $res
   
       // Redirect to the dashboard page
       // header('Location: dashboard.php');
       redirect('dashboard.php');
    
       // exit(); // Ensure no further code is executed after redirection
   }
   
   else 
{
    alert('error','Login failed -Invalid Credentials!');
}

}



?>



<?php
require('inc/script.php');
?>
</body>
</html>