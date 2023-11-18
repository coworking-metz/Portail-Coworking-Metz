<?php

add_action( 'wp_enqueue_scripts', 'liquid_child_theme_style', 99 );

function liquid_parent_theme_scripts() {
    wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css?2' );
}
function liquid_child_theme_style(){
    wp_enqueue_style( 'child-one-style', get_stylesheet_directory_uri() . '/style.css?2' );	
}

add_action( 'wp_enqueue_scripts', function () {
    wp_register_script( 'custom-scripts', get_stylesheet_directory_uri( ).'/scripts.js?2' );
    wp_enqueue_script( 'custom-scripts' );
});
//features
include get_stylesheet_directory() . '/features/polaroid.php';
include get_stylesheet_directory() . '/features/gender.php';
include get_stylesheet_directory() . '/features/coworkers-now.php';
include get_stylesheet_directory() . '/features/test.php';
include get_stylesheet_directory() . '/features/top-challenge.php';
include get_stylesheet_directory() . '/features/chart-day-month.php';
//include get_stylesheet_directory() . '/money.php';

//coworkers functions
include get_stylesheet_directory() . '/features/coworker-account/user-balance.php';
include get_stylesheet_directory() . '/features/coworker-account/user-presences.php';
include get_stylesheet_directory() . '/features/coworker-account/user-subscription.php';

//app functions
include get_stylesheet_directory() . '/features/app/app-user-balance.php';

/* Allow SVG files */
function wpc_mime_types($mimes) {
	$mimes['svg'] = 'image/svg+xml';
	return $mimes;
}
add_filter('upload_mimes', 'wpc_mime_types');

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

/*
 * Remove Sitemap.xml
 */
add_action( 'init', function() {
    remove_action( 'init', 'wp_sitemaps_get_server' );
    }, 5 );

/**
 * Stay logged in
 */
add_filter ( 'auth_cookie_expiration', 'wpdev_login_session' );
 
function wpdev_login_session( $expire ) {
    // 6 month in seconds
    return 15552000;
}


//******************** WOOCOMMERCE ********************

//remove Proceed to ckechout Button if user role isn't "coworker"
//function disable_ptc() { 
//    $user = wp_get_current_user_id();
//    $roles = $user->roles;
    //if ( in_array( 'subscriber', $roles, true ) ) {
//        remove_action( 'woocommerce_proceed_to_checkout', 'woocommerce_button_proceed_to_checkout', 20 );
//        echo '<p class="checkout-button button alt">Vous ne pouvez pas passer commande </p>'; 
    //}
//}
//add_action( 'woocommerce_proceed_to_checkout', 'disable_ptc', 10 );



function golden_oak_web_design_woocommerce_checkout_terms_and_conditions() {
    remove_action( 'woocommerce_checkout_terms_and_conditions', 'wc_terms_and_conditions_page_content', 30 );
  }
  add_action( 'wp', 'golden_oak_web_design_woocommerce_checkout_terms_and_conditions' );

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

// filters the woocommerce_account_menu_items 
function filter_woocommerce_account_menu_items( $items ) { 
    
    // Remove downloads and payment-methods from woocommerce menu item

    //Array ( [dashboard] => Tableau de bord [orders] => Commandes [downloads] => Téléchargements [edit-address] => Adresses [payment-methods] => Moyens de paiement [edit-account] => Détails du compte [customer-logout] => Déconnexion )
    foreach ($items as $index => $data) {
        if ($index == 'downloads' || $index == 'payment-methods') {
            unset($items[$index]);
        }
    }

    return $items; 
}; 
         
// add the filter 
add_filter( 'woocommerce_account_menu_items', 'filter_woocommerce_account_menu_items', 10, 1 ); 



//******************** END WOOCOMMERCE ********************

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

// Disable Gutenberg
add_filter( 'use_block_editor_for_post', '__return_false' );

// This will replace the 'wp-json' REST API prefix with 'api-json-wp'.
add_filter( 'rest_url_prefix', function () {
	return 'api-json-wp';
} );

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