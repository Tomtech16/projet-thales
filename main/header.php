<header>
    <a href="./index.php">
        <img src="./img/logo.svg" alt="Logo Thales Alenia Space">
    </a>
    <a href="./index.php">
        <h1>Projet Checklist</h1>
    </a>
    <nav>
        <ul>
            <?php if (isset($_SESSION['LOGGED_USER']) && ($_SESSION['LOGGED_USER']['profile'] === 'superadmin' || $_SESSION['LOGGED_USER']['profile'] === 'admin')) : ?>
                <li>
                    <a href="./index.php">Accueil</a>
                </li>
                <li>
                    <a href="./admin.php">Administration</a>
                </li>
            <?php endif; ?>
            <?php if (isset($_SESSION['LOGGED_USER'])) : ?>
                <li>
                    <a href="./logout.php">Se déconnecter</a>
                </li>
            <?php endif; ?>
        </ul>
    </nav>
</header>