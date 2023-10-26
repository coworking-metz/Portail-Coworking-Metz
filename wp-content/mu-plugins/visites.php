<?php


/**
 * Filtrer la page des visites pour ne garder que les users ayant une viiste future (et on met aussi toutes les visites du mois passé)
 */
if (isset($_GET['visitesOnly'])) {
    add_action('pre_get_users', function ($query) {
        if (is_admin()) {
            $query->set('meta_key', 'visite');
            $query->set('meta_value', date('Y-m-d H:i:s', strtotime('-1 month')));
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
 * Remplir les champs de choix des templates dans la page des reglages des visites avec la liste des templates d'emails
 */
add_action('init', function () {
    $args = array(
        'post_type' => 'viwec_template',
        'post_status' => 'publish',
        'posts_per_page' => -1,
    );
    $posts = get_posts($args);

    foreach (['email_alerte_cowo', 'email_confirmation_de_visite', 'email_finalisation_compte'] as $nom_champ) {
        add_filter('acf/load_field/name=' . $nom_champ, function ($field) use ($posts) {

            $field['choices'] = [''];
            if (!empty($posts)) {
                foreach ($posts as $post) {
                    $field['choices'][$post->ID] = '#' . $post->ID . ' - ' . $post->post_title;
                }
            }

            return $field;
        });
    }
});

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
                const selectFields = document.querySelectorAll('[data-name*="email_"][data-type="select"] select');
                selectFields.forEach(function(select) {
                    console.log(select);

                    const linkVoir = document.createElement('a');
                    linkVoir.target = '_blank';
                    linkVoir.innerText = 'Voir ce template';
                    select.parentNode.appendChild(linkVoir);

                    function updateLinkVoir() {
                        const value = this.value;
                        if (this.value > 0) {
                            linkVoir.classList.remove('hidden')
                            linkVoir.href = `/wp-admin/?template_preview=${value}`;
                        } else {
                            linkVoir.classList.add('hidden')
                        }
                    }

                    // Lien initial
                    updateLinkVoir.call(select);

                    const span = document.createElement('span');
                    span.innerHTML = ' &nbsp; ';
                    select.parentNode.appendChild(span);

                    const linkModifier = document.createElement('a');
                    linkModifier.target = '_blank';
                    linkModifier.innerText = 'Modifier ce template';
                    select.parentNode.appendChild(linkModifier);

                    function updateLinkModifier() {
                        const value = this.value;
                        if (this.value > 0) {
                            linkModifier.classList.remove('hidden')
                            linkModifier.href = `post.php?post=${value}&action=edit&classic-editor`;
                        } else {
                            linkModifier.classList.add('hidden')
                        }

                    }

                    // Lien initial
                    updateLinkModifier.call(select);

                    select.addEventListener('change', function() {
                        // Mettre à jour le lien lors du changement de sélection
                        updateLinkVoir.call(this);
                        updateLinkModifier.call(this);
                    });
                });
            });
        </script>
<?php
    }
});


/**
 * Ajouter un lien vers la plateforme d'oboarding dans la menu bar de la page des reglages de visites
 */
add_action('admin_bar_menu', function ($admin_bar) {
    if (!is_admin()) return;
    $screen = get_current_screen();
    if ($screen->base != 'user-edit') return;
    $user_id = $_GET['user_id'] ?? false;
    if (!$user_id) return;
    $user_info = get_userdata($user_id);
    $roles = $user_info->roles;
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
