<?php
namespace Bookmify;



// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {exit; }


/**
 * Class Helper Customfields
 */
class HelperCustomfields
{
	
	/**
	 * @since 1.0.0
	 * @access public
	*/
	public static function defaultCFOptions(){
		$options = array(
			'label' 	=> esc_html__('Do you like bookmify?', 'bookmify'),
			'value' 	=> array(
				array( 'label'  => esc_html__( 'Yes, of course', 'bookmify' )),
				array( 'label'  => esc_html__( 'Not much', 'bookmify' )),
			),
		);
		return $options;
	}
	public static function clonableForm(){
		$html = '';
		
		$default_options 	= self::defaultCFOptions();
		
		$label 				= $default_options['label'];
		$values 			= $default_options['value'];
		
		
		$label1 			= '<span class="label_options">'.esc_html__('Label & Options', 'bookmify').'</span>';
		$label2 			= '<span class="label_only">'.esc_html__('Label', 'bookmify').'</span>';
		
		$newHeading1		= '<span class="heading_checkbox">'.esc_html__('New Checkbox', 'bookmify').'</span>';
		$newHeading2		= '<span class="heading_radiobuttons">'.esc_html__('New Radio Buttons', 'bookmify').'</span>';
		$newHeading3		= '<span class="heading_selectbox">'.esc_html__('New Selectbox', 'bookmify').'</span>';
		$newHeading4		= '<span class="heading_text">'.esc_html__('New Text', 'bookmify').'</span>';
		$newHeading5		= '<span class="heading_textarea">'.esc_html__('New Textarea', 'bookmify').'</span>';
		$newHeading6		= '<span class="heading_textcontent">'.esc_html__('New Text Content', 'bookmify').'</span>';

		$servicesIds 		= self::getServicesIdsForNewCF();
		$attached			= self::getServicesNamesForNewCF();
		// FIELD OPTIONS
		$output = '';
		if(!empty($values)){
			foreach($values as $value){
				$output .= '<div class="bookmify_be_options_list_item option_wrap">
								<div class="label_wrap">
									<input type="text" value="'.$value['label'].'">
								</div>
								<div class="buttons_holder">
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
							</div>';
			}
		}

		$html .= '<div class="bookmify_be_popup_form_wrap" data-entity-id="" data-entity-type="">

					<div class="bookmify_be_popup_form_position_fixer">
						<div class="bookmify_be_popup_form_bg">
							<div class="bookmify_be_popup_form">

								<div class="bookmify_be_popup_form_header">
									<h3>'.$newHeading1.$newHeading2.$newHeading3.$newHeading4.$newHeading5.$newHeading6.'</h3>
									<span class="closer"></span>
								</div>

								<div class="bookmify_be_popup_form_content">
									<div class="bookmify_be_popup_form_content_in">

										<div class="bookmify_be_popup_form_fields">

											<div class="cf_content_left">
												<div class="label"><label>'.$label1.$label2.'</label></div>
												<div class="top">
													<div class="label_wrap textarea">
														<textarea class="cf_label required_field">'.$label.'</textarea>
													</div>
													<div class="label_wrap text">
														<input type="text" class="cf_label required_field" value="'.$label.'">
													</div>
													<div class="required_wrap">
														<span class="bookmify_be_checkbox protip" data-pt-target="true" data-pt-title="'. esc_attr__('Required field', 'bookmify').'" data-pt-gravity="right 4 0">
															<input class="req" type="checkbox" checked>
															<span class="checkmark">
																<img class="bookmify_be_svg" src="'.BOOKMIFY_ASSETS_URL.'/img/checked.svg" alt="" />
															</span>
														</span>
													</div>
												</div>
												<div class="bottom">
													<div class="bookmify_be_options_list" data-cf-id="">
														'.$output.'
													</div>
													<div class="bookmify_be_add_new_text_button cf_add_option">
														<a href="#"><span></span>'.esc_html__('Add Option', 'bookmify').'</a>
													</div>
												</div>
											</div>
											<div class="cf_content_right">
												<div class="cf_services_holder">
													<div class="label"><label>'.esc_html__('Attached Services', 'bookmify').'</label></div>
													<div class="bookmify_be_custom_select">
														<input type="text" data-placeholder="'.esc_attr__('Attach Services','bookmify').'" placeholder="" readonly />
														<input type="hidden" class="cf_services_ids" value="'.$servicesIds.'">
														<span class="bot_btn">
															<span></span>
															<img class="bookmify_be_svg" src="'.BOOKMIFY_ASSETS_URL.'img/down.svg" alt="" />
														</span>
														<div class="bookmify_be_new_value">'.$attached.'</div>
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
	
	public static function getServicesIdsForNewCF(){
		global $wpdb;
		$query 		= "SELECT id FROM {$wpdb->prefix}bmify_services ORDER BY title";
		$results 	= $wpdb->get_results( $query, OBJECT  );
		$array 		= array();
		foreach($results as $result){
			$array[] = $result->id;
		}
		$html 		= implode(',', $array);
		return $html;
	}
	/**
	 * @since 1.0.0
	 * @access public
	*/
	public static function getServicesNamesForNewCF(){
		global $wpdb;
		
		$output = '';
		
		$query 		= "SELECT title FROM {$wpdb->prefix}bmify_services ORDER BY title";
		$services 	= $wpdb->get_results( $query, OBJECT  );
		
		
		
		$key = 0;
		$myKey = 0;
		$ofKey = 0;
		
		// experimental types: +3 or 3/4
		$type = 'of'; // plus
		foreach($services as $service){
			$ofKey++;
			
			$key++;
			if($key == 1){
				$output = '<span class="text">'.$service->title.'</span>';
			}else{
				$myKey++;
			}
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
	public static function allNanoInOne(){
		global $wpdb;
		$html  = '<div class="bookmify_be_all_nano customfield">';
		$html .= self::serviceListAsNano();
		$html .= '</div>';
		return $html;
	}
	/**
	 * @since 1.0.0
	 * @access public
	*/
	public static function serviceListAsNano(){
		global $wpdb;
		
		$output = '<div class="nano cf_services"><div class="nano-content">';
		$output .= self::serviceList();
		$output .= '</div></div>';
		return $output;
	}
	public static function serviceList(){
		global $wpdb;

		$query 		= "SELECT * FROM {$wpdb->prefix}bmify_services ORDER BY title";
		$services 	= $wpdb->get_results( $query, OBJECT  );
		
		$output = '<ul class="services_list">';
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
		
		foreach($services as $service){
			$output .= '<li>
							<div class="item cf_service_item">
								<span class="bookmify_be_checkbox">
									<input type="checkbox" class="bookmify_be_check_item" value="'.$service->id.'">
									<span class="checkmark">
										<img class="bookmify_be_svg" src="'.BOOKMIFY_ASSETS_URL.'img/checked.svg" alt="" />
									</span>
								</span>
								<span class="name">'.$service->title.'</span>
							</div>
						</li>';
			
		}
		
		$output .= '</ul>';
		return $output;
	}
	
	public static function clonableOption(){
		$html  = '<div class="clonable_option">
						<div class="bookmify_be_options_list_item option_wrap">
							<div class="label_wrap">
								<input type="text" value="">
							</div>
							<div class="buttons_holder">
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
					</div>';
		return $html;
	}
	
	

	/**
	 * @since 1.0.0
	 * @access public
	*/
	public static function cfServicesIDs($cf_id){
		global $wpdb;
		
		$attached 	= '';
		
		$cfs 		= $wpdb->get_results( $wpdb->prepare("SELECT * FROM {$wpdb->prefix}bmify_customfields WHERE id=%d", $cf_id), OBJECT  );
		
		foreach($cfs as $cf){
			$attached = $cf->services_ids;
		}

		return $attached;
	}
	
	
	/**
	 * @since 1.0.0
	 * @access public
	*/
	public static function cfServicesNames($cf_id){
		global $wpdb;
		
		$assigned_services_ids = '';
		$checked 	= '';
		$output 	= '';
		
		$query 		= "SELECT * FROM {$wpdb->prefix}bmify_services ORDER BY title";
		$services 	= $wpdb->get_results( $query, OBJECT  );
		
		$cf_id 		= esc_sql($cf_id);
		$query 		= "SELECT * FROM {$wpdb->prefix}bmify_customfields WHERE id=".$cf_id;
		$cfs 		= $wpdb->get_results( $query, OBJECT  );
		
		foreach($cfs as $cf){
			$assigned_services_ids = $cf->services_ids;
		}
		$assigned_services_ids = explode(',', $assigned_services_ids);
		
		
		$count = '';
		$key = 0;
		$myKey = 0;
		$ofKey = 0;
		
		// experimental types: +3 or 3/4
		$type = 'of'; // plus
		foreach($services as $service){
			$ofKey++;
			if(in_array( $service->id, $assigned_services_ids )){$checked = 'checked';}
			
			if($checked == 'checked'){
				$key++;
				if($key == 1){
					$output = '<span class="text">'.$service->title.'</span>';
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