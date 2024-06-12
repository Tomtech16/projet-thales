<?php
    session_start();
    require_once(__DIR__ . '/../functions.php');
    CheckRightsAndConnectionAttempt();
    
    // MySQL server configuration
    $mysqlServer = "";
    $mysqlUser = "";
    $mysqlPassword = "";
    $mysqlBase = "";
?>