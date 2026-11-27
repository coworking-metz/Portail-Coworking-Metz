<?php
/**
 * Plugin Name: Cowo - Synthèse du compte connecté (me/summary)
 * Description: Route REST GET /wp-json/cowo/v1/me/summary : synthèse minimale du compte de la personne authentifiée
 *              (adhésion, abonnement, carnets, tarif, statut nomade, présences, droits). Utilisée par l'assistant IA du coworking.
 * Version:     1.0.0
 *
 * Principes :
 * - la route ne lit QUE le compte de l'utilisateur courant (aucun paramètre d'identifiant) ;
 * - authentification par la session WordPress ou par un jeton OAuth Bearer (filtre determine_current_user du serveur OAuth) ;
 * - aucune donnée de contact (e-mail, téléphone, adresse, naissance), aucun identifiant technique, aucune clé ;
 * - une source indisponible n'entraîne jamais d'erreur 500 : les champs concernés valent null et la section est listée dans "indisponible" ;
 * - réponse jamais mise en cache par les navigateurs/proxys (no-store), cache serveur de 60 s par utilisateur.
 *
 * Source des données de fréquentation et d'abonnement : le service « tickets » du site (constantes TICKET_BASE_URL et API_KEY_TICKET
 * déjà définies par le site ; la clé n'est jamais renvoyée ni journalisée).
 */

defined('ABSPATH') || exit;

const COWO_MS_CACHE_TTL = 60;          // secondes, cache complet
const COWO_MS_CACHE_TTL_PARTIAL = 15;  // secondes, cache d'une réponse partielle
const COWO_MS_HTTP_TIMEOUT = 5;        // secondes, par appel au service tickets
const COWO_MS_SEUIL_WEEKEND = 10;      // journées coworkées nécessaires pour venir le week-end

add_action('rest_api_init', function () {
    register_rest_route('cowo/v1', '/me/summary', [
        'methods'             => 'GET',
        'callback'            => 'cowo_ms_handle',
        'permission_callback' => 'cowo_ms_permission',
        'args'                => [], // volontairement aucun paramètre
    ]);
});

/** Accès : utilisateur authentifié uniquement. */
function cowo_ms_permission()
{
    if (is_user_logged_in()) {
        return true;
    }
    return new WP_Error('cowo_non_authentifie', 'Authentification requise.', ['status' => 401]);
}

/** En-têtes de sécurité/cache sur toutes les réponses de cette route (succès comme erreurs). */
add_filter('rest_post_dispatch', function ($result, $server, $request) {
    if ($request instanceof WP_REST_Request && $request->get_route() === '/cowo/v1/me/summary' && $result instanceof WP_HTTP_Response) {
        $result->header('Cache-Control', 'no-store, private');
        $result->header('Pragma', 'no-cache');
        $result->header('Vary', 'Authorization, Cookie');
        $result->header('X-Content-Type-Options', 'nosniff');
    }
    return $result;
}, 10, 3);

function cowo_ms_handle(WP_REST_Request $request)
{
    $uid = (int) get_current_user_id();
    if ($uid <= 0) {
        return new WP_Error('cowo_non_authentifie', 'Authentification requise.', ['status' => 401]);
    }

    try {
        $key = 'cowo_ms_' . $uid;
        $cached = get_transient($key);
        if (is_array($cached)) {
            return new WP_REST_Response($cached, 200);
        }

        $data = cowo_ms_build($uid);
        set_transient($key, $data, empty($data['indisponible']) ? COWO_MS_CACHE_TTL : COWO_MS_CACHE_TTL_PARTIAL);
        return new WP_REST_Response($data, 200);
    } catch (Throwable $e) {
        // Aucune information interne dans la réponse.
        return new WP_Error('cowo_indisponible', 'Service momentanément indisponible.', ['status' => 503]);
    }
}

