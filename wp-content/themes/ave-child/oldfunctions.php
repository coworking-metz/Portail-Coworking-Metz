<?php

add_action( 'wp_enqueue_scripts', 'liquid_child_theme_style', 99 );

function liquid_parent_theme_scripts() {
    wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css' );
}
function liquid_child_theme_style(){
    wp_enqueue_style( 'child-one-style', get_stylesheet_directory_uri() . '/style.css' );	
}

// function coworking_scripts() {
//     wp_enqueue_script( 'coworking-scripts', get_stylesheet_directory_uri() . '/js/coworking.js', '', '1.0', true );
// }
// add_action( 'wp_enqueue_scripts', 'coworking_scripts' );

// filters the woocommerce_account_menu_items 
function filter_woocommerce_account_menu_items( $items ) { 
    
    // Remove downloads and payment-methods from woocommerce menu item

    //Array ( [dashboard] => Tableau de bord [orders] => Commandes [downloads] => Téléchargements [edit-address] => Adresses [payment-methods] => Moyens de paiement [edit-account] => Détails du compte [customer-logout] => Déconnexion )
    foreach ($items as $index => $data) {
        if ($index == 'downloads' || $index == 'payment-methods') {
            unset($items[$index]);
        }
    }

    //print_r($items);
    //exit;
    return $items; 
}; 
         
// add the filter 
add_filter( 'woocommerce_account_menu_items', 'filter_woocommerce_account_menu_items', 10, 1 ); 


function golden_oak_web_design_woocommerce_checkout_terms_and_conditions() {
    remove_action( 'woocommerce_checkout_terms_and_conditions', 'wc_terms_and_conditions_page_content', 30 );
  }
  add_action( 'wp', 'golden_oak_web_design_woocommerce_checkout_terms_and_conditions' );

// redirect to my account page after reset password
function woocommerce_new_pass_redirect( $user ) {
    wp_redirect( get_permalink(woocommerce_get_page_id('myaccount')));
    exit;
}
  
add_action( 'woocommerce_customer_reset_password', 'woocommerce_new_pass_redirect' );

// keep users logged in for longer in wordpress
add_filter( 'auth_cookie_expiration', 'keep_me_logged_in_for_1_year' );

function keep_me_logged_in_for_1_year( $expirein ) {
    return YEAR_IN_SECONDS; // 1 year in seconds
}

/**
 * coworkers_now_tv();
 * Get the number of people connected
 */
