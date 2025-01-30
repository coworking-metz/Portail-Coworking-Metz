<?php

/**
 * Récupère le nombre total de commandes WooCommerce pour un utilisateur WordPress donné.
 *
 * @param int $user_id ID de l'utilisateur WordPress.
 * @return int Nombre total de commandes.
 */
function get_user_order_count($user_id) {
    // Vérifie que l'ID de l'utilisateur est valide
    if (!is_numeric($user_id)) {
        return 0;
    }

    // Prépare la requête WP_Query pour les commandes
    $query_args = [
        'post_type'   => 'shop_order',
//        'post_status' => ['wc-completed', 'wc-processing', 'wc-on-hold', 'wc-pending'],
        'post_status' => ['wc-completed'],
        'meta_query'  => [
            [
                'key'     => '_customer_user',
                'value'   => $user_id,
                'compare' => '=',
            ],
        ],
        'fields'      => 'ids', // Récupérer uniquement les IDs pour optimiser
    ];

    // Compte le nombre de commandes
    $orders = get_posts($query_args);
    return count($orders);
}



/**
 * Check if a user has a valid membership and cache the result in a transient until the next 1st of January.
 *
 * @param int $user_id The user ID to check for membership validity.
 * @return bool True if the membership is valid, false otherwise.
 */
function has_valid_membership($user_id) {
    // Generate a unique transient key for this user.
    $transient_key = 'user_membership_is_valid_' . $user_id;

    // Attempt to retrieve the value from the transient.
    $cached_result = get_transient($transient_key);
    if ($cached_result !== false) {
        return $cached_result;
    }

    // Fetch the result via the tickets API.
    $result = tickets('/members/' . $user_id); 
    $is_valid = $result['membershipOk'] ?? false;

    // Calculate expiration date (next 1st of January).
    $current_time = time();
    $next_january = strtotime('first day of January next year', $current_time);
    $expiration = $next_january - $current_time;

	if($is_valid) {
		// Save the result in a transient.
		set_transient($transient_key, $is_valid, $expiration);
	}
    return $is_valid;
}



function current_user_can_tarif_reduit(){
    $user_id = get_current_user_id();
    $tarifs_reduits_ok = get_user_meta($user_id, 'tarifs_reduits_ok', true);
    return $tarifs_reduits_ok;
}
function get_date_naissance($user_id) {
    $d = get_field('date_naissance', 'user_'.$user_id);

    if(!$d) return '';

    $t = strtotime($d);
    if(!$t) return '';

    return date('Y-m-d', $t);
}
function get_user_ranking($uid) {

    $rankings = get_transient('user_rankings');
    if(!$rankings) {
        $api = TICKET_BASE_URL.'/users-stats?key=bupNanriCit1&period=last-365-days&sort=createdAt';
        $data = file_get_contents($api);

        $users = json_decode($data, true);

        $rankings=[];
        foreach($users as $user) {
            if(!$user['wpUserId']) continue;
            $rankings[$user['wpUserId']]=explode('-',$user['createdAt'])[0]??date('Y');
        }
        set_transient('user_rankings', $rankings, DAY_IN_SECONDS);
    }
    return $rankings[$uid] ?? false;

}
/**
 * Retourne les stats du compte utilisateur, dont la balance des tickets et ses infos abo, membership, etc
 *
 * @param  mixed $uid
 * @return mixed
 */
function get_user_balance($user) {
    $uid = get_post_id($user);
    // Vérifier si les résultats sont en cache
    $cached_result = get_transient('user_balance_' . $uid);
    $cached_result=false;
    if ($cached_result) {
        return $cached_result;
    }

    $user = get_userdata($uid);
    if (!$user) return;


	$response = file_get_contents(TICKET_BASE_URL.'/members/'.$uid.'?key='.API_KEY_TICKET); 


    if (!$response) return;

    $result = json_decode($response, true);

    // Stocker le résultat dans un transient pour 1 heure
    set_transient('user_balance_' . $uid, $result, HOUR_IN_SECONDS);

    return $result;
}


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
