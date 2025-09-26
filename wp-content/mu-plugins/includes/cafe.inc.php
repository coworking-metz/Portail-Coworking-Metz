<?php
function get_users_with_contribution_cafe_the_last_30_days() {
    // Step 1: Get products with the meta key
    $product_query = new WP_Query([
        'post_type'      => 'product',
        'posts_per_page' => -1,
        'meta_query'     => [
            [
                'key'     => 'contribution-cafe-the',
                'compare' => '!=',
                'value'   => ''
            ]
        ],
        'fields' => 'ids',
    ]);

    $product_ids = $product_query->posts;

    if (empty($product_ids)) {
        return [];
    }

    // Step 2: Get orders from last 30 days
    $date_30_days_ago = (new DateTime('-30 days'))->format('Y-m-d H:i:s');

    $orders = wc_get_orders([
        'status' => ['wc-completed', 'wc-processing'],
        'date_created' => '>' . $date_30_days_ago,
        'limit'  => -1,
    ]);

    if (empty($orders)) {
        return [];
    }

    // Step 3: Collect user IDs from orders containing these products
    $user_ids = [];

    foreach ($orders as $order) {
        foreach ($order->get_items() as $item) {
            if (in_array($item->get_product_id(), $product_ids, true)) {
                $user_ids[] = $order->get_user_id();
                break;
            }
        }
    }

    return array_unique(array_filter($user_ids));
}