/** Appel GET au service tickets (timeout court). Renvoie le JSON décodé (tableau) ou null. */
function cowo_ms_tickets_get($path)
{
    if (!defined('TICKET_BASE_URL') || !defined('API_KEY_TICKET') || !TICKET_BASE_URL || !API_KEY_TICKET) {
        return null;
    }
    $response = wp_remote_get(rtrim(TICKET_BASE_URL, '/') . $path, [
        'timeout'     => COWO_MS_HTTP_TIMEOUT,
        'redirection' => 0,
        'headers'     => ['Authorization' => 'Token ' . API_KEY_TICKET, 'Accept' => 'application/json'],
    ]);
    if (is_wp_error($response) || (int) wp_remote_retrieve_response_code($response) !== 200) {
        return null;
    }
    $json = json_decode(wp_remote_retrieve_body($response), true);
    return is_array($json) ? $json : null;
}

function cowo_ms_date($v)
{
    return (is_string($v) && preg_match('/^(\d{4}-\d{2}-\d{2})/', $v, $m)) ? $m[1] : null;
}

function cowo_ms_days_between($from, $to)
{
    try {
        $a = new DateTimeImmutable($from . ' 00:00:00', wp_timezone());
        $b = new DateTimeImmutable($to . ' 00:00:00', wp_timezone());
        return (int) $a->diff($b)->format('%r%a');
    } catch (Throwable $e) {
        return null;
    }
}