function coworkers_now_tv() {
    //date_default_timezone_set('Europe/Paris');

    $hour_now = date('H');
    $hour_now += 2;
    $start_hour = 12;
    $end_hour = 14;

    if ($hour_now >= $start_hour && $hour_now < $end_hour) {
        $curl_url = 'https://tickets.coworking-metz.fr/coworkersNow?delay=120';
    } else {
        $curl_url = 'https://tickets.coworking-metz.fr/coworkersNow?delay=15';
    }

    $ch = curl_init($curl_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $number_coworkers = curl_exec($ch);
    $number_worplaces = 28;
    $remaining_workplaces = $number_worplaces - $number_coworkers;
    
    if ($number_coworkers == 0) {
        echo 'Pas de coworker !';
    }
    elseif ($number_coworkers == 1) {
        echo '<span class="highlight-text">' . $number_coworkers . ' </span>coworker actuellement !';
    }
    else {
        echo 'Nous sommes <span class="highlight-text">' . $number_coworkers . '</span> actuellement !';
    }
}
/**
 * coworkers_now();
 * Get the number of people connected
 */
function coworkers_now() {
    //date_default_timezone_set('Europe/Paris');

    $hour_now = date('H');
    $hour_now += 2;
    $start_hour = 12;
    $end_hour = 14;

    if ($hour_now >= $start_hour && $hour_now < $end_hour) {
        $curl_url = 'https://tickets.coworking-metz.fr/coworkersNow?delay=120';
    } else {
        $curl_url = 'https://tickets.coworking-metz.fr/coworkersNow?delay=15';
    }

    $ch = curl_init($curl_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $number_coworkers = curl_exec($ch);
    $number_worplaces = 28;
    $remaining_workplaces = $number_worplaces - $number_coworkers;
    
    if ($number_coworkers == 0) {
        echo 'Pas de coworker actuellement. <span class="highlight-text">';
    }
    elseif ($number_coworkers == 1) {
        echo 'Actuellement <span class="highlight-text">' . $number_coworkers . ' </span>coworker présent.<br/><span class="highlight-text">' . $remaining_workplaces . '</span> postes de travail encore disponibles.';
    }
    else {
        echo 'Actuellement <span class="highlight-text">' . $number_coworkers . ' </span>coworkers présents.<br/><span class="highlight-text">' . $remaining_workplaces . '</span> postes de travail encore disponibles.';
    }
}
/**
 * coworkers_now_app();
 * Get the number of people connected 
 */
function coworkers_now_app() {
    //date_default_timezone_set('Europe/Paris');

    $hour_now = date('H');
    $hour_now += 2;
    $start_hour = 12;
    $end_hour = 14;

    if ($hour_now >= $start_hour && $hour_now < $end_hour) {
        $curl_url = 'https://tickets.coworking-metz.fr/coworkersNow?delay=120';
    } else {
        $curl_url = 'https://tickets.coworking-metz.fr/coworkersNow?delay=15';
    }

    $ch = curl_init($curl_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    echo curl_exec($ch);
}
/**
 * coworkers_now_app();
 * Get the number of people connected 
 */
function remaining_workplaces_app() {
    //date_default_timezone_set('Europe/Paris');

    $hour_now = date('H');
    $hour_now += 2;
    $start_hour = 12;
    $end_hour = 14;

    if ($hour_now >= $start_hour && $hour_now < $end_hour) {
        $curl_url = 'https://tickets.coworking-metz.fr/coworkersNow?delay=120';
    } else {
        $curl_url = 'https://tickets.coworking-metz.fr/coworkersNow?delay=15';
    }

    $ch = curl_init($curl_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $number_coworkers = curl_exec($ch);
    $number_worplaces = 28;
    $remaining_workplaces = $number_worplaces - $number_coworkers;
    echo $remaining_workplaces;
}

/**
 * api_user_balance_app();
 * Get the user balance information from the ticket service (improved)
 */
function api_user_balance_app($email = NULL) {
    if(is_user_logged_in() ) {
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
  CURLOPT_POSTFIELDS => 'key=bupNanriCit1&email=' . $user_email,
  CURLOPT_HTTPHEADER => array(
    'Content-Type: application/x-www-form-urlencoded'
  ),
));


$result = json_decode(curl_exec($curl));

curl_close($curl); 
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
                        echo 'Vosu n\'avez pas de ticket' ;
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
     
     //if ($result && $result['status'] == 200) echo $result['message'];
     //else echo 'Erreur lors de la récupération des informations.';
     
     //echo curl_exec($ch);
     //curl_close($ch);
    }
    }

/**
 * picture_user_presence();
 * Get the picture coworker
 */
function picture_user_presence() {
    //date_default_timezone_set('Europe/Paris');

    $hour_now = date('H');
    $hour_now += 2;
    $start_hour = 12;
    $end_hour = 14;


    if ($hour_now >= $start_hour && $hour_now < $end_hour) {
        $curl_url = 'https://tickets.coworking-metz.fr/api/current-users?key=bupNanriCit1&delay=120';
    } else {
        $curl_url = 'https://tickets.coworking-metz.fr/api/current-users?key=bupNanriCit1&delay=15';
    }

    $curl = curl_init();

    curl_setopt_array($curl, array(
    CURLOPT_URL => $curl_url,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'GET',
    ));
    
    $user_presence = curl_exec($curl);
    
    curl_close($curl);
    
    $json_user_presence = json_decode($user_presence, true);
    
    //DIV wrapper
    echo $hour_now;
    echo '<div class="grid-images">';
    
    //Images trombi
    foreach ($json_user_presence as $key => $value){
      $i = 0;
      $url_image = get_user_meta($value['wpUserId'], $key = 'url_image_trombinoscope', $single = true );
      $image_array = wp_get_attachment_image_src($url_image);
      $image_url = $image_array[0];
      $user_balance = $value['balance'];
     
        if ($user_balance < 1) {
            echo '<img class="animated-image balance-tickets" style="transform: rotate(' . rand(-6,6) . 'deg); animation-delay: '. (rand(0,1000) + ($i*100)) . 'ms;" src="' . $image_url . '"><span class="language"></span>';
        } else {
            echo '<img class="animated-image" style="transform: rotate(' . rand(-6,6) . 'deg); animation-delay: '. (rand(0,1000) + ($i*100)) . 'ms;" src="' . $image_url . '"><span class="language"></span>';
        }
      $i++;
    }
    //DIV closing
    echo '</div>';
    }

/*
 * Remove Sitemap.xml
 */
add_action( 'init', function() {
    remove_action( 'init', 'wp_sitemaps_get_server' );
    }, 5 );

/*
 * WooCommerce Get custom meta product Membership -> webhook
 */

function add_order_item_meta($item_id, $values) {
    $key = 'purchase_membership';
    $value = get_post_meta( $values['product_id'], 'purchase_membership', true );
    woocommerce_add_order_item_meta($item_id, $key, $value);
}
add_action('woocommerce_add_order_item_meta', 'add_order_item_meta', 10, 2);

/*
 * WooCommerce Get customer email meta -> webhook
 */

add_action( 'woocommerce_checkout_update_order_meta', 'coworker_email_checkout_field_update_order_meta' );

function coworker_email_checkout_field_update_order_meta( $order_id ) {
    $current_user = wp_get_current_user();
    $coworker_email = $current_user->user_login;
    update_post_meta( $order_id, 'coworker_email' , $coworker_email );
}

function coworker_email_wc_api_order_response( $order_data, $order ) {

    $coworker_email_meta = get_post_meta($order->id , 'coworker_email' , true );
    $order_data['coworker_email'] = $coworker_email_meta;
    return $order_data;
}

add_filter( 'woocommerce_api_order_response', 'coworker_email_wc_api_order_response', 10, 3 );

/*
 * WooCommerce Disable async webhook delivery
 */
function custom_woocommerce_disable_async_webhook() {
    return false;
}
add_filter('woocommerce_webhook_deliver_async', 'custom_woocommerce_disable_async_webhook');

/*
 * WooCommerce Disable order again button
 */
remove_action( 'woocommerce_order_details_after_order_table', 'woocommerce_order_again_button' );

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
  CURLOPT_POSTFIELDS => 'key=bupNanriCit1&email=' . $user_email,
  CURLOPT_HTTPHEADER => array(
    'Content-Type: application/x-www-form-urlencoded'
  ),
));


