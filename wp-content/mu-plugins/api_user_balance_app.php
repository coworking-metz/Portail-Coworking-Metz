<?php
/**
 * api_user_balance_app();
 * Get the user balance information from the ticket service (improved)
 */
function api_user_balance_app() {
	$user_id = get_current_user_id();
	if(!$user_id) return;

    $json = file_get_contents(TICKET_BASE_URL.'/members/'.$user_id.'?key='.API_KEY_TICKET); 

	$result = json_decode($json);

?>
        <div class="tickets-status">
            <p>
                <?php
                    if($result->balance > 0) {
                        echo 'Il vous reste <em>' . $result->balance . '</em> ';
                            if ($result->balance > 0 && $result->balance < 2) {
                                echo ' ticket.';
                            } else {
                                echo ' tickets.';
                            }
                    } elseif ($result->balance == 0){
                        echo 'Vous n\'avez pas de ticket' ;
                    } else {
                        echo 'Votre balance de tickets est négative : <em>' . $result->balance . '</em>';
                    }
                ?>
            </p>
            <p>
                <?php
                    if( isset($result->lastAboEnd)) {
                        $dateAbo = strtotime($result->lastAboEnd);
                        echo 'Votre abonnement se termine le <em>' . date_i18n('l d F Y', $dateAbo) . '</em> au soir.';
                    } else {
                        echo 'Vous n\'avez pas d\'abonnement en cours.';
                    }
                ?>
            </p>
            <p>
                <?php echo 'Vous êtes venu <em>' . $result->presencesJours .'</em> fois au total.'; ?>
            </p>
        </div>

    <?php 
}

