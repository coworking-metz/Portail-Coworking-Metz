<?php

add_action('woocommerce_single_product_summary', function () {
    global $product;
    if (has_term('tarifs-reduits', 'product_cat', $product->get_id())) {
        if (current_user_can_tarif_reduit()) {
            echo '<div style="background:#e9b142;color: white;padding: 1em;margin-inline: -.5em;margin-block: 1em;"><strong>Offre à tarif réduit destinées aux étudiants et personnes en recherche d\'emploi.</strong></div>';
        } else {
            echo '<div style="background:#e9b142;color: white;padding: 1em;margin-inline: -.5em;margin-block: 1em;"><strong>Offre à tarif réduit destinées aux étudiants et personnes en recherche d\'emploi.</strong> Si vous pensez être éligible à cette offre, contactez nous par mail à <a style="color:inherit" href="mailto:contact@coworking-metz.fr"><u>contact@coworking-metz.fr</u></a> ou via le module de chat du site et nous vous ouvrirons les droits.</div>';
        }
    }
}, 19); // Hook before the product short description (priority 20)



add_filter('woocommerce_add_to_cart_validation', function ($passed, $product_id, $quantity) {
    if (has_term('tarifs-reduits', 'product_cat', $product_id)) {
        if (!current_user_can_tarif_reduit()) {
            wc_add_notice(__('Désolé, vous n\'êtes pas autorisé à acheter des produits dans la catégorie Tarifs Réduits.'), 'error');
            $passed = false;
        }
    }

    return $passed;
}, 10, 3);


add_filter('woocommerce_is_purchasable', function ($purchasable, $product) {
    if (has_term('tarifs-reduits', 'product_cat', $product->get_id())) {
        if (current_user_can_tarif_reduit()) {
            return $purchasable;
        } else {
            return false;
        }
    } else return $purchasable;
}, 10, 2);
