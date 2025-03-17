<?php



function isNomade($user = false)
{
    $user_id = get_post_id($user);

    if (!$user_id) {
        $user_id = get_current_user_id();
    }

    if (!$user_id) return;

    return !empty(get_user_meta($user_id, 'nomade', true));
}
function get_users_with_visit_no_orders()
{
    // Obtenir tous les utilisateurs avec la meta 'visite' renseignée
    $args = [
        'meta_key'     => 'visite',
        'meta_compare' => 'EXISTS',
    ];

    $users = get_users($args);
    $filtered_users = [];

    // Filtrer les utilisateurs qui n'ont pas de commande WooCommerce
    foreach ($users as $user) {
        if (!in_array('subscriber', (array) $user->roles, true) && !in_array('customer', (array) $user->roles, true)) {
            continue;
        }
        $orders = wc_get_orders([
            'customer_id' => $user->ID,
            'limit'       => 1, // Limiter pour optimisation
        ]);

        if (empty($orders)) {
            // Récupérer la valeur de la meta 'visite' pour tri ultérieur
            $user->visite_sort = strtotime(get_user_meta($user->ID, 'visite', true));
            if (!$user->visite_sort) continue;
            if ($user->visite_sort > time()) continue;
            $filtered_users[] = $user;
        }
    }

    // Trier les utilisateurs par 'visite' de manière décroissante
    usort($filtered_users, function ($a, $b) {
        return $b->visite_sort <=> $a->visite_sort;
    });

    return $filtered_users;
}


/**
 * Passer un user en customer (Coworker) et lui envoyer le mail de creation de compte
 * Status de retour : 
 *  1 : Le compte adhérent a été finalisé
 * -1 : Compte adhérent déjà été finalisé
 * -2 : Erreur d'envoi du mail
 * -3 : Utilisateur inconnu
 * ?? : Erreur de finalisation inconnue
 */
function finaliser_user($user_id)
{

    $status = -3;
    if (!$user_id) return $status;

    $user = get_userdata($user_id);
    if (!$user) return $status;

    if (in_array('subscriber', $user->roles) || in_array('bookmify-customer', $user->roles)) {
        $user->set_role('customer');
        if (envoyer_email_creation_compte($user)) {
            $status = 1;
        } else $status = -2;
    } else $status = -1;

    return $status;
}

/**
 * Retourne une explication textuelle du statut de retour de finalisation d'un user
 */
function finaliser_status_details($status)
{


    switch ($status) {
        case 1:
            $type = 'success';
            $title = 'Le compte adhérent a été activé';
            $subtitle = 'Un e-mail contenant les détails de l\'activation viens d\'être envoyé à l\'adresse e-mail associée à cet utilisateur';
            $description = 'Ce compte utilisateur a désormais le rôle "Coworker" et <a href="/wp-admin/admin.php?page=reglages-visites#tab-field_653279fcd5252">le mail de création de compte</a> lui a été envoyé. <a href="/wp-admin/admin.php?page=reglages-visites">Voir les options des visites</a>';
            break;
        case -1:
            $type = 'warning';
            $title = 'Ce compte adhérent a déjà été activé précédemment';
            $subtitle = 'Aucune autre action n\'est requise de votre part';
            $description = 'Ce compte utilisateur n\'avait pas un compte "En attente" et n\'a donc pas été modifié. Le mail de création de compte ne lui a pas été ré-envoyé.';
            break;
        case -2:
            $type = 'error';
            $title = 'Erreur d\'envoi du mail lors de l\'activation du compte';
            $subtitle = 'Contactez <b>contact@coworking-metz.fr</b> si le problème persiste';
            $description = 'Le <a href="/wp-admin/admin.php?page=reglages-visites#tab-field_653279fcd5252">mail de création de compte</a> n\'a pas été envoyé à cause d\'une erreur inconnue.';
            break;
        default:
            $type = 'error';
            $title = 'Erreur lors de l\'activation du compte';
            $subtitle = 'Contactez <b>contact@coworking-metz.fr</b> si le problème persiste';
            $description = 'Une erreur inconnue s\'est produite.';
            break;
    }
    $response = [
        'type'        => $type,
        'title'       => $title,
        'subtitle'       => $subtitle ?? '',
        'description' => $description
    ];
    return $response;
}


/**
 * Retourne une explication textuelle du statut de retour de finalisation d'un user
 */
