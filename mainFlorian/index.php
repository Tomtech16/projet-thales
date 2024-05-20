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
            <?php 
                // check for password update required
                if ($_SESSION['PASSWORD_UPDATE_REQUIRED'] === TRUE) {
                    require_once(__DIR__ . '/password_update.php');
                } else {
                    // give the right page according to profiles : operator, admin, superadmin
                    if ($_SESSION['LOGGED_USER']['profile'] === 'operator' || $_SESSION['LOGGED_USER']['profile'] === 'admin' || $_SESSION['LOGGED_USER']['profile'] === 'superadmin')
                    { 
                        require_once(__DIR__ . '/goodpractices_selection.php');
                        require_once(__DIR__ . '/goodpractices_print.php');
                    } else {
                        require_once(__DIR__ . '/logout.php');
                    }
                }
            ?>
        <?php endif; ?>

        <?php require_once(__DIR__ . '/footer.php'); ?>
	</body>
</html>