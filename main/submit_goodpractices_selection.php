<?php 
    session_start(); 
    $path = $_SERVER['PHP_SELF'];
    $file = basename($path);
    require_once(__DIR__ . '/functions.php');
    if (!isset($_SESSION['LOGGED_USER']) || $_SERVER['REQUEST_METHOD'] !== 'POST') { Logger(Sanitize($_SESSION['LOGGED_USER']['username']), Sanitize($_SESSION['LOGGED_USER']['profile']), 2, 'Unauthorized access attempt to '.$file); header('Location:logout.php'); exit(); }
    
    require_once(__DIR__ . '/config/database_connect.php');
    require_once(__DIR__ . '/sql_functions.php');

    $postData = $_POST;
    if ($postData['submit'] === 'select-all-programs') {
        if ($_SESSION['SELECT_ALL_PROGRAMS']) {
            $_SESSION['SELECT_ALL_PROGRAMS'] = 0;
            unset($_SESSION['SELECT_ALL_PROGRAMS_CHECK']);
        } else {
            $_SESSION['SELECT_ALL_PROGRAMS'] = 1;
        }
        $_SESSION['PHASE_CHECK'] = $postData['phasesSelection'];
        if (isset($_SESSION['GOODPRACTICES_SELECTION']['onekeyword'])) {
            $keywordsSelectionChain = Sanitize(implode(', ', $_SESSION['GOODPRACTICES_SELECTION']['onekeyword']));
        } else {
            $keywordsSelectionChain = '';
        }
        $_SESSION['KEYWORDS_CHECK'] = str_replace($keywordsSelectionChain, '', Sanitize($postData['keywordSearch']));
    } elseif ($postData['submit'] === 'submit') {
        $_SESSION['GOODPRACTICES_SELECTION']['program_name'] = $postData['programsSelection'];
        $_SESSION['SELECT_ALL_PROGRAMS_CHECK'] = $postData['programsSelection'];
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
        unset($_SESSION['PHASE_CHECK']);
        unset($_SESSION['KEYWORDS_CHECK']);
    } elseif ($postData['submit'] === 'reset') {
        unset($_SESSION['GOODPRACTICES_SELECTION']);
        unset($_SESSION['GOODPRACTICES_ORDER']);
        unset($_SESSION['GOODPRACTICES_KEYWORDS_SELECTION_MESSAGE']);
        unset($_SESSION['PHASE_CHECK']);
        unset($_SESSION['KEYWORDS_CHECK']);
        $_SESSION['SELECT_ALL_PROGRAMS'] = 0;
    } elseif ($postData['submit'] === 'create') {
        header('Location:create_goodpractice.php');
        exit();
    } elseif ($postData['submit'] === 'export-csv') {
        $whereIs = $_SESSION['GOODPRACTICES_SELECTION'];
        $orderBy = $_SESSION['GOODPRACTICES_ORDER'];
        $erased = $_SESSION['ERASED_GOODPRACTICES'];
        $erasedPrograms = $_SESSION['ERASED_GOODPRACTICES_PROGRAMS'];
        $username = Sanitize($_SESSION['LOGGED_USER']['username']);
        $profile = Sanitize($_SESSION['LOGGED_USER']['profile']);
        $download = DownloadChecklist($whereIs, $orderBy, $erased, $erasedPrograms, $username, $profile, 'csv');
        $_SESSION['CHECKLIST_CREATION_OUTPUT'] = Sanitize($download[0]);
        $_SESSION['CHECKLIST_FILENAME'] = Sanitize($download[1]);
    } elseif ($postData['submit'] === 'export-pdf') {
        $whereIs = $_SESSION['GOODPRACTICES_SELECTION'];
        $orderBy = $_SESSION['GOODPRACTICES_ORDER'];
        $erased = $_SESSION['ERASED_GOODPRACTICES'];
        $erasedPrograms = $_SESSION['ERASED_GOODPRACTICES_PROGRAMS'];
        $username = Sanitize($_SESSION['LOGGED_USER']['username']);
        $profile = Sanitize($_SESSION['LOGGED_USER']['profile']);
        $download = DownloadChecklist($whereIs, $orderBy, $erased, $erasedPrograms, $username, $profile, 'pdf');
        $_SESSION['CHECKLIST_CREATION_OUTPUT'] = Sanitize($download[0]);
        $_SESSION['CHECKLIST_FILENAME'] = Sanitize($download[1]);
    }
    header('Location:index.php');
?>