function tarif_reduit_status_details($status)
{


    switch ($status) {
        case 1:
            $type = 'success';
            $title = 'Accès aux tarifs réduits ouvert';
            $description = 'Cette personne pourra désormais commander dans la boutique tous les produits de la catégorie "<a href="' . admin_url('edit.php?product_cat=tarifs-reduits&post_type=product') . '">Tarifs réduits</a>".';
            break;
        case -1:
            $type = 'warning';
            $title = 'Ce compte adhérent a déjà accès aux tarifs réduits';
            $description = 'Aucune autre action n\'est requise de votre part';
            break;
        default:
            $type = 'error';
            $title = 'Erreur lors de l\'activation de l\'accès aux tarifs réduits';
            $description = 'Contactez <b>contact@coworking-metz.fr</b> si le problème persiste';
            break;
    }
    $response = [
        'type'        => $type,
        'title'       => $title,
        'subtitle'       => $subtitle ?? '',
        'description' => $description
    ];
    return $response;
}


function boutonVisites()
{
    if (visites_fermees())
        return;
?>
    <a href="https://rejoindre.coworking-metz.fr/" title="Prendre rendez-vous" target="_self" class="btn btn-solid btn-xlg semi-round btn-bordered border-thin ld_button_653a54d4ec23e lqd-unit-animation-done" style="">
        <span>

            <span class="btn-txt">Je prends rendez-vous !</span>

        </span>
    </a>
<?php
}
/**
 * Indique si les visites sont fermées
 * */
function visites_fermees()
{
    return get_field('fermer_visites', 'option');
}

/**
 * Forme une phrase expliquant les jours et heures de visites
 * Exemple : Les visites ont lieu les mardis et jeudis à 10:00
 * */
function recapJoursDeVisites()
{
    if (visites_fermees())
        return 'Les visites sont fermées temporairement';

    $visites = [
        'jours_de_visites' => array_map('intval', get_field('jours_de_visites', 'option')),
        'horaire' => trim(get_field('horaire', 'option')),
    ];

    $jours = ['dimanches', 'lundis', 'mardis', 'mercredis', 'jeudis', 'vendredis', 'samedis'];

    $total = count($visites['jours_de_visites']);
    $mention = '';
    foreach ($visites['jours_de_visites'] as $key => $jour) {
        if ($mention) {
            if ($key == $total - 1) {
                $mention .= ' et ';
            } else {
                $mention .= ', ';
            }
        }
        $mention .= $jours[$jour];
    }

    return 'Les visites ont lieu les ' . $mention . ' à ' . $visites['horaire'];
}
/**
 * Obtenir le nombre de visites
 * 
 * @return int Retourne le nombre de visites
 */
function getNbVisites()
{
    $ret = get_transient('getNbVisites');
    if ($ret === false) {
        $ret = count(fetch_users_with_future_visite());
        set_transient('getNbVisites', $ret, HOUR_IN_SECONDS);
    }
    return $ret;
}

/**
 * Obtenir le nombre de visites
 * 
 * @return int Retourne le nombre de visites
 */
function getNbVisitesToday()
{
    $ret = get_transient('getNbVisitesToday');
    if ($ret === false) {
        $ret = count(fetch_users_with_visite_today());
        set_transient('getNbVisitesToday', $ret, HOUR_IN_SECONDS);
    }
    return $ret;
}

/**
 * Obtenir et stocker les utilisateurs avec des visites aujourd'hui
 *
 * @return array Retourne une liste des utilisateurs avec des visites ce jour
 */
function fetch_users_with_visite_today()
{
    return fetch_users_with_visite_for_date(date('Y-m-d'));
}

/**
 * Obtenir et stocker les utilisateurs avec des visites demain
 *
 * @return array Retourne une liste des utilisateurs avec des visites ce jour
 */
function fetch_users_with_visite_tomorrow()
{
    return fetch_users_with_visite_for_date(date('Y-m-d', strtotime('+1 day')));
}


/**
 * Obtenir et stocker les utilisateurs avec des visites aujourd'hui
 *
 * @return array Retourne une liste des utilisateurs avec des visites ce jour
 */
