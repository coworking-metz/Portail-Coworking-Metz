<?php
function api_purchase_start_stop_abo() {
    if (is_user_logged_in()) {

        $current_user = wp_get_current_user();
		$user_id = $current_user->ID;
		$json = file_get_contents(TICKET_BASE_URL.'/members/'.$user_id.'?key='.API_KEY_TICKET); 
		$json = json_decode($json);

        
        $abo_array = $json->abos;
    
        $html = '<div class="my-account-subscription-list"><table class="table table-left">';
        $html .= '<caption></caption>';
        $html .= '<tr><th>Date d\'achat</th><th>Date de début</th><th>Date de fin</th></tr>';
    
        foreach ($abo_array as $value){
            $abo_purchase = strtotime($value->purchaseDate);
            $abo_start = strtotime($value->aboStart);
            $abo_end = strtotime($value->aboEnd);
            $abo_current = ($value->current == true) ? '<br><span class="current-abo">Abonnement en cours...</span>' : '';
            $html .= '<tr>';
            $html .= '<td class="purchase-abo"><span>' . date_i18n('d M Y', $abo_purchase) . '</span></td>';
            $html .= '<td class="purchase-start"><span>' . date_i18n('D d M Y', $abo_start) . $abo_current . '</span></td>';
            $html .= '<td class="purchase-end">' . date_i18n('D d M Y', $abo_end) . $abo_current . '</td>';
            $html .= '</tr>';
        }
        
        $html .= '</table></div>';
        
        echo $html;
    }
}