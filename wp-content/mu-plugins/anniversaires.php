<?php




/**
 * Flux ics des anniversaires via l'url /wp-admin/?anniversaires-ics
 */
if (isset($_GET['anniversaires-ics'])) {
    add_action('init', function () {
        $args = [
            'meta_key' => 'date_naissance'
        ];
        $users = get_users($args);

        
//        Initialiser le fichier ICS
        header('Content-Type: text/calendar; charset=utf-8');
        header('Content-Disposition: attachment; filename=anniversaires-' . date('Y-m-d-h-i-s') . '.ics');

        echo "BEGIN:VCALENDAR\r\n";
        echo "VERSION:2.0\r\n";
        echo "PRODID:-//Coworking Metz//anniversaires//EN\r\n";

        // Pour chaque utilisateur, créer un événement ICS
        foreach ($users as $user) {
            $date_naissance = get_user_meta($user->ID, 'date_naissance', true); 
            if(strtotime($date_naissance)) {
                $Y=date('Y');
                $formatted_start_date = $Y . date('md', strtotime($date_naissance));
                $formatted_end_date = $Y . date('md', strtotime($date_naissance . ' +1 day'));
        
            $admin_url = get_admin_url() . "user-edit.php?user_id=" . $user->ID;

            echo "BEGIN:VEVENT\r\n";
            echo "UID:" . uniqid() . "\r\n";
            echo "DTSTAMP:" . gmdate('Ymd\THis\Z') . "\r\n";
            echo "DTSTART;VALUE=DATE:" . $formatted_start_date . "\r\n";
            echo "DTEND;VALUE=DATE:" . $formatted_end_date . "\r\n";
            echo "SUMMARY:" . $user->display_name . "/ Anniversaire \r\n";
            echo "DESCRIPTION:" . $user->user_email . " / Fiche: " . $admin_url . "\r\n";
            echo "END:VEVENT\r\n";
        }
    }

        echo "END:VCALENDAR\r\n";
        exit;
    });
}
