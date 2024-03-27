<?php


if ($_SERVER['HTTP_HOST'] === 'wpapi.coworking-metz.fr') {

    if (strstr($_SERVER['REQUEST_URI'], 'wp-admin')) {
        header('Location: https://www.coworking-metz.fr'.$_SERVER['REQUEST_URI']);
        exit;
    }
    add_action('template_redirect', function() {
        remove_filter('template_redirect', 'redirect_canonical');
    });
}


// if (strstr($_SERVER['REQUEST_URI'], 'api-json-wp')) {
//     add_filter('option_home', function ($url) {
//         if ($_SERVER['HTTP_HOST'] == 'wpapi.coworking-metz.fr') {
//             return 'https://wpapi.coworking-metz.fr';
//         }
//         return $url;
//     });

//     add_filter('option_siteurl', function ($url) {
//         if ($_SERVER['HTTP_HOST'] == 'wpapi.coworking-metz.fr') {
//             return 'https://wpapi.coworking-metz.fr';
//         }
//         return $url;
//     });
// }
