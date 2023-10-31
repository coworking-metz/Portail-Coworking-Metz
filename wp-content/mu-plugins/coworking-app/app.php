<?php

include __DIR__ . '/app-auth.php';
include __DIR__ . '/app-droits.php';
include __DIR__ . '/app-settings.php';
include __DIR__ . '/app-session.php';
include __DIR__ . '/app-user-exists.php';
include __DIR__ . '/app-nouvelle-visite.php';
include __DIR__ . '/app-visite-ics.php';


function coworking_app_settings()
{
    $url = 'https://tickets.coworking-metz.fr/api/current-users?key=' . API_KEY_TICKET . '&delay=15';
    $data = file_get_contents($url);
    $presences = json_decode($data, true);
    $fermer_vacances = get_field('fermer_vacances', 'option');
    if ($fermer_vacances) {
        $exclude = extractDatesExcludePast(get_field('empecher_visites', 'option'), fetch_holidays());
    } else {
        $exclude = extractDatesExcludePast(get_field('empecher_visites', 'option'));
    }

    $mentions  = get_field_raw('mentions', 'option');

    $mentions = [
        'visite' => $mentions['mentions-page-visite'],
        'recap' => $mentions['mentions-page-recap'],
        'infos' => $mentions['mentions-page-infos']
    ];
    $visites = [
        'jours_de_visites' => array_map('intval', get_field('jours_de_visites', 'option')),
        'horaire' => trim(get_field('horaire', 'option')),
        'limite_mois' => intval(get_field('limite_mois', 'option')),
        'fermer_vacances' => $fermer_vacances,
        'fermer_visites' => visites_fermees(),
        'empecher_visites' => $exclude,
    ];
    $settings = [
        'mentions' => $mentions,
        'visites' => $visites,
        'polaroid_default' => site_url() . '/images/pola-poule-vide.jpg',
        'occupation' => [
            'total' => 28,
            'presents' => count($presences)
        ]
    ];
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

function can_use_app($user)
{
    if (is_visiteur($user)) return true;
    if (user_can($user, 'administrator')) return true;
    if (in_array('customer', (array) $user->roles)) return true;
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
function coworking_app_user($user)
{
    if (is_numeric($user)) {
        $user = get_user_by('ID', $user);
    }

    if (!can_use_app($user)) return;

    return [
        'login' => $user->user_email,
        'name' => $user->display_name,
        'id' => $user->ID,
        'session_id' => coworking_app_gen_session_id($user->ID),
    ];
}
function date_de_visite($user)
{
    $user_id = get_user_id($user);
    $visite_date = get_user_meta($user_id, 'visite', true);
    return $visite_date;
}
function is_visiteur($user)
{
    $user_id = $user->ID;
    if (!in_array('subscriber', (array) $user->roles)) return;

    $visite_date = date_de_visite($user_id);
    if (!$visite_date) return;

    if (isPast($visite_date) && !istoday($visite_date)) return;

    return true;

    /*    $dateTimeZone = new DateTimeZone('Europe/Paris');

    $dateToCheck = new DateTime($visite_date, $dateTimeZone);
    $dateToCheck->setTime(0, 0); // Reset time to midnight to only compare date

    $today = new DateTime('now', $dateTimeZone);
    $today->setTime(0, 0); // Reset time to midnight

    $isToday = $dateToCheck == $today;

    return $isToday;*/
}
function coworking_app_droits($user_id, $options = [])
{

    $user = get_user_by('ID', $user_id);
    if (!$user) return;

    if (is_visiteur($user)) {
        $date = date_de_visite($user);
        return [
            'guest' => true,
            'visite' => [
                'date' => $date,
                'dateFr' => date_francais($date, true),
                'isToday' => isToday($date)
            ],
            'settings' => coworking_app_settings(),
            'droits' => [
                'ouvrir_parking' => true,
                'ouvrir_portail' => true,
            ]
        ];
    } else {

        $bloquer_ouvrir_portail = get_field('bloquer_ouvrir_portail', 'user_' . $user_id);
        $ouvrir_parking = get_field('ouvrir_parking', 'user_' . $user_id) || date('Ymd') < '20240101';

        // $ouvrir_parking = user_can($user_id, 'ouvrir_parking');

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
                'polaroid' => polaroid_existe($user_id) ? polaroid_url($user_id, true) : false,
                'admin' => $admin,
                'ouvrir_parking' => $ouvrir_parking,
                'ouvrir_portail' => $bloquer_ouvrir_portail ? false : true,
            ]
        ];
    }
}
function coworking_app_check_origins($origin)
{
    return true;

    if (in_array($origin, coworking_app_origins())) return true;

    if (strstr($origin, '127.0.0.1')) return true;
    if (strstr($origin, 'localhost')) return true;
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

    // header('test-origin:' . $origin);
    if (coworking_app_check_origins($origin)) {
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


function addEventToCalendar($user_id, $event)
{

    if (!$user_id) return;
    $key = 'ajout-calendrier-' . $event['start'];
    if (get_user_meta($user_id, $key, true)) return;
    update_user_meta($user_id, $key, true);


    // Editer la task sur IFTTT à cette adresse:  https://ifttt.com/applets/vD4gcHhx
    $webhook = 'https://maker.ifttt.com/trigger/nouvelle-visite/with/key/mVGGKzi6RS8B-x5ohxM4q8SuZgm6s-OdjbidwgUYvvV';
    $payload = ['value1' => $event['name'], 'value2' => $event['start'], 'value3' => $event['end']];

    $ch = curl_init($webhook);

    // Création du payload JSON
    $data = json_encode($payload);

    // Configuration des options cURL
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    // Exécution de la requête
    $response = curl_exec($ch);

    // Fermeture de la connexion
    curl_close($ch);

    return $payload;
}

/**
 * Crée un nouvel utilisateur WordPress si l'email n'existe pas déjà
 *
 * @param array $user Informations de base de l'utilisateur
 * @param array $meta Métadonnées supplémentaires pour l'utilisateur
 */
function create_wp_user_if_not_exists($user, $meta = [])
{
    // Désactive l'envoi de mail temporairement
    remove_action('register_new_user', 'wp_send_new_user_notifications');

    // Récupération des données utilisateur
    $nom = $user['nom'];
    $prenom = $user['prenom'];
    $email = $user['email'];
    $password = $user['password'];
    if (!$password) {
        $password = sha1(time());
    }

    $user_id = email_exists($email);
    // Vérifie si l'utilisateur existe déjà
    if (!$user_id) {
        // Crée l'utilisateur
        $user_id = wp_create_user($email, $password, $email);

        // Met à jour les informations supplémentaires
        wp_update_user([
            'ID'         => $user_id,
            'first_name' => $prenom,
            'last_name'  => $nom,
            'nickname'   => $prenom . ' ' . $nom,
            'display_name' => $prenom . ' ' . $nom,
        ]);

        // Définit le 'user_nicename'
        wp_update_user([
            'ID'            => $user_id,
            'user_nicename' => sanitize_title($prenom . ' ' . $nom)
        ]);

        // Ajoute les métadonnées utilisateur
        foreach ($meta as $key => $value) {
            update_user_meta($user_id, $key, $value);
        }
    }

    // Réactive l'envoi de mail
    add_action('register_new_user', 'wp_send_new_user_notifications');

    return $user_id;
}

function app_login_link($user_id)
{
    $check = sha1($user_id . APP_AUTH_TOKEN);
    return 'https://app.coworking-metz.fr/visite?visite=' . $user_id . '&check=' . $check;
}
