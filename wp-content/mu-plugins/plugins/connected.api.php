<?php

if (isset($_GET['is-connected'])) {
    add_action('init', function () {
        CoworkingMetz\CloudFlare::noCacheHeaders();
        if ($uid = get_current_user_id()) {
            $data = get_userdata($uid);
            $user = $data->data;
            $user->roles = $data->roles;
            unset($user->user_pass);
            unset($user->user_activation_key);

            $parts = explode(' ', $user->display_name);

            $user->firstName = $parts[0];
            $user->lastName = implode(' ', array_slice($parts, 1));
            $user->_first_order_date = date_francais(get_user_meta($user->ID, '_first_order_date', true));
            $user->hash = sha1($user->ID . AUTH_KEY);
        } else {

            $hash = $_COOKIE['session-hash'] ?? false;
            $cache = true;
            if (!$hash) {
                $cache = false;
                $hash = sha1(time() . AUTH_KEY);
                setcookie('session-hash', $hash);
            }
            $user = ['hash' => $hash, 'cache'=>$cache];
        }
        $payload = ['user' => $user];
        header('Content-type: application/json');
        echo json_encode($payload);
        exit;
    });
}
