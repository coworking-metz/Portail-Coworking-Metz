<?php
define('WOOCOMMERCE_CLOSED', false);
define('WOOCOMMERCE_CLOSED_ALLOW_ADMINS', true);
define('WOOCOMMERCE_CLOSED_MESSAGE', 'La boutique est fermée temporairement pour maintenance. Merci de revenir dans quelques instants.');


if (WOOCOMMERCE_CLOSED) {
    // Disable purchasing capabilities
    add_filter('woocommerce_is_purchasable', function ($purchasable, $product) {
        if (WOOCOMMERCE_CLOSED_ALLOW_ADMINS && current_user_can('administrator')) return $purchasable;
        return false; // Makes all products unpurchasable
    }, 10, 2);

    add_filter('woocommerce_add_to_cart_validation', function ($valid, $product_id, $quantity) {
        if (WOOCOMMERCE_CLOSED_ALLOW_ADMINS && current_user_can('administrator')) return $valid;
        wc_add_notice(__('Sorry, our shop is currently closed.', 'woocommerce'), 'error');
        return false; // Prevents adding products to the cart
    }, 10, 3);

    // Display a shop closure notice
    add_action('woocommerce_before_main_content', function () {
        if (WOOCOMMERCE_CLOSED_ALLOW_ADMINS && current_user_can('administrator')) {
            echo '<div style="margin-block:.5em;background-color: #ff2f00; color:white;padding: 10px; text-align: center;">La boutique est fermée au membres pour maintenance. Les administrateurs ont accès à la boutique.</div>';
        } else {
            echo '<div style="margin-block:.5em;background-color: #ff2f00; color:white;padding: 10px; text-align: center;">' . esc_html__(WOOCOMMERCE_CLOSED_MESSAGE, 'woocommerce') . '</div>';
        }
    });
}


add_action( 'wp', function () {
    remove_theme_support( 'wc-product-gallery-zoom' );
}, 100 );

add_filter('woocommerce_post_class', function ($classes) {
    if ('product' == get_post_type()) {

        $pid = get_the_ID();
        $terms = wp_get_post_terms($pid, 'product_cat');
        $classes = array_merge($classes, array_map(function ($slug) {
            return 'cat-' . $slug;
        }, array_column($terms, 'slug')));
    }
    return $classes;
}, 21, 3); //woocommerce use priority 20, so if you want to do something after they finish be more lazy


add_filter('woocommerce_available_payment_gateways', function ($available_gateways) {
    if (is_admin() && !defined('DOING_AJAX')) {
        return $available_gateways;
    }

    $user = wp_get_current_user();

    if (current_user_can('administrator')) return $available_gateways;

    if (get_field('payer_en_virement', $user)) return $available_gateways;

    unset($available_gateways['bacs']); // 'bacs' is the id for bank transfer

    return $available_gateways;
});

add_filter('woocommerce_webhook_payload', function ($payload, $resource, $resource_id, $id) {
    $order = wc_get_order($resource_id);

    if ($order && isset($payload['line_items'])) {
        // Loop through each item in the order
        foreach ($payload['line_items'] as &$item) {
            $item['productType'] = convertProductType(get_field('productType', $item['product_id']));
        }
    }
    return $payload;
}, 10, 4);