function fetch_users_with_visite_for_date($date)
{
    $args = [
        'meta_key' => 'visite',
        'meta_value' => $date,
        'meta_compare' => 'LIKE',
    ];
    $users = get_users($args);

    $out = [];
    foreach ($users as $user) {
        $visite = get_field('visite', $user);
        $user->visite = $visite;
        $out[] = $user;
    }
    return $out;
}


function fetch_nomades_for_today()
{
    return fetch_nomades_for_date(date('Y-m-d'));
}

/**
 * Obtenir et stocker les utilisateurs avec des visites aujourd'hui
 *
 * @return array Retourne une liste des utilisateurs avec des visites ce jour
 */
function fetch_nomades_for_date($date)
{
    $args = [
        'meta_key' => 'nomade',
        'meta_value' => '1',
        'meta_compare' => '=',
    ];
    $users = get_users($args);

    $out = [];
    foreach ($users as $user) {
        $user->datesNomades = get_dates_nomades_user($user->ID);
        if (!in_array($date, $user->datesNomades)) continue;
        $out[] = $user;
    }
    return $out;
}


/**
 * Obtenir et stocker les utilisateurs avec des visites futures dans un transitoire
 *
 * @return array Retourne une liste des utilisateurs avec des visites futures
 */
function fetch_users_with_future_visite()
{
    $args = array(
        'meta_key' => 'visite',
        'meta_compare' => '>',
        'meta_value' => current_time('mysql'),
        'meta_type' => 'DATETIME',
    );

    $users = get_users($args);
    $out = [];
    foreach ($users as $user) {
        $visite = get_field('visite', $user);
        $user->visite = $visite;
        $out[] = $user;
    }
    return $out;
}

function get_dates_nomades_user($user_id)
{
    $orders = get_user_orders_with_product_category($user_id, 'tickets-nomades');
    $dates = [];
    foreach ($orders as $order) {
        foreach ($order->get_items() as $item) {
            $tmcp_data = $item->get_meta('_tmdata', true);
            foreach ($tmcp_data as $data) {
                if (isset($data['tmcp_post_fields']['tmcp_date_0'])) {
                    $dates[] = DateTime::createFromFormat('d/m/Y', $data['tmcp_post_fields']['tmcp_date_0'])->format('Y-m-d');
                    break;
                }
            }
        }
    }

    return $dates;
}


function envoyerMailAlerteNomade($user_id, $autres_codes = [])
{

    $data = get_userdata($user_id);
    if (!$data)
        return;
    $template_id = get_field('email_alerte_cowo_nomade', 'option');
    // $visite = get_user_meta($user_id, 'visite', true);

    // $key = 'email-alerte-nomade-' . $user_id;
    // if (get_user_meta($user_id, $key, true))
    //     return;
    // update_user_meta($user_id, $key, true);


    $codes = [
        ['{_user_id}' => $data->ID],
        ['{user_name}' => $data->display_name],
        ['{_user_email}' => $data->user_email],
        ['{activite}' => get_visiteur_activite($user_id)],
        ['{date_presence}' => date_francais($autres_codes['all_dates'][0])],
        ['{url_commandes_user}' => admin_url('edit.php?s&post_status=all&post_type=shop_order&_customer_user=' . $user_id)],
        ['{url_fiche_user}' => admin_url('user-edit.php?user_id=' . $user_id)],
        ['{_admin_url}' => admin_url()],

    ];

    foreach ($autres_codes as $k => $v) {
        $codes[] = ['{' . $k . '}' => $v];
    }
    $mail = charger_template_mail($template_id, $codes);
    // echo $mail['message'];exit;
    $to = get_field('destinataire_alerte', 'option');

    if (wp_get_environment_type() == 'local')
        $to = DEFAULT_TO_EMAIL;

    $headers = array('Content-Type: text/html; charset=UTF-8');
    return wp_mail($to, $mail['subject'], $mail['message'], $headers);
}

/**
 * Envoyer un mail d'alerte à un utilisateur
 * le mail ne peut pas etre envoyé plusieurs fois à un même 
 * user, même si la fonction est apellée plusieurs fois 
 *
 * @param int $user_id ID de l'utilisateur
 * @return bool Retourne true si le mail est envoyé, false sinon
 */
