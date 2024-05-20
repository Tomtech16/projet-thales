<?php session_start(); ?>
<?php if (!isset($_SESSION['LOGGED_USER'])) { header('Location:index.php'); } ?>
<!DOCTYPE HTML>
<html>
	<head>
		<meta charset="utf-8" />
		<link rel="stylesheet" href="./style.css" />
		<title>Thales - Checklist</title>
	</head>
	<body>
        <?php require_once(__DIR__ . '/header.php'); ?>
        <?php 
            if (!isset($_SESSION['LOGGED_USER'])) {
                header('Location:index.php');
            }
        ?>
        <!-- if the user is logged -->  
        <?php if (isset($_SESSION['LOGGED_USER'])) : ?>
            <?php 
                // give the right page according to profiles : operator, admin, superadmin
                if ($_SESSION['LOGGED_USER']['profile'] === 'admin' || $_SESSION['LOGGED_USER']['profile'] === 'superadmin') {
                    require_once(__DIR__ . '/users_gestion.php');
                    require_once(__DIR__ . '/users_print.php');
                } else {
                    require_once(__DIR__ . '/logout.php');
                }
            ?>
        <?php endif; ?>
        <?php require_once(__DIR__ . '/footer.php'); ?>
	</body>
</html>