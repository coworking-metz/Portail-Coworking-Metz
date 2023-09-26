<?php


function is_product_in_cart($product_id) {
    $cart = WC()->cart->get_cart();
    foreach ($cart as $cart_item) {
        if ($cart_item['product_id'] == $product_id || $cart_item['variation_id'] == $product_id) {
            return true;
        }
    }
    return false;
}