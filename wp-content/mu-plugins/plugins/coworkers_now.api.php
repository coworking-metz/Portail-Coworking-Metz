<?php
add_action(
    'rest_api_init',
    function () {
        register_rest_route('cowo/v1', '/coworkers_now', array(
            'methods'  => 'GET',
            'callback' => function () {
                $data = tickets('/current-members');
                            
                $number_coworkers = count($data);
                $number_worplaces = 40;
                $remaining_workplaces = $number_worplaces - $number_coworkers;

                $output = '';
                if ($number_coworkers == 0) {
                    $output = 'Pas de coworker actuellement. <span class="highlight-text">';
                } elseif ($number_coworkers == 1) {
                    $output = 'Actuellement <span class="highlight-text">' . $number_coworkers . ' </span>coworker présent.<br/><span class="highlight-text">' . $remaining_workplaces . '</span> postes de travail encore disponibles.';
                } else {
                    $output = 'Actuellement <span class="highlight-text">' . $number_coworkers . ' </span>coworkers présents.<br/><span class="highlight-text">' . $remaining_workplaces . '</span> postes de travail encore disponibles.';
                }
				$output.='<br><small>Information mise à jour à '.wp_date('H:i:s').'</small>';

                CoworkingMetz\CloudFlare::cacheHeaders(HOUR_IN_SECONDS/4);
                return ['content'=>$output];
            }
        ));
    }
);
