<?php

use Httpful\Http;

add_action('rest_api_init', function () {
    register_rest_route('cowo/v1', '/nouvelle-visite', [
        'methods' => 'POST',
        'callback' => function ($request) {
            coworking_app_check($request);

            $params = $request->get_json_params();
            $user = $params['user'];
            $nom = $user['prenom'] . ' ' . $user['nom'] . ' (' . $user['email'] . ')';


            $date = new DateTime($params['visite'], new DateTimeZone('Europe/Paris'));
            $start = $date->format(DateTime::RFC3339);

            $date->modify('+30 minutes');
            $end = $date->format(DateTime::RFC3339);

            $event = ['name' => 'Visite pour ' . $nom, 'start' => $start, 'end' => $end];

            $user_id = create_wp_user_if_not_exists($user, ['visite' => date_maline($start)]);
            if ($user_id) {
                addEventToCalendar($user_id, $event);

                envoyerMailVisite($user_id, $params['visite']);

                //			$success = WC_Shortcode_My_Account::retrieve_password(); envoyer le mail de setup

                return rest_ensure_response(['event' => $event]);
            }
        },
    ]);
});
