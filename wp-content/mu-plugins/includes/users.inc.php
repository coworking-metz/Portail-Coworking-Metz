<?php

/**
 * Retourne les stats du compte utilisateur, dont la balance des tickets et ses infos abo, membership, etc
 *
 * @param  mixed $uid
 * @return mixed
 */
function get_user_balance($uid) {
    // Vérifier si les résultats sont en cache
    $cached_result = get_transient('user_balance_' . $uid);
    $cached_result=false;
    if ($cached_result) {
        return $cached_result;
    }

    $user = get_userdata($uid);
    if (!$user) return;

    $email = $user->user_email;
    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => TICKET_BASE_URL.'/user-stats',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => 'key=' . API_KEY_TICKET . '&email=' . $email,
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/x-www-form-urlencoded'
        ),
    ));

    $response = curl_exec($curl);
    curl_close($curl);
    
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
