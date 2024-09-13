<?php


function get_meals($product)
{
    return intval(get_field('coupons_repas', $product['product_id'] ?? $product->get_id() ?? false));
}



function get_meal_price($product)
{
    return get_field('prix_repas', $product['product_id'] ?? $product->get_id() ?? false);
}
