<?php
add_action(
    'rest_api_init',
    function () {
        register_rest_route('cowo/v1', '/coworkers_now', array(
            'methods'  => 'GET',
            'callback' => function () {
                return ['content' => coworkers_now(true)];
            }
        ));
    }
);
