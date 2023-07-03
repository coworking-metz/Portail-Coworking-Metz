<?php
add_action(
    'rest_api_init',
    function () {
        register_rest_route('cowo/v1', '/app-droits', array(
            'methods'  => 'POST',
            'callback' => function ($request) {

                coworking_app_check($request);

                $uid = $request['user_id'];

                $droits = coworking_app_droits($uid);


                if ($droits) {
                    if (user_can($uid, 'administrator')) {
                        $admin = true;
                    } else {
                        $admin = false;
                    }
                    $response = array('droits' => $droits, 'admin' => $admin);
                } else {
                    return new WP_Error('rest_user_invalid_id', 'Invalid user ID ', array('status' => 404));
                }

                return rest_ensure_response($response);
            },
        ));
    }
);
