<?php


add_action('woocommerce_before_add_to_cart_button', function() {
    global $product;
    if (35832 === $product->get_id()) {
        echo '<div class="custom-price-field">
                <label for="custom_price">Prix de votre plat:</label>
                <input type="number" id="custom_price" name="custom_price" placeholder="Prix" step="any"/>
             </div>';
    }
});


add_filter('woocommerce_add_cart_item_data', function($cart_item_data, $product_id, $variation_id, $quantity) {
    if (35832 === $product_id && isset($_POST['custom_price']) && !empty($_POST['custom_price'])) {
        $custom_price = floatval(sanitize_text_field($_POST['custom_price'])); // Convert input to float
        $cart_item_data['custom_price'] = $custom_price;
        $cart_item_data['unique_key'] = md5(microtime().rand()); // Ensure unique line item
    }
    return $cart_item_data;
}, 10, 4);

add_action('woocommerce_before_calculate_totals', function($cart) {
    if (is_admin() && !defined('DOING_AJAX')) return;

    foreach ($cart->get_cart() as $cart_item_key => $cart_item) {
        if (isset($cart_item['custom_price'])) {
            $cart_item['data']->set_price($cart_item['custom_price']); // Set the custom price
        }
    }
});
