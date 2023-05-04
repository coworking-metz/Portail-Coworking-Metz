<?php
namespace Bookmify;


use Bookmify\Helper;
use Bookmify\HelperEmployees;
use Bookmify\GoogleCalendarProject;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {exit; }


/**
 * Class Helper Cabinet
 */
class HelperCabinet{
	
	
	public static function getProfile($userID, $userSlug){
		$html		= '';
		
		if($userID != ''){
			if($userSlug == 'employee'){
				$html	= self::getEmployeeProfile($userID);
			}else{
				$html	= self::getCustomerProfile($userID);
			}
		}
		
		return $html;
	}
	
	public static function getEmployeeProfile($ID = ''){
		global $wpdb;
		$html				= '';
		$googleClientID 	= get_option( 'bookmify_be_gc_client_id', '' );
		$googleClientSecret = get_option( 'bookmify_be_gc_client_secret', '' );
		$googleContent 		= 'enable';
		if($googleClientID == '' || $googleClientSecret == ''){
			$googleContent 	= 'disable';
		}
		if($ID != ''){
			$ID 			= esc_sql($ID);
			$query 			= "SELECT * FROM {$wpdb->prefix}bmify_employees WHERE id=".$ID;
			$employees	 	= $wpdb->get_results( $query, OBJECT  );
			foreach($employees as $employee){
				$ID						= $employee->id;
				$attachmentID			= $employee->attachment_id;
				$visibility				= $employee->visibility;
				$firstName				= $employee->first_name;
				$lastName				= $employee->last_name;
				$email					= $employee->email;
				$phone					= $employee->phone;
				$info					= $employee->info;
				$attachmentURLLarge		= Helper::bookmifyGetImageByID($attachmentID, 'large');
				$attachmentURL	 		= Helper::bookmifyGetImageByID($attachmentID);
				if($attachmentURL != ''){$opened = 'has_image';}else{$opened = '';}
				$selected	 			= bookmify_be_checked($visibility, "public");
				
				
				$googleWrap				= '';
				if($googleContent == 'enable'){
					$googleData 			= HelperEmployees::getGoogleData($ID);

					if($googleData != NULL){
						$googleData 		= json_decode(stripslashes($googleData), true);
						$googleTop 			= $googleData['calendarID'];
						$googleBottom 		= '<a href="#" class="bookmify_be_google_disable">'.esc_html__('Disconnect', 'bookmify').'</a>';
					}else{
						$google 			= new GoogleCalendarProject();
						$authURL 			= $google->createAuthUrl($ID);
						$authURL 			= filter_var($authURL, FILTER_SANITIZE_URL);
						$authURL			= '<a href='.$authURL.'>'.esc_html__('Authentification', 'bookmify').'</a>';
						$googleTop      	= '<span>'.esc_html__('Google Profile', 'bookmify').'</span>';
						$googleBottom 		= $authURL;
					}
					$googleWrap		= 	'<div class="bookmify_be_emmp_google_cal">
											<div class="google_cal_in">
												<span class="g_top">'.$googleTop.'</span>
												<span class="g_bottom">'.$googleBottom.'</span>
											</div>
											<div class="google_cal_icon">
												<span><img class="bookmify_be_svg" src="'.BOOKMIFY_ASSETS_URL.'img/google_dash.svg" alt="" /></span>
											</div>
										</div>';
				}
					

					
				$html 			= '<div class="bookmify_be_employee_profile" data-id="'.$ID.'">
									<div class="left_part">
										<div class="input_img">
											<input type="hidden" class="bookmify_be_img_id" name="employee_img_id" value="'.$attachmentID.'" />
											<div class="bookmify_thumb_wrap '.$opened.'" style="background-image:url('.$attachmentURLLarge.')">
												<div class="bookmify_thumb_edit">
													<span class="edit"><img class="bookmify_be_svg" src="'.BOOKMIFY_ASSETS_URL.'img/image.svg" alt="" /></span>
												</div>
												<div class="bookmify_thumb_remove '.$opened.'"><a href="#" class="bookmify_be_delete" data-entity-id="'.$ID.'"><img class="bookmify_be_svg" src="'.BOOKMIFY_ASSETS_URL.'img/delete.svg" alt="" /></a></div>
											</div>
										</div>
										<div class="visibility">
											<label class="switch">
												<input type="checkbox" disabled id="visible_'.$ID.'" value="1" name="employee_visibility" '.$selected.' />
												<span class="slider round"></span>
											</label>
											<label class="repeater" for="visible_'.$ID.'">'.esc_html__('Visible to Public','bookmify').'</label>
										</div>
										'.$googleWrap.'
									</div>
									<div class="right_part">

										<div class="first_last_name">
											<div class="first_name">
												<label>
													<span class="title">'.esc_html__('First Name','bookmify').'<span>*</span></span>
												</label>
												<input class="required_field" type="text" name="first_name" value="'.$firstName.'" />
											</div>
											<div class="last_name">
												<label>
													<span class="title">'.esc_html__('Last Name','bookmify').'<span>*</span></span>
												</label>
												<input class="required_field" type="text" name="last_name" value="'.$lastName.'" />
											</div>
										</div>

										<div class="email_phone">
											<div class="email_wrap">
												<label>
													<span class="title">'.esc_html__('Email','bookmify').'<span>*</span></span>
												</label>
												<input class="required_field employee_email" type="text" name="email" value="'.$email.'" />
											</div>
											<div class="phone_wrap">
												<label>
													<span class="title">'.esc_html__('Phone','bookmify').'</span>
												</label>
												<input type="tel" name="phone" value="'.$phone.'" />
												<span class="bot__btn"><img class="bookmify_be_svg" src="'.BOOKMIFY_ASSETS_URL.'img/down.svg" alt="" /></span>
											</div>
										</div>

										<div class="info_holder">
											<label>'.esc_html__('Info','bookmify').'</label>
											<textarea name="employee_info" placeholder="'.esc_attr__('Some info for internal usage','bookmify').'">'.$info.'</textarea>
										</div>

										<div class="save_holder">
											<a class="save" href="#">
												<span class="text">'.esc_html__('Save','bookmify').'</span>
												<span class="save_process">
													<span class="ball"></span>
													<span class="ball"></span>
													<span class="ball"></span>
												</span>
											</a>
										</div>

									</div>
								</div>';
			}
		}
			
			
		return $html;
	}
	
