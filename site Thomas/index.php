<?php
session_start(); // Start session

// Include database connection file
include('connection.php');

// Check for form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
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
        // Store user information in session variables
        $_SESSION['username'] = $user['login'];
        $_SESSION['nom'] = $user['nom'];
        $_SESSION['prenom'] = $user['prenom'];
        $_SESSION['role'] = $user['role'];
        // Redirect to welcome page
        header('Location: welcome.php');
        exit();
    } else {
        // Invalid credentials, display error message
        $error_message = "Nom d'utilisateur ou mot de passe incorrect.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Projet Checklist</title>
  <link rel="stylesheet" href="styles.css">
</head>
<body>

  <header>
    <h1>Projet Checklist</h1>
  </header>
  <div class="container">
    <div class="login-box">
      <h2>Connexion</h2>
      <?php if(isset($error_message)) echo "<p class='error'>$error_message</p>"; ?>
      <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" onsubmit="return validateForm()">
        <div class="form-group">
          <label for="username">Nom d'utilisateur :</label>
          <input type="text" id="username" name="username" required>
        </div>
        <div class="form-group">
          <label for="password">Mot de passe :</label>
          <input type="password" id="password" name="password" required>
        </div>
        <button type="submit">Se connecter</button>
      </form>
    </div>
  </div>
  <script>
    function validateForm() {
      var username = document.getElementById("username").value;
      var password = document.getElementById("password").value;

      if (username === "" || password === "") {
        alert("Veuillez remplir tous les champs !");
        return false;
      }
      return true;
    }
  </script>
  <footer>
    <p>&copy; 2024 Projet Checkilist @Innovations Space</p>
  </footer>

</body>
</html>
