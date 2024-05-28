<?php 
    session_start();
    if (!isset($_SESSION['LOGGED_USER']) || ($_SESSION['LOGGED_USER']['profile'] !== 'admin' && $_SESSION['LOGGED_USER']['profile'] !== 'superadmin')) { header('Location: logout.php'); exit(); }


    require_once(__DIR__ . '/database_connect.php');
    require_once(__DIR__ . '/sql_functions.php');
    require_once(__DIR__ . '/functions.php');

    $postData = $_POST;

    if ($postData['submit'] === 'users-order') {
        $_SESSION['USERS_SELECTION_ORDER'] = array(Sanitize($postData['users-order']['type']), Sanitize($postData['users-order']['direction']));
    } elseif ($postData['submit'] === 'password-update') {
        $n = Sanitize($postData['n']);
        $p = Sanitize($postData['p']);
        $q = Sanitize($postData['q']);
        $r = Sanitize($postData['r']);
        PasswordUpdate($n, $p, $q, $r);
    } elseif ($postData['submit'] === 'create-user') {
        header('Location:create_user.php');
        exit();
    } elseif ($postData['submit'] === 'to-logs') {
        header('Location:log.php');
        exit();
    }
    header('Location:admin.php');
?>