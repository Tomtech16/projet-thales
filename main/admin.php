<?php 
    session_start();
    $path = $_SERVER['PHP_SELF'];
    $file = basename($path);
    require_once(__DIR__ . '/functions.php');
    if (!isset($_SESSION['LOGGED_USER']) || ($_SESSION['LOGGED_USER']['profile'] !== 'admin' && $_SESSION['LOGGED_USER']['profile'] !== 'superadmin')) { Logger(Sanitize($_SESSION['LOGGED_USER']['username']), Sanitize($_SESSION['LOGGED_USER']['profile']), 2, 'Unauthorized access attempt to '.$file); header('Location:logout.php'); exit(); }

    require_once(__DIR__ . '/header.php');
    require_once(__DIR__ . '/users_gestion.php');
    require_once(__DIR__ . '/users_print.php');
    require_once(__DIR__ . '/footer.php');
?>
<?php if (isset($_SESSION['RESET_USER_PASSWORD_MESSAGE'])) : ?>
    <script>alert('<?= Sanitize($_SESSION['RESET_USER_PASSWORD_MESSAGE']) ?>')</script>
    <?php unset($_SESSION['RESET_USER_PASSWORD_MESSAGE']); ?>
<?php endif; ?>