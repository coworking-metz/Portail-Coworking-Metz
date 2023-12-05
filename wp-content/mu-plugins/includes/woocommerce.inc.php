<?php


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
    } else {
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
