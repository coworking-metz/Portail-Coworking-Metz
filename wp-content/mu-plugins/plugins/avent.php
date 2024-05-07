<?php


add_action(
    'rest_api_init',
    function () {
        register_rest_route('cowo/v1', '/avent_tirages', array(
            'methods'  => 'POST',
            'callback' => function ($request) {
                $data = json_decode(file_get_contents('php://input'), true);
                $value = $data['value'];

                $value = json_decode($value, true);


                $out = [];
                foreach ($value as $date => $user_id) {
                    $user = get_userdata($user_id);
                    $out[] = ['date' => $date, 'user' => ['name' => $user->data->user_nicename, 'email' => $user->data->user_email, 'id' => $user_id]];
                }
                return rest_ensure_response($out);
            },
        ));
    }
);
