<?php

define('PRODUIT_ADHESION', 3063);

/**
 * Afficher une notif lors du checkout pour inviter les gens a ajouter l'adhesion dans leur panier 
 */
add_action('wp_footer', function () {
    if (is_order_received_page()) return;

    if (is_checkout() || is_cart()) {
        if (is_product_in_cart(PRODUIT_ADHESION)) return;

		$uid = get_current_user_id();
        if(!$uid) return;
        $json = file_get_contents(TICKET_BASE_URL.'/members/'.$uid.'?key='.API_KEY_TICKET); // actifs dans les 6 derniers moois
		$member = json_decode($json, true);


		$lastMembership = $member['lastMembership']??false;

		if($lastMembership == date('Y')) return;

		echo generateNotification([
			'titre' => 'Vous n\'êtes pas à jour de votre adhésion',
			'texte' => 'Pensez à règler l\'adhésion annuelle à l\'association. Elle est obligatoire pour tous les coworkers. <a href="https://www.coworking-metz.fr/boutique/carte-adherent/" target="_blank">En savoir plus</a>.',
			'cta' => [
				'url' => add_query_arg('adhesion', 'true', $_SERVER['REQUEST_URI']),
				'caption' => 'Ajouter l\'adhésion au panier'
			],
			'image' => 'https://www.coworking-metz.fr/wp-content/uploads/2015/08/carte-de-membre-lepoulailler-1.png'
		]);

    }
});



if (isset($_GET['adhesion'])) {
    /**
     * Ajouter le café au panier
     */
    add_action(
        'template_redirect',
        function () {
            if (!is_product_in_cart(PRODUIT_ADHESION)) {
                WC()->cart->add_to_cart(PRODUIT_ADHESION);
                wp_redirect($_SERVER['HTTP_REFERER']);
                exit;
            }
        }
    );
}
