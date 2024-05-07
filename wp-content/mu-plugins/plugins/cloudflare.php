<?php


add_action('wp_enqueue_scripts', function() {
    wp_enqueue_script('cf.js', 'https://cloudflare.coworking-metz.fr/cf.js?nocache', array(), false, true);
});
