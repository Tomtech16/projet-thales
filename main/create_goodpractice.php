<?php session_start(); ?>
<?php if (!isset($_SESSION['LOGGED_USER'])) { header('Location:index..php'); } ?>
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
            if (isset($_SESSION['LOGGED_USER'])) {
                require_once(__DIR__ . '/goodpractices_create_selection.php');
            } else {
                header('Location:logout.php');
                exit();
            }
        ?>

        <?php require_once(__DIR__ . '/footer.php'); ?>
	</body>
</html>