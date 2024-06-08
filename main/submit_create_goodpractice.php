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
        if ($_SESSION['CREATE_ALL_PROGRAMS']) {
            $_SESSION['CREATE_ALL_PROGRAMS'] = 0;
            unset($_SESSION['GOODPRACTICES_CREATION']['program_name']);
        } else {
            $_SESSION['CREATE_ALL_PROGRAMS'] = 1;
        }
        $_SESSION['CREATE_PHASE_CHECK'] = $postData['phasesSelection'];
        if (isset($_SESSION['GOODPRACTICES_CREATION']['onekeyword'])) {
            $keywordsSelectionChain = Sanitize(implode(', ', $_SESSION['GOODPRACTICES_CREATION']['onekeyword']));
        } else {
            $keywordsSelectionChain = '';
        }
        $_SESSION['CREATE_KEYWORDS_CHECK'] = str_replace($keywordsSelectionChain, '', Sanitize($postData['keywordSearch']));
        $_SESSION['CREATE_ADD_KEYWORDS_CHECK'] = Sanitize($postData['addKeyword']);
    } elseif ($postData['submit'] === 'reset') {
        $item = rtrim(Sanitize($postData['goodpractice']));
        if (!empty($item)) {
            $_SESSION['GOODPRACTICE_TEXT'] = $item;
        }
        unset($_SESSION['GOODPRACTICES_CREATION']);
        unset($_SESSION['GOODPRACTICES_KEYWORDS_CREATION_MESSAGE']);
        unset($_SESSION['CREATE_PHASE_CHECK']);
        unset($_SESSION['CREATE_KEYWORDS_CHECK']);
        unset($_SESSION['CREATE_ADD_KEYWORDS_CHECK']);
        $_SESSION['CREATE_ALL_PROGRAMS'] = 0;
    } elseif ($postData['submit'] === 'submit') {
        $_SESSION['GOODPRACTICES_CREATION']['program_name'] = $postData['programsSelection'];
        $_SESSION['GOODPRACTICES_CREATION']['phase_name'] = $postData['phasesSelection'];
        $validateKeywordsSelection = ValidateKeywordsSelection(Sanitize($postData['keywordSearch']));
        $keywordsSelection = $validateKeywordsSelection[0];
        $wrongKeywords = Sanitize(implode(', ', $validateKeywordsSelection[1]));
        $_SESSION['GOODPRACTICES_CREATION']['onekeyword'] = $keywordsSelection;
        $addKeyword = Sanitize($postData['addKeyword']);
        $_SESSION['GOODPRACTICES_CREATION']['addOnekeyword'] = $addKeyword; 
        if (!empty($wrongKeywords)) {
            $_SESSION['GOODPRACTICE_CREATION_MESSAGE'] = 'Erreur !\n\nUn ou des mots-clés sont invalides.';
            $_SESSION['GOODPRACTICES_KEYWORDS_CREATION_MESSAGE'] = 'Erreur avec les mots-clés suivant : '.$wrongKeywords;
            Logger(Sanitize($_SESSION['LOGGED_USER']['username']), Sanitize($_SESSION['LOGGED_USER']['profile']), 1, 'Failed to create a goodpractice, wrong keywords');
        } else {
            if (!empty($postData['programsSelection'])) {
                $item = rtrim(Sanitize($postData['goodpractice']));
                if (!empty($item)) {
                    unset($_SESSION['GOODPRACTICE_TEXT']);
                    $programNames = $postData['programsSelection'];
                    $phaseName = Sanitize($postData['phasesSelection']);
                    if (!empty($keywordsSelection[0]) && !empty($addKeyword)) {
                        $keywords = array_merge($keywordsSelection, explode(', ', $addKeyword));
                    } elseif (!empty($keywordsSelection[0])) {
                        $keywords = $keywordsSelection;
                    } elseif (!empty($addKeyword)) {
                        $keywords = explode(', ', $addKeyword);
                    } else {
                        $keywords = array(' ');
                    }
                    InsertGoodpractice($programNames, $phaseName, $item, $keywords);
                    $_SESSION['GOODPRACTICE_CREATION_MESSAGE'] = 'Succès !\n\nLa bonne pratique : \n\n'.$item.'\n\nA bien été créée.';
                    Logger(Sanitize($_SESSION['LOGGED_USER']['username']), Sanitize($_SESSION['LOGGED_USER']['profile']), 1, 'Successfully create a goodpractice');
                    unset($_SESSION['GOODPRACTICES_CREATION']['addOnekeyword']);
                } else {
                    $_SESSION['GOODPRACTICE_CREATION_MESSAGE'] = 'Erreur !\n\nLa bonne pratique ne doit pas être vide.';
                    Logger(Sanitize($_SESSION['LOGGED_USER']['username']), Sanitize($_SESSION['LOGGED_USER']['profile']), 0, 'Failed to create a goodpractice, empty goodpractice');
                }
            } else {
                $_SESSION['GOODPRACTICE_CREATION_MESSAGE'] = 'Erreur !\n\nVeuillez sélectionner au moins un programme pour la bonne pratique.';
                Logger(Sanitize($_SESSION['LOGGED_USER']['username']), Sanitize($_SESSION['LOGGED_USER']['profile']), 0, 'Failed to create a goodpractice, no program selected');
            }
            unset($_SESSION['GOODPRACTICES_KEYWORDS_CREATION_MESSAGE']);
        }
        unset($_SESSION['CREATE_PHASE_CHECK']);
        unset($_SESSION['CREATE_KEYWORDS_CHECK']);
        unset($_SESSION['CREATE_ADD_KEYWORDS_CHECK']);
    }
    header('Location:create_goodpractice.php');
?>