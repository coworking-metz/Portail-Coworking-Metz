<?php

function get_users_with_photos() {
	// Vérifie si le résultat est déjà stocké dans un transient
    $cached_user_ids = get_transient('users_with_photos');
    if ($cached_user_ids !== false) {
        return $cached_user_ids;
    }


    $args = array(
        'meta_query' => array(
            array(
                'key'     => 'votre_photo',
                'value'   => '',
                'compare' => '!=',
            ),
        ),
    );

    $users = get_users();
	$attachements = [];
	foreach($users as $user) {
		$attachements[$user->ID] = $user->votre_photo??false;
	}
	asort($attachements);
	$attachements = array_reverse($attachements, true); 
	$user_ids = array_keys($attachements);

    // Stocke le résultat dans un transient pour 12 heures
    set_transient('users_with_photos', $user_ids, 12 * HOUR_IN_SECONDS);

	return ($user_ids);
	
}
function get_user_id($user)
{
    return get_post_id($user);
}


function get_users_by_email_list($email_list, $exclude = array(), $args=[]) {

    // Convertir les e-mails en IDs d'utilisateurs
    $user_ids = array();
    foreach ($email_list as $email) {
        $user = get_user_by('email', $email);
        if ($user) {
            $user_id = $user->ID;
            if(!in_array($user_id, $exclude)) {
                $user_ids[] = $user_id;
            }
        }
    }

    // Utiliser get_users pour récupérer les objets utilisateur.
    $args['include'] = $user_ids;

    $users = get_users($args);
    return $users;

}