/** Construit la synthèse de l'utilisateur $uid. Ne lève pas d'erreur pour une source manquante. */
function cowo_ms_build($uid)
{
    $tz = wp_timezone();
    $today = (new DateTimeImmutable('now', $tz))->format('Y-m-d');
    $indispo = [];

    $user = get_userdata($uid);
    $m = cowo_ms_tickets_get('/members/' . $uid);
    // Si le service ne répond pas du tout, inutile de tenter un second appel (même délai d'attente).
    $act = $m === null ? null : cowo_ms_tickets_get('/members/' . $uid . '/activity');

    // --- Identité minimale : prénom seulement (jamais de nom de famille) ---
    $prenom = get_user_meta($uid, 'first_name', true);
    if (!is_string($prenom) || $prenom === '') {
        $prenom = is_array($m) && isset($m['firstName']) && is_string($m['firstName']) ? $m['firstName'] : null;
    }
    $prenom = $prenom !== null && $prenom !== '' ? mb_substr(trim(wp_strip_all_tags($prenom)), 0, 40) : null;
    $depuis = $user ? cowo_ms_date($user->user_registered) : null;

    // --- Adhésion, abonnement, carnets, tarif ---
    $adherent = $abonnement = $carnets = $tarif = null;
    if (is_array($m)) {
        $annee = isset($m['lastMembership']) && preg_match('/^\d{4}$/', (string) $m['lastMembership']) ? (int) $m['lastMembership'] : null;
        $adherent = [
            'a_jour'        => isset($m['membershipOk']) ? (bool) $m['membershipOk'] : null,
            'derniere_annee' => $annee,
            'fin'           => $annee ? $annee . '-12-31' : null, // l'adhésion court sur l'année civile
        ];

        $courant = null;
        $dernier = null;
        foreach ((isset($m['abos']) && is_array($m['abos']) ? $m['abos'] : []) as $abo) {
            if (!is_array($abo)) continue;
            $d = cowo_ms_date($abo['aboStart'] ?? null);
            $f = cowo_ms_date($abo['aboEnd'] ?? null);
            if (!$f) continue;
            if ($dernier === null || $f > $dernier['fin']) $dernier = ['debut' => $d, 'fin' => $f];
            if ($f >= $today && ($d === null || $d <= $today)) {
                if ($courant === null || $f > $courant['fin']) $courant = ['debut' => $d, 'fin' => $f];
            }
        }
        $sel = $courant ?? $dernier;
        $abonnement = [
            'en_cours'       => $courant !== null,
            'debut'          => $sel['debut'] ?? null,
            'fin'            => $sel['fin'] ?? null,
            'duree_jours'    => ($sel && $sel['debut']) ? cowo_ms_days_between($sel['debut'], $sel['fin']) + 1 : null,
            'jours_restants' => $courant ? max(0, (int) cowo_ms_days_between($today, $courant['fin'])) : null,
        ];

        $carnets = ['solde' => isset($m['balance']) && is_numeric($m['balance']) ? round((float) $m['balance'], 2) : null];

        $reduit = isset($m['isEligibleToReducedRate']) ? (bool) $m['isEligibleToReducedRate'] : null;
        $blida = isset($m['isBlidaResident']) ? (bool) $m['isBlidaResident'] : null;
        $tarif = [
            'type'           => $blida ? 'resident' : ($reduit ? 'reduit' : 'normal'),
            'reduit'         => $reduit,
            'resident_blida' => $blida,
        ];
    } else {
        $indispo[] = 'adhesion';
        $indispo[] = 'abonnement';
        $indispo[] = 'carnets';
        $indispo[] = 'tarif';
    }

    // --- Présences (une journée = valeur 1 ; une demi-journée peut valoir moins) ---
    $presences = null;
    $statistiques = null;
    if (is_array($act)) {
        $d30 = (new DateTimeImmutable($today, $tz))->modify('-30 days')->format('Y-m-d');
        $d90 = (new DateTimeImmutable($today, $tz))->modify('-90 days')->format('Y-m-d');
        $j30 = $j90 = $total = 0.0;
        $last = null;
        foreach ($act as $p) {
            if (!is_array($p)) continue;
            $d = cowo_ms_date($p['date'] ?? null);
            $v = isset($p['value']) && is_numeric($p['value']) ? (float) $p['value'] : (isset($p['amount']) && is_numeric($p['amount']) ? (float) $p['amount'] : 0.0);
            if (!$d || $v <= 0) continue;
            $total += $v;
            if ($d >= $d90) $j90 += $v;
            if ($d >= $d30) $j30 += $v;
            if ($last === null || $d > $last) $last = $d;
        }
        $presences = [
            'jours_30'       => (int) round($j30),
            'jours_90'       => (int) round($j90),
            'total'          => (int) round($total),
            'derniere_venue' => $last,
        ];
        $statistiques = cowo_ms_stats($act, $today);
    } else {
        $indispo[] = 'presences';
    }

    // --- Droits : week-ends à partir de 10 journées coworkées au total ---
    $droits = null;
    if ($presences !== null) {
        $droits = [
            'week_end'                => $presences['total'] >= COWO_MS_SEUIL_WEEKEND,
            'seuil_week_end'          => COWO_MS_SEUIL_WEEKEND,
            'jours_manquants_week_end' => max(0, COWO_MS_SEUIL_WEEKEND - $presences['total']),
        ];
    } else {
        $indispo[] = 'droits';
    }

    // --- Statut nomade (même fonction que le filtre d'administration « nomadesOnly ») ---
    $nomade = null;
    $passees = null;
    try {
        if (function_exists('isNomade') && function_exists('wc_get_orders')) {
            $est = (bool) isNomade($uid);
            $dates = [];
            if ($est && function_exists('get_dates_nomade_user')) {
                $toutes = @get_dates_nomade_user($uid);
                if (is_array($toutes)) {
                    $dates = array_values(array_unique(array_filter(array_map('cowo_ms_date', $toutes))));
                    sort($dates);
                }
            }
            $avenir = array_values(array_filter($dates, function ($d) use ($today) { return $d >= $today; }));
            $passees = array_values(array_filter($dates, function ($d) use ($today) { return $d < $today; }));
            $nomade = [
                'actif'            => $est,
                'dates_a_venir'    => array_slice($avenir, 0, 5),
                'derniere_date'    => $passees ? end($passees) : null,
            ];
        } else {
            $indispo[] = 'nomade';
        }
    } catch (Throwable $e) {
        $nomade = null;
        $indispo[] = 'nomade';
    }

    // Les journées nomades ne sont pas dans le service tickets : on les ajoute en agrégat si connues.
    if (is_array($statistiques) && is_array($nomade)) {
        $statistiques['types']['nomade'] = is_array($passees) ? count($passees) : 0;
    }

    // --- Achats boutique (WooCommerce, commandes payées/terminées du seul utilisateur courant) ---
    $achats = null;
    $achats_resume = null;
    try {
        $r = cowo_ms_purchases($uid);
        if (is_array($r)) {
            $achats = $r['achats'];
            $achats_resume = $r['resume'];
        } else {
            $indispo[] = 'achats';
        }
    } catch (Throwable $e) {
        $indispo[] = 'achats';
    }

    return [
        'prenom'       => $prenom,
        'membre_depuis' => $depuis,
        'adherent'     => $adherent,
        'abonnement'   => $abonnement,
        'carnets'      => $carnets,
        'tarif'        => $tarif,
        'nomade'       => $nomade,
        'presences'    => $presences,
        'statistiques' => $statistiques,
        'achats'       => $achats,
        'achats_resume' => $achats_resume,
        'droits'       => $droits,
        'indisponible' => array_values(array_unique($indispo)),
        'maj'          => gmdate('c'),
    ];
}

