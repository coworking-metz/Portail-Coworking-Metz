<?php
namespace Bookmify;

use Bookmify\Helper;


// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {exit; }


/**
 * Class Helper Locations
 */
class HelperLocations
{
	/*
	 * @since 1.0.0
	 * @access private
	*/
	public static function clonableForm(){
		
		$html = '<div class="bookmify_be_popup_form_wrap">
					<div class="bookmify_be_popup_form_position_fixer">
						<div class="bookmify_be_popup_form_bg">
							<div class="bookmify_be_popup_form">

								<div class="bookmify_be_popup_form_header">
									<h3>'.esc_html__('New Location','bookmify').'</h3>
									<span class="closer"></span>
								</div>

								<div class="bookmify_be_popup_form_content">
									<div class="bookmify_be_popup_form_content_in">

										<div class="bookmify_be_popup_form_fields">

											<div class="bookmify_be_form_wrap">
												<div class="left_part">
													<div class="input_wrap input_img">
														<input type="hidden" class="bookmify_be_img_id" name="service_img_id" value="" />
														<div class="bookmify_thumb_wrap">
															<div class="bookmify_thumb_edit">
																<span class="edit"><img class="bookmify_be_svg" src="'.BOOKMIFY_ASSETS_URL.'img/image.svg" alt="" /></span>
															</div>
															<div class="bookmify_thumb_remove"><a href="#" class="bookmify_be_delete" data-entity-id=""><img class="bookmify_be_svg" src="'.BOOKMIFY_ASSETS_URL.'img/delete.svg" alt="" /></a></div>
														</div>
													</div>
												</div>

												<div class="right_part">
													<div class="input_wrap_row">
														<div class="input_wrap name_holder">
															<label><span class="title">'.esc_html__('Name','bookmify').'<span>*</span></span></label>
															<input type="text" class="location_name required_field" placeholder="'.esc_attr__('Location Name','bookmify').'" value="" />
														</div>
														<div class="input_wrap address_holder">
															<label><span class="title">'.esc_html__('Location','bookmify').'<span>*</span></span></label>
															<input type="text" class="location_address required_field" placeholder="'.esc_attr__('Location Address','bookmify').'" value="" />
														</div>
													</div>';


										$html .=    '<div class="input_wrap_row">
														<div class="input_wrap location_employees_holder">
															<label><span class="title">'.esc_html__('Employees','bookmify').'</span></label>
															<div class="bookmify_be_custom_select">
																<input type="text" data-placeholder="'.esc_attr__('Select from Employees','bookmify').'" placeholder="'.esc_attr__('Select from Employees','bookmify').'" readonly />
																<input type="hidden" class="location_employees_ids" value="">
																<span class="bot_btn">
																	<span></span>
																	<img class="bookmify_be_svg" src="'.BOOKMIFY_ASSETS_URL.'img/down.svg" alt="" />
																</span>
																<div class="bookmify_be_new_value"></div>
															</div>
														</div>
													</div>

													<div class="input_wrap_row">
														<div class="input_wrap info_holder">
															<label><span class="title">'.esc_html__('Description','bookmify').'</span></label>
															<textarea class="location_info" placeholder="'.esc_attr__('Some info','bookmify').'"></textarea>
														</div>
													</div>


												</div>
											</div>
											

										</div>

									</div>
								</div>

								<div class="bookmify_be_popup_form_button">
									<a class="save" href="#">
										<span class="text">'.esc_html__('Save','bookmify').'</span>
										<span class="save_process">
											<span class="ball"></span>
											<span class="ball"></span>
											<span class="ball"></span>
										</span>
									</a>
									<a class="cancel" href="#">'.esc_html__('Cancel','bookmify').'</a>
								</div>

							</div>
						</div>
					</div>
				</div>';
		
		return $html;
	}
	
	public static function allNanoInOne(){
		global $wpdb;
		$html  = '<div class="bookmify_be_all_nano location">';
		$html .= self::employeeListNano();
		$html .= '</div>';
		return $html;
	}
	
