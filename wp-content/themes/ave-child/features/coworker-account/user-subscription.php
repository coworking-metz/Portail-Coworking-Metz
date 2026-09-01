<?php
function api_purchase_start_stop_abo()
{
    if (is_user_logged_in()) {

        $current_user = wp_get_current_user();
        $user_id = $current_user->ID;
        $abos = tickets('/members/' . $user_id . '/subscriptions');

        if (!is_array($abos)) {
            // L'API tickets n'a rien renvoyé d'exploitable : on affiche un message
            // plutôt que de laisser array_column() casser toute la page.
            if (tickets_last_status() === 404) {
                // Membre encore inconnu du back-office billetterie : aucun abonnement.
                echo '<div class="my-account-subscription-list"><p>Vous n\'avez pas encore d\'abonnement.</p></div>';
            } else {
                echo '<div class="my-account-subscription-list"><p>Vos abonnements sont momentanément indisponibles. Merci de réessayer dans quelques instants ou de nous contacter si le problème persiste.</p></div>';
            }
            return;
        }

        if (empty($abos)) {
            echo '<div class="my-account-subscription-list"><p>Vous n\'avez pas encore d\'abonnement.</p></div>';
            return;
        }

        $html = '<div class="my-account-subscription-list"><table class="table table-left">';
        $html .= '<caption></caption>';
        $html .= '<tr><th>Date d\'achat</th><th>Date de début</th><th>Date de fin</th><th>Commande</th></tr>';

        $orders = get_orders_by_custom_order_numbers(array_column($abos, 'orderReference'), true);
        if (!is_array($orders)) {
            $orders = [];
        }
        foreach ($abos as $abo) {
            $abo_purchase = strtotime($abo['purchased']);
            $abo_start = strtotime($abo['started']);
            $abo_end = strtotime($abo['ended']);
            $order = $orders[$abo['orderReference'] ?? ''] ?? false;
            $abo_current = '';
            // $abo_current = ($abo['current'] == true) ? '<br><span class="current-abo">Abonnement en cours...</span>' : ''; TODO
            $html .= '<tr>';
            $html .= '<td class="purchase-abo"><span>' . date_i18n('d M Y', $abo_purchase) . '</span></td>';
            $html .= '<td class="purchase-start"><span>' . date_i18n('D d M Y', $abo_start) . $abo_current . '</span></td>';
            $html .= '<td class="purchase-end">' . date_i18n('D d M Y', $abo_end) . $abo_current . '</td>';
            if ($order) {
                $html .= '<td class="order-reference"><a href="' . $order->get_view_order_url() . '">' . $abo['orderReference'] . '</a></td>';
            } else {
                $html .= '<td class="order-reference">' . $abo['orderReference'] . '</td>';
            }
            $html .= '</tr>';
        }

        $html .= '</table></div>';

        echo $html;
    }
}
