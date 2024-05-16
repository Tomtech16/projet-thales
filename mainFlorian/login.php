<?php if (!isset($_SESSION['LOGGED_USER'])) : ?>
    <section class="login">
        <form action="submit_login.php" method="POST">
            <!-- Si message d'erreur on l'affiche -->
            <?php 
                if (isset($_SESSION['LOGIN_MESSAGE'])) {
                    echo "<p>".$_SESSION['LOGIN_MESSAGE']."</p>";
                    unset($_SESSION['LOGIN_MESSAGE']);
                }
            ?>
            <h2>Veuillez vous identifier</h2><p>
            <p><label for="username">Nom d'utilisateur : </label><input id="username" name="username" type="text" placeholder="Saisissez votre nom d'utilisateur" required autofocus/></p>
            <p><label for="password">Mot de passe : </label><input id="password" name="password" type="password" placeholder="Saisissez votre mot de passe" required /></p>
            <p><button id="submit" name="submit" type="submit" value="submit">Connnexion</button></p>
        </form>
    </section>
<!-- Si utilisateur/trice bien connectée on affiche un message de succès -->
<?php else : ?>
    <section>
        <p>Connecté en tant que <?php echo $_SESSION['LOGGED_USER']['username']; ?>.</p>
        <p>Bonjour <?php echo $_SESSION['LOGGED_USER']['firstname']." ".$_SESSION['LOGGED_USER']['lastname']; ?>.</p>
    </section>
<?php endif; ?>