	public static function getCustomerProfile($ID){
		global $wpdb;
		$html 			= '';
		$ID 			= esc_sql($ID);
		$query 			= "SELECT * FROM {$wpdb->prefix}bmify_customers WHERE id=".$ID;
		$customers	 	= $wpdb->get_results( $query, OBJECT  );
		foreach($customers as $customer){

			$year = '0000'; $month = '00'; $day = '00';
			if($customer->birthday != '0000-00-00'){
				$year 	= date('Y', strtotime($customer->birthday));
				$month 	= date('m', strtotime($customer->birthday));
				$day 	= date('d', strtotime($customer->birthday));
			}

			$input  = '<div class="birthday_input_wrap">';
			$input .= 	'<select class="years" data-index="'.$year.'"><option value="0000">---</option></select>';
			$input .= 	'<select class="months" data-index="'.$month.'"><option value="00">---</option></select>';
			$input .= 	'<select class="days" data-index="'.$day.'"><option value="00">---</option></select>';
			$input .= '</div>';

			$customerBirthday = $input;


			$html = '

											<div class="bookmify_be_customer_profile" data-id="'.$ID.'">

												<div class="input_wrap_row">
													<div class="input_wrap first_name">
														<label><span class="title">'.esc_html__('First Name','bookmify').'<span>*</span></span></label>
														<input class="customer_first_name required_field" type="text" value="'.$customer->first_name.'" />
													</div>
												</div>

												<div class="input_wrap_row">
													<div class="input_wrap last_name">
														<label><span class="title">'.esc_html__('Last Name','bookmify').'<span>*</span></span></label>
														<input class="customer_last_name required_field" type="text" value="'.$customer->last_name.'" />
													</div>
												</div>

												<div class="input_wrap_row">
													<div class="input_wrap email">
														<label><span class="title">'.esc_html__('Email Address','bookmify').'<span>*</span></span></label>
														<input class="customer_email required_field" type="text" value="'.$customer->email.'" />
													</div>
												</div>

												<div class="input_wrap_row">
													<div class="input_wrap phone">
														<label><span class="title">'.esc_html__('Phone Number','bookmify').'</span></label>
														<input class="customer_phone" type="tel" value="'.$customer->phone.'" />
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
													<div class="input_wrap country">
														<label><span class="title">'.esc_html__('Country','bookmify').'</span></label>
														<input class="customer_country" type="text" value="'.$customer->country.'" />
													</div>
												</div>

												<div class="input_wrap_row">
													<div class="input_wrap state">
														<label><span class="title">'.esc_html__('State','bookmify').'</span></label>
														<input class="customer_state" type="text" value="'.$customer->state.'" />
													</div>
												</div>

												<div class="input_wrap_row">
													<div class="input_wrap city">
														<label><span class="title">'.esc_html__('City','bookmify').'</span></label>
														<input class="customer_city" type="text" value="'.$customer->city.'" />
													</div>
												</div>

												<div class="input_wrap_row">
													<div class="input_wrap postcode">
														<label><span class="title">'.esc_html__('Post Code','bookmify').'</span></label>
														<input class="customer_postcode" type="text" value="'.$customer->post_code = (($customer->post_code==0)?'':$customer->post_code).'" />
													</div>
												</div>

												<div class="input_wrap_row">
													<div class="input_wrap address">
														<label><span class="title">'.esc_html__('Address','bookmify').'</span></label>
														<input class="customer_address" type="text" value="'.$customer->address.'" />
													</div>
												</div>

												<div class="input_wrap_row full">
													<div class="input_wrap info_holder">
														<label><span class="title">'.esc_html__('Info','bookmify').'</span></label>
														<textarea class="customer_info" placeholder="'.esc_attr__('Some info for internal usage','bookmify').'">'.$customer->info.'</textarea>
													</div>
												</div>
												
												<div class="input_wrap_row full">
													<a class="save" href="#">
														<span class="text">'.esc_html__('Save','bookmify').'</span>
														<span class="save_process">
															<span class="ball"></span>
															<span class="ball"></span>
															<span class="ball"></span>
														</span>
													</a>
												</div>

											</div>';
		}
		return $html;
	}
	
