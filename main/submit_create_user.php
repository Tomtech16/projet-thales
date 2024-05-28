<?php
    session_start();
    if (!isset($_SESSION['LOGGED_USER']) || ($_SESSION['LOGGED_USER']['profile'] !== 'admin' && $_SESSION['LOGGED_USER']['profile'] !== 'superadmin')) { header('Location: logout.php'); exit(); }

    require_once(__DIR__ . '/database_connect.php');
    require_once(__DIR__ . '/functions.php');
    require_once(__DIR__ . '/sql_functions.php');

    $postData = $_POST;

    if (isset($postData['username']) && isset($postData['firstname']) && isset($postData['lastname']) && isset($postData['password']) && isset($postData['password2'])) {
        $username = Sanitize($postData['username']);
        $firstname = Sanitize($postData['firstname']);
        $lastname = Sanitize($postData['lastname']);
        $password = Sanitize($postData['password']);
        $password2 = Sanitize($postData['password2']);
        
        if ($_SESSION['LOGGED_USER']['profile'] === 'superadmin' && isset($postData['profile'])) {
            $profile = Sanitize($postData['profile']);
        }

        $passwordValidationResult = PasswordIsValid($username, $password, $password2);
        if ($passwordValidationResult === NULL) {
            if (UserAppend($username, $firstname, $lastname, $password, $profile)) {
                $_SESSION['CREATE_USER_MESSAGE'] = 'Utilisateur créé avec succès !\n\nVous pouvez en ajouter un nouveau.';
            } else {
                $_SESSION['CREATE_USER_MESSAGE'] = 'Erreur !\n\nNom d\'utilisateur indisponible.';
            } 
        } else {
            $_SESSION['CREATE_USER_MESSAGE'] = $passwordValidationResult;
        }
    }

    header('Location:create_user.php');
?>