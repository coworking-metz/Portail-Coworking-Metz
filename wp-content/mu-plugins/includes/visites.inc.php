<?php
/**
 * Passer un user en customer (Coworker) et lui envoyer le mail de creation de compte
 * Status de retour : 
 *  1 : Le compte adhérent a été finalisé
 * -1 : Compte adhérent déjà été finalisé
 * -2 : Erreur d'envoi du mail
 * -3 : Utilisateur inconnu
 * ?? : Erreur de finalisation inconnue
 */
function finaliser_user($user_id) {
    
    $status = -3;
    if(!$user_id) return $status;
    
    $user = get_userdata($user_id);
    if(!$user) return $status;

    if (in_array('subscriber', $user->roles) || in_array('bookmify-customer', $user->roles)) {
        $user->set_role('customer');
        if (envoyer_email_creation_compte($user)) {
            $status=1;
        } else $status = -2;
    } else $status = -1;

    return $status;
}

/**
 * Retourne une explication textuelle du statut de retour de finalisation d'un user
 */
function finaliser_status_details($status) {


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
        'subtitle'       => $subtitle??'',
        'description' => $description
    ];
    return $response;
}



function boutonVisites()
{
    if (visites_fermees())
        return;
    ?>
    <a href="https://rejoindre.coworking-metz.fr/" title="Prendre rendez-vous" target="_self"
        class="btn btn-solid btn-xlg semi-round btn-bordered border-thin ld_button_653a54d4ec23e lqd-unit-animation-done"
        style="">
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
    return count(fetch_users_with_future_visite());
}

/**
 * Obtenir le nombre de visites
 * 
 * @return int Retourne le nombre de visites
 */
function getNbVisitesToday()
{
    return count(fetch_users_with_visite_today());
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
    if (wp_get_environment_type() == 'local')
        return;

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
        ['{date_visite}' => date_francais($visite, true)],
        ['{url_commandes_user}' => admin_url('edit.php?s&post_status=all&post_type=shop_order&_customer_user=' . $user_id)],
        ['{url_fiche_user}' => admin_url('user-edit.php?user_id=' . $user_id)],
        ['{url_finaliser_compte_coworker_user}' => admin_url('user-edit.php?finaliser=true&user_id=' . $user_id)],

    ];
    foreach ($autres_codes as $k => $v) {
        $codes[] = ['{' . $k . '}' => $v];
    }
    $mail = charger_template_mail($template_id, $codes);
    // echo $mail['message'];exit;
    $to = get_field('destinataire_alerte', 'option');
    $headers = array('Content-Type: text/html; charset=UTF-8');
    return wp_mail($to, $mail['subject'], $mail['message'], $headers);
}

function mailRecapVisiteDejaEnvoye($user_id) {
    $key = 'email-recap-visite-' . $user_id;
    if (get_user_meta($user_id, $key, true)) {
        return true;
    }

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

    $key = 'email-recap-visite-' . $user_id;
    if (get_user_meta($user_id, $key, true))
        return;
    update_user_meta($user_id, $key, true);


    $codes = [
        ['{user_name}' => $user->display_name],
        ['{date_visite}' => date_francais($visite, true)],
        ['{date_visite_mention}' => isToday($visite)? "aujourd'hui" :date_francais($visite, true)],
        ['{url_visite_activer_compte}' => site_url('/mon-compte/?uid='.$user_id.'&validation-compte=' . sha1($user_id.AUTH_SALT))],
    ];
    foreach ($autres_codes as $k => $v) {
        $codes[] = ['{' . $k . '}' => $v];
    }
    $mail = charger_template_mail($template_id, $codes);
    // m($codes);echo $mail['message'];exit;
    $bcc = get_field('destinataire_alerte', 'option');

    $to = $user->user_email;
    $headers = array('Content-Type: text/html; charset=UTF-8', 'Bcc: '.$bcc);
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

    if (wp_get_environment_type() == 'local')
        return;
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
    $headers = array('Content-Type: text/html; charset=UTF-8');

    return wp_mail($to, $mail['subject'], $mail['message'], $headers);
}

