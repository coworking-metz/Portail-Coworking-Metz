<?php

// Register 5-min schedule
add_filter('cron_schedules', function ($schedules) {
    $schedules['every_five_minutes'] = [
        'interval' => 300,
        'display'  => __('Every 5 Minutes')
    ];
    return $schedules;
});

// Ajouter un intervalle personnalisé d'une minute au cron WP
add_filter('cron_schedules', function($schedules) {
    $schedules['every_minute'] = [
        'interval' => 60,
        'display'  => __('Every Minute')
    ];
    return $schedules;
});

// Schedule event if not yet scheduled
add_action('wp', function () {
    if (!wp_next_scheduled('pennylane_check_transactions')) {
        wp_schedule_event(time(), 'every_minute', 'pennylane_check_transactions');
    }
});

/**
 * Retourne un tableau de chemins de PDF de facture pour un ID de commande.
 *
 * Essaie d'abord le plugin "Invoices for WooCommerce" (BEWPI), puis
 * "WooCommerce PDF Invoices & Packing Slips" (WP Overnight) en secours.
 *
 * @param int $order_id ID de la commande WooCommerce.
 * @return array Liste des chemins absolus des PDF trouvés.
 */
function get_invoice_pdf_attachments($order_id) {
    $attachments = [];

    // Invoices for WooCommerce (BEWPI)
    if (class_exists('BEWPI_Invoice')) {
        try {
            $invoice = new BEWPI_Invoice($order_id);
            $path = '';
            if (method_exists($invoice, 'get_full_path')) {
                $path = $invoice->get_full_path();
            }
            if (empty($path) && method_exists($invoice, 'create')) {
                // Génère la facture si nécessaire
                $invoice->create();
                if (method_exists($invoice, 'get_full_path')) {
                    $path = $invoice->get_full_path();
                }
            }
            if (!empty($path) && file_exists($path)) {
                $attachments[] = $path;
            }
        } catch (\Throwable $e) {
            // silencieux : on tentera le fallback
        }
    }

    // Fallback : WooCommerce PDF Invoices & Packing Slips (WP Overnight)
    if (empty($attachments) && function_exists('wcpdf_get_document')) {
        try {
            $order = wc_get_order($order_id);
            if ($order) {
                $document = wcpdf_get_document('invoice', $order);
                if ($document) {
                    $pdf_path = '';
                    if (method_exists($document, 'get_pdf_path')) {
                        $pdf_path = $document->get_pdf_path();
                    }
                    if (empty($pdf_path) && method_exists($document, 'generate_pdf')) {
                        // Génère le PDF temporaire si nécessaire
                        $document->generate_pdf();
                        if (method_exists($document, 'get_pdf_path')) {
                            $pdf_path = $document->get_pdf_path();
                        }
                    }
                    if (!empty($pdf_path) && file_exists($pdf_path)) {
                        $attachments[] = $pdf_path;
                    }
                }
            }
        } catch (\Throwable $e) {
            // silencieux
        }
    }

    return $attachments;
}

