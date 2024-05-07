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
            
            /*
                Jouer avec les && et les || pour créer une architecture de filtres respectant les exigences.

                    ==> Exigences à revoir !!!
                

                Exemple simple :

                    if (StrContainsAnySubstring($goodPractice['program'], $_SESSION['PROGRAM_FILTER']) 
                        && StrContainsAnySubstring($goodPractice['phase'], $_SESSION['PHASE_FILTER']) 
                        && StrContainsAnySubstring($goodPractice['keywords'], $_SESSION['KEYWORDS_FILTER']))
                    {
            */

            echo "<tbody>";
            echo "<tr>";
            echo "<td>".$goodPractice['program']."</td>";
            echo "<td>".$goodPractice['phase']."</td>";
            echo "<td>".$goodPractice['item']."</td>";
            echo "<td>".$goodPractice['keywords']."</td>";
            echo "</tr>";
            echo "</tbody>";
        }
    ?>
</table>
