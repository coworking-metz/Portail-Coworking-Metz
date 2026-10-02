<?php

/**
 * Produits reserves aux residents Blida.
 *
 * Meme principe que tarifs-reduits.php : les produits de la categorie
 * "residents-blida" ne sont achetables que par les comptes dont le champ ACF
 * resident_blida_ok est coche.
 */

add_action('woocommerce_single_product_summary', function () {
    global $product;
    if (has_term('residents-blida', 'product_cat', $product->get_id())) {
        if (current_user_can_resident_blida()) return;
        $baseStyle = 'style="background:#e9b142;color: white;padding: 1em;margin-inline: -.5em;margin-block: 1em;"';
        $baseMessage = '<strong>Offre réservée aux résidents de Blida.</strong>';
        $contactInfo = ' Si vous êtes résident et pensez être éligible à cette offre, contactez nous par mail à <a style="color:inherit" href="mailto:contact@coworking-metz.fr"><u>contact@coworking-metz.fr</u></a> ou via <a style="color:inherit" href="#ouvrir-brevo"><u>le module de chat du site</u></a>.';
        echo "<div $baseStyle>$baseMessage$contactInfo</div>";
    }
}, 19);

add_filter('woocommerce_add_to_cart_validation', function ($passed, $product_id, $quantity) {
    if (has_term('residents-blida', 'product_cat', $product_id)) {
        if (!current_user_can_resident_blida()) {
            wc_add_notice(__('Désolé, ce produit est réservé aux résidents de Blida.'), 'error');
            $passed = false;
        }
    }

    return $passed;
}, 10, 3);

add_filter('woocommerce_is_purchasable', function ($purchasable, $product) {
    if (has_term('residents-blida', 'product_cat', $product->get_id())) {
        return current_user_can_resident_blida() ? $purchasable : false;
    }
    return $purchasable;
}, 10, 2);
