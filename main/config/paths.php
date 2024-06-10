<?php
    session_start();   
    $path = $_SERVER['PHP_SELF'];
    $file = basename($path);
    require_once(__DIR__ . '/../functions.php');
    if (!isset($_SESSION['LOGGED_USER'])) { Logger(NULL, NULL, 2, 'Unauthorized access attempt to '.$file); header('Location:logout.php'); exit(); }

    $python3BinaryPath = "";

    if ($python3BinaryPath === "") {
        $python3BinaryPath = "/usr/share/bin/python3";
    }
?>