<?php

use Httpful\Http;

// add_action('rest_api_init', function () {
//     register_rest_route('cowo/v1', '/nouvelle-visite', [
//         'methods' => 'GET',
//         'callback' => function ($request) {
//             coworking_app_check($request);
//             return rest_ensure_response(['event' => false]);
//         },
//     ]);
// });

add_action('rest_api_init', function () {
    register_rest_route('cowo/v1', '/nouvelle-visite', [
        'methods' => 'GET',
        'callback' => function ($request) {
            coworking_app_check($request);

            // // Allow from any origin
            // if (isset($_SERVER['HTTP_ORIGIN'])) {
            //     // Decide if the origin in $_SERVER['HTTP_ORIGIN'] is one
            //     // you want to allow, and if so:
            //     header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
            //     header('Access-Control-Allow-Credentials: true');
            //     header('Access-Control-Max-Age: 86400');    // cache for 1 day
            // }

            // // Access-Control headers are received during OPTIONS requests
            // if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {

            //     if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
            //         // may also be using PUT, PATCH, HEAD etc
            //         header("Access-Control-Allow-Methods: GET, POST, OPTIONS");

            //     if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
            //         header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");

            //     exit(0);
            // }



            $payload = $_GET['payload'] ?? false;
            if (!$payload) return;
            
            $params = json_decode(stripslashes($payload), true);

            $user = $params['user'] ?? false;

            if (empty($user['email']))
                return rest_ensure_response(['event' => false]);

            $nom = $user['prenom'] . ' ' . $user['nom'] . ' (' . $user['email'] . ')';
            $activite = $user['activite'];
            if($activite) {
                $nom.=' - '.$activite;
            }
            $date = new DateTime($params['visite'], new DateTimeZone('Europe/Paris'));
            $start = $date->format(DateTime::RFC3339);

            $date->modify('+30 minutes');
            $end = $date->format(DateTime::RFC3339);

            $event = ['name' => 'Visite pour ' . $nom, 'start' => $start, 'end' => $end];

            $user_id = create_wp_user_if_not_exists($user, ['visite' => date_maline($start), 'activite'=>$activite]);
            if ($user_id) {
                // addEventToCalendar($user_id, $event);

                envoyerMailAlerte($user_id, ['activite'=>$activite?$activite:'Non renseigné']);
                envoyerMailVisite($user_id, $params['visite']);

                return rest_ensure_response(['event' => $event]);
            }
        },
    ]);
});
