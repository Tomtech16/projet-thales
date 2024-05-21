<?php 
    session_start();   
    if (!isset($_SESSION['LOGGED_USER'])) { header('Location:index.php'); }
    require_once(__DIR__ . '/database_connect.php');
    require_once(__DIR__ . '/functions.php');
    require_once(__DIR__ . '/sql_functions.php');

    $whereIs = $_SESSION['GOODPRACTICES_SELECTION'];
    $orderBy = $_SESSION['GOODPRACTICES_ORDER'];
    $erased = $_SESSION['ERASED_GOODPRACTICES'];
    $erasedPrograms = $_SESSION['ERASED_GOODPRACTICES_PROGRAMS'];
    $goodPractices = GoodPracticesSelect($whereIs, $orderBy, $erased, $erasedPrograms);
    $_SESSION['GOODPRACTICES'] = $goodPractices;
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
                            <td class="programs-column"><?= Sanitize($goodPractice['program_names']) ?></td>
                            <td class="phase-column"><?= Sanitize($goodPractice['phase_name']) ?></td>
                            <td class="item-column"><?= Sanitize($goodPractice['item']) ?></td>
                            <td class="keywords-column"><?= Sanitize($goodPractice['keywords']) ?></td>
                            <td class="actions-column">
                                <div class="action-btn-container">
                                    <button class="action-btn" onclick="openGoodpracticeForm('<?= Sanitize($goodPractice['goodpractice_id']) ?>', '<?= Sanitize($goodPractice['program_names']) ?>')">Modifier</button>
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
                <?php else : ?>
                    <li>Effacer la bonne pratique pour un ou des programmes.</li>
                    <li>Si aucun programme n'est sélectionné, effacer la bonne pratique pour tous les programmes.</li>
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
            <?php endif; ?>
            <button type="submit" class="btn-warning" name="submit" value="erase">Effacer</button>
            <button type="submit" class="btn" name="submit" value="duplicate">Dupliquer</button>
            <button type="button" class="btn" onclick="closeGoodpracticeForm()">Annuler</button>
        </form>
    </div>

</section>

<script>
    // Function to open duplicate form
    function openGoodpracticeForm(goodpracticeId, programNamesString) {
        // Set the good practice ID
        document.getElementById("goodpracticeId").value = goodpracticeId;
        
        // Convert the string of program names into an array
        const programNamesArray = programNamesString.split(', ');

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
        
        document.getElementById("goodpracticeForm").style.display = "block";
        });
    }

    // Function to close delete form
    function closeGoodpracticeForm() {
        document.getElementById("goodpracticeForm").style.display = "none";
    }
</script>