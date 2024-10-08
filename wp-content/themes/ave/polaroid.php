<?php

// error_reporting(E_ALL);
// ini_set('display_errors', 'On');

$user = wp_get_current_user();
if (!$user) exit;
$uid = $user->ID;
$polaroid = polaroid_get($uid, false);
$content = false;
$changer = false;
if (isset($_GET['modifier'])) {
    $changer = true;
}


$options = get_field('polaroids', 'option');
$cadre = $options['cadre'];

if (isset($_POST['valider-polaroid'])) {
    $polaroid = $_POST['polaroid'] ?? false;

    $key = 'user_' . $uid;
    update_field('polaroid_nom', $polaroid['nom'], $key);
    update_field('polaroid_description', $polaroid['description'], $key);
    update_field('polaroid_complement', $polaroid['complement'], $key);

    $tmpfile = wp_tempnam('polaroid', get_temp_dir()) . '.jpg';
    $content = explode('base64,', $polaroid['content'])[1];
    file_put_contents($tmpfile, base64_decode($content));
    $aid = insert_attachment_from_file($tmpfile, ['post_title' => 'Photo ' . $uid . ' ' . $user->display_name]);
    unlink($tmpfile);
    update_field('votre_photo', $aid, $key);

    update_field('url_image_trombinoscope', '', $key);

    wp_redirect('/mon-compte/polaroid/');
    exit;
}


if (!empty($_FILES['photo'])) {
    $message = getFileUploadError($_FILES['photo']['error']);
    $basename = $_FILES['photo']['name'];
    $tmp_name = $_FILES['photo']['tmp_name'] ?? false;
    if (!$message && $tmp_name) {
        // Use finfo to detect the MIME type of the file
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->buffer(file_get_contents($tmp_name));

        // Check if the file is not JPEG, then check if it's PNG to convert it
        if ($mimeType != 'image/jpeg') {
            if ($mimeType == 'image/png') {
                // Convert PNG to JPG
                $image = imagecreatefrompng($tmp_name);
                $convertedPath = $tmp_name . '.jpg';
                imagejpeg($image, $convertedPath, 100); // Save as JPEG with max quality
                imagedestroy($image); // Free up memory
                $tmp_name = $convertedPath; // Update tmp_name to the new JPEG path
                $basename = str_ireplace('.png', '.jpg', $basename);
            } else {
                $message = 'Seules les images au format JPG ou PNG son autorisées';
            }
        }
    }

    if ($message) {
        echo generateNotification([
            'type' => 'error',
            'titre' => 'Impossible d\'envoyer ce fichier',
            'texte' => $message
        ]);
    } else {
        $ext = end(explode('.', $basename));
        $content = file_get_contents($tmp_name);
        $tmp_path = wp_upload_dir()['basedir'] . '/' . sha1($content) . '-' . $ext;
        file_put_contents($tmp_path, $content);
        $tmp_url = site_url() . '/wp-content/' . explode('/wp-content/', $tmp_path)[1];
        $isImagePhoto = isImagePhoto($tmp_url);
        unlink($tmp_path);
        if (!$isImagePhoto) {
            custom_redirect('/mon-compte/polaroid/?notification=' . urlencode(json_encode(['type' => 'error', 'titre' => 'Photo invalide', 'texte' => "Merci d'utiliser une photo en prise de vue réelle: Pas de dessin, pas de logo, etc."])));
        }

        $content = getBase64EncodedImage($tmp_name);
    }
}



?>

<?php if ($content) { ?>
    <h3>Aperçu de votre polaroïd</h3>
    <div class="polaroid__generateur">
        <div>
            <div class="polaroid__apercu">
                <img src="<?= $cadre; ?>" class="cadre">
                <img src="" class="photo">
                <div class="texte">
                    <div class="nom" data-id="polaroid_nom"></div>
                    <div class="desc" data-id="polaroid_description"></div>
                    <div class="desc" data-id="polaroid_complement"></div>
                </div>
            </div>

            <p><a href="/mon-compte/polaroid/?changer">Changer de photo</a></p>

        </div>
        <div>
            <form method="post" action="/mon-compte/polaroid/" id="saisie-polaroid" class="woocommerce-EditAccountForm">
                <input type="hidden" id="polaroid_content" name="polaroid[content]" value="<?= $content; ?>">
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
        <div class="polaroid__definitif"><img src="https://photos.coworking-metz.fr/polaroid/size/big/<?= $uid . '.jpg?' . rand(); ?>"></div>
        <br>
        <a class="button" href="?modifier">Modifier</a>
    <?php } else { ?>
        <p>Utilisez l'outil ci-dessous pour choisir la photo qui sera affichée sur l'écran du coworking sous forme d'un polaroïd aux couleurs du Poulailler.</p>
        <p>Ce polaroïd sera <strong><span style="text-decoration: underline;">uniquement</span></strong> utilisé pour un affichage sur la télévision lorsque vous êtes présent au Poulailler. Il pourra aussi faire l'objet par la suite d'une impression en format papier pour être affiché sur le tableau d'honneur des coworkers de la salle de pause. Merci pour votre participation !</p>
        <form id="polaroid" method="post" enctype="multipart/form-data">
            <div class="file-upload">
                <input type=file name="photo">
                <button type="submit" class="button">Choisir une photo</button>
            </div>

        </form>
    <?php } ?>
<?php } ?>