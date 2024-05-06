<?php
session_start(); // Démarrez la session si ce n'est pas déjà fait

// Détruire la session
session_unset();
session_destroy();

session_start();
$_SESSION['LOGIN_MESSAGE'] = 'Vous êtes déconnecté(e)';

// Rediriger l'utilisateur vers la page d'accueil
header('Location:index.php');
?>