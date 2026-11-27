<?php
/**
 * Plugin Name: Cowo - Présence en direct (presence-now)
 * Description: Route REST publique GET /api-json-wp/cowo/v1/presence-now : nombre de coworkers présents (estimation wifi) en JSON, sans HTML.
 *              Utilisée par l'assistant IA du coworking. Même source que la route coworkers_now de la page d'accueil.
 * Version:     1.0.0
 *
 * Principes :
 * - information déjà publique sur la page d'accueil : route en lecture seule, sans authentification, sans paramètre ;
 * - même source de vérité que /cowo/v1/coworkers_now : nombre d'éléments de GET {TICKET_BASE_URL}/current-members
 *   (constantes TICKET_BASE_URL et API_KEY_TICKET du site, la clé n'est jamais renvoyée ni journalisée) ;
 *   capacité = COWORKING_NB_PLACES (40 par défaut), comme la route existante ;
 * - seuls des totaux agrégés sont renvoyés : aucun nom, identifiant, appareil ni donnée personnelle ;
 * - source en échec : HTTP 200 avec presents/libres à null et indisponible à true (jamais d'erreur 500, ni chemin, ni trace) ;
 * - cache serveur (transient) de 30 s ; Cache-Control public, max-age=30, s-maxage=30 (Cloudflare) ;
 * - le nombre est une estimation : appareils enregistrés vus sur le wifi depuis moins de 10 minutes.
 */

defined('ABSPATH') || exit;

const COWO_PN_CACHE_TTL = 30;          // secondes, cache d'une réponse valide
const COWO_PN_CACHE_TTL_ECHEC = 10;    // secondes, cache d'une réponse « indisponible »
const COWO_PN_HTTP_TIMEOUT = 3;        // secondes, appel au service tickets
const COWO_PN_DELAI_MINUTES = 10;      // fenêtre de détection du wifi (défaut de current-members)
const COWO_PN_TRANSIENT = 'cowo_pn_presence';

add_action('rest_api_init', function () {
    register_rest_route('cowo/v1', '/presence-now', [
        'methods'             => 'GET',
        'callback'            => 'cowo_pn_handle',
        'permission_callback' => '__return_true', // information publique (page d'accueil)
        'args'                => [],              // volontairement aucun paramètre
    ]);
});

/** En-têtes de cache de cette route (Cloudflare : 30 s, pas 15 minutes). */
add_filter('rest_post_dispatch', function ($result, $server, $request) {
    if ($request instanceof WP_REST_Request && $request->get_route() === '/cowo/v1/presence-now' && $result instanceof WP_HTTP_Response) {
        $result->header('Cache-Control', 'public, max-age=30, s-maxage=30');
        $result->header('X-Content-Type-Options', 'nosniff');
    }
    return $result;
}, 10, 3);

function cowo_pn_handle(WP_REST_Request $request)
{
    try {
        $cached = get_transient(COWO_PN_TRANSIENT);
        if (is_array($cached)) {
            return new WP_REST_Response($cached, 200);
        }

        $data = cowo_pn_build();
        set_transient(COWO_PN_TRANSIENT, $data, empty($data['indisponible']) ? COWO_PN_CACHE_TTL : COWO_PN_CACHE_TTL_ECHEC);
        return new WP_REST_Response($data, 200);
    } catch (Throwable $e) {
        return new WP_REST_Response(cowo_pn_payload(null), 200);
    }
}

/** Nombre de présents d'après le service tickets, ou null si la source est indisponible. */
function cowo_pn_count()
{
    if (!defined('TICKET_BASE_URL') || !defined('API_KEY_TICKET') || !TICKET_BASE_URL || !API_KEY_TICKET) {
        return null;
    }
    $response = wp_remote_get(rtrim(TICKET_BASE_URL, '/') . '/current-members', [
        'timeout'     => COWO_PN_HTTP_TIMEOUT,
        'redirection' => 0,
        'headers'     => ['Authorization' => 'Token ' . API_KEY_TICKET, 'Accept' => 'application/json'],
    ]);
    if (is_wp_error($response) || (int) wp_remote_retrieve_response_code($response) !== 200) {
        return null;
    }
    $json = json_decode(wp_remote_retrieve_body($response), true);
    if (!is_array($json)) {
        return null;
    }
    $n = count($json); // même calcul que coworkers_now ; la liste elle-même n'est jamais exposée
    return ($n >= 0 && $n <= 1000) ? $n : null;
}

function cowo_pn_capacite()
{
    $c = defined('COWORKING_NB_PLACES') ? (int) COWORKING_NB_PLACES : 40;
    return $c > 0 ? $c : 40;
}

function cowo_pn_build()
{
    return cowo_pn_payload(cowo_pn_count());
}

/** Contrat JSON ; $presents null = source indisponible. */
function cowo_pn_payload($presents)
{
    $capacite = cowo_pn_capacite();
    $ok = is_int($presents);
    return [
        'presents'      => $ok ? $presents : null,
        'capacite'      => $capacite,
        'libres'        => $ok ? max(0, $capacite - $presents) : null,
        'estimation'    => true,
        'delai_minutes' => COWO_PN_DELAI_MINUTES,
        'mis_a_jour'    => wp_date('c'),
        'indisponible'  => !$ok,
    ];
}
