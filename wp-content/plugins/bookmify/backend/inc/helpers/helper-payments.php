<?php
namespace Bookmify;



// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {exit; }


/**
 * Class HelperPayments
 */
class HelperPayments
{
	
	/**
     * Public Funtion.
	 * @since 1.0.0
     */
	public static function servicesAsFilter(){
		global $wpdb;
		$query 		= "SELECT title, id FROM {$wpdb->prefix}bmify_services";
		$results	= $wpdb->get_results( $query, OBJECT  );
		$html 		= '<div class="bookmify_be_services_filter_list">';
		foreach ( $results as $result ) {
			$html  .= '<div data-id="'.$result->id.'">'.esc_html( $result->title ).'</div>';
		}
		$html 	   .= '</div>';
		return $html;
	}
	
	
	/**
     * Public Funtion.
	 * @since 1.0.0
     */
	public static function customersAsFilter(){
		global $wpdb;
		$query 		= "SELECT first_name, last_name, id FROM {$wpdb->prefix}bmify_customers";
		$results	= $wpdb->get_results( $query, OBJECT  );
		$html 		= '<div class="bookmify_be_filter_popup_list customers">
							<div class="bookmify_be_filter_popup_list_in">';
		foreach ( $results as $result ) {
			$html  .= '<div data-id="'.$result->id.'" class="item"><span>'.esc_html( $result->first_name.' '.$result->last_name ).'</span></div>';
		}
		$html 	   .= '</div></div>';
		return $html;
	}
	
	/**
     * Public Funtion.
	 * @since 1.0.0
     */
	public static function employeesAsFilter(){
		global $wpdb;
		$query 		= "SELECT first_name, last_name, id FROM {$wpdb->prefix}bmify_employees";
		$results	= $wpdb->get_results( $query, OBJECT  );
		$html 		= '<div class="bookmify_be_filter_popup_list employees">
							<div class="bookmify_be_filter_popup_list_in">';
		foreach ( $results as $result ) {
			$html  .= '<div data-id="'.$result->id.'" class="item"><span>'.esc_html( $result->first_name.' '.$result->last_name ).'</span></div>';
		}
		$html 	   .= '</div></div>';
		return $html;
	}
	
	/**
     * Public Funtion.
	 * @since 1.0.0
     */
	public static function statusAsFilter(){
		
		$html 		= '<div class="bookmify_be_filter_popup_list status">
							<div class="bookmify_be_filter_popup_list_in">';
		
		$html 				.= '<div data-status="full" class="item"><span>'.esc_html__('Completed', 'bookmify').'</span></div>';
		$html 				.= '<div data-status="not" class="item"><span>'.esc_html__('Pending', 'bookmify').'</span></div>';
		
		$html 	   .= '</div></div>';
		return $html;
	}
	
}

