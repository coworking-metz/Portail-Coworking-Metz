<?php
add_action(
    'rest_api_init',
    function () {
        register_rest_route('cowo/v1', '/coworkers_now', array(
            'methods'  => 'GET',
            'callback' => function () {
                $api = TICKET_BASE_URL.'/current-members?key='.API_KEY_TICKET;
                $json = file_get_contents($api);
                $data = json_decode($json);
                            
                $number_coworkers = count($data);
                $number_worplaces = 28;
                $remaining_workplaces = $number_worplaces - $number_coworkers;

                $output = '';
                if ($number_coworkers == 0) {
                    $output = 'Pas de coworker actuellement. <span class="highlight-text">';
                } elseif ($number_coworkers == 1) {
                    $output = 'Actuellement <span class="highlight-text">' . $number_coworkers . ' </span>coworker présent.<br/><span class="highlight-text">' . $remaining_workplaces . '</span> postes de travail encore disponibles.';
                } else {
                    $output = 'Actuellement <span class="highlight-text">' . $number_coworkers . ' </span>coworkers présents.<br/><span class="highlight-text">' . $remaining_workplaces . '</span> postes de travail encore disponibles.';
                }

                CoworkingMetz\CloudFlare::cacheHeaders(HOUR_IN_SECONDS);
                return ['content'=>$output];
            }
        ));
    }
);
