<?php

add_action('woocommerce_single_product_summary', function () {
    global $product;
    if (has_term('tarifs-reduits', 'product_cat', $product->get_id())) {
        if (current_user_can_tarif_reduit()) return;
        $baseStyle = 'style="background:#e9b142;color: white;padding: 1em;margin-inline: -.5em;margin-block: 1em;"';
        $baseMessage = '<strong>Offre à tarif réduit destinée aux étudiants et personnes en recherche d\'emploi.</strong>';
        $contactInfo = ' Si vous pensez être éligible à cette offre, contactez nous par mail à <a style="color:inherit" href="mailto:contact@coworking-metz.fr"><u>contact@coworking-metz.fr</u></a> ou via <a style="color:inherit" href="#ouvrir-brevo"><u>le module de chat du site</u></a>.';
        echo "<div $baseStyle>$baseMessage$contactInfo</div>";
    }
}, 19);


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
