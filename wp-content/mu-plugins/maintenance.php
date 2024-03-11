<?php

// Vérifie si le paramètre 'ouvrir' est présent dans l'URL ou si le cookie est déjà défini, puis stocke une valeur dans un cookie si nécessaire. Empêche l'exécution du reste du code si 'ouvrir' est défini ou si le cookie est présent.
add_action('template_redirect', function() {
    // Vérifie si le paramètre 'ouvrir' est présent dans l'URL ou si le cookie est déjà défini
    if(isset($_GET['ouvrir']) || isset($_COOKIE['ouvrir'])) {
        // Stocke une valeur dans un cookie si 'ouvrir' est défini
        if(isset($_GET['ouvrir'])) {
            setcookie('ouvrir', '1', time() + 3600, "/"); // Le cookie expire dans 1 heure
        }
        CoworkingMetz\CloudFlare::noCacheHeaders();
        header('coworking-maintenance: BYPASS');
        return; // Retourne sans exécuter le reste du code
    }

    $uri = $_SERVER['REQUEST_URI'];

    $block = false;
    if(strstr($uri, 'boutique')) $block = true;
    if(strstr($uri, 'mon-compte')) $block = true;
    if(strstr($uri, 'cart')) $block = true;

    if($block) {
        CoworkingMetz\CloudFlare::noCacheHeaders();
        wp_die('<big><strong>Le site du Coworking est en maintenance temporaire</strong></big><br>Merci de revenir dans quelques instants.', 'Maintenance en cours', ['response'=>503]);
    }
});

