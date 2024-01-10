<?php


if (strstr($_SERVER['REQUEST_URI'], 'api-json-wp')) {
    add_filter('option_home', function ($url) {
        if ($_SERVER['HTTP_HOST'] == 'api.coworking-metz.fr') {
            return 'https://api.coworking-metz.fr';
        }
        if ($_SERVER['HTTP_HOST'] == 'wpapi.coworking-metz.fr') {
            return 'https://wpapi.coworking-metz.fr';
        }
        return $url;
    });

    add_filter('option_siteurl', function ($url) {
        if ($_SERVER['HTTP_HOST'] == 'api.coworking-metz.fr') {
            return 'https://api.coworking-metz.fr';
        }
        if ($_SERVER['HTTP_HOST'] == 'wpapi.coworking-metz.fr') {
            return 'https://wpapi.coworking-metz.fr';
        }
        return $url;
    });
}