/** Statistiques agrégées de fréquentation (jamais la liste brute des présences). */
function cowo_ms_stats(array $act, $today)
{
    $tz = wp_timezone();
    $noms = [1 => 'lundi', 2 => 'mardi', 3 => 'mercredi', 4 => 'jeudi', 5 => 'vendredi', 6 => 'samedi', 7 => 'dimanche'];
    $parJour = array_fill_keys(array_values($noms), 0.0);
    $parMois = [];
    $semaines = [];
    $types = ['abonnement' => 0.0, 'carnet' => 0.0];
    $premiere = null;
    $total = 0.0;

    foreach ($act as $p) {
        if (!is_array($p)) continue;
        $d = cowo_ms_date($p['date'] ?? null);
        $v = isset($p['value']) && is_numeric($p['value']) ? (float) $p['value'] : (isset($p['amount']) && is_numeric($p['amount']) ? (float) $p['amount'] : 0.0);
        if (!$d || $v <= 0) continue;
        try {
            $dt = new DateTimeImmutable($d, $tz);
        } catch (Throwable $e) {
            continue;
        }
        $total += $v;
        $parJour[$noms[(int) $dt->format('N')]] += $v;
        $mois = substr($d, 0, 7);
        $parMois[$mois] = ($parMois[$mois] ?? 0) + $v;
        $semaines[$dt->modify('monday this week')->format('Y-m-d')] = true;
        $t = isset($p['type']) ? (string) $p['type'] : '';
        if ($t === 'subscription' || $t === 'abo') $types['abonnement'] += $v; else $types['carnet'] += $v;
        if ($premiere === null || $d < $premiere) $premiere = $d;
    }

    // 12 derniers mois (mois courant inclus)
    $douze = [];
    $base = new DateTimeImmutable(substr($today, 0, 7) . '-01', $tz);
    $somme12 = 0.0;
    for ($i = 11; $i >= 0; $i--) {
        $k = $base->modify("-$i months")->format('Y-m');
        $j = (int) round($parMois[$k] ?? 0);
        $somme12 += $parMois[$k] ?? 0;
        $douze[] = ['mois' => $k, 'jours' => $j];
    }

    $record = null;
    foreach ($parMois as $k => $v) {
        if ($record === null || $v > $record['jours']) $record = ['mois' => $k, 'jours' => (int) round($v)];
    }

    // Régularité : semaines consécutives (jusqu'à la semaine courante, ou la précédente si aucune venue cette semaine)
    $lundi = (new DateTimeImmutable($today, $tz))->modify('monday this week');
    if (!isset($semaines[$lundi->format('Y-m-d')])) $lundi = $lundi->modify('-7 days');
    $serie = 0;
    while (isset($semaines[$lundi->format('Y-m-d')])) {
        $serie++;
        $lundi = $lundi->modify('-7 days');
    }

    return [
        'par_mois_12'              => $douze,
        'moyenne_mensuelle_12'     => round($somme12 / 12, 1),
        'par_jour_semaine'         => array_map(function ($v) { return (int) round($v); }, $parJour),
        'mois_record'              => $record,
        'premiere_venue'           => $premiere,
        'total_jours'              => (int) round($total),
        'semaines_consecutives'    => $serie,
        'types'                    => array_map(function ($v) { return (int) round($v); }, $types),
    ];
}

