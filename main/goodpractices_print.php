<?php 
    session_start();   
    $path = $_SERVER['PHP_SELF'];
    $file = basename($path);
    require_once(__DIR__ . '/functions.php');
    if (!isset($_SESSION['LOGGED_USER'])) { Logger(NULL, NULL, 2, 'Unauthorized access attempt to '.$file); header('Location:logout.php'); exit(); }

    require_once(__DIR__ . '/database_connect.php');
    require_once(__DIR__ . '/sql_functions.php');

    $whereIs = $_SESSION['GOODPRACTICES_SELECTION'];
    $orderBy = $_SESSION['GOODPRACTICES_ORDER'];
    $erased = $_SESSION['ERASED_GOODPRACTICES'];
    $erasedPrograms = $_SESSION['ERASED_GOODPRACTICES_PROGRAMS'];
    $profile = Sanitize($_SESSION['LOGGED_USER']['profile']);
    $goodPracticesSelect = GoodPracticesSelect($whereIs, $orderBy, $erased, $erasedPrograms, $profile);
    $goodPractices = $goodPracticesSelect[0];
    $_SESSION['GOODPRACTICES_PARAMETERS']['SQL'] = $goodPracticesSelect[1];
    $_SESSION['GOODPRACTICES_PARAMETERS']['MARKERS'] = $goodPracticesSelect[2];
    $_SESSION['GOODPRACTICES_PARAMETERS']['ERASED_PROGRAMS'] = $erasedPrograms;
?>

