<?php

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
            $item['productType'] = convertProductType(get_field('productType',$item['product_id']));
        }
    }
    return $payload;
}, 10, 4);
