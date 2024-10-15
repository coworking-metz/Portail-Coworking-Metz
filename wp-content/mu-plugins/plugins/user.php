<?php
add_filter('pre_user_query', function ($user_query) {
    global $wpdb;

    if (isset($_GET['pola']) && $_GET['pola'] === 'true') {
        $user_query->query_from .= " LEFT JOIN $wpdb->usermeta ON $wpdb->usermeta.user_id = $wpdb->users.ID AND $wpdb->usermeta.meta_key = 'votre_photo'";
        $user_query->query_from .= " LEFT JOIN $wpdb->posts ON $wpdb->posts.ID = $wpdb->usermeta.meta_value";

        $user_query->query_where .= " AND $wpdb->usermeta.meta_value != ''"; // Ensuring the meta_value is not empty

        $user_query->query_orderby = " ORDER BY $wpdb->posts.post_date DESC"; // Sorting users by attachment upload date
    }
});


add_action('profile_update', function ($user_id, $old_user_data) {
    $webhook_url = TICKET_BASE_URL.'/sync-user-webhook?key='.API_KEY_TICKET.'&wpUserId=' . $user_id;

    $response = wp_remote_post($webhook_url, array(
        'method'    => 'POST',
    ));
    if (is_wp_error($response)) {
        // Handle error accordingly
        error_log('Error calling webhook: ' . $response->get_error_message());
    }
}, 99, 2);



add_filter( 'user_row_actions', function( $actions, $user ) {
    $actions['manager'] = '<a target="_blank" href="' . esc_url( 'https://manager.coworking-metz.fr/members/'.$user->ID ) . '">Fiche dans manager</a>';
    return $actions;
}, 10, 2 );


/**
 * Finaliser un compte
 */
add_action('admin_bar_menu', function ($admin_bar) {
    if (!is_admin()) return;
    $screen = get_current_screen();
    if ($screen->base != 'user-edit') return;
    $user_id = $_GET['user_id'] ?? false;
    if (!$user_id) return;
    $user_info = get_userdata($user_id);
    $roles = $user_info->roles;
    if (in_array('subscriber', $roles) || in_array('bookmify-customer', $roles)) return;

    

    $admin_bar->add_menu(array(
        'id'    => 'manager',
        'title' => 'Voir la fiche dans manager',
        'href'  => esc_url( 'https://manager.coworking-metz.fr/members/'.$user_id ),
        'meta'  => array(
            'target' => '_blank'
        ),
    ));
    $admin_bar->add_menu(array(
        'id'    => 'pola_pdf',
        'title' => 'Téléchager le pdf du polaroid',
        'href'  => esc_url( 'https://photos.coworking-metz.fr/'.$user_id.'.pdf' ),
        'meta'  => array(
            'target' => '_blank'
        ),
    ));
}, 100);