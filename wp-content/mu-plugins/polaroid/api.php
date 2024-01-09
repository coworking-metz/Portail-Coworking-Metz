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
