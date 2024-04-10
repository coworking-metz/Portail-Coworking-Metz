<?php
function get_image_fond_pola()
{
    if (!get_field('activer_theme_trombi', 'option')) return;

    $image_fond_pola = get_attached_file(get_field('image_fond_pola', 'option'));
    return $image_fond_pola;
}


function get_alpha_polaroid($uid, $file)
{
    // if($uid!=225) return;
    if(basename($file) == 'default.jpg' || basename($file) == 'poussin.jpg') {
        return str_replace('.jpg','-alpha.png',$file);
    }
    $url = pathTourl($file);
    // update_field('votre_photo_alpha', '', 'user_861');

    $alpha_id = get_field('votre_photo_alpha', 'user_' . $uid);

    if (!$alpha_id) {
        $api = 'https://tools.sopress.net/remove-background/?raw&force=true&crop=false&image=' . urlencode($url);
        $alpha_url = file_get_contents($api);
        $alpha_id = insert_attachment_from_file($alpha_url, [], ['original' => $url], 1000);
    } else {
        if (!get_post($alpha_id)) {
            update_field('votre_photo_alpha', '', 'user_' . $uid);
            return get_alpha_polaroid($uid, $file);
        }
    }
    if ($alpha_id) {
        $original = get_post_meta($alpha_id, 'original', true);
        if ($original != $url) {
            wp_delete_attachment($alpha_id);
            update_field('votre_photo_alpha', '', 'user_' . $uid);
            return get_alpha_polaroid($uid, $file);
        }
        update_field('votre_photo_alpha', $alpha_id, 'user_' . $uid);
        $alpha = wp_get_attachment_url($alpha_id);
        return urlToPath($alpha);
    }
}

function polaroid_output($file)
{
    $expires = 86400; // 60 seconds * 60 minutes * 24 hours = 1 day
    header('Cache-Control: max-age=' . $expires . ', must-revalidate');
    header('Pragma: cache');
    header('Expires: ' . gmdate('D, d M Y H:i:s', time() + $expires) . ' GMT');

    header('Content-Type: image/jpeg');
    echo file_get_contents($file);
    exit;
}

function polaroid_gen_file($uid = null)
{
    if (!$uid) {
        $user = wp_get_current_user();
        if (!$user) return;
        $uid = $user->ID;
    }

    return ABSPATH . '/polaroid/' . $uid . '.jpg';
}
function polaroid_url($uid = null, $domain = false)
{
    if (!$uid) {
        $user = wp_get_current_user();
        if (!$user) return;
        $uid = $user->ID;
    }

    $image = wp_get_attachment_url(get_user_meta($uid, 'url_image_trombinoscope', true));

    if ($image) {
        return $image;
    } else {
        return ($domain ? site_url() : '') . '/polaroid/' . $uid . '.jpg';
    }
}



function polaroid_existe($uid = null)
{

    $polaroid = polaroid_get($uid, false);
    return $polaroid['photo'] && $polaroid['nom'];

    if (!$uid) {
        $user = wp_get_current_user();
        if (!$user) return;
        $uid = $user->ID;
    }


    $ret = true;
    if (!$uid) $ret = false;

    // $photo = get_field('votre_photo', 'user_' . $uid);
    // if (!$photo) $ret = false;

    $nom = get_field('polaroid_nom', 'user_' . $uid);
    if (!$nom) {
        $user_info = get_userdata($uid);
        $nom = $user_info->display_name ?? false;
        if (!$nom) {
            $ret = false;
        }
    }

    if (!$ret) {
        $image = get_user_meta($uid, 'url_image_trombinoscope', true);
        if ($image) {
            $ret = true;
        }
    }

    return $ret;
}

function polaroid_get($uid = null, $defaults = true)
{
    if (!$uid) {
        $user = wp_get_current_user();
        if (!$user) return;
        $uid = $user->ID;
    }


    if (!$uid) return;

    $user_info = get_userdata($uid);

    $photo = get_field('votre_photo', 'user_' . $uid);
    $nom = get_field('polaroid_nom', 'user_' . $uid);
    if (!$nom) {
        $nom = $user_info->display_name ?? false;
    }
    $description = get_field('polaroid_description', 'user_' . $uid);
    $complement = get_field('polaroid_complement', 'user_' . $uid);

    $file = get_attached_file($photo);

    if (!$file && $defaults) {
        $file = ABSPATH . 'polaroid/images/default.jpg';
    }
    // $file = get_gravatar_url($user_info->user_email);
    $visite = false;

    if(is_visiteur($user_info)) {
        if($defaults) {
            $file = ABSPATH . 'polaroid/images/poussin.jpg';
        } else $file=false;
        $description='Visite & Journée d\'essai';    
        $complement='';
        $visite = true;
    }
    $alpha = get_alpha_polaroid($uid, $file);
    
    return ['photo' => $file, 'alpha' => $alpha, 'nom' => $nom, 'description' => $description, 'complement' => $complement, 'visite'=>$visite];
}
function polaroid_tmpphoto($uid = null)
{
    if (!$uid) {
        $user = wp_get_current_user();
        if (!$user) return;
        $uid = $user->ID;
    }

    $tmpdir = polaroid_tmpdir();
    $tmp_photo = $tmpdir . '/photo_' . $uid;
    return $tmp_photo;
}
function polaroid_tmpfile()
{
    return polaroid_tmpdir() . '/' . md5(rand());
}
function polaroid_tmpdir()
{
    $upload_dir = wp_upload_dir();
    $tmpdir = $upload_dir['basedir'] . '/tmp';
    if (!is_dir($tmpdir)) {
        mkdir($tmpdir, 0775);
    }
    return $tmpdir;
}
function polaroid_upload()
{

    wp_enqueue_style('polaroid', '/wp-content/mu-plugins/polaroid/polaroid.css', array(), time(), false);
    wp_enqueue_script('polaroid', '/wp-content/mu-plugins/polaroid/polaroid.js', array(), time(), false);
    get_template_part('polaroid'); // This will load polaroid.php from your theme.
    // var_dump($_FILES);
}

function getBase64EncodedImage($path)
{
    // Check if file exists
    if (!file_exists($path)) {
        return false;
    }

    // Read the file content
    $fileContent = file_get_contents($path);
    if ($fileContent === false) {
        return false;
    }
    // Use finfo to detect the MIME type of the file
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mimeType = $finfo->buffer($fileContent);

    // Convert the image to base64
    $base64Encoded = base64_encode($fileContent);

    // Return the src-ready encoded string
    return "data:$mimeType;base64,$base64Encoded";
}
