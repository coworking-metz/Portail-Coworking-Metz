<?php

function isNomade($user = false)
{
    $user_id = get_post_id($user);
    if (!$user_id) $user_id = get_current_user_id();
    if (!$user_id) return false;

    // Load stored map
    $status_map = get_transient('nomade_user_status');
    if (!is_array($status_map)) $status_map = [];

    // Already computed → return cached
    if (isset($status_map[$user_id])) {
        return $status_map[$user_id];
    }

    // --- Compute user status ---
    $statuses = ['wc-completed'];
    $orders = wc_get_orders([
        'customer_id' => $user_id,
        'status'      => $statuses,
        'limit'       => -1,
        'return'      => 'ids',
    ]);

    if (empty($orders)) {
        $status_map[$user_id] = false;
        set_transient('nomade_user_status', $status_map, DAY_IN_SECONDS);
        return false;
    }

    foreach ($orders as $order_id) {
        $order = wc_get_order($order_id);

        foreach ($order->get_items() as $item) {
            $product_id = $item->get_product_id();
            $nomade = get_post_meta($product_id, 'journee_nomade', true);

            if (empty($nomade)) {
                $status_map[$user_id] = false;
                set_transient('nomade_user_status', $status_map, DAY_IN_SECONDS);
                return false;
            }
        }
    }

    // If we reach here → all items nomade
    $status_map[$user_id] = true;
    set_transient('nomade_user_status', $status_map, DAY_IN_SECONDS);
    return true;
}




function fetch_nomades_for_today()
{
    return fetch_nomades_for_date(date('Y-m-d'));
}

/**
 * Obtenir et stocker les utilisateurs avec des visites aujourd'hui
 *
 * @return array Retourne une liste des utilisateurs avec des visites ce jour
 */
function fetch_nomades_for_date($date)
{
    $orders = get_orders_with_nomade_products_for_date($date);
    $user_ids = [];

    foreach ($orders as $order) {
        $uid = $order->get_user_id();
        if (!$uid) continue;
        $user_ids[$uid] = true; // unique by array keys
    }

    $users = [];
    foreach (array_keys($user_ids) as $uid) {
        $user = get_userdata($uid);
        if ($user) $users[] = $user;
    }

    return $users;
}


function get_orders_with_nomade_products_for_date($date)
{
    $orders = get_orders_with_nomade_products(); // all nomade orders
    $result = [];

    foreach ($orders as $order) {

        foreach ($order->get_items() as $item) {
            $tmcp_data = $item->get_meta('_tmdata', true);
            if (empty($tmcp_data)) continue;

            foreach ($tmcp_data as $data) {
                $fields = $data['tmcp_post_fields'] ?? [];
                if (!isset($fields['tmcp_date_0'])) continue;

                $parsed = DateTime::createFromFormat('d/m/Y', $fields['tmcp_date_0']);
                if (!$parsed) continue;

                if ($parsed->format('Y-m-d') === $date) {
                    $result[] = $order;
                    continue 3; // skip to next order
                }
            }
        }
    }

    return $result;
}


function get_dates_nomades_user($user_id)
{
    $orders = get_orders_with_nomade_products($user_id);
    $dates = [];
    foreach ($orders as $order) {
        foreach ($order->get_items() as $item) {
            $tmcp_data = $item->get_meta('_tmdata', true);
            foreach ($tmcp_data as $data) {
                if (isset($data['tmcp_post_fields']['tmcp_date_0'])) {
                    $dates[] = DateTime::createFromFormat('d/m/Y', $data['tmcp_post_fields']['tmcp_date_0'])->format('Y-m-d');
                    break;
                }
            }
        }
    }

    return $dates;
}

