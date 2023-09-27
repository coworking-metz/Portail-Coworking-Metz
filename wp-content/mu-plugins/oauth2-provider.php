<?php

/**
 * Redirect user to a custom login page
 * for OAuth authorization
 *
 * @see https://wp-oauth.com/docs/how-to/custom-login-page/
 */
if (wp_get_environment_type() != 'local') {
  add_action('wo_before_authorize_method', function () {
    add_filter('login_url', function ($login_url) {
      $login_url = site_url('mon-compte');
      return add_query_arg('redirect_to', urlencode(site_url($_SERVER['REQUEST_URI'])), $login_url);
    }, PHP_INT_MAX);
  });
}


/**
 * Ajouter le grant refresh_token dans tous les clients du plugin oauth-server
 */
add_action('admin_init', function () {
  // Récupérer tous les posts avec la meta_key 'grant_types'
  $args = [
    'post_type' => 'wo_client',
    'posts_per_page' => -1,
    'meta_key' => 'grant_types',
  ];

  $query = new WP_Query($args);

  foreach ($query->posts as $post) {
    $post_id = $post->ID;
    $meta_value = get_post_meta($post_id, 'grant_types', true);

    $changed = false;
    if (!is_array($meta_value)) {
      $meta_value = [];
    }

    if (!in_array('refresh_token', $meta_value)) {
      $meta_value[] = 'refresh_token';
      $changed = true;
    }

    // Mettre à jour si changé
    if ($changed) {
      update_post_meta($post_id, 'grant_types', $meta_value);
    }
  }

  wp_reset_postdata();
});
