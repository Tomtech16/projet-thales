<?php 
    session_start(); 
    if (!isset($_SESSION['LOGGED_USER'])) { header('Location:index.php'); }
    require_once(__DIR__ . '/database_connect.php');
    require_once(__DIR__ . '/functions.php');
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
    } elseif ($postData['submit'] === 'create') {
        header('Location:create_goodpractice.php');
        exit();
    } elseif ($postData['submit'] === 'export') {
        $_SESSION['CHECKLIST_CREATION_OUTPUT'] = Sanitize(DownloadChecklist($_SESSION['GOODPRACTICES']));
    }
    header('Location:index.php');
?>
