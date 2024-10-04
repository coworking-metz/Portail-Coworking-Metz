<?php

/**
 * Afficher une notif lors du checkout pour inviter les gens a ajouter le café dans leur panier s'il n'y est pas déja
 */
// add_action('wp_footer', function () {


//     $uri = $_SERVER['REQUEST_URI'];
//     if (strstr($uri, '/mon-compte/appareils/')) return;
//     $uid = get_current_user_id();
//     if (!$uid) return;
//     $devices = getDevices();

//     if (count($devices)) return;

//     echo generateNotification([
//         'type' => 'warning',
//         'titre' => 'Compte incomplet',
//         'texte' => "Attention, vous n'avez associé aucun appareil. Vous devez ajouter au moins un appareil (ordinateur, tablette, etc.) pour compléter votre compte.",
//         'cta' => [
//             'url' => '/mon-compte/appareils/',
//             'caption' => 'Ajouter un appareil'
//         ],
//     ]);
// }, 99);
