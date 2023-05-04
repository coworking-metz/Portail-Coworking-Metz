<?php
namespace Bookmify;



// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {exit; }


/**
 * Class HelperCoupons
 */
class HelperCoupons
{
	
	/* since bookmify v1.3.6 */
	public static function clearExpiredCoupons(){
		global $wpdb;
		$today			= date('Y-m-d H:i:s');
		$status			= 'overdue';
		$active			= 'active';
		$ddd			= '0000-00-00 00:00:00';
		
		$wpdb->query($wpdb->prepare( "UPDATE {$wpdb->prefix}bmify_coupons SET status=%s WHERE status=%s AND date_limit_end!=%s AND date_limit_end<%s", $status, $active, $ddd, $today));
		$usedCount		= 5;
		$query 			= "SELECT id,usage_limit FROM {$wpdb->prefix}bmify_coupons WHERE status='active' AND date_limit_end>'".$today."'";
		$results 		= $wpdb->get_results( $query);
		foreach($results as $result){
			$couponID		= $result->id;
			$usageLimit		= $result->usage_limit;
			$query2 		= "SELECT * FROM {$wpdb->prefix}bmify_coupons_used WHERE coupon_id=".$couponID;
			$usedCount		= count($wpdb->get_results( $query2));
			if($usedCount >= $usageLimit){
				$wpdb->query($wpdb->prepare( "UPDATE {$wpdb->prefix}bmify_coupons SET status=%s WHERE id=%d", $status, $couponID));
			}
		}
	}
	
	/* since bookmify v1.3.6 */
	public static function getUsedCouponsCount($couponID){
		global $wpdb;
		
		$query 			= "SELECT * FROM {$wpdb->prefix}bmify_coupons_used WHERE coupon_id=".$couponID;
		$results 		= $wpdb->get_results( $query);
		$usedCount		= count($results);
		return $usedCount;
		
	}
}