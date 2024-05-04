<?php if (!isset($_SESSION['LOGGED_USER'])) : ?>
    <form action="submit_login.php" method="POST">
        <!-- si message d'erreur on l'affiche -->
        <?php 
            if (isset($_SESSION['LOGIN_ERROR_MESSAGE'])) {
                echo $_SESSION['LOGIN_ERROR_MESSAGE'];
                unset($_SESSION['LOGIN_ERROR_MESSAGE']);
            }
        ?>
        <p>Veuillez vous identifier</p>
        <p><label for="username">Nom d'utilisateur</label><input id="username" name="username" type="text" required autofocus/></p>
        <p><label for="password">Mot de passe</label><input id="password" name="password" type="password" required /></p>
        <p><button id="submit" name="submit" type="submit" value="submit">Connnexion</button></p>
    </form>
    <!-- Si utilisateur/trice bien connectée on affiche un message de succès -->
<?php else : ?>
        Connecté en tant que <?php echo $_SESSION['LOGGED_USER']['username']; ?>.
        Bonjour <?php echo $_SESSION['LOGGED_USER']['firstname']." ".$_SESSION['LOGGED_USER']['lastname']; ?>.
<?php endif; ?>