/** Catégorie d'un produit : abonnement | carnet | nomade | adhesion | contribution | impression | autre. */
function cowo_ms_category($name, $product_id)
{
    $hay = remove_accents(mb_strtolower((string) $name));
    $terms = [];
    if ($product_id) {
        $t = get_the_terms($product_id, 'product_cat');
        if (is_array($t)) {
            foreach ($t as $x) $terms[] = remove_accents(mb_strtolower($x->slug . ' ' . $x->name));
        }
    }
    $all = $hay . ' ' . implode(' ', $terms);
    if ($product_id && !empty(get_post_meta($product_id, 'journee_nomade', true))) return 'nomade';
    if (strpos($all, 'nomade') !== false) return 'nomade';
    if (strpos($all, 'adhesion') !== false) return 'adhesion';
    if (strpos($hay, 'impression') !== false || strpos($hay, 'photocop') !== false) return 'impression';
    if (preg_match('/abonnement/', $hay)) return 'abonnement';
    if (preg_match('/carnet|ticket/', $hay)) return 'carnet';
    if (preg_match('/cafe|the |contribution|effort|don /', $hay . ' ') || strpos($all, 'effort-collectif') !== false) return 'contribution';
    if (strpos($all, 'abonnements') !== false) return 'abonnement';
    return 'autre';
}

/** Achats de l'utilisateur courant : 30 derniers (date, produit, quantité, montant TTC) + résumé agrégé. null si WooCommerce indisponible. */
function cowo_ms_purchases($uid)
{
    if (!function_exists('wc_get_orders')) {
        return null;
    }
    $max_orders = 100;
    $res = wc_get_orders([
        'customer_id' => (int) $uid,
        'status'      => ['wc-completed', 'wc-processing'],
        'limit'       => $max_orders,
        'orderby'     => 'date',
        'order'       => 'DESC',
        'paginate'    => true,
    ]);
    $orders = is_object($res) && isset($res->orders) ? $res->orders : (is_array($res) ? $res : []);
    $found = is_object($res) && isset($res->total) ? (int) $res->total : count($orders);

    $lignes = [];
    foreach ($orders as $order) {
        if (!($order instanceof WC_Order)) continue;
        $dt = $order->get_date_paid() ?: $order->get_date_created();
        $date = $dt ? $dt->date_i18n('Y-m-d') : null;
        $statut = $order->get_status() === 'completed' ? 'termine' : 'paye';
        foreach ($order->get_items() as $item) {
            $pid = method_exists($item, 'get_product_id') ? (int) $item->get_product_id() : 0;
            $nom = wp_strip_all_tags((string) $item->get_name());
            $ttc = round((float) $item->get_total() + (float) $item->get_total_tax(), 2);
            $lignes[] = [
                'date'        => $date,
                'produit'     => mb_substr($nom, 0, 80),
                'categorie'   => cowo_ms_category($nom, $pid),
                'quantite'    => (int) $item->get_quantity(),
                'montant_ttc' => $ttc,
                'statut'      => $statut,
            ];
        }
    }

    $par_cat = [];
    $total = 0.0;
    $premiere = null;
    foreach ($lignes as $l) {
        $c = $l['categorie'];
        $par_cat[$c]['quantite'] = ($par_cat[$c]['quantite'] ?? 0) + $l['quantite'];
        $par_cat[$c]['montant_ttc'] = round(($par_cat[$c]['montant_ttc'] ?? 0) + $l['montant_ttc'], 2);
        $total += $l['montant_ttc'];
        if ($l['date'] && ($premiere === null || $l['date'] < $premiere)) $premiere = $l['date'];
    }

    return [
        'achats' => array_slice($lignes, 0, 30),
        'resume' => [
            'commandes'     => count($orders),
            'articles'      => count($lignes),
            'total_ttc'     => round($total, 2),
            'par_categorie' => $par_cat ?: new stdClass(),
            'depuis'        => $premiere,
            'complet'       => $found <= $max_orders,
            'plus_anciens'  => max(0, count($lignes) - 30) + max(0, $found - count($orders)),
        ],
    ];
}
