<?php
    session_start();
    if (!isset($_SESSION['LOGGED_USER'])) { header('Location:index.php'); }  
    require_once(__DIR__ . '/config/mysql.php');

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

