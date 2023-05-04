<?php
namespace Bookmify;

use Bookmify\Helper;
use Bookmify\HelperService;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {exit; }


/**
 * Class Helper Shortcodes
 */
class HelperShortcodes
{
	
	public static function bookmifyMainShortcodes(){
		global $wpdb;
		$array = (object)[
			 'id' => 0, 'title' => esc_html__( 'Main Shortcode #1', 'bookmify' ), 'shortcode' => '[bookmify_app_alpha]'
		];
		return $array;
	}
	public static function sorting($order = ''){
		$output = '<div class="bookmify_be_shortcode_popup_list b_sorting">';
		
		$taClass = '';
		$tdClass = '';
		$paClass = '';
		$pdClass = '';
		$class = 'class="sending"';
		switch($order){
			case 'title_asc': 	$taClass = $class; break;
			case 'title_desc': 	$tdClass = $class; break;
			case 'price_asc': 	$paClass = $class; break;
			case 'price_desc': 	$pdClass = $class; break;
		}
		$output .= '<div '.$taClass.' data-id="title_asc">'.esc_html__( 'Title Ascending', 'bookmify' ).'</div>';
		$output .= '<div '.$tdClass.' data-id="title_desc">'.esc_html__( 'Title Descending', 'bookmify' ).'</div>';
		$output .= '<div '.$paClass.' data-id="price_asc">'.esc_html__( 'Price Ascending', 'bookmify' ).'</div>';
		$output .= '<div '.$pdClass.' data-id="price_desc">'.esc_html__( 'Price Descending', 'bookmify' ).'</div>';
		
		
		
		$output .= '</div>';
		return $output;
	}
	public static function employeeList($eIDs = ''){
        global $wpdb;
		
		$output = '<div class="bookmify_be_shortcode_popup_list b_employee">';
		
		$query 		= "SELECT
							e.id employeeID,
							e.first_name empFirstName,
							e.last_name empLastName

						FROM 	   	   {$wpdb->prefix}bmify_employees e 
							INNER JOIN {$wpdb->prefix}bmify_employee_services es 				ON es.employee_id = e.id
							INNER JOIN {$wpdb->prefix}bmify_services s 							ON es.service_id = s.id 
						GROUP BY e.id ORDER BY e.first_name,e.last_name";
		$results 		= $wpdb->get_results( $query, OBJECT  );
		
		$eIDs			= explode(',',$eIDs);
		$class			= '';
		foreach($results as $result){
			if(in_array($result->employeeID,$eIDs)){
				$class = 'class="sending"';
			}
			$output 	.= '<div '.$class.' data-id="'.$result->employeeID.'">'.$result->empFirstName.' '.$result->empLastName.'</div>';
			$class		= '';
		}
		$output .= '</div>';
		return $output;
    }
	 public static function serviceList($sIDs = ''){
        global $wpdb;
		
		$output = '<div class="bookmify_be_shortcode_popup_list b_service">';
		
		$query 			= "SELECT 
								s.id	 			serviceID,
								s.title 			serviceTitle
								
							FROM 			{$wpdb->prefix}bmify_services s 
								INNER JOIN 	{$wpdb->prefix}bmify_employee_services es ON s.id = es.service_id GROUP BY s.id ORDER BY s.title";
		$results 		= $wpdb->get_results( $query);
		
		$sIDs			= explode(',',$sIDs);
		$class			= '';
		$resultArray 	= array();
		foreach($results as $result){
			if(in_array($result->serviceID,$sIDs)){
				$class = 'class="sending"';
			}
			$output  	.= '<div '.$class.' data-id="'.$result->serviceID.'">'.$result->serviceTitle.'</div>';
			$class		= '';
		}
		
		
		$output .= '</div>';
		return $output;
    }
	 public static function locationList($lIDs = ''){
        global $wpdb;
		
		$output = '<div class="bookmify_be_shortcode_popup_list b_location">';
		
		$query 			= "SELECT 
								l.id	 			locationID,
								l.title	 			locationTitle
								
							FROM 			{$wpdb->prefix}bmify_locations l";
		$results 		= $wpdb->get_results( $query);
		
		$lIDs			= explode(',',$lIDs);
		$class			= '';
		$resultArray 	= array();
		foreach($results as $result){
			if(in_array($result->locationID,$lIDs)){
				$class = 'class="sending"';
			}
			$output  	.= '<div '.$class.' data-id="'.$result->locationID.'">'.$result->locationTitle.'</div>';
			$class		= '';
		}
		
		
		$output .= '</div>';
		return $output;
    }
	public static function categoryList($cIDs = '')
    {
        global $wpdb;
		
		$output 	= '<div class="bookmify_be_shortcode_popup_list b_category">';
		
		$query 		= "SELECT id,title FROM {$wpdb->prefix}bmify_categories ORDER BY title";
		$results 	= $wpdb->get_results( $query);
		
		$cIDs		= explode(',',$cIDs);
		$class		= '';
		$resultArray = array();
		foreach($results as $result){
			if(in_array($result->id,$cIDs)){
				$class = 'class="sending"';
			}
			$output 	.= '<div '.$class.' data-id="'.$result->id.'">'.$result->title.'</div>';
			$class		= '';
		}
		
		
		$output .= '</div>';
		return $output;
    }
	public static function frontendPreview(){
		$servicelist 	= HelperService::alphaServicesAndCategoriesList()[0];
		
		$html = '<div class="bookmify_be_frontend_preview">
					<h3 class="live_prev">'.esc_html__('Live Preview','bookmify').'</h3>
					<div class="bookmify_be_p_alpha">
						<div class="bookmify_be_p_alpha_in">
							<div class="bookmify_be_p_alpha_header">
								<span class="span_bg"></span>
								<div><div></div><h3 class="choose">'.esc_html__('Choose a Service','bookmify').'</h3></div>
							</div>
							<div class="bookmify_be_p_alpha_content">
								<div class="bookmify_be_p_alpha_footer">
									<a target="_blank" href="https://codecanyon.net/item/bookmify-appointment-booking-wordpress-plugin/23837899">
										<span class="frenify_developed_text">Developed by</span>
										<span class="frenify">Frenify</span>
									</a>
								</div>
								<div class="bookmify_be_p_service_list">
									'.$servicelist.'
								</div>
							</div>
						</div>
					</div>
				</div>';
		return $html;
	}
	public static function clonableFormShortcode(){
		
		$html = '<div class="bookmify_be_popup_form_wrap">
					'.self::frontendPreview().'
					<div class="bookmify_be_popup_form_position_fixer">
						<div class="bookmify_be_popup_form_bg">
							<div class="bookmify_be_popup_form">

								<div class="bookmify_be_popup_form_header">
									<h3>'.esc_html__('New Shortcode','bookmify').'</h3>
									<span class="closer"></span>
								</div>
								
								<div class="bookmify_be_popup_form_content">
									<div class="bookmify_be_popup_form_content_in">
									
										<div class="bookmify_be_popup_form_fields">
										
											<form autocomplete="off">
												<div class="input_wrap_row">
													<div class="input_wrap">
														<label><span class="title">'.esc_html__('Title','bookmify').'<span>*</span></span></label>
														<input class="sh_title required_field" type="text" value="" />
													</div>
												</div>
												
												<div class="input_wrap_row">
													<div class="input_wrap category">
														<label>
															<span class="title">'.esc_html__('Category','bookmify').'</span>
														</label>
														<input type="text" name="category" placeholder="'.esc_attr__('Select from Categories','bookmify').'" readonly value="" data-placeholder="'.esc_attr__('Select from Categories','bookmify').'" />
														<input type="hidden" name="category_ids" value="" />
														<span class="bot_btn"><span></span><img class="bookmify_be_svg" src="'.BOOKMIFY_ASSETS_URL.'img/down.svg" alt="" /></span>
														<div class="bookmify_be_new_value"></div>
														'.self::categoryList().'
													</div>
												</div>
												
												<div class="input_wrap_row">
													<div class="input_wrap service">
														<label>
															<span class="title">'.esc_html__('Service','bookmify').'</span>
														</label>
														<input type="text" name="service" placeholder="'.esc_attr__('Select from Services','bookmify').'" readonly value=""  data-placeholder="'.esc_attr__('Select from Services','bookmify').'" />
														<input type="hidden" name="service_ids" value="">
														<span class="bot_btn"><span></span><img class="bookmify_be_svg" src="'.BOOKMIFY_ASSETS_URL.'img/down.svg" alt="" /></span>
														<div class="bookmify_be_new_value"></div>
														'.self::serviceList().'
													</div>
												</div>
												
												<div class="input_wrap_row">
													<div class="input_wrap employee">
														<label>
															<span class="title">'.esc_html__('Employee','bookmify').'</span>
														</label>
														<input type="text" name="employee" placeholder="'.esc_attr__('Select from Employees','bookmify').'" readonly value=""  data-placeholder="'.esc_attr__('Select from Employees','bookmify').'" />
														<input type="hidden" name="employee_ids" value="">
														<span class="bot_btn"><span></span><img class="bookmify_be_svg" src="'.BOOKMIFY_ASSETS_URL.'img/down.svg" alt="" /></span>
														<div class="bookmify_be_new_value"></div>
														'.self::employeeList().'
													</div>
												</div>
												
												<div class="input_wrap_row">
													<div class="input_wrap location">
														<label>
															<span class="title">'.esc_html__('Location','bookmify').'</span>
														</label>
														<input type="text" name="location" placeholder="'.esc_attr__('Select from Locations','bookmify').'" readonly value=""  data-placeholder="'.esc_attr__('Select from Locations','bookmify').'" />
														<input type="hidden" name="location_ids" value="">
														<span class="bot_btn"><span></span><img class="bookmify_be_svg" src="'.BOOKMIFY_ASSETS_URL.'img/down.svg" alt="" /></span>
														<div class="bookmify_be_new_value"></div>
														'.self::locationList().'
													</div>
												</div>
												
												<div class="input_wrap_row">
													<div class="input_wrap sort">
														<label>
															<span class="title">'.esc_html__('Sort By','bookmify').'</span>
														</label>
														<input type="text" name="sorting" placeholder="'.esc_attr__('Select from Options','bookmify').'" readonly value=""  data-placeholder="'.esc_attr__('Select from Options','bookmify').'" />
														<input type="hidden" name="sorting_by" value="">
														<span class="bot_btn"><span></span><img class="bookmify_be_svg" src="'.BOOKMIFY_ASSETS_URL.'img/down.svg" alt="" /></span>
														<div class="bookmify_be_new_value"></div>
														'.self::sorting().'
													</div>
												</div>

												
												
											</form>
											
										</div>
										
									</div>
								</div>
								
								'.Helper::bookmifyPopupSaveSection('generate').'

							</div>
						</div>
					</div>
				</div>';
		
		return $html;
	}
}

