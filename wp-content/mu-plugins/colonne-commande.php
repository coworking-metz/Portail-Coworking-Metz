<?php
// Add a new column header in the user list
function add_orders_column_header($columns)
{
    $columns['user_orders'] = 'Orders';
    return $columns;
}
add_filter('manage_users_columns', 'add_orders_column_header');
// Add the links to the last order and list of products for each user
function add_orders_column_data($value, $column_name, $user_id)
{
    if ($column_name === 'user_orders') {
        $user_orders = wc_get_orders(array(
            'limit' => 1,
            'orderby' => 'date',
            'order' => 'DESC',
            'customer_id' => $user_id,
        ));

        if (!empty($user_orders)) {
            $last_order = reset($user_orders);
            $order_id = $last_order->get_id();
            $order_edit_url = admin_url('post.php?post=' . $order_id . '&action=edit');

            $products_html = '';
            foreach ($last_order->get_items() as $item_id => $item) {
                $product_id = $item->get_product_id();
                $product = wc_get_product($product_id);
                $product_name = $product->get_name();
                $products_html .= '<li>&middot; ' . $product_name . '</li>';
            }

            if (!empty($products_html)) {

                $value = '<p><b><a href="' . $order_edit_url . '">Dernière commande en date</a></b><br><ul> ' . $products_html . '</ul></p>';
            }
            $orders_page_url = admin_url('edit.php?s&post_status=all&post_type=shop_order&_customer_user=' . $user_id);
            $value .= '<a href="' . $orders_page_url . '">Voir toutes les commandes</a>';
        } else {
            $value = 'No Orders';
        }
    }
    return $value;
}
add_filter('manage_users_custom_column', 'add_orders_column_data', 10, 3);
