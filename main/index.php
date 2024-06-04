<?php 
    session_start();
    if (isset($_SESSION['LOGOUT_TENTATIVE'])) { unset($_SESSION['LOGOUT_TENTATIVE']); }

    require_once(__DIR__ . '/header.php'); 
?>

<?php if (!isset($_SESSION['LOGGED_USER'])) : ?>
    <section class="welcome">
        <h2>Bienvenue sur le projet Thales Checklist.</h2>
        <ul>
            <p>Connectez-vous pour : </p>
            <li>gérer les bonnes pratiques</li>
            <li>créer des checklists</li>
        </ul>
        <form action="login.php" method="POST">
            <button type="submit" name="submit" class="btn" >Se connecter</button>
        </form>
    </section>
<?php endif; ?>

<?php if (isset($_SESSION['LOGGED_USER'])) : ?>
    <?php 
        if ($_SESSION['LOGGED_USER']['profile'] === 'operator' || $_SESSION['LOGGED_USER']['profile'] === 'admin' || $_SESSION['LOGGED_USER']['profile'] === 'superadmin')
        { 
            require_once(__DIR__ . '/goodpractices_selection.php');
            require_once(__DIR__ . '/goodpractices_print.php');
        } else {
            header('Location:logout.php');
            exit();
        }
    ?>
<?php endif; ?>

<?php require_once(__DIR__ . '/footer.php'); ?>
