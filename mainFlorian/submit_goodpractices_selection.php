<?php 
    session_start(); 

    require_once(__DIR__ . '/database_connect.php');
    require_once(__DIR__ . '/functions.php');
    require_once(__DIR__ . '/sql_functions.php');

    $postData = $_POST;
    if ($postData['submit'] === 'submit') {
        if (!empty($postData['programSearch'])) {
            $_SESSION['GOODPRACTICES_SELECTION']['program_name'] = explode(', ', $postData['programSearch']);
        }
        $_SESSION['GOODPRACTICES_SELECTION']['phase_name'] = $postData['phasesSelection'];
        if (!empty($postData['keywordSearch'])) {
            $_SESSION['GOODPRACTICES_SELECTION']['onekeyword'] = explode(', ', $postData['keywordSearch']);
        }
        if (!empty($postData['order']['program'])) {
            if ($postData['order']['program'] === 'asc') {
                $_SESSION['GOODPRACTICES_ORDER'] = array('program_names' => TRUE);
            } else {
                $_SESSION['GOODPRACTICES_ORDER'] = array('program_names' => FALSE);
            }
        }
        if (!empty($postData['order']['phase'])) {
            if ($postData['order']['phase'] === 'asc') {
                $_SESSION['GOODPRACTICES_ORDER'] = array('phase_name' => TRUE);
            } else {
                $_SESSION['GOODPRACTICES_ORDER'] = array('phase_name' => FALSE);
            }
        }
        if (!empty($postData['order']['item'])) {
            if ($postData['order']['item'] === 'asc') {
                $_SESSION['GOODPRACTICES_ORDER'] = array('item' => TRUE);
            } else {
                $_SESSION['GOODPRACTICES_ORDER'] = array('item' => FALSE);
            }
        }
        if (!empty($postData['order']['keywords'])) {
            if ($postData['order']['keywords'] === 'asc') {
                $_SESSION['GOODPRACTICES_ORDER'] = array('keywords' => TRUE);
            } else {
                $_SESSION['GOODPRACTICES_ORDER'] = array('keywords' => FALSE);
            }
        }
    } elseif ($postData['submit'] === 'reset') {
        $_SESSION['GOODPRACTICES_SELECTION'] = NULL;
        $_SESSION['GOODPRACTICES_ORDER'] = NULL;
    }
    header('Location:index.php');
?>
