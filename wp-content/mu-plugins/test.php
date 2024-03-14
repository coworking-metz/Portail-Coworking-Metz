<?php

if (isset($_GET['test'])) {
    $_GET['debug'] = true;
    add_action('init', function () {

        $visiteurs = fetch_users_with_visite_today();

        foreach ($visiteurs as $visiteur) {

            // on ignore les utilisateurs qui sont déjà au statut customer : ils ont déjà été validés manuellement
            if (in_array('customer', $visiteur->roles))
                continue;

            envoyerMailRecapVisite($visiteur->ID);
            m($visiteur);

        }

        exit;
    });
}
