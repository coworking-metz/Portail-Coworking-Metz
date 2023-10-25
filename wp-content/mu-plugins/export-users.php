<?php

/***
 * Creation de l'export CSV des users
 */
if (isset($_GET['export-users'])) {
    add_action('admin_init', function () {
        $recents = isset($_GET['recents']);

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=users-' . wp_date('Y-m-d-H-i-s') . '.csv');

        $output = fopen('php://output', 'w');

        // Écrire les en-têtes de colonnes
        fputcsv($output, ['ID', 'Email', 'Display Name', 'Registration Date', '_last_order_date', '_first_order_date', 'Date de la visite', 'Role']);

        $args = ['fields' => ['ID']];

        if ($recents) {
            $date_six_months_ago = date('Y-m-d', strtotime('-6 months'));
            $args = [
                'fields' => ['ID'],
                'meta_query' => [
                    [
                        'key' => '_last_order_date',
                        'value' => $date_six_months_ago,
                        'compare' => '>',
                        'type' => 'DATE'
                    ]
                ]
            ];
        }
        $users = get_users($args);

        foreach ($users as $user) {
            $user_data = get_userdata($user->ID);

            $id = $user_data->ID;
            $email = $user_data->user_email;
            $display_name = $user_data->display_name;
            $registration_date = $user_data->user_registered;
            $visite = get_user_meta($id, 'visite', true);
            $last_order_date = get_user_meta($id, '_last_order_date', true);
            $first_order_date = get_user_meta($id, '_first_order_date', true);
            $visite = get_user_meta($id, 'visite', true);
            $role = !empty($user_data->roles) ? implode(',', $user_data->roles) : '';

            // Écrire la ligne de données pour chaque utilisateur
            fputcsv($output, [$id, $email, $display_name, $registration_date, $last_order_date, $first_order_date, $visite, $role]);
        }

        fclose($output);
        exit;
    });
}
