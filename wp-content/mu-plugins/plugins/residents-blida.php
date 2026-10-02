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
        $baseMessage = '<strong>Offre réservée aux résident-e-s Bliiida.</strong>';
        $contactInfo = ' Si vous êtes résident-e, contactez-nous par mail à <a style="color:inherit" href="mailto:contact@coworking-metz.fr"><u>contact@coworking-metz.fr</u></a> ou via <a style="color:inherit" href="#ouvrir-brevo"><u>le module de chat du site</u></a> pour activer vos droits.';
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


/**
 * Les residents Blida n'ont pas besoin de l'adhesion : elle est refusee a
 * l'ajout et retiree du panier si elle s'y trouve deja.
 */
function est_produit_adhesion($product_id)
{
    return (int) $product_id === (int) PRODUIT_ADHESION
        || get_post_meta($product_id, 'productType', true) === 'adhesion';
}

add_filter('woocommerce_add_to_cart_validation', function ($passed, $product_id) {
    if (est_produit_adhesion($product_id) && current_user_can_resident_blida()) {
        wc_add_notice(__('En tant que résident de Blida, vous n\'avez pas besoin de régler l\'adhésion : elle est incluse dans votre statut de résident.'), 'notice');
        return false;
    }
    return $passed;
}, 10, 2);

add_action('woocommerce_check_cart_items', function () {
    if (!current_user_can_resident_blida() || !WC()->cart) return;

    $retire = false;
    foreach (WC()->cart->get_cart() as $cle => $item) {
        if (est_produit_adhesion($item['product_id'])) {
            WC()->cart->remove_cart_item($cle);
            $retire = true;
        }
    }
    if ($retire) {
        wc_add_notice(__('En tant que résident de Blida, vous n\'avez pas besoin de régler l\'adhésion : elle a été retirée de votre panier.'), 'notice');
    }
});


/**
 * Garde-fou : un resident Blida ne peut prendre qu'un carnet de tickets de la
 * categorie "residents-blida", pour eviter de prendre la mauvaise version.
 */
function est_carnet_non_resident($product_id)
{
    return get_post_meta($product_id, 'productType', true) === 'carnet-tickets'
        && !has_term('residents-blida', 'product_cat', $product_id);
}

add_filter('woocommerce_add_to_cart_validation', function ($passed, $product_id) {
    if (current_user_can_resident_blida() && est_carnet_non_resident($product_id)) {
        wc_add_notice(__('En tant que résident de Blida, vous bénéficiez d\'un carnet de tickets dédié : ce carnet ne vous est pas destiné. Merci de choisir le carnet de la catégorie « Résidents Blida ».'), 'error');
        return false;
    }
    return $passed;
}, 10, 2);

add_action('woocommerce_check_cart_items', function () {
    if (!current_user_can_resident_blida() || !WC()->cart) return;

    $retire = false;
    foreach (WC()->cart->get_cart() as $cle => $item) {
        if (est_carnet_non_resident($item['product_id'])) {
            WC()->cart->remove_cart_item($cle);
            $retire = true;
        }
    }
    if ($retire) {
        wc_add_notice(__('Un carnet de tickets qui n\'est pas celui des résidents Blida a été retiré de votre panier. Merci de choisir le carnet de la catégorie « Résidents Blida ».'), 'error');
    }
});


/**
 * Page boutique : pour un resident Blida, les sections "Tickets / Abos" et
 * "Tarifs reduits" sont grisees, reduites et non cliquables. Les sections sont
 * reperees par les categories de leurs produits (le markup du builder n'a pas
 * de classe stable).
 */
add_action('wp_head', function () {
    if (!is_page('la-boutique') || !current_user_can_resident_blida()) return;
?>
    <style>
        main.content { display: flex; flex-direction: column; }
        main.content > section.vc_row:first-of-type { order: -2; }
        main.content > section.vc_row:has(li.product.cat-residents-blida) { order: -1; }

        section.vc_row:has(li.product.cat-abonnements-tickets),
        section.vc_row:has(li.product.cat-tarifs-reduits) {
            pointer-events: none;
            filter: grayscale(1);
            opacity: .45;
            user-select: none;
        }

        section.vc_row:has(li.product.cat-abonnements-tickets) h2,
        section.vc_row:has(li.product.cat-tarifs-reduits) h2 {
            font-size: 1.1rem !important;
        }

        section.vc_row:has(li.product.cat-abonnements-tickets) ul.products,
        section.vc_row:has(li.product.cat-tarifs-reduits) ul.products {
            display: flex !important;
            flex-wrap: wrap;
            gap: 1rem;
        }

        section.vc_row:has(li.product.cat-abonnements-tickets) ul.products li.product,
        section.vc_row:has(li.product.cat-tarifs-reduits) ul.products li.product {
            float: none !important;
            width: 140px !important;
            margin: 0 !important;
        }

        section.vc_row:has(li.product.cat-abonnements-tickets) .ld-sp-info h3,
        section.vc_row:has(li.product.cat-tarifs-reduits) .ld-sp-info h3,
        section.vc_row:has(li.product.cat-abonnements-tickets) .ld-sp-info .price,
        section.vc_row:has(li.product.cat-tarifs-reduits) .ld-sp-info .price {
            font-size: .8rem !important;
        }
    </style>
<?php
});


/**
 * Produit adhesion : l'image est un format paysage, on evite le recadrage carre
 * de la vignette dans les listes de produits.
 */
add_filter('single_product_archive_thumbnail_size', function ($size) {
    $product = wc_get_product(get_the_ID());
    return ($product && $product->get_id() === (int) PRODUIT_ADHESION) ? 'full' : $size;
});

add_action('wp_head', function () {
?>
    <style>
        li.product.post-<?= (int) PRODUIT_ADHESION ?> .ld-sp-img img {
            object-fit: contain;
        }
    </style>
<?php
});
