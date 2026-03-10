<?php


add_action('init', function () {

    if (!isset($_GET['export_statut_juridique_csv'])) {
        return;
    }

    if (!current_user_can('manage_woocommerce')) {
        wp_die('Unauthorized');
    }

    // Headers CSV
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=statut_juridique_stats_last_2_years.csv');

    $output = fopen('php://output', 'w');

    // En-têtes CSV
    fputcsv($output, [
        'Statut Juridique',
        'Nombre d\'utilisateurs',
        'Total dépensé (2 dernières années)'
    ]);

    // Date -2 ans
    $date_from = date('Y-m-d', strtotime('-2 years'));

    // Récupération des commandes
    $orders = wc_get_orders([
        'limit'        => -1,
        'status'       => ['wc-completed', 'wc-processing'],
        'date_created' => '>=' . $date_from,
        'return'       => 'ids',
    ]);

    $users_data = [];

    foreach ($orders as $order_id) {

        $order = wc_get_order($order_id);
        $user_id = $order->get_user_id();

        if (!$user_id) {
            continue; // Ignorer commandes invité
        }

        if (!isset($users_data[$user_id])) {
            $users_data[$user_id] = 0;
        }

        $users_data[$user_id] += (float) $order->get_total();
    }

    $statuts = [];

    foreach ($users_data as $user_id => $total_spent) {

        $statut = get_user_meta($user_id, 'statut_juridique', true);

        if (empty($statut)) {
            $statut = 'Non renseigné';
        }

        if (!isset($statuts[$statut])) {
            $statuts[$statut] = [
                'count' => 0,
                'total' => 0
            ];
        }

        $statuts[$statut]['count'] += 1;
        $statuts[$statut]['total'] += $total_spent;
    }

    // Écriture CSV
    foreach ($statuts as $statut => $data) {

        fputcsv($output, [
            $statut,
            $data['count'],
            wc_format_decimal($data['total'], 2)
        ]);
    }

    fclose($output);
    exit;
});
