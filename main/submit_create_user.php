<?php
    session_start();
    $path = $_SERVER['PHP_SELF'];
    $file = basename($path);
    require_once(__DIR__ . '/functions.php');
    if (!isset($_SESSION['LOGGED_USER']) || $_SERVER['REQUEST_METHOD'] !== 'POST' || ($_SESSION['LOGGED_USER']['profile'] !== 'admin' && $_SESSION['LOGGED_USER']['profile'] !== 'superadmin')) { Logger(Sanitize($_SESSION['LOGGED_USER']['username']), Sanitize($_SESSION['LOGGED_USER']['profile']), 2, 'Unauthorized access attempt to '.$file); header('Location:logout.php'); exit(); }

    require_once(__DIR__ . '/config/database_connect.php');
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
        if (!empty($username) && !empty($firstname) && !empty($lastname)) {
            if ($passwordValidationResult === NULL) {
                if (str_contains(strtolower($username), 'operator') || str_contains(strtolower($username), 'admin') || str_contains(strtolower($username), 'unauthenticated')) {
                    $_SESSION['CREATE_USER_MESSAGE'] = 'Erreur !\n\nNom d utilisateur indisponible : '.$username.'.';
                    Logger(Sanitize($_SESSION['LOGGED_USER']['username']), Sanitize($_SESSION['LOGGED_USER']['profile']), 2, 'Failed to create an user with username : '.$username.', username invalid');
                } elseif (UserAppend($username, $firstname, $lastname, $password, $profile)) {
                    $_SESSION['CREATE_USER_MESSAGE'] = 'Utilisateur créé avec succès !\n\nVous pouvez en ajouter un nouveau.';
                    Logger(Sanitize($_SESSION['LOGGED_USER']['username']), Sanitize($_SESSION['LOGGED_USER']['profile']), 1, 'Successfully create an user with username : '.$username);
                } else {
                    $_SESSION['CREATE_USER_MESSAGE'] = 'Erreur !\n\nNom d utilisateur indisponible : '.$username.'.';
                    Logger(Sanitize($_SESSION['LOGGED_USER']['username']), Sanitize($_SESSION['LOGGED_USER']['profile']), 1, 'Failed to create an user with username : '.$username.', username unavailable');
                }
            } else {
                $_SESSION['CREATE_USER_MESSAGE'] = $passwordValidationResult;
                Logger(Sanitize($_SESSION['LOGGED_USER']['username']), Sanitize($_SESSION['LOGGED_USER']['profile']), 1, 'Failed to create an user with username : '.$username.', password issue');
            }
        } else {
            $_SESSION['CREATE_USER_MESSAGE'] = 'Erreur !\n\nAucune information ne doit être laissée vide.';
            Logger(Sanitize($_SESSION['LOGGED_USER']['username']), Sanitize($_SESSION['LOGGED_USER']['profile']), 0, 'Failed to create an user with username : '.$username.', empty information');
        } 
    }

    header('Location:create_user.php');
?>