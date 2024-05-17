<?php 
    session_start(); 

    require_once(__DIR__ . '/database_connect.php');
    require_once(__DIR__ . '/functions.php');
    require_once(__DIR__ . '/sql_functions.php');

    $programs = ProgramSelect();
    $phases = PhaseSelect();
    $keywords = KeywordSelect();
    // $_SESSION['PROGRAMS_CHAIN'] = Sanitize(implode(', ', $programs));
    // $_SESSION['PHASE_CHAIN'] = Sanitize(implode(', ', $phases));
    // $_SESSION['KEYWORDS_CHAIN'] = Sanitize(implode(', ', $keywords));
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
<section>
    <h2>Interface de filtrage des bonnes pratiques</h2>
    <form class="selection-form" action="submit_goodpractices_selection.php" method="POST">
        <div class="gestion">
            <div class="phases-selection">
                <h3>Sélection de phase(s)</h3>
                <?php
                    // Generating the checkboxs for phase selection
                    foreach ($phases as $phase) {
                        if (str_contains($phasesSelectionChain, $phase[0])) {
                            echo "<div class='checkbox-line'><input type='checkbox' id='".$phase[0]."' name='phasesSelection[]' value='".$phase[0]."' checked><label for='".$phase[0]."'>".$phase[0]."</label></div>";
                        } else {
                            echo "<div class='checkbox-line'><input type='checkbox' id='".$phase[0]."' name='phasesSelection[]' value='".$phase[0]."'><label for='".$phase[0]."'>".$phase[0]."</label></div>";
                        }
                    }
                ?>
            </div>
            
            <div class="programs-selection">
                <h3>Recherche de programme(s)</h3>
                <input class="search-input" type="text" id="programSearch" name="programSearch" placeholder="Programmes séparés par des virgules" value="<?= $programsSelectionChain ?>">
            </div>
            
            <div class="keywords-selection">
                <h3>Recherche de mot(s)-clé(s)</h3>
                <input class="search-input" type="text" id="keywordSearch" name="keywordSearch" placeholder="Mots-clés séparés par des virgules" value="<?= $keywordsSelectionChain ?>">
            </div>
        </div>
        <div class="gestion">
            <div class="order-selection">
                <div class="radio-text">
                    <h3>Sélection de tri(s)</h3>
                    <p>Ascendant/Descendant</p>
                </div>
                <div class='radio-line'>
                    <h4>Programme : </h4>
                    <input type='radio' id='order-program-asc' name='order[program]' value='asc' <?php if (isset($_SESSION['GOODPRACTICES_ORDER']['program_names']) && $_SESSION['GOODPRACTICES_ORDER']['program_names']) echo 'checked'; ?>>
                    <label for='order-program-asc'>ASC</label>
                    <input type='radio' id='order-program-desc' name='order[program]' value='desc' <?php if (isset($_SESSION['GOODPRACTICES_ORDER']['program_names']) && !$_SESSION['GOODPRACTICES_ORDER']['program_names']) echo 'checked'; ?>>
                    <label for='order-program-desc'>DESC</label>
                    <input type='radio' id='order-program-none' name='order[program]' value='' <?php if (!isset($_SESSION['GOODPRACTICES_ORDER']['program_names'])) echo 'checked'; ?>>
                    <label for='order-program-none'>Aucun tri</label>
                </div>

                <div class='radio-line'>
                    <h4>Phase : </h4>
                    <input type='radio' id='order-phase-asc' name='order[phase]' value='asc' <?php if (isset($_SESSION['GOODPRACTICES_ORDER']['phase_name']) && $_SESSION['GOODPRACTICES_ORDER']['phase_name']) echo 'checked'; ?>>
                    <label for='order-phase-asc'>ASC</label>
                    <input type='radio' id='order-phase-desc' name='order[phase]' value='desc' <?php if (isset($_SESSION['GOODPRACTICES_ORDER']['phase_name']) && !$_SESSION['GOODPRACTICES_ORDER']['phase_name']) echo 'checked'; ?>>
                    <label for='order-phase-desc'>DESC</label>
                    <input type='radio' id='order-phase-none' name='order[phase]' value='' <?php if (!isset($_SESSION['GOODPRACTICES_ORDER']['phase_name'])) echo 'checked'; ?>>
                    <label for='order-phase-none'>Aucun tri</label>
                </div>

                <div class='radio-line'>
                    <h4>Item : </h4>
                    <input type='radio' id='order-item-asc' name='order[item]' value='asc' <?php if (isset($_SESSION['GOODPRACTICES_ORDER']['item']) && $_SESSION['GOODPRACTICES_ORDER']['item']) echo 'checked'; ?>>
                    <label for='order-item-asc'>ASC</label>
                    <input type='radio' id='order-item-desc' name='order[item]' value='desc' <?php if (isset($_SESSION['GOODPRACTICES_ORDER']['item']) && !$_SESSION['GOODPRACTICES_ORDER']['item']) echo 'checked'; ?>>
                    <label for='order-item-desc'>DESC</label>
                    <input type='radio' id='order-item-none' name='order[item]' value='' <?php if (!isset($_SESSION['GOODPRACTICES_ORDER']['item'])) echo 'checked'; ?>>
                    <label for='order-item-none'>Aucun tri</label>
                </div>

                <div class='radio-line'>
                    <h4>Mots-clés : </h4>
                    <input type='radio' id='order-keyword-asc' name='order[keywords]' value='asc' <?php if (isset($_SESSION['GOODPRACTICES_ORDER']['keywords']) && $_SESSION['GOODPRACTICES_ORDER']['keywords']) echo 'checked'; ?>>
                    <label for='order-keyword-asc'>ASC</label>
                    <input type='radio' id='order-keyword-desc' name='order[keywords]' value='desc' <?php if (isset($_SESSION['GOODPRACTICES_ORDER']['keywords']) && !$_SESSION['GOODPRACTICES_ORDER']['keywords']) echo 'checked'; ?>>
                    <label for='order-keyword-desc'>DESC</label>
                    <input type='radio' id='order-keyword-none' name='order[keywords]' value='' <?php if (!isset($_SESSION['GOODPRACTICES_ORDER']['keywords'])) echo 'checked'; ?>>
                    <label for='order-keyword-none'>Aucun tri</label>
                </div>
            </div>
        </div>
        <div class="gestion">
            <div class="selection-button">
                <button id="submit" type="submit" name="submit" value="submit">Appliquer</button>
                <button id="reset" type="submit" name="submit" value="reset">Effacer les filtres</button>
            </div>
        </div>
    </form> 
</section>