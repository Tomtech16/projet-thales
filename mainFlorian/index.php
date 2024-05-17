<?php session_start(); ?>

<!DOCTYPE HTML>
<html>
	<head>
		<meta charset="utf-8" />
		<link rel="stylesheet" href="./style.css" />
		<title>Thales - Checklist</title>
	</head>
	<body>
        <?php require_once(__DIR__ . '/header.php'); ?>

        <?php if (!isset($_SESSION['LOGGED_USER'])) : ?>
            <section class="welcome">
                <h2>Bienvenue sur le projet Thales Checklist.</h2>
                <ul>
                    <p>Connectez-vous pour : </p>
                    <li>gérer les bonnes pratiques</li>
                    <li>créer des checklists</li>
                </ul>
                <form action="login.php" method="POST">
                    <button type="submit" name="submit" class="btn" >Se connecter</button>
                </form>
            </section>
        <?php endif; ?>


        <!-- if the user is logged -->  
        <?php if (isset($_SESSION['LOGGED_USER'])) : ?>
            <section>
                <p>Connecté en tant que <?php echo $_SESSION['LOGGED_USER']['username']; ?>.</p>
                <p>Bonjour <?php echo $_SESSION['LOGGED_USER']['firstname']." ".$_SESSION['LOGGED_USER']['lastname']; ?>.</p>
            </section>
            <?php 
                // check for password update required
                if ($_SESSION['PASSWORD_UPDATE_REQUIRED'] === TRUE) {
                    require_once(__DIR__ . '/password_update.php');
                } else {
                    // give the right page according to profiles : operator, admin, superadmin
                    switch ($_SESSION['LOGGED_USER']['profile'])
                    { 
                        case 'operator':
                            require_once(__DIR__ . '/operator.php');
                        break;
                        
                        case 'admin':
                            require_once(__DIR__ . '/admin.php');
                        break;
                        
                        case 'superadmin':
                            require_once(__DIR__ . '/superadmin.php');
                        break;

                        default:
                            require_once(__DIR__ . '/logout.php');
                    }
                }
            ?>
        <?php endif; ?>

        <?php require_once(__DIR__ . '/footer.php'); ?>
	</body>
</html>