<?php 

    session_start();  

    require_once(__DIR__ . '/database_connect.php');
    // require_once(__DIR__ . '/variables.php');
    require_once(__DIR__ . '/functions.php');
    require_once(__DIR__ . '/sql_functions.php');

    $postData = $_POST;
    $programNames = explode(', ', Sanitize($postData['duplicateProgram']));
    $phaseName = Sanitize($postData['duplicatePhase']);
    $item = Sanitize($postData['item']);
    $keywords = explode(', ', Sanitize($postData['keywords']));
    
    InsertGoodpractice($programNames, $phaseName, $item, $keywords);

    header('Location:index.php');
?>