<?php 

    session_start();  

    require_once(__DIR__ . '/database_connect.php');
    require_once(__DIR__ . '/functions.php');
    require_once(__DIR__ . '/sql_functions.php');

    $postData = $_POST;
    $programNames = explode(', ', Sanitize($postData['duplicateProgram']));
    $goodpracticeId = Sanitize($_POST['goodpracticeId']);
    $whereIs = array('goodpractice_id' => $goodpracticeId);
    $goodpractice = GoodPracticesSelect($whereIs)[0];
    $phaseName = $goodpractice['phase_name'];
    $item = $goodpractice['item'];
    $keywords = array($goodpractice['keywords']);

    InsertGoodpractice($programNames, $phaseName, $item, $keywords);

    header('Location:index.php');
?>