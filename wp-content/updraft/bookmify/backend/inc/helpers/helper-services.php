<?php
namespace Bookmify;


use Bookmify\Helper;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {exit; }


/**
 * Class Helper Services
 */
class HelperServices
{
	/*
	 * @since 1.0.0
	 * @access private
	*/
	public static function clonableFormToAddExtra(){	
		
		$html = '<li class="extra_item bookmify_be_list_item bookmify_be_clone_extra">
					<div class="bookmify_be_list_item_in">
						<div class="extra_heading bookmify_be_list_item_header">
							<div class="e_heading_in header_in">
								<div class="img_holder"></div>
								<div class="extra_info">
									<span class="extra_title">
										<span class="e_top"></span>
										<span class="e_bottom">
											<span class="e_price">'.Helper::bookmifyPriceCorrection(0).'</span> / 
											<span class="e_duration"></span>
										</span>
									</span>
									<span class="extra_price">'.Helper::bookmifyPriceCorrection(0).'</span>
									<span class="extra_duration"></span>
								</div>
								<div class="buttons_holder">
									<div class="btn_item btn_edit">
										<a href="#" class="bookmify_be_edit"><img class="bookmify_be_svg edit" src="'.BOOKMIFY_ASSETS_URL.'img/edit.svg" alt="" /><img class="bookmify_be_svg checked" src="'.BOOKMIFY_ASSETS_URL.'img/checked.svg" alt="" /></a>
									</div>
									<div class="btn_item">
										<a href="#" class="bookmify_be_delete" data-entity-id=""><img class="bookmify_be_svg" src="'.BOOKMIFY_ASSETS_URL.'img/delete.svg" alt="" /></a>
									</div>
									<div class="btn_item">
										<span class="bookmify_drag_handle">
											<span class="a"></span>
											<span class="b"></span>
											<span class="c"></span>
										</span>
									</div>
								</div>
							</div>
						</div>

						<div class="extra_content">
							<div class="extra_content_in">
								<div class="extra_left">
									<div class="img_wrap">
										<input type="hidden" name="extra_img_id" value="" />
										<div class="extra_thumb_wrap">
											<div class="extra_thumb_edit">
												<img class="bookmify_be_svg" src="'.BOOKMIFY_ASSETS_URL.'img/image.svg" alt="" />
											</div>
											<div class="extra_thumb_remove"><a href="#" class="bookmify_be_delete"><img class="bookmify_be_svg" src="'.BOOKMIFY_ASSETS_URL.'img/delete.svg" alt="" /></a></div>
										</div>
									</div>
								</div>


								<div class="extra_right">
									<div class="name_price">
										<div class="title_holder">
											<label><span class="title">'.esc_html__('Name','bookmify').'<span>*</span></span></label>
											<input class="required_field" type="text" name="extra_title" placeholder="'.esc_attr__('Extra Name','bookmify').'" value="" />
										</div>
										<div class="price_holder">
											<label><span class="title">'.esc_html__('Price','bookmify').'<span>*</span></span></label>
											<input class="required_field" type="number" step="0.01" name="extra_price" value="0" />
										</div>
									</div>
									<div class="duration_capacity">
										<div class="duration">
											<label><span class="title">'.esc_html__('Duration','bookmify').'</span></label>
											<input type="text" name="extra_duration" placeholder="'.esc_attr__('Duration','bookmify').'" readonly value="" />
											<input type="hidden" name="extra_duration_sec" value="">
											<span class="bot_btn"><span></span><img class="bookmify_be_svg" src="'.BOOKMIFY_ASSETS_URL.'img/down.svg" alt="" /></a></span>
										</div>
										<div class="max_cap">
											<label>
												<span class="title">'.esc_html__('Max Capacity','bookmify').'
													<div class="f_tooltip">
														<span>?</span>
														<div class="f_tooltip_content">'.esc_html__('The maximum amount of this Extra that a customer can use.', 'bookmify').'
														</div>
													</div>
												</span>
											</label>
											<div class="bookmify_be_quantity">
												<input type="number" min="1" name="extra_max_cap" value="1" />
												<span class="increase"><span></span></span>
												<span class="decrease"><span></span></span>
											</div>
										</div>
									</div>
									<div class="info_holder">
										<label><span class="title">'.esc_html__('Description','bookmify').'</span></label>
										<textarea placeholder="'.esc_attr__('Some info for internal usage','bookmify').'" name="extra_info"></textarea>
									</div>
								</div>
							</div>
						</div>
					</div>
				</li>';
		
		return $html;
	}
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
									<h3>'.esc_html__('New Service','bookmify').'</h3>
									<span class="closer"></span>
								</div>

