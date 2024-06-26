<?php

// error_reporting(E_ALL);
// ini_set('display_errors', 'On');

$user = wp_get_current_user();
if (!$user) exit;
$uid = $user->ID;

$changer = isset($_GET['changer']);
$photo = false;
if (isset($_GET['modifier'])) {
    $polaroid = polaroid_get($uid, false);
    if ($polaroid['photo']) {
        copy($polaroid['photo'], polaroid_tmpphoto());
        $photo = true;
    } else {
        $changer = true;
    }
}

if (!$photo && !empty($_FILES['photo'])) {
    move_uploaded_file($_FILES['photo']['tmp_name'], polaroid_tmpphoto());
    $photo = true;
}

if (isset($_POST['valider-polaroid'])) {
    $polaroid = $_POST['polaroid'] ?? false;

    $key = 'user_' . $uid;
    update_field('polaroid_nom', $polaroid['nom'], $key);
    update_field('polaroid_description', $polaroid['description'], $key);
    update_field('polaroid_complement', $polaroid['complement'], $key);

    $aid = insert_attachment_from_file(polaroid_tmpphoto(), ['post_title' => 'Photo ' . $uid . ' ' . $user->display_name]);

    update_field('votre_photo', $aid, $key);

    @unlink(polaroid_tmpphoto());
    @unlink(polaroid_gen_file());
    update_field('url_image_trombinoscope', '', $key);

    wp_redirect('/mon-compte/polaroid/');
    exit;
}
?>


<?php if ($photo) {

    $content = pathTourl(polaroid_tmpphoto());
    $polaroid = ['nom' => get_field('polaroid_nom', 'user_' . $uid), 'description' => get_field('polaroid_description', 'user_' . $uid), 'complement' => get_field('polaroid_complement', 'user_' . $uid)];

    if (!$polaroid['nom']) {
        $polaroid['nom'] = $user->display_name;
    }
?>
    <h3>Aperçu de votre polaroïd</h3>
    <div class="polaroid__generateur">
        <div>
            <div class="polaroid__apercu">
                <img src="<?= $content; ?>">
            </div>

            <p><a href="/mon-compte/polaroid/?changer">Changer de photo</a></p>

        </div>
        <div>
            <form method="post" action="/mon-compte/polaroid/" id="saisie-polaroid" class="woocommerce-EditAccountForm">
                <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
                    <label for="polaroid_nom">Nom affiché</label>
                    <input type="text" class="woocommerce-Input woocommerce-Input--text input-text" required pattern=".*\S+.*" name="polaroid[nom]" id="polaroid_nom" value="<?= htmlspecialchars($polaroid['nom']); ?>" maxlength="40">
                </p>
                <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
                    <label for="polaroid_description">Description</label>
                    <input type="text" class="woocommerce-Input woocommerce-Input--text input-text" required pattern=".*\S+.*" name="polaroid[description]" id="polaroid_description" value="<?= htmlspecialchars($polaroid['description']); ?>" maxlength="40">
                    <small>Pour décrire votre métier, votre intitulé de poste, etc.</small>
                </p>
                <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
                    <label for="polaroid_complement">Ligne complémentaire (facultatif)</label>
                    <input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="polaroid[complement]" id="polaroid_complement" value="<?= htmlspecialchars($polaroid['complement']); ?>" maxlength="40">
                    <small>Si vous avez encore des choses à dire</small>
                </p>

                <button name="valider-polaroid" class="button">Valider ce polaroïd</button>

            </form>
        </div>
    </div>
<?php } else { ?>

    <?php if (!$changer && polaroid_existe()) { ?>
        <div class="polaroid__definitif"><img src="<?= polaroid_url() . '?' . rand(); ?>"></div>
        <a class="button" href="?modifier">Modifier</a>
    <?php } else { ?>
        <p>Utilisez l'outil ci-dessous pour choisir la photo qui sera affichée sur l'écran du coworking sous forme d'un polaroïd aux couleurs du Poulailler.</p>
        <p>Ce polaroïd sera <strong><span style="text-decoration: underline;">uniquement</span></strong> utilisé pour un affichage sur la télévision lorsque vous êtes présent au Poulailler. Il pourra aussi faire l'objet par la suite d'une impression en format papier pour être affiché sur le tableau d'honneur des coworkers de la salle de pause. Merci pour votre participation !</p>
        <form id="polaroid" method="post" enctype="multipart/form-data">
            <div class="file-upload">
                <input type=file name="photo">
                <button class="button">Choisir une photo</button>
            </div>

        </form>
    <?php } ?>
<?php } ?>