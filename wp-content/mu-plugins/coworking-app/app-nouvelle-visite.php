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



            $payload = $_GET['payload'] ?? false;
            if (!$payload) return;

            $params = json_decode(stripslashes($payload), true);

            $modeTest = $params['modeTest'] ?? false;
            $user = $params['user'] ?? false;

            if (empty($user['email']))
                return rest_ensure_response(['event' => false]);

            $nom = $user['prenom'] . ' ' . $user['nom'] . ' (' . $user['email'] . ')';
            $activite = $user['activite'];
            if ($activite) {
                $nom .= ' - ' . $activite;
            }
            if ($user['nomade']) {
                $user['role'] = 'customer';
                $user_id = create_wp_user_if_not_exists($user, ['activite' => $activite]);
                if ($user_id) {
					// TODO : envoyer un mail à la personne
                    set_transient('auto_login_' . $user_id, true, ONE_DAY);
                    return rest_ensure_response(['user_id' => $user_id]);
                }
            } else {
                $date = new DateTime($params['visite'], new DateTimeZone('Europe/Paris'));
                $start = $date->format(DateTime::RFC3339);

                $date->modify('+30 minutes');
                $end = $date->format(DateTime::RFC3339);

                $user_id = create_wp_user_if_not_exists($user, ['visite' => date_maline($start), 'activite' => $activite]);
                if ($user_id) {
                    $event = ['name' => 'Visite pour ' . $nom, 'start' => $start, 'end' => $end];

                    if (!$modeTest) {
                        envoyerMailAlerte($user_id, ['activite' => $activite ? $activite : 'Non renseigné']);
                        envoyerMailVisite($user_id, $params['visite']);
                    }

                    return rest_ensure_response(['event' => $event, 'user_id' => $user_id]);
                }
            }
        },
    ]);
});
