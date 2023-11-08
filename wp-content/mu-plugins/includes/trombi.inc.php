<?php
/**
 * Send an alert email to the user for an event
 * 
 * @param int $user_id The ID of the user to alert.
 * @param string $dateDuJour The date of the event.
 * @return void|bool Whether the email was sent successfully.
 */
function avent_email_alerte($user_id, $dateDuJour)
{

    $data = get_userdata($user_id);
    if (!$data) return;
    $template_id = get_field('avent', 'option')['email_alerte_avent'];
    $adresse_email_alerte = get_field('avent', 'option')['adresse_email_alerte'];


    $key = 'email-alerte-avent-' . $user_id;
    if (get_user_meta($user_id, $key, true)) return;
    update_user_meta($user_id, $key, true);

    $codes = [
        ['{user_name}' => $data->display_name],
        ['{date_du_jour}' => $dateDuJour],
        ['{date_du_jour_fr}' => date_francais(date('Y-m-d', DateTime::createFromFormat('d/m/Y', $dateDuJour)->getTimestamp()))],
    ];

    $mail = charger_template_mail($template_id, $codes);
    // echo $mail['message'];exit;
    $to  = $data->user_email;
    $headers = array('Content-Type: text/html; charset=UTF-8');
    if ($adresse_email_alerte) {
        $headers[] = 'Bcc: ' . $adresse_email_alerte;
    }
    return wp_mail($to, $mail['subject'], $mail['message'], $headers);
}

/**
 * Set the draw results for an event
 * 
 * @param array $tirages The draw results to set.
 * @return void
 */
function avent_set_tirages($tirages)
{
    update_field('avent_tirages_' . date('Y'), json_encode($tirages), 'option');
}
/**
 * Add a draw result for a user on a specific date
 * 
 * @param int $user_id The ID of the user who won the draw.
 * @param string $date_tirage The date of the draw.
 * @return array The updated draw results.
 */
function avent_add_tirage($user_id, $date_tirage)
{
    if (!$date_tirage) return;
    if (!$user_id) return;

    $tirages = avent_get_tirages();
    if (!isset($tirages[$date_tirage])) {
        $tirages[$date_tirage] = intval($user_id);
        uksort($tirages, function ($a, $b) {
            $dateA = DateTime::createFromFormat('d/m/Y', $a);
            $dateB = DateTime::createFromFormat('d/m/Y', $b);
            return $dateB <=> $dateA;
        });        
        update_field('avent_tirages_' . date('Y'), json_encode($tirages), 'option');
    }
    return $tirages;
}


/**
 * Retrieve the draw results for an event
 * 
 * @return array The draw results.
 */
function avent_get_tirages()
{
    $tirages = get_field('avent_tirages_' . date('Y'), 'option');
    $tirages = json_decode($tirages, true);
    if (!$tirages) {
        $tirages = [];
    }

    return $tirages;
}
