<?php



/**
 * Vérifie si un produit dans le panier a le méta 'adhesion_inclue' égal à 1.
 *
 * @return bool Retourne true si un produit correspondant est trouvé, sinon false.
 */
function is_adhesion_in_cart() {
    if (WC()->cart && WC()->cart->get_cart()) {
        foreach (WC()->cart->get_cart() as $cart_item) {
            $adhesion_inclue = get_post_meta($cart_item['product_id'], 'adhesion_inclue', true);
            if ($adhesion_inclue) {
                return true;
            }
        }
    }
    return false;
}

/**
 * Supprime un produit du panier à partir de son ID produit.
 *
 * @param int $product_id L'ID du produit à supprimer du panier.
 */
function remove_product_from_cart($product_id) {
    if (WC()->cart && WC()->cart->get_cart()) {
        foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {
            if ($cart_item['product_id'] == $product_id) {
                WC()->cart->remove_cart_item($cart_item_key);
                break;
            }
        }
    }
}


/**
 * Récupère toutes les commandes d'un utilisateur contenant un produit d'une catégorie donnée.
 *
 * @param int $user_id ID de l'utilisateur.
 * @param string $category_slug Slug de la catégorie produit.
 * @return array Liste des objets commandes correspondant.
 */
function get_user_orders_with_product_category($user_id, $category_slug) {
    if (!$user_id) {
        return [];
    }

    $orders = wc_get_orders([
        'customer_id' => $user_id,
        'status'      => ['wc-completed', 'wc-processing', 'wc-on-hold'], // Statuts pertinents
        'limit'       => -1,
    ]);

    $filtered_orders = [];

    foreach ($orders as $order) {
        foreach ($order->get_items() as $item) {
            $product_id = $item->get_product_id();
            if (has_term($category_slug, 'product_cat', $product_id)) {
                $filtered_orders[] = $order;
                break;
            }
        }
    }

    return $filtered_orders;
}


/**
 * Get the number of WooCommerce orders made by the current connected user
 *
 * @return int The number of orders
 */
function get_current_user_order_count() {
    // Get the current user's ID
    $user_id = get_current_user_id();
    
    if ( $user_id === 0 ) {
        return 0; // Return 0 if no user is logged in
    }

    // Query WooCommerce orders for the current user
    $order_query = new WC_Order_Query( array(
        'customer_id' => $user_id,
        'return'      => 'ids', // Only get the order IDs
    ) );

    $orders = $order_query->get_orders();

    // Return the number of orders
    return count( $orders );
}

/**
 * Récupère les commandes WooCommerce selon les numéros de commande personnalisés spécifiés.
 *
 * Cette fonction interroge les commandes WooCommerce en cherchant des métadonnées spécifiques,
 * '_alg_wc_full_custom_order_number', qui correspondent à n'importe quelle valeur dans le tableau
 * fourni. Selon le paramètre 'makeArrayAssoc', la fonction peut retourner un tableau associatif
 * où les clés sont les valeurs de '_alg_wc_full_custom_order_number' pour chaque commande.
 *
 * @param array $order_numbers Un tableau des valeurs de numéro de commande à rechercher.
 * @param bool $makeArrayAssoc (optionnel) Si vrai, retourne un tableau associatif basé sur les numéros de commande.
 * @return array|null Retourne un tableau d'objets de commande ou un tableau associatif si 'makeArrayAssoc' est vrai,
 *                     null si WooCommerce n'est pas actif ou aucun ordre n'est trouvé.
 */

function get_orders_by_custom_order_numbers($order_numbers, $makeArrayAssoc = false) {
    // Ensure WooCommerce is active
    if (!function_exists('wc_get_orders')) {
        return null;
    }

    $args = [
        'limit'        => -1, // Retrieve all matching orders
        'meta_key'     => '_alg_wc_full_custom_order_number',
        'meta_value'   => $order_numbers,
        'meta_compare' => 'IN',
    ];

    $orders = wc_get_orders($args);
    if ($makeArrayAssoc) {
        $assoc_orders = [];
        foreach ($orders as $order) {
            $custom_order_number = $order->get_meta('_alg_wc_full_custom_order_number');
            $assoc_orders[$custom_order_number] = $order;
        }
        return $assoc_orders;
    }
    return $orders;
}




