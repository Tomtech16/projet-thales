<?php session_start(); ?>
<?php if (!isset($_SESSION['LOGGED_USER'])) { header('Location:index.php'); } ?>
<!DOCTYPE HTML>
<html>
	<head>
		<meta charset="utf-8" />
		<link rel="stylesheet" href="./style.css" />
		<title>Thales - Checklist</title>
	</head>
	<body>
        <?php require_once(__DIR__ . '/header.php'); ?>
        <section>
            <h2>Création d'un nouvel utilisateur</h2>
            <form class="create-user-form" action="submit_create_user.php" method="POST">
                <div class="gestion">
                    <div class="create-user">
                        <?php if (isset($_SESSION['CREATE_USER_MESSAGE']) && str_contains($_SESSION['CREATE_USER_MESSAGE'], 'Erreur')) : ?>
                            <p class="red-error"><?= $_SESSION['CREATE_USER_MESSAGE'] ?></p>
                        <?php else : ?>
                            <p><?= $_SESSION['CREATE_USER_MESSAGE'] ?></p>
                        <?php 
                            unset($_SESSION['CREATE_USER_MESSAGE']); 
                        endif; ?>
                        <h3>Veuillez remplir les informations du nouvel utilisateur</h3>
                        <div class="create-user-input-area">
                            <div class="create-user-input-line">
                                <label for="username">Nom d'utilisateur : </label><input id="username" name="username" type="text" placeholder="Saisir le nom d'utilisateur" required autofocus/>
                            </div>
                            <div class="create-user-input-line">
                                <label for="firstname">Prénom : </label><input id="firstname" name="firstname" type="text" placeholder="Saisir le prénom" required />
                            </div>
                            <div class="create-user-input-line">
                                <label for="lastname">Nom : </label><input id="lastname" name="lastname" type="text" placeholder="Saisir le nom" required />
                            </div>
                            <?php if ($_SESSION['LOGGED_USER']['profile'] === 'superadmin') : ?>
                                <div class="create-user-input-line">
                                    <label for="profile">Profil : </label>
                                    <select id='profile' name='profile'>
                                        <option value='operator' selected>Opérateur</option>
                                        <option value='admin'>Administrateur</option>
                                    </select>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <h4>Règles de configuration du mot de passe : </h4>
                        <ol>
                            <li>Ne doit pas contenir d'accent.</li>
                            <li>Ne doit pas contenir le nom d'utilisateur.</li>
                            <?php
                                require_once(__DIR__ . '/database_connect.php');
                                require_once(__DIR__ . '/sql_functions.php');
                                $parameters = PasswordSelect();
                                echo "<li>Le mot de passe doit contenir au moins ".$parameters['n']." caractère(s) numérique(s) (entre “0” et “9”).</li>\n";
                                echo "<li>Le mot de passe doit contenir au moins ".$parameters['p']." caractère(s) alphabétique(s) en minuscule (entre « a » et « z »).</li>\n";
                                echo "<li>Le mot de passe doit contenir au moins ".$parameters['q']." caractère(s) alphabétique(s) en majuscule (entre « A » et « Z »).</li>\n";
                                echo "<li>Le mot de passe doit contenir au moins ".$parameters['r']." caractère(s) spécial(aux) parmi ([!\"#$%&'*+,-./;<=>?@\^_`|}~]),{.</li>\n";
                            ?>
                        </ol>
                        <div class="create-user-input-area">
                            <div class="create-user-input-line">
                                <label for="password">Mot de passe : </label><input id="password" name="password" type="password" placeholder="Saisir le mot de passe" required />
                            </div>
                            <div class="create-user-input-line">
                                <label for="password2">Mot de passe : </label><input id="password2" name="password2" type="password" placeholder="Ressaisir le mot de passe" required />
                            </div>
                        </div>
                        <div class="create-user-button">
                            <button id="submit" name="submit" type="submit" value="submit">Créer le nouvel utilisateur</button>
                        </div>
                    </div>
                </div>
            </form>
        </section>
    </body>
</html>