$result = json_decode(curl_exec($curl));

curl_close($curl); 
?>
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
                        l\'accès à coworking est conditionné par un solde positif de tickets.<br>
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
                        il s\'agit d\'une prérogative de notre assureur. Sans cette cotisation, il ne vous est pas possible de venir coworker.</u>';
                    }
                ?>
            </p>
            <p>
                Vous avez coworké <em><?php echo $result->activity; ?></em> journées au cours des 6 derniers mois.
                    <?php 
                        if ($result->activeUser == true && $result->lastMembership == $currentYear){
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
                    <sup>*</sup>Ce solde n'inclut pas votre éventuelle présence de ce jour. Il est recalculé tous les soirs entre 23h00 et 00h00. Le décompte des tickets se fait de la manière suivante :<br>
                        - entre 0 et 5h de présence sur une journée : 1/2 ticket ;<br>
                        - plus de 5h de présence sur une journée : 1 ticket.<br><br>
                    <sup>**</sup>Membre actif : personne qui dispose d'une cotisation annuelle à jour et qui a coworké au moins 20 journées au cours des 6 derniers mois. Permet de voter lors de 
                    l'assemblée générale de l'Association.</span>
            </p>
        </div>
    <?php
     
     //if ($result && $result['status'] == 200) echo $result['message'];
     //else echo 'Erreur lors de la récupération des informations.';
     
     //echo curl_exec($ch);
     //curl_close($ch);
 
    }

/**
 * api_top_twenty();
 * Get the top twenty list of presences from the ticket service
 */
