<?php 
    session_start();   
    if (!isset($_SESSION['LOGGED_USER'])) { header('Location:index.php'); }
    require_once(__DIR__ . '/database_connect.php');
    require_once(__DIR__ . '/functions.php');
    require_once(__DIR__ . '/sql_functions.php');

    $usersSelectionOrder = $_SESSION['USERS_SELECTION_ORDER'];
    $users = UsersSelect($usersSelectionOrder);
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
                <tbody class="scrollable-tbody">
                    <?php foreach ($users as $user) { ?>
                        <tr>
                            <td class="username-column"><?= Sanitize($user['username']) ?></td>
                            <td class="firstname-column"><?= Sanitize($user['firstname']) ?></td>
                            <td class="lastname-column"><?= Sanitize($user['lastname']) ?></td>
                            <td class="profile-column"><?= Sanitize($user['profile']) ?></td>
                            <?php 
                                if (UserIsBlocked(Sanitize($user['attempts']))) {
                                    echo '<td class="attempts-column">Compte bloqué</td>';
                                } else {
                                    echo '<td class="attempts-column">'.Sanitize($user['attempts']).'</td>';
                                }
                            ?>
                            <td class="actions-column">
                                <div class="action-btn-container">
                                    <button class="action-btn" onclick="openUserForm(<?= Sanitize($user['user_id']) ?>)">Supprimer</button>
                                </div>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </div>
        </table>
    </div>

    <?php if (UserIsBlocked(Sanitize($user['attempts']))) : ?>
        <button class="action-button" onclick="openUserResetPasswordForm(<?= Sanitize($user['user_id']) ?>)">Changer le mot de passe</button>
    <?php endif; ?>

    <div class="form-popup" id="userForm">
        <form action="delete_user.php" class="form-container" method="POST">
            <h3>Supprimer l'utilisateur</h3>
            <p>Êtes-vous sûr de vouloir supprimer cet utilisateur ?</p>
            <input type="hidden" id="deleteUserId" name="deleteUserId" value="">
            <button type="submit" class="btn-warning" >Confirmer</button>
            <button type="button" class="btn" onclick="closeUserDeleteForm()">Annuler</button>
        </form>
    </div>

    <div class="form-popup" id="deleteUserForm">
        <form action="delete_user.php" class="form-container" method="POST">
            <h3>Supprimer l'utilisateur</h3>
            <p>Êtes-vous sûr de vouloir supprimer cet utilisateur ?</p>
            <input type="hidden" id="deleteUserId" name="deleteUserId" value="">
            <button type="submit" class="btn-warning" >Confirmer</button>
            <button type="button" class="btn" onclick="closeUserDeleteForm()">Annuler</button>
        </form>
    </div>
    
    <?php if (UserIsBlocked(Sanitize($user['attempts']))) : ?>
        <div class="form-popup" id="resetUSerPasswordForm">
            <form action="reset_user_password.php" class="form-container" method="POST">
                <h3>Redéfinir le mot de passe</h3>
                <input type="hidden" id="resetUserPasswordId" name="resetUserPasswordId" value="">
                <button type="submit" class="btn">Dupliquer</button>
                <button type="button" class="btn" onclick="closeUserResetPasswordForm()">Annuler</button>
            </form>
        </div>

        <script>
            function openResetUserPasswordForm(userId) {
                document.getElementById("resetUserPasswordId").value = userId;
                document.getElementById("resetUserPasswordForm").style.display = "block";
                document.getElementById("deleteUserForm").style.display = "none";
            }

            function closeResetUserPasswordForm() {
                document.getElementById("resetUserPasswordForm").style.display = "none";
            }
        </script>
    <?php endif; ?>


</section>

<script>
    function openUserDeleteForm(userId) {
        document.getElementById("deleteUserId").value = userId;
        document.getElementById("deleteUserForm").style.display = "block";
        document.getElementById("resetUserPasswordForm").style.display = "none";
    }

    function closeUserDeleteForm() {
        document.getElementById("deleteUserForm").style.display = "none";
    }
</script>