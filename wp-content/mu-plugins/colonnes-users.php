<?php

/**
 * Ajoute des colonnes triables dans l'interface utilisateur du tableau des utilisateurs.
 * Date de la visite innitiale du coworking
 * Date d'inscription
 * Date de la première commande
 * Date de la dernière commande en date
 */
add_filter('manage_users_sortable_columns', function ($columns) {
    $columns['visite'] = 'visite';
    $columns['date_naissance'] = 'date_naissance';
    $columns['first_login_date'] = 'first_login_date';
    $columns['user_registered'] = 'user_registered';
    $columns['first_order_date'] = 'first_order_date';
    $columns['last_order_date'] = 'last_order_date';
    return $columns;
});

/**
 * Ajoute des colonnes supplémentaires dans l'interface utilisateur du tableau des utilisateurs.
 * Photo du pola
 * Date de la visite innitiale du coworking
 * Date d'inscription
 * Date de la première commande
 * Date de la dernière commande en date
 * Details de la derniere commande + lien vers les commandes
 */
add_filter('manage_users_columns', function ($columns) {

    // $columns['payer_en_virement'] = 'Peut payer en virement bancaire ?';
    $columns['votre_photo'] = 'Photo';
    $columns['date_naissance'] = 'Anniversaire';
    $columns['visite'] = 'Visite';
    $columns['first_login_date'] = 'Premier login';
    $columns['user_registered'] = 'Inscription';
    $columns['first_order_date'] = 'Première commande';
    $columns['last_order_date'] = 'Dernière commande';
    $columns['user_orders'] = 'Commandes';
    unset($columns['posts']);
    return $columns;
});

/**
 * Remplir les nouvelles colonnes du tableau des utilisateurs avec des données.
 */
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
        if ('date_naissance' === $column_name) {
            $value = get_field('date_naissance', 'user_'.$user_id);
        } else
        // if ('payer_en_virement' === $column_name) {
        //     $value = get_field('payer_en_virement', 'user_'.$user_id) ? '<strong>Oui</strong>' : '';
        // } else
        if ('user_registered' === $column_name) {
            $user = get_userdata($user_id);
            $value = date_francais($user->user_registered);
        } else
        if ('first_login_date' === $column_name) {
            $value = date_francais(get_user_meta($user_id, '_first_login_date', true));
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



/**
 * Modifie la requête utilisateur pour trier les colonnes supplémentaires
 */
add_action('pre_get_users', function ($query) {
    global $pagenow;

    $orderby = $_GET['orderby'] ?? false;

    if (!$orderby) return;

    if ('users.php' !== $pagenow) return;


    if ($orderby == 'visite') {
        $query->set('meta_key', 'visite');
        $query->set('orderby', 'meta_value');
    } else if ($orderby == 'date_naissance') {
        $query->set('meta_key', 'date_naissance');
        $query->set('orderby', 'meta_value');
    } else if ($orderby == 'last_order_date') {
        $query->set('meta_key', '_last_order_date');
        $query->set('orderby', 'meta_value');
    } else if ($orderby == 'first_order_date') {
        $query->set('meta_key', '_first_order_date');
        $query->set('orderby', 'meta_value');
    } else if ($orderby == 'first_login_date') {
        $query->set('meta_key', '_first_login_date');
        $query->set('orderby', 'meta_value');
    }
});

/**
 * Met à jour les métadonnées "_last_order_date" et "_first_order_date" lorsqu'une commande est complétée.
 */
add_action('woocommerce_order_status_completed', function ($order_id) {
    $order = wc_get_order($order_id);
    $user_id = $order->get_user_id();
    $order_date = $order->get_date_created()->date('Y-m-d H:i:s');

    update_user_meta($user_id, '_last_order_date', $order_date);

    if (!get_user_meta($user_id, '_first_order_date', true)) {
        update_user_meta($user_id, '_first_order_date', $order_date);
    }
}, 10, 1);




/**
 * Met à jour le méta "_last_order_date" pour tous les utilisateurs.
 * S'exécute lorsque le paramètre GET "_last_order_date" est défini.
 */
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

/**
 * Met à jour le méta "_first_order_date" pour tous les utilisateurs.
 * S'exécute lorsque le paramètre GET "_first_order_date" est défini.
 */
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




/**
 * Met à jour le méta "_first_login_date" pour tous les utilisateurs.
 * S'exécute lorsque le paramètre GET "_first_login_date" est défini.
 */
if (isset($_GET['_first_login_date'])) {
    add_action('admin_init', function () {
        // Get all users
        $args = [
            'fields' => 'ID',  // Only need ID field for performance
        ];
        $users = get_users($args);
        foreach ($users as $user_id) {
            if(get_user_meta($user_id, '_first_login_date', true)) continue;
            $user = get_userdata($user_id);
            $t = $user->user_registered;

            update_user_meta($user_id, '_first_login_date', $t);
            echo $user_id . ' - ' . $t . '<hr>';
        }
        exit;
    });
}
