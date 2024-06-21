<?php


$status = $_GET['status'] ?? '';
$equipeInviter = $_GET['equipe-inviter'] ?? '';
$erreur = equipe_get_erreur($_GET['erreur'] ?? false);

$inviteSent = $status == 'invite-sent';
$saisie = true;
if ($inviteSent) {
    echo generateNotification([
        'titre' => 'Invitation envoyée',
        'texte' => 'Un email a été envoyé à l\'adresse <b>' . htmlspecialchars($equipeInviter) . '</b> pour inviter cette personne à rejoindre votre équipe'
    ]);
    $saisie = false;
}
if ($status == 'invite-error') {
    echo generateNotification([
        'type' => 'error',
        'titre' => 'Invitation non valide',
        'texte' => 'Le lien d\'invitation que vous avez suivi est invalide. Contactez le gestionnaire de votre équipe pour plus d\'informations'
    ]);
}
if ($status == 'invite-accepted') {
    echo generateNotification([
        'type' => 'success',
        'titre' => 'Invitation acceptée',
        'texte' => 'Vous avez rejoint une nouvelle équipe !'
    ]);
}
if ($status == 'member-removed') {
    echo generateNotification([
        'type' => 'success',
        'titre' => 'Membre retiré',
        'texte' => 'Le membre sélectionné a été retiré de votre équipe !',
        'temporaire' => true
    ]);
}
$uid = get_current_user_id();
$equipe = getMonEquipe($uid);
if ($equipe) {
    $admin = $equipe['role'] == 'admin';
?>
    <?php if ($admin) { ?>
        <p>Ajoutez des membres à votre équipe. Vous pourrez ensuite avoir accès à la gestion de leurs abonnements, tickets et présences</p>

        <form method="post" action="/mon-compte/equipe/" class="woocommerce-EditAccountForm edit-account">
            <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
                <label for="account_display_name">Invitez un membre à rejoindre votre équipe</label>
                <input type="email" required class="woocommerce-Input woocommerce-Input--text input-text" name="equipe-inviter" value="<?= $saisie ? htmlspecialchars($equipeInviter) : ''; ?>" placeholder="Adresse e-mail du coworker que vous voulez inviter">
                <?php if ($erreur) { ?><span style="color:red"><?= $erreur; ?></span><br><?php } ?>
                <span><em>La personne que vous invitez doît déjà avoir un compte coworker</em></span>
            </p>
            <p><button type="submit" class="woocommerce-Button button wp-element-button">Inviter par e-mail</button></p>
        </form>
    <?php } ?>
    <div>
        <table class="table table-left">
            <caption><?= $equipe['post_title']; ?></caption>
            <tbody>
                <tr>
                    <th colspan="2">Nom</th>
                    <th>Email</th>
                    <th>Rôle</th>
                    <?php if ($admin) { ?>
                        <th></th>
                        <th></th>
                    <?php } ?>
                </tr>
                <?php foreach ($equipe['membres'] as $membre) { ?>
                    <tr>
                        <td valign="middle"><img width="32" height="32" style="width:32px;height:32px;object-fit:cover" decoding="async" src="<?= $membre['photo']; ?>"></td>
                        <td valign="middle"><span><?= $membre['display_name']; ?></span></td>
                        <td valign="middle"><span><?= $membre['user_email']; ?></span></td>
                        <td valign="middle"><span><?= equipe_role($membre['equipe-role']); ?></span></td>
                        <?php if ($admin && $membre['equipe-role'] != 'waiting') { ?>
                            <th>
                                <?php if ($membre['ID'] != $uid) { ?>
                                    <a onclick="return confirm('Vous allez accéder à la boutique en étant connecté en tant que <?= addslashes($membre['display_name']); ?>.')" href="?se-connecter-en-tant-que=<?= $membre['ID']; ?>">Passer commande</a>
                                <?php } ?>
                            </th>
                            <th>
                                <?php if ($membre['ID'] != $uid) { ?>
                                    <a onclick="return confirm('Voulez-vous retirer ce membre de votre équipe ?')" href="?equipe-retirer=<?= $membre['ID']; ?>">Retirer</a>
                                <?php } ?>
                            </th>
                        <?php } else { ?>
                            <th>
                                <?php if ($membre['ID'] == $uid) { ?>
                                    <a onclick="return confirm('Voulez-vous quitter cette équipe ?')" href="?equipe-quitter=<?= $membre['ID']; ?>">Quitter</a>
                                <?php } ?>
                            </th>
                        <?php } ?>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

<?php } else { ?>
    <form method="post" action="/mon-compte/equipe/" class="woocommerce-EditAccountForm edit-account">
        <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
            <label for="account_display_name">Créez votre équipe</label>
            <input type="text" required class="woocommerce-Input woocommerce-Input--text input-text" name="equipe-creer" value="" placeholder="Nom de votre société, association, structure, etc.">
            <span><em>Créer une équipe vous permet de centraliser la gestion des abonnements et présences de vos collaborateur</em></span>
        </p>
        <p><button type="submit" class="woocommerce-Button button wp-element-button">Créer une équipe</button></p>
    </form>
<?php } ?>