								<div class="bookmify_be_popup_form_content">
									<div class="bookmify_be_popup_form_content_in">

										<div class="bookmify_be_popup_form_fields">


												<div class="bookmify_be_tabs_wrap_service_edit bookmify_be_tab_wrap">
													<form class="bookmify_be_main_form service_update" autocomplete="off">
														<div class="service_edit_tab_wrapper bookmify_be_link_tabs">
															<ul>
																<li class="active">
																	<a class="bookmify_be_tab_link" href="#">'.esc_html__('Details', 'bookmify').'</a>
																</li>
																<li style="display:none;">
																	<a class="bookmify_be_tab_link" href="#">'.esc_html__('Gallery', 'bookmify').'</a>
																</li>
																<li>
																	<a class="bookmify_be_tab_link" href="#">'.esc_html__('Extras', 'bookmify').'</a>
																</li>
															</ul>
														</div>
														<div class="bookmify_be_tabs_content_service_edit bookmify_be_content_tabs">
															<div class="active bookmify_be_tab_pane">
																<div class="bookmify_be_service_edit_detail">
																	<div class="left_part">
																		<div class="input_wrap input_img">
																			<input type="hidden" class="bookmify_be_img_id" name="service_img_id" value="" />
																			<div class="bookmify_thumb_wrap">
																				<div class="bookmify_thumb_edit">
																					<span class="edit"><img class="bookmify_be_svg" src="'.BOOKMIFY_ASSETS_URL.'img/image.svg" alt="" /></span>
																				</div>
																				<div class="bookmify_thumb_remove"><a href="#" class="bookmify_be_delete"><img class="bookmify_be_svg" src="'.BOOKMIFY_ASSETS_URL.'img/delete.svg" alt="" /></a></div>
																			</div>
																		</div>
																		<div class="color_visible">
																			<div class="choose_color">
																				<div class="input_holder"><input id="service_color" type="text" name="service_color" class="bookmify_color_picker" value="" /></div>
																				<label class="select_color" for="service_color">'.esc_html__('Select Color','bookmify').'</label>
																			</div>
																			<div class="visibility">
																				<label class="switch">
																					<input type="checkbox" id="repeat_1_" value="1" name="service_visibility" checked="checked" />
																					<span class="slider round"></span>
																				</label>
																				<label class="repeater" for="repeat_1_">'.esc_html__('Visible to Public','bookmify').'</label>
																			</div>
																		</div>
																	</div>
																	<div class="right_part">
																		<div class="name_price">
																			<div class="title_holder">
																				<label><span class="title">'.esc_html__('Name','bookmify').'<span>*</span></span></label>
																				<input class="required_field" type="text" name="service_title" placeholder="'.esc_attr__('Service Name','bookmify').'" value="" />
																			</div>
																			<div class="price_holder">
																				<label><span class="title">'.esc_html__('Price','bookmify').'<span>*</span></span></label>
																				<input class="required_field" type="number" step="0.01" name="service_price" placeholder="'.esc_attr__('Service Price','bookmify').'" value="" />
																			</div>
																		</div>
																		<div class="category_employees">
																			<div class="category_holder">
																				<label><span class="title">'.esc_html__('Category','bookmify').'<span>*</span></span></label>
																				<input class="required_field" type="text" name="service_category" placeholder="'.esc_attr__('Select from Categories','bookmify').'" readonly value="">
																				<input type="hidden" name="service_category_id" value="">
																				<span class="bot_btn"><span></span><img class="bookmify_be_svg" src="'.BOOKMIFY_ASSETS_URL.'img/down.svg" alt="" /></span>
																			</div>
																			<div class="provider_holder">
																				<label><span class="title">'.esc_html__('Employees','bookmify').'</span></label>
																				<input type="text" name="service_provider" data-placeholder="'.esc_attr__('Select from Employees','bookmify').'" placeholder="'.esc_attr__('Select from Employees','bookmify').'" readonly />
																				<input type="hidden" name="service_provider_ids" value="">
																				<span class="bot_btn"><span></span><img class="bookmify_be_svg" src="'.BOOKMIFY_ASSETS_URL.'img/down.svg" alt="" /></span>
																				<div class="bookmify_be_new_value"></div>
																			</div>
																		</div>
																		<div class="duration_buffer">
																			<div class="duration">
																				<label><span class="title">'.esc_html__('Duration','bookmify').'<span>*</span></span></label>
																				<input class="required_field" type="text" name="service_duration" placeholder="'.esc_attr__('Duration','bookmify').'" readonly value="" />
																				<input type="hidden" name="service_duration_sec" value="">
																				<span class="bot_btn"><span></span><img class="bookmify_be_svg" src="'.BOOKMIFY_ASSETS_URL.'img/down.svg" alt="" /></span>
																			</div>
																			<div class="buffer_before">
																				<label>
																					<span class="title">'.esc_html__('Buffer Time Before','bookmify').'
																						<div class="f_tooltip">
																							<span>?</span>
																							<div class="f_tooltip_content">'.esc_html__('Time before the start of the appointment with this service. When creating an appointment with this service, this time will be taken into account.', 'bookmify').'
																							</div>
																						</div>
																					</span>
																				</label>
																				<input type="text" name="service_buffer_before" placeholder="'.esc_attr__('Buffer Time Before','bookmify').'" readonly value="">
																				<input type="hidden" name="service_bb_sec" value="">
																				<span class="bot_btn"><span></span><img class="bookmify_be_svg" src="'.BOOKMIFY_ASSETS_URL.'img/down.svg" alt="" /></span>
																			</div>
																			<div class="buffer_after">
																				<label for="service_buffer_after">
																					<span class="title">'.esc_html__('Buffer Time After','bookmify').'
																						<div class="f_tooltip">
																							<span>?</span>
																							<div class="f_tooltip_content">'.esc_html__('Time after the end of the appointment with this service. When creating an appointment with this service, this time will be taken into account.', 'bookmify').'
																							</div>
																						</div>
																					</span>
																				</label>
																				<input id="service_buffer_after" type="text" name="service_buffer_after" placeholder="'.esc_html__('Buffer Time After','bookmify').'" readonly value="">
																				<input type="hidden" name="service_ba_sec" value="">
																				<span class="bot_btn"><span></span><img class="bookmify_be_svg" src="'.BOOKMIFY_ASSETS_URL.'img/down.svg" alt="" /></span>
																			</div>
																		</div>
																		<div class="min_max_capacity">
																			<div class="min_cap">
																				<label>
																					<span class="title">'.esc_html__('Min Capacity','bookmify').'
																						<div class="f_tooltip">
																							<span>?</span>
																							<div class="f_tooltip_content">'.esc_html__('The minimum number of people who can come to an appointment with this service.', 'bookmify').'
																							</div>
																						</div>
																					</span>
																				</label>
																				<div class="bookmify_be_quantity">
																					<input type="number" min="1" name="service_min_cap" value="1" />
																					<span class="increase"><span></span></span>
																					<span class="decrease"><span></span></span>
																				</div>
																			</div>
																			<div class="max_cap">
																				<label>
																					<span class="title">'.esc_html__('Max Capacity','bookmify').'
																						<div class="f_tooltip">
																							<span>?</span>
																							<div class="f_tooltip_content">'.esc_html__('The maximum number of people who can come to an appointment with this service.', 'bookmify').'
																							</div>
																						</div>
																					</span>
																				</label>
																				<div class="bookmify_be_quantity">
																					<input type="number" min="1" name="service_max_cap" value="1" />
																					<span class="increase"><span></span></span>
																					<span class="decrease"><span></span></span>
																				</div>
																			</div>
																			<div class="tax_holder">
																				<label><span class="title">'.esc_html__('Taxes','bookmify').'</span></label>
																				<input type="text" name="service_tax" data-placeholder="'.esc_attr__('Select from Taxes','bookmify').'" placeholder="'.esc_attr__('Select from Taxes','bookmify').'" readonly />
																				<input type="hidden" name="service_tax_ids" value="">
																				<span class="bot_btn"><span></span><img class="bookmify_be_svg" src="'.BOOKMIFY_ASSETS_URL.'img/down.svg" alt="" /></span>
																				<div class="bookmify_be_new_value"></div>
																			</div>
																		</div>
																		<div class="info_holder">
																			<label for="service_info"><span class="title">'.esc_html__('Description','bookmify').'</span></label>
																			<textarea placeholder="'.esc_attr__('Some info for internal usage','bookmify').'" id="service_info" name="service_info"></textarea>
																		</div>
																	</div>
																</div>
																
