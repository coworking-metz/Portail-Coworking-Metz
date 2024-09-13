<?php


function get_meals($product)
{
    $pid = false;
    if (is_array($product)) {
        $pid = $product['product_id'] ?? $product['ID'] ?? intval($product);
    } else if (is_object($product)) {
        $pid = $product->get_id();
    }
    if (!$pid) return;
    return intval(get_field('coupons_repas', $pid));
}



function get_meal_price($product)
{
    $pid = false;
    if (is_array($product)) {
        $pid = $product['product_id'] ?? $product['ID'] ?? intval($product);
    } else if (is_object($product)) {
        $pid = $product->get_id();
    }
    if (!$pid) return;
    return get_field('prix_repas', $pid);
}
