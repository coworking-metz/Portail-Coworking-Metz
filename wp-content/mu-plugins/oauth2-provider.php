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
 * AJouter des données dans la sortie oauth
 */
add_filter('wo_me_resource_return', function ($data, $token) {
  $capabilities = $data['capabilities'];
  unset($data['capabilities']);
  unset($data['user_status']);
  $user_id = $data['ID'];
  // $bloquer_ouvrir_portail = get_field('bloquer_ouvrir_portail', 'user_' . $user_id);
  // $ouvrir_parking = get_field('ouvrir_parking', 'user_' . $user_id) || date('Ymd') < '20240101';
  // if (user_can($user_id, 'administrator')) {
  //   $data['admin'] = true;
  // }
  // $data['polaroid'] = polaroid_existe($user_id) ? polaroid_url($user_id, true) : false;
  // $data['photo'] = site_url('polaroid/'.$user_id.'-raw.jpg');
  // $data['droits'] = [
  //   'ouvrir_portail' => $bloquer_ouvrir_portail ? false : true,
  //   'ouvrir_parking' => $ouvrir_parking ? true : false
  // ];

  // $data['birthDate'] = get_field('date_naissance', 'user_'.$user_id);
  $subscriber = in_array('subscriber',$data['user_roles']);
  // unset($data['user_roles']);

  $tab = coworking_app_droits($user_id);
  $data['roles'] = [];

  if($subscriber && !$tab['guest']) {
    $data['roles'][]='inactive';
  } else {
    $polaroid = polaroid_url($user_id, true);
    unset($tab['droits']['polaroid']);
    unset($tab['droits']['admin']);
    $data['photos'] = ['polaroid'=>$polaroid, 'photo'=>site_url('polaroid/'.$user_id.'-raw.jpg')];
    $data['droits'] = $tab['droits'];

    if($tab['admin']??false) $data['roles'][]='admin';
    if($tab['guest']??false)  $data['roles'][]='guest';
    if($tab['externe']??false)  $data['roles'][]='external';
    if(!$tab['guest']??false && !$tab['externe']??false) $data['roles'][]='coworker';

    $data['visite'] = $tab['visite']??null;
  }
  // print_r([$data, $token]);
  return $data;
}, 10, 2);


/**
 * Ajouter le grant refresh_token dans tous les clients du plugin oauth-server
 * CHanger la valeur par defaut du refresh_token_lifetime
 */
add_action('admin_init', function () {

  /**
   * Edition automatique ed'un fichier du plugin oauth
   * Le but est de changer la valeur par defaut de refresh_token_lifetime pour le passer à 86400 secondes (10 jours)
   * Le plugin ne propose pas de le faire en dehors de son option payante
   */
  $files_to_edit = [
    '/wp-content/plugins/oauth2-provider/wp-oauth-main.php' => [
      'find'=>'=> 86400,',
      'replace'=>'=> 864000,',
    ],
    '/wp-content/plugins/oauth2-provider/library/class-wo-api.php' => [
      'find'=>'=> 86400,',
      'replace'=>'=> 864000,',
    ],
    '/wp-content/plugins/oauth2-provider/includes/admin/page-server-options.php'=>[
      'find'=>'placeholder="86400" disabled', 
      'replace'=>'placeholder="864000" disabled'
    ]
  ];

  foreach($files_to_edit as $file_to_edit => $data) {
    $file_to_edit = ABSPATH.$file_to_edit;
    if(file_exists($file_to_edit)) {
      $php = file_get_contents($file_to_edit);

      // $find = ;
      if(mb_substr_count($php, $data['find']) == 1) {
        $php = str_replace($data['find'], $data['replace'], $php);
        file_put_contents($file_to_edit, $php);
      }
    }
  }

  // Récupérer tous les posts de type wo_client (dédiées à oauth2) avec la meta_key 'grant_types', pour y ajouter 'refresh_token' s'il n'y est pas deja
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

    // Mettre à jour la valeur e grant_types si elle a été modifiée
    if ($changed) {
      update_post_meta($post_id, 'grant_types', $meta_value);
    }
  }

  wp_reset_postdata();
});
