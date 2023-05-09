<?php
namespace Bookmify;



// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {exit; }


/**
 * Class Helper Customers
 */
class HelperCustomers
{
	public static function clonableForm(){	
		
		$customerBirthday  = '<div class="birthday_input_wrap">';
		$customerBirthday .= 	'<select class="years" data-index="0000"><option value="0000">---</option></select>';
		$customerBirthday .= 	'<select class="months" data-index="00"><option value="00">---</option></select>';
		$customerBirthday .= 	'<select class="days" data-index="00"><option value="00">---</option></select>';
		$customerBirthday .= '</div>';
		
		$phoneAsRequired 			= get_option('bookmify_be_phone_as_required', '');
		$phoneRField				= '';
		$phoneRStar					= '';
		if($phoneAsRequired == 'on'){
			$phoneRField			= 'required_field';
			$phoneRStar				= '<span>*</span>';
		}
		
		$html = '<div class="bookmify_be_popup_form_wrap">
					'.self::allNanoInOne().'
					<div class="bookmify_be_popup_form_position_fixer">
						<div class="bookmify_be_popup_form_bg">
							<div class="bookmify_be_popup_form">

								<div class="bookmify_be_popup_form_header">
									<h3>'.esc_html__('New Customer','bookmify').'</h3>
									<span class="closer"></span>
								</div>
								
								<div class="bookmify_be_popup_form_content">
									<div class="bookmify_be_popup_form_content_in">
									
										<div class="bookmify_be_popup_form_fields">
										
											<form autocomplete="off">
												<div class="input_wrap_row">
													<div class="input_wrap first_name">
														<label><span class="title">'.esc_html__('First Name','bookmify').'<span>*</span></span></label>
														<input class="customer_first_name required_field" type="text" value="" />
													</div>
												</div>

												<div class="input_wrap_row">
													<div class="input_wrap last_name">
														<label><span class="title">'.esc_html__('Last Name','bookmify').'<span>*</span></span></label>
														<input class="customer_last_name required_field" type="text" value="" />
													</div>
												</div>

												<div class="input_wrap_row">
													<div class="input_wrap email">
														<label><span class="title">'.esc_html__('Email Address','bookmify').'<span>*</span></span></label>
														<input class="customer_email required_field" type="text" value="" />
													</div>
												</div>

												<div class="input_wrap_row">
													<div class="input_wrap phone">
														<label><span class="title">'.esc_html__('Phone Number','bookmify').$phoneRStar.'</span></label>
														<input class="customer_phone '.$phoneRField.'" type="tel" value="" />
														<span class="bot__btn"><img class="bookmify_be_svg" src="'.BOOKMIFY_ASSETS_URL.'img/down.svg" alt="" /></span>
													</div>
												</div>

												<div class="input_wrap_row">
													<div class="input_wrap birthday">
														<label><span class="title">'.esc_html__('Date of Birth','bookmify').'</span></label>
														'.$customerBirthday.'
													</div>
												</div>
												
												<div class="input_wrap_row">
													<div class="input_wrap wp_user">
														<label>
															<span class="title">'.esc_html__('WordPress User','bookmify').'</span>
														</label>
														<input type="text" name="wp_user" placeholder="'.esc_attr__('Select from WP users','bookmify').'" readonly value="">
														<input type="hidden" name="wp_user_id" value="">
														<span class="bot_btn"><span></span><img class="bookmify_be_svg" src="'.BOOKMIFY_ASSETS_URL.'img/down.svg" alt="" /></span>
													</div>
												</div>

												<div class="input_wrap_row">
													<div class="input_wrap country">
														<label><span class="title">'.esc_html__('Country','bookmify').'</span></label>
														<input class="customer_country" type="text" value="" />
													</div>
												</div>

												<div class="input_wrap_row">
													<div class="input_wrap state">
														<label><span class="title">'.esc_html__('State','bookmify').'</span></label>
														<input class="customer_state" type="text" value="" />
													</div>
												</div>

												<div class="input_wrap_row">
													<div class="input_wrap city">
														<label><span class="title">'.esc_html__('City','bookmify').'</span></label>
														<input class="customer_city" type="text" value="" />
													</div>
												</div>

												<div class="input_wrap_row">
													<div class="input_wrap postcode">
														<label><span class="title">'.esc_html__('Post Code','bookmify').'</span></label>
														<input class="customer_postcode" type="text" value="" />
													</div>
												</div>

												<div class="input_wrap_row">
													<div class="input_wrap address">
														<label><span class="title">'.esc_html__('Address','bookmify').'</span></label>
														<input class="customer_address" type="text" value="" />
													</div>
												</div>

												<div class="input_wrap_row">
													<div class="input_wrap info_holder">
														<label><span class="title">'.esc_html__('Info','bookmify').'</span></label>
														<textarea class="customer_info" placeholder="'.esc_attr__('Some info for internal usage','bookmify').'"></textarea>
													</div>
												</div>
												
											</form>
											
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
	public static function deleteCustomerAppointments($customerID){
		global $wpdb;
		$query 		= "SELECT 
		
							ca.id customerAppID,
							ca.appointment_id appID,
							ca.payment_id paymentID
								
							FROM 	   	   {$wpdb->prefix}bmify_customer_appointments ca
							
							WHERE ca.customer_id=".$customerID;
		$results 	= $wpdb->get_results( $query, OBJECT  );
		foreach($results as $result){
			// удаление платежей выбранного клиента
			$wpdb->query($wpdb->prepare( "DELETE FROM {$wpdb->prefix}bmify_payments WHERE id=%d", $result->paymentID));
			// удаление экстра сервисов
			$wpdb->query($wpdb->prepare( "DELETE FROM {$wpdb->prefix}bmify_customer_appointments_extras WHERE customer_appointment_id=%d", $result->customerAppID));
			// получение идентификационных номеров встреч в массив для дальнейших действий
			$appIDs[] = $result->appID;
		}
		// удаление клиентской встречи выбранного клиента
		$wpdb->query($wpdb->prepare( "DELETE FROM {$wpdb->prefix}bmify_customer_appointments WHERE customer_id=%d", $customerID));
		// действие с встречами
		foreach($appIDs as $appID){
			$count 	= $wpdb->get_var( "SELECT COUNT(*) 
							FROM 	   	   {$wpdb->prefix}bmify_customer_appointments ca 
								LEFT JOIN  {$wpdb->prefix}bmify_appointments a  			ON a.id = ca.appointment_id
							
							WHERE ca.appointment_id=".$appID);
			if($count == 0){
				$wpdb->query($wpdb->prepare( "DELETE FROM {$wpdb->prefix}bmify_appointments WHERE id=%d", $appID));
			}
		}
		// удаление выбранного клиента
		$wpdb->query($wpdb->prepare( "DELETE FROM {$wpdb->prefix}bmify_customers WHERE id=%d", $customerID));
	}
	
	public static function allNanoInOne($ID = ''){
		global $wpdb;
		$html  = '<div class="bookmify_be_all_nano customer">';
		$html .= self::wpUserListNano($ID);
		$html .= '</div>';
		return $html;
	}
	
	public static function wpUserListNano($ID = ''){
		global $wpdb;
		$andQuery 	= "";
		$ID 		= esc_sql($ID);
		if($ID != ''){
			$andQuery = " WHERE id<>".$ID;
		}
		$query 		= "SELECT wp_user_id FROM {$wpdb->prefix}bmify_customers".$andQuery;
		$results 	= $wpdb->get_results( $query, OBJECT  );
		$excludeArr = array();
		foreach($results as $result){
			$excludeArr[] = $result->wp_user_id;
		}
		$args = array(
			'role__in'     	=> array('bookmify-customer'),
			'exclude'     	=> $excludeArr,
		 ); 
		$users 		 = get_users($args);
		$html 		 = 	'<div class="nano scrollbar-inner wp_users_customers">';
		$html 		.= 		'<div class="nano-content">';
		$html 		.= 			'<div data-id="n">'.esc_html__('Create New','bookmify').'</div>';
		foreach ( $users as $user ) {
			$html  .= 			'<div data-id="'.$user->ID.'">'.esc_html( $user->display_name ).'</div>';
		}
		$html 	   .= 		'</div>';
		$html 	   .= 	'</div>';
		return $html;
	}
	
}