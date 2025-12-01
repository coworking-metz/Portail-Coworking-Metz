<?php

/***
 * Creation de l'export CSV des users
 */
if (isset($_GET['export-users'])) {
    add_action('admin_init', function () {
        $visiteSansCommande = isset($_GET['visite-sans-commande']);
        $recents = isset($_GET['recents']);
        $voting = isset($_GET['voting']);
        $adhesions = isset($_GET['adhesions']);
		$year = $_GET['year']?:date('Y');
        $args = ['fields' => ['ID']];
        $usersactifs = [];
        if ($adhesions) {
			$users = get_users_with_adhesion_orders($year);
		} else
        if ($visiteSansCommande) {
            $name = "visite-sans-commande";
            $users = get_users_with_visit_no_orders();
        } else if ($recents) {

            $name = 'recents';
			$mois = $_GET['mois']??6;

            $date_six_months_ago = date('Y-m-d', strtotime('-'.$mois.' months'));
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

            $json = file_get_contents(TICKET_BASE_URL . '/current-members?key=' . API_KEY_TICKET . '&delay=263002'); // actifs dans les 6 derniers moois

            $usersactifs = json_decode($json, true);
            $emails = array_column($usersactifs, 'email');
            $autres_users = get_users_by_email_list($emails, $ids, ['fields' => ['ID']]);
            $users = array_merge($users, $autres_users);
        } else if ($voting) {
            $name = 'voting';
            $minActivity = 20;
            $date = $_GET['date'] ?? 'aujourd\'hui';

            if ($date != 'aujourd\'hui') {
                $minActivity = 10;
            }
            $json = file_get_contents(TICKET_BASE_URL . '/voting-members?minActivity=' . $minActivity . '&key=' . API_KEY_TICKET);
            $usersactifs = json_decode($json, true);

            if ($date != 'aujourd\'hui') {
                $usersactifs = calculerPresencesTheoriques($usersactifs, $date, 20);
            }
            $emails = array_column($usersactifs, 'email');
            $users = get_users_by_email_list($emails, [], ['fields' => ['ID']]);
        } else {
            $name = 'all';
            $users = get_users($args);
        }

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=coworking-users-' . $name . '-' . wp_date('Y-m-d-H-i-s') . '.csv');

        $output = fopen('php://output', 'w');

        $ligne = ['ID', 'Email', 'Display Name', 'Registration Date', '_last_order_date', '_first_order_date', 'Date de la visite', 'Role'];

        if ($usersactifs) {
            $useractif = $usersactifs[0];
            unset($useractif['firstName']);
            unset($useractif['lastName']);
            unset($useractif['email']);

            $ligne = array_merge($ligne, array_keys($useractif));
        }
        // Écrire les en-têtes de colonnes
        fputcsv($output, $ligne);



        foreach ($users as $user) {
            $user_data = get_userdata($user->ID);

            $id = $user_data->ID;
            $email = $user_data->user_email;
            $display_name = $user_data->display_name;
            $registration_date = $user_data->user_registered;
            $last_order_date = get_user_meta($id, '_last_order_date', true);
            $first_order_date = get_user_meta($id, '_first_order_date', true);
            $visite = date('Y-m-d H:i',strtotime(get_user_meta($id, 'visite', true)));
            // $visite = get_user_meta($id, 'visite', true);
            $role = !empty($user_data->roles) ? implode(',', $user_data->roles) : '';
            $ligne = [$id, $email, $display_name, $registration_date, $last_order_date, $first_order_date, $visite, $role];
            foreach ($usersactifs as $useractif) {
                if ($useractif['email'] == $email) {
                    unset($useractif['firstName']);
                    unset($useractif['lastName']);
                    unset($useractif['email']);
                    $ligne = array_merge($ligne, $useractif);
                }
            }
            // Écrire la ligne de données pour chaque utilisateur
            fputcsv($output, $ligne);
        }

        fclose($output);
        exit;
    });
}


function get_users_with_adhesion_orders($year) {
    $products = get_posts([
        'post_type'   => 'product',
        'numberposts' => -1,
        'fields'      => 'ids',
        'meta_query'  => [
            [
                'key'   => 'productType',
                'value' => 'adhesion'
            ]
        ]
    ]);

    if (!$products) return [];

    $start = $year . '-01-01 00:00:00';
    $end   = $year . '-12-31 23:59:59';

    $orders = wc_get_orders([
        'status'        => ['wc-completed', 'wc-processing'],
        'limit'         => -1,
        'date_created'  => $start . '...' . $end,
        'return'        => 'ids'
    ]);

    if (!$orders) return [];

    $users = [];

    foreach ($orders as $order_id) {
        $order = wc_get_order($order_id);
        if (!$order) continue;

        foreach ($order->get_items() as $item) {
            if (!in_array($item->get_product_id(), $products)) continue;
            $users[] = $order->get_user_id();
            break;
        }
    }

    $ids = array_values(array_unique(array_filter($users)));

	return get_users([
        'include' => $ids,
        'fields'  => 'all'
    ]);
}


/**
 * Calcule le nombre de jours de présence théorique pour chaque personne à une date future.
 * 
 * @param array $personnes Tableau associatif des personnes et de leurs jours de présence.
 * @param string $dateFuture Date future au format DD/MM/YYYY.
 * @param int $keepOnlyPresence Garder les personnes qui auront au moins $keepOnlyPresence dans activity.
 * @return array Tableau mis à jour avec le nombre de jours de présence théorique.
 */
function calculerPresencesTheoriques(array $personnes, string $dateFuture, int $keepOnlyPresence = 0): array
{
    // Conversion de la date future en objet DateTime
    $dateActuelle = new DateTime();
	if($dateFuture == 'today') {
		$dateFuture = new DateTime();
	} else  {
		$dateFuture = DateTime::createFromFormat('d/m/Y', $dateFuture);
	}
    // Calcul du nombre de jours jusqu'à la date future
    $interval = $dateActuelle->diff($dateFuture);
    $joursJusquaDateFuture = $interval->days;

    // Nombre de jours dans les 6 derniers mois (approximativement)
    $joursDans6Mois = 6 * 30;

    // Mise à jour des présences théoriques pour chaque personne
    foreach ($personnes as $index => $data) {
        $originalActivity = $data['activity'];
        // Taux de présence quotidienne basé sur les 6 derniers mois
        $tauxQuotidien = $data['activity'] / $joursDans6Mois;

        // Calcul de la présence théorique à la date future
        $presencesTheoriques = ceil($data['activity'] + ($tauxQuotidien * $joursJusquaDateFuture));

        // Mise à jour du tableau
        $personnes[$index]['activity'] = $presencesTheoriques;

        if ($personnes[$index]['activity'] < $keepOnlyPresence) {
            unset($personnes[$index]);
        } else {

            $personnes[$index]['activityRating'] = round($tauxQuotidien, 3);
            $personnes[$index]['originalActivity'] = $originalActivity;
            $personnes[$index]['activityDiff'] = $presencesTheoriques - $originalActivity;
        }
    }

    return $personnes;
}
