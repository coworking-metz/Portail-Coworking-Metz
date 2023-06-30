<?php
add_action(
    'rest_api_init',
    function () {
        register_rest_route('cowo/v1', '/app-droits', array(
            'methods'  => 'POST',
            'callback' => function ($request) {

                coworking_app_check($request);

                $droits = coworking_app_droits($request['user_id']);

                if ($droits) {
                    $response = array('droits' => $droits);
                } else {
                    return new WP_Error('rest_user_invalid_id', 'Invalid user ID ', array('status' => 404));
                }

                return rest_ensure_response($response);
            },
        ));
    }
);
