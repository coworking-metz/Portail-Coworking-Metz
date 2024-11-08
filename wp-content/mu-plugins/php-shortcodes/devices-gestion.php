<?php

$mac = $_GET['mac'] ?? '';
$status = $_GET['status'] ?? '';
$adresseMac = formatMac($_GET['adresse-mac'] ?? '');
$user_id = get_current_user_id();
$erreur = devices_get_erreur($_GET['erreur'] ?? false);

if ($erreur || $adresseMac) {

    if (is_array($erreur)) {
        echo generateNotification($erreur);
        $erreur = $erreur['erreur']??false;
    }
    $formOpen = true;
}


if ($status == 'device-removed') {
    echo generateNotification([
        'titre' => 'Appareil retiré',
        'texte' => 'Cet appareil n\'est plus lié votre compte'
    ]);
}

if ($status == 'device-added') {
    echo generateNotification([
        'titre' => 'Appareil ajouté',
        'texte' => 'Cet appareil est désormais lié à votre compte'
    ]);
}

$devices = getDevices();
$okToDelete = count($devices) > 1;


$nbInvalides = 0;

?>
<p>Vous pouvez gérer ici la liste des appareils associés à votre compte Coworking.</p>

<div class="<?= $formOpen ? 'hide' : ''; ?> cta-devices">
    <p><strong>Associer au moins un appareil est nécéssaire</strong> pour que vos présences au coworking soient détectées. Si vous <strong>changez d'ordinateur</strong>, ou si <strong>vous utilisez parfois en alternance une tablette</strong> en plus de votre ordinateur principal, vous devez ajouter tous ces appareils à votre compte.</p>

    <p><button type="button" data-action="ajouter-appareil" class="woocommerce-Button button wp-element-button">Ajouter un appareil&hellip;</button></p>
</div>
<form method="post" action="/mon-compte/appareils/" class="<?= $formOpen ? '' : 'hide'; ?> woocommerce-EditAccountForm edit-account">
    <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
        <label for="adresse-mac"><strong>Ajouter un appareil à votre compte</strong></label>
        <input type="text" required class="woocommerce-Input woocommerce-Input--text input-text" name="adresse-mac" value="<?= htmlspecialchars($adresseMac); ?>" placeholder="Adresse MAC de l'appareil à ajouter">
        <?php if ($erreur) { ?><span class="form-error"><?= $erreur; ?></span><br><?php } else { ?>
            <span><em>Vous pouvez trouver votre adresse MAC <a href="https://www.studentinternet.eu/fr/docs/francais/depannage/comment-puis-je-trouver-ladresse-mac-de-mon-appareil/" target="_blank"><u> en suivant cette procédure</u></a>.</em></span>
        <?php } ?>
    </p>
    <p>
        <button type="submit" class="woocommerce-Button button wp-element-button">Ajouter cet appareil</button>
        <button type="button" data-action="annuler-ajouter-appareil" class="woocommerce-Button button" style="background-color:transparent;color:black;border:1px solid black">Annuler</button>
    </p>
</form>

<?php if ($devices) { ?>
    <table class="table table-left">
        <caption>Appareils enregistrés</caption>
        <tbody>
            <tr>
                <th>Adresse MAC</th>
                <th>Dernière utilisation</th>
                <th>Espace</th>
                <th></th>
            </tr>
            <?php foreach ($devices as $device) { ?>
                <tr data-mac="<?= $device['macAddress']; ?>">
                    <td>
                        <code><?= $device['macAddress']; ?></code>
                        <?php if (isMacAddressRandomized($device['macAddress'])) {
                            $nbInvalides++; ?> <span title="Cette adresse MAC est randomisée, elle est incompatible avec le système de détection des présences du coworking">⚠️</span>
                        <?php } ?>
                    </td>
                    <td>
                        <?= date_francais($device['heartbeat'] ?? '', true) ?: 'Jamais'; ?>
                    </td>
                    <td>
                        <?= ($device['heartbeat'] ?? false) ? ucfirst(str_replace('-', '', ($device['location'] ?: 'poulailler'))) : ''; ?>
                    </td>
                    <th>
                        <?php if ($okToDelete) { ?>
                            <a href="?effacer-adresse-mac=<?= urlencode($device['macAddress']); ?>" onclick="return confirm('Voulez-vous retirer cet appareil de votre compte ?')">🗑️</a>
                        <?php } else { ?>
                            <a href="#" onclick="alert('Vous ne pouvez pas retirer cet appareil, vous devez en avoir au moins un associé à votre compte.')">🗑️</a>
                        <?php } ?>
                    </th>
                </tr>
            <?php } ?>

        </tbody>
    </table>
    <?php if ($nbInvalides > 0) { ?>
        <!-- <b>Légende</b><br> -->
        <span>⚠️</span>: <?= devices_get_erreur('mac-random-legende'); ?>
    <?php } ?>
<?php } ?>