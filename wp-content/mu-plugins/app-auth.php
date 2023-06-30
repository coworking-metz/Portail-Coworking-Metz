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
                $user = wp_authenticate($credentials['user_login'], $credentials['user_password']);

                // Check if authentication succeeded
                if (!is_wp_error($user)) {
                    $response = array('user' => [
                        'login' => $user->user_login,
                        'id' => $user->ID
                    ]);
                } else {
                    return new WP_Error('authorization_failed', 'Invalid credentials ', array('status' => 401));
                }

                return rest_ensure_response($response);
            },
        ));
    }
);
