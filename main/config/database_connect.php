<?php
    session_start();
    $path = $_SERVER['PHP_SELF'];
    $file = basename($path);
    require_once(__DIR__ . '/../functions.php');
    if (!isset($_SESSION['LOGGED_USER']) && !isset($_SESSION['LOGIN_TENTATIVE'])) { Logger(NULL, NULL, 2, 'Unauthorized access attempt to '.$file); header('Location:logout.php'); exit(); }
  
    require_once(__DIR__ . '/mysql.php');

    // mysql_connect($mysqlServer,$mysqlUser,$mysqlPassword) or die("Pb connexion MySQL");
    // mysql_select_db($mysqlBase) or die("Pb selection base");

    try {
        $bd = new PDO ("mysql:host={$mysqlServer};dbname={$mysqlBase}",$mysqlUser,$mysqlPassword);
        $bd->exec ('SET NAMES utf8');
    }
    catch (Exception $e) {
        die ("Erreur: Connexion à la base impossible");
    }
?>
