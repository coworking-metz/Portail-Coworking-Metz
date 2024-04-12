<?php

if (!strstr($_SERVER['HTTP_HOST'] ?? '', '.local')) {

    if (($_SERVER['HTTP_HOST'] ?? '') === 'wpapi.coworking-metz.fr') {

        if (strstr($_SERVER['REQUEST_URI'], 'wp-admin')) {
            header('Location: https://www.coworking-metz.fr' . $_SERVER['REQUEST_URI']);
            exit;
        }
        add_action('template_redirect', function () {
            remove_filter('template_redirect', 'redirect_canonical');
        });
    }


    if (($_SERVER['HTTP_HOST'] ?? '') === 'wpapi-recette.coworking-metz.fr') {

        if (strstr($_SERVER['REQUEST_URI'], 'wp-admin')) {
            header('Location: https://recette.coworking-metz.fr' . $_SERVER['REQUEST_URI']);
            exit;
        }
        add_action('template_redirect', function () {
            remove_filter('template_redirect', 'redirect_canonical');
        });
    }


}
