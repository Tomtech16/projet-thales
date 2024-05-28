<?php
    session_start();
    if (!isset($_SESSION['LOGGED_USER']) || ($_SESSION['LOGGED_USER']['profile'] !== 'admin' && $_SESSION['LOGGED_USER']['profile'] !== 'superadmin')) { header('Location: logout.php'); exit(); }
    
    require_once(__DIR__ . '/database_connect.php');
    require_once(__DIR__ . '/functions.php');
    require_once(__DIR__ . '/sql_functions.php');

    $profile = Sanitize($_SESSION['LOGGED_USER']['profile']);
    $postData = $_POST;
    $userId = Sanitize($postData['userId']);

    if ($postData['submit'] === 'delete-user') {
        UserDelete($userId, $profile);
    } elseif ($postData['submit'] === 'reset-password') {
        $password = Sanitize($postData['password']);
        $password2 = Sanitize($postData['password2']);
        $username = UserWhatIsName($userId);
        $passwordValidationResult = PasswordIsValid($username, $password, $password2);
        if ($passwordValidationResult === NULL) {
            UserResetPassword($userId, $password, $profile);
            $_SESSION['RESET_USER_PASSWORD_MESSAGE'] = 'Mot de passe réinitialisé avec succès !';
        } else {
            $_SESSION['RESET_USER_PASSWORD_MESSAGE'] = $passwordValidationResult;
        }
    }
    header('Location:admin.php');
?>