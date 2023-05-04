<?php

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
 * coworking_curl_tickets() 
 * 
 * performs a curl request ton send POST data to any url and retreive the corresponding data as a json content
 * @param $url [string] the service url
 * @param $post [string] a json string containing the post data to send
 * 
*/
function coworking_curl($url,$posts=""){
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
    // curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    //     'Content-Type: application/json',
    //     'Content-Length: ' . strlen($data_string))
    // );
    curl_setopt($ch, CURLOPT_POST, $posts ? 0 :1);
    curl_setopt($ch, CURLOPT_POSTFIELDS,$posts);
    $json = curl_exec($ch);
    return $json;
    curl_close($ch);
}

/**
 * coworking_user_balance();
 * Get the user balance information from the ticket service
 */
function the_coworking_user_balance($email = NULL) {
    if( !isset($email) || !$email ) :
        global $user_email;
    else:
        $user_email = $email;
    endif;

    $data = array("key" => "bupNanriCit1", "email" => $user_email);
    $data_string = json_encode($data);

    $ch = curl_init('http://tickets.coworking-metz.fr/balance');
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
        'Content-Length: ' . strlen($data_string))
    );

    //$result = json_decode(curl_exec($ch));

    //if ($result && $result['status'] == 200) echo $result['message'];
    //else echo 'Erreur lors de la récupération des informations.';
    echo curl_exec($ch);
    curl_close($ch);
}

/**
 * coworking_user_presences();
 * Get the user list of presences from the ticket service
 */
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
            $html .= '<tr>';
            $html .= '<td class="presence-after-6-month" colspan="2"><span>' . $presenceCount . ' présence(s) de plus de 6 mois...</span></td>';
            $html .= '</tr>';
        }
        $html .= '</table></div>';

        echo $html;
    } else {
        echo '<div class="my-account-presences-list">Aucune donnée trouvée.</div>';
    }
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
    <style>
        .tickets-status {
            border: 1px solid #e0e1e0;
            background: #FFF;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 10px;
        }
        .tickets-status em {
            font-style: normal;
            display: inline-block;
            padding: 2px 6px;
            background-color: #fff8e5;
            font-weight: bold;
            border-radius: 4px;
            margin: 0px 4px;
            box-shadow: 1px 1px 2px rgba(0,0,0,.3);
        }
    </style>

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
