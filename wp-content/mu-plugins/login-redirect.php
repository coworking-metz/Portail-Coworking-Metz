<?php


/**
 * @snippet       Redirect to Referrer @ WooCommerce My Account Login
 * @how-to        Get CustomizeWoo.com FREE
 * @author        Rodolfo Melogli, BusinessBloomer.com
 * @testedwith    WooCommerce 5
 * @community     https://businessbloomer.com/club/
 */

add_action('woocommerce_login_form_end', function () {

    echo '<input type="hidden" name="redirect" value="' . wp_validate_redirect($_GET['redirect'] ?? wc_get_raw_referer(), wc_get_page_permalink('myaccount')) . '" />';
});