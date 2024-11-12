<?php

define('PRODUIT_TICKET_UNITE', 35573);
define('PRODUIT_CARNET_TICKETS', 3022);

/**
 * Afficher une notification relative au status retourné lors de la validation d'un compte
 * 
 */
add_action('wp_head', function () {
    $uid = get_current_user_id();
    if (!$uid) return;

    $uri = get_current_uri();

    if (is_product()) {
        global $post;
        if ($post->ID == PRODUIT_TICKET_UNITE) return;
        if ($post->ID == PRODUIT_CARNET_TICKETS) return;
    }


    if (is_product_in_cart(PRODUIT_TICKET_UNITE)) return;
    if (is_product_in_cart(PRODUIT_CARNET_TICKETS)) return;



    $stats = get_user_balance($uid);
    if ($stats['balance'] >= 0) return;
    $abos_en_cours = ($stats['lastAboEnd']??false) >= date('Y-m-d');
    $titre = 'Solde de tickets débiteur';
    $texte = 'Votre balance est de <b>' . $stats['balance'] . ' ticket(s)</b>.';
    $fermer = true;
    $blocked = false;
    $type = false;
    $total = round(abs($stats['balance']));
    if ($abos_en_cours || $total > 3) {
        // $blocked = true;
        $fermer = false;
        $type = 'error';
        $texte .= ' Vous devez acheter des tickets à l\'unité pour régulariser la situation. <a href="/mon-compte/">En savoir plus</a>.';
        $cta = ['url' => '/boutique/ticket-1-journee/?quantite=' . $total, 'caption' => 'Acheter des tickets'];
    } else {
        $texte .= ' Vous pouvez commander <a href="/boutique/ticket-1-journee/">un carnet de tickets</a> pour régulariser la situation.';
        $cta = ['url' => '/mon-compte/', 'caption' => 'En savoir plus'];
    }
    $data = [
        'type' => $type,
        'fermer' => $fermer,
        'image' => '/wp-content/uploads/2015/08/ticket-lepoulailler.png',
        'titre' => $titre,
        'texte' => $texte,
        'cta' => $cta
    ];
    if (!$blocked && ($uri == '/mon-compte' || strstr($uri, 'commande-recue'))) return;
    if ($blocked) {
?>
        <script type="text/javascript">
            const PRODUIT_TICKET_UNITE = <?= PRODUIT_TICKET_UNITE; ?>;
            document.addEventListener('DOMContentLoaded', function() {
                const query = ['.add_to_cart_button:not(.single_add_to_cart_button)'];
                if (!document.querySelector('.postid-' + PRODUIT_TICKET_UNITE)) query.push('.single_add_to_cart_button');
                const addToCartButtons = document.querySelectorAll(query.join(', '));

                addToCartButtons.forEach(function(button) {
                    button.setAttribute('style', 'opacity:0.8;cursor:not-allowed')
                    // Remove existing event listeners that can be cloned
                    const oldButton = button;
                    const newButton = oldButton.cloneNode(true);
                    oldButton.parentNode.replaceChild(newButton, oldButton);

                    // Add new event listener to show an alert
                    newButton.addEventListener('click', function(event) {
                        event.preventDefault(); // Prevent the button from doing its default button action
                        if (confirm('Impossible de commander car votre compte est débiteur.\nVoulez-vous en savoir plus ?')) document.location.href = '/mon-compte';
                    });
                });
            });
        </script>
<?php
    }

    sendNotification($data);
});
