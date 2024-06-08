<?php
    session_start();
    $path = $_SERVER['PHP_SELF'];
    $file = basename($path);
    require_once(__DIR__ . '/../functions.php');
    if (!isset($_SESSION['LOGGED_USER']) && !isset($_SESSION['LOGIN_TENTATIVE'])) { Logger(NULL, NULL, 2, 'Unauthorized access attempt to '.$file); header('Location:logout.php'); exit(); }
    
    $mysqlServer = "";
    $mysqlUser = "";
    $mysqlPassword = "";
    $mysqlBase = "";
?>
