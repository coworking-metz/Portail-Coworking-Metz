<?php
use CoworkingMetz\CloudFlare;


/**
 * Headers envoyés à Cloudflare pour l'aider à savoir combien de temps cacher certaines ressources
 * Inactif pour l'instant car le cache CF  n'est pas actife sur le site
 */
add_action('send_headers', function () {

    if (is_admin()) return;
    if (is_shop()) return;
    if(strstr($_SERVER['REQUEST_URI'], 'la-boutique')) return;
    if(strstr($_SERVER['REQUEST_URI'], 'mon-compte')) return;


    header_remove("Cache-Control");
    header_remove("Expires");
    header_remove("Pragma");

    $max_age = ONE_MINUTE;
    $smax_age = $options['s-maxage'] ?? ONE_DAY;

    header('Cache-Control: public, max-age=' . $max_age . ', s-maxage=' . $smax_age . '');

});



if (isset($_GET['vider-cache'])) {
    add_action('admin_init', function () {
        CloudFlare::purgeDomainCache();

        wp_redirect($_SERVER['HTTP_REFERER']);
        exit;
    });
}



add_action('admin_bar_menu', function ($wp_admin_bar) {

    if (is_admin()) {

        $args = array(
            'id'    => 'cache',
            'title' => '<span class="ab-icon dashicons dashicons-admin-appearance"></span> Vider le cache',
            'href'  => '/wp-admin/?vider-cache'
        );

        $wp_admin_bar->add_node($args);
    }
}, 100);
