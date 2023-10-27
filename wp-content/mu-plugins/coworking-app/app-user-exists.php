<?php

add_action('rest_api_init', function () {
    register_rest_route('cowo/v1', '/user-exists', [
        'methods' => 'GET',
        'callback' => function (WP_REST_Request $request) {
            coworking_app_check($request);

            $email = $request->get_param('email');
            if (!$email) {
                return new WP_Error('no_email', 'Email parameter is required', ['status' => 400]);
            }

            // Vérifier si l'email existe dans la base de données
            $user = get_user_by('email', $email);
            return rest_ensure_response(['exists' => $user ? true : false]);
        },
    ]);
});
