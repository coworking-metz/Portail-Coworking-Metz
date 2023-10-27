<?php

/**
 * Empecher tout acces au backoffice pour les personnes qui n'ont pas un profil admin
 * Redirection vers la page "mon compte" du site public, sauf lors du tout premier login, 
 */
add_action('admin_init', function () {
    if (current_user_can('manage_options')) return;
    wp_redirect(get_permalink(get_option('woocommerce_myaccount_page_id')));
});

add_action('wp_login', function ($user_login, $user) {
    $uid = $user->ID;
    if (!is_first_login($uid)) return;
    if (in_array('administrator', $user->roles)) return;

    wp_redirect('/mon-compte/polaroid/');
    exit;
}, 99, 2);

function is_first_login($uid)
{
    return set_date_first_login($uid);
}
function set_date_first_login($uid)
{
    if (!get_user_meta($uid, '_first_login_date', true)) {
        update_user_meta($uid, '_first_login_date', date('Y-m-d H:i:s'));
        return true;
    }
    return true;
}
