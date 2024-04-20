<?php


add_action('rest_api_init', function () {
    register_rest_route('cowo/v1', '/commandes/(?P<annee>\d+)', array(
        'methods' => 'GET',
        'args' => array(
            'annee' => array(
                'validate_callback' => function ($param, $request, $key) {
                    return is_numeric($param);
                },
                'required' => true,
            ),
        ),
        'permission_callback' => function () {
            return isset($_GET['open']) ? true : current_user_can('manage_woocommerce');
        },
        'callback' => function ($data) {
            $year = $data['annee'];
            $args = array(
                'limit' => -1,
                'date_created' => $year . '-01-01 ... ' . $year . '-12-31',
                'return' => 'ids',
            );
        
            $orders = wc_get_orders($args);
            $orders_details = array();
        
            foreach ($orders as $order_id) {
                $order = wc_get_order($order_id);
                $products = array();
                foreach ($order->get_items() as $item_id => $item) {
                    $product = $item->get_product();
                    if(!$product) continue;
                    $products[] = array(
                        'product_id' => $product->get_id(),
                        'name' => $product->get_name(),
                        'quantity' => $item->get_quantity(),
                        'price' => $item->get_total(),
                        'productType' => toCamelCase(get_field('productType',$product->get_id())),
                    );
                }
                $orders_details[] = array(
                    'orderId' => $order_id,
                    'orderReference' => get_order_meta_data($order)['_alg_wc_full_custom_order_number']??'',
                    'wpUserId' => $order->customer_id,
                    'purchaseDate' => $order->get_date_created()->date('Y-m-d H:i:s'),
                    'total' => $order->get_total(),
                    'products' => $products,
                );
            }
        
            return new WP_REST_Response($orders_details, 200);
        },
    ));
});
