<?php 
    session_start();   
    $path = $_SERVER['PHP_SELF'];
    $file = basename($path);
    require_once(__DIR__ . '/functions.php');
    if (!isset($_SESSION['LOGGED_USER']) || ($_SESSION['LOGGED_USER']['profile'] !== 'admin' && $_SESSION['LOGGED_USER']['profile'] !== 'superadmin')) { Logger(Sanitize($_SESSION['LOGGED_USER']['username']), Sanitize($_SESSION['LOGGED_USER']['profile']), 2, 'Unauthorized access attempt to '.$file); header('Location:logout.php'); exit(); }

    require_once(__DIR__ . '/config/database_connect.php');
    require_once(__DIR__ . '/sql_functions.php');

    $passwordParameters = PasswordSelect();
    $n = Sanitize($passwordParameters['n']);
    $p = Sanitize($passwordParameters['p']);
    $q = Sanitize($passwordParameters['q']);
    $r = Sanitize($passwordParameters['r']);
    $usersSelectionOrder = $_SESSION['USERS_SELECTION_ORDER'];
    $profile = Sanitize($_SESSION['LOGGED_USER']['profile']);
    $users = UsersSelect($usersSelectionOrder, $profile);
?>

<section>
    <h2>Tableau des utilisateurs</h2>
    <div class="table-container">
        <table>
            <div class="grid-container">
                <thead>
                    <tr>
                        <th class="username-column">Nom d'utilisateur</th>
                        <th class="firstname-column">Prénom</th>
                        <th class="lastname-column">Nom</th>
                        <th class="profile-column">Profil</th>
                        <th class="attempts-column">Tentatives de connexion</th>
                        <th class="actions-column">Actions</th>
                    </tr>
                </thead>
            </div>
            <div class="grid-container">
                <tbody class="scrollable-tbody" id="users-tbody">
                    <?php if ($_SESSION['LOGGED_USER']['profile'] === 'admin' || $_SESSION['LOGGED_USER']['profile'] === 'superadmin') : ?>
                        <tr id="self-admin">
                            <td class="username-column"><?= Sanitize($_SESSION['LOGGED_USER']['username']) ?></td>
                            <td class="firstname-column"><?= Sanitize($_SESSION['LOGGED_USER']['firstname']) ?></td>
                            <td class="lastname-column"><?= Sanitize($_SESSION['LOGGED_USER']['lastname']) ?></td>
                            <td class="profile-column"><?= Sanitize($_SESSION['LOGGED_USER']['profile']) ?></td>
                            <td class="attempts-column"></td>
                            <td class="actions-column"></td>
                        </tr>
                    <?php endif; ?>
                    <?php foreach ($users as $user) { ?>
                        <tr <?= (UserIsBlocked(Sanitize($user['attempts']))) ? 'class="blocked"' : '' ?>>
                            <td class="username-column"><?= Sanitize($user['username']) ?></td>
                            <td class="firstname-column"><?= Sanitize($user['firstname']) ?></td>
                            <td class="lastname-column"><?= Sanitize($user['lastname']) ?></td>
                            <td class="profile-column"><?= Sanitize($user['profile']) ?></td>
                            <?php if (UserIsBlocked(Sanitize($user['attempts']))) : ?>
                                <td class="attempts-column">Compte bloqué</td>
                            <?php else : ?>
                                <td class="attempts-column"><?= Sanitize($user['attempts'])?></td>
                            <?php endif; ?>
                            <td class="actions-column">
                                <?php if ($user['profile'] !== 'superadmin') : ?>
                                        <div class="action-btn-container">
                                            <button class="action-btn" onclick="openUserForm(<?= Sanitize($user['user_id']) ?>, <?= (UserIsBlocked(Sanitize($user['attempts']))) ? 1 : 0 ?>, <?= $n ?>, <?= $p ?>, <?= $q ?>, <?= $r ?>)">Gérer</button>
                                        </div>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </div>
        </table>
    </div>

    <div class="form-popup" id="userForm">
        <form action="manage_user.php" class="form-container" method="POST">
            <h3>Administration de l'utilisateur</h3>
            <p>Vous pouvez : </p>
            <ul>
                <li>Supprimer l'utilisateur.</li>
                <li>Réinitialiser le mot de passe d'un utilisateur bloqué.</li>
            </ul>
            <input type="hidden" id="userId" name="userId" value="">
            <button type="submit" class="btn-warning" name="submit" value="delete-user">Supprimer</button>
            <button id="cancel" type="button" class="btn" onclick="closeUserForm()">Annuler</button>
        </form>
    </div>
</section>

<script>

function openUserForm(userId, userIsBlocked, n, p, q, r) {
    document.getElementById("userId").value = userId;

    if (userIsBlocked) {
        if (!document.getElementById('reset-password-button')) {
            const deleteButton = document.querySelector('button[value="delete-user"]');
            const cancelButton = document.querySelector('button[id="cancel"]');

            const resetButton = document.createElement('button');
            resetButton.type = 'submit';
            resetButton.id = 'reset-password-button';
            resetButton.className = 'btn';
            resetButton.name = 'submit';
            resetButton.value = 'reset-password';
            resetButton.textContent = 'Réinitialiser le mot de passe';

            deleteButton.parentNode.insertBefore(resetButton, cancelButton);
        }

        if (!document.getElementById('password-rules')) {
            const form = document.querySelector("#userForm form");
            const deleteButton = document.querySelector('button[value="delete-user"]');
            const passwordRulesDiv = document.createElement('div');
            passwordRulesDiv.id = 'password-rules';

            let passwordRulesHTML = `
                <div class="gestion">
                        <div class="reset-user-password">
                            <h4>Règles de configuration du mot de passe : </h4>
                            <ol>
                                <li>Ne doit pas contenir d'accent.</li>
                                <li>Ne doit pas contenir le nom d'utilisateur.</li>
            `;
            if (n > 0) {
                passwordRulesHTML += `<li>Doit contenir au moins ${n} chiffre${n > 1 ? 's' : ''}.</li>`;
            }
            if (p > 0) {
                passwordRulesHTML += `<li>Doit contenir au moins ${p} minuscule${p > 1 ? 's' : ''}.</li>`;
            }
            if (q > 0) {
                passwordRulesHTML += `<li>Doit contenir au moins ${q} majuscule${q > 1 ? 's' : ''}.</li>`;
            }
            if (r > 0) {
                passwordRulesHTML += `<li>Doit contenir au moins ${r} caractère${r > 1 ? 's' : ''} spécia${r > 1 ? 'ux' : 'l'}.</li>`;
            }
            passwordRulesHTML += `
                        </ol>
                        <h4>Renseignez le nouveau mot de passe.</h4>
                        <div class="reset-user-password-input-area">
                            <div class="reset-user-password-input-line">
                                <label for="password">Mot de passe : </label>
                                <input id="password" name="password" type="password" placeholder="Saisir le mot de passe" required autofocus/>
                            </div>
                            <div class="reset-user-password-input-line">
                                <label for="password2">Mot de passe : </label>
                                <input id="password2" name="password2" type="password" placeholder="Ressaisir le mot de passe" required/>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            passwordRulesDiv.innerHTML = passwordRulesHTML;
            form.insertBefore(passwordRulesDiv, deleteButton);
        }
    } else {
        const existingResetButton = document.getElementById('reset-password-button');
        if (existingResetButton) {
            existingResetButton.parentNode.removeChild(existingResetButton);
        }

        const passwordRulesDiv = document.getElementById('password-rules');
        if (passwordRulesDiv) {
            passwordRulesDiv.parentNode.removeChild(passwordRulesDiv);
        }
    }

    document.getElementById("userForm").style.display = "block";
}



    function closeUserForm() {
        document.getElementById("userForm").style.display = "none";
    }
</script>