<?php 
    session_start(); 
    $path = $_SERVER['PHP_SELF'];
    $file = basename($path);
    require_once(__DIR__ . '/functions.php');
    if (!isset($_SESSION['LOGGED_USER']) || $_SERVER['REQUEST_METHOD'] !== 'POST') { Logger(Sanitize($_SESSION['LOGGED_USER']['username']), Sanitize($_SESSION['LOGGED_USER']['profile']), 2, 'Unauthorized access attempt to '.$file); header('Location:logout.php'); exit(); }
    
    require_once(__DIR__ . '/database_connect.php');
    require_once(__DIR__ . '/sql_functions.php');

    $postData = $_POST;
    if ($postData['submit'] === 'submit') {
        $_SESSION['GOODPRACTICES_SELECTION']['program_name'] = $postData['programsSelection'];
        $_SESSION['GOODPRACTICES_SELECTION']['phase_name'] = $postData['phasesSelection'];
        $validateKeywordsSelection = ValidateKeywordsSelection($postData['keywordSearch']);
        $keywordsSelection = $validateKeywordsSelection[0];
        $wrongKeywords = Sanitize(implode(', ', $validateKeywordsSelection[1]));
        $_SESSION['GOODPRACTICES_SELECTION']['onekeyword'] = $keywordsSelection;
        if (!empty($wrongKeywords)) {
            $_SESSION['GOODPRACTICES_KEYWORDS_SELECTION_MESSAGE'] = 'Erreur avec les mots-clés suivant : '.$wrongKeywords;
            Logger(Sanitize($_SESSION['LOGGED_USER']['username']), Sanitize($_SESSION['LOGGED_USER']['profile']), 1, 'Goodpractices selection issue, wrong keywords');
        } else {
            unset($_SESSION['GOODPRACTICES_KEYWORDS_SELECTION_MESSAGE']);
        }
        if (!empty($postData['order']['type']) && !empty($postData['order']['direction'])) {
            $orderType = Sanitize($postData['order']['type']);
            $orderDirection = Sanitize($postData['order']['direction']);
            $_SESSION['GOODPRACTICES_ORDER'] = array($orderType, $orderDirection);
        }
    } elseif ($postData['submit'] === 'reset') {
        unset($_SESSION['GOODPRACTICES_SELECTION']);
        unset($_SESSION['GOODPRACTICES_ORDER']);
        unset($_SESSION['GOODPRACTICES_KEYWORDS_SELECTION_MESSAGE']);
    } elseif ($postData['submit'] === 'create') {
        header('Location:create_goodpractice.php');
        exit();
    } elseif ($postData['submit'] === 'export') {
        $_SESSION['CHECKLIST_CREATION_OUTPUT'] = Sanitize(DownloadChecklist($_SESSION['GOODPRACTICES']));
        if (str_contains(Sanitize($_SESSION['CHECKLIST_CREATION_OUTPUT']), 'Succès !')) {
            Logger(Sanitize($_SESSION['LOGGED_USER']['username']), Sanitize($_SESSION['LOGGED_USER']['profile']), 0, 'Successfully create a checklist');
        } else {
            Logger(Sanitize($_SESSION['LOGGED_USER']['username']), Sanitize($_SESSION['LOGGED_USER']['profile']), 1, 'Failed to create a checklist');
        }
    }
    header('Location:index.php');
?>
