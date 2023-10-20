<?php

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
