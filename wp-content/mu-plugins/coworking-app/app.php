<?php

// In order to whitelist endpoints, I rolled back the changes from
// this commit https://github.com/usefulteam/jwt-auth/commit/84087733a6ed087df2ca53e6b4be767854754eb5
// The added code to `class-auth.php` should be removed once the cowo/v1 endpoints are removed.
add_filter(
	'jwt_auth_whitelist',
	function ( $endpoints ) {
		$your_endpoints = array(
            '/api-json-wp/cowo/v1/*',
        );
        return array_unique( array_merge( $endpoints, $your_endpoints ) );
	}
);

function coworking_app_settings()
{

    $settings = get_transient('coworking-app-settings');
    if (!$settings) {
        $url = 'https://tickets.coworking-metz.fr/api/current-users?key=' . API_KEY_TICKET . '&delay=15';
        $data = file_get_contents($url);
        $presences = json_decode($data, true);

        $settings = ['occupation' => ['total' => 29, 'presents' => count($presences)]];
        set_transient('coworking', $settings, 60 * 5);
    }
    return $settings;
}

function coworking_app_gen_session_id($uid, $expiry_in_days = 30)
{
    $sessions = coworking_app_get_sessions($uid);
    $session_id = wp_generate_password(30, false);

    // Calculate expiry date
    $expiry_date = date('Y-m-d H:i:s', strtotime("+$expiry_in_days days"));

    // Save session id with its expiry date
    $sessions[$session_id] = $expiry_date;

    update_user_meta($uid, 'sessions', json_encode($sessions));
    return $session_id;
}

function coworking_app_get_sessions($uid)
{

    if (!$uid) return;
    $sessions = get_user_meta($uid, 'sessions', true);
    if (empty($sessions)) {
        $sessions = [];
    } else {
        $sessions = json_decode($sessions, true);

        // Check if $sessions is a numerically indexed array (not associative)
        if (array_values($sessions) === $sessions) {
            $sessions = [];  // Reset to empty array
        } else {
            $current_time = time();
            foreach ($sessions as $session_id => $expiry_time) {
                // If the session has expired, remove it from the array
                if (strtotime($expiry_time) <= $current_time) {
                    unset($sessions[$session_id]);
                }
            }
        }
    }

    return $sessions;
}


function coworking_app_get_valid_sessions($uid)
{
    $sessions = coworking_app_get_sessions($uid);

    // Iterate over sessions and only keep the ones that have not expired
    $valid_sessions = [];
    foreach ($sessions as $session_id => $expiry_time) {
        if (strtotime($expiry_time) > time()) {
            $valid_sessions[] = $session_id;
        }
    }
    return $valid_sessions;

}

function coworking_app_droits($user_id)
{

    $user = get_user_by('ID', $user_id);
    if (!$user) return;

    $bloquer_ouvrir_portail = get_field('bloquer_ouvrir_portail', 'user_' . $user_id);


    if (user_can($user_id, 'administrator')) {
        $admin = true;
    } else {
        $admin = false;
    }

    return [
        'admin' => $admin,
        // 'sessions'=>coworking_app_get_sessions($user_id),
        'settings' => coworking_app_settings(),
        'droits' => [
            'admin' => $admin,
            'ouvrir_portail' => $bloquer_ouvrir_portail ? false : true,
        ]
    ];
}
function coworking_app_origins()
{
    return array(
        '',
        'http://127.0.0.1:5173',
        'https://melodious-entremet-ad9165.netlify.app',
        'https://app.coworking-metz.fr'
    );
}


function coworking_app_check($request)
{


    $origin = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : '';

    if (in_array($origin, coworking_app_origins())) {
        header('Access-Control-Allow-Origin: ' . $origin);
        header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
        header('Access-Control-Allow-Headers: Authorization, Content-Type');
    } else {
        http_response_code(403);
        exit('Forbidden');
    }

    // Check if it's a preflight request and handle it
    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        header('Access-Control-Allow-Headers: Authorization, Content-Type');
        exit();
    }

    $headers = $request->get_headers();
    $authorization_header = isset($headers['Authorization']) ? $headers['Authorization'] : '';
    if (!$authorization_header) {
        $authorization_header = isset($headers['authorization']) ? $headers['authorization'][0] : '';
    }

    if ($authorization_header != APP_AUTH_TOKEN) {
        http_response_code(403);
        exit('{"message":"Invalid authorization header"}');
        // return new WP_Error('authorization_failed', 'Invalid authorization header ', array('status' => 401));
    }


    if ($request['session'] ?? false) {
        return coworking_app_check_session_id($request['session'], $request['user_id']);
    }
}

function coworking_app_check_session_id($sid, $uid)
{
    $sessions = coworking_app_get_valid_sessions($uid);


    if (in_array($sid, $sessions)) {
        return $sid;
    } else {
        return false;
    }
}

function coworking_app_delete_session_id($sid, $uid)
{
    $sessions = coworking_app_get_sessions($uid);

    // If the session id exists, remove it from the sessions array
    unset($sessions[$sid]);

    // Save the updated sessions array back to the user meta data
    update_user_meta($uid, 'sessions', json_encode($sessions));
    return true;
}
