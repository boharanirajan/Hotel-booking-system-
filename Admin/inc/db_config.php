<?php

$hname = "localhost";
$uname = "root";
$pass = "";
$db = "hotel_db";

// Create connection
$con = mysqli_connect($hname, $uname, $pass, $db);

// Check connection
if (!$con) {
    die("Cannot connect to database: " . mysqli_connect_error());
}

// After establishing the connection, use `$con` instead of `$conn` in your queries

 
 
 function filteration($data)
 {
     foreach ($data as $key => $value) {
         if (is_array($value)) {
             // Recursively sanitize arrays
             $data[$key] = filteration($value);
         } else {
             $value = trim($value);              // Remove extra spaces
             $value = stripslashes($value);      // Remove backslashes
             $value = htmlspecialchars($value);  // Convert special characters to HTML entities
             $value = strip_tags($value);        // Remove HTML and PHP tags
             $data[$key] = $value;
         }
     }
     return $data;
 }
 
// SELECT ALL function to fetch data
function selectAll($table)
{
$con=$GLOBALS['con'];
$res=mysqli_query($con,"SELECT * FROM $table");
return $res;
}

// SELECT function to fetch data
function select($sql, $values, $datatypes) 
{
    // Access the global database connection
    $con = $GLOBALS['con'];

    // Prepare the SQL statement
    $stmt = mysqli_prepare($con, $sql);
    if ($stmt) 
    {
        // Bind the parameters (values) to the statement
        mysqli_stmt_bind_param($stmt, $datatypes, ...$values); 
        
        // Execute the prepared statement
        if (mysqli_stmt_execute($stmt)) 
        {
            // Fetch the result of the query
            $res = mysqli_stmt_get_result($stmt);
            
            // Close the prepared statement
            mysqli_stmt_close($stmt);
            
            // Return the result of the query
            return $res;
        } 
        else 
        {
            // Close the prepared statement in case of failure
            mysqli_stmt_close($stmt);
            
            // Log error and terminate
            error_log("Query cannot be executed - select: " . mysqli_error($con));
            die("Query cannot be executed");
        }
    } 
    else 
    {
        // Log error and terminate if statement preparation fails
        error_log("Query cannot be prepared - select: " . mysqli_error($con));
        die("Query cannot be prepared - select");
    }
}

// UPDATE function to update data
function update($sql, $values, $datatypes) 
{
    // Access the global database connection
    $con = $GLOBALS['con'];

    // Prepare the SQL statement
    $stmt = mysqli_prepare($con, $sql);
    if ($stmt) 
    {
        // Bind the parameters (values) to the statement
        mysqli_stmt_bind_param($stmt, $datatypes, ...$values); 
        
        // Execute the prepared statement
        if (mysqli_stmt_execute($stmt)) 
        {
            // Get the number of affected rows
            $res = mysqli_stmt_affected_rows($stmt);
            
            // Close the prepared statement
            mysqli_stmt_close($stmt);
            
            // Return the result of the query
            return $res;
        } 
        else 
        {
            // Close the prepared statement in case of failure
            mysqli_stmt_close($stmt);
            
            // Log error and terminate
            error_log("Query cannot be executed - update: " . mysqli_error($con));
            die("Query cannot be executed");
        }
    } 
    else 
    {
        // Log error and terminate if statement preparation fails
        error_log("Query cannot be prepared - update: " . mysqli_error($con));
        die("Query cannot be prepared - update");
    }
}

// INSERT function to update data
function insert($sql, $values, $datatypes) 
{
    // Access the global database connection
    $con = $GLOBALS['con'];

    // Prepare the SQL statement
    $stmt = mysqli_prepare($con, $sql);
    if ($stmt) 
    {
        // Bind the parameters (values) to the statement
        mysqli_stmt_bind_param($stmt, $datatypes, ...$values); 
        
        // Execute the prepared statement
        if (mysqli_stmt_execute($stmt)) 
        {
            // Get the number of affected rows
            $res = mysqli_stmt_affected_rows($stmt);
            
            // Close the prepared statement
            mysqli_stmt_close($stmt);
            
            // Return the result of the query
            return $res;
        } 
        else 
        {
            // Close the prepared statement in case of failure
            mysqli_stmt_close($stmt);
            
            // Log error and terminate
            error_log("Query cannot be executed - inserted: " . mysqli_error($con));
            die("Query cannot be executed");
        }
    } 
    else 
    {
        // Log error and terminate if statement preparation fails
        error_log("Query cannot be prepared - inserted: " . mysqli_error($con));
        die("Query cannot be prepared - inserted");
    }
}

//  function to delete data
function delete($sql, $values, $datatypes) 
{
    // Access the global database connection
    $con = $GLOBALS['con'];

    // Prepare the SQL statement
    $stmt = mysqli_prepare($con, $sql);
    if ($stmt) 
    {
        // Bind the parameters (values) to the statement
        mysqli_stmt_bind_param($stmt, $datatypes, ...$values); 
        
        // Execute the prepared statement
        if (mysqli_stmt_execute($stmt)) 
        {
            // Get the number of affected rows
            $res = mysqli_stmt_affected_rows($stmt);
            
            // Close the prepared statement
            mysqli_stmt_close($stmt);
            
            // Return the result of the query
            return $res;
        } 
        else 
        {
            // Close the prepared statement in case of failure
            mysqli_stmt_close($stmt);
            
            // Log error and terminate
            error_log("Query cannot be executed - delete: " . mysqli_error($con));
            die("Query cannot be executed");
        }
    } 
    else 
    {
        // Log error and terminate if statement preparation fails
        error_log("Query cannot be prepared - delete: " . mysqli_error($con));
        die("Query cannot be prepared - delete");
    }
}

?>