<?php

/**
 * Lien magique : /boutique/<produit>/?coupon=CODE
 * Ajoute le produit courant au panier, applique le coupon, redirige vers le panier.
 */
add_action('template_redirect', function () {
    if (empty($_GET['coupon']) || !function_exists('WC') || !is_product()) {
        return;
    }

    $code       = wc_format_coupon_code(wp_unslash($_GET['coupon']));
    $product_id = get_queried_object_id();
    if (!$code || !$product_id) {
        return;
    }

    if (null === WC()->cart) {
        wc_load_cart();
    }

    $in_cart = false;
    foreach (WC()->cart->get_cart() as $item) {
        if ((int) $item['product_id'] === (int) $product_id) {
            $in_cart = true;
            break;
        }
    }
    if (!$in_cart) {
        WC()->cart->add_to_cart($product_id, 1);
    }

    if (!WC()->cart->has_discount($code)) {
        WC()->cart->apply_coupon($code);
    }

    wp_safe_redirect(wc_get_cart_url());
    exit;
});
