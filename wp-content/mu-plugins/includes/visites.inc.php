<?php

/*
{url_fiche_user}
{user_name}
{date_visite}
{url_finaliser_compte_coworker_user} // role cowo + envoi mail
*/
function envoyerMailAlerte($user_id) {

    $data = get_userdata($user_id);
    if(!$data) return;
    $template_id = get_field('email_alerte_cowo', 'option');
    $visite = get_user_meta($user_id, 'visite', true);

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
    $to  = get_field('destinataire_alerte','option');
    $headers = array('Content-Type: text/html; charset=UTF-8');
    return wp_mail($to, $mail['subject'], $mail['message'], $headers);
}

function envoyerMailVisite($user_id, $visite = null)
{
    $user = get_userdata($user_id);
    if (!$user) return;


    if (is_null($visite)) {
        $visite = get_user_meta($user_id, 'visite', true);
    }

    $key = 'email-visite-' . $visite;
    // if (get_user_meta($user_id, $key, true)) return;
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
