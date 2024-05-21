<?php session_start(); ?>
<?php if (!isset($_SESSION['LOGGED_USER'])) { header('Location:index.php'); } ?>
<form action="submit_create_user.php" method="POST">
    <!-- Affiche un message de succès ou d'erreur -->
    <?php 
        if (isset($_SESSION['CREATE_USER_MESSAGE'])) {
            echo "<p>".$_SESSION['CREATE_USER_MESSAGE']."</p>";
            unset($_SESSION['CREATE_USER_MESSAGE']);
        }
    ?>
    <p>Veuillez saisir les identifiants du nouvel opérateur.</p>
    <p><label for="username">Nom d'utilisateur</label><input id="username" name="username" type="text" required autofocus/></p>
    <p><label for="firstname">Prénom</label><input id="firstname" name="firstname" type="text" required /></p>
    <p><label for="lastname">Nom</label><input id="lastname" name="lastname" type="text" required /></p>
    <p><label for="password">Mot de passe</label><input id="password" name="password" type="password" required /></p>
    <p>Règles de configuration du mot de passe :</p>
    <ol>
        <li>Ne doit pas contenir d'accent.</li>
        <li>Ne doit pas contenir le nom d'utilisateur.</li>
        <?php
            require_once(__DIR__ . '/database_connect.php');
            require_once(__DIR__ . '/sql_functions.php');
            $parameters = PasswordSelect();
            echo "<li>Le mot de passe doit contenir au moins ".$parameters['n']." caractère(s) numérique(s) (entre “0” et “9”).</li>\n";
            echo "<li>Le mot de passe doit contenir au moins ".$parameters['p']." caractère(s) alphabétique(s) en minuscule (entre « a » et « z »).</li>\n";
            echo "<li>Le mot de passe doit contenir au moins ".$parameters['q']." caractère(s) alphabétique(s) en majuscule (entre « A » et « Z »).</li>\n";
            echo "<li>Le mot de passe doit contenir au moins ".$parameters['r']." caractère(s) spécial(aux) parmi ([!\"#$%&'*+,-./;<=>?@\^_`|}~]),{.</li>\n";
        ?>
    </ol>
    <p><label for="password2">Retapez le mot de passe</label><input id="password2" name="password2" type="password" required /></p>
    <p><button id="submit" name="submit" type="submit" value="submit">Créer le nouvel utilisateur</button></p>
</form>
