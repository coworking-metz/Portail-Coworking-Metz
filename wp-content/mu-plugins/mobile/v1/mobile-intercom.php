<?php

add_action('rest_api_init', function () {
    /**
     * Should open the door by opening trigger the intercom
     * Note: the door will be locked again after 5 seconds
     *
     * TODO: the tickets or Home Assistant should send back the timeout before locking the door
     */
    register_rest_route('mobile/v1', 'intercom', array(
        'methods'  => 'POST',
        'callback' => function ($request) {
            $userId = (new \JWTAuth\Auth())->validate_token(false)->data->user->id;

            $queryParams = http_build_query([
                'key' => API_KEY_TICKET,
            ]);
            $url = "https://tickets.coworking-metz.fr/api/interphone?$queryParams";
            $request = wp_remote_get($url);

            $responseDate = $request['http_response']->get_headers()['date'];
            $responseDateWithTimezone = (new DateTime($responseDate))->setTimezone(new DateTimeZone('Europe/Paris'));

            $data = json_decode($request['body']);
            // TODO: should log that the user opened the door to keep a history
            // in case of audit

            return rest_ensure_response(array(
              'triggered' => $responseDateWithTimezone->format('c'),
              'locked' => $responseDateWithTimezone->add(new DateInterval('PT5S'))->format('c'),
              'timeout' => 'PT5S'
            ));
        },
    ));
});