function convertProductType($product_type) {
    if($product_type == 'carnet-tickets') return 'ticketsBook';
    if($product_type == 'ticket-unite') return 'singleTicket';
    if($product_type == 'abonnement') return 'subscription';
    if($product_type == 'adhesion') return 'membership';
}
/**
 * Récupère les métadonnées d'une commande WooCommerce spécifiée par son ID.
 *
 * Cette fonction charge une commande à l'aide de son ID, puis récupère toutes les métadonnées
 * associées à cette commande. Chaque métadonnée est ensuite ajoutée à un tableau associatif,
 * où chaque clé est le nom de la métadonnée et chaque valeur est la valeur de la métadonnée.
 *
 * @param int $order_id L'ID de la commande pour laquelle récupérer les métadonnées.
 * @return array Un tableau associatif des métadonnées de la commande, où chaque clé est
 *               le nom de la métadonnée et chaque valeur est la valeur de la métadonnée.
 */

 function get_order_meta_data($order_id) {
    $order = wc_get_order($order_id);
    $meta_data = $order->get_meta_data();
    $meta_data_array = array();

    foreach ($meta_data as $meta) {
        // Convert the meta object to an array and access its elements
        $meta_data_array[$meta->get_data()['key']] = $meta->get_data()['value'];
    }

    return $meta_data_array;
}

/**
 * Vérifie si un produit est dans le panier.
 * 
 * @param int $product_id L'ID du produit à vérifier. Peut etre un tableau d'ids
 * @return bool Renvoie vrai si le produit est dans le panier, sinon faux.
 */
function is_product_in_cart($product_id)
{
    if (is_array($product_id)) {
        foreach ($product_id as $id) {
            if (is_product_in_cart($id)) {
                return true;
            }
        }
    } else if(WC()->cart){
        $cart = WC()->cart->get_cart();
        foreach ($cart as $cart_item) {
            if ($cart_item['product_id'] == $product_id || $cart_item['variation_id'] == $product_id) {
                return true;
            }
        }
    }
    return false;
}


/**
 * Vérifie si un produit a été commandé récemment par l'utilisateur connecté.
 * 
 * @param int $product_id L'ID du produit à vérifier. peut être un tableau d'ids
 * @return bool Renvoie vrai si le produit a été commandé récemment, sinon faux.
 */
function commande_recente($product_id)
{

    if (is_array($product_id)) {
        foreach ($product_id as $id) {
            if (commande_recente($id)) {
                return true;
            }
        }
    } else {
        if (!is_user_logged_in()) return false;

        $user_id = get_current_user_id();
        $date_one_month_ago = date('Y-m-d', strtotime('-15 days'));

        $args = array(
            'customer_id' => $user_id,
            'date_after'  => $date_one_month_ago,
            'return'      => 'ids',
        );

        $order_ids = wc_get_orders($args);

        foreach ($order_ids as $order_id) {
            $order = wc_get_order($order_id);
            foreach ($order->get_items() as $item) {
                if ($item->get_product_id() == $product_id || $item->get_variation_id() == $product_id) {
                    return true;
                }
            }
        }
    }
    return false;
}


/**
 * Récupère tous les produits WooCommerce dont la méta 'contribution-cafe-the' est définie à 1 ou true.
 * 
 * @return WP_Post[] Tableau des produits correspondants.
 */
function get_products_with_contribution_cafe_the($return_ids = false)
{
    $args = array(
        'post_type' => 'product',
        'posts_per_page' => -1,
        'meta_query' => array(
            array(
                'key' => 'contribution-cafe-the',
                'value' => array('1', 'true'),
                'compare' => 'IN'
            )
        )
    );

    $query = new WP_Query($args);
    $ret = $query->posts;

    if ($return_ids) {
        $ret = array_column($ret, 'ID');
    }

    return $ret;
}
