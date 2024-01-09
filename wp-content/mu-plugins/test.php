<?php

if (isset($_GET['test'])) {
    $_GET['debug'] = true;
    add_action('init', function () {

        $users = get_users();
        $cpt = 0;
        foreach ($users as $user) {
            if ($d = get_field('date_naissance', 'user_' . $user->ID)) {
                $cpt++;

                // $annee = substr($d, 0, 4);
                // $mois = substr($d, 4, 2);
                // $jour = substr($d, 6, 2);
                // update_field('date_naissance', $annee . '/' . $mois . '/' . $jour, 'user_' . $user->ID);
                m($user->user_nicename,$d,nettoyerDate($d));
            }
        }
        exit;
    });
}
