<?php


/**
 * @snippet       Redirect to Referrer @ WooCommerce My Account Login
 * @how-to        Get CustomizeWoo.com FREE
 * @author        Rodolfo Melogli, BusinessBloomer.com
 * @testedwith    WooCommerce 5
 * @community     https://businessbloomer.com/club/
 */

if ($redirect = $_GET['redirect'] ?? false) {
    add_action('woocommerce_login_form_end', function () use ($redirect) {

        echo '<input type="hidden" name="redirect" value="' . wp_validate_redirect($redirect ?? wc_get_raw_referer(), wc_get_page_permalink('myaccount')) . '" />';
    });

    add_action('login_form', function () use ($redirect) {
        echo '<input type="hidden" name="redirect" value="' . htmlentities($redirect) . '" />';
    });
}
add_filter('login_redirect', function ($redirect_to) {
    if (isset($_POST['redirect'])) {
        $redirect_to = site_url('redirect.php?url='.urlencode($_POST['redirect']));
    }
    return $redirect_to;
}, 1);
