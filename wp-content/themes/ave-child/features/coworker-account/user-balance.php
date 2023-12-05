<?php
/**
 * api_user_balance();
 * Get the user balance information from the ticket service (improved)
 */
 function api_user_balance($email = NULL) {
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

?>
        <div class="tickets-status">
            <p>
                <em>Petit rappel : </em> Lorsque vous disposez d'un solde positif de tickets, il vous est tout de même possible de 
                basculer sur un abonnement.<br>Vos tickets ne seront alors plus décomptés, c'est l'abonnement qui prendra le relai pendant toute 
                la période de l'abonnnement !
            </p>
        </div>
        <div class="tickets-status">
            <p>
                <?php
                    if($result->balance > 0) {
                        echo 'Il vous reste <em>' . $result->balance . '</em> ';
                            if ($result->balance > 0 && $result->balance < 2) {
                                echo ' ticket<sup>*</sup> à consommer.';
                            } else {
                                echo ' tickets<sup>*</sup> à consommer.';
                            }
                    } elseif ($result->balance == 0){
                        echo 'La balance de vos tickets<sup>*</sup> est de <em>' . $result->balance . '</em> .' ;
                    } else {
                        echo 'Votre balance de tickets est négative : <em>' . $result->balance . '</em><br>Pour rappel, 
                        l\'accès à l\'espace de coworking est conditionné par un solde positif de tickets.<br>
                        <strong>Merci de bien vouloir régulariser</strong> à  l\'aide d\'un carnet de 10 journées ou de l\'achat de tickets à l\'unité
                        <a href="https://www.coworking-metz.fr/boutique/ticket-1-journee/"><span class="dispo">disponibles ici</span></a>.';
                    }
                ?>
            </p>
            <p>
                <?php
                    if( isset($result->lastAboEnd)) {
                        $dateAbo = strtotime($result->lastAboEnd);
                        echo 'Vous disposez d’un abonnement valable jusqu’au <em>' . date_i18n('l d F Y', $dateAbo) . '</em> inclus.';
                    } else {
                        echo 'Vous n\'avez pas d\'abonnement en cours. Vous pouvez vous en procurer un 
                        <a href="https://www.coworking-metz.fr/boutique/pass-resident/">
                        <span class="dispo">directement ici</span></a>.';
                    }
                ?>
            </p>
            <p>
                <?php

                $currentYear = date('Y');
                $nextYear = date('Y', strtotime('+1 year'));
                    if($result->lastMembership == $currentYear || $result->lastMembership == $nextYear){
                        echo 'Vous disposez d\'une carte d’adhérent à jour pour l\'année<em>' . $result->lastMembership . '</em> .';
                    } else {
                        echo '<span class="alerte"><strong>Vous n\'êtes pas à jour concernant l\'adhésion ' . $currentYear . '.</strong></span><br/><br/><u>La carte adhérent est obligatoire pour venir coworker, 
                        il s\'agit d\'une prérogative de notre assureur. Sans cette adhésion, il ne vous est pas possible de coworker.</u>';
                    }
                
                ?>
            </p>
            <p>
                Vous avez coworké <em><?php echo $result->activity; ?></em> journées au cours des 6 derniers mois.
                    <?php 
                        if ($result->activeUser == true && ($result->lastMembership == $currentYear || $result->lastMembership == $currentYear+1)){
                            echo '<br/><br/>Vous êtes <em>membre actif <sup>**</sup></em> .';
                        } else {
                            echo '<br/><br><em>Vous n\'êtes pas membre actif <sup>**</sup></em> .';
                        }
                    ?>
            </p>
            <p>
                <?php echo 'Vous avez coworké au total <em>' . $result->presencesConso . '</em> journées pour un total de <em>' . $result->presencesJours .'</em> jours de présence (jours uniques).<br><br>'; ?>
                <?php if ($result->trustedUser == false) {
                echo '<strong>Dès votre 11<sup>ème</sup> journée de coworking, vous pourrez :</strong>
                        <ul>
                            <li>accéder à l\'espace aux horaires habituelles (de 7h à 23h) 💪</li>
                            <li>arriver le premier 💪</li>
                            <li>partir le dernier 💪</li>
                            <li>venir coworker les week-end et jours fériés 💪</li>
                        <li>accueillir des personnes extérieures pour une réunion de travail 💪</li>
                </ul>';
                } ?>
            </p>   
        </div>
        <div>
            <p>
                <span class="notabene">
                    <sup>*</sup>Ce solde n'inclut pas votre éventuelle présence de ce jour. Il est recalculé tous les soirs entre 23h00 et 00h00.
                    Le décompte des tickets se fait de la manière suivante :<br>
                        - entre 0 et 5h de présence sur une journée : 1/2 ticket ;<br>
                        - plus de 5h de présence sur une journée : 1 ticket.<br><br>
                    <sup>**</sup>Membre actif : personne qui dispose d'une cotisation annuelle à jour et qui a coworké au moins 20 journées 
                    au cours des 6 derniers mois. Permet de voter lors de l'assemblée générale de l'Association.</span>
            </p>
        </div>
    <?php
 
    }