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

function get_all_dates_nomade(array $bookings)
{
    $all_dates = [];

    foreach ($bookings as $booking) {
        $date = DateTime::createFromFormat('d/m/Y', $booking['date']);
        for ($i = 0; $i < $booking['quantity']; $i++) {
            $all_dates[] = $date->format('d/m/Y');
            $date->modify('+1 day');
        }
    }

    return $all_dates;
}


/**
 * Filtrer la page des visites pour ne garder que les users ayant une visite future (et on met aussi toutes les visites de la semaine passée)
 */
if (isset($_GET['nomadesOnly'])) {
    /*    add_action('pre_get_users', function ($query) {
        if (is_admin()) {
            $query->set('meta_key', 'nomade');
            $query->set('meta_value', '');
            $query->set('meta_compare', '!=');
        }
    });*/


    add_action('pre_get_users', function ($query) {
        if (is_admin()) {
            global $wpdb;
            $user_ids = $wpdb->get_col("
            SELECT DISTINCT postmeta.meta_value 
            FROM {$wpdb->posts} AS posts
            INNER JOIN {$wpdb->postmeta} AS postmeta ON posts.ID = postmeta.post_id
            WHERE posts.post_type = 'shop_order'
            AND posts.post_status IN ('wc-completed', 'wc-processing', 'wc-on-hold')
            AND postmeta.meta_key = '_customer_user'
            AND postmeta.meta_value != '0'
        ");

            if (!empty($user_ids)) {
                $query->query_vars['include'] = $user_ids;
            } else {
                $query->query_vars['include'] = [0]; // Empêche d'afficher des utilisateurs
            }

            $query->set('meta_key', 'nomade');
            $query->set('meta_value', '1');
            $query->set('meta_compare', '=');
        }
    });
}

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
            if (get_field('nomade', 'user_' . $user_id)) {
                // Extra output
                $output .= "<br><b>🕰️ nomade</b>";
            }


            // Housecleaning
            unset($u);
        }
    }
    return $output;
}, 10, 3);
