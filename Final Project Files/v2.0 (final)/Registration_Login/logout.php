<?php
// Initialize the session
session_start();
 
// Unset all of the session variables
$_SESSION = array();
 
// Destroy the session.
session_destroy();
 
// Redirect to login page (or homepage)
// Adjust the path as needed
// echo "<script>alert('You have Logged Out');</script>";
header("location: ../Home_Page/index.php");
exit;
?>