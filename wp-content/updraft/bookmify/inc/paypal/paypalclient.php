<?php
namespace Bookmify;

use PayPalCheckoutSdk\Core\PayPalHttpClient;
use PayPalCheckoutSdk\Core\SandboxEnvironment;
use PayPalCheckoutSdk\Core\ProductionEnvironment;

ini_set('error_reporting', E_ALL); // or error_reporting(E_ALL);
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');


class PayPalClient
{
	
    /**
     * Returns PayPal HTTP client instance with environment that has access
     * credentials context. Use this instance to invoke PayPal APIs, provided the
     * credentials have access.
     */
    public static function client()
    {
        return new PayPalHttpClient(self::environment());
    }
	

    /**
     * Set up and return PayPal PHP SDK environment with PayPal access credentials.
     * This sample uses SandboxEnvironment. In production, use ProductionEnvironment.
     */
    public static function environment()
    {
		$sandboxMode		 	= get_option('bookmify_be_paypal_sendbox_mode', 'on');
		$liveClientID 			= get_option('bookmify_be_paypal_client_id_live', '');
		$sandboxClientID 		= get_option('bookmify_be_paypal_client_id', '');
		$liveClientSecret		= get_option('bookmify_be_paypal_client_secret_live', '');
		$sandboxClientSecret	= get_option('bookmify_be_paypal_client_secret', '');
		
		
		
		if($sandboxMode == 'on'){
			// if sandbox
			$clientId 			= getenv("CLIENT_ID") ?: $sandboxClientID;
			$clientSecret 		= getenv("CLIENT_SECRET") ?: $sandboxClientSecret;
			return new SandboxEnvironment($clientId, $clientSecret);
		}else{
			//if live
			$clientId 			= getenv("CLIENT_ID") ?: $liveClientID;
			$clientSecret 		= getenv("CLIENT_SECRET") ?: $liveClientSecret;
			return new ProductionEnvironment($clientId, $clientSecret);
		}

    }
}