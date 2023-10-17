<?php

if (isset($_GET['export-users'])) {
    add_action('admin_init', function () {

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=users-' . date('Y-m-d-H-i-s') . '.csv');

        $output = fopen('php://output', 'w');

        // Écrire les en-têtes de colonnes
        fputcsv($output, ['ID', 'Email', 'Display Name', 'Registration Date', '_last_order_date', '_first_order_date', 'Role']);

        $args = ['fields' => ['ID']];
        $users = get_users($args);

        foreach ($users as $user) {
            $user_data = get_userdata($user->ID);

            $id = $user_data->ID;
            $email = $user_data->user_email;
            $display_name = $user_data->display_name;
            $registration_date = $user_data->user_registered;
            $visite = get_user_meta($id, 'visite', true);
            $last_order_date = get_user_meta($id, '_last_order_date', true);
            $first_order_date = get_user_meta($id, '_first_order_date', true);
            $role = !empty($user_data->roles) ? implode(',', $user_data->roles) : '';

            // Écrire la ligne de données pour chaque utilisateur
            fputcsv($output, [$id, $email, $display_name, $registration_date, $visite, $last_order_date, $first_order_date, $role]);
        }

        fclose($output);
        exit;
    });
}



if (isset($_GET['_last_order_date'])) {
    add_action('admin_init', function () {
        // Get all users
        $args = [
            'fields' => 'ID',  // Only need ID field for performance
        ];
        $users = get_users($args);
        foreach ($users as $user_id) {
            // Check if user already has _last_order_date meta
            // if (!get_user_meta($user_id, '_last_order_date', true)) {

            // Get user's last order
            $customer_orders = wc_get_orders([
                'customer' => $user_id,
                'limit'    => 1,
                'orderby'  => 'date',
                'order'    => 'DESC',
            ]);

            // If there's an order, update _last_order_date meta
            if (!empty($customer_orders)) {
                $order = $customer_orders[0];
                $order_date = $order->get_date_created();
                // $t = $order_date->getTimestamp();
                $t = $order_date->date('Y-m-d H:i:s');
                update_user_meta($user_id, '_last_order_date', $t);
                echo $user_id . ' - ' . $t . '<hr>';
            }
            // }

        }
        exit;
    });
}
if (isset($_GET['_first_order_date'])) {
    add_action('admin_init', function () {
        // Get all users
        $args = [
            'fields' => 'ID',  // Only need ID field for performance
        ];
        $users = get_users($args);
        foreach ($users as $user_id) {
            // Check if user already has _first_order_date meta
            // if (!get_user_meta($user_id, '_first_order_date', true)) {

            // Get user's first order
            $customer_orders = wc_get_orders([
                'customer' => $user_id,
                'limit'    => 1,
                'orderby'  => 'date',
                'order'    => 'ASC',
            ]);

            // If there's an order, update _first_order_date meta
            if (!empty($customer_orders)) {
                $order = $customer_orders[0];
                $order_date = $order->get_date_created();
                // $t = $order_date->getTimestamp();
                $t = $order_date->date('Y-m-d H:i:s');
                update_user_meta($user_id, '_first_order_date', $t);
                echo $user_id . ' - ' . $t . '<hr>';
            }
            // }

        }
        exit;
    });
}



add_filter('manage_users_sortable_columns', function ($columns) {
    $columns['visite'] = 'visite';
    $columns['user_registered'] = 'user_registered';
    $columns['first_order_date'] = 'first_order_date';
    $columns['last_order_date'] = 'last_order_date';
    return $columns;
});

add_filter('manage_users_columns', function ($columns) {

    $columns['votre_photo'] = 'Photo';
    $columns['visite'] = 'Visite';
    $columns['user_registered'] = 'Inscription';
    $columns['first_order_date'] = 'Première commande';
    $columns['last_order_date'] = 'Dernière commande';
    $columns['user_orders'] = 'Commandes';
    return $columns;
});

add_filter(
    'manage_users_custom_column',
    function ($value, $column_name, $user_id) {
        if ($column_name == 'votre_photo') {
            $photo = get_field('votre_photo', 'user_' . $user_id);
            $url = wp_get_attachment_url($photo);
            if ($url) {
                $value = '<a href="/polaroid/pdf.php?id=' . $user_id . '" target="_blank"><img src="' . $url . '" style="width:32px;height:32px;object-fit:cover;"></a>';
            }
        } else
        if ('user_registered' === $column_name) {
            $user = get_userdata($user_id);
            $value = date_francais($user->user_registered);
        } else
        if ('first_order_date' === $column_name) {
            $value = date_francais(get_user_meta($user_id, '_first_order_date', true));
        } else
        if ('visite' === $column_name) {
            $value = date_francais(get_user_meta($user_id, 'visite', true), true);
        } else       
          if ('last_order_date' === $column_name) {
            $value = date_francais(get_user_meta($user_id, '_last_order_date', true));
        } else if ($column_name === 'user_orders') {
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
                    if ($product) {
                        $product_name = $product->get_name();
                        $products_html .= '<li>&middot; ' . $product_name . '</li>';
                    }
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
    },
    10,
    3
);



// Modify the user query to sort by last order date
add_action('pre_get_users', function ($query) {
    global $pagenow;

    $orderby = $_GET['orderby'] ?? false;

    if (!$orderby) return;

    if ('users.php' !== $pagenow) return;


    if ($orderby == 'visite') {
        $query->set('meta_key', 'visite');
        $query->set('orderby', 'meta_value');
    } else if ($orderby == 'last_order_date') {
        $query->set('meta_key', '_last_order_date');
        $query->set('orderby', 'meta_value');
    } else if ($orderby == 'first_order_date') {
        $query->set('meta_key', '_first_order_date');
        $query->set('orderby', 'meta_value');
    }
});

// Update last order date when an order is completed
add_action('woocommerce_order_status_completed', function ($order_id) {
    $order = wc_get_order($order_id);
    $user_id = $order->get_user_id();
    $order_date = $order->get_date_created()->date('Y-m-d H:i:s');

    update_user_meta($user_id, '_last_order_date', $order_date);

    if (!get_user_meta($user_id, '_first_order_date', true)) {
        update_user_meta($user_id, '_first_order_date', $order_date);
    }
}, 10, 1);
