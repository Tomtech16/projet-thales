<?php
    session_start();
    require_once(__DIR__ . '/database_connect.php');
    require_once(__DIR__ . '/functions.php');
    require_once(__DIR__ . '/sql_functions.php');

    $postData = $_POST;

    if (isset($postData['username']) && isset($postData['password'])) {
        $username = Sanitize($postData['username']);
        $password = Sanitize($postData['password']);

        $users = UsersSelect();

        foreach ($users as $user) {
            if ($username === $user['username']) {
                $hash = $user['password'];
                if (password_verify($password, $hash)) {
                    $userIsBlocked = UserIsBlocked($user['attempts']);
                    if (!$userIsBlocked) {
                        $_SESSION['LOGGED_USER'] = [
                        'username' => $user['username'], 
                        'firstname' => $user['firstname'], 
                        'lastname' => $user['lastname'],
                        'profile' => $user['profile'],
                        ];
                        UserAttempts($user['userkey'], 'reset');
                        header('Location:index.php');
                        break;
                    } else {
                        $_SESSION['LOGIN_MESSAGE'] = 'Utilisateur bloqué.';
                        break;
                    }
                    
                } else {
                    if ($user['profile'] !== 'superadmin') {
                        UserAttempts($user['userkey'],'increment');
                        break;
                    }
                }
            }
        }
        if (!isset($_SESSION['LOGGED_USER']) && !$userIsBlocked) {
            $_SESSION['LOGIN_MESSAGE'] = "Echec de l'authentification.";
        }
        header('Location:login.php');
    }
    
?>