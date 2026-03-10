<?php
// Schedule cron (daily)
add_action('init', function () {

    if (!wp_next_scheduled('cw_update_membre_actif_meta')) {
        wp_schedule_event(time(), 'daily', 'cw_update_membre_actif_meta');
    }

});


// Cron task
add_action('cw_update_membre_actif_meta', function () {

    $users = get_users([
        'fields' => ['ID']
    ]);

    foreach ($users as $user) {
		echo ".";
        $value = is_user_electeur($user->ID) ? 1 : 0;
        update_user_meta($user->ID, '_is_membre_actif', $value);

    }

	echo "\n";

});

add_action('pre_get_users', function ($query) {

    if (!is_admin()) {
        return;
    }

    global $pagenow;

    if ($pagenow !== 'users.php') {
        return;
    }

    if (!isset($_GET['membres_actifs'])) {
        return;
    }

    $query->set('meta_query', [
        [
            'key'   => '_is_membre_actif',
            'value' => 1,
            'compare' => '='
        ]
    ]);

});

add_filter('views_users', function ($views) {

    global $wpdb;

    $count = $wpdb->get_var("
        SELECT COUNT(user_id)
        FROM {$wpdb->usermeta}
        WHERE meta_key = '_is_membre_actif'
        AND meta_value = '1'
    ");

    $url = admin_url('users.php?membres_actifs');

    $class = (isset($_GET['membres_actifs'])) ? 'current' : '';

    $views['membres_actifs'] = "<a href='$url' class='$class'>Membres actifs (votants)</a> ($count)";

    return $views;

});

	add_filter('manage_users_custom_column', function($value, $column_name, $user_id) {


		if ($column_name === 'custom_name') {
			if(is_user_electeur($user_id)) {

				$value .= '<br><span style="color:green;font-size:12px;"><b>Membre actif</b> Peut voter et être candidat</span>';
			} else {
				$value .= '<br><span style="color:darkred;font-size:12px;"><b>Membre inactif</b> ne peut pas voter ni être candidat!</span>';
			}
		}

		return $value;

	}, 999, 3);