function api_top_twenty_challenge() {

    $curl = curl_init();

    $yesterday = date('Y-m-d',strtotime("-1 days"));
    $thirty_yesterday = date('Y-m-d',strtotime("-30 days"));
    $before_yesterday = date('Y-m-d',strtotime("-31 days"));
    $thirty_before_yesterday = date('Y-m-d',strtotime("-60 days"));

    curl_setopt_array($curl, array(
      CURLOPT_URL => 'https://tickets.coworking-metz.fr/api/users-stats?sort=presencesJours&key=bupNanriCit1&from=' . $thirty_yesterday . '&' . $yesterday,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'GET',
    ));
    
    $response_yesterday = curl_exec($curl);
    curl_close($curl);
    
    $json_yesterday = json_decode($response_yesterday, true);

    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://tickets.coworking-metz.fr/api/users-stats?sort=presencesJours&key=bupNanriCit1&from=' . $thirty_before_yesterday . '&' . $before_yesterday,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
      ));
      
    $response_before_yesterday = curl_exec($curl);
    curl_close($curl);
      
    $json_before_yesterday = json_decode($response_before_yesterday, true);
    
    $html = '<div class="top-20"><table class="first-top"><tr>';
    
    $rank = 0;
    $last_score = false;
    $rows = 0;

    for($i=0; $i<10; $i++){
        $rows++;
        if( $last_score!= round($json_yesterday[$i]['presencesJours']) ){
            $last_score = round($json_yesterday[$i]['presencesJours']);
            $rank = $rows;
        }
        if ( $json_yesterday[$i]['ranking'] > $json_before_yesterday[$i]['ranking'] ) {
            $up_down = '<img src="https://www.coworking-metz.fr/wp-content/uploads/2021/11/fleche-verte.png">';
        } elseif ( $json_yesterday[$i]['ranking'] < $json_before_yesterday[$i]['ranking'] ) {
            $up_down = '<img src="https://www.coworking-metz.fr/wp-content/uploads/2021/11/fleche-rouge.png">';
        } else {
            $up_down = '<img src="https://www.coworking-metz.fr/wp-content/uploads/2021/11/fleche-orange.png">';
        }
        $html .= '<td class="top-position">' . $rank . '</td><td class="name-position">' . $json_yesterday[$i]['firstName'] . '  ' . 
        substr($json_yesterday[$i]['lastName'], 0, 1) . '. (' . round($json_yesterday[$i]['presencesJours']) . ' jours) ' . '</td><td><span class="arrow">' . $up_down . '</span></td></tr>' ;
    }

    $html .= '</table><table class="last-top"><tr>';

    for($i=10; $i<20; $i++){
        $rows++;
        if( $last_score!= round($json_yesterday[$i]['presencesJours']) ){
            $last_score = round($json_yesterday[$i]['presencesJours']);
            $rank = $rows;
        }
        if ( $json_yesterday[$i]['ranking'] > $json_before_yesterday[$i]['ranking'] ) {
            $up_down = '<img src="https://www.coworking-metz.fr/wp-content/uploads/2021/11/fleche-verte.png">';
        } elseif ( $json_yesterday[$i]['ranking'] < $json_before_yesterday[$i]['ranking'] ) {
            $up_down = '<img src="https://www.coworking-metz.fr/wp-content/uploads/2021/11/fleche-rouge.png">';
        } else {
            $up_down = '<img src="https://www.coworking-metz.fr/wp-content/uploads/2021/11/fleche-orange.png">';
        }
        
        $html .= '<td class="top-position">' . $rank . '</td><td class="name-position">' . $json_yesterday[$i]['firstName'] . '  ' . 
        substr($json_yesterday[$i]['lastName'], 0, 1) . '. (' . round($json_yesterday[$i]['presencesJours']) . ' jours) ' . '</td><td><span class="arrow">' . $up_down . '</span></td></tr>' ;
    }

    $html .= '</table></div>';

    echo $html;

}

/**
 * ----------- OLD version ----------   
 * coworking_user_presences(); 
 * Get the user list of presences from the ticket service

function the_coworking_user_presences() {
    
    global $user_email;

    $users_list = json_decode( coworking_curl("http://tickets.coworking-metz.fr/backup", "key=friWuItJoys0") );
    
    //find user by email
    $user_datas = array_filter($users_list->users, function($obj) use ($user_email) {
        if (isset($obj->emails)) {
            foreach($obj->emails as $m) {
                if($m->address == $user_email) {
                    return true;
                }
            }
        }
        return false;
    });
    //transforms $user_datas[nn] in $user_datas
    $user_datas = reset($user_datas);
    
    //returns an HTML Table to display the user's presences
    if($user_datas && $user_datas->profile && $user_datas->profile->presences && count($user_datas->profile->presences) > 0) {
        // print('<pre>');
        // print_r($user_datas->profile->presences);
        // print('</pre>');
        
        $html = '<div class="my-account-presences-list"><table class="table table-left">';
        $html .= '<caption>3 derniers mois</caption>';
        $html .= '<tr><th>Date</th><th>durée</th></tr>';
        
        $presences = (array) $user_datas->profile->presences;

        $now = time();
        
        $switchCol = false;
        $presenceCount = 0;
        
        for ($i=count($presences)-1; $i>=0; $i--) {
            $presence_date = DateTime::createFromFormat('Y-m-d', $presences[$i]->date);

            //convert $presence_date into a timestamp.
            $thenTimestamp = strtotime( $presence_date->format('Y-m-d H:i:s') );
            //Get the difference in days.
            $difference = ($now - $thenTimestamp)/60/60/24;

            //only the last 6 month are displayed
            if($difference < 182) {

                $html .= '<tr>';
                $html .= '<td class="presence-date"><span>' . $presence_date->format('j/m/Y') . '</span></td>';
                $html .= '<td class="presence-amount"><span>' . $presences[$i]->amount . ' journée</span></td>';
                $html .= '</tr>';
                
                //second row
                if($difference > 91 && !$switchCol) {
                    $switchCol = true;
                    $html .= '</table>';
                    $html .= '<table class="table table-right">';
                    $html .= '<caption>3 mois suivants</caption>';
                    $html .= '<tr><th>Date</th><th>durée</th></tr>';
                }
            }
            if($difference > 182){
                $presenceCount++;
            }
        }
        if($presenceCount>0){
            //$html .= '<tr>';
            //$html .= '<td class="presence-after-6-month" colspan="2"><span>' . $presenceCount . ' présence(s) de plus de 6 mois...</span></td>';
            //$html .= '</tr>';
        }
        $html .= '</table></div>';

        echo $html;
    } else {
        echo '<div class="my-account-presences-list">Aucune donnée trouvée.</div>';
    }
}
 */
