<?php

add_action(
    'rest_api_init',

    function () {


        register_rest_route('cowo/v1', '/app-session', array(
            'methods'  => 'POST',
            'callback' => function ($request) {
                if ($sid = coworking_app_check($request)) {
                    $response = array('session' => $sid);
                } else {
                    return new WP_Error('session_error', 'Invalid session', array('status' => 401));
                }
                return rest_ensure_response($response);
            },
        ));
    }
);
