<?php
add_action(
    'rest_api_init',
    function () {
        register_rest_route('cowo/v1', '/stats', array(
            'methods'  => 'GET',
            'callback' => function () {
                CoworkingMetz\CloudFlare::cacheHeaders(WEEK_IN_SECONDS);
                return json_decode(file_get_contents(TICKET_URL.'/stats'), true);
            }
        ));
    }
);
