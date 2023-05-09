<?php
namespace Bookmify;

use Bookmify\Helper;
use Bookmify\HelperFrontend;


// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) { exit; }



class HelperService{
	
	
	public static function alphaServicesAndCategoriesList($cIDs='',$sIDs='',$eIDs='',$lIDs = '', $sortBy='') {
		global $wpdb;
		$categoryFilter	 	= get_option( 'bookmify_be_feoption_category_filter_alpha', 'disabled' );
		$html 				= '';
		if($cIDs != ''){$categoryIDs 	= explode(',', $cIDs);}else{$categoryIDs 	= array();}
		if($sIDs != ''){$serviceIDs 	= explode(',', $sIDs);}else{$serviceIDs 	= array();}
		if($eIDs != ''){$employeeIDs 	= explode(',', $eIDs);}else{$employeeIDs 	= array();}
		if($lIDs != ''){$locationIDs 	= explode(',', $lIDs);}else{$locationIDs 	= array();}
		
		$query 		 = "SELECT
							s.id id,
							s.info info,
							s.attachment_id attachment_id,
							s.visibility visibility,
							s.price servicePrice,
							s.duration serviceDuration,
							s.title serviceTitle,
							s.category_id categoryIDs

						FROM 	   	   {$wpdb->prefix}bmify_services s 
							INNER 	JOIN {$wpdb->prefix}bmify_employee_services es 				ON es.service_id = s.id
							INNER 	JOIN {$wpdb->prefix}bmify_employees e 						ON es.employee_id = e.id
							LEFT 	JOIN {$wpdb->prefix}bmify_employee_locations el				ON el.employee_id = e.id
							WHERE s.visibility='public' AND e.visibility='public' AND";
			
		if(!empty($serviceIDs)){
			$serviceIDs = esc_sql($serviceIDs);
			$query .= " s.id IN (" . implode(",", array_map("intval", $serviceIDs)) . ") AND";
		}
		if(!empty($categoryIDs)){
			$categoryIDs = esc_sql($categoryIDs);
			$query .= " s.category_id IN (" . implode(",", array_map("intval", $categoryIDs)) . ") AND";
		}
		if(!empty($employeeIDs)){
			$employeeIDs = esc_sql($employeeIDs);
			$query .= " e.id IN (" . implode(",", array_map("intval", $employeeIDs)) . ") AND";
		}
		if(!empty($locationIDs)){
			$locationIDs = esc_sql($locationIDs);
			$query .= " el.location_id IN (" . implode(",", array_map("intval", $locationIDs)) . ") AND";
		}

		$query = rtrim($query, 'AND');

		$query .= " GROUP BY es.service_id";

		$sortBy = esc_sql($sortBy);
		switch($sortBy){
			default:
			case 'title_asc': 	$query .= " ORDER BY s.title ASC"; break;
			case 'title_desc': 	$query .= " ORDER BY s.title DESC"; break;
			case 'price_asc': 	$query .= " ORDER BY s.price ASC"; break;
			case 'price_desc':	$query .= " ORDER BY s.price DESC"; break;
		}

		$results		= $wpdb->get_results( $query, OBJECT  );
		$activePayment 	= HelperFrontend::activePaymentType();
		if($activePayment == ''){
			$html 		= '<p class="bookmify_fe_no_payment_method">'.esc_html__('Please, set payment method in Bookmify Settings. You have not selected any payment method.','bookmify').'</p>';
			$resultss 	= array($html,'',1);
		}else{
			$html 		= '<ul class="bookmify_fe_list service_list">';

			$categoryFilterList 		= array();
			$safeServicesList			= array();
			foreach( $results as $key => $result ){
				$ID						= $result->id;
				if(is_null($result->info)){
					$serviceInfo		= '';
				}else{
					$serviceInfo		= $result->info;
				}
				$attachmentID			= $result->attachment_id;
				$visibility				= $result->visibility;
				$price					= $result->servicePrice;
				$duration				= $result->serviceDuration;
				$title					= Helper::titleDecryption($result->serviceTitle);
				$attachmentURL	 		= Helper::bookmifyGetImageByID($attachmentID);
				$selected	 			= bookmify_be_checked($visibility, "public");
				if($attachmentURL != ''){$opened = 'has_image';}else{$opened = '';}
				$price 					= Helper::bookmifyPriceCorrection($price, 'frontend');
				$duration2 				= Helper::bookmifyNumberToDuration($duration);
				$categoryID				= $result->categoryIDs;
				$categoryFilterList[]	= $categoryID;
				if($visibility == 'public'){
					$html .=   '<li data-service-id="'.$ID.'" data-category-id="'.$categoryID.'" class="bookmify_fe_service_item bookmify_fe_list_item '.$opened.'">
									<div class="bookmify_fe_list_item_in">
										<div class="bookmify_service_heading bookmify_fe_list_item_header">
											<div class="heading_in header_in">
												<div class="img_and_color_holder">
													<div class="img_holder" style="background-image:url('.$attachmentURL.')"></div>
												</div>
												<div class="service_info">
													<div class="left_part">
														<span class="service_title">'.$title.'</span>
														<span class="service_duration">'.$duration2.'</span>
													</div>
													<div class="right_part">
														<span class="service_price"><span>'.$price.'</span></span>
														<span class="service_hover"><span>'.esc_html__('Book Now', 'bookmify').'</span></span>
													</div>
												</div>
											</div>
										</div>
									</div>
								</li>';
					$safeServicesList[$key]['id'] 				= $ID;
					$safeServicesList[$key]['title'] 			= $title;
					$safeServicesList[$key]['img_url'] 			= $attachmentURL;
					$safeServicesList[$key]['duration_sec'] 	= $duration;
					$safeServicesList[$key]['duration_html'] 	= $duration2;
					$safeServicesList[$key]['info'] 			= $serviceInfo;
				}

			}
			$html .= '</ul>';

			// get category count
			$categoryCount 			= 0;
			if(!empty($categoryFilterList)){
				$query 		 		= "SELECT title,id FROM {$wpdb->prefix}bmify_categories WHERE";
				$query 			   .= " id IN (" . implode(",", array_map("intval", $categoryFilterList)) . ") ORDER BY title";
				$results			= $wpdb->get_results( $query, OBJECT  );
				$categoryCount 		= count($results);
			}
			
			// ******************


			// Get Category List
			if($categoryFilter == 'enabled' && $categoryCount > 1){
				$categoryFilterList  = array_unique($categoryFilterList);
				$categoryList 		 = '';
				$dropDownBtn	 	 = '<span class="d_d">'.HelperFrontend::bookmifyFeSVG('drop-down-arrow').'</span>';
				$categoryList 		.= '<h3 class="bookmify_fe_alphafilter choose"><span><span class="d_text">'.esc_html__('All Services', 'bookmify').'</span>'.$dropDownBtn.'</span></h3>';
				$categoryList 		.= '<div class="bookmify_fe_alphafilter_dd">';
				$categoryList 		.= 	'<div class="bookmify_fe_alphafilter_dd_in">';
				$categoryList 		.= 		'<span class="bf_triangle"><span></span></span>';
				$categoryList 		.= 		'<div class="bookmify_fe_alphafilter_dd_list">';
				$categoryList 		.= 		'<span class="active" data-id="0">'.esc_html__('All Services', 'bookmify').'</span>';
				foreach($results as $result){
					$categoryList 	.= '<span data-id="'.$result->id.'"><span>'.$result->title.'</span></span>';
				}
				$categoryList 		.= 		'</div>';
				$categoryList 		.= 	'</div>';
				$categoryList 		.= '</div>';
				$resultss 			 = array($html,$categoryList,$categoryCount,$safeServicesList);

			}else{
				$resultss = array($html,$safeServicesList,$categoryCount);
			}
		}
			
		
		return $resultss;
	}
	
	public static function priceRange($ID){
		global $wpdb;
		
		$query 		= "SELECT MIN(price) AS min_price, MAX(price) AS max_price FROM {$wpdb->prefix}bmify_employee_services WHERE service_id=".$ID;
		$results 	= $wpdb->get_results( $query, OBJECT  );
		return Helper::bookmifyPriceCorrection($results[0]->min_price) .' - '. Helper::bookmifyPriceCorrection($results[0]->max_price);
	}
	
	
}