// Main cron callback
add_action('pennylane_check_transactions', 'pennylane_check_transactions_callback');
function pennylane_check_transactions_callback() {
	$to = 'coworkingmetz+wordpress-pennylane@gmail.com';
	$cc = 'pennylane@coworking-metz.fr';
    $is_cli = (defined('WP_CLI') && WP_CLI);

//	$order = wc_get_order(37026);
//	$order->update_status('wc-pending', 'Order testing');

    echo('--- Début de l’exécution ---'.PHP_EOL);

    if (!function_exists('wc_get_orders')) {
        echo('WooCommerce non détecté. Fin du script.'.PHP_EOL);
        return;
    }

    $three_months_ago = (new DateTime('-3 months'))->format('Y-m-d H:i:s');
    echo("Recherche des commandes 'pending' depuis le $three_months_ago...".PHP_EOL);

	$orders = wc_get_orders([
		'status'       => ['wc-pending', 'wc-on-hold'],
		'date_created' => '>' . $three_months_ago,
		'limit'        => -1,
		'return'       => 'objects'
	]);


    echo(count($orders) . ' commandes trouvées.'.PHP_EOL);

    foreach ($orders as $order) {
        $order_id = $order->get_id();
		
        $custom_order_number = $order->get_meta('_alg_wc_custom_order_number');

        if (empty($custom_order_number)) {
            echo("Commande #$order_id : aucun numéro personnalisé trouvé, ignorée.".PHP_EOL);
            continue;
        }

        echo("Commande #$order_id : vérification du numéro $custom_order_number...".PHP_EOL);

        $api_url = "https://tools.coworking-metz.fr/pennylane/?filter=" . urlencode($custom_order_number);
        echo("Appel API : $api_url".PHP_EOL);

        $response = wp_remote_get($api_url, ['timeout' => 15]);
        if (is_wp_error($response)) {
            echo("Erreur API : " . $response->get_error_message().PHP_EOL);
            continue;
        }

        $data = json_decode(wp_remote_retrieve_body($response), true);

        if (is_array($data) && count($data) === 1) {
            $transaction        = $data[0];
            $transaction_amount = floatval($transaction['amount']);
            $order_amount       = floatval($order->get_total());

            echo("Montant transaction : $transaction_amount / Montant commande : $order_amount".PHP_EOL);

            if (abs($transaction_amount - $order_amount) < 0.01) {
				echo ("Transaction trouvée : ".pennylane_link($transaction).PHP_EOL);
                $order->update_status('completed', 'Commande validée suite à réception <a href="'.pennylane_link($transaction).'" target="_blank">d\'un virement bancaire</a>');
                echo("Commande #$order_id complétée automatiquement.".PHP_EOL);

                $order_link = admin_url('post.php?post=' . $order_id . '&action=edit');

                $email_body = "La commande #$order_id ($custom_order_number) a été marquée comme payée automatiquement.\n\n";
                $email_body .= "Détails de la commande :\n";
                $email_body .= "Nom : " . $order->get_formatted_billing_full_name() . "\n";
                $email_body .= "Email : " . $order->get_billing_email() . "\n";
                $email_body .= "Total : " . wc_price($order_amount) . "\n";
                $email_body .= "Méthode de paiement : " . $order->get_payment_method_title() . "\n\n";
                $email_body .= "\n<a href='$order_link'>Voir la commande dans le tableau de bord</a>\n";
                $email_body .= "\nDétails de la transaction :\n";
                $email_body .= "\n<a href='".pennylane_link($transaction)."'>Voir la transaction dans Pennylane</a>\n";
                foreach ($transaction as $key => $value) {
                    $email_body .= ucfirst($key) . " : " . print_r($value, true) . "\n";
                }



                // 🧾 Ajout des pièces jointes (PDF facture)
                $attachments = get_invoice_pdf_attachments($order_id);
                if (!empty($attachments)) {
                    echo('Pièces jointes trouvées : ' . implode(', ', $attachments).PHP_EOL);
                } else {
                    echo("Aucune facture PDF trouvée/attachée pour la commande #$order_id.".PHP_EOL);
                }


				wp_mail(
					$to,
					'Commande auto-validée par virement',
					$email_body,
					[ 'Cc: ' . $cc ], 
					$attachments
				);

                echo("Email envoyé à contact@coworking-metz.fr.".PHP_EOL);
            } else {
                echo("Montants différents, commande non modifiée.".PHP_EOL);
            }
        } else {
            echo("Aucune transaction unique trouvée pour cette commande.".PHP_EOL);
        }
    }

    echo('--- Fin de l’exécution ---'.PHP_EOL);
}


function pennylane_link($transaction) {

	$base = 'https://app.pennylane.com/companies/22078886/clients/transactions';

	$filters = [
		[
			'field'    => 'date',
			'operator' => 'gteq',
			'value'    => $transaction['date']
		],
		[
			'field'    => 'date',
			'operator' => 'lteq',
			'value'    => $transaction['date']
		]
	];

	return $base.'?'.http_build_query(['filters'=>json_encode($filters), 'transaction_id'=>$transaction['id']]);
}
