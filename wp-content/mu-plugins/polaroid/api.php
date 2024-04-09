<?php
add_action(
    'rest_api_init',
    function () {
        register_rest_route('cowo/v1', '/polaroids', array(
            'methods'  => 'GET',
            'callback' => function ($request) {

                $response = [];
                if (get_field('activer_theme_trombi', 'option')) {
                    $response['fond'] = get_field('fond_trombi', 'option');
                }

                return rest_ensure_response($response);
            },
        ));
    }
);


add_action(
    'rest_api_init',
    function () {
        register_rest_route('cowo/v1', '/polaroid/(?P<user_id>\d+)', array(
            'methods'  => 'GET',
            'callback' => function ($request) {

                $params = $request->get_params();
                $user_id = $params['user_id'];
                $response = [];

                $ranking = get_user_ranking($user_id);
                $response = polaroid_get($user_id);
                $response['legacy'] = wp_get_attachment_url(get_user_meta($user_id, 'url_image_trombinoscope', true));
                $response['photo'] = pathTourl($response['photo']);
                $response['alpha'] = pathTourl($response['alpha']);
                $response['ranking']=$ranking;
                $response['options'] = get_field('polaroids', 'option');
                $response['options']['image_fond_pola'] = pathTourl(get_image_fond_pola());


                // $response['polaroid_default'] = coworking_app_settings()['polaroid_default']??'';
                return rest_ensure_response($response);
            },
        ));
    }
);
