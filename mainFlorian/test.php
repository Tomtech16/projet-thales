<?php 
    $password = 'password';
    $hash = password_hash($password, PASSWORD_BCRYPT);
    echo $hash;
?>