	/**
	 * @since 1.0.0
	 * @access protected
	*/
	public static function appointmentsCount($user,$userID){
		global $wpdb;
		$count		= 0;
		if($userID != ''){
			if($user == 'employee'){
				$userID	= esc_sql($userID);
				$count	= $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->prefix}bmify_appointments WHERE employee_id=".$userID );
			}else{
				$userID	= esc_sql($userID);
				$count	= $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->prefix}bmify_appointments a LEFT JOIN {$wpdb->prefix}bmify_customer_appointments ca  ON ca.appointment_id = a.id  WHERE ca.customer_id=".$userID );
			}
		}
		
		return $count;
	}
	
	public static function allFilter($user,$userID){
		$html = '';
		if($userID != ''){
			$html = '<div class="bookmify_be_appointments_filter" data-filter-status="">
							<div class="bookmify_be_filter_wrap">
								<div class="bookmify_be_filter">
									<div class="bookmify_be_row">

										<div class="bookmify_be_filter_list daterange">
											<div class="bookmify_be_filter_list_in">
												<div class="input_wrapper">
													<input type="text" placeholder="'.esc_attr__('Date', 'bookmify').'" class="filter_date" autocomplete=off />
												</div>
											</div>
										</div>

										'.self::servicesAsFilter($user,$userID).'

										'.self::customersAsFilter($user,$userID).'

										'.self::employeesAsFilter($user,$userID).'

										'.self::statusAsFilter($user,$userID).'

										<div class="bookmify_be_filter_list reset">
											<div class="bookmify_be_filter_list_in">
												<div class="input_wrapper">
													<a href="#">'.esc_html__('Reset', 'bookmify').'</a>
												</div>
											</div>
										</div>

									</div>

								</div>
							</div>
						</div>';
		}
		return $html;
	}
	
	/**
     * Public Funtion.
	 * @since 1.0.0
     */
	public static function servicesAsFilter($user,$userID){
		global $wpdb;
		$query 		= "SELECT title, id FROM {$wpdb->prefix}bmify_services";
		$results	= $wpdb->get_results( $query, OBJECT  );
		
		
		$list 		= '<div class="bookmify_be_services_filter_list">';
		foreach ( $results as $result ) {
			$list  .= '<div data-id="'.$result->id.'">'.esc_html( $result->title ).'</div>';
		}
		$list 	   .= '</div>';
		
		$html 		= '';
		$html 	   .= '<div class="bookmify_be_filter_list services">
							<div class="bookmify_be_filter_list_in">
								<div class="input_wrapper">
									<input readonly data-placeholder="'.esc_attr__('Services', 'bookmify').'" type="text" placeholder="'.esc_attr__('Services', 'bookmify').'" class="filter_list" autocomplete=off />
									<span class="icon">
										<img class="bookmify_be_svg" src="'.BOOKMIFY_ASSETS_URL.'img/down.svg'.'" alt="" />
										<span class="bookmify_be_loader small">
											<span class="loader_process">
												<span class="ball"></span>
												<span class="ball"></span>
												<span class="ball"></span>
											</span>
										</span>
										<span class="reset"></span>
									</span>
									<div class="bookmify_be_new_value"></div>
								</div>

								'.$list.'
							</div>
						</div>';
		return $html;
	}
	
	/**
     * Public Funtion.
	 * @since 1.0.0
     */
	public static function customersAsFilter($user,$userID){
		global $wpdb;
		if($user == 'customer'){
			$html = '';
		}else{
			$query 		= "SELECT first_name, last_name, id FROM {$wpdb->prefix}bmify_customers";
			$results	= $wpdb->get_results( $query, OBJECT  );
			$list 		= '<div class="bookmify_be_filter_popup_list customers">
								<div class="bookmify_be_filter_popup_list_in">';
			foreach ( $results as $result ) {
				$list  .= '<div data-id="'.$result->id.'" class="item"><span>'.esc_html( $result->first_name.' '.$result->last_name ).'</span></div>';
			}
			$list 	   .= '</div></div>';

			$html 		= '<div class="bookmify_be_filter_list customers">
								<div class="bookmify_be_filter_list_in">
									<div class="input_wrapper">
										<input data-placeholder="'.esc_attr__('Customers', 'bookmify').'" type="text" placeholder="'.esc_attr__('Customers', 'bookmify').'" class="filter_list" autocomplete=off />
										<span class="icon">
											<img class="bookmify_be_svg" src="'.BOOKMIFY_ASSETS_URL.'img/down.svg'.'" alt="" />
											<span class="bookmify_be_loader small">
												<span class="loader_process">
													<span class="ball"></span>
													<span class="ball"></span>
													<span class="ball"></span>
												</span>
											</span>
											<span class="reset"></span>
										</span>
										<div class="bookmify_be_new_value"></div>
									</div>

									'.$list.'
								</div>
							</div>';
		}
			
		return $html;
	}
	/**
     * Public Funtion.
	 * @since 1.0.0
     */
	public static function employeesAsFilter($user,$userID){
		global $wpdb;
		
		if($user == 'employee'){
			$html = '';
		}else{
			$query 		= "SELECT first_name, last_name, id FROM {$wpdb->prefix}bmify_employees";
			$results	= $wpdb->get_results( $query, OBJECT  );
			$list 		= '<div class="bookmify_be_filter_popup_list employees">
								<div class="bookmify_be_filter_popup_list_in">';
			foreach ( $results as $result ) {
				$list  .= '<div data-id="'.$result->id.'" class="item"><span>'.esc_html( $result->first_name.' '.$result->last_name ).'</span></div>';
			}
			$list 	   .= '</div></div>';


			$html		= '<div class="bookmify_be_filter_list employees">
							<div class="bookmify_be_filter_list_in">
								<div class="input_wrapper">
									<input data-placeholder="'.esc_attr__('Employees', 'bookmify').'" type="text" placeholder="'.esc_attr__('Employees', 'bookmify').'" class="filter_list" autocomplete=off />
									<span class="icon">
										<img class="bookmify_be_svg" src="'.BOOKMIFY_ASSETS_URL.'img/down.svg'.'" alt="" />
										<span class="bookmify_be_loader small">
											<span class="loader_process">
												<span class="ball"></span>
												<span class="ball"></span>
												<span class="ball"></span>
											</span>
										</span>
										<span class="reset"></span>
									</span>
									<div class="bookmify_be_new_value"></div>
								</div>

								'.$list.'
							</div>
						</div>';
			return $html;
		}
			
	}
	/**
     * Public Funtion.
	 * @since 1.0.0
     */
	public static function statusAsFilter(){
		
		$list 		= '<div class="bookmify_be_filter_popup_list status">
							<div class="bookmify_be_filter_popup_list_in">';
		
		$list 				.= '<div data-status="approved" class="item"><span>'.esc_html__('Approved', 'bookmify').'</span></div>';
		$list 				.= '<div data-status="pending" class="item"><span>'.esc_html__('Pending', 'bookmify').'</span></div>';
		$list 				.= '<div data-status="canceled" class="item"><span>'.esc_html__('Canceled', 'bookmify').'</span></div>';
		$list 				.= '<div data-status="rejected" class="item"><span>'.esc_html__('Rejected', 'bookmify').'</span></div>';
		
		$list 	   .= '</div></div>';
		
		$html		= '<div class="bookmify_be_filter_list status">
						<div class="input_wrapper">
							<input data-placeholder="'.esc_attr__('Status', 'bookmify').'" type="text" placeholder="'.esc_attr__('Status', 'bookmify').'" class="filter_list" autocomplete=off />
							<span class="icon">
								<img class="bookmify_be_svg" src="'.BOOKMIFY_ASSETS_URL.'img/down.svg'.'" alt="" />
								<span class="bookmify_be_loader small">
									<span class="loader_process">
										<span class="ball"></span>
										<span class="ball"></span>
										<span class="ball"></span>
									</span>
								</span>
								<span class="reset"></span>
							</span>
							<div class="bookmify_be_new_value"></div>
						</div>

						'.$list.'

					</div>';
		return $html;
	}
	
	
	/**
     * Get Customers Col.
	 * @since 1.0.0
     */
    public static function bookmifyGetCustomersCol( $id, $user, $userID ){
        global $wpdb;
		$extraQuery 		= "";
		$userID				= esc_sql($userID);
		if($user == 'customer'){
			$extraQuery 	= " AND customer_id=".$userID;
		}
		$customerIDs 		= array();
		$html 				= '';
		$id					= esc_sql($id);
		$query 				= "SELECT customer_id FROM {$wpdb->prefix}bmify_customer_appointments WHERE appointment_id=".$id.$extraQuery;
		$results 			= $wpdb->get_results( $query );
		foreach($results as $result){
			$customerIDs[] 	= $result->customer_id;
		}
		$count 	= count($customerIDs);
		if($count == 1){
			$customerIDs	= esc_sql($customerIDs);
			$query 			= "SELECT first_name, last_name FROM {$wpdb->prefix}bmify_customers WHERE id=".$customerIDs[0];
			$results 		= $wpdb->get_results( $query );
			$html 			.= '<span><span class="full_name only_one">'.$results[0]->first_name.' '.$results[0]->last_name.'</span></span>';
		}else if($count > 1){
			$customerIDs	= esc_sql($customerIDs);
			$query 			= "SELECT first_name, last_name FROM {$wpdb->prefix}bmify_customers WHERE id=".$customerIDs[0];
			$results 		= $wpdb->get_results( $query );
			$html 			.= '<span><span class="full_name only_one">'.$results[0]->first_name.' '.$results[0]->last_name.'</span><span class="plus">+'.($count-1).'</span></span>';
		}
		
			
		return $html;
    }
	
	
	public static function detailsOfAppointment($appointmentID,$user,$userID){
		global $wpdb;
		$html				= '';
		$extraQuery			= '';
		$userID				= esc_sql($userID);
		if($user == 'customer'){
			$extraQuery 	= " AND ca.customer_id=".$userID;
		}
		$appointmentID		= esc_sql($appointmentID);
		$query 		= "SELECT
							s.title serviceTitle,
							e.first_name empFirstName,
							e.last_name empLastName,
							a.start_date startDate,
							a.end_date endDate,
							a.status status,
							a.info info,
							a.service_id serviceID,
							a.employee_id employeeID,
							s.buffer_before bufferBefore,
							s.buffer_after bufferAfter,
							GROUP_CONCAT(ca.customer_id ORDER BY ca.id) customerIDs,
							GROUP_CONCAT(ca.number_of_people ORDER BY ca.id) customerPeopleCounts,
							GROUP_CONCAT(ca.price ORDER BY ca.id) customerServicesPrice,
							GROUP_CONCAT(ca.status ORDER BY ca.id) customerStatuses,
							GROUP_CONCAT(ca.id ORDER BY ca.id) customerAppointmentIDs,
							GROUP_CONCAT(p.id) customerPaymentIDs

						FROM 	   	   {$wpdb->prefix}bmify_appointments a 
							INNER JOIN {$wpdb->prefix}bmify_customer_appointments ca 			ON ca.appointment_id = a.id
							INNER JOIN {$wpdb->prefix}bmify_employees e 						ON a.employee_id = e.id 
							INNER JOIN {$wpdb->prefix}bmify_services s 							ON a.service_id = s.id
							INNER JOIN {$wpdb->prefix}bmify_payments p 							ON ca.payment_id = p.id

						WHERE a.id=".$appointmentID.$extraQuery;
		$results 	= $wpdb->get_results( $query, OBJECT  );
		
		foreach($results as $result){
			$servicesAllTotal 	= 0;
			$extrasAllTotal 	= 0;
			$paidAllTotal		= 0;
			$dueAllTotal		= 0;
			$paymentTotal		= 0;
			$status 			= $result->status;
			switch($status){
				case 'approved': 	$icon = 'checked'; 	$statusText = esc_html__('Approved', 'bookmify'); break;
				case 'pending': 	$icon = 'circle'; 	$statusText = esc_html__('Pending', 'bookmify');  break;
				case 'canceled':	$icon = 'cancel'; 	$statusText = esc_html__('Canceled', 'bookmify'); break;
				case 'rejected': 	$icon = 'cancel'; 	$statusText = esc_html__('Rejected', 'bookmify'); break;
			}
			
			$customerIDs 	= explode(',', $result->customerIDs); 				// creating array from string
			
			// get people count with approved and pending statuses
			$appointmentID		= esc_sql($appointmentID);
			$customerIDs		= esc_sql($customerIDs);
			$query 			= "SELECT number_of_people FROM {$wpdb->prefix}bmify_customer_appointments WHERE `customer_id` IN (" . implode(',', array_map('intval', $customerIDs)) . ") AND appointment_id=".$appointmentID." AND status IN ('approved','pending')";
			$res 			= $wpdb->get_results( $query, OBJECT  );
			$peopleCount	= 0;
			foreach($res as $re){
				$peopleCount += $re->number_of_people;
			}
			// get capacity min and capacity max of selected service and employee
			$rServiceID		= $result->serviceID;
			$rEmployeeID	= $result->employeeID;
			$rServiceID		= esc_sql($rServiceID);
			$rEmployeeID	= esc_sql($rEmployeeID);
			$query 			= "SELECT capacity_min,capacity_max FROM {$wpdb->prefix}bmify_employee_services WHERE service_id=".$rServiceID." AND employee_id=".$rEmployeeID;
			$res 			= $wpdb->get_results( $query, OBJECT  );
			$capacityMin	= $res[0]->capacity_min;
			$capacityMax	= $res[0]->capacity_max;
			
			// get service buffer before and after
			$bufferBefore 	= Helper::bookmifyNumberToDuration($result->bufferBefore);
			$bufferAfter 	= Helper::bookmifyNumberToDuration($result->bufferAfter);
			
			// **************************************************************************************************************************
			// DETAILS
			// **************************************************************************************************************************
			$details = '<div class="detail_box">
							<div class="detail_box_header"><h4>'.esc_html__('Appointment Details', 'bookmify').'</h4></div>
							<div class="detail_box_content">
								<div class="detail_box_row">
									<div class="detail_box_col col_left"><span>'.esc_html__('Date:', 'bookmify').'</span></div>
									<div class="detail_box_col col_right"><span>'.date_i18n(get_option('bookmify_be_date_format', 'd F, Y'), strtotime($result->startDate)).'</span></div>
								</div>';
			if($user == 'customer'){
				$time = self::getDurationForAppointment($appointmentID, 'duration', $user, $userID);
				$details	.= '<div class="detail_box_row">
									<div class="detail_box_col col_left"><span>'.esc_html__('Time:', 'bookmify').'</span></div>
									<div class="detail_box_col col_right"><span class="app_time">'.date('H:i', strtotime($result->startDate)).' - '.date('H:i', strtotime($result->startDate) + $time).'</span></div>
								</div>';
			}else if($user == 'employee'){
				$details	.= '<div class="detail_box_row">
									<div class="detail_box_col col_left"><span>'.esc_html__('Time:', 'bookmify').'</span></div>
									<div class="detail_box_col col_right"><span class="app_time">'.date('H:i', strtotime($result->startDate)).' - '.date('H:i', strtotime($result->endDate)).'</span></div>
								</div>';
			}
			if($user == 'employee'){
				$details	.= '<div class="detail_box_row">
									<div class="detail_box_col col_left"><span>'.esc_html__('Status:', 'bookmify').'</span></div>
									<div class="detail_box_col col_right"><span class="status '.$status.'">'.$statusText.'</span></div>
								</div>';
			}
				$details	.= '<div class="detail_box_row">
									<div class="detail_box_col col_left"><span>'.esc_html__('Employee:', 'bookmify').'</span></div>
									<div class="detail_box_col col_right"><span>'.$result->empFirstName.' '.$result->empLastName.'</span></div>
								</div>
								<div class="detail_box_row">
									<div class="detail_box_col col_left"><span>'.esc_html__('Service:', 'bookmify').'</span></div>
									<div class="detail_box_col col_right"><span>'.$result->serviceTitle.'</span></div>
								</div>';
			if($user == 'employee'){
				$details 	.='<div class="detail_box_row">
									<div class="detail_box_col col_left"><span>'.esc_html__('Buffer Before/After:', 'bookmify').'</span></div>
									<div class="detail_box_col col_right"><span>'.$bufferBefore.' / '.$bufferAfter.'</span></div>
								</div>
								<div class="detail_box_row">
									<div class="detail_box_col col_left"><span>'.esc_html__('Capacity Min/Max:', 'bookmify').'</span></div>
									<div class="detail_box_col col_right"><span>'.$capacityMin.' / '.$capacityMax.'</span></div>
								</div>
								<div class="detail_box_row">
									<div class="detail_box_col col_left"><span>'.esc_html__('People Count:', 'bookmify').'</span></div>
									<div class="detail_box_col col_right"><span>'.$peopleCount.' '.esc_html__('People', 'bookmify').'</span></div>
								</div>';
								}
			if($result->info != '' && $user == 'employee'){
				$details 	.= '<div class="detail_box_row">
									<div class="detail_box_col col_left"><span>'.esc_html__('Info:', 'bookmify').'</span></div>
									<div class="detail_box_col col_right"><span>'.$result->info.'</span></div>
								</div>';
			}
				$details 	.= '</div>
							</div>';
			// **************************************************************************************************************************
			// CUSTOMERS
			// **************************************************************************************************************************
			$customers 					= '';
			$customerStatuses 			= explode(',', $result->customerStatuses); 			// creating array from string
			$customerAppointmentIDs 	= explode(',', $result->customerAppointmentIDs); 	// creating array from string
			$customerPeopleCounts 		= explode(',', $result->customerPeopleCounts); 		// creating array from string
			$customerServicesPrice 		= explode(',', $result->customerServicesPrice); 	// creating array from string
			$customerPaymentIDs 		= explode(',', $result->customerPaymentIDs); 		// creating array from string
			foreach($customerIDs as $key => $customerID){
				$customerFullName 		= Helper::bookmifyGetCustomerCol($customerID);
				$customerEmail	 		= Helper::bookmifyGetCustomerCol($customerID, 'email');
				$customerPhone	 		= Helper::bookmifyGetCustomerCol($customerID, 'phone');
				
				$customerAppointmentID 	= $customerAppointmentIDs[$key];
				$customerPeopleCount 	= $customerPeopleCounts[$key];
				$customerServicePrice 	= $customerServicesPrice[$key];
				$customerServiceTotal	= $customerPeopleCount * $customerServicePrice;
				
				$customerAppointmentID	= esc_sql($customerAppointmentID);
				$query 			= "SELECT * FROM {$wpdb->prefix}bmify_customer_appointments_extras WHERE customer_appointment_id=".$customerAppointmentID;
				$res 			= $wpdb->get_results( $query, OBJECT  );
				$extras			= '';
				if(count($res) != 0){
					$extras .= '<div class="detail_box_row">
									<div class="detail_box_col col_left"><span class="sub_title">'.esc_html__('Extras', 'bookmify').'</span></div>
								</div>';
				}
				$extraTotalForCustomer = 0;
				foreach($res as $re){
					$extraID 		= $re->extra_id;
					$extraQuantity	= $re->quantity;
					$extraPrice		= $re->price;
					$extraTotal		= $extraPrice * $extraQuantity * $customerPeopleCount;
					$extraTotalForCustomer += $extraTotal;
					$extras .= '<div class="detail_box_row">
									<div class="detail_box_col col_left"><span>'.Helper::bookmifyGetExtraServicesCol($re->extra_id).'</span></div>
									<div class="detail_box_col col_right"><span><span class="price_calc">'.$customerPeopleCount.' x '.$extraQuantity.' x '.Helper::bookmifyPriceCorrection($extraPrice).' = </span><span class="price_eq">'.Helper::bookmifyPriceCorrection($extraTotal).'</span></span></div>
								</div>';
				}
				
				$customerPaymentIDs[$key]	= esc_sql($customerPaymentIDs[$key]);
				$query 			= "SELECT * FROM {$wpdb->prefix}bmify_payments WHERE id=".$customerPaymentIDs[$key];
				$payments 		= $wpdb->get_results( $query, OBJECT  );
				$pPaidType		= $payments[0]->paid_type;
				$pCreatedDate	= $payments[0]->created_date;
				$pPaid 			= $payments[0]->paid;
				$pTotalPrice	= $payments[0]->total_price;
				
				switch($customerStatuses[$key]){
					case 'approved': 	$icon = 'checked'; 	$statusText = esc_html__('Approved', 'bookmify'); break;
					case 'pending': 	$icon = 'circle'; 	$statusText = esc_html__('Pending', 'bookmify');  break;
					case 'canceled':	$icon = 'cancel'; 	$statusText = esc_html__('Canceled', 'bookmify'); break;
					case 'rejected': 	$icon = 'cancel'; 	$statusText = esc_html__('Rejected', 'bookmify'); break;
				}
				if($customerStatuses[$key] == 'approved' || $customerStatuses[$key] == 'pending'){
					$servicesAllTotal			+= $customerServiceTotal;
					$extrasAllTotal				+= $extraTotalForCustomer;
					$paidAllTotal 				+= $pPaid;
					$paymentTotal				+= $pTotalPrice;
					$dueAllTotal 				+= ($pTotalPrice - $pPaid);
				}else{
					$customerServicePrice 		= 0;
					$customerServiceTotal 		= 0;
					$extraTotalForCustomer 		= 0;
					$pTotalPrice 				= 0;
					$pPaid 						= 0;
				}
				
				$taxCustomer	= self::taxOfCustomer($appointmentID,$customerID);
				switch($pPaidType){
					case 'paypal': 	$paymentGateway = esc_html__('Paypal', 'bookmify'); break;
					case 'stripe': 	$paymentGateway = esc_html__('Stripe', 'bookmify'); break;
					case 'local': 	
					default: 		$paymentGateway = esc_html__('Local', 'bookmify'); break;
				}
				
				$extras .= 	   '<div class="detail_box_row">
									<div class="detail_box_col col_left"><span class="sub_title">'.esc_html__('Payment', 'bookmify').'</span></div>
								</div>

								<div class="detail_box_row">
									<div class="detail_box_col col_left"><span>'.esc_html__('Created Date:', 'bookmify').'</span></div>
									<div class="detail_box_col col_right"><span>'.date_i18n(get_option('bookmify_be_date_format', 'd F, Y'), strtotime($pCreatedDate)).'</span></div>
								</div>
								<div class="detail_box_row">
									<div class="detail_box_col col_left"><span>'.esc_html__('Payment Method:', 'bookmify').'</span></div>
									<div class="detail_box_col col_right"><span>'.$paymentGateway.'</span></div>
								</div>
								<div class="detail_box_row">
									<div class="detail_box_col col_left"><span>'.esc_html__('Service Price:', 'bookmify').'</span></div>
									<div class="detail_box_col col_right"><span><span class="price_calc">'.$customerPeopleCount.' x '.Helper::bookmifyPriceCorrection($customerServicePrice).' = </span><span class="price_eq">'.Helper::bookmifyPriceCorrection($customerServiceTotal).'</span></span></div>
								</div>
								<div class="detail_box_row">
									<div class="detail_box_col col_left"><span>'.esc_html__('Extras Price:', 'bookmify').'</span></div>
									<div class="detail_box_col col_right"><span><span class="price_eq">'.Helper::bookmifyPriceCorrection($extraTotalForCustomer).'</span></span></div>
								</div>
								<div class="detail_box_row">
									<div class="detail_box_col col_left"><span>'.esc_html__('Service Tax:', 'bookmify').'</span></div>
									<div class="detail_box_col col_right"><span><span class="price_calc">'.$customerPeopleCount.' x '.Helper::bookmifyPriceCorrection($customerServicePrice).' x  '.$taxCustomer.'% = </span><span class="price_eq">'.Helper::bookmifyPriceCorrection(floor($customerServiceTotal*$taxCustomer)/100).'</span></span></div>
								</div>
								<div class="detail_box_row">
									<div class="detail_box_col col_left"><span>'.esc_html__('Extras Tax:', 'bookmify').'</span></div>
									<div class="detail_box_col col_right"><span><span class="price_eq">'.Helper::bookmifyPriceCorrection(floor($extraTotalForCustomer*$taxCustomer)/100).'</span></span></div>
								</div>
								<div class="detail_box_row">
									<div class="detail_box_col col_left"><span>'.esc_html__('Paid:', 'bookmify').'</span></div>
									<div class="detail_box_col col_right"><span><span class="price_eq">'.Helper::bookmifyPriceCorrection($pPaid).'</span></span></div>
								</div>
								<div class="detail_box_row">
									<div class="detail_box_col col_left"><span>'.esc_html__('Due:', 'bookmify').'</span></div>
									<div class="detail_box_col col_right"><span><span class="price_eq">'.Helper::bookmifyPriceCorrection($pTotalPrice - $pPaid).'</span></span></div>
								</div>
								<div class="detail_box_row sub_total">
									<div class="detail_box_col col_left"><span>'.esc_html__('Subtotal:', 'bookmify').'</span></div>
									<div class="detail_box_col col_right"><span><span class="price_eq">'.Helper::bookmifyPriceCorrection($pTotalPrice).'</span></span></div>
								</div>';
				
				
				
				$customers .= '<div class="detail_box has_subtotal">
								<div class="detail_box_header"><h4>'.esc_html__('Customer', 'bookmify').' #'.($key+1).'</h4></div>
								<div class="detail_box_content">
									<div class="detail_box_row">
										<div class="detail_box_col col_left"><span class="sub_title">'.esc_html__('Info', 'bookmify').'</span></div>
									</div>
									<div class="detail_box_row">
										<div class="detail_box_col col_left"><span>'.esc_html__('Name:', 'bookmify').'</span></div>
										<div class="detail_box_col col_right"><span>'.$customerFullName.'</span></div>
									</div>
									<div class="detail_box_row">
										<div class="detail_box_col col_left"><span>'.esc_html__('Email:', 'bookmify').'</span></div>
										<div class="detail_box_col col_right"><span>'.$customerEmail.'</span></div>
									</div>';
				if($customerPhone != ''){
				$customers 	.= 		'<div class="detail_box_row">
										<div class="detail_box_col col_left"><span>'.esc_html__('Phone:', 'bookmify').'</span></div>
										<div class="detail_box_col col_right"><span>'.$customerPhone.'</span></div>
									</div>';
				}
				
				$customers .=	   '<div class="detail_box_row">
										<div class="detail_box_col col_left"><span class="sub_title">'.esc_html__('Appointment', 'bookmify').'</span></div>
									</div>
									<div class="detail_box_row">
										<div class="detail_box_col col_left"><span>'.esc_html__('Status:', 'bookmify').'</span></div>
										<div class="detail_box_col col_right"><span class="status '.$customerStatuses[$key].'">'.$statusText.'</span></div>
									</div>
									<div class="detail_box_row">
										<div class="detail_box_col col_left"><span>'.esc_html__('People Count:', 'bookmify').'</span></div>
										<div class="detail_box_col col_right"><span>'.$customerPeopleCount.' '.esc_html__('People', 'bookmify').'</span></div>
									</div>
									'.$extras.'
						</div>
					</div>';
			}
			$total = '';
			$total = '<div class="detail_box highlighted">
							<div class="detail_box_content">
								<div class="detail_box_row">
									<div class="detail_box_col col_left"><span>'.esc_html__('Services Price:', 'bookmify').'</span></div>
									<div class="detail_box_col col_right"><span><span class="price_eq">'.Helper::bookmifyPriceCorrection($servicesAllTotal).'</span></span></div>
								</div>
								<div class="detail_box_row">
									<div class="detail_box_col col_left"><span>'.esc_html__('Extras Price:', 'bookmify').'</span></div>
									<div class="detail_box_col col_right"><span><span class="price_eq">'.Helper::bookmifyPriceCorrection($extrasAllTotal).'</span></span></div>
								</div>
								<div class="detail_box_row">
									<div class="detail_box_col col_left"><span>'.esc_html__('Paid:', 'bookmify').'</span></div>
									<div class="detail_box_col col_right"><span><span class="price_eq">'.Helper::bookmifyPriceCorrection($paidAllTotal).'</span></span></div>
								</div>
								<div class="detail_box_row">
									<div class="detail_box_col col_left"><span>'.esc_html__('Due:', 'bookmify').'</span></div>
									<div class="detail_box_col col_right"><span><span class="price_eq">'.Helper::bookmifyPriceCorrection($dueAllTotal).'</span></span></div>
								</div>
								<div class="detail_box_row total">
									<div class="detail_box_col col_left"><span>'.esc_html__('Total Price:', 'bookmify').'</span></div>
									<div class="detail_box_col col_right"><span><span class="price_eq">'.Helper::bookmifyPriceCorrection($extrasAllTotal + $servicesAllTotal).'</span></span></div>
								</div>
							</div>
						</div>';
		}
		$html .= $details.$customers.$total;
		return $html;
	}
	
	
	public static function getDurationForAppointment($ID, $res = 'duration', $user, $userID){
		global $wpdb;
		
		// get service ID
		$ID				= esc_sql($ID);
		$query 			= "SELECT service_id,start_date,end_date FROM {$wpdb->prefix}bmify_appointments WHERE id=".$ID;
		$results 		= $wpdb->get_results( $query, OBJECT  );
		$serviceID		= $results[0]->service_id;
		$startDate		= $results[0]->start_date;
		$endDate		= $results[0]->end_date;
		
		// get duration of this service
		$serviceID		= esc_sql($serviceID);
		$query 			= "SELECT duration,buffer_before,buffer_after FROM {$wpdb->prefix}bmify_services WHERE id=".$serviceID;
		$results 		= $wpdb->get_results( $query, OBJECT  );
		$serDuration	= $results[0]->duration;
		$bufferBefore	= $results[0]->buffer_before;
		$bufferAfter	= $results[0]->buffer_after;
		
		if($user == 'customer'){
			$userID				= esc_sql($userID);
			$ID					= esc_sql($ID);
			$query 				= "SELECT id FROM {$wpdb->prefix}bmify_customer_appointments WHERE appointment_id=".$ID." AND customer_id=".$userID;
			$results 			= $wpdb->get_results( $query, OBJECT  );
			$customerAppID		= $results[0]->id;
			
			$customerAppID		= esc_sql($customerAppID);
			$query 				= "SELECT quantity,extra_id FROM {$wpdb->prefix}bmify_customer_appointments_extras WHERE customer_appointment_id=".$customerAppID;
			$results 			= $wpdb->get_results( $query, OBJECT  );
			$extraDuration		= 0;
			foreach($results as $result){
				$extraID		= $result->extra_id;
				$extraQuantity	= $result->quantity;
				$extraID		= esc_sql($extraID);
				$query 			= "SELECT duration FROM {$wpdb->prefix}bmify_extra_services WHERE id=".$extraID;
				$resS 			= $wpdb->get_results( $query, OBJECT  );
				$extraDuration	+= ($resS[0]->duration * $extraQuantity);
			}
			$duration			= $serDuration + $extraDuration;
		}else if($user == 'employee'){
			$startTimeInMinutes = date('H',strtotime($startDate))*60 + date('i',strtotime($startDate));
			$endTimeInMinutes 	= date('H',strtotime($endDate))*60 	+ date('i',strtotime($endDate));
			$duration 			= ($endTimeInMinutes - $startTimeInMinutes)*60;
			$extraDuration		= $duration - $serDuration;
		}
		
		switch($res){
			case 'duration': return $duration; break;
			case 'after': return $bufferAfter; break;
			case 'before': return $bufferBefore; break;
			case 'extra': return $extraDuration; break;
		}
	}
	
	
	public static function getPriceForAppointment($ID, $user, $userID){
		global $wpdb;
		
		$html			= '';
		$extraQuery	= '';
		$price			= 0;
		$paymentIDs		= array();
		$userID			= esc_sql($userID);
		if($user == 'customer'){
			$extraQuery = " AND customer_id=".$userID;
		}
		$ID			= esc_sql($ID);
		$query 		= "SELECT payment_id FROM {$wpdb->prefix}bmify_customer_appointments WHERE appointment_id=".$ID." AND status IN ('approved','pending')".$extraQuery;
		$results 	= $wpdb->get_results( $query, OBJECT  );
		foreach($results as $result){
			$paymentIDs[] = $result->payment_id;
		}
		if(!empty($paymentIDs)){
			$paymentIDs = esc_sql($paymentIDs);
			$query 		= "SELECT total_price FROM {$wpdb->prefix}bmify_payments WHERE `id` IN (" . implode(',', array_map('intval', $paymentIDs)) . ")";
			$results 	= $wpdb->get_results( $query, OBJECT  );
			foreach($results as $result){
				$price	+= $result->total_price;
			}
		}
		return $price;
	}
	
	public static function taxOfCustomer($appointmentID, $customerID){
		global $wpdb;
		$taxSummary = 0;
		
		if($appointmentID != ''){
			$query = "SELECT 
						p.tax_ids taxIDs

					FROM {$wpdb->prefix}bmify_payments p
						INNER JOIN {$wpdb->prefix}bmify_customer_appointments ca					ON ca.payment_id = p.id

					WHERE ca.customer_id=".$customerID." AND ca.appointment_id=".$appointmentID;
			$results = $wpdb->get_results( $query);
			if(!empty($results)){
				foreach($results as $result){
					$taxes = $result->taxIDs;
					$taxes = unserialize($taxes);
					if(!empty($taxes)){
						foreach($taxes as $tax){
							$taxSummary += (float) $tax['rate'];
						}
					}
					$taxes = NULL;
				}

			}
		}
			
		
		return $taxSummary;
	}
}