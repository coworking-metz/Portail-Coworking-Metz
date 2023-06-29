<?php
function custom_appauth_api_route()
{
    register_rest_route('cowo/v1', '/app-auth', array(
        'methods'  => 'POST',
        'callback' => function ($request) {
            // header("Access-Control-Allow-Origin: *");
            // header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
            // header("Access-Control-Allow-Headers: Authorization, Content-Type");

            $allowedOrigins = array(
                'http://127.0.0.1:5173',
                'https://melodious-entremet-ad9165.netlify.app',
                'https://app.coworking-metz.fr'
            );

            $origin = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : '';

            if (in_array($origin, $allowedOrigins)) {
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
                return new WP_Error('authorization_failed', 'Invalid authorization header ', array('status' => 401));
            }
            $email = $request->get_param('email');
            $password = $request->get_param('password');

            // Authenticate the user credentials
            $credentials = array(
                'user_login'    => $email,
                'user_password' => $password,
            );
            $user = wp_authenticate($credentials['user_login'], $credentials['user_password']);

            // Check if authentication succeeded
            if (!is_wp_error($user)) {
                $response = array('user' => [
                    'login' => $user->user_login,
                    'id' => $user->ID
                ]);
            } else {
                return new WP_Error('authorization_failed', 'Invalid credentials ', array('status' => 401));
            }

            return rest_ensure_response($response);
        },
    ));
}
add_action('rest_api_init', 'custom_appauth_api_route');
