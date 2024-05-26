<?php
    $path = $_SERVER['PHP_SELF'];
    $file = basename($path);
?>
<header>
    <a href="https://www.thalesaleniaspace.com/fr">
        <img src="./img/logo.svg" alt="Logo Thales Alenia Space">
    </a>
    <a href="./index.php">
        <h1>Projet Checklist</h1>
    </a>
    <nav>
        <ul>
            <li>
                <a href="./index.php" class="<?= ($file === 'index.php') ? 'current-' : '' ?>page">Accueil</a>
            </li>
            <?php if (isset($_SESSION['LOGGED_USER'])) : ?>
                <li>
                    <a href="./create_goodpractice.php" class="<?= ($file === 'create_goodpractice.php') ? 'current-' : '' ?>page">Créer un bonne pratique</a>
                </li>
            <?php endif; ?>
            <?php if (isset($_SESSION['LOGGED_USER']) && ($_SESSION['LOGGED_USER']['profile'] === 'superadmin' || $_SESSION['LOGGED_USER']['profile'] === 'admin')) : ?>
                <li>
                    <a href="./admin.php" class="<?= ($file === 'admin.php') ? 'current-' : '' ?>page">Administration</a>
                </li>
                <li>
                    <a href="./create_user.php" class="<?= ($file === 'create_user.php') ? 'current-' : '' ?>page">Créer un utilisateur</a>
                </li>
            <?php endif; ?>
            <?php if (isset($_SESSION['LOGGED_USER'])) : ?>
                <li>
                    <a href="./logout.php" class="page">Se déconnecter</a>
                </li>
            <?php elseif ($file === 'index.php') : ?>
                <li>
                    <a href="./login.php" class="page">Page de connexion</a>
                </li>
            <?php elseif ($file === 'login.php') : ?>
                <li>
                    <a href="./login.php" class="current-page">Page de connexion</a>
                </li>
            <?php endif; ?>
        </ul>
    </nav>
    <a href="https://univ-cotedazur.fr/">
        <img id ="uca" src="./img/uca.png" alt="Logo Université Côte-d'Azur">
    </a>
</header>