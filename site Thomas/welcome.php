<?php
session_start();

// Check if user is logged in, otherwise redirect to login page
if (!isset($_SESSION['username'])) {
    header('Location: index.php');
    exit();
}

// Access user information from session variables
$username = $_SESSION['username'];
$nom = $_SESSION['nom'];
$prenom = $_SESSION['prenom'];
$role = $_SESSION['role'];
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Welcome</title>
</head>
<body>
  <h1>Welcome, <?php echo $prenom . ' ' . $nom; ?>!</h1>
  <p>Your username: <?php echo $username; ?></p>
  <p>Your role: <?php echo $role; ?></p>
  <a href="logout.php">Logout</a> <!-- Add a logout link -->
</body>
</html>
