<?php
/**
 * api_user_balance_app();
 * Get the user balance information from the ticket service (improved)
 */
function api_user_balance_app($email = NULL) {
    if(!isset($email) || !$email ) :
        global $user_email;
    else:
        $user_email = $email;
    endif;

    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://tickets.coworking-metz.fr/api/user-stats',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => 'key=' . API_KEY_TICKET . '&email=' . $user_email,
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/x-www-form-urlencoded'
        ),
    ));

    $result = json_decode(curl_exec($curl));

    curl_close($curl);

    if ( is_user_logged_in() ) {
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

    <?php } 
        else { 
            echo '<p class="connexion"><em>Veuillez vous connecter !</em></p>';
            }
}

