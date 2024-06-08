<?php
    session_start();
    $path = $_SERVER['PHP_SELF'];
    $file = basename($path);
    require_once(__DIR__ . '/functions.php');
    if (!isset($_SESSION['LOGGED_USER']) || $_SERVER['REQUEST_METHOD'] !== 'POST' || ($_SESSION['LOGGED_USER']['profile'] !== 'admin' && $_SESSION['LOGGED_USER']['profile'] !== 'superadmin')) { Logger(Sanitize($_SESSION['LOGGED_USER']['username']), Sanitize($_SESSION['LOGGED_USER']['profile']), 2, 'Unauthorized access attempt to '.$file); header('Location:logout.php'); exit(); }

    require_once(__DIR__ . '/config/database_connect.php');
    require_once(__DIR__ . '/sql_functions.php');

    $profile = Sanitize($_SESSION['LOGGED_USER']['profile']);
    $postData = $_POST;
    $userId = Sanitize($postData['userId']);

    if ($postData['submit'] === 'delete-user') {
        $username = UserWhatIsName($userId);
        UserDelete($userId, $profile);
        Logger(Sanitize($_SESSION['LOGGED_USER']['username']), Sanitize($_SESSION['LOGGED_USER']['profile']), 2, 'User with username : '.$username.' permanently deleted');
    } elseif ($postData['submit'] === 'reset-password') {
        $password = Sanitize($postData['password']);
        $password2 = Sanitize($postData['password2']);
        $username = UserWhatIsName($userId);
        $passwordValidationResult = PasswordIsValid($username, $password, $password2);
        if ($passwordValidationResult === NULL) {
            UserResetPassword($userId, $password, $profile);
            $_SESSION['RESET_USER_PASSWORD_MESSAGE'] = 'Mot de passe réinitialisé avec succès !';
            Logger(Sanitize($_SESSION['LOGGED_USER']['username']), Sanitize($_SESSION['LOGGED_USER']['profile']), 1, 'Successfully reset password for blocked user with username : '.$username.', account unlocked');
        } else {
            $_SESSION['RESET_USER_PASSWORD_MESSAGE'] = $passwordValidationResult;
            Logger(Sanitize($_SESSION['LOGGED_USER']['username']), Sanitize($_SESSION['LOGGED_USER']['profile']), 2, 'Failed to reset password for blocked user with username : '.$username.', new password issue');
        }
    }
    header('Location:admin.php');
?>