	public static function employeeListNano(){
		global $wpdb;
		$output 	= '<div class="nano location_employees"><div class="nano-content">';
		$query 		= "SELECT * FROM {$wpdb->prefix}bmify_employees ORDER BY first_name";
		$employees	= $wpdb->get_results( $query, OBJECT  );
		
		
		$output .= '<ul class="employees_list">';
		$output .= '<li>
						<div class="item">
							<span class="bookmify_be_checkbox">
								<input type="checkbox" class="bookmify_be_check_all_items">
								<span class="checkmark">
									<img class="bookmify_be_svg" src="'.BOOKMIFY_ASSETS_URL.'img/checked.svg" alt="" />
								</span>
							</span>
							<span class="name">'.esc_html__('Select All', 'bookmify').'</span>
						</div>
					</li>';
		
		foreach($employees as $employee){
			
			$output .= '<li>
							<div class="item location_employee_item">
								<span class="bookmify_be_checkbox">
									<input type="checkbox" class="bookmify_be_check_item" value="'.$employee->id.'">
									<span class="checkmark">
										<img class="bookmify_be_svg" src="'.BOOKMIFY_ASSETS_URL.'img/checked.svg" alt="" />
									</span>
								</span>
								<span class="name">'.Helper::bookmifyGetEmployeeCol($employee->id).'</span>
							</div>
						</li>';
			
		}
		
		$output .= '</ul>';
		$output .= '</div></div>';
		return $output;
	}
	
	
	/**
	 * @since 1.0.0
	 * @access public
	*/
	public static function locationEmployeesIds($id){
		global $wpdb;
		
		$attached = [];
		
		$id		= esc_sql($id);
		$query 	= "SELECT * FROM {$wpdb->prefix}bmify_employee_locations WHERE location_id=".$id;
		$ids 	= $wpdb->get_results( $query, OBJECT  );
		
		foreach($ids as $id){
			$attached[] = $id->employee_id;
		}
		
		$attached = implode(",", $attached);
		
		return $attached;
	}
	
	/**
	 * @since v1.3.8
	 * @access public
	*/
	public static function getLocationAddressOfEmployee($employeeID){
		global $wpdb;
		$locationAddress 	= '';
		$query 				= "SELECT l.address locationAddress FROM {$wpdb->prefix}bmify_employee_locations el INNER JOIN {$wpdb->prefix}bmify_locations l ON el.location_id = l.id WHERE el.employee_id=".$employeeID;
		$results 			= $wpdb->get_results( $query, OBJECT  );
		
		foreach($results as $result){
			$locationAddress = $result->locationAddress;
		}
		
		return $locationAddress;
	}
	

	/*
	 * @since 1.0.0
	 * @access public
	*/
	public static function locationEmployeesNames($id){
		global $wpdb;
		
		$assigned_employees_ids = [];
		$checked 	= '';
		$output 	= '';
		
		$query 		= "SELECT id FROM {$wpdb->prefix}bmify_employees ORDER BY first_name";
		$employees 	= $wpdb->get_results( $query, OBJECT  );
		
		$id		= esc_sql($id);
		$query 	= "SELECT * FROM {$wpdb->prefix}bmify_employee_locations WHERE location_id=".$id;
		$ids 	= $wpdb->get_results( $query, OBJECT  );
		
		foreach($ids as $id){
			$assigned_employees_ids[] = $id->employee_id;
		}
		
		$count 	= '';
		$key	= 0;
		$myKey 	= 0;
		$ofKey 	= 0;
		
		// experimental types: +3 or 3/4
		$type = 'of'; // plus
		foreach($employees as $employee){
			$employeeID = $employee->id;
			$ofKey++;
			
			if(in_array( $employeeID, $assigned_employees_ids )){$checked = 'checked';}
			
			if($checked == 'checked'){
				$key++;
				if($key == 1){
					$output = '<span class="text">'.Helper::bookmifyGetEmployeeCol($employeeID).'</span>';
				}else{
					$myKey++;
				}
			}
			$checked = '';
		}
		if($output == ''){
			$output = '';
		}else{
			if($myKey > 0){
				if($type == 'of'){
					$output .= '<span class="number">'.($myKey+1).' / '.($ofKey).'</span>';
				}else{
					$output .= '<span class="number">+'.($myKey).'</span>';
				}
				
			}
		}
			
		return $output;
	}
}

