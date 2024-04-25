<?php
function api_purchase_start_stop_abo()
{
    if (is_user_logged_in()) {

        $current_user = wp_get_current_user();
        $user_id = $current_user->ID;
        $json = file_get_contents(TICKET_BASE_URL . '/members/' . $user_id . '/subscriptions?key=' . API_KEY_TICKET);
        $abos = json_decode($json);


        $html = '<div class="my-account-subscription-list"><table class="table table-left">';
        $html .= '<caption></caption>';
        $html .= '<tr><th>Date d\'achat</th><th>Date de début</th><th>Date de fin</th><th>Commande</th></tr>';

        $orders = get_orders_by_custom_order_numbers(array_column($abos, 'orderReference'), true);
        foreach ($abos as $abo) {
            $abo_purchase = strtotime($abo->purchased);
            $abo_start = strtotime($abo->started);
            $abo_end = strtotime($abo->ended);
            $order = $orders[$abo->orderReference] ?? false;
            $abo_current = '';
            // $abo_current = ($abo->current == true) ? '<br><span class="current-abo">Abonnement en cours...</span>' : ''; TODO
            $html .= '<tr>';
            $html .= '<td class="purchase-abo"><span>' . date_i18n('d M Y', $abo_purchase) . '</span></td>';
            $html .= '<td class="purchase-start"><span>' . date_i18n('D d M Y', $abo_start) . $abo_current . '</span></td>';
            $html .= '<td class="purchase-end">' . date_i18n('D d M Y', $abo_end) . $abo_current . '</td>';
            if ($order) {
                $html .= '<td class="order-reference"><a href="' . $order->get_view_order_url() . '">' . $abo->orderReference . '</a></td>';
            } else {
                $html .= '<td class="order-reference">' . $abo->orderReference . '</td>';
            }
            $html .= '</tr>';
        }

        $html .= '</table></div>';

        echo $html;
    }
}
