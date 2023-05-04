<?php
/**
 * Plugin Name: WooCommerce Character Case Correction
 * Plugin URI: http://actualityextensions.com/
 * Description: Customers can sometimes enter their name all in lower case or in other unexpected case. This extension enables you to correct this when they submit their details.
 * Version: 1.1
 * Author: Actuality Extensions
 * Author URI: http://actualityextensions.com/
 * Tested up to: 3.7.1
 *
 * Copyright: (c) 2012-2013 Actuality Extensions
 *
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 *
 * @package     WC-Customer-Case-Correction
 * @author      Actuality Extensions
 * @category    Plugin
 * @copyright   Copyright (c) 2012-2013, Actuality Extensions
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

if ( ! defined( 'ABSPATH' ) ) exit;

if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
	class RS_Caps_filter {
			
		public function __construct() {
				
			global $woocommmerce, $wp_query;
			
			add_action( 'woocommerce_before_checkout_process', array(&$this, 'rs_caps_filter' ), 99);
		
		}
		
		function rs_caps_filter( $posted ) {
		
			global $woocommerce;
			
			$check_fields = array(
						'billing_first_name' => $_POST[ 'billing_first_name' ],
						'billing_last_name' => $_POST[ 'billing_last_name' ],
						'billing_company' => $_POST[ 'billing_company' ],
						'billing_address_1' => $_POST[ 'billing_address_1' ],
						'billing_address_2' => $_POST[ 'billing_address_2' ],
						'billing_city' => $_POST[ 'billing_city' ],
						'billing_postcode' => $_POST[ 'billing_postcode' ],
						'billing_state' => $_POST[ 'billing_state' ],
						'shipping_first_name' => $_POST[ 'shipping_first_name' ],
						'shipping_last_name' => $_POST[ 'shipping_last_name' ],
						'shipping_company' => $_POST[ 'shipping_company' ],
						'shipping_address_1' => $_POST[ 'shipping_address_1' ],
						'shipping_address_2' => $_POST[ 'shipping_address_2' ],
						'shipping_city' => $_POST[ 'shipping_city' ],
						'shipping_postcode' => $_POST[ 'shipping_postcode' ],
						'shipping_state' => $_POST[ 'shipping_state' ]
			);
		
			
			foreach ( $check_fields as $key => $field ) {
				$field = $this->nameize( $field );
				$_POST[ $key ] = $field;
			}
			
		
		
		}
		
		function nameize($str,$a_char = array("'","-"," ")){   
		    //$str contains the complete raw name string
		    //$a_char is an array containing the characters we use as separators for capitalization. If you don't pass anything, there are three in there as default.
		 	$string = strtolower($str);
		    foreach ($a_char as $temp){
		        $pos = strpos($string,$temp);
		        if ($pos){
		            //we are in the loop because we found one of the special characters in the array, so lets split it up into chunks and capitalize each one.
		            $mend = '';
		            $a_split = explode($temp,$string);
		            foreach ($a_split as $temp2){
		                //capitalize each portion of the string which was separated at a special character
		                $mend .= ucfirst($temp2).$temp;
		                }
		            $string = substr($mend,0,-1);
		            }   
		        }	
		   			
		    return ucfirst($string);
		    }
	}
	$caps_filter = new RS_Caps_filter();
}



?>