function envoyerMailAlerte($user_id, $autres_codes = [])
{

    $data = get_userdata($user_id);
    if (!$data)
        return;
    $template_id = get_field('email_alerte_cowo', 'option');
    $visite = get_user_meta($user_id, 'visite', true);

    $key = 'email-alerte-' . $user_id;
    if (get_user_meta($user_id, $key, true))
        return;
    update_user_meta($user_id, $key, true);


    $codes = [
        ['{_user_id}' => $data->ID],
        ['{user_name}' => $data->display_name],
        ['{_user_email}' => $data->user_email],
        ['{activite}' => get_visiteur_activite($user_id)],
        ['{date_visite}' => date_francais($visite, true)],
        ['{url_commandes_user}' => admin_url('edit.php?s&post_status=all&post_type=shop_order&_customer_user=' . $user_id)],
        ['{url_fiche_user}' => admin_url('user-edit.php?user_id=' . $user_id)],
        ['{_admin_url}' => admin_url()],
        ['{url_finaliser_compte_coworker_user}' => admin_url('user-edit.php?finaliser=true&user_id=' . $user_id)],

    ];
    foreach ($autres_codes as $k => $v) {
        $codes[] = ['{' . $k . '}' => $v];
    }
    $mail = charger_template_mail($template_id, $codes);
    // echo $mail['message'];exit;
    $to = get_field('destinataire_alerte', 'option');

    if (wp_get_environment_type() == 'local')
        $to = DEFAULT_TO_EMAIL;

    $headers = array('Content-Type: text/html; charset=UTF-8');
    return wp_mail($to, $mail['subject'], $mail['message'], $headers);
}

function mailRecapVisiteDejaEnvoye($user_id)
{
    $key = 'email-recap-visite-' . $user_id;
    if (get_user_meta($user_id, $key, true)) {
        return true;
    }
}


/**
 * Envoyer un mail à un utilisateurla veille de sa visite
 * le mail ne peut pas etre envoyé plusieurs fois à un même 
 * user, même si la fonction est apellée plusieurs fois 
 *
 * @param int $user_id ID de l'utilisateur
 * @return bool Retourne true si le mail est envoyé, false sinon
 */
function envoyerMailRappelVisite($user_id, $autres_codes = [])
{
    if (wp_get_environment_type() == 'local')
        return;

    $user = get_userdata($user_id);
    if (!$user)
        return;
    $template_id = get_field('email_rappel_visite', 'option');

    $visite = get_user_meta($user_id, 'visite', true);
    if (!$visite) return;
    $key = 'email-rappel-visite-' . $user_id;
    if (get_user_meta($user_id, $key, true))
        return;
    update_user_meta($user_id, $key, true);


    $codes = [
        ['{user_name}' => $user->display_name],
        ['{date_visite}' => date_francais($visite, true)],
        ['{date_visite_mention}' => isToday($visite) ? "aujourd'hui" : (isTomorrow($visite) ? 'demain' : date_francais($visite, true))],
        ['{url_visite_activer_compte}' => (site_url('/mon-compte/?uid=' . $user_id . '&validation-compte=' . sha1($user_id . AUTH_SALT)))],
    ];
    foreach ($autres_codes as $k => $v) {
        $codes[] = ['{' . $k . '}' => $v];
    }
    $mail = charger_template_mail($template_id, $codes);
    // m($codes,$mail);exit;
    $bcc = get_field('destinataire_alerte', 'option');

    $to = $user->user_email;
    $headers = array('Content-Type: text/html; charset=UTF-8', 'Bcc: ' . $bcc);
    return wp_mail($to, $mail['subject'], $mail['message'], $headers);
}

/**
 * Envoyer un mail à un utilisateur le soir de sa visite
 * le mail ne peut pas etre envoyé plusieurs fois à un même 
 * user, même si la fonction est apellée plusieurs fois 
 *
 * @param int $user_id ID de l'utilisateur
 * @return bool Retourne true si le mail est envoyé, false sinon
 */
