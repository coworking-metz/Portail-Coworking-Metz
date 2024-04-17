<?php

/**
 * Filtrer la page des visites pour ne garder que les users ayant une visite future (et on met aussi toutes les visites de la semaine passée)
 */
if (isset($_GET['visitesOnly'])) {
    add_action('pre_get_users', function ($query) {
        if (is_admin()) {
            $query->set('meta_key', 'visite');
            $query->set('meta_value', date('Y-m-d H:i:s', strtotime('last monday')));
            $query->set('meta_compare', '>');
        }
    });
}


/**
 * Flux ics des visites via l'url /wp-admin/?visites-ics
 */
if (isset($_GET['visites-ics'])) {
    add_action('init', function () {
        $args = [
            'meta_key' => 'visite'
        ];
        $users = get_users($args);

        // Initialiser le fichier ICS
        header('Content-Type: text/calendar; charset=utf-8');
        header('Content-Disposition: attachment; filename=visites-' . date('Y-m-d-h-i-s') . '.ics');

        echo "BEGIN:VCALENDAR\r\n";
        echo "VERSION:2.0\r\n";
        echo "PRODID:-//Coworking Metz//Visites//EN\r\n";

        // Pour chaque utilisateur, créer un événement ICS
        foreach ($users as $user) {
            $visite_date = get_user_meta($user->ID, 'visite', true);  // Récupérer la date de visite
            $formatted_start_date = date('Ymd\THis', strtotime($visite_date));
            $formatted_end_date = date('Ymd\THis', strtotime("+30 minutes", strtotime($visite_date)));

            $admin_url = get_admin_url() . "user-edit.php?user_id=" . $user->ID;

            // Créer un événement ICS pour cette visite
            echo "BEGIN:VEVENT\r\n";
            echo "UID:" . uniqid() . "\r\n";
            echo "DTSTAMP:" . gmdate('Ymd\THis\Z') . "\r\n";
            echo "DTSTART;TZID=Europe/Paris:" . $formatted_start_date . "\r\n";
            echo "DTEND;TZID=Europe/Paris:" . $formatted_end_date . "\r\n";
            echo "SUMMARY:Visite de " . $user->display_name . " (" . $user->user_email . ")\r\n";
            echo "DESCRIPTION:Fiche: " . $admin_url . "\r\n";
            echo "END:VEVENT\r\n";
        }

        echo "END:VCALENDAR\r\n";
        exit;
    });
}



/**
 * gestion des liens voir / Modifier ce template dans la page de reglages des visites
 */
add_action('admin_footer', function () {
    $screen = get_current_screen();
    if ($screen->base == 'toplevel_page_reglages-visites') {
?>
        <script type="text/javascript">
            document.addEventListener('DOMContentLoaded', function() {

                document.querySelector('[data-name="email_finalisation_compte"] select').disabled = true;

            });
        </script>
<?php
    }
});


/**
 * Finaliser un compte
 */
add_action('admin_bar_menu', function ($admin_bar) {
    if (!is_admin()) return;
    $screen = get_current_screen();
    if ($screen->base != 'user-edit') return;
    $user_id = $_GET['user_id'] ?? false;
    if (!$user_id) return;
    $user_info = get_userdata($user_id);
    $roles = $user_info->roles;

    

    // $admin_bar->add_menu(array(
    //     'id'    => 'app_login_link',
    //     'title' => 'Se connecter dans l\'app en tant que',
    //     'href'  => app_login_link($user_id),
    //     'meta'  => array(
    //         'target' => '_blank',
    //     ),
    // ));
    if (!mailRecapVisiteDejaEnvoye($user_id)) {

        $admin_bar->add_menu(array(
            'id'    => 'mail-recap',
            'title' => 'Envoyer le mail recapitulatif de visite',
            'href'  => admin_url('user-edit.php?mail-recap-visite&user_id=' . $user_id),
            'meta'  => array(
                'onclick' => 'return confirm("Confirmez cet envoi ? Note : si la personne a déjà reçu le mail de visite, elle ne le recevera pas une seconde fois.")',
                'title' => __('Envoyer le mail recap de visite. Une confirmation vous sera demandée'),
            ),
        ));
    }

    if (!in_array('subscriber', $roles) && !in_array('bookmify-customer', $roles)) return;

    $admin_bar->add_menu(array(
        'id'    => 'finaliser',
        'title' => 'Finaliser le compte',
        'href'  => admin_url('user-edit.php?finaliser&user_id=' . $user_id),
        'meta'  => array(
            'onclick' => 'return confirm("Faire de ce compte un compte coworker ? Il passera au rôle Coworker et recevra le mail de création de compte.")',
            'title' => __('Faire de ce compte un compte coworker. Une confirmation vous sera demandée'),
        ),
    ));
}, 100);


/**
 * Ajouter un lien vers la plateforme d'oboarding dans la menu bar de la page des reglages de visites
 */
add_action('admin_bar_menu', function ($admin_bar) {
    if (is_admin()) {
        $screen = get_current_screen();
        if ($screen->base == 'toplevel_page_reglages-visites') {
            $admin_bar->add_menu(array(
                'id'    => 'voir',
                'title' => 'Voir la page de prise de rendez-vous',
                'href'  => 'https://rejoindre.coworking-metz.fr',
                'meta'  => array(
                    'target' => '_blank',
                    'title' => __('Voir la page'),
                ),
            ));
        }
    }
}, 100);

// Creation du menu "Visites" le menu admin de WP
add_action('admin_menu', function () {
    add_menu_page(
        'Visites',
        'Visites',
        'manage_options',
        'users.php?orderby=visite&order=desc',
        '',
        'dashicons-calendar',
        2
    );
});


// Ajouter une puce rouge avec le nombre de visites à venir
add_action('admin_menu', function () {
    global $menu;

    // Ajouter un nombre rouge
    foreach ($menu as $key => $value) {
        if ($menu[$key][0] == 'Visites') {

            $menu[$key][0] .= ' <span class="update-plugins count-1"><span class="update-count">' . getNbVisites() . '</span></span>';
            break;
        }
    }
}, 999);
