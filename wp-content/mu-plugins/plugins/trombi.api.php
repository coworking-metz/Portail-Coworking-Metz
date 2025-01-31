<?php
add_action(
    'rest_api_init',
    function () {
        register_rest_route('cowo/v1', '/trombi', array(
            'methods'  => 'GET',
            'callback' => function ($request) {
				\CoworkingMetz\CloudFlare::cacheHeaders(60*5);
                $response = [];
                if (get_field('activer_theme_trombi', 'option')) {
                    $response['couleur_du_texte'] = get_field('couleur_du_texte', 'option');
                    $response['fond'] = get_field('fond_trombi', 'option');
                }
                if (get_field('activer_avent', 'option')) {
                    $response['avent'] = get_field('avent', 'option');
                    foreach(['debut_avent', 'fin_avent'] as $date) {
                        $obj = DateTime::createFromFormat('d/m/Y', $response['avent'][$date]);
                        $response['avent'][$date] = $obj->format('Y-m-d');
                    }
                }
                $response['polaroids'] = get_field('polaroids', 'option');
                $response['image_fond_pola'] = get_field('image_fond_pola', 'option');
                $response['divers'] = get_field('divers', 'option');
                $visites = [];
                $users = fetch_users_with_visite_today();
                foreach($users as $user) {
                    $visites[] = ['wpUserId'=>$user->ID, 'name'=>$user->display_name, 'visite'=>$user->visite];
                }
                $response['visites'] = $visites;

                $nomades = [];
                $users = fetch_nomades_for_today();
                foreach($users as $user) {
                    $nomades[] = ['wpUserId'=>$user->ID, 'name'=>$user->display_name, 'nomade'=>true];
                }
                $response['nomades'] = $nomades;

                return rest_ensure_response($response);
            },
        ));
        register_rest_route('cowo/v1', '/trombi/avent/tirage', array(
            'methods'  => 'POST',
            'callback' => function ($request) {
                $data = json_decode(file_get_contents('php://input'), true);
                $user_id = $data['user_id'] ?? false;
                $date_tirage = $data['date_tirage'] ?? false;

                $tirages = avent_add_tirage($user_id, $date_tirage);
                if ($tirages) {
                    avent_email_alerte($user_id, $date_tirage);
                    return rest_ensure_response($tirages);
                }
            },
        ));
        register_rest_route('cowo/v1', '/trombi/avent/tirage', array(
            'methods'  => 'GET',
            'callback' => function ($request) {
                // update_field('avent_tirages_' . date('Y'), null, 'option');

                $date_tirage = $_GET['date_tirage'] ?? false;
                $tirages = avent_get_tirages();
                return rest_ensure_response($tirages[$date_tirage] ?? false);
            },
        ));
        register_rest_route('cowo/v1', '/trombi/avent/tirages', array(
            'methods'  => 'GET',
            'callback' => function ($request) {

                $tirages = array_slice(avent_get_tirages(), 0, 10, true);
                return rest_ensure_response($tirages);
            },
        ));
    }
);
