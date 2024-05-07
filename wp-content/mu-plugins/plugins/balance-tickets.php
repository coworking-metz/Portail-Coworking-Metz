<?php

define('PRODUIT_TICKET_UNITE', 3021);
define('PRODUIT_CARNET_TICKETS', 3022);

/**
 * Afficher une notification relative au status retourné lors de la validation d'un compte
 * 
 */
    add_action('wp_footer', function() {

        $uri = get_current_uri();
        if($uri == '/mon-compte') return;
        if($uri == '/boutique/ticket-1-journee') return;
        
        $uid = get_current_user_id();
        if(!$uid) return;


        if (is_product_in_cart(PRODUIT_TICKET_UNITE)) return;
        if (is_product_in_cart(PRODUIT_CARNET_TICKETS)) return;



        $stats = get_user_balance($uid);
        if($stats['balance']>=0) return;
        $abos_en_cours = $stats['lastAboEnd']>=date('Y-m-d');
        $titre = 'Solde de tickets débiteur';
        $texte = 'Votre balance est de <b>'.$stats['balance'].' ticket(s)</b>.';
        if($abos_en_cours || abs($stats['balance']) > 10) {
            $texte.=' Vous pouvez <a href="/boutique/ticket-1-journee/">acheter des tickets à l\'unité</a> pour régulariser la situation.';
        } else {
            $texte.=' Vous pouvez commander <a href="/boutique/ticket-1-journee/">un carnet de tickets</a> pour régulariser la situation.';
        }
        echo generateNotification([
            'image'=>'/wp-content/uploads/2015/08/ticket-lepoulailler.png',
            'titre' => $titre,
            'texte' => $texte,
            'cta'=>['url'=>'/mon-compte/','caption'=>'En savoir plus']
        ]);
    });
