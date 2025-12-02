<?php
// add_action('woocommerce_update_order', function ($order_id) {
// Exécuter du code PHP lorsqu'une commande WooCommerce est marquée comme payée (complétée)
add_action('woocommerce_order_status_completed', function ($order_id) {


    $order = wc_get_order($order_id);

    $user_id = $order->get_user_id();
    $user = get_user_by('id', $user_id);
    if (!isNomade($user)) return;

    // Vérifier si tous les produits sont dans la catégorie 'tickets-nomade'
    $items = $order->get_items();
    $dates = [];
    foreach ($items as $item) {
        $product_id = $item->get_product_id();
        if (has_term('tickets-nomades', 'product_cat', $product_id)) {
            $tm_data = $item->get_meta('_tmcartepo_data', true);
            $dates[] = ['date' => $tm_data[0]['value'] ?? false, 'quantity' => $item->get_quantity()];
        }
    }


    $all_dates = get_all_dates_nomade($dates);

    if ($all_dates) {
        envoyerMailNomade($user_id, $all_dates);
        envoyerMailAlerteNomade($user_id, ['all_dates' => $all_dates]);
    }
});

/**
 * Clear cached nomade orders when any order is created or updated
 */
add_action('woocommerce_checkout_order_processed', function() {
    delete_nomade_order_transients();
});

add_action('woocommerce_order_status_changed', function() {
    delete_nomade_order_transients();
});

add_action('pre_get_users', function ($query) {
    if (!is_admin()) return;
    if (!isset($_GET['nomadesOnly'])) return;

    $flag = $_GET['nomadesOnly'];
    if ($flag !== 'true' && $flag !== '1') return;

    // 1 — Get all nomade orders
    $orders = get_orders_with_nomade_products();
    if (empty($orders)) {
        $query->set('include', [0]);
        return;
    }

    // 2 — Extract user IDs
    $temp_ids = [];
    foreach ($orders as $order) {
        $uid = $order->get_user_id();
        if ($uid) $temp_ids[$uid] = true;
    }

    if (empty($temp_ids)) {
        $query->set('include', [0]);
        return;
    }

    // 3 — Only test these user IDs with isNomade()
    $final_ids = [];
    foreach (array_keys($temp_ids) as $uid) {
        if (isNomade($uid)) $final_ids[] = $uid;
    }

    if (empty($final_ids)) {
        $query->set('include', [0]);
        return;
    }

    // 4 — Apply filtered list
    $query->set('include', $final_ids);
});




add_filter('manage_users_columns', function ($columns) {
    return array_slice($columns, 0, 2, true)
        + ['custom_name' => __('Name')]
        + array_slice($columns, 3, null, true);
});


add_filter('manage_users_custom_column', function ($output, $column_name, $user_id) {
    if ('custom_name' === $column_name) {
        $u = new WP_User($user_id);
        if ($u instanceof \WP_User) {
            // Default output
            $output .= "$u->first_name $u->last_name";
            if (isNomade($user_id)) {
                // Extra output
                $output .= "<br><b>🕰️ nomade</b>";
            }


            // Housecleaning
            unset($u);
        }
    }
    return $output;
}, 10, 3);