function get_orders_with_nomade_products( $user_id = false, $statuses = ['wc-completed'] )
{
    $cache_key = $user_id
        ? false
        : 'nomade_orders_all';

	if($cache_key) {
		// Read cache
		$cached = get_transient($cache_key);
		if ($cached !== false) {
			return $cached;
		}
	}

	$per_page = 200;
    $offset   = 0;
    $result   = [];

    $args = [
        'status' => $statuses,
        'limit'  => $per_page,
        'return' => 'ids',
    ];

    if ($user_id) {
        $args['customer_id'] = $user_id;
    }

    while (true) {
        $args['offset'] = $offset;
        $order_ids = wc_get_orders($args);

        if (empty($order_ids)) break;

        foreach ($order_ids as $order_id) {
            $order = wc_get_order($order_id);

            foreach ($order->get_items() as $item) {
                $product_id = $item->get_product_id();
                $nomade = get_post_meta($product_id, 'journee_nomade', true);

                if (!empty($nomade)) {
                    $result[] = $order;
                    break;
                }
            }
        }

        $offset += $per_page;
    }

	if($cache_key) {
		// Cache for 1 day
		set_transient($cache_key, $result, DAY_IN_SECONDS);
	}
    return $result;
}

function envoyerMailAlerteNomade($user_id, $autres_codes = [])
{

    $data = get_userdata($user_id);
    if (!$data)
        return;
    $template_id = get_field('email_alerte_cowo_nomade', 'option');
    // $visite = get_user_meta($user_id, 'visite', true);

    // $key = 'email-alerte-nomade-' . $user_id;
    // if (get_user_meta($user_id, $key, true))
    //     return;
    // update_user_meta($user_id, $key, true);


    $codes = [
        ['{_user_id}' => $data->ID],
        ['{user_name}' => $data->display_name],
        ['{_user_email}' => $data->user_email],
        ['{activite}' => get_visiteur_activite($user_id)],
        ['{date_presence}' => date_francais($autres_codes['all_dates'][0])],
        ['{url_commandes_user}' => admin_url('edit.php?s&post_status=all&post_type=shop_order&_customer_user=' . $user_id)],
        ['{url_fiche_user}' => admin_url('user-edit.php?user_id=' . $user_id)],
        ['{_admin_url}' => admin_url()],

    ];

    foreach ($autres_codes as $k => $v) {
        $codes[] = ['{' . $k . '}' => $v];
    }
    $mail = charger_template_mail($template_id, $codes);
    // echo $mail['message'];exit;
    $to = get_field('destinataire_alerte', 'option');

    if (wp_get_environment_type() == 'local')
        $to = DEFAULT_TO_EMAIL;

    $headers = array('Content-Type: text/html; charset=UTF-8');
    return wp_mail($to, $mail['subject'], $mail['message'], $headers);
}


function envoyerMailNomade($user_id, $datesPresence = null, $autres_codes = [])
{

    $user = get_userdata($user_id);
    if (!$user)
        return;

    $datePresence = $datesPresence[0] ?? false;

    $all_dates_txt = '';
    if (count($datesPresence) > 1) {
        $all_dates = array_map(function ($date) {
            return date_francais($date);
        }, $datesPresence);
        $all_dates_txt = '(Détail des dates réservées: '.implode(', ', $all_dates).')';
    }

    // $key = 'email-nomade-' . $user_id;
    // if (get_user_meta($user_id, $key, true))
    //     return;
    // update_user_meta($user_id, $key, true);



    $template_id = get_field('email_confirmation_nomade', 'option');
    $codes = [
        ['{date_presence}' => date_francais($datePresence)],
        ['{dates_presences}' => $all_dates_txt],
        ['{buy_ticket_link}' => site_url('/boutique/ticket-journee-nomade/?al_id=' . $user_id . '&startDate=' . $datePresence)],
        ['{app_login_link}' => app_login_link($user_id)],
    ];

    foreach ($autres_codes as $k => $v) {
        $codes[] = ['{' . $k . '}' => $v];
    }
    $mail = charger_template_mail($template_id, $codes);
    $to = $user->user_email;
    if (wp_get_environment_type() == 'local')
        $to = DEFAULT_TO_EMAIL;

    $headers = array('Content-Type: text/html; charset=UTF-8');

    return wp_mail($to, $mail['subject'], $mail['message'], $headers);
}


function get_all_dates_nomade(array $bookings)
{
    $all_dates = [];

    foreach ($bookings as $booking) {
        $date = DateTime::createFromFormat('d/m/Y', $booking['date']);
        for ($i = 0; $i < $booking['quantity']; $i++) {
            $all_dates[] = $date->format('d/m/Y');
            $date->modify('+1 day');
        }
    }

    return $all_dates;
}

function delete_nomade_order_transients() {
	delete_transient('nomade_orders_all');
	delete_transient('nomade_user_status');
}