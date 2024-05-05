<?php

session_start();
// require_once(__DIR__ . '/config/mysql.php');
require_once(__DIR__ . '/database_connect.php');
// require_once(__DIR__ . '/variables.php');
 require_once(__DIR__ . '/functions.php');

/**
 * On ne traite pas les super globales provenant de l'utilisateur directement, 
 * ces données doivent être testées et vérifiées.
 */

$postData = $_POST;

// Validation du formulaire
if (isset($postData['username']) &&  isset($postData['password'])) {
    $username = htmlspecialchars($postData['username'], ENT_QUOTES);
    $password = htmlspecialchars($postData['password'], ENT_QUOTES);

    $users = UsersSelect();

    foreach ($users as $user) {
        if ($username === $user['username']) {
            $hash = $user['password'];
            if (password_verify($password, $hash) && !UserIsBlocked($user['attempts'])) {
                $_SESSION['LOGGED_USER'] = [
                    'username' => $user['username'], 
                    'firstname' => $user['firstname'], 
                    'lastname' => $user['lastname'],
                    'profile' => $user['profile']
                ];
                $passwordParameters = PasswordSelect();
                $_SESSION['PASSWORD_UPDATE_REQUIRED'] = ($passwordParameters['update']);
                UserAttempts($user['userkey'],'reset');
            } else {
                UserAttempts($user['userkey'],'increment');
            }
        }
    }

    if (!isset($_SESSION['LOGGED_USER'])) {
        $_SESSION['LOGIN_MESSAGE'] = "Echec de l'authentification.";
    }
}
