<?php

add_action(
    'rest_api_init',

    function () {


        register_rest_route('cowo/v1', '/app-session', array(
            'methods'  => 'POST',
            'callback' => function ($request) {
                if ($sid = coworking_app_check($request)) {
                    $response = array(
                        'session' => $sid,
                        // 'sessions' => coworking_app_get_sessions($request['user_id'])
                    );
                } else {
                    return new WP_Error('session_error', 'Invalid session', array('status' => 401));
                }
                return rest_ensure_response($response);
            },
        ));
    }
);

add_action(
    'rest_api_init',

    function () {


        register_rest_route('cowo/v1', '/app-session', array(
            'methods'  => 'DELETE',
            'callback' => function ($request) {
                if ($sid = coworking_app_check($request)) {
                    $response = array('status' => coworking_app_delete_session_id($sid, $request['user_id']));
                } else {
                    return new WP_Error('session_error', 'Invalid session', array('status' => 401));
                }
                return rest_ensure_response($response);
            },
        ));
    }
);
