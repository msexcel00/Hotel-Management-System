<?php
session_start();
// Unset all session variables
$_SESSION = array();
// Destroy the session
session_destroy();

// CRITICAL FIX: Redirect to the home page instead of the login page
header("location: admin_login.php"); 
exit;
?>