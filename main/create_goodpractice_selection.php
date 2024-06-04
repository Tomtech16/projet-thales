<?php 
    session_start(); 
    if (!isset($_SESSION['LOGGED_USER'])) { header('Location:index.php'); }
    require_once(__DIR__ . '/database_connect.php');
    require_once(__DIR__ . '/functions.php');
    require_once(__DIR__ . '/sql_functions.php');

    $programs = ProgramSelect();
    $phases = PhaseSelect();

    if (isset($_SESSION['GOODPRACTICES_CREATION']['program_name'])) {
        $programsSelectionChain = Sanitize(implode(', ', $_SESSION['GOODPRACTICES_CREATION']['program_name']));
    } else {
        $programsSelectionChain = '';
    }
    if (isset($_SESSION['GOODPRACTICES_CREATION']['phase_name'])) {
        $phaseSelectionChain = Sanitize(implode(', ', $_SESSION['GOODPRACTICES_CREATION']['phase_name']));
    } else {
        $phaseSelectionChain = '';
    }
    if (isset($_SESSION['GOODPRACTICES_CREATION']['onekeyword'])) {
        $keywordsSelectionChain = Sanitize(implode(', ', $_SESSION['GOODPRACTICES_CREATION']['onekeyword']));
    } else {
        $keywordsSelectionChain = '';
    }
?>
<section class="goodpractices-selection">
    <h2>Interface de création de bonne pratique</h2>
    <form class="selection-form" action="submit_create_goodpractice.php" method="POST">
        <div class="gestion">

            <div class="programs-selection">
                <h3>Choix des programmes</h3>
                <div class="checkbox-area">
                    <?php foreach ($programs as $program): ?>
                        <div class="checkbox-line">
                            <input class="checkbox" type="checkbox" id="id<?= $program[0] ?>" name="programsSelection[]" value="<?= $program[0] ?>" <?= (str_contains($programsSelectionChain, $program[0]) ? 'checked' : '') ?>>
                            <label for="id<?= $program[0] ?>"><?= $program[0] ?></label>
                        </div>
                    <?php endforeach; ?>   
                </div>         
            </div>

            <div class="phase-selection">
                <h3>Choix de la phase</h3>
                <div class="radio-area">
                    <div class='radio-line'>
                        <label for='creation-phase'>Phase : </label>
                        <select id='creation-phase' name='creation-phase[]'>
                            <?php foreach ($phases as $phase): ?>
                                <option id="<?= $phase[0] ?>" value="<?= $phase[0] ?>" <?= (str_contains($phaseSelectionChain, $phase[0]) ? 'selected' : '') ?>><?= $phase[0] ?></option>
                            <?php endforeach; ?>
                        </select>
                </div>
            </div>
            
            <div class="keywords-selection">
                <h3>Recherche de mot(s)-clé(s)</h3>
                <input class="search-input" type="text" id="keywordSearch" name="keywordSearch" placeholder="Mots-clés séparés par des virgules" value="<?= $keywordsSelectionChain ?>">
                <p><?= $_SESSION['GOODPRACTICES_KEYWORDS_SELECTION_MESSAGE'] ?></p>
            </div>
        </div>

        <div class="selection-button">
            <button id="submit" type="submit" name="submit" value="submit">Appliquer</button>
            <button id="reset" type="submit" name="submit" value="reset">Effacer les filtres</button>
            <button id="create" type="submit" name="submit" value="create">Créer une bonne pratique</button>
            <button id="export" type="submit" name="submit" value="export">Télécharger la checklist</button>
        </div>
    </form> 
</section>