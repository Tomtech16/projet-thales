<?php
    session_start();

    require_once(__DIR__ . '/functions.php');

    $postData = $_POST;
    $goodpracticeId = Sanitize($_POST['goodpracticeId']);

    // Make sure the session array exists
    if (!isset($_SESSION['DELETED_GOODPRACTICES_IDS'])) {
        $_SESSION['DELETED_GOODPRACTICES_IDS'] = array(); // Initialize the array if it doesn't exist
    }

    // Add the new value to the end of the session array
    $_SESSION['DELETED_GOODPRACTICES_IDS'][] = $goodpracticeId;

    header('Location:index.php');
?>