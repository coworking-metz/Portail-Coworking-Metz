<?php


function coworking_app_droits($user_id)
{

    $user = get_user_by('ID', $user_id);
    if (!$user) return;

    $bloquer_ouvrir_portail = get_field('bloquer_ouvrir_portail', 'user_' . $user_id);

    return array(
        'ouvrir_portail' => $bloquer_ouvrir_portail ? false : true,
    );
}
function coworking_app_origins()
{
    return array(
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
        exit('Invalid authorization header');
        // return new WP_Error('authorization_failed', 'Invalid authorization header ', array('status' => 401));
    }
}
