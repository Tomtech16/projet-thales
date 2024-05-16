<?php session_start(); ?>    

<?php
    require_once(__DIR__ . '/database_connect.php');
    // require_once(__DIR__ . '/variables.php');
    require_once(__DIR__ . '/functions.php');
    require_once(__DIR__ . '/sql_functions.php');

    if (isset($_SESSION['DELETED_GOODPRACTICES_IDS'])) {
        $deletedGoodpractices = $_SESSION['DELETED_GOODPRACTICES_IDS'];
    }
    $goodPractices = GoodPracticesSelect($whereIs, $orderBy, $deletedGoodpractices);
?>

<section>
    <h2>Bonnes pratiques</h2>
    
    <table>
        <thead>
        <tr>
            <th>Programme</th>
            <th>Phase</th>
            <th>Item</th>
            <th>Mots clés</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($goodPractices as $goodPractice) { ?>
            <tr>
                <td><?= $goodPractice['program_names'] ?></td>
                <td><?= $goodPractice['phase_name'] ?></td>
                <td><?= $goodPractice['item'] ?></td>
                <td><?= $goodPractice['keywords'] ?></td>
                <td>
                <div class="action-btn-container">
                    <button class="action-btn" onclick="openDuplicateForm('<?= htmlspecialchars($goodPractice['item']) ?>', '<?= htmlspecialchars($goodPractice['keywords']) ?>')">Dupliquer</button>
                    <button class="action-btn" onclick="openDeleteForm(<?= $goodPractice['goodpractice_id'] ?>)">Supprimer</button>
                </div>
                </td>
            </tr>
        <?php } ?>
        </tbody>
    </table>

    <div class="form-popup" id="duplicateForm">
        <form action="duplicate_goodpractice.php" class="form-container" method="POST">
            <h3>Dupliquer la bonne pratique</h3>
            <label for="duplicateProgram"><strong>Programme :</strong></label>
            <input type="text" id="duplicateProgram" placeholder="Entrez le programme" name="duplicateProgram" required />
            <label for="duplicatePhase"><strong>Phase :</strong></label>
            <input type="text" id="duplicatePhase" placeholder="Entrez la phase" name="duplicatePhase" required />
            <input type="hidden" id="duplicateItem" name="item" value="">
            <input type="hidden" id="duplicateKeywords" name="keywords" value="">
            <button type="submit" class="btn">Dupliquer</button>
            <button type="button" class="btn" onclick="closeDuplicateForm()">Annuler</button>
        </form>
    </div>

    <div class="form-popup" id="deleteForm">
        <form action="delete_goodpractice.php" class="form-container" method="POST">
            <h3>Supprimer la bonne pratique</h3>
            <p>Êtes-vous sûr de vouloir supprimer cette bonne pratique ?</p>
            <input type="hidden" id="deleteGoodpracticeId" name="goodpracticeId" value="">
            <button type="submit" class="btn-warning" >Confirmer</button>
            <button type="button" class="btn" onclick="closeDeleteForm()">Annuler</button>
        </form>
    </div>

</section>

<script>
    // Function to open duplicate form
    function openDuplicateForm(item, keywords) {
        document.getElementById("duplicateItem").value = item;
        document.getElementById("duplicateKeywords").value = keywords;
        document.getElementById("duplicateForm").style.display = "block";
        document.getElementById("deleteForm").style.display = "none"; // Fermer le popup de suppression
    }

    // Function to open delete form
    function openDeleteForm(goodpracticeId) {
        document.getElementById("deleteGoodpracticeId").value = goodpracticeId;
        document.getElementById("deleteForm").style.display = "block";
        document.getElementById("duplicateForm").style.display = "none"; // Fermer le popup de duplication
    }

    // Function to close duplicate form
    function closeDuplicateForm() {
        document.getElementById("duplicateForm").style.display = "none";
    }

    // Function to close delete form
    function closeDeleteForm() {
        document.getElementById("deleteForm").style.display = "none";
    }
</script>