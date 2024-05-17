<header>
    <img src="./img/logo.svg" alt="Logo Thales Alenia Space">
    <h1>Projet Checklist</h1>

    <nav>
        <ul>
            <li>
                <a href="./index.php">Accueil</a>
            </li>
            <?php if (isset($_SESSION['LOGGED_USER'])) : ?>
                <li>
                    <a href="./logout.php">Se déconnecter</a>
                </li>
            <?php endif; ?>
            </li>
        </ul>
    </nav>
</header>