<?php

    session_start();
    require_once(__DIR__ . '/database_connect.php');
    // require_once(__DIR__ . '/variables.php');
    require_once(__DIR__ . '/functions.php');
    require_once(__DIR__ . '/sql_functions.php');

    $goodPractices = GoodPracticesSelect();
?>    

<h2>Bonnes pratiques</h2>

<table>
    <thead>
        <tr>
            <th>Programme</th>
            <th>Phase</th>
            <th>Item</th>
            <th>Mots clés</th>
        </tr>
    </thead>


    <?php
        foreach ($goodPractices as $goodPractice) {
            
            echo "<tbody>";
            echo "<tr>";
            echo "<td>".$goodPractice['program_names']."</td>";
            echo "<td>".$goodPractice['phase_name']."</td>";
            echo "<td>".$goodPractice['item']."</td>";
            echo "<td>".$goodPractice['keywords']."</td>";
            echo "</tr>";
            echo "</tbody>";
        }
    ?>
</table>
