<?php

session_start();
// require_once(__DIR__ . '/config/mysql.php');
require_once(__DIR__ . '/database_connect.php');
// require_once(__DIR__ . '/variables.php');
require_once(__DIR__ . '/functions.php');
require_once(__DIR__ . '/sql_functions.php');

/**
 * On ne traite pas les super globales provenant de l'utilisateur directement, 
 * ces données doivent être testées et vérifiées.
 */

$postData = $_POST;

// Validation du formulaire
if (isset($postData['username']) && isset($postData['password'])) {
    $username = Sanitize($postData['username']);
    $password = Sanitize($postData['password']);

    $users = UsersSelect();
    $userIsBlocked = FALSE;

    foreach ($users as $user) {
        if ($username === $user['username']) {
            $hash = $user['password'];
            if (password_verify($password, $hash)) {
                if (!UserIsBlocked($user['attempts'])) {
                    $_SESSION['LOGGED_USER'] = [
                    'username' => $user['username'], 
                    'firstname' => $user['firstname'], 
                    'lastname' => $user['lastname'],
                    'profile' => $user['profile'],
                    'PASSWORD_UPDATE_REQUIRED' => $user['passwordupdaterequired']
                    ];
                    UserAttempts($user['userkey'],'reset');
                    break;
                } else {
                    $userIsBlocked = TRUE;
                    $_SESSION['LOGIN_MESSAGE'] = 'Utilisateur bloqué.';
                    break;
                }
                
            } else {
                UserAttempts($user['userkey'],'increment');
                break;
            }
        }
    }

    if (!isset($_SESSION['LOGGED_USER']) && !$userIsBlocked) {
        $_SESSION['LOGIN_MESSAGE'] = "Echec de l'authentification.";
    }

    header('Location:index.php');
}
