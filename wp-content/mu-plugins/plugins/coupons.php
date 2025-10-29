<?php

/**
 * Restreint le coupon « BIENVENUE » aux nouveaux clients uniquement
 * et affiche des messages d'erreur explicites en français.
 */
add_filter('woocommerce_coupon_is_valid', function($valid, $coupon, $discounts) {

    if (!$valid) return false;

    if ($coupon->get_code() !== 'BIENVENUE') return $valid;

    $user_id = get_current_user_id();
    if (!$user_id) {
        return false;
    }

    $orders = wc_get_orders([
        'customer_id' => $user_id,
        'limit'       => 1,
        'return'      => 'ids',
    ]);

    if (!empty($orders)) {
        wc_add_notice('Ce code promo est réservé à votre première commande uniquement.', 'error');
        return false;
    }

    return true;
}, 10, 3);

