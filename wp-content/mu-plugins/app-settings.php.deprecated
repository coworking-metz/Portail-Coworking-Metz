<?php
add_action(
    'rest_api_init',
    function () {
        register_rest_route('cowo/v1', '/app-settings', array(
            'methods'  => 'POST',
            'callback' => function ($request) {

                coworking_app_check($request);

                $url = 'https://tickets.coworking-metz.fr/api/current-users?key=' . API_KEY_TICKET . '&delay=15';
                $data = file_get_contents($url);
                $presences = json_decode($data, true);

                $settings = ['occupation' => ['total' => 29, 'presents' => count($presences)]];

                $response = array('settings' => $settings);

                return rest_ensure_response($response);
            },
        ));
    }
);
