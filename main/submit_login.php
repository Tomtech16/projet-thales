<?php
    session_start();
    $_SESSION['LOGIN_TENTATIVE'] = TRUE;
    $path = $_SERVER['PHP_SELF'];
    $file = basename($path);
    require_once(__DIR__ . '/functions.php');
    if (isset($_SESSION['LOGGED_USER']) || $_SERVER['REQUEST_METHOD'] !== 'POST') { Logger(Sanitize($_SESSION['LOGGED_USER']['username']), Sanitize($_SESSION['LOGGED_USER']['profile']), 2, 'Unauthorized access attempt to '.$file); header('Location:logout.php'); exit(); }
  
    require_once(__DIR__ . '/config/database_connect.php');
    require_once(__DIR__ . '/sql_functions.php');

    $postData = $_POST;

    if (isset($postData['username']) && isset($postData['password'])) {
        $username = Sanitize($postData['username']);
        $password = Sanitize($postData['password']);

        $users = UsersSelect();
        $userIsBlocked = 0;
        $u = 0;

        foreach ($users as $user) {
            if ($username === $user['username']) {
                $u = 1;
                $hash = $user['password'];
                if (password_verify($password, $hash)) {
                    $userIsBlocked = UserIsBlocked(Sanitize($user['attempts']));
                    if (!$userIsBlocked) {
                        $_SESSION['LOGGED_USER'] = [
                        'username' => Sanitize($user['username']), 
                        'firstname' => Sanitize($user['firstname']), 
                        'lastname' => Sanitize($user['lastname']),
                        'profile' => Sanitize($user['profile']),
                        ];
                        UserAttempts($user['user_id'], 'reset');
                        Logger(Sanitize($_SESSION['LOGGED_USER']['username']), Sanitize($_SESSION['LOGGED_USER']['profile']), 0, 'Successful connection attempt');
                        header('Location:index.php');
                        break;
                    } else {
                        $_SESSION['LOGIN_MESSAGE'] = 'Utilisateur bloqué.';
                        Logger(NULL, NULL, 2, 'Connection attempt to a blocked account with username : '.$username);
                        break;
                    }
                } else {
                    if ($user['profile'] !== 'superadmin') {
                        UserAttempts($user['user_id'], 'increment');
                        if (Sanitize($user['attempts']+1 === 3)) {
                            Logger(NULL, NULL, 2, 'Failed connection attempt with username : '.$username.', account blocked');
                        } elseif (UserIsBlocked(Sanitize($user['attempts']+1))) {
                            Logger(NULL, NULL, 2, 'Failed connection attempt to a blocked account with username : '.$username);
                        } else {
                            Logger(NULL, NULL, 1, 'Failed connection attempt with username : '.$username);
                        }
                        break;
                    }
                }
            }
        }
        if (!isset($_SESSION['LOGGED_USER']) && !$userIsBlocked) {
            $_SESSION['LOGIN_MESSAGE'] = "Echec de l'authentification.";
        }

        if ($u === 0) {
            Logger(NULL, NULL, 2, 'Failed connection attempt with username : '.$username.', no account with this username');
        }
        header('Location:login.php');
    }
    
?>