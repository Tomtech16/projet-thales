<?php
// Database connection parameters
$host = "localhost";
$username = "root"; // Assuming the username is still the default 'root'
$password = ""; // Assuming there is no password set
$database = "bddchecklist"; // Your database name

// Create connection
$conn = mysqli_connect($host, $username, $password, $database);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>
