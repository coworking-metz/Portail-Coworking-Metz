<?php


add_filter('woocommerce_webhook_payload', function ($payload, $resource, $resource_id, $id) {
    $order = wc_get_order($resource_id);

    if ($order && isset($payload['line_items'])) {
        // Loop through each item in the order
        foreach ($payload['line_items'] as &$item) {
            $item['meals'] = intval(get_field('coupons_repas',  $item['product_id']));
            $item['meal_price'] = get_field('prix_repas',  $item['product_id']);
        }
    }
    return $payload;
}, 10, 4);