/**
 * api_coworker_presences();
 * Get the user list of presences from the ticket service
 */

function api_coworker_presences(){
    if (is_user_logged_in()) {

        $current_user = wp_get_current_user();
        $coworker_email = $current_user->user_login;
            
        $curl = curl_init();
        
        curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://tickets.coworking-metz.fr/api/user-presences?key=bupNanriCit1&email=' . $coworker_email,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/x-www-form-urlencoded'
        ),
        ));
        
        $user_presence = curl_exec($curl);
        
        curl_close($curl);
        
        $json_user_presence = json_decode($user_presence, true);

        $html = '<h5 style="text-align: center">Décompte</h5>';
        $html .= '<div class="my-account-presences-list"><table class="table table-left">';
        $html .= '<caption></caption>';
        $html .= '<tr><th>Date</th><th>Durée</th><th>Couvert par</th></tr>';
        
        foreach ($json_user_presence as $key => $value) {
            $presence_date = strtotime($value['date']);
			$presence_day = date_i18n('l', $presence_date);
			$presence_month = date_i18n('F', $presence_date);
			$presence_year = date_i18n('Y', $presence_date);
			$array_day [] = $presence_day;
			$array_month [] = $presence_month;
			$array_year [] = $presence_year;
            $result_type = ($value['type'] == 'T') ? '<img src="https://www.coworking-metz.fr/wp-content/uploads/2021/11/ticket-le-poulailler.png"> ticket' : '<img src="https://www.coworking-metz.fr/wp-content/uploads/2021/11/abonnement-type.png"> abonnement';
            $html .= '<tr>';
            $html .= '<td class="presence-date"><span>' . date_i18n('l d F Y', $presence_date) . '</span></td>';
            $html .= '<td class="presence-amount"><span>' . $value['amount'] . ' journée</span></td>';
            $html .= '<td class="result-type">' . $result_type . '</td>';
            
            $html .= '</tr>';
        }
        echo '<script src="https://cdn.jsdelivr.net/npm/chart.js@3.6.0/dist/chart.min.js"></script>';
        // values by days
        $json_value = '<script> const datasPresencesMonday = ';
        $json_value .= json_encode(array_count_values($array_day)['lundi']);
        $json_value .= '</script>';
        $json_value .= '<script> const datasPresencesTuesday = ';
        $json_value .= json_encode(array_count_values($array_day)['mardi']);
        $json_value .= '</script>';
        $json_value .= '<script> const datasPresencesWednesday = ';
        $json_value .= json_encode(array_count_values($array_day)['mercredi']);
        $json_value .= '</script>';
        $json_value .= '<script> const datasPresencesThursday = ';
        $json_value .= json_encode(array_count_values($array_day)['jeudi']);
        $json_value .= '</script>';
        $json_value .= '<script> const datasPresencesFriday = ';
        $json_value .= json_encode(array_count_values($array_day)['vendredi']);
        $json_value .= '</script>';
        $json_value .= '<script> const datasPresencesSaturday = ';
        $json_value .= json_encode(array_count_values($array_day)['samedi']);
        $json_value .= '</script>';
        $json_value .= '<script> const datasPresencesSunday = ';
        $json_value .= json_encode(array_count_values($array_day)['dimanche']);
        $json_value .= '</script>';
        //values by months
        $json_value .= '<script> const datasPresencesJanuary = ';
        $json_value .= json_encode(array_count_values($array_month)['janvier']);
        $json_value .= '</script>';
        $json_value .= '<script> const datasPresencesFebruary = ';
        $json_value .= json_encode(array_count_values($array_month)['février']);
        $json_value .= '</script>';
        $json_value .= '<script> const datasPresencesMarch = ';
        $json_value .= json_encode(array_count_values($array_month)['mars']);
        $json_value .= '</script>';
        $json_value .= '<script> const datasPresencesApril = ';
        $json_value .= json_encode(array_count_values($array_month)['avril']);
        $json_value .= '</script>';
        $json_value .= '<script> const datasPresencesMay = ';
        $json_value .= json_encode(array_count_values($array_month)['mai']);
        $json_value .= '</script>';
        $json_value .= '<script> const datasPresencesJune = ';
        $json_value .= json_encode(array_count_values($array_month)['juin']);
        $json_value .= '</script>';
        $json_value .= '<script> const datasPresencesJuly = ';
        $json_value .= json_encode(array_count_values($array_month)['juillet']);
        $json_value .= '</script>';
        $json_value .= '<script> const datasPresencesAugust = ';
        $json_value .= json_encode(array_count_values($array_month)['août']);
        $json_value .= '</script>';
        $json_value .= '<script> const datasPresencesSeptember = ';
        $json_value .= json_encode(array_count_values($array_month)['septembre']);
        $json_value .= '</script>';
        $json_value .= '<script> const datasPresencesOctober = ';
        $json_value .= json_encode(array_count_values($array_month)['octobre']);
        $json_value .= '</script>';
        $json_value .= '<script> const datasPresencesNovember = ';
        $json_value .= json_encode(array_count_values($array_month)['novembre']);
        $json_value .= '</script>';
        $json_value .= '<script> const datasPresencesDecember = ';
        $json_value .= json_encode(array_count_values($array_month)['décembre']);
        $json_value .= '</script>';
        //values by years
        $json_value .= '<script> const datasPresences2014 = ';
        $json_value .= json_encode(array_count_values($array_year)['2014']);
        $json_value .= '</script>';
        $json_value .= '<script> const datasPresences2015 = ';
        $json_value .= json_encode(array_count_values($array_year)['2015']);
        $json_value .= '</script>';
        $json_value .= '<script> const datasPresences2016 = ';
        $json_value .= json_encode(array_count_values($array_year)['2016']);
        $json_value .= '</script>';
        $json_value .= '<script> const datasPresences2017 = ';
        $json_value .= json_encode(array_count_values($array_year)['2017']);
        $json_value .= '</script>';
        $json_value .= '<script> const datasPresences2018 = ';
        $json_value .= json_encode(array_count_values($array_year)['2018']);
        $json_value .= '</script>';
        $json_value .= '<script> const datasPresences2019 = ';
        $json_value .= json_encode(array_count_values($array_year)['2019']);
        $json_value .= '</script>';
        $json_value .= '<script> const datasPresences2020 = ';
        $json_value .= json_encode(array_count_values($array_year)['2020']);
        $json_value .= '</script>';
        $json_value .= '<script> const datasPresences2021 = ';
        $json_value .= json_encode(array_count_values($array_year)['2021']);
        $json_value .= '</script>';
        $json_value .= '<script> const datasPresences2022 = ';
        $json_value .= json_encode(array_count_values($array_year)['2022']);
        $json_value .= '</script>';
  
    echo $json_value;

    echo '<canvas id="dayChart" width="400" height="400"></canvas>';
    echo "<script>
    const ctxDay = document.getElementById('dayChart').getContext('2d');
    const dayChart = new Chart(ctxDay, {
        type: 'bar',
        data: {
            labels: ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche'],
            datasets: [{
                label: 'Nb de présences ',
                data: [datasPresencesMonday, datasPresencesTuesday, datasPresencesWednesday, datasPresencesThursday, datasPresencesFriday, datasPresencesSaturday, datasPresencesSunday],
                backgroundColor: [
                    'rgba(224, 171, 78, 0.5)',
                    'rgba(224, 171, 78, 0.5)',
                    'rgba(224, 171, 78, 0.5)',
                    'rgba(224, 171, 78, 0.5)',
                    'rgba(224, 171, 78, 0.5)',
                    'rgba(224, 171, 78, 0.5)',
                    'rgba(224, 171, 78, 0.5)'
                ],
                borderColor: [
                    'rgba(224, 171, 78, 1)',
                    'rgba(224, 171, 78, 1)',
                    'rgba(224, 171, 78, 1)',
                    'rgba(224, 171, 78, 1)',
                    'rgba(224, 171, 78, 1)',
                    'rgba(224, 171, 78, 1)',
                    'rgba(224, 171, 78, 1)'
                ],
                borderWidth: 2,
                borderRadius: 5
            }]
        },
        options: {
            plugins: {
                title: {
                    display: true,
                    text: 'Nombre de présences cumulées (jours)'
                },
                legend: {
                    display: false,
                    labels: {
                        color: 'rgb(255, 99, 132)'
                    }
                },
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            },
            aspectRatio: 2
        }
    });
    </script>";

    echo '<canvas id="monthChart" width="400" height="400"></canvas>';
    echo "<script>
    const ctxMonth = document.getElementById('monthChart').getContext('2d');
    const monthChart = new Chart(ctxMonth, {
        type: 'bar',
        data: {
            labels: ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'],
            datasets: [{
                label: 'Nb de présences ',
                data: [datasPresencesJanuary, datasPresencesFebruary, datasPresencesMarch, datasPresencesApril, datasPresencesMay, datasPresencesJune, datasPresencesJuly, datasPresencesAugust, datasPresencesSeptember,
                 datasPresencesOctober, datasPresencesNovember, datasPresencesDecember],
                backgroundColor: [
                    'rgba(224, 171, 78, 0.5)',
                    'rgba(224, 171, 78, 0.5)',
                    'rgba(224, 171, 78, 0.5)',
                    'rgba(224, 171, 78, 0.5)',
                    'rgba(224, 171, 78, 0.5)',
                    'rgba(224, 171, 78, 0.5)',
                    'rgba(224, 171, 78, 0.5)',
                    'rgba(224, 171, 78, 0.5)',
                    'rgba(224, 171, 78, 0.5)',
                    'rgba(224, 171, 78, 0.5)',
                    'rgba(224, 171, 78, 0.5)',
                    'rgba(224, 171, 78, 0.5)'
                ],
                borderColor: [
                    'rgba(224, 171, 78, 1)',
                    'rgba(224, 171, 78, 1)',
                    'rgba(224, 171, 78, 1)',
                    'rgba(224, 171, 78, 1)',
                    'rgba(224, 171, 78, 1)',
                    'rgba(224, 171, 78, 1)',
                    'rgba(224, 171, 78, 1)',
                    'rgba(224, 171, 78, 1)',
                    'rgba(224, 171, 78, 1)',
                    'rgba(224, 171, 78, 1)',
                    'rgba(224, 171, 78, 1)',
                    'rgba(224, 171, 78, 1)'
                ],
                borderWidth: 2,
                borderRadius: 5
            }]
        },
        options: {
            plugins: {
                title: {
                    display: true,
                    text: 'Nombre de présences cumulées (mois)',
                    padding: {
                        top: 30,
                        bottom: 10
                    },
                },
                legend: {
                    display: false,
                    labels: {
                        color: 'rgb(255, 99, 132)'
                    }
                },
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            },
            aspectRatio: 2
        }
    });
    </script>";

    echo '<canvas id="yearChart" width="400" height="400"></canvas>';
    echo "<script>
    const ctxYears = document.getElementById('yearChart').getContext('2d');
    const yearChart = new Chart(ctxYears, {
        type: 'bar',
        data: {
            labels: ['2014', '2015', '2016', '2017', '2018', '2019', '2020', '2021', '2022'],
            datasets: [{
                label: 'Nb de présences ',
                data: [datasPresences2014, datasPresences2015, datasPresences2016, datasPresences2017, datasPresences2018, datasPresences2019, datasPresences2020, datasPresences2021, datasPresences2022],
                backgroundColor: [
                    'rgba(224, 171, 78, 0.5)',
                    'rgba(224, 171, 78, 0.5)',
                    'rgba(224, 171, 78, 0.5)',
                    'rgba(224, 171, 78, 0.5)',
                    'rgba(224, 171, 78, 0.5)',
                    'rgba(224, 171, 78, 0.5)',
                    'rgba(224, 171, 78, 0.5)',
                    'rgba(224, 171, 78, 0.5)',
                    'rgba(224, 171, 78, 0.5)'
                ],
                borderColor: [
                    'rgba(224, 171, 78, 1)',
                    'rgba(224, 171, 78, 1)',
                    'rgba(224, 171, 78, 1)',
                    'rgba(224, 171, 78, 1)',
                    'rgba(224, 171, 78, 1)',
                    'rgba(224, 171, 78, 1)',
                    'rgba(224, 171, 78, 1)',
                    'rgba(224, 171, 78, 1)',
                    'rgba(224, 171, 78, 1)'
                ],
                borderWidth: 2,
                borderRadius: 5
            }]
        },
        options: {
            plugins: {
                title: {
                    display: true,
                    text: 'Nombre de présences cumulées (année)',
                    padding: {
                        top: 30,
                        bottom: 10
                    },
                },
                legend: {
                    display: false,
                    labels: {
                        color: 'rgb(255, 99, 132)'
                    }
                },
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            },
            aspectRatio: 2
        }
    });
    </script>";
        
        $html .= '</table></div>';
        
        echo $html;
    } else {
        echo 'Tu n\'es pas connecté';
    }
}

