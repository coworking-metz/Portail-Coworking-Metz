<?php
$user_id = $_GET['user_id'] ?? false;

if ($user_id) {
    define('CHEMIN_SITE', realpath(__DIR__));
    define('URL_SITE', 'https://www.coworking-metz.fr' . explode('coworking-metz.fr', CHEMIN_SITE)[1]);
    require_once(CHEMIN_SITE . '/../wp-load.php');

    
    $aid = get_user_meta($user_id, $key = 'url_image_trombinoscope', $single = true);
    
    $photo = wp_get_attachment_image_src($aid)[0];
    if ($photo) {
        header('Location: ' . $photo);
    } else {
        $pola_vide = CHEMIN_SITE . 'pola-vide.png';

    }
}