function envoyerMailRecapVisite($user_id, $autres_codes = [])
{
    if (wp_get_environment_type() == 'local')
        return;
    $user = get_userdata($user_id);
    if (!$user)
        return;
    $template_id = get_field('email_recap_visite', 'option');

    $visite = get_user_meta($user_id, 'visite', true);
    if (!$visite) return;

    $key = 'email-recap-visite-' . $user_id;
    if (get_user_meta($user_id, $key, true))
        return;
    update_user_meta($user_id, $key, true);


    $codes = [
        ['{user_name}' => $user->display_name],
        ['{date_visite}' => date_francais($visite, true)],
        ['{date_visite_mention}' => isToday($visite) ? "aujourd'hui" : (isTomorrow($visite) ? 'demain' : date_francais($visite, true))],
        ['{url_visite_activer_compte}' => (site_url('/mon-compte/?uid=' . $user_id . '&validation-compte=' . sha1($user_id . AUTH_SALT)))],
    ];
    foreach ($autres_codes as $k => $v) {
        $codes[] = ['{' . $k . '}' => $v];
    }
    $mail = charger_template_mail($template_id, $codes);
    // m($codes);echo $mail['message'];exit;
    $bcc = get_field('destinataire_alerte', 'option');

    $to = $user->user_email;
    $headers = array('Content-Type: text/html; charset=UTF-8', 'Bcc: ' . $bcc);
    return wp_mail($to, $mail['subject'], $mail['message'], $headers);
}

/**
 * Envoyer un mail de confirmation de visite. 
 * le mail ne peut pas etre envoyé plusieurs fois à un même 
 * user, même si la fonction est apellée plusieurs fois 
 *
 * @param int $user_id ID de l'utilisateur
 * @param string|null $visite La date de la visite
 * @return bool Retourne true si le mail est envoyé, false sinon
 */
function envoyerMailVisite($user_id, $visite = null, $autres_codes = [])
{

    $user = get_userdata($user_id);
    if (!$user)
        return;


    if (is_null($visite)) {
        $visite = get_user_meta($user_id, 'visite', true);
    }

    $key = 'email-visite-' . $user_id;
    if (get_user_meta($user_id, $key, true))
        return;
    update_user_meta($user_id, $key, true);



    $template_id = get_field('email_confirmation_de_visite', 'option');
    $codes = [
        ['{date_visite}' => date_francais($visite, true)],
        ['{url_visite_ics}' =>   'https://www.coworking-metz.fr/api-json-wp/cowo/v1/visite-ics?user_id=' . $user_id],
        ['{app_login_link}' => app_login_link($user_id)],
    ];

    foreach ($autres_codes as $k => $v) {
        $codes[] = ['{' . $k . '}' => $v];
    }

    $mail = charger_template_mail($template_id, $codes);

    $to = $user->user_email;
    if (wp_get_environment_type() == 'local')
        $to = DEFAULT_TO_EMAIL;

    $headers = array('Content-Type: text/html; charset=UTF-8');

    return wp_mail($to, $mail['subject'], $mail['message'], $headers);
}


function envoyerMailNomade($user_id, $datesPresence = null, $autres_codes = [])
{

    $user = get_userdata($user_id);
    if (!$user)
        return;

    $datePresence = $datesPresence[0] ?? false;

    $all_dates_txt = '';
    if (count($datesPresence) > 1) {
        $all_dates = array_map(function ($date) {
            return date_francais($date);
        }, $datesPresence);
        $all_dates_txt = '(Détail des dates réservées: '.implode(', ', $all_dates).')';
    }

    // $key = 'email-nomade-' . $user_id;
    // if (get_user_meta($user_id, $key, true))
    //     return;
    // update_user_meta($user_id, $key, true);



    $template_id = get_field('email_confirmation_nomade', 'option');
    $codes = [
        ['{date_presence}' => date_francais($datePresence)],
        ['{dates_presences}' => $all_dates_txt],
        ['{buy_ticket_link}' => site_url('/boutique/ticket-journee-nomade/?al_id=' . $user_id . '&startDate=' . $datePresence)],
        ['{app_login_link}' => app_login_link($user_id)],
    ];

    foreach ($autres_codes as $k => $v) {
        $codes[] = ['{' . $k . '}' => $v];
    }
    $mail = charger_template_mail($template_id, $codes);
    $to = $user->user_email;
    if (wp_get_environment_type() == 'local')
        $to = DEFAULT_TO_EMAIL;

    $headers = array('Content-Type: text/html; charset=UTF-8');

    return wp_mail($to, $mail['subject'], $mail['message'], $headers);
}



function get_visiteur_activite($user_id)
{
    $activite = get_user_meta($user_id, 'activite', true);
    if (!$activite) {
        $activite = get_user_meta($user_id, 'polaroid_description', true);
        if (!$activite) {
            $activite = 'Non renseigné';
        }
    }
    return $activite;
}
