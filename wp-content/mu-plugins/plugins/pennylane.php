<?php

// Register 5-min schedule
add_filter('cron_schedules', function ($schedules) {
    $schedules['every_five_minutes'] = [
        'interval' => 300,
        'display'  => __('Every 5 Minutes')
    ];
    return $schedules;
});

// Schedule event if not yet scheduled
add_action('wp', function () {
    if (!wp_next_scheduled('pennylane_check_transactions')) {
        wp_schedule_event(time(), 'every_five_minutes', 'pennylane_check_transactions');
    }
});

// Main cron callback
add_action('pennylane_check_transactions', 'pennylane_check_transactions_callback');
function pennylane_check_transactions_callback() {
    $is_cli = (defined('WP_CLI') && WP_CLI);

    $log = function($msg) use ($is_cli) {
        if ($is_cli) {
            \WP_CLI::log($msg);
        } else {
            error_log('[Pennylane Cron] ' . $msg);
        }
    };

    $log('--- Début de l’exécution ---');

    if (!function_exists('wc_get_orders')) {
        $log('WooCommerce non détecté. Fin du script.');
        return;
    }

    $three_months_ago = (new DateTime('-3 months'))->format('Y-m-d H:i:s');
    $log("Recherche des commandes 'pending' depuis le $three_months_ago...");

    $orders = wc_get_orders([
        'status'       => 'wc-pending',
        'date_created' => '>' . $three_months_ago,
        'limit'        => -1,
        'return'       => 'objects'
    ]);

    $log(count($orders) . ' commandes trouvées.');

    foreach ($orders as $order) {
        $order_id = $order->get_id();
        $custom_order_number = $order->get_meta('_alg_wc_custom_order_number');

        if (empty($custom_order_number)) {
            $log("Commande #$order_id : aucun numéro personnalisé trouvé, ignorée.");
            continue;
        }

        $log("Commande #$order_id : vérification du numéro $custom_order_number...");

        $api_url = "https://tools.coworking-metz.fr/pennylane/?filter=" . urlencode($custom_order_number);
        $log("Appel API : $api_url");

        $response = wp_remote_get($api_url, ['timeout' => 15]);
        if (is_wp_error($response)) {
            $log("Erreur API : " . $response->get_error_message());
            continue;
        }

        $data = json_decode(wp_remote_retrieve_body($response), true);

        if (is_array($data) && count($data) === 1) {
            $transaction        = $data[0];
            $transaction_amount = floatval($transaction['amount']);
            $order_amount       = floatval($order->get_total());

            $log("Montant transaction : $transaction_amount / Montant commande : $order_amount");

            if (abs($transaction_amount - $order_amount) < 0.01) {
                $order->update_status('completed', 'Order auto-completed via Pennylane API');
                $log("Commande #$order_id complétée automatiquement.");

                $order_link = admin_url('post.php?post=' . $order_id . '&action=edit');

                $email_body = "La commande #$order_id ($custom_order_number) a été marquée comme payée automatiquement.\n\n";
                $email_body .= "Détails de la commande :\n";
                $email_body .= "Nom : " . $order->get_formatted_billing_full_name() . "\n";
                $email_body .= "Email : " . $order->get_billing_email() . "\n";
                $email_body .= "Total : " . wc_price($order_amount) . "\n";
                $email_body .= "Méthode de paiement : " . $order->get_payment_method_title() . "\n\n";

                $email_body .= "Détails de la transaction :\n";
                foreach ($transaction as $key => $value) {
                    $email_body .= ucfirst($key) . " : " . print_r($value, true) . "\n";
                }

                $email_body .= "\nLien vers la commande dans le tableau de bord :\n$order_link\n";

                wp_mail(
                    'contact@coworking-metz.fr',
                    'Commande auto-validée par virement',
                    $email_body
                );
                $log("Email envoyé à contact@coworking-metz.fr.");
            } else {
                $log("Montants différents, commande non modifiée.");
            }
        } else {
            $log("Aucune transaction unique trouvée pour cette commande.");
        }
    }

    $log('--- Fin de l’exécution ---');
}
