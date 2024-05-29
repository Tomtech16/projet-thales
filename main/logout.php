<?php
    session_start();
    require_once(__DIR__ . '/functions.php');
    if (isset($_SESSION['LOGGED_USER'])) { 
        $lm = 1;
        Logger(Sanitize($_SESSION['LOGGED_USER']['username']), Sanitize($_SESSION['LOGGED_USER']['profile']), 0, 'Successfully deconnected');
    }
    session_unset();
    session_destroy();

    session_start();

    if ($lm === 1) { $_SESSION['LOGIN_MESSAGE'] = 'Vous êtes déconnecté(e)'; }
    unset($lm);
    
    header('Location:index.php');
?>