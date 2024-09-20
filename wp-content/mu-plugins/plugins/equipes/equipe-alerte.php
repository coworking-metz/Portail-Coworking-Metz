<?php

/**
 * Afficher une notif lors du checkout pour inviter les gens a ajouter le café dans leur panier s'il n'y est pas déja
 */
add_action('wp_footer', function () {


    $uri = $_SERVER['REQUEST_URI'];
    if(strstr($uri,'/mon-compte/equipe/')) return;
    $uid = get_current_user_id();
    if(!$uid) return;
    $equipe = getMonEquipe($uid, true);
    if (!$equipe) return;
    $debiteurs = [];

    foreach ($equipe['membres'] as $membre) {
        $balance = $membre['balance']['balance'];
        $aboActif = isAboEnCours($membre['balance']['lastAboEnd']);
        if ($aboActif) continue;
        if ($balance > 0) continue;
        $debiteurs[] = $membre;
    }
    if (!count($debiteurs)) return;
    echo generateNotification([
        // 'type' => 'warning',
        'titre' => 'Alerte abonnement / tickets',
        'texte' => 'Les personnes suivantes de votre équipe '.$equipe->post_title.' présentent un compte débiteur: <b>' . implode('</b>, <b>', array_column($debiteurs, 'display_name')).'</b>',
        'cta' => [
            'url' => '/mon-compte/equipe/',
            'caption' => 'Régulariser la situation'
        ],
        // 'image' => '/images/cafe.jpg'
    ]);
}, 99);
