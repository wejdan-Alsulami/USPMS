<?php
// Database configuration
$servername = "localhost:3307";
$username = "root";
$password = ""; // Replace with your database password
$dbname = "UniversityPayroll";

// Create a connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
