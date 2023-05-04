<?php
namespace Bookmify;

use Bookmify\NotificationManagement;
use Twilio\Rest\Client;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {exit; }


/**
 * Class Twilio SMS
 */
class SMSTwilio{
 
	
	/**
     * Construct
	 * @since 1.0.0
     */
	public function __construct() {
		
		require( BOOKMIFY_PATH . 'inc/twilioAPI/Twilio/autoload.php' );
		add_action( 'wp_ajax_ajaxQuerySendSMS', [$this, 'ajaxQuerySendSMS'] );
		add_action( 'wp_ajax_ajaxQuerySMSTest', [$this, 'ajaxQuerySMSTest'] );

	}
	
	
	
	public function ajaxQuerySendSMS(){
		
		// twilio important options
		$account_sid 	= get_option( 'bookmify_be_twilio_account_sid', '' );
		$auth_token 	= get_option( 'bookmify_be_twilio_auth_token', '' );
		$twilio_number 	= get_option( 'bookmify_be_twilio_number', '' );
		
		$client 		= new Client($account_sid, $auth_token);
		
		$client->messages->create(
			// Where to send a text message (your cell phone?)
			'+123123455', // receiver number for
			array(
				'from' => $twilio_number,
				'body' => 'Do you like Bookmify?' // text
			)
		);
		
		
		die(json_encode($twilio_number));
		
	}
	
	public static function sendSMS($text = '', $phoneNumber){
		// twilio important options
		$account_sid 	= get_option( 'bookmify_be_twilio_account_sid', '' );
		$auth_token 	= get_option( 'bookmify_be_twilio_auth_token', '' );
		$twilio_number 	= get_option( 'bookmify_be_twilio_number', '' );
		

		if($text != ''){

			$client 		= new Client($account_sid, $auth_token);
			$text 			= strval( $text );

			$client->messages->create(
				$phoneNumber, // receiver number for
				array(
					'from' => $twilio_number,
					'body' => $text // text
				)
			);
		}
	}
	
	public static function ajaxQuerySMSTest(){
		$management = new NotificationManagement();
		$isAjaxCall = false;
		$n_id = $n_recipient = $n_subject = $n_text = '';
	
		if(!empty($_POST['bookmify_data'])){
			$isAjaxCall 		= true;

			$notifications 		= json_decode(stripslashes($_POST['bookmify_data']));
			
			foreach($notifications as $notification){
				
				$n_id 			= $notification->id;
				$n_recipient	= $notification->recipient;
				$n_subject 		= $notification->subject;
				$n_text 		= $notification->text;
			}
			
			$placeholders 	= $management::demoPlaceholders();
			$n_subject 		= $management::replacePlaceholders($n_subject, $placeholders);
			$n_text 		= $management::replacePlaceholders($n_text, $placeholders);
			
			$this->sendSMS($n_text,$n_recipient);
			
		}
		
		
		$buffyArray = array(
			'bookmify_be_data' 		=> $n_text
		);


		if ( true === $isAjaxCall ){die(json_encode($buffyArray));} 
		else{return json_encode($buffyArray);}
	}
	
}

