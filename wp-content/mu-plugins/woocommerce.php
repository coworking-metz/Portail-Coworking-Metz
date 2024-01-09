<?php

add_filter('woocommerce_available_payment_gateways', function ($available_gateways) {
    if (is_admin() && !defined('DOING_AJAX')) {
        return $available_gateways;
    }

    $user = wp_get_current_user();

    if (current_user_can('administrator')) return $available_gateways;

    if (get_field('payer_en_virement', $user)) return $available_gateways;

    unset($available_gateways['bacs']); // 'bacs' is the id for bank transfer

    return $available_gateways;
});


