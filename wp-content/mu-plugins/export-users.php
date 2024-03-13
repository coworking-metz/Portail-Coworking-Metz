<?php

/***
 * Creation de l'export CSV des users
 */
if (isset($_GET['export-users'])) {
    add_action('admin_init', function () {
        $recents = isset($_GET['recents']);
        $voting = isset($_GET['voting']);

        $args = ['fields' => ['ID']];

        if ($recents) {

            $name='recents';

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
            $users = get_users($args);
            $ids = array_column($users, 'ID');

            $json = file_get_contents(TICKET_BASE_URL.'/current-users?key=bupNanriCit1&delay=263002'); // actifs dans les 6 derniers moois
            $usersactifs = json_decode($json, true);
            $emails = array_column($usersactifs, 'email');
            $autres_users = get_users_by_email_list($emails, $ids, ['fields' => ['ID']]);
            $users = array_merge($users, $autres_users);
        } else if($voting){
            $name='voting';
            $json = file_get_contents(TICKET_BASE_URL.'/voting-members?key=bupNanriCit1');
            $usersactifs = json_decode($json, true);
            $emails = array_column($usersactifs, 'email');
            $users = get_users_by_email_list($emails, [], ['fields' => ['ID']]);
        }else{
            $name='all';
            $users = get_users($args);
        }

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=users-'.$name.'-' . wp_date('Y-m-d-H-i-s') . '.csv');

        $output = fopen('php://output', 'w');

        // Écrire les en-têtes de colonnes
        fputcsv($output, ['ID', 'Email', 'Display Name', 'Registration Date', '_last_order_date', '_first_order_date', 'Date de la visite', 'Role']);



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
