<?php
// Include database connection file
include('connection.php');

// Retrieve username and password from form submission
$username = $_POST['username'];
$password = $_POST['password'];

// Query to check if user exists
$query = "SELECT * FROM user WHERE login='$username' AND mdp='$password'";
$result = mysqli_query($conn, $query);

// Check if query returns any rows
if (mysqli_num_rows($result) == 1) {
    // User exists, fetch user information
    $user = mysqli_fetch_assoc($result);
    // Start session
    session_start();
    // Store user information in session variables
    $_SESSION['username'] = $user['login'];
    $_SESSION['nom'] = $user['nom'];
    $_SESSION['prenom'] = $user['prenom'];
    $_SESSION['role'] = $user['role'];
    // Redirect to welcome page
    header('Location: welcome.php');
} else {
    // Invalid credentials, redirect back to login page with error message
    header('Location: index.php?error=1');
}
?>
