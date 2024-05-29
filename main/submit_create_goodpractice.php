<?php 
    session_start(); 
    $path = $_SERVER['PHP_SELF'];
    $file = basename($path);
    require_once(__DIR__ . '/functions.php');
    if (!isset($_SESSION['LOGGED_USER']) || $_SERVER['REQUEST_METHOD'] !== 'POST') { Logger(Sanitize($_SESSION['LOGGED_USER']['username']), Sanitize($_SESSION['LOGGED_USER']['profile']), 2, 'Unauthorized access attempt to '.$file); header('Location:logout.php'); exit(); }
    
    require_once(__DIR__ . '/database_connect.php');
    require_once(__DIR__ . '/sql_functions.php');

    $postData = $_POST;
    if ($postData['submit'] === 'reset') {
        $item = rtrim(Sanitize($postData['goodpractice']));
        if (!empty($item)) {
            $_SESSION['GOODPRACTICE_TEXT'] = $item;
        }
        unset($_SESSION['GOODPRACTICES_CREATION']);
        unset($_SESSION['GOODPRACTICES_KEYWORDS_CREATION_MESSAGE']);
    } elseif ($postData['submit'] === 'submit') {
        $_SESSION['GOODPRACTICES_CREATION']['program_name'] = $postData['programsSelection'];
        $_SESSION['GOODPRACTICES_CREATION']['phase_name'] = $postData['phasesSelection'];
        $validateKeywordsSelection = ValidateKeywordsSelection(Sanitize($postData['keywordSearch']));
        $keywordsSelection = $validateKeywordsSelection[0];
        $wrongKeywords = Sanitize(implode(', ', $validateKeywordsSelection[1]));
        $_SESSION['GOODPRACTICES_CREATION']['onekeyword'] = $keywordsSelection;
        if (!empty($wrongKeywords)) {
            $_SESSION['GOODPRACTICES_KEYWORDS_CREATION_MESSAGE'] = 'Erreur avec les mots-clés suivant : '.$wrongKeywords;
            Logger(Sanitize($_SESSION['LOGGED_USER']['username']), Sanitize($_SESSION['LOGGED_USER']['profile']), 1, 'Goodpractices selection issue, wrong keywords');
        } else {
            unset($_SESSION['GOODPRACTICES_KEYWORDS_CREATION_MESSAGE']);
        }
        if (!empty($postData['programsSelection'])) {
            $item = rtrim(Sanitize($postData['goodpractice']));
            if (!empty($item)) {
                unset($_SESSION['GOODPRACTICE_TEXT']);
                $programNames = $postData['programsSelection'];
                $phaseName = Sanitize($postData['phasesSelection']);
                $keywords = $keywordsSelection;
                InsertGoodpractice($programNames, $phaseName, $item, $keywordsSelection);
                $_SESSION['GOODPRACTICE_CREATION_MESSAGE'] = 'Succès !\n\nLa bonne pratique : \n\n'.$item.'\n\nA bien été créée.';
                Logger(Sanitize($_SESSION['LOGGED_USER']['username']), Sanitize($_SESSION['LOGGED_USER']['profile']), 1, 'Successfully create a goodpractice');
            } else {
                $_SESSION['GOODPRACTICE_CREATION_MESSAGE'] = 'Erreur !\n\nLa bonne pratique ne doit pas être vide.';
                Logger(Sanitize($_SESSION['LOGGED_USER']['username']), Sanitize($_SESSION['LOGGED_USER']['profile']), 0, 'Failed to create a goodpractice, empty goodpractice');
            }
        } else {
            $_SESSION['GOODPRACTICE_CREATION_MESSAGE'] = 'Erreur !\n\nVeuillez sélectionner au moins un programme pour la bonne pratique.';
            Logger(Sanitize($_SESSION['LOGGED_USER']['username']), Sanitize($_SESSION['LOGGED_USER']['profile']), 0, 'Failed to create a goodpractice, no program selected');
        }
    }
    header('Location:create_goodpractice.php');
?>