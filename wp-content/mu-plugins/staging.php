<?php


// Définir une fonction pour changer la couleur de la barre d'administration en environnement de staging
add_action('admin_bar_menu', function ($wp_admin_bar) {
    if (defined('WP_ENVIRONMENT_TYPE') && WP_ENVIRONMENT_TYPE == 'staging') {
        echo '<style>#wpadminbar { background-color: darkred !important; }</style>';
    }
}, 100);
