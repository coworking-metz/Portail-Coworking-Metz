<?php
add_action(
    'rest_api_init',
    function () {
        register_rest_route('cowo/v1', '/app-settings', array(
            'methods'  => ['GET','POST'],
            'callback' => function ($request) {

                coworking_app_check($request);

                $settings = coworking_app_settings();


                return rest_ensure_response($settings);
            },
        ));
    }
);
