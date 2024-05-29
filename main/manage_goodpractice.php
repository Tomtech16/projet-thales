<?php
    session_start();
    $path = $_SERVER['PHP_SELF'];
    $file = basename($path);
    require_once(__DIR__ . '/functions.php');
    if (!isset($_SESSION['LOGGED_USER']) || $_SERVER['REQUEST_METHOD'] !== 'POST') { Logger(Sanitize($_SESSION['LOGGED_USER']['username']), Sanitize($_SESSION['LOGGED_USER']['profile']), 2, 'Unauthorized access attempt to '.$file); header('Location:logout.php'); exit(); }
    
    require_once(__DIR__ . '/database_connect.php');
    require_once(__DIR__ . '/sql_functions.php');

    $postData = $_POST;
    $goodpracticeId = Sanitize($postData['goodpracticeId']);

    if ($postData['submit'] === 'erase') {
        if (!isset($postData['programNames'])) {
            $_SESSION['ERASED_GOODPRACTICES'][] = $goodpracticeId;
            Logger(Sanitize($_SESSION['LOGGED_USER']['username']), Sanitize($_SESSION['LOGGED_USER']['profile']), 0, 'Goodpractice temporarily erased');
        } else {
            $programNames = $postData['programNames'];
            foreach ($programNames as $programName) {
                $_SESSION['ERASED_GOODPRACTICES_PROGRAMS']['id'.$goodpracticeId][] = Sanitize($programName);
            }
            Logger(Sanitize($_SESSION['LOGGED_USER']['username']), Sanitize($_SESSION['LOGGED_USER']['profile']), 0, 'Goodpractice temporarily erased for one or more programs');
        }
    } elseif ($postData['submit'] === 'duplicate' && !empty($postData['programNames'])) {    
        $programNames = $postData['programNames'];
        $goodpracticeId = Sanitize($postData['goodpracticeId']);
        DuplicateGoodpractice($programNames, $goodpracticeId);
        Logger(Sanitize($_SESSION['LOGGED_USER']['username']), Sanitize($_SESSION['LOGGED_USER']['profile']), 0, 'Goodpractice duplicated for one or more programs');
    } elseif ($postData['submit'] === 'operator-delete') {
        if (!isset($postData['programNames'])) {
            DeleteOperatorGoodpractice($goodpracticeId);
            Logger(Sanitize($_SESSION['LOGGED_USER']['username']), Sanitize($_SESSION['LOGGED_USER']['profile']), 1, 'Goodpractice non-permanently deleted by an operator');
        } else {
            $programNames = $postData['programNames'];
            DeleteOperatorGoodpractice($goodpracticeId, $programNames);
            Logger(Sanitize($_SESSION['LOGGED_USER']['username']), Sanitize($_SESSION['LOGGED_USER']['profile']), 1, 'Goodpractice non-permanently deleted by an operator for one or more programs');
        }
    } elseif ($_SESSION['LOGGED_USER']['profile'] === 'admin' || $_SESSION['LOGGED_USER']['profile'] === 'superadmin') {
        if ($postData['submit'] === 'delete') {
            if (!isset($postData['programNames'])) {
                DeleteGoodpractice($goodpracticeId);
                Logger(Sanitize($_SESSION['LOGGED_USER']['username']), Sanitize($_SESSION['LOGGED_USER']['profile']), 2, 'Goodpractice permanently deleted');
            } else {
                $programNames = $postData['programNames'];
                DeleteGoodpractice($goodpracticeId, $programNames);
            }
            Logger(Sanitize($_SESSION['LOGGED_USER']['username']), Sanitize($_SESSION['LOGGED_USER']['profile']), 2, 'Goodpractice permanently deleted for one or more programs');
        } elseif ($postData['submit'] === 'restore') {
            RestoreGoodpractice($goodpracticeId);
            Logger(Sanitize($_SESSION['LOGGED_USER']['username']), Sanitize($_SESSION['LOGGED_USER']['profile']), 0, 'Goodpractice restored');
        }
    }
    header('Location:index.php');
?>