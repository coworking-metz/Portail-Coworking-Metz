<?php
namespace Bookmify;



// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {exit; }


/**
 * Class Payment Stripe
 */
class PaymentStripe{
 
	
	private $publishable_key 	= '';
	private $secret_key 		= '';
	private $stripeToken 		= '';
	
	/**
     * Construct
	 * @since 1.0.0
     */
	public function __construct() {
		
		require( BOOKMIFY_PATH . 'inc/stripeAPI/autoload.php' );
		
		if(get_option('bookmify_be_stripe_test_mode', 'on') == 'on'){// if test mode
			$this->publishable_key 	= get_option('bookmify_be_stripe_test_publishable_key', ''); 
			$this->secret_key 		= get_option('bookmify_be_stripe_test_secret_key', ''); 		
		}else{
			$this->publishable_key 	= get_option('bookmify_be_stripe_publishable_key', ''); 
			$this->secret_key 		= get_option('bookmify_be_stripe_secret_key', ''); 	
		}
		
			
		
		add_action( 'wp_ajax_nopriv_ajaxQueryGetStripeToken', [$this, 'ajaxQueryGetStripeToken'] );
		add_action( 'wp_ajax_ajaxQueryGetStripeToken', [$this, 'ajaxQueryGetStripeToken'] );
	}
	
	
	
	public function ajaxQueryGetStripeToken(){
		
		if (!empty($_POST['bookmify_stripeToken'])) {
			$this->stripeToken 	= $_POST['bookmify_stripeToken'];
			
			$serviceName		= $_POST['serviceName'];
			$appointmentPrice	= $_POST['appointmentPrice'];
			
			$this->setApiKeyAndserviceName($serviceName,$appointmentPrice);
		}
	}
	
	
	private function setApiKeyAndserviceName($serviceName,$appointmentPrice){
		
		\Stripe\Stripe::setVerifySslCerts(false);
		\Stripe\Stripe::setApiKey($this->secret_key);
		
		$charge = \Stripe\Charge::create([
			'amount' 		=> $appointmentPrice,
			'currency' 		=> get_option('bookmify_be_currency_format', 'USD'),
			'description' 	=> $serviceName,
			'source' 		=> $this->stripeToken,
			'metadata' 		=> ['order_id' => 6735],
		]);
		
	}		
}

new PaymentStripe;

