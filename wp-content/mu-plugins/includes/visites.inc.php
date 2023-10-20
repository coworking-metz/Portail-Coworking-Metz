<?php

/*
{url_fiche_user}
{user_name}
{date_visite}
{url_finaliser_compte_coworker_user} // role cowo + envoi mail
*/

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
    add_custom_shortcodes_to_template($codes);

    $template = new VIWEC_Render_Email_Template(['template_id' => $template_id]);
    ob_start();
    $template->get_content();
    $message = ob_get_contents();
    ob_end_clean();

    $to  = $user->user_email;
    $to = 'gilles@lesfrancois.com';
    $headers = array('Content-Type: text/html; charset=UTF-8');
    return wp_mail($to, 'Votre visite au Coworking', $message, $headers);
}
