<?php 
    session_start(); 
    if (!isset($_SESSION['LOGGED_USER'])) { header('Location:index.php'); }
    require_once(__DIR__ . '/database_connect.php');
    require_once(__DIR__ . '/functions.php');
    require_once(__DIR__ . '/sql_functions.php');

    $programs = ProgramSelect();
    $phases = PhaseSelect();

    $orderType = Sanitize($_SESSION['GOODPRACTICES_ORDER'][0]);
    $orderDirection = Sanitize($_SESSION['GOODPRACTICES_ORDER'][1]);

    if (isset($_SESSION['GOODPRACTICES_SELECTION']['program_name'])) {
        $programsSelectionChain = Sanitize(implode(', ', $_SESSION['GOODPRACTICES_SELECTION']['program_name']));
    } else {
        $programsSelectionChain = '';
    }
    if (isset($_SESSION['GOODPRACTICES_SELECTION']['phase_name'])) {
        $phasesSelectionChain = Sanitize(implode(', ', $_SESSION['GOODPRACTICES_SELECTION']['phase_name']));
    } else {
        $phasesSelectionChain = '';
    }
    if (isset($_SESSION['GOODPRACTICES_SELECTION']['onekeyword'])) {
        $keywordsSelectionChain = Sanitize(implode(', ', $_SESSION['GOODPRACTICES_SELECTION']['onekeyword']));
    } else {
        $keywordsSelectionChain = '';
    }
?>
<section class="goodpractices-selection">
    <h2>Interface de filtrage des bonnes pratiques</h2>
    <form class="selection-form" action="submit_goodpractices_selection.php" method="POST">
        <div class="gestion">

            <div class="programs-selection">
                <h3>Recherche de programme(s)</h3>
                <div class="checkbox-area">
                    <?php foreach ($programs as $program): ?>
                        <div class="checkbox-line">
                            <input class="checkbox" type="checkbox" id="id<?= $program[0] ?>" name="programsSelection[]" value="<?= $program[0] ?>" <?= (str_contains($programsSelectionChain, $program[0]) ? 'checked' : '') ?>>
                            <label for="id<?= $program[0] ?>"><?= $program[0] ?></label>
                        </div>
                    <?php endforeach; ?>   
                </div>         
            </div>

            <div class="phases-and-order-selection">
                <h3>Sélection de phase(s)</h3>
                <div class="checkbox-area">
                    <?php foreach ($phases as $phase): ?>
                        <div class="checkbox-line">
                            <input class="checkbox" type="checkbox" id="<?= $phase[0] ?>" name="phasesSelection[]" value="<?= $phase[0] ?>" <?= (str_contains($phasesSelectionChain, $phase[0]) ? 'checked' : '') ?>>
                            <label for="<?= $phase[0] ?>"><?= $phase[0] ?></label>
                        </div>
                    <?php endforeach; ?>
                </div>
                <h3>Sélection de tri</h3>
                <div class="radio-area">
                    <div class='radio-line'>
                        <label for='order-type'>Type de tri :</label>
                        <select id='order-type' name='order[type]'>
                            <option value='program_names' <?= $orderType === 'program_names' ? 'selected' : '' ?>>Programme</option>
                            <option value='phase_name' <?= $orderType === 'phase_name' ? 'selected' : '' ?>>Phase</option>
                            <option value='item' <?= $orderType === 'item' ? 'selected' : '' ?>>Item</option>
                            <option value='keywords' <?= $orderType === 'keywords' ? 'selected' : '' ?>>Mots-clés</option>
                        </select>
                    </div>

                    <div class='radio-line'>
                        <label for='order-direction'>Direction :</label>
                        <select id='order-direction' name='order[direction]'>
                            <option value='asc' <?= $orderDirection === 'asc' ? 'selected' : '' ?>>Ascendant</option>
                            <option value='desc' <?= $orderDirection === 'desc' ? 'selected' : '' ?>>Descendant</option>
                        </select>
                    </div>
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
        </div>
    </form> 
</section>

