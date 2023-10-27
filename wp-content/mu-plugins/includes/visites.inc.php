<?php

function boutonVisites()
{
    if (visites_fermees()) return;
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
    if (visites_fermees()) return 'Les visites sont fermées temporairement';

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
 * Obtenir et stocker les utilisateurs avec des visites futures dans un transitoire
 *
 * @return array Retourne une liste des utilisateurs avec des visites futures
 */
function fetch_users_with_future_visite()
{
    $args = array(
        'meta_key'     => 'visite',
        'meta_compare' => '>',
        'meta_value'   => current_time('mysql'),
        'meta_type'    => 'DATETIME',
    );

    $users_with_future_visite = get_users($args);

    return $users_with_future_visite;
}

/**
 * Envoyer un mail d'alerte à un utilisateur
 * le mail ne peut pas etre envoyé plusieurs fois à un même 
 * user, même si la fonction est apellée plusieurs fois 
 *
 * @param int $user_id ID de l'utilisateur
 * @return bool Retourne true si le mail est envoyé, false sinon
 */
function envoyerMailAlerte($user_id)
{

    $data = get_userdata($user_id);
    if (!$data) return;
    $template_id = get_field('email_alerte_cowo', 'option');
    $visite = get_user_meta($user_id, 'visite', true);

    $key = 'email-alerte-' . $user_id;
    if (get_user_meta($user_id, $key, true)) return;
    update_user_meta($user_id, $key, true);


    $codes = [
        ['{user_name}' => $data->display_name],
        ['{_user_email}' => $data->user_email],
        ['{date_visite}' => date_francais($visite, true)],
        ['{url_commandes_user}' => admin_url('edit.php?s&post_status=all&post_type=shop_order&_customer_user=' . $user_id)],
        ['{url_fiche_user}' => admin_url('user-edit.php?user_id=' . $user_id)],
        ['{url_finaliser_compte_coworker_user}' => admin_url('user-edit.php?finaliser=true&user_id=' . $user_id)],

    ];

    $mail = charger_template_mail($template_id, $codes);
    // echo $mail['message'];exit;
    $to  = get_field('destinataire_alerte', 'option');
    $headers = array('Content-Type: text/html; charset=UTF-8');
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
function envoyerMailVisite($user_id, $visite = null)
{
    $user = get_userdata($user_id);
    if (!$user) return;


    if (is_null($visite)) {
        $visite = get_user_meta($user_id, 'visite', true);
    }

    $key = 'email-visite-' . $user_id;
    if (get_user_meta($user_id, $key, true)) return;
    update_user_meta($user_id, $key, true);



    $template_id = get_field('email_confirmation_de_visite', 'option');
    $codes = [
        ['{date_visite}' => date_francais($visite, true)],
        ['{url_visite_ics}' => site_url() . '/api-json-wp/cowo/v1/visite-ics?user_id=' . $user_id],
        ['{app_login_link}' => app_login_link($user_id)],
    ];

    $mail = charger_template_mail($template_id, $codes);

    $to  = $user->user_email;
    $headers = array('Content-Type: text/html; charset=UTF-8');
    return wp_mail($to, $mail['subject'], $mail['message'], $headers);
}
