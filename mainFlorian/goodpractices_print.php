<?php

    session_start();
    require_once(__DIR__ . '/database_connect.php');
    // require_once(__DIR__ . '/variables.php');
    require_once(__DIR__ . '/functions.php');
    require_once(__DIR__ . '/sql_functions.php');
    
    $goodPractices = GoodPracticesSelect($_SESSION['GOODPRACTICES_SELECTION']);

    print_r($goodPractices);

?>