<?php 
    session_start();
    if (!isset($_SESSION['LOGGED_USER']) || ($_SESSION['LOGGED_USER']['profile'] !== 'admin' && $_SESSION['LOGGED_USER']['profile'] !== 'superadmin')) { header('Location: logout.php'); exit(); }
    require_once(__DIR__ . '/database_connect.php');
    require_once(__DIR__ . '/sql_functions.php');
    require_once(__DIR__ . '/functions.php');

    $log = file('./log/log.txt');

    require_once(__DIR__ . '/header.php'); 
?>
<section>
    <h2>Interface de visualisation des logs</h2>
    <div class="log">
        <?php foreach ($log as $logLine) : ?>
            <p <?= (str_contains($logLine, 'Information') ? 'class="information"' : (str_contains($logLine, 'Warning') ? 'class="warning"' : (str_contains($logLine, 'Alarm') ? 'class="alarm"' : '' ))) ?>><?= $logLine ?></p>
        <?php endforeach; ?>
    </div>
</section>
<?php require_once(__DIR__ . '/footer.php'); ?>
