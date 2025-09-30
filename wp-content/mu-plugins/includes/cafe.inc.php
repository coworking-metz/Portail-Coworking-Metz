<?php
	
function get_users_with_contribution_cafe_active() {

    // Check transient
    $cached = get_transient('users_with_contribution_cafe_active');

    if ($cached !== false) {
        return $cached;
    }

    $abonnements = getProductsOfType('abonnement');
    $carnets = getProductsOfType('carnet-tickets');
    $cafes = get_contributions_cafe();
	$pids = array_merge(array_column($abonnements,'ID'), array_column($carnets,'ID'));

    $commandes = get_last_order_per_user(['products_ids'=>$pids]);
	// garder seulement les commandes avec café
    $commandes = array_filter($commandes, function($commande) use($cafes) {
        foreach($cafes as $cafe_id) {
            if (in_array($cafe_id, $commande['products_ids'])) return true;
        }
        return false;
    });


    $commandes = array_filter(array_map(function($commande) use ($abonnements, $carnets) {
        foreach ($abonnements as $abonnement) {
            if (in_array($abonnement->ID, $commande['products_ids'])) {
                $commande['abonnement'] = $commande['quantities'][$abonnement->ID];
            }
        }
        foreach ($carnets as $carnet) {
            if (in_array($carnet->ID, $commande['products_ids'])) {
                $commande['tickets'] = $commande['quantities'][$carnet->ID];
            }
        }
        if (empty($commande['tickets']) && empty($commande['abonnement'])) return false;

        return $commande;
    }, $commandes));

    // Collect user IDs
    $user_ids = [];
    foreach ($commandes as $commande) {
        $user_ids[] = $commande['user_id'];
    }

    $result = array_values(array_unique(array_filter($user_ids)));

    // Cache the result for 1 day
    set_transient('users_with_contribution_cafe_active', $result, DAY_IN_SECONDS);

    return $result;
}