/**
 * api_purchase_start_stop_abo();
 * Get the abo purchases, start and end dates
 */
function api_purchase_start_stop_abo() {
    if (is_user_logged_in()) {

        $current_user = wp_get_current_user();
        $coworker_email = $current_user->user_login;
    
        $curl = curl_init();
    
        curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://tickets.coworking-metz.fr/api/user-stats?key=bupNanriCit1&email=' . $coworker_email,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        ));
    
        
        $result = json_decode(curl_exec($curl));
    
        curl_close($curl);
        
        $abo_array = $result->abos;
    
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

/**
 * Automatically add product to cart on visit (café / thé)
 */
add_action( 'template_redirect', 'add_product_to_cart' );
function add_product_to_cart() {
    if ( ! is_admin()  && !is_cart() && !is_checkout()) {
        $product_id = 20367; //café-thé
        $found = false;
        //check if product already in cart
        if ( sizeof( WC()->cart->get_cart() ) > 0 ) {
            foreach ( WC()->cart->get_cart() as $cart_item_key => $values ) {
                $_product = $values['data'];
                if ( $_product->get_id() == $product_id )
                    $found = true;
            }
            // if product not found, add it
            if ( ! $found )
                WC()->cart->add_to_cart( $product_id );
        } else {
            // if no products in cart, add it
            WC()->cart->add_to_cart( $product_id );
        }
    }
}

/**
 * Stay logged in
 */
add_filter ( 'auth_cookie_expiration', 'wpdev_login_session' );
 
function wpdev_login_session( $expire ) {
    // 6 month in seconds
    return 15552000;
}

/**
 * Page of users presence
 */

if(is_admin()){
    add_action( 'admin_menu', 'coworking_user_presences_add_plugin_page' );
}

function coworking_user_presences_add_plugin_page() {
    add_users_page(
        'User presences', // page_title
        'User presences', // menu_title
        'manage_options', // capability
        'user-presences', // menu_slug
        'coworking_user_presences_create_admin_page'
    );
}

function coworking_user_presences_create_admin_page() {
?>

    <div class="wrap">
        <h2>Résumé des présences</h2>
        <h4 class="title">Liste des utilisateurs et de leurs présences au Coworking.</h4>

        <div class="user-list">
            <?php
            $args = array(
                'orderby' => 'display_name',
                'role__not_in' => 'Subscriber'
            );
            
            // The Query
            $user_query = new WP_User_Query( $args );
            
            // User Loop
            if ( ! empty( $user_query->get_results() ) ) {
                foreach ( $user_query->get_results() as $user ) {
                    echo '<div class="box">';
                    echo '<a href="'. get_admin_url(NULL, 'user-edit.php?user_id='.$user->ID ) .'">' . $user->display_name . '</a> - '. $user->user_email .'<br>';
                    the_coworking_user_balance($user->user_email);
                    echo '</div>';
                }
            } else {
                echo 'Aucun utilisateur trouvé.';
            }
            ?>
        </div>
    </div>
<?php }


//remove endpoints api for security 
add_filter( 'rest_endpoints', 'disable_custom_rest_endpoints');
function disable_custom_rest_endpoints( $endpoints ) {
    $routes = array( '/wp/v2/types', '/wp/v2/media', '/wp/v2/pages', '/wp/v2/media/(?P<id>[\d]+)' );

    foreach ( $routes as $route ) {
        if ( empty( $endpoints[ $route ] ) ) {
            continue;
        }

        foreach ( $endpoints[ $route ] as $i => $handlers ) {
            if ( is_array( $handlers ) && isset( $handlers['methods'] ) &&
                'GET' === $handlers['methods'] ) {
                unset( $endpoints[ $route ][ $i ] );
            }
        }
    }

    return $endpoints;
}

// Disable Gutenberg
add_filter( 'use_block_editor_for_post', '__return_false' );

// This will replace the 'wp-json' REST API prefix with 'api-json-wp'.
add_filter( 'rest_url_prefix', function () {
	return 'api-json-wp';
} );