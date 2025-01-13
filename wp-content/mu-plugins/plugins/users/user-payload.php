<?php

add_filter('rest_prepare_user', function ($response, $user, $request) {
    // Vérifiez si le contexte de la requête est 'edit'
    if ('edit' === $request->get_param('context')) {
        $response->data['birthDate'] = get_date_naissance($user->ID);
        $response->data['trialDay'] = explode(' ',date_de_visite($user->ID))[0];
    }

    return $response;
}, 10, 3);