															</div>
															<div class="bookmify_be_tab_pane">
																'.self::bookmifyGalleryListServiceByID('').'
															</div>
															<div class="bookmify_be_tab_pane">
																<div class="bookmify_be_extra_service_edit">
																	<div class="add_extra_button">
																		<a href="#" class="bookmify_add_new_button">
																			<span class="text">'.esc_html__('Add Extra','bookmify').'</span>
																			<span class="plus"><span class="icon"></span></span>
																		</a>
																	</div>
																	'.self::bookmifyExtraListServiceByID('').'
																	
																</div>
															</div>
														</div>
													</form>


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
	/**
	 * @since 1.0.0
	 * @access public
	*/
	public static function employeeAsNewValue($service_id){
		global $wpdb;
		
		$output 	= '';
		
		$assigned_employees_ids = array();
		$checked	= '';
		
		$query 		= "SELECT * FROM {$wpdb->prefix}bmify_employees ORDER BY position, id";
		$employees 	= $wpdb->get_results( $query, OBJECT  );
		
		$service_id	= esc_sql($service_id);
		$query 		= "SELECT employee_id FROM {$wpdb->prefix}bmify_employee_services WHERE service_id=".$service_id;
		$group 		= $wpdb->get_results( $query, OBJECT  );
		
		foreach($group as $employee){
			$assigned_employees_ids[] = $employee->employee_id;
		}
		$count 		= '';
		$key 		= 0;
		$myKey 		= 0;
		$ofKey 		= 0;
		
		// experimental types: +3 or 3/4
		$type 		= 'of'; // plus
		foreach($employees as $employee){
			$ofKey++;
			if(in_array( $employee->id, $assigned_employees_ids )){$checked = 'checked';}
			
			if($checked == 'checked'){
				$key++;
				if($key == 1){
					$output .= '<span class="text">'.$employee->first_name.' '.$employee->last_name."</span>";
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
	/**
	 * @since 1.0.2
	 * @access public
	*/
	public static function taxAsNewValue($service_id){
		global $wpdb;
		
		$output 	= '';
		
		$assigned_employees_ids = array();
		$checked	= '';
		
		$query 		= "SELECT * FROM {$wpdb->prefix}bmify_taxes ORDER BY title, id";
		$employees 	= $wpdb->get_results( $query, OBJECT  );
		
		$service_id	= esc_sql($service_id);
		$query 		= "SELECT tax_id FROM {$wpdb->prefix}bmify_services_taxes WHERE service_id=".$service_id;
		$group 		= $wpdb->get_results( $query, OBJECT  );
		
		foreach($group as $employee){
			$assigned_employees_ids[] = $employee->tax_id;
		}
		$count 		= '';
		$key 		= 0;
		$myKey 		= 0;
		$ofKey 		= 0;
		
		// experimental types: +3 or 3/4
		$type 		= 'of'; // plus
		foreach($employees as $employee){
			$ofKey++;
			if(in_array( $employee->id, $assigned_employees_ids )){$checked = 'checked';}
			
			if($checked == 'checked'){
				$key++;
				if($key == 1){
					$output .= '<span class="text">'.$employee->title.' ('.$employee->rate.")</span>";
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
	/**
	 * @since 1.0.0
	 * @access protected
	*/
	public static function bookmifyGalleryListServiceByID($serviceID){
		global $wpdb;
		$html = '';
		$result = '';
		if($serviceID == NULL || $serviceID == ''){
			// do nothing
		}else{
			$serviceID	= esc_sql($serviceID);
			$query 		= "SELECT * FROM {$wpdb->prefix}bmify_services WHERE id=".$serviceID;
			$results	= $wpdb->get_results( $query);
			foreach($results as $service){
				$result = $service->gallery_ids;
			}
		}
		$html .= '<input type="hidden" name="gallery_ids" value="'.$result.'" />';
		$html .= '<ul class="service_gallery_list">';
		if(!empty($result)){
			$attIDs = explode(",",$result);
			foreach($attIDs as $attID){
				$url = wp_get_attachment_image_src($attID, 'large');
				$html .= '<li class="drag_handle" data-img-id="'.$attID.'">
					<div class="item li_item">
						<div class="thumb_wrap" style="background-image:url('.$url[0].')">
							<div class="thumb_remove"><a href="#" class="bookmify_be_delete" data-entity-id="'.$serviceID.'"><img class="bookmify_be_svg" src="'.BOOKMIFY_ASSETS_URL.'img/delete.svg" alt="" /></a></div>
						</div>
					</div>
				</li>';
			}
		}
		$html .= '<li class="add_images"><div class="item"><span class="plus"></span><span class="text">'.esc_html__('Add Image','bookmify').'</span></div></li>';
		$html .= '</ul>';
			
		return $html;
	}
	/**
	 * @since 1.0.0
	 * @access public
	*/
	public static function categoryListNano(){
		global $wpdb;
		
		
		$query 		 = "SELECT * FROM {$wpdb->prefix}bmify_categories ORDER BY position, id";
		$cats 		 = $wpdb->get_results( $query, OBJECT  );
		$output 	 = '<div class="nano service_categories"><div class="nano-content">';
		foreach($cats as $cat){
			$output .= '<div data-id="'.$cat->id.'">'.$cat->title.'</div>';
		}
		$output     .= '</div></div>';
		return $output;
	}
	/**
	 * @since 1.0.0
	 * @access public
	*/
	public static function durationAndBufferNano($class = NULL){
		global $wpdb;
		
		$sixty 			= 60;
		$timeInterInMin	= get_option( 'bookmify_be_time_interval', '15' ) * $sixty;
		$hours			= 24;
		$countInterval	= ($hours*$sixty*$sixty) / $timeInterInMin;
		$timeDiv		= '<div class="nano buffer_time '.$class.'"><div class="nano-content">';
		for($i = 1; $i < $countInterval+1; $i++){
			$timeDiv .= '<div data-sec="'.$i*$timeInterInMin.'">'.Helper::bookmifyNumberToDuration($i*$timeInterInMin).'</div>';
		}
		$timeDiv .= '</div></div>';
		return $timeDiv;
	}
	/**
	 * @since 1.0.0
	 * @access public
	*/
	public static function employeeListNano(){
		global $wpdb;
		
		$output = '<div class="nano service_employees"><div class="nano-content">';
		$output .= self::employeeListForNano();
		$output .= '</div></div>';
		return $output;
	}
	/**
	 * @since 1.0.2
	 * @access public
	*/
	public static function taxListNano(){
		global $wpdb;
		
		$output = '<div class="nano service_taxes"><div class="nano-content">';
		$output .= self::taxListForNano();
		$output .= '</div></div>';
		return $output;
	}
	/**
	 * @since 1.0.0
	 * @access public
	*/
	public static function employeeListForNano(){
		global $wpdb;
		$checked 	= '';
		$output 	= '';
		
		$query 		= "SELECT * FROM {$wpdb->prefix}bmify_employees ORDER BY first_name, last_name, id";
		$employees 	= $wpdb->get_results( $query, OBJECT  );

		$output = '<ul class="employees_nano_list">';
		$output .= '<li>
						<div class="item">
							<label>
								<input type="checkbox" class="bookmify_be_check_all_items">
								<span class="checkmark">
									<img class="bookmify_be_svg" src="'.BOOKMIFY_ASSETS_URL.'img/checked.svg" alt="" />
								</span>
							</label>
							<span class="name">'.esc_html__('Select All', 'bookmify').'</span>
						</div>
					</li>';

		foreach($employees as $provider){

			$output .= '<li>
							<div class="item">
								<label>
									<input type="checkbox" name="employee_id" class="bookmify_be_check_item" value="'.$provider->id.'">
									<span class="checkmark">
										<img class="bookmify_be_svg" src="'.BOOKMIFY_ASSETS_URL.'img/checked.svg" alt="" />
									</span>
								</label>
								<span class="name">'.$provider->first_name.' '.$provider->last_name.'</span>
							</div>
						</li>';
			$checked = '';
		}

		$output .= '</ul>';
		
		
		return $output;
	}
	/**
	 * @since 1.0.2
	 * @access public
	*/
	public static function taxListForNano(){
		global $wpdb;
		$checked 	= '';
		$output 	= '';
		
		$query 		= "SELECT * FROM {$wpdb->prefix}bmify_taxes ORDER BY title,id";
		$taxes	 	= $wpdb->get_results( $query, OBJECT  );

		$output = '<ul class="taxes_nano_list">';
		$output .= '<li>
						<div class="item">
							<label>
								<input type="checkbox" class="bookmify_be_check_all_items">
								<span class="checkmark">
									<img class="bookmify_be_svg" src="'.BOOKMIFY_ASSETS_URL.'img/checked.svg" alt="" />
								</span>
							</label>
							<span class="name">'.esc_html__('Select All', 'bookmify').'</span>
						</div>
					</li>';

		foreach($taxes as $tax){

			$output .= '<li>
							<div class="item">
								<label>
									<input type="checkbox" name="tax_id" class="bookmify_be_check_item" value="'.$tax->id.'">
									<span class="checkmark">
										<img class="bookmify_be_svg" src="'.BOOKMIFY_ASSETS_URL.'img/checked.svg" alt="" />
									</span>
								</label>
								<span class="name">'.$tax->title.' ('.$tax->rate.'%)</span>
							</div>
						</li>';
			$checked = '';
		}

		$output .= '</ul>';
		
		
		return $output;
	}
	
	public static function allNanoInOne(){
		global $wpdb;
		$html  = '<div class="bookmify_be_all_nano service">';
		$html .= self::durationAndBufferNano('extra_duration');
		$html .= self::durationAndBufferNano('duration');
		$html .= self::durationAndBufferNano('buffer_before');
		$html .= self::durationAndBufferNano('buffer_after');
		$html .= self::categoryListNano();
		$html .= self::employeeListNano();
		$html .= self::taxListNano();
		$html .= '</div>';
		return $html;
	}
	/**
	 * @since 1.0.0
	 * @access protected
	*/
	public static function bookmifyExtraListServiceByID($serviceID){
		global $wpdb;
		$html = '';
		$list	= '';
		if($serviceID == NULL || $serviceID == ''){
			$html .= '<ul class="extra_service_list">';
		}else{
			$serviceID	= esc_sql($serviceID);
			$query 		= "SELECT * FROM {$wpdb->prefix}bmify_extra_services WHERE service_id=".$serviceID." ORDER BY position, id";
			$results 	= $wpdb->get_results( $query);
			foreach($results as $extra){
				$price 					= Helper::bookmifyPriceCorrection($extra->price);
				$duration 				= Helper::bookmifyNumberToDuration($extra->duration);
				$attachment_url 		= Helper::bookmifyGetImageByID($extra->attachment_id);
				$attachment_url_large 	= Helper::bookmifyGetImageByID($extra->attachment_id, 'large');
				if($attachment_url != ''){$opened = 'has_image';}else{$opened = '';}
				$list .= '<li class="extra_item bookmify_be_list_item" data-extra-id="'.$extra->id.'">
								<div class="bookmify_be_list_item_in">
									<div class="extra_heading bookmify_be_list_item_header">
										<div class="e_heading_in header_in">
											<div class="img_holder" style="background-image: url('.$attachment_url.');"></div>
											<div class="extra_info">
												<span class="extra_title">
													<span class="e_top">'.$extra->title.'</span>
													<span class="e_bottom">
														<span class="e_price">'.$price.'</span> / 
														<span class="e_duration">'.$duration.'</span>
													</span>
												</span>
												<span class="extra_price">'.$price.'</span>
												<span class="extra_duration">'.$duration.'</span>
											</div>
											<div class="buttons_holder">
												<div class="btn_item btn_edit">
													<a href="#" class="bookmify_be_edit"><img class="bookmify_be_svg edit" src="'.BOOKMIFY_ASSETS_URL.'img/edit.svg" alt="" /><img class="bookmify_be_svg checked" src="'.BOOKMIFY_ASSETS_URL.'img/checked.svg" alt="" /></a>
												</div>
												<div class="btn_item">
													<a href="#" class="bookmify_be_delete" data-entity-id="'.$extra->id.'"><img class="bookmify_be_svg" src="'.BOOKMIFY_ASSETS_URL.'img/delete.svg" alt="" /></a>
												</div>
												<div class="btn_item">
													<span class="bookmify_drag_handle">
														<span class="a"></span>
														<span class="b"></span>
														<span class="c"></span>
													</span>
												</div>
											</div>
										</div>
									</div>

									<div class="extra_content">
										<div class="extra_content_in">
											<div class="extra_left">
												<div class="img_wrap">
													<input type="hidden" name="extra_img_id" value="'.$extra->attachment_id.'" />
													<div class="extra_thumb_wrap '.$opened.'" style="background-image:url('.$attachment_url_large.')">
														<div class="extra_thumb_edit">
															<img class="bookmify_be_svg" src="'.BOOKMIFY_ASSETS_URL.'img/image.svg" alt="" />
														</div>
														<div class="extra_thumb_remove '.$opened.'"><a href="#" class="bookmify_be_delete" data-entity-id="'.$extra->id.'"><img class="bookmify_be_svg" src="'.BOOKMIFY_ASSETS_URL.'img/delete.svg" alt="" /></a></div>
													</div>
												</div>
											</div>


											<div class="extra_right">
												<div class="name_price">
													<div class="title_holder">
														<label><span class="title">'.esc_html__('Name','bookmify').'<span>*</span></span></label>
														<input class="required_field" type="text" name="extra_title" placeholder="'.esc_attr__('Extra Name','bookmify').'" value="'.$extra->title.'" />
													</div>
													<div class="price_holder">
														<label><span class="title">'.esc_html__('Price','bookmify').'<span>*</span></span></label>
														<input class="required_field" type="number" step="0.01" name="extra_price" value="'.$extra->price.'" />
													</div>
												</div>
												<div class="duration_capacity">
													<div class="duration">
														<label><span class="title">'.esc_html__('Duration','bookmify').'</span></label>
														<input type="text" name="extra_duration" placeholder="'.esc_attr__('Duration','bookmify').'" readonly value="'.$duration.'" />
														<input type="hidden" name="extra_duration_sec" value="'.$extra->duration.'">
														<span class="bot_btn"><span></span><img class="bookmify_be_svg" src="'.BOOKMIFY_ASSETS_URL.'img/down.svg" alt="" /></a></span>
													</div>
													<div class="max_cap">
														<label>
															<span class="title">'.esc_html__('Max Capacity','bookmify').'
																<div class="f_tooltip">
																	<span>?</span>
																	<div class="f_tooltip_content">'.esc_html__('The maximum amount of this Extra that a customer can use.', 'bookmify').'
																	</div>
																</div>
															</span>
														</label>
														<div class="bookmify_be_quantity">
															<input type="number" min="1" name="extra_max_cap" value="'.$extra->capacity_max.'" />
															<span class="increase"><span></span></span>
															<span class="decrease"><span></span></span>
														</div>
													</div>
												</div>
												<div class="info_holder">
													<label><span class="title">'.esc_html__('Description','bookmify').'</span></label>
													<textarea placeholder="'.esc_attr__('Some info for internal usage','bookmify').'" name="extra_info">'.$extra->info.'</textarea>
												</div>
											</div>
										</div>
									</div>
								</div>
							</li>';
			}
			if(!empty($results)){
				$html .= '<ul class="extra_service_list">';
			}else{
				$html .= '<ul class="extra_service_list">';
			}
			$html .= $list;
		}
		
		$html .= '</ul>';
		return $html;
	}
	/**
     * Get Service Col.
	 * @since 1.0.0
     */
    public static function employeeIDsByServiceID( $id = NULL )
    {
        global $wpdb;
		$result = '';
		
		if($id == NULL || $id == ''){
			
		}else{
			$id			= esc_sql($id);
			$query 		= "SELECT employee_id FROM {$wpdb->prefix}bmify_employee_services WHERE service_id=".$id;
			$results 	= $wpdb->get_results( $query);
			foreach($results as $service){
				$result .= $service->employee_id;
				$result .= ',';
			}
			$result = rtrim($result,",");
		}
		return $result;
    }
	/**
     * Get Service Col.
	 * @since 1.0.2
     */
    public static function taxIDsByServiceID( $id = NULL )
    {
        global $wpdb;
		$result = '';
		
		if($id == NULL || $id == ''){
			
		}else{
			$id			= esc_sql($id);
			$query 		= "SELECT tax_id FROM {$wpdb->prefix}bmify_services_taxes WHERE service_id=".$id;
			$results 	= $wpdb->get_results( $query);
			foreach($results as $service){
				$result .= $service->tax_id;
				$result .= ',';
			}
			$result = rtrim($result,",");
		}
		return $result;
    }
	/**
     * Get Category Name by ID.
	 * @since 1.0.0
     */
    public static function categoryIDToName( $id = NULL )
    {
        global $wpdb;
		$result = '';
		if($id == NULL || $id == ''){
			
		}else{
			$id			= esc_sql($id);
			$query 		= "SELECT title FROM {$wpdb->prefix}bmify_categories WHERE id=".$id;
			$results 	= $wpdb->get_results( $query);
			foreach($results as $service){
				$result = $service->title;
			}
		}
			
		
		return $result;
    }
}

