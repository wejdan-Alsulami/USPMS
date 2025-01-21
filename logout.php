<?php
session_start();
session_unset();   // Remove all session variables
session_destroy(); // Destroy the session
header("Location: index.php#signIn"); // Redirect the user to the login page or homepage
exit();
?>
