<?php

/**
 * Quand on sauve la page option menu, on peut faire revenir aux valeurs par defaut la liste des comp�titions pr�f�r�es
 * On stocke deux donn�es : la liste des compets, et leur ordre
 */
add_action('acf/save_post', function () {
    if (!is_admin()) return;
	$screen = get_current_screen();
	if (!strpos($screen->id, "reglages-comptes")) return;
	\CoworkingMetz\CloudFlare::purgeUrls(site_url('/api-json-wp/cowo/v1/trombi'));
}, 20);
