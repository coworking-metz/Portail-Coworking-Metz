<?php
namespace Bookmify;

use Bookmify\PayPalClient;
use PayPalCheckoutSdk\Orders\OrdersGetRequest;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {exit; }


/**
 * Class Payment Paypal
 */
class PaymentPaypal{
 
	
	/**
     * Construct
	 * @since 1.0.0
     */
	public function __construct() {
		
		require( BOOKMIFY_PATH . 'inc/paypalAPI/autoload.php' );
		require( BOOKMIFY_PATH . 'inc/paypal/paypalclient.php' );
		
		
		// paypal response
		add_action( 'wp_ajax_nopriv_ajaxQueryGetResponse', [$this, 'ajaxQueryGetResponse'] );
		add_action( 'wp_ajax_ajaxQueryGetResponse', [$this, 'ajaxQueryGetResponse'] );
		
	}
	
	public static function ajaxQueryGetResponse(){
		if (!empty($_POST['bookmify_data'])) {
			$orderID 	= $_POST['bookmify_data'];


			// 3. Call PayPal to get the transaction details
			$client 	= PayPalClient::client();
			$response 	= $client->execute(new OrdersGetRequest($orderID));
			die(json_encode($response->result, JSON_PRETTY_PRINT));
			

		}
	}

}

new PaymentPaypal;