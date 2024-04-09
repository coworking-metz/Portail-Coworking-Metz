<?php

if (isset($_GET['test'])) {
    $_GET['debug'] = true;
    add_action('init', function () {
        if (!class_exists('WooCommerce')) return; // S'assurer que WooCommerce est activé

        // Définir la période de recherche
        $date_debut = '2024-01-01 00:00:00';
        $date_fin = '2024-12-31 23:59:59';
    
        // Arguments de la requête pour récupérer les commandes
        $args = array(
            'limit' => -1, // Pas de limite sur le nombre de résultats
            'status' => array('wc-completed', 'wc-processing', 'wc-on-hold'), // Inclure plusieurs statuts si nécessaire
            'type' => 'shop_order',
            'date_query' => array(
                'after' => $date_debut,
                'before' => $date_fin,
                'inclusive' => true,
            ),
            'return' => 'ids',
        );
    
        // Récupération des commandes
        $orders = wc_get_orders($args);
        // Affichage des liens vers les factures PDF (assurez-vous que le plugin de facturation est compatible et fournit une telle URL)
        foreach ($orders as $order_id) {

            $invoice_url = 'https://www.coworking-metz.fr/wp-admin/post.php?post='.$order_id.'&action=edit&bewpi_action=view&nonce=28536c4063';
            echo "<a href='{$invoice_url}' target='_blank'>Facture PDF pour la commande {$order_id}</a><br />";
        }
        exit;
    });
}
