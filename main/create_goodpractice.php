<?php 
    session_start(); 
    $path = $_SERVER['PHP_SELF'];
    $file = basename($path);
    require_once(__DIR__ . '/functions.php');
    if (!isset($_SESSION['LOGGED_USER'])) { Logger(NULL, NULL, 2, 'Unauthorized access attempt to '.$file); header('Location:logout.php'); exit(); }
    
    require_once(__DIR__ . '/database_connect.php');
    require_once(__DIR__ . '/sql_functions.php');


    if (isset($_SESSION['GOODPRACTICE_CREATION_MESSAGE'])) {
        echo '<script>alert("'.Sanitize($_SESSION['GOODPRACTICE_CREATION_MESSAGE']).'")</script>';
        unset($_SESSION['GOODPRACTICE_CREATION_MESSAGE']);
    }
?>

<?php require_once(__DIR__ . '/header.php'); ?>

<?php
    $programs = ProgramSelect();
    $phases = PhaseSelect();

    if (isset($_SESSION['GOODPRACTICES_CREATION']['program_name'])) {
        $programsSelectionChain = Sanitize(implode(', ', $_SESSION['GOODPRACTICES_CREATION']['program_name']));
    } else {
        $programsSelectionChain = '';
    }
    if (!isset($_SESSION['CREATE_ALL_PROGRAMS'])) {
        $_SESSION['CREATE_ALL_PROGRAMS'] = 0;
    }
    if (isset($_SESSION['GOODPRACTICES_CREATION']['phase_name'])) {
        $phaseSelectionChain = Sanitize($_SESSION['GOODPRACTICES_CREATION']['phase_name']);
    } else {
        $phaseSelectionChain = '';
    }
    if (isset($_SESSION['CREATE_PHASE_CHECK'])) {
        $phaseSelectionChain = Sanitize($_SESSION['CREATE_PHASE_CHECK']);
    }
    if (isset($_SESSION['GOODPRACTICES_CREATION']['onekeyword'])) {
        $keywordsSelectionChain = Sanitize(implode(', ', $_SESSION['GOODPRACTICES_CREATION']['onekeyword']));
    } else {
        $keywordsSelectionChain = '';
    }
    if (isset($_SESSION['CREATE_KEYWORDS_CHECK'])) {
        $keywordsSelectionChain .= Sanitize($_SESSION['CREATE_KEYWORDS_CHECK']);
    }
    if (isset($_SESSION['GOODPRACTICES_CREATION']['addOnekeyword'])) {
        $addKeywordsSelectionChain = Sanitize($_SESSION['GOODPRACTICES_CREATION']['addOnekeyword']);
    } else {
        $addKeywordsSelectionChain = '';
    }
    if (isset($_SESSION['CREATE_ADD_KEYWORDS_CHECK'])) {
        $addKeywordsSelectionChain .= Sanitize($_SESSION['CREATE_ADD_KEYWORDS_CHECK']);
    }
?>

<section class="goodpractices-selection">
    <h2>Interface de création de bonnes pratiques</h2>
    <form class="selection-form" id="goodpractice-creation-form" action="submit_create_goodpractice.php" method="POST">
        <div class="gestion">
            <div class="programs-selection">
                <div id="programs-selection-title-and-button">
                    <h3>Sélection des programmes</h3>
                    <button id="select-all-programs" type="submit" name="submit" value="select-all-programs"><?= $_SESSION['CREATE_ALL_PROGRAMS'] ? 'Tout désélectionner' : 'Tout sélectionner' ?></button>
                </div>
                <div class="checkbox-area">
                    <?php foreach ($programs as $program): ?>
                        <div class="checkbox-line">
                            <input class="checkbox" type="checkbox" id="id<?= $program[0] ?>" name="programsSelection[]" value="<?= $program[0] ?>" <?= (str_contains($programsSelectionChain, $program[0]) || $_SESSION['CREATE_ALL_PROGRAMS'] ? 'checked' : '') ?>>
                            <label for="id<?= $program[0] ?>"><?= $program[0] ?></label>
                        </div>
                    <?php endforeach; ?>   
                </div>         
            </div>

            <div class="phase-selection">
                <h3>Sélection de la phase</h3>
                <div class="radio-area">
                    <div class='radio-line'>
                        <label for='phasesSelection'>Phase : </label>
                        <select id='phasesSelection' name='phasesSelection'>
                            <?php foreach ($phases as $phase): ?>
                                <option id="<?= $phase[0] ?>" value="<?= $phase[0] ?>" <?= (str_contains($phaseSelectionChain, $phase[0]) ? 'selected' : '') ?>><?= $phase[0] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>
            
            <div class="keywords-selection">
                <h3>Sélection des mots-clés</h3>
                <input class="search-input" type="text" id="keywordSearch" name="keywordSearch" placeholder="Mots-clés séparés par des virgules" value="<?= $keywordsSelectionChain ?>">
                <p><?= Sanitize($_SESSION['GOODPRACTICES_KEYWORDS_CREATION_MESSAGE']) ?></p>
            </div>

            <div class="keywords-selection">
                <h3>Nouveaux mots-clés</h3>
                <input class="search-input" type="text" id="keywordSearch" name="addKeyword" placeholder="Mots-clés séparés par des virgules" value="<?= $addKeywordsSelectionChain ?>">
            </div>
        </div>

        <div class="gestion">
            <div class="write-goodpractice">
                <h3>Ecriture de la nouvelle bonne pratique</h3>
                <textarea id="write-area" name="goodpractice" placeholder="Ecrivez la nouvelle bonne pratique" autofocus><?= (isset($_SESSION['GOODPRACTICE_TEXT']) && !empty($_SESSION['GOODPRACTICE_TEXT'])) ? $_SESSION['GOODPRACTICE_TEXT'] : NULL ?></textarea>
            </div>
        </div>

        <div class="selection-button" id="create-goodpractice-selection-button">
            <button id="submit" type="submit" name="submit" value="submit">Créer la bonne pratique</button>
            <button id="reset" type="submit" name="submit" value="reset">Effacer la sélection</button>
        </div>
    </form> 
</section>

<?php require_once(__DIR__ . '/footer.php'); ?>
