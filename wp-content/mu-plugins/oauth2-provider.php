<?php

/**
 * Redirect user to a custom login page
 * for OAuth authorization
 *
 * @see https://wp-oauth.com/docs/how-to/custom-login-page/
 */
if (wp_get_environment_type() != 'local') {
  add_action( 'wo_before_authorize_method', function () {
    add_filter( 'login_url', function ( $login_url ) {
      $login_url = site_url('mon-compte');
      return add_query_arg('redirect_to', urlencode(site_url($_SERVER['REQUEST_URI'])), $login_url);
    }, PHP_INT_MAX);
  });
}
