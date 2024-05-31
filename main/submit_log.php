<?php 
    session_start();
    $path = $_SERVER['PHP_SELF'];
    $file = basename($path);
    require_once(__DIR__ . '/functions.php');
    if (!isset($_SESSION['LOGGED_USER']) || $_SERVER['REQUEST_METHOD'] !== 'POST' || ($_SESSION['LOGGED_USER']['profile'] !== 'admin' && $_SESSION['LOGGED_USER']['profile'] !== 'superadmin')) { Logger(Sanitize($_SESSION['LOGGED_USER']['username']), Sanitize($_SESSION['LOGGED_USER']['profile']), 2, 'Unauthorized access attempt to '.$file); header('Location:logout.php'); exit(); }

    $postData = $_POST;
    
    if ($postData['submit'] === 'reset') {
        unset($_SESSION['LOG_FILTERS']['LOG_EVENEMENT_TYPE']);
        unset($_SESSION['LOG_FILTERS']['LOG_PROFILES']);
        unset($_SESSION['LOG_FILTERS']['LOG_SEARCH']);
    } elseif ($postData['submit'] === 'submit') {
        $_SESSION['LOG_FILTERS']['LOG_EVENEMENT_TYPE'] = $postData['logEvenementTypeSelection'];
        $_SESSION['LOG_FILTERS']['LOG_PROFILES'] = $postData['logUserProfileSelection'];
        $_SESSION['LOG_FILTERS']['LOG_SEARCH'] = Sanitize($postData['logSearch']);
        $_SESSION['LOG_FILTERS']['LOG_DATE_DAY'] = Sanitize($postData['log-date-day']);
        $_SESSION['LOG_FILTERS']['LOG_DATE_MONTH'] = Sanitize($postData['log-date-month']);
        $_SESSION['LOG_FILTERS']['LOG_DATE_YEAR'] = Sanitize($postData['log-date-year']);
    }
    header('Location:log.php');
?>