<?php

define('PRODUIT_CAFE_THE', 20367);

/**
 * Afficher une notif lors du checkout pour inviter les gens a ajouter le café dans leur panier s'il n'y est pas déja
 */
add_action('wp_footer', function () {
    if (is_order_received_page()) return;

    if (is_checkout() || is_cart()) {

        $cafe_ids = get_products_with_contribution_cafe_the(true);
        if (commande_recente($cafe_ids)) return;
        if (!is_product_in_cart($cafe_ids)) {
            echo generateNotification([
                'titre' => 'Vous consommez du café ou du thé ?',
                'texte' => 'Pensez à ajouter l\'option payante café/thé afin de participer aux frais. <a href="/boutique/contribution-cafe-the/" target="_blank">En savoir plus</a>.',
                'cta' => [
                    'url' => add_query_arg('cafe', 'true', $_SERVER['REQUEST_URI']),
                    'caption' => 'Ajouter l\'option café/thé à 5€'
                ],
                'image' => '/images/cafe.jpg'
            ]);
        }
    }
}, 99);



if (isset($_GET['cafe'])) {
    /**
     * Ajouter le café au panier
     */
    add_action(
        'template_redirect',
        function () {
            if (!is_product_in_cart(PRODUIT_CAFE_THE)) {
                WC()->cart->add_to_cart(PRODUIT_CAFE_THE);
                wp_redirect($_SERVER['HTTP_REFERER']);
                exit;
            }
        }
    );
}
