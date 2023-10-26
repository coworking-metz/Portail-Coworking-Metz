<?php

/**
 * Telecharger un fichier ICS de la date de visite du compte utilisateur
 */
add_action('rest_api_init', function () {
    register_rest_route('cowo/v1', '/visite-ics', [
        'methods' => 'GET',
        'callback' => function (WP_REST_Request $request) {

            $user_id = $request->get_param('user_id');
            if (!$user_id) {
                return new WP_Error('no_user', 'user not found', ['status' => 404]);
            }

            $visite_date = get_user_meta($user_id, 'visite', true);

            if ($visite_date) {
                $datetime = new DateTime($visite_date, new DateTimeZone('Europe/Paris'));
                $datetime->setTimezone(new DateTimeZone('UTC'));
                $dtstart = $datetime->format('Ymd\THis\Z');

                $datetime->modify('+30 minutes');
                $dtend = $datetime->format('Ymd\THis\Z');

                $ics_content = "BEGIN:VCALENDAR\r\n";
                $ics_content .= "VERSION:2.0\r\n";
                $ics_content .= "X-WR-CALNAME:Coworking Metz - Visite\r\n"; // Ajout de X-WR-CALNAME
                $ics_content .= "X-WR-TIMEZONE:Europe/Paris\r\n"; // Ajout de X-WR-TIMEZONE
                $ics_content .= "X-WR-CALDESC:Date et heure de votre visite\r\n"; // Ajout de X-WR-CALDESC
                $ics_content .= "BEGIN:VEVENT\r\n";
                $ics_content .= "DTSTART:$dtstart\r\n";
                $ics_content .= "DTEND:$dtend\r\n";
                $ics_content .= "LOCATION:7, avenue de Blida, 57000 Metz\r\n"; // Ajout de l'adresse
                $ics_content .= "SUMMARY:Votre visite du coworking\r\n";
                $ics_content .= "END:VEVENT\r\n";
                $ics_content .= "END:VCALENDAR\r\n";

                header('Content-Type: text/calendar; charset=utf-8');
                header('Content-Disposition: attachment; filename="visite.ics"');
                echo $ics_content;
                exit;
            }
        },
    ]);
});
