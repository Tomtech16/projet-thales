<?php session_start(); ?>
<form action="goodpractices_submit_selection.php" method="POST">
    <p></p>
    <p><label for="orderProgram">Ordre </label><input id="orderProgram" name="orderProgram" type="text" required autofocus/></p>
    <p><label for="firstname">Prénom</label><input id="firstname" name="firstname" type="text" required /></p>
    <p><label for="lastname">Nom</label><input id="lastname" name="lastname" type="text" required /></p>
    <p><label for="password">Mot de passe</label><input id="password" name="password" type="password" required /></p>
    <p><label for="password2">Retapez le mot de passe</label><input id="password2" name="password2" type="password" required /></p>
    <p><button id="submit" name="submit" type="submit" value="submit">Appliquer les filtres</button></p>
</form>