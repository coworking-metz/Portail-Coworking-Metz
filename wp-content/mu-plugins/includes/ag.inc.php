<?php

include __DIR__ . '/ag/main.inc.php';


if (isset($_GET['purge-votes-ca'])) {
    add_action('admin_init', function () {
        $date = ag_date();
        $keys = ['a-vote-' . $date, 'votes-' . $date];

        // Récupération de tous les utilisateurs
        $users = get_users();
        foreach ($users as $user) {
            foreach ($keys as $key) {
                // Vérification de l'existence de la métadonnée
                if ($value = get_user_meta($user->ID, $key, true)) {
                    // Renommage de la métadonnée
                    update_user_meta($user->ID, '_trash-' . $key, $value);
                    // Suppression de la métadonnée originale
                    delete_user_meta($user->ID, $key);
                }
            }
        }
        wp_redirect($_SERVER['HTTP_REFERER']);
        exit;
    });

}