<?php


/**
 * Afficher une notif lors du checkout pour inviter les gens a ajouter le café dans leur panier s'il n'y est pas déja
 */
add_action('wp_footer', function () {
    if (is_order_received_page() || strstr($_SERVER['REQUEST_URI'],'/mon-compte/')!==false) {



		$data = [
			'id'=>'auto',
			'titre' => 'Enquête coworking !',
			'texte' => 'Pour en savoir plus sur vos besoins, nous vous proposons un rapide questionnaire (ça dure 5 minutes) pour savoir ce qui pour vous fait du Poulailler le meilleur coworking de l\'univers!',
			'cta' => [
				'target'=>'_blank',
				'url' => 'https://docs.google.com/forms/d/1FbKH1lVLBHZsfekl9S9GserfMF_DedMY-tsV5-E_bUU',
				'caption' => 'Aller au questionnaire'
			],
			'image' => '/images/enquete.png'
		];
		echo generateNotification($data);
	}
}, 99);
