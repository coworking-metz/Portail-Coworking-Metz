<?php
add_action(
    'rest_api_init',
    function () {
        register_rest_route('cowo/v1', '/polaroids', array(
            'methods'  => 'GET',
            'callback' => function ($request) {

                $response = [];
                if (get_field('activer_theme_trombi', 'option')) {
                    $response['fond'] = get_field('fond_trombi', 'option');
                }

                return rest_ensure_response($response);
            },
        ));
    }
);


add_action(
    'rest_api_init',
    function () {
        register_rest_route('cowo/v1', '/polaroid/(?P<user_id>\d+)', array(
            'methods'  => 'GET',
            'callback' => function ($request) {

                $params = $request->get_params();
                $user_id = $params['user_id'];
                $response = [];

                $ranking = get_user_ranking($user_id);
                $response = polaroid_get($user_id, false);
                $response['legacy'] = wp_get_attachment_url(get_user_meta($user_id, 'url_image_trombinoscope', true));
                $response['ranking']=$ranking;
                $response['options'] = get_field('polaroids', 'option');
                foreach($response['options'] as $k => $v) {
                    $prefix = explode('_',$k)[0];
                    if($prefix == 'photo') {
                        $response['options'][$k.'_alpha']=generer_image_alpha($v);
                    }
                }

                $response['options']['image_fond_pola'] = pathTourl(get_image_fond_pola());


                if($response['photo']) {
                    $response['photo'] = pathTourl($response['photo']);
                } else if($response['visite']) {
                    $response['photo'] = $response['options']['photo_visiteur'];
                } else {
                    $response['photo'] = $response['options']['photo_par_defaut'];
                }
                if($response['alpha']) {
                    $response['alpha'] = pathTourl($response['alpha']);
                } else if($response['visite']) {
                    $response['alpha'] = $response['options']['photo_visiteur_alpha'];
                } else {
                    $response['alpha'] = $response['options']['photo_par_defaut_alpha'];
                }


                // $response['polaroid_default'] = coworking_app_settings()['polaroid_default']??'';
                return rest_ensure_response($response);
            },
        ));
    }
);
