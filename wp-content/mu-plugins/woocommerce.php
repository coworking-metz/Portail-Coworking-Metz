<?php


/**
 * Vérifie si un produit est dans le panier.
 * 
 * @param int $product_id L'ID du produit à vérifier.
 * @return bool Renvoie vrai si le produit est dans le panier, sinon faux.
 */
function is_product_in_cart($product_id)
{
    $cart = WC()->cart->get_cart();
    foreach ($cart as $cart_item) {
        if ($cart_item['product_id'] == $product_id || $cart_item['variation_id'] == $product_id) {
            return true;
        }
    }
    return false;
}


/**
 * Vérifie si un produit a été commandé récemment par l'utilisateur connecté.
 * 
 * @param int $product_id L'ID du produit à vérifier.
 * @return bool Renvoie vrai si le produit a été commandé récemment, sinon faux.
 */
function commande_recente($product_id)
{
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

    return false;
}
