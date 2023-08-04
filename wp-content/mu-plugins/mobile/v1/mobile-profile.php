<?php

add_action('rest_api_init', function () {
    register_rest_route('mobile/v1', 'profile', array(
        'methods'  => 'GET',
        'callback' => function ($request) {
            $userId = (new \JWTAuth\Auth())->validate_token(false)->data->user->id;
            $email = (new \JWTAuth\Auth())->validate_token(false)->data->user->email;

            $userPresencesQueryParams = http_build_query([
                'key' => API_KEY_TICKET,
                'email' => $email,
            ]);
            // $userPresencesUrl = "https://tickets.coworking-metz.fr/api/users/$userId/user-stats?$userPresencesQueryParams";
            $userPresencesUrl = "https://tickets.coworking-metz.fr/api/user-stats?$userPresencesQueryParams";
            $userPresencesData = json_decode(wp_remote_get($userPresencesUrl)['body']);

            $ongoingSubscription = current(array_filter($userPresencesData->abos, function ($subscription) {
                return $subscription->current;
            }));

            $url_image = get_user_meta($userId, $key = 'url_image_trombinoscope', $single = true );
            $image_array = wp_get_attachment_image_src($url_image);
            $image_url = $image_array[0];

            $profile = array(
                'id' => $userId,
                'picture' => $image_url,
                'email' => $userPresencesData->email,
                'firstname' => $userPresencesData->firstName,
                'lastname' => $userPresencesData->lastName,
                'balance' => $userPresencesData->balance,
                'subscription' => $ongoingSubscription ? array(
                    'startDate' => $ongoingSubscription->aboStart,
                    'endDate' => $ongoingSubscription->aboEnd,
                    'purchased' => $ongoingSubscription->purchaseDate,
                ) : null,
            );

            return rest_ensure_response($profile);
        },
    ));

    register_rest_route('mobile/v1', 'profile/presence', array(
        'methods'  => 'GET',
        'callback' => function ($request) {
            $userId = (new \JWTAuth\Auth())->validate_token(false)->data->user->id;
            $email = (new \JWTAuth\Auth())->validate_token(false)->data->user->email;

            $userPresencesQueryParams = http_build_query([
                'key' => API_KEY_TICKET,
                'email' => $email,
            ]);
            // $userPresencesUrl = "https://tickets.coworking-metz.fr/api/users/$userId/user-presences?$userPresencesQueryParams";
            $userPresencesUrl = "https://tickets.coworking-metz.fr/api/user-presences?$userPresencesQueryParams";
            $userPresencesData = json_decode(wp_remote_get($userPresencesUrl)['body']);

            $presences = array(
                'timeline' => array_map(function ($userPresence) {
                    $type = 'UNKNOWN';
                    if ($userPresence->type === 'A') { // 'A' for 'Abonnement'
                        $type = 'SUBSCRIPTION';
                    } elseif ($userPresence->type === 'T') { // 'T' for 'Tickets'
                        $type = 'BALANCE';
                    }
                    return array(
                        'date' => $userPresence->date,
                        'amount' => $userPresence->amount,
                        'type' => $type,
                    );
                }, $userPresencesData),
            );

            return rest_ensure_response($presences);
        },
    ));
});
