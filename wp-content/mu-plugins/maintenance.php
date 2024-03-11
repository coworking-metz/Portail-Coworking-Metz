<?php


add_action('template_redirect', function() {
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
