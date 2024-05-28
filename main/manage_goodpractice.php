<?php
    session_start();
    if (!isset($_SESSION['LOGGED_USER'])) { header('Location:index.php'); }
    require_once(__DIR__ . '/database_connect.php');
    require_once(__DIR__ . '/functions.php');
    require_once(__DIR__ . '/sql_functions.php');

    $postData = $_POST;
    $goodpracticeId = Sanitize($postData['goodpracticeId']);

    if ($postData['submit'] === 'erase') {
        if (!isset($postData['programNames'])) {
            $_SESSION['ERASED_GOODPRACTICES'][] = $goodpracticeId;
        } else {
            $programNames = $postData['programNames'];
            foreach ($programNames as $programName) {
                $_SESSION['ERASED_GOODPRACTICES_PROGRAMS']['id'.$goodpracticeId][] = Sanitize($programName);
            }
        }
    } elseif ($postData['submit'] === 'duplicate' && !empty($postData['programNames'])) {    
        $programNames = $postData['programNames'];
        $goodpracticeId = Sanitize($postData['goodpracticeId']);
        DuplicateGoodpractice($programNames, $goodpracticeId);
    } elseif ($postData['submit'] === 'operator-delete') {
        if (!isset($postData['programNames'])) {
            DeleteOperatorGoodpractice($goodpracticeId);
        } else {
            $programNames = $postData['programNames'];
            DeleteOperatorGoodpractice($goodpracticeId, $programNames);
        }
    } elseif ($_SESSION['LOGGED_USER']['profile'] === 'admin' || $_SESSION['LOGGED_USER']['profile'] === 'superadmin') {
        if ($postData['submit'] === 'delete') {
            if (!isset($postData['programNames'])) {
                DeleteGoodpractice($goodpracticeId);
            } else {
                $programNames = $postData['programNames'];
                DeleteGoodpractice($goodpracticeId, $programNames);
            }
        } elseif ($postData['submit'] === 'restore') {
            RestoreGoodpractice($goodpracticeId);
        }
    }
    header('Location:index.php');
?>