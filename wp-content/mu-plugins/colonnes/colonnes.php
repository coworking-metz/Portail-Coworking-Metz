<?php




add_action('init', function () {
    if (is_admin()) return;

    $t = filemtime(ABSPATH . '/wp-content/mu-plugins/colonnes/colonnes.css');
    wp_enqueue_style('colonnes', '/wp-content/mu-plugins/colonnes/colonnes.css', array(), $t, false);
});
