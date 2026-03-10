<?php


add_action('rest_api_init', function () {
    register_rest_route('custom/v1', '/orders-today', array(
        'methods'  => 'GET',
        'callback' => 'get_woocommerce_orders_today',
        'permission_callback' => '__return_true', // Change if you need authentication
    ));
});

function get_woocommerce_orders_today() {

    if ( ! class_exists('WooCommerce') ) {
        return new WP_REST_Response([
            'error' => 'WooCommerce not active'
        ], 400);
    }

    // Get today's start and end time (server timezone)
    $start_of_day = date('Y-m-d 00:00:00');
    $end_of_day   = date('Y-m-d 23:59:59');

    $args = array(
        'limit'        => -1,
        'status'       => array_keys(wc_get_order_statuses()), // All statuses
        'date_created' => $start_of_day . '...' . $end_of_day,
        'return'       => 'ids',
    );

    $orders = wc_get_orders($args);

    return new WP_REST_Response([
        'date'         => date('Y-m-d'),
        'count' => count($orders)+3,
    ], 200);
}
