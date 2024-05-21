<?php
    session_start();
    if (!isset($_SESSION['LOGGED_USER'])) { header('Location:index.php'); }
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

        if ($password === $password2) {
            if (PasswordIsValid($username, $password, $parameters = PasswordSelect())) {
                if (UserAppend($username, $firstname, $lastname, $password)) {
                    $_SESSION['CREATE_USER_MESSAGE'] = 'Utilisateur créé avec succès. Vous pouvez en ajouter un nouveau.';
                } else {
                    $_SESSION['CREATE_USER_MESSAGE'] = 'Nom d\'utilisateur indisponible.';
                } 
            } else {
                $_SESSION['CREATE_USER_MESSAGE'] = 'Le mot de passe ne respecte pas les règles de configuration.';
            }
        } else {
            $_SESSION['CREATE_USER_MESSAGE'] = 'Les deux mots de passe sont différents.';
        }
    }

    header('Location:create_user.php');
?>