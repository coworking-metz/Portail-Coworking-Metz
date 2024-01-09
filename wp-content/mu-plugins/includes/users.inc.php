<?php

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
