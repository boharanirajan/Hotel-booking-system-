<?php
require('inc/essentials.php');
session_start(); // Start the session to access the session data
session_destroy(); // Destroy the session

// Redirect to index.php
 redirect('index.php');// Redirect to index.php
?>
