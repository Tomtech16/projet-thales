<?php
session_start();
// require_once(__DIR__ . '/config/mysql.php');
// require_once(__DIR__ . '/databaseconnect.php');
// require_once(__DIR__ . '/variables.php');
// require_once(__DIR__ . '/functions.php');
?>

<!DOCTYPE HTML>
<html>
	<head>
		<meta charset="utf-8" />
		<link rel="stylesheet" href="../Utils/style.css" />
		<title>THALES - CHECKLIST</title>
	</head>
	<body>
        <?php require_once(__DIR__ . '/header.php'); ?>
		<?php require_once(__DIR__ . '/menu.php'); ?>
        <?php require_once(__DIR__ . '/login.php'); ?>

        <?php
            // if the user is logged
            if (isset($_SESSION['LOGGED_USER'])) {
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
            }
        ?>

        <?php require_once(__DIR__ . '/footer.php'); ?>
	</body>
</html>