<section>
    <h2>Tableau des bonnes pratiques</h2>
    <div class="table-container">
        <table>
            <div class="grid-container">
                <thead>
                    <tr>
                        <th class="programs-column">Programmes</th>
                        <th class="phase-column">Phase</th>
                        <th class="item-column">Item</th>
                        <th class="keywords-column">Mots clés</th>
                        <th class="actions-column">Actions</th>
                    </tr>
                </thead>
            </div>
            <div class="grid-container">
                <tbody class="scrollable-tbody">
                    <?php foreach ($goodPractices as $goodPractice) { ?>
                        <tr>
                            <td class="programs-column">
                                <?php 
                                    $restore = FALSE;
                                    if ($profile === 'admin' || $profile === 'superadmin') {
                                        foreach (explode(', ', Sanitize($goodPractice['program_names'])) as $program) {
                                            $isHidden = substr($program, -2);
                                            $programName = substr($program, 0, -2);
                                            if ($isHidden === ':1') {
                                                $restore = TRUE;
                                                $programNames .= '<span class="darkred">'.$programName.'</span>, ';
                                            } else {
                                                $programNames .= $programName.', ';
                                            }
                                        }
                                        $programNames = rtrim($programNames, ', ');
                                        echo $programNames;
                                        $programNames = '';
                                    } else {
                                        echo Sanitize($goodPractice['program_names']);
                                    }
                                ?>
                            </td>
                            <td class="phase-column"><?= Sanitize($goodPractice['phase_name']) ?></td>
                            <td class="item-column"><?= $goodPractice['goodpractice_is_hidden'] === 1 ? '<span class="darkred">'.Sanitize($goodPractice['item']).'</span>' : Sanitize($goodPractice['item']) ?></td>
                            <td class="keywords-column"><?= Sanitize($goodPractice['keywords']) ?></td>
                            <td class="actions-column">
                                <div class="action-btn-container">
                                    <button class="action-btn" onclick="openGoodpracticeForm('<?= Sanitize($goodPractice['goodpractice_id']) ?>', '<?= Sanitize($goodPractice['program_names']) ?>', <?= ($goodPractice['goodpractice_is_hidden'] === 1 || $restore === TRUE) ? 1 : 0 ?>, '<?= Sanitize($profile) ?>')">Modifier</button>
                                </div>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </div>
        </table>
    </div>

    <div class="form-popup" id="goodpracticeForm">
        <form action="manage_goodpractice.php" class="form-container" method="POST">
            <input type="hidden" id="goodpracticeId" name="goodpracticeId" value="">

            <h3>Modifier la bonne pratique</h3>
            <p>Vous pouvez : </p>
            <ul>
                <li>Dupliquer la bonne pratique pour un ou des programmes.</li>
                <?php if (isset($_SESSION['LOGGED_USER']) && ($_SESSION['LOGGED_USER']['profile'] === 'superadmin' || $_SESSION['LOGGED_USER']['profile'] === 'admin')) : ?>
                    <li>Effacer ou supprimer définitivement la bonne pratique pour un ou des programmes.</li>
                    <li>Si aucun programme n'est sélectionné, effacer ou supprimer définitivement la bonne pratique pour tous les programmes.</li>
                    <li>Restaurer une bonne pratique supprimée par un opérateur.</li>
                <?php elseif (isset($_SESSION['LOGGED_USER']) && $_SESSION['LOGGED_USER']['profile'] === 'operator') : ?>
                    <li>Effacer ou supprimer la bonne pratique pour un ou des programmes.</li>
                    <li>Si aucun programme n'est sélectionné, effacer ou supprimer la bonne pratique pour tous les programmes.</li>
                <?php endif; ?>
            </ul>
            <p></p>
            <div class="popup-programs-selection">
                <h4>Programme(s)</h4>
                <div class="popup-checkbox-area">
                    <?php foreach ($programs as $program): ?>
                        <div class="popup-checkbox-line">
                            <input class="popup-programs-checkbox" type="checkbox" id="<?= $program[0] ?>" name="programNames[]" value="<?= $program[0] ?>">
                            <label for="<?= $program[0] ?>"><?= $program[0] ?></label>
                        </div>
                    <?php endforeach; ?>   
                </div>         
            </div>
            <?php if (isset($_SESSION['LOGGED_USER']) && ($_SESSION['LOGGED_USER']['profile'] === 'superadmin' || $_SESSION['LOGGED_USER']['profile'] === 'admin')) : ?>
                <button type="submit" class="btn-warning" name="submit" value="delete">Supprimer définitivement</button>
            <?php elseif (isset($_SESSION['LOGGED_USER']) && $_SESSION['LOGGED_USER']['profile'] === 'operator') : ?>
                <button type="submit" class="btn-warning" name="submit" value="operator-delete">Supprimer</button>
            <?php endif; ?>
            <button type="submit" class="btn-warning" name="submit" value="erase">Effacer</button>
            <button type="submit" class="btn" name="submit" value="duplicate">Dupliquer</button>
            <button type="button" class="btn" onclick="closeGoodpracticeForm()">Annuler</button>
        </form>
    </div>

</section>

<script>
    // Function to open duplicate form
    function openGoodpracticeForm(goodpracticeId, programNamesString, restore, profile) {
        // Set the good practice ID
        document.getElementById("goodpracticeId").value = goodpracticeId;
        
        if (profile !== 'admin' && profile !== 'superadmin') {
            var programNamesArray = programNamesString.split(', ');
        } else {
            var programNamesArray = programNamesString.replace(/:0|:1/g, '').split(', ');
        }

        // Select all labels within the .popup-programs-selection
        const labels = document.querySelectorAll('.popup-programs-selection label');
        labels.forEach(label => {
            if (programNamesArray.includes(label.getAttribute('for'))) {
                // Change the label color to red
                label.style.color = 'red';
            } else {
                // Reset the label color if needed
                label.style.color = '#fff'; // or set it to the default color
            }        
        });

        if (restore) {
            // Check if the restore button already exists
            if (!document.getElementById('restore-button')) {
                // Select the buttons
                const eraseButton = document.querySelector('button[value="erase"]');
                const duplicateButton = document.querySelector('button[value="duplicate"]');

                // Create the restore button
                const restoreButton = document.createElement('button');
                restoreButton.type = 'submit';
                restoreButton.id = 'restore-button';
                restoreButton.className = 'btn';
                restoreButton.name = 'submit';
                restoreButton.value = 'restore';
                restoreButton.textContent = 'Restaurer';

                // Insert the restore button between the duplicate and cancel buttons
                eraseButton.parentNode.insertBefore(restoreButton, duplicateButton);
            }
        } else {
            // If restore is false, remove the restore button if it exists
            const existingRestoreButton = document.getElementById('restore-button');
            if (existingRestoreButton) {
                existingRestoreButton.parentNode.removeChild(existingRestoreButton);
            }
        }

        document.getElementById("goodpracticeForm").style.display = "block";
    }

    // Function to close delete form
    function closeGoodpracticeForm() {
        document.getElementById("goodpracticeForm").style.display = "none";
    }
</script>