<?php

require 'include.php';
require 'api.php';

add_action('profile_update', function ($uid, $old_user_data) {

    @system('rm -rf ' . ABSPATH . 'polaroid/tmp/' . $uid . '-*.jpg');
    @unlink(polaroid_tmpphoto($uid));
    @unlink(polaroid_gen_file($uid));
    @unlink(str_replace('.jpg', '-hd.jpg', polaroid_gen_file($uid)));

    if (polaroid_existe($uid)) {
        update_user_meta($uid, 'url_image_trombinoscope', '');
    }
    CoworkingMetz\CloudFlare::purgeUrls([site_url("/polaroid/$uid.jpg"), site_url("/polaroid/$uid-hd.jpg"), site_url("/polaroid/$uid-raw-small.jpg")]);
    $polaroid = polaroid_get($uid);
}, 99, 2);

add_action('wp_footer', function () {
    if (is_user_logged_in()) {
        $uid = get_current_user_id();
        if (!polaroid_get($uid, false)) {
            echo generateNotification(['titre' => 'Nouveau polaroïd disponible !', 'texte' => '<a href="/mon-compte/polaroid/?modifier">Ajoutez une photo pour profiter du nouveau format de polaroïd</a>.', 'image' => 'images/pola-poule-vide.jpg']);
        }
    }
});

