<?php
namespace Bookmify;

use Bookmify\Helper;
use Bookmify\HelperTime;
use Bookmify\HelperAdmin;
use Bookmify\HelperCabinet;
use Bookmify\HelperAppointments;
use Bookmify\NotificationManagement;
use Bookmify\GoogleCalendarProject;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


class UserAppointments{

	const PAGE_ID 		= 'bookmify_user_appointments';
	
	private $per_page;
	private $daterange;
	private $dateformat;
	
	private $userSlug;
	private $userID;
	
	/**
	 * @since 1.0.0
	 * @access public
	*/
	public function __construct() {
		$this->assignValToVar();
		
		add_action( 'admin_menu', [ $this, 'register_admin_menu' ], 20 );
		
		// filter
		add_action( 'wp_ajax_ajaxFilterUserAppointmentList', [$this, 'ajaxFilterUserAppointmentList'] );
		// edit appointment
		add_action( 'wp_ajax_ajaxQueryEditUserAppointment', [$this, 'ajaxQueryEditUserAppointment'] );
		// details appointment
		add_action( 'wp_ajax_ajaxQueryUserDetailsAppointment', [$this, 'ajaxQueryUserDetailsAppointment'] );
		// cancel appointment
		add_action( 'wp_ajax_ajaxQueryCancelAppointment', [$this, 'ajaxQueryCancelAppointment'] );
	}
	
	/**
	 * @since 1.0.0
	 * @access public
	*/
	private function assignValToVar(){
		global $wpdb;
		$this->per_page 	= get_option('bookmify_be_appointments_pp', 10);
		$this->daterange 	= get_option('bookmify_be_appointments_daterange', 30) - 1;
		$this->dateformat 	= get_option('bookmify_be_date_format', 'd F, Y');
		
		
		$WPuserID			= get_current_user_id();
		$userData 			= get_userdata($WPuserID);
		$user				= '';
		if(is_user_logged_in()){
			if(in_array('bookmify-employee',$userData->roles)){
				$this->userSlug = 'employee';
				$user			= 'employee';
			}else if(in_array('bookmify-customer',$userData->roles)){
				$this->userSlug = 'customer';
				$user			= 'customer';
			}
			if($user == 'customer' || $user == 'employee'){
				$WPuserID			= esc_sql($WPuserID);
				$user				= esc_sql($user);
				$query 				= "SELECT id FROM {$wpdb->prefix}bmify_".$user."s WHERE wp_user_id=".$WPuserID;
				$results			= $wpdb->get_results( $query, OBJECT  );
				$this->userID 		= '';
				if(!empty($results)){
					$this->userID 	= $results[0]->id;
				}
			}
		}
	}
	

	/**
	 * @since 1.0.0
	 * @access public
	*/
	public function register_admin_menu() {

		add_submenu_page(
			BOOKMIFY_MENU,
			esc_html__( 'Appointments', 'bookmify' ),
			esc_html__( 'Appointments', 'bookmify' ),
			'bookmify_be_read_user_appointments',
			self::PAGE_ID,
			[ $this, 'display_user_appointments_page' ]
		);
		
		remove_submenu_page(BOOKMIFY_MENU, BOOKMIFY_MENU);
	}
	
	/**
	 * @since 1.0.0
	 * @access public
	*/
	public function display_user_appointments_page() {
		global $wpdb;

		echo HelperAdmin::bookmifyAdminContentStart('bookmify-user');
		?>
		
		<!-- PAGE WRAPPER -->
		<div class="bookmify_be_content_wrap bookmify_be_user_appoinments_page">
			
			<!-- PAGE TITLE -->
			<div class="bookmify_be_page_title">
				<h3><?php echo esc_html($this->get_page_title()); ?><span class="count"><?php echo HelperCabinet::appointmentsCount($this->userSlug,$this->userID);?></span></h3>
				
				<?php if($this->userSlug == 'emp'){ // ('employee') will be added to next updates ?>
				<div class="bookmify_be_add_new_item bookmify_be_add_new_app">
					<a href="#" class="bookmify_button add_new bookmify_add_new_button">
						<span class="text"><?php esc_html_e('Add New Appointment','bookmify');?></span>
						<span class="plus">
							<span class="icon"></span>
							<span class="bookmify_be_loader small">
								<span class="loader_process">
									<span class="ball"></span>
									<span class="ball"></span>
									<span class="ball"></span>
								</span>
							</span>
						</span>
					</a>
				</div>
				<?php }?>
				
			</div>
			<!-- /PAGE TITLE -->
			
			<!-- PAGE CONTENT -->
			<div class="bookmify_be_page_content">
				
				<div class="bookmify_be_user_appointments bookmify_be_role_<?php echo esc_attr($this->userSlug);?>">
					
					<!-- HIDDEN INFORMATION -->
					<div class="bookmify_be_hidden_info">
						<span class="nothing_found"><?php esc_html_e('Nothing to choose', 'bookmify');?></span>
						<span class="select_ser_emp"><?php esc_html_e('Select service and employee', 'bookmify');?></span>
						<span class="select_cus_emp_ser"><?php esc_html_e('Select customer, employee and service!', 'bookmify');?></span>
						<span class="approved_icon"><img class="bookmify_be_svg" src="<?php echo BOOKMIFY_ASSETS_URL.'img/checked.svg' ?>" alt="" /></span>
						<span class="pending_icon"><img class="bookmify_be_svg" src="<?php echo BOOKMIFY_ASSETS_URL.'img/circle.svg' ?>" alt="" /></span>
						<span class="canceled_icon"><img class="bookmify_be_svg" src="<?php echo BOOKMIFY_ASSETS_URL.'img/cancel.svg' ?>" alt="" /></span>
						<span class="rejected_icon"><img class="bookmify_be_svg" src="<?php echo BOOKMIFY_ASSETS_URL.'img/cancel.svg' ?>" alt="" /></span>
						<span class="person_icon"><img class="bookmify_be_svg" src="<?php echo BOOKMIFY_ASSETS_URL.'/img/add-user.svg' ?>" alt="" /></span>
						<span class="down_icon"><img class="bookmify_be_svg" src="<?php echo BOOKMIFY_ASSETS_URL.'/img/down.svg' ?>" alt="" /></span>
						<div class="info_remover">
							<div class="info">
								<div class="f_tooltip"><span>?</span>
									<div class="f_tooltip_content"><?php esc_html_e('This is the number of people who will come with this customer including the customer. This number varies depending on the selected service and employee.', 'bookmify'); ?></div>
								</div>
							</div>
							<div class="remover"><span><span></span></span></div>
						</div>
						<div class="no_extras">
							<div class="bookmify_be_infobox">
								<label><?php esc_html_e('This service doesn\'t have extra services', 'bookmify'); ?></label>
							</div>
						</div>
						<div class="canceled_text"><?php esc_html_e('Canceled', 'bookmify'); ?></div>
					</div>
					<!-- /HIDDEN INFORMATION -->
					
					<!-- PAGE FILTER -->
					<?php echo HelperCabinet::allFilter($this->userSlug,$this->userID);?>
					<!-- /PAGE FILTER -->
					
					<!-- PAGE HEADER -->
					<div class="bookmify_be_appointments_header">
						<div class="bookmify_be_appointments_header_in">
							<span class="list_date"><?php esc_html_e('Date', 'bookmify');?></span>
							<span class="list_customer"><?php esc_html_e('Customer', 'bookmify');?></span>
							<span class="list_service"><?php esc_html_e('Service', 'bookmify');?></span>
							<span class="list_employee"><?php esc_html_e('Employee', 'bookmify');?></span>
							<span class="list_price"><?php esc_html_e('Price', 'bookmify');?></span>
							<span class="list_duration"><?php esc_html_e('Duration', 'bookmify');?></span>
							<span class="list_status"><?php esc_html_e('Status', 'bookmify');?></span>
						</div>
					</div>
					<!-- /PAGE HEADER -->
					
					<!-- PAGE LIST -->
					<div class="bookmify_be_appointments_list">
						<?php echo wp_kses_post($this->appointments_list()); ?>
					</div>
					<!-- /PAGE LIST -->
					
				</div>
				
			</div>
			<!-- /PAGE CONTENT -->
			
		</div>
		<!-- /PAGE WRAPPER -->
		<?php echo HelperAdmin::bookmifyAdminContentEnd();
		
	}
	

	
	
	/*
	 * @since 1.0.0
	 * @access public
	*/
	public function appointments_list(){
		global $wpdb;
		$html		= '';
		if($this->userID != ''){
			if($this->userSlug == 'employee'){
				$userID 	= esc_sql($this->userID);
				$extraQuery = " AND a.employee_id=".$userID;
			}else if($this->userSlug == 'customer'){
				$userID 	= esc_sql($this->userID);
				$extraQuery = " AND ca.customer_id=".$userID;
			}

			$startDate 		= HelperTime::getCurrentDateTime();
			$startDate  	= date('Y-m-d').' 00:00:00';
			$endDate  		= date('Y-m-d', strtotime('+'.$this->daterange.' days')).' 23:59:59';


			$startDate		= esc_sql($startDate);
			$endDate		= esc_sql($endDate);
			$query 		 = "SELECT
									a.id appID,
									a.service_id appServiceID,
									a.employee_id appEmployeeID,
									a.location_id appLocationID,
									a.status appStatus,
									a.start_date appStartDate,
									a.end_date appEndDate,
									a.info appInfo,
									a.created_from appCreatedFrom

								FROM 	   	   {$wpdb->prefix}bmify_appointments a 
									LEFT JOIN {$wpdb->prefix}bmify_customer_appointments ca  ON ca.appointment_id = a.id

								WHERE (a.start_date BETWEEN '".$startDate."' AND '".$endDate."')".$extraQuery;

			$Querify  		= new Querify( $query, 'user_appointment' );
			$appointments   = $Querify->getData( $this->per_page );

			$html 			= '';
			$customlist 	= array();

			for( $i = 0; $i < count( $appointments->data ); $i++ ){

					$day 	= date_i18n(get_option('bookmify_be_date_format', 'd F, Y'), strtotime($appointments->data[$i]->appStartDate));

					if(!isset($customlist[$day]))
					{
					 $customlist[$day] 	= array();
					}

					$customlist[$day][] = $appointments->data[$i];

			}

			$html .= '<div class="appointments_list bookmify_be_list">';

			foreach($customlist as $day => $appointments){
				$list 		= '';
				$appCount 	= 0;
				$pendCount	= 0;
				foreach($appointments as $appointment){
					$appointmentID	= $appointment->appID;
					$duration 		= HelperCabinet::getDurationForAppointment($appointmentID, 'duration', $this->userSlug, $this->userID);
					$duration 		= Helper::bookmifyNumberToDuration($duration);
					$time			= date_i18n(get_option('bookmify_be_time_format', 'h:i a'), strtotime($appointment->appStartDate));

					$price 			= HelperCabinet::getPriceForAppointment($appointmentID,$this->userSlug,$this->userID);
					$price 			= Helper::bookmifyPriceCorrection($price);

					$status 		= $appointment->appStatus;
					if($this->userSlug == 'customer'){
						$query 		= "SELECT ca.status custStatus FROM {$wpdb->prefix}bmify_customer_appointments ca INNER JOIN {$wpdb->prefix}bmify_appointments a  ON ca.appointment_id = a.id WHERE ca.appointment_id=".$appointmentID." AND ca.customer_id=".$this->userID;
						$results 	= $wpdb->get_results( $query, OBJECT  );
						$status		= $results[0]->custStatus;
					}
					switch($status){
						case 'approved': 	$icon = 'checked'; 	$statusText = esc_html__('Approved', 'bookmify'); $appCount++; break;
						case 'pending': 	$icon = 'circle'; 	$statusText = esc_html__('Pending', 'bookmify'); $pendCount++;  break;
						case 'canceled':	$icon = 'cancel'; 	$statusText = esc_html__('Canceled', 'bookmify'); break;
						case 'rejected': 	$icon = 'cancel'; 	$statusText = esc_html__('Rejected', 'bookmify'); break;
					}
					$statusIcon 	= '<span class="icon"><img class="bookmify_be_svg '.$status.'" src="'.BOOKMIFY_ASSETS_URL.'img/'.$icon.'.svg" alt="" /></span>';
					$statusText 	= $statusIcon.'<span class="text">'.$statusText.'</span>';;


					$appointmentStartDate 	= date("Y-m-d H:i:s", strtotime($appointment->appStartDate));
					$today					= HelperTime::getCurrentDateTime();
					if($appointmentStartDate <= $today){
						$appDateStatus		= 'bookmify_be_closed_item';
						$appDateStatusBtn	= 'bookmify_be_closed_btn';
					}else{
						$appDateStatus 		= 'bookmify_be_open_item';
						$appDateStatusBtn 	= 'bookmify_be_open_btn';
					}
					if($this->userSlug == 'customer' && ($status == 'rejected' || $status == 'canceled')){
						$appDateStatus		= 'bookmify_be_closed_item';
						$appDateStatusBtn	= 'bookmify_be_closed_btn';
					}

					if(get_option( 'bookmify_be_mintime_tocancel', 'disabled' ) != 'disabled' && $this->userSlug == 'customer'){
						$cancelInterval		= get_option( 'bookmify_be_mintime_tocancel', 'disabled' ) * 3600;
						$cancelTime 		= HelperTime::getCurrentDateTimePlus($cancelInterval);
						if($cancelTime >= $appointmentStartDate){
							$appDateStatus		= 'bookmify_be_closed_item';
							$appDateStatusBtn	= 'bookmify_be_closed_btn';
						}
					}

					$list .= '<div data-entity-id="'.$appointmentID.'" class="bookmify_be_appointment_item bookmify_be_list_item '.$appDateStatus.'">
								<div class="bookmify_be_list_item_in">
									<div class="bookmify_appointment_heading bookmify_be_list_item_header">
										<div class="bookmify_heading_in header_in">


											<div class="appointment_info">
												<span class="appointment_time">
													<span>
														<img class="bookmify_be_svg" src="'.BOOKMIFY_ASSETS_URL.'img/clock.svg" alt="" />'.$time.'
													</span>
												</span>
												<span class="appointment_customer">
													<span>'.HelperCabinet::bookmifyGetCustomersCol($appointmentID, $this->userSlug, $this->userID).'</span>
												</span>
												<span class="appointment_service">
													<span>'.Helper::bookmifyGetServiceCol($appointment->appServiceID).'</span>
												</span>
												<span class="appointment_employee">
													<span>'.Helper::bookmifyGetEmployeeCol($appointment->appEmployeeID).'</span>
												</span>
												<span class="appointment_price">
													<span>'.$price.'</span>
												</span>
												<span class="appointment_duration">
													<span>'.$duration.'</span>
												</span>
												<span class="appointment_status '.$status.'">
													<span>'.$statusText.'</span>
												</span>
											</div>

											<div class="buttons_holder">

												<div class="btn_item btn_more" title="'.esc_attr__('Details', 'bookmify').'">
													<a href="#" class="bookmify_be_more" data-entity-id="'.$appointmentID.'">
														<img class="bookmify_be_svg more" src="'.BOOKMIFY_ASSETS_URL.'img/more.svg" alt="" />
														<span class="bookmify_be_loader small">
															<span class="loader_process">
																<span class="ball"></span>
																<span class="ball"></span>
																<span class="ball"></span>
															</span>
														</span>
													</a>
												</div>';
									if($this->userSlug == 'employee'){
										$list .= '<div class="btn_item btn_edit '.$appDateStatusBtn.'" title="'.esc_attr__('Edit', 'bookmify').'">
													<a href="#" class="bookmify_be_edit">
														<img class="bookmify_be_svg edit" src="'.BOOKMIFY_ASSETS_URL.'img/edit.svg" alt="" />
														<img class="bookmify_be_svg checked" src="'.BOOKMIFY_ASSETS_URL.'img/checked.svg" alt="" />
														<span class="bookmify_be_loader small">
															<span class="loader_process">
																<span class="ball"></span>
																<span class="ball"></span>
																<span class="ball"></span>
															</span>
														</span>
													</a>
												</div>';
									}
									if($this->userSlug == 'customer'){
										$list .= '<div class="btn_item btn_cancel '.$appDateStatusBtn.'" title="'.esc_attr__('Cancel', 'bookmify').'">
													<a href="#" class="bookmify_be_cancel">
														<img class="bookmify_be_svg" src="'.BOOKMIFY_ASSETS_URL.'img/cancel-circle-2.svg" alt="" />
														<span class="bookmify_be_loader small">
															<span class="loader_process">
																<span class="ball"></span>
																<span class="ball"></span>
																<span class="ball"></span>
															</span>
														</span>
													</a>
												</div>';
									}
										$list .='
											</div>

										</div>
									</div>
								</div>';

					$list .= '</div>';
				}

				$html .= 	'<div class="bookmify_be_day_separator">
								<span class="date_holder">'.$day.'</span>
								<span class="status_holder">
									<span class="app_count">
										<span class="app_count_c">'.$appCount.'</span>
										<span class="app_count_t">'.esc_html__('Approved', 'bookmify').'</span>
									</span>
									<span class="pend_count">
										<span class="pend_count_c">'.$pendCount.'</span>
										<span class="pend_count_t">'.esc_html__('Pending', 'bookmify').'</span>
									</span>
								</span>
							</div>';
				$html .= $list;

			}

			$html .= '</div>';

			$html .= $Querify->getPagination( 1, 'bookmify_be_pagination user_appointment');
		}
			
		
		return $html;
	}
	
	
	
	public function ajaxFilterUserAppointmentList(){
		global $wpdb;
		
		$html		= '';
		if($this->userSlug == 'employee'){
			$userID		= esc_sql($this->userID);
			$extraQuery = " a.employee_id=".$userID." AND";
		}else{
			$userID		= esc_sql($this->userID);
			$extraQuery = " ca.customer_id=".$userID." AND";
		}
		
		$isAjaxCall 		= false;
		$html 				= '';
		$page 				= 1;
		$filter 			= array();
		$search_text 		= '';
		$employeeIDs		= array();
		$filterByService	= array();
		$filterDateRange 	= array();
		
		if (!empty($_POST['bookmify_page'])) {
			$isAjaxCall 		= true;
			$page 				= $_POST['bookmify_page'];
			if(!empty($_POST['bookmify_services'])){
				$filterByService = $_POST['bookmify_services'];
			}
			$filterByCustomer	= $_POST['bookmify_customer'];
			$filterByEmployee	= $_POST['bookmify_employee'];
			$filterByStatus		= $_POST['bookmify_status'];
			$filterDateRange	= $_POST['bookmify_daterange'];
			
			
			
			$query 		 = "SELECT
								a.id appID,
								a.service_id appServiceID,
								a.employee_id appEmployeeID,
								a.location_id appLocationID,
								a.status appStatus,
								a.start_date appStartDate,
								a.end_date appEndDate,
								a.info appInfo,
								a.created_from appCreatedFrom
								
							FROM 	   	   {$wpdb->prefix}bmify_appointments a 
								LEFT JOIN {$wpdb->prefix}bmify_customer_appointments ca  ON ca.appointment_id = a.id
								
						    WHERE".$extraQuery;



			$html 			= '';
			$customlist 	= array();
			
			
			if(!empty($filterByService)){
				$filterByService = esc_sql($filterByService);
				$query .= " a.service_id IN (" . implode(",", array_map("intval", $filterByService)) . ") AND";
			}
			if($this->userSlug == 'employee'){
				if($filterByCustomer != ''){
					$filterByCustomer = esc_sql($filterByCustomer);
					$query .= " ca.customer_id = ".$filterByCustomer." AND";
				}
			}
			if($this->userSlug == 'customer'){
				if($filterByEmployee != ''){
					$filterByEmployee = esc_sql($filterByEmployee);
					$query .= " a.employee_id = ".$filterByEmployee." AND";
				}
			}
			if($filterByStatus != ''){
				if($this->userSlug == 'customer'){
					$filterByStatus = esc_sql($filterByStatus);
					$query .= " ca.status = '".$filterByStatus."' AND";
				}else{
					$filterByStatus = esc_sql($filterByStatus);
					$query .= " a.status = '".$filterByStatus."' AND";
				}
			}
			
			if(!empty($filterDateRange)){
				$filterDateRange = esc_sql($filterDateRange);
				$query .= " (a.start_date BETWEEN '".$filterDateRange[0]."' AND '".$filterDateRange[1]."') AND";
			}
			
			
			
			$query = rtrim($query, 'AND');
			$query = rtrim($query, 'WHERE');
			
			
			
			$Querify  		= new Querify( $query, 'user_appointment' );
			$appointments	= $Querify->getData( $this->per_page, $page, $filter );
			
			$html 			= '';
			$customlist 	= array();

			for( $i = 0; $i < count( $appointments->data ); $i++ ){

					$day 	= date_i18n(get_option('bookmify_be_date_format', 'd F, Y'), strtotime($appointments->data[$i]->appStartDate));

					if(!isset($customlist[$day]))
					{
					 $customlist[$day] 	= array();
					}

					$customlist[$day][] = $appointments->data[$i];

			}

			$html .= '<div class="appointments_list bookmify_be_list">';
			foreach($customlist as $day => $appointments){
				$list 		= '';
				$appCount 	= 0;
				$pendCount	= 0;
				foreach($appointments as $appointment){
						$appointmentID	= $appointment->appID;
						$duration 		= HelperCabinet::getDurationForAppointment($appointmentID, 'duration', $this->userSlug, $this->userID);
						$duration 		= Helper::bookmifyNumberToDuration($duration);
						$time			= date_i18n(get_option('bookmify_be_time_format', 'h:i a'), strtotime($appointment->appStartDate));

						$price 			= HelperCabinet::getPriceForAppointment($appointmentID,$this->userSlug,$this->userID);
						$price 			= Helper::bookmifyPriceCorrection($price);

						$status 		= $appointment->appStatus;
						if($this->userSlug == 'customer'){
							$queryy 		= "SELECT ca.status custStatus FROM {$wpdb->prefix}bmify_customer_appointments ca INNER JOIN {$wpdb->prefix}bmify_appointments a  ON ca.appointment_id = a.id WHERE ca.appointment_id=".$appointmentID." AND ca.customer_id=".$this->userID;
							$results 	= $wpdb->get_results( $queryy, OBJECT  );
							$status		= $results[0]->custStatus;
						}
						switch($status){
							case 'approved': 	$icon = 'checked'; 	$statusText = esc_html__('Approved', 'bookmify'); $appCount++; break;
							case 'pending': 	$icon = 'circle'; 	$statusText = esc_html__('Pending', 'bookmify'); $pendCount++;  break;
							case 'canceled':	$icon = 'cancel'; 	$statusText = esc_html__('Canceled', 'bookmify'); break;
							case 'rejected': 	$icon = 'cancel'; 	$statusText = esc_html__('Rejected', 'bookmify'); break;
						}
						$statusIcon 	= '<span class="icon"><img class="bookmify_be_svg '.$status.'" src="'.BOOKMIFY_ASSETS_URL.'img/'.$icon.'.svg" alt="" /></span>';
						$statusText 	= $statusIcon.'<span class="text">'.$statusText.'</span>';;
					
						$appointmentStartDate 	= date("Y-m-d H:i:s", strtotime($appointment->appStartDate));
						$today					= HelperTime::getCurrentDateTime();
						if($appointmentStartDate <= $today){
							$appDateStatus		= 'bookmify_be_closed_item';
							$appDateStatusBtn	= 'bookmify_be_closed_btn';
						}else{
							$appDateStatus 		= 'bookmify_be_open_item';
							$appDateStatusBtn 	= 'bookmify_be_open_btn';
						}
						if($this->userSlug == 'customer' && ($status == 'rejected' || $status == 'canceled')){
							$appDateStatus		= 'bookmify_be_closed_item';
							$appDateStatusBtn	= 'bookmify_be_closed_btn';
						}
						$list .= '<div data-entity-id="'.$appointmentID.'" class="bookmify_be_appointment_item bookmify_be_list_item bookmify_be_animated '.$appDateStatus.'">

									<div class="bookmify_be_list_item_in">
										<div class="bookmify_appointment_heading bookmify_be_list_item_header">
											<div class="bookmify_heading_in header_in">


												<div class="appointment_info">
													<span class="appointment_time">
														<span>
															<img class="bookmify_be_svg" src="'.BOOKMIFY_ASSETS_URL.'img/clock.svg" alt="" />'.$time.'
														</span>
													</span>
													<span class="appointment_customer">
														<span>'.HelperCabinet::bookmifyGetCustomersCol($appointmentID, $this->userSlug, $this->userID).'</span>
													</span>
													<span class="appointment_service">
														<span>'.Helper::bookmifyGetServiceCol($appointment->appServiceID).'</span>
													</span>
													<span class="appointment_employee">
														<span>'.Helper::bookmifyGetEmployeeCol($appointment->appEmployeeID).'</span>
													</span>
													<span class="appointment_price">
														<span>'.$price.'</span>
													</span>
													<span class="appointment_duration">
														<span>'.$duration.'</span>
													</span>
													<span class="appointment_status '.$status.'">
														<span>'.$statusText.'</span>
													</span>
												</div>
												
												<div class="buttons_holder">
												
													<div class="btn_item btn_more">
														<a href="#" class="bookmify_be_more" data-entity-id="'.$appointmentID.'">
															<img class="bookmify_be_svg more" src="'.BOOKMIFY_ASSETS_URL.'img/more.svg" alt="" />
															<span class="bookmify_be_loader small">
																<span class="loader_process">
																	<span class="ball"></span>
																	<span class="ball"></span>
																	<span class="ball"></span>
																</span>
															</span>
														</a>
													</div>';
									if($this->userSlug == 'employee'){
										$list .=   '<div class="btn_item btn_edit '.$appDateStatusBtn.'">
														<a href="#" class="bookmify_be_edit">
															<img class="bookmify_be_svg edit" src="'.BOOKMIFY_ASSETS_URL.'img/edit.svg" alt="" />
															<img class="bookmify_be_svg checked" src="'.BOOKMIFY_ASSETS_URL.'img/checked.svg" alt="" />
															<span class="bookmify_be_loader small">
																<span class="loader_process">
																	<span class="ball"></span>
																	<span class="ball"></span>
																	<span class="ball"></span>
																</span>
															</span>
														</a>
													</div>';
									}
									if($this->userSlug == 'customer'){
										$list .= '<div class="btn_item btn_cancel '.$appDateStatusBtn.'" title="'.esc_attr__('Cancel', 'bookmify').'">
													<a href="#" class="bookmify_be_cancel">
														<img class="bookmify_be_svg" src="'.BOOKMIFY_ASSETS_URL.'img/cancel-circle-2.svg" alt="" />
														<span class="bookmify_be_loader small">
															<span class="loader_process">
																<span class="ball"></span>
																<span class="ball"></span>
																<span class="ball"></span>
															</span>
														</span>
													</a>
												</div>';
									}
										$list .='
												</div>

											</div>
										</div>
									</div>';

						$list .= '</div>';

				}
				$html .= 	'<div class="bookmify_be_day_separator">
								<span class="date_holder">'.$day.'</span>
								<span class="status_holder">
									<span class="app_count">
										<span class="app_count_c">'.$appCount.'</span>
										<span class="app_count_t">'.esc_html__('Approved', 'bookmify').'</span>
									</span>
									<span class="pend_count">
										<span class="pend_count_c">'.$pendCount.'</span>
										<span class="pend_count_t">'.esc_html__('Pending', 'bookmify').'</span>
									</span>
								</span>
							</div>';
				$html .= $list;
			}

			$html .= '</div>';
			

			$html .= $Querify->getPagination( 1, 'bookmify_be_pagination user_appointment');
			
			
		}
		
		$buffy = '';
		if ( $html != NULL ) {
			$buffy .= $html; 
		}

		// remove whitespaces form the ajax HTML
		$search = array(
			'/\>[^\S ]+/s',  // strip whitespaces after tags, except space
			'/[^\S ]+\</s',  // strip whitespaces before tags, except space
			'/(\s)+/s'       // shorten multiple whitespace sequences
		);
		$replace = array(
			'>',
			'<',
			'\\1'
		);
		$buffy = preg_replace($search, $replace, $buffy);
		$query = preg_replace($search, $replace, $query);

		$buffyArray = array(
			'bookmify_be_data' 		=> $buffy, // employeeIDs filter['list']
			'bookmify_be_p' 		=> $query
		);

		if ( true === $isAjaxCall ){die(json_encode($buffyArray));}
		else{return json_encode($buffyArray);}
	}
	
	
	public function ajaxQueryEditUserAppointment(){
		global $wpdb;
		$isAjaxCall 					= true;
		
		$hack							= 0;
		$arrayToSendNotification			= array();
		// update details
		if (!empty($_POST['details'])) {
			$isAjaxCall					= true;
			$details 					= json_decode(stripslashes($_POST['details']));
			$empID 						= $details->empID;
			if($empID != $this->userID){
				$hack					= 1;
			}else{
				$appointmentID 			= $details->ID;
				$serID 					= $details->serID;
				$dateApp 				= $details->dateApp;
				$timeApp 				= $details->timeApp;
				$info 					= $details->info;
				$status 				= $_POST['status'];
				$timeAppStart			= date("H:i:s",strtotime($timeApp));
				$timeAppEnd				= date("H:i:s",strtotime($timeApp) + $_POST['duration']);
				$dateApp 				= date("Y-m-d",strtotime($dateApp));
				$startDate				= $dateApp . " " . $timeAppStart;
				$endDate				= $dateApp . " " . $timeAppEnd;
				$startDate				= date("Y-m-d H:i:s", strtotime($startDate));
				$endDate				= date("Y-m-d H:i:s", strtotime($endDate));
				$checked				= $details->checked;


				// notification	|| send notification to employee
				if($checked == 'public'){

					// get selected customers to send a notification
					$allCustomers 		= json_decode(stripslashes($_POST['allCustomers']));
					$customerIDs		= array();
					foreach($allCustomers as $customer){
						$customerIDs[] 	= $customer->ID;
					}
					$customerCount		= count($allCustomers);
					$customerName		= '';
					$customerEmail		= '';
					$customerPhone		= '';
					if($customerCount == 1){
						$customerName	= Helper::bookmifyGetCustomerCol($customerIDs[0]);
						$customerEmail	= Helper::bookmifyGetCustomerCol($customerIDs[0], 'email');
						$customerPhone	= Helper::bookmifyGetCustomerCol($customerIDs[0], 'phone');
					}
					// ********************************************

					$infoObject			= new \stdClass();
					$appointmentID 		= esc_sql($appointmentID);
					$query 				= "SELECT * FROM {$wpdb->prefix}bmify_appointments WHERE id=".$appointmentID;
					$results 			= $wpdb->get_results( $query, OBJECT  );
					$oldServiceID		= $results[0]->service_id;
					$oldEmployeeID		= $results[0]->employee_id;
					$oldStatus			= $results[0]->status;
					$oldDate 			= date("Y-m-d",strtotime($results[0]->start_date));
					$oldTime 			= date("H:i:s",strtotime($results[0]->start_date));
					if($oldEmployeeID == $empID){ // если работник тотже
						// 1. если сервис изменился
						if($oldServiceID != $serID){
							$serviceName					= Helper::bookmifyGetServiceCol($oldServiceID);
							$employeeEmail					= Helper::bookmifyGetEmployeeCol($oldEmployeeID, 'email');
							$infoObject->sendTo				= 'employee';
							$infoObject->appID				= $appointmentID;
							$infoObject->userID				= $empID;
							$infoObject->service_name 		= $serviceName;
							$infoObject->appointment_date 	= $oldDate;
							$infoObject->appointment_time 	= $oldTime;
							$infoObject->status			 	= 'rejected';
							$infoObject->employee_email	 	= $employeeEmail;
							$infoObject->customer_count	 	= $customerCount;
							$infoObject->customer_name	 	= $customerName;
							$infoObject->customer_email	 	= $customerEmail;
							$infoObject->customer_phone	 	= $customerPhone;
							$arrayToSendNotification[]		= array($infoObject, '', 'employee');
//							$this->pretraintmentToSendNotification($infoObject); // отправка уведомление старому работнику об отмене встречи
							
							
							$infoObject						= new \stdClass();
							$serviceName					= Helper::bookmifyGetServiceCol($serID);
							$employeeEmail					= Helper::bookmifyGetEmployeeCol($empID, 'email');
							$infoObject->sendTo				= 'employee';
							$infoObject->appID				= $appointmentID;
							$infoObject->userID				= $empID;
							$infoObject->service_name 		= $serviceName;
							$infoObject->appointment_date 	= $dateApp;
							$infoObject->appointment_time 	= $timeAppStart;
							$infoObject->status			 	= $status;
							$infoObject->employee_email	 	= $employeeEmail;
							$infoObject->customer_count	 	= $customerCount;
							$infoObject->customer_name	 	= $customerName;
							$infoObject->customer_email	 	= $customerEmail;
							$infoObject->customer_phone	 	= $customerPhone;
							$arrayToSendNotification[]		= array($infoObject, '', 'employee');
//							$this->pretraintmentToSendNotification($infoObject); // отправка уведомление работнику о встречи, с уже другим сервисом
						}else if(($oldStatus != $status) && (($status == 'rejected') || ($status == 'canceled'))){
							// 2. Если статус изменился и статус rejected или canceled
							$infoObject						= new \stdClass();
							$serviceName					= Helper::bookmifyGetServiceCol($serID);
							$employeeEmail					= Helper::bookmifyGetEmployeeCol($empID, 'email');
							$infoObject->sendTo				= 'employee';
							$infoObject->appID				= $appointmentID;
							$infoObject->userID				= $empID;
							$infoObject->service_name 		= $serviceName;
							$infoObject->appointment_date 	= $dateApp;
							$infoObject->appointment_time 	= $timeAppStart;
							$infoObject->status			 	= $status;
							$infoObject->employee_email	 	= $employeeEmail;
							$infoObject->customer_count	 	= $customerCount;
							$infoObject->customer_name	 	= $customerName;
							$infoObject->customer_email	 	= $customerEmail;
							$infoObject->customer_phone	 	= $customerPhone;
							$arrayToSendNotification[]		= array($infoObject, '', 'employee');
//							$this->pretraintmentToSendNotification($infoObject); // отправка уведомление работнику о встречи, с другим статусом
						}else if(($oldTime != $timeAppStart) || ($oldDate != $dateApp)){
							//  3. Если изменилось время или дата
							$serviceName					= Helper::bookmifyGetServiceCol($serID);
							$employeeEmail					= Helper::bookmifyGetEmployeeCol($empID, 'email');
							$infoObject->sendTo				= 'employee';
							$infoObject->appID				= $appointmentID;
							$infoObject->userID				= $empID;
							$infoObject->service_name 		= $serviceName;
							$infoObject->appointment_date 	= $dateApp;
							$infoObject->appointment_time 	= $timeAppStart;
							$infoObject->status			 	= $status;
							$infoObject->employee_email	 	= $employeeEmail;
							$infoObject->customer_count	 	= $customerCount;
							$infoObject->customer_name	 	= $customerName;
							$infoObject->customer_email	 	= $customerEmail;
							$infoObject->customer_phone	 	= $customerPhone;
							$arrayToSendNotification[]		= array($infoObject, 'rescheduled', 'employee');
//							$this->pretraintmentToSendNotification($infoObject, 'rescheduled'); // отправка уведомление работнику о переносе встречи
						}else if($oldStatus != $status){
							// 3. Если изменился статус (так написано, потому что наверху уже была проверка на статус)
							$serviceName					= Helper::bookmifyGetServiceCol($serID);
							$employeeEmail					= Helper::bookmifyGetEmployeeCol($empID, 'email');
							$infoObject->sendTo				= 'employee';
							$infoObject->appID				= $appointmentID;
							$infoObject->userID				= $empID;
							$infoObject->service_name 		= $serviceName;
							$infoObject->appointment_date 	= $dateApp;
							$infoObject->appointment_time 	= $timeAppStart;
							$infoObject->status			 	= $status;
							$infoObject->employee_email	 	= $employeeEmail;
							$infoObject->customer_count	 	= $customerCount;
							$infoObject->customer_name	 	= $customerName;
							$infoObject->customer_email	 	= $customerEmail;
							$infoObject->customer_phone	 	= $customerPhone;
							$arrayToSendNotification[]		= array($infoObject, '', 'employee');
//							$this->pretraintmentToSendNotification($infoObject); // отправка уведомление работнику о встречи, с новым статусом
						}
					}
				}

				$wpdb->query($wpdb->prepare( "UPDATE {$wpdb->prefix}bmify_appointments SET service_id=%d, employee_id=%d, start_date=%s, end_date=%s, info=%s, status=%s WHERE id=%d", $serID, $empID, $startDate, $endDate, $info, $status, $appointmentID));
			}
				
		}

		// update customers and their extra services

		if (!empty($_POST['allCustomers']) && $hack == 0) {
			$isAjaxCall				= true;
			$allCustomers 			= json_decode(stripslashes($_POST['allCustomers']));
			$paymentPrice		 	= 0;
			$oldCustomerIDs			= array();
			$chosenCustomerIDs		= array();

			$allCustomersAsArray	= [];
			foreach($allCustomers as $customer){
				$customerID											= $customer->ID;
				$serPrice											= $customer->serPrice;
				$peopleCount 										= $customer->peopleCount;
				$status												= $customer->status;
				$chosenCustomerIDs[] 								= $customerID;
				$allCustomersAsArray[$customerID]['serPrice'] 		= $customer->serPrice;
				$allCustomersAsArray[$customerID]['peopleCount'] 	= $customer->peopleCount;
				$allCustomersAsArray[$customerID]['status']			= $customer->status;
				$allCustomersAsArray[$customerID]['extras']			= $customer->extras;
			}


			$appointmentID 			= esc_sql($appointmentID);
			$query 					= "SELECT customer_id FROM {$wpdb->prefix}bmify_customer_appointments WHERE appointment_id=".$appointmentID;
			$results 				= $wpdb->get_results( $query, OBJECT  );
			foreach($results as $result){
				$oldCustomerIDs[] 	= $result->customer_id;
			}


			$existingCustomers 		= array_values(array_intersect($oldCustomerIDs, $chosenCustomerIDs)); 	// existing Customer Appointment
			$newCustomers 			= array_values(array_diff($chosenCustomerIDs,$oldCustomerIDs)); 		// new 		Customer Appointment
			$releasedCustomers 		= array_values(array_diff($oldCustomerIDs,$chosenCustomerIDs)); 		// released Customer Appointment

			// Update existing Customer Appointment
			if(!empty($existingCustomers)){
				foreach($existingCustomers as $existingCustomer){
					$serPrice		  	= $allCustomersAsArray[$existingCustomer]['serPrice'];
					$peopleCount 		= $allCustomersAsArray[$existingCustomer]['peopleCount'];
					$status				= $allCustomersAsArray[$existingCustomer]['status'];
					$extras				= $allCustomersAsArray[$existingCustomer]['extras'];
					$paymentPrice		+= ($peopleCount * $serPrice);

					$appointmentID 		= esc_sql($appointmentID);
					$existingCustomer 	= esc_sql($existingCustomer);
					$query 				= "SELECT status,id,number_of_people FROM {$wpdb->prefix}bmify_customer_appointments WHERE customer_id=".$existingCustomer." AND appointment_id=".$appointmentID;
					$results 			= $wpdb->get_results( $query, OBJECT  );
					$oldStatus			= $results[0]->status;
					$custAppID 			= $results[0]->id;
					$oldPeopleCount		= $results[0]->number_of_people;

					if($status != $oldStatus){
						$statusChangedDate = HelperTime::getCurrentDateTime();
						$wpdb->query($wpdb->prepare( "UPDATE {$wpdb->prefix}bmify_customer_appointments SET number_of_people=%d, price=%f, status=%s, status_changed_at=%s WHERE customer_id=%d AND appointment_id=%d", $peopleCount, $serPrice, $status, $statusChangedDate, $existingCustomer, $appointmentID));
					}else{
						$wpdb->query($wpdb->prepare( "UPDATE {$wpdb->prefix}bmify_customer_appointments SET number_of_people=%d, price=%f, status=%s WHERE customer_id=%d AND appointment_id=%d", $peopleCount, $serPrice, $status, $existingCustomer, $appointmentID));
					}

					if($_POST['haveExtras'] == "yes"){
						// if have extras
						$oldFolk 		= array();
						$chosenFolk 	= array();
						$allExtras		= array();
						foreach($extras as $extra){
							$extraID				= $extra->ID;
							$quantity				= $extra->quantity;
							$price					= $extra->price;
							$allExtras[$extraID] 	= array('price' => $price,'quantity' => $quantity);
							$chosenFolk[] 			= $extraID;
						}
						$custAppID 			= esc_sql($custAppID);
						$query 				= "SELECT extra_id FROM {$wpdb->prefix}bmify_customer_appointments_extras WHERE customer_appointment_id=".$custAppID;
						$results 			= $wpdb->get_results( $query, OBJECT  );
						foreach($results as $result){
							$oldFolk[] 		= $result->extra_id;
						}

						$existingExtras 	= array_values(array_intersect($oldFolk, $chosenFolk)); // existing extra
						$newExtras	 		= array_values(array_diff($chosenFolk,$oldFolk)); 		// new 		extra
						$releasedExtras 	= array_values(array_diff($oldFolk,$chosenFolk)); 		// released extra

						// Update existing extra
						if(!empty($existingExtras)){
							foreach($existingExtras as $existingExtra){
								$quantity	= $allExtras[$existingExtra]['quantity'];
								$price		= $allExtras[$existingExtra]['price'];
								$wpdb->query($wpdb->prepare( "UPDATE {$wpdb->prefix}bmify_customer_appointments_extras SET price=%f, quantity=%d WHERE customer_appointment_id=%d AND extra_id=%d", $price, $quantity, $custAppID, $existingExtra));
							}
						}

						// Insert new extra
						if(!empty($newExtras)){
							foreach($newExtras as $newExtra){
								$quantity	= $allExtras[$newExtra]['quantity'];
								$price		= $allExtras[$newExtra]['price'];
								$wpdb->query($wpdb->prepare( "INSERT INTO {$wpdb->prefix}bmify_customer_appointments_extras ( customer_appointment_id, extra_id, quantity, price ) VALUES ( %d, %d, %d, %f )", $custAppID, $newExtra, $quantity, $price ));
							}
						}

						// Delete released extra
						if(!empty($releasedExtras)){
							foreach($releasedExtras as $releasedExtra){
								$wpdb->query($wpdb->prepare( "DELETE FROM {$wpdb->prefix}bmify_customer_appointments_extras WHERE customer_appointment_id=%d AND extra_id=%d", $custAppID, $releasedExtra));
							}
						}

						// Calculate payment price
						$custAppID 			= esc_sql($custAppID);
						$query 				= "SELECT quantity, price FROM {$wpdb->prefix}bmify_customer_appointments_extras WHERE customer_appointment_id=".$custAppID;
						$results 			= $wpdb->get_results( $query, OBJECT  );
						foreach($results as $result){
							$paymentPrice 	+= ($result->quantity * $result->price * $peopleCount);
						}
					}else{
						// if does not exist any extra
						$wpdb->query($wpdb->prepare( "DELETE FROM {$wpdb->prefix}bmify_customer_appointments_extras WHERE customer_appointment_id=%d", $custAppID));
					}

					// get payment ID
					$custAppID 		= esc_sql($custAppID);
					$query 			= "SELECT payment_id FROM {$wpdb->prefix}bmify_customer_appointments WHERE id=".$custAppID;
					$results 		= $wpdb->get_results( $query, OBJECT  );
					$paymentID 		= $results[0]->payment_id;

					// get OLD total price
					$paymentID		= esc_sql($paymentID);
					$query 			= "SELECT total_price FROM {$wpdb->prefix}bmify_payments WHERE id=".$paymentID;
					$results 		= $wpdb->get_results( $query, OBJECT  );
					$oldTotalPrice	= $results[0]->total_price;
					// change price
					$wpdb->query($wpdb->prepare( "UPDATE {$wpdb->prefix}bmify_payments SET total_price=%f WHERE id=%d", $paymentPrice, $paymentID));


					// notification	|| send notification to existing customer
					if($checked == 'public'){
						$infoObject								= new \stdClass();
						// если работник тотже
							if($oldEmployeeID == $empID){
								// 1. если сервис изменился
								if($oldServiceID != $serID){									
									$serviceName					= Helper::bookmifyGetServiceCol($serID);
									$customerName					= Helper::bookmifyGetCustomerCol($existingCustomer);
									$customerEmail					= Helper::bookmifyGetCustomerCol($existingCustomer, 'email');
									$infoObject->sendTo				= 'customer';
									$infoObject->appID				= $appointmentID;
									$infoObject->userID				= $existingCustomer;
									$infoObject->service_name 		= $serviceName;
									$infoObject->appointment_date 	= $dateApp;
									$infoObject->appointment_time 	= $timeAppStart;
									$infoObject->status			 	= 'rejected';
									$infoObject->customer_name	 	= $customerName;
									$infoObject->customer_email	 	= $customerEmail;
									$arrayToSendNotification[]		= array($infoObject, '', 'customer');
//									$this->pretraintmentToSendNotification($infoObject); // отправка уведомление клиенту об отмене встречи
									
									
									$infoObject						= new \stdClass();
									$serviceName					= Helper::bookmifyGetServiceCol($serID);
									$customerName					= Helper::bookmifyGetCustomerCol($existingCustomer);
									$customerEmail					= Helper::bookmifyGetCustomerCol($existingCustomer, 'email');
									$infoObject->sendTo				= 'customer';
									$infoObject->appID				= $appointmentID;
									$infoObject->userID				= $existingCustomer;
									$infoObject->service_name 		= $serviceName;
									$infoObject->appointment_date 	= $dateApp;
									$infoObject->appointment_time 	= $timeAppStart;
									$infoObject->status			 	= $status;
									$infoObject->customer_name	 	= $customerName;
									$infoObject->customer_email	 	= $customerEmail;
									$arrayToSendNotification[]		= array($infoObject, '', 'customer');
//									$this->pretraintmentToSendNotification($infoObject); // отправка уведомление клиенту о встречи, с уже другим сервисом


								}else if(($oldStatus != $status) && (($status == 'rejected') || ($status == 'canceled'))){
									// 2. Если статус изменился и статус rejected или canceled
									$infoObject						= new \stdClass();
									$serviceName					= Helper::bookmifyGetServiceCol($serID);
									$customerName					= Helper::bookmifyGetCustomerCol($existingCustomer);
									$customerEmail					= Helper::bookmifyGetCustomerCol($existingCustomer, 'email');
									$infoObject->sendTo				= 'customer';
									$infoObject->appID				= $appointmentID;
									$infoObject->userID				= $existingCustomer;
									$infoObject->service_name 		= $serviceName;
									$infoObject->appointment_date 	= $dateApp;
									$infoObject->appointment_time 	= $timeAppStart;
									$infoObject->status			 	= $status;
									$infoObject->customer_name	 	= $customerName;
									$infoObject->customer_email	 	= $customerEmail;
									$arrayToSendNotification[]		= array($infoObject, '', 'customer');
//									$this->pretraintmentToSendNotification($infoObject); // отправка уведомление клиенту о встречи, с другим статусом
								}else if(($oldTime != $timeAppStart) || ($oldDate != $dateApp)){
									//  3. Если изменилось время или дата
									$infoObject						= new \stdClass();
									$serviceName					= Helper::bookmifyGetServiceCol($serID);
									$customerName					= Helper::bookmifyGetCustomerCol($existingCustomer);
									$customerEmail					= Helper::bookmifyGetCustomerCol($existingCustomer, 'email');
									$infoObject->sendTo				= 'customer';
									$infoObject->appID				= $appointmentID;
									$infoObject->userID				= $existingCustomer;
									$infoObject->service_name 		= $serviceName;
									$infoObject->appointment_date 	= $dateApp;
									$infoObject->appointment_time 	= $timeAppStart;
									$infoObject->status			 	= $status;
									$infoObject->customer_name	 	= $customerName;
									$infoObject->customer_email	 	= $customerEmail;
									$arrayToSendNotification[]		= array($infoObject, 'rescheduled', 'customer');
//									$this->pretraintmentToSendNotification($infoObject, 'rescheduled'); // отправка уведомление клиенту о переносе встречи
								}else if($oldStatus != $status){
									// 3. Если изменился статус (так написано, потому что наверху уже была проверка на статус)
									$infoObject						= new \stdClass();
									$serviceName					= Helper::bookmifyGetServiceCol($serID);
									$customerName					= Helper::bookmifyGetCustomerCol($existingCustomer);
									$customerEmail					= Helper::bookmifyGetCustomerCol($existingCustomer, 'email');
									$infoObject->sendTo				= 'customer';
									$infoObject->appID				= $appointmentID;
									$infoObject->userID				= $existingCustomer;
									$infoObject->service_name 		= $serviceName;
									$infoObject->appointment_date 	= $dateApp;
									$infoObject->appointment_time 	= $timeAppStart;
									$infoObject->status			 	= $status;
									$infoObject->customer_name	 	= $customerName;
									$infoObject->customer_email	 	= $customerEmail;
									$arrayToSendNotification[]		= array($infoObject, '', 'customer');
//									$this->pretraintmentToSendNotification($infoObject); // отправка уведомление клиенту о встречи, с новым статусом
								}
							}else{ // если работник изменился
								$infoObject						= new \stdClass();
								$serviceName					= Helper::bookmifyGetServiceCol($oldServiceID);
								$customerName					= Helper::bookmifyGetCustomerCol($existingCustomer);
								$customerEmail					= Helper::bookmifyGetCustomerCol($existingCustomer, 'email');
								$infoObject->sendTo				= 'customer';
								$infoObject->appID				= $appointmentID;
								$infoObject->userID				= $existingCustomer;
								$infoObject->service_name 		= $serviceName;
								$infoObject->appointment_date 	= $oldDate;
								$infoObject->appointment_time 	= $oldTime;
								$infoObject->status			 	= 'rejected';
								$infoObject->customer_name	 	= $customerName;
								$infoObject->customer_email	 	= $customerEmail;
								$arrayToSendNotification[]		= array($infoObject, '', 'customer');
//								$this->pretraintmentToSendNotification($infoObject); // отправка уведомление клиенту об отмене встречи
								
								$infoObject						= new \stdClass();
								$serviceName					= Helper::bookmifyGetServiceCol($serID);
								$customerName					= Helper::bookmifyGetCustomerCol($existingCustomer);
								$customerEmail					= Helper::bookmifyGetCustomerCol($existingCustomer, 'email');
								$infoObject->sendTo				= 'customer';
								$infoObject->appID				= $appointmentID;
								$infoObject->userID				= $existingCustomer;
								$infoObject->service_name 		= $serviceName;
								$infoObject->appointment_date 	= $dateApp;
								$infoObject->appointment_time 	= $timeAppStart;
								$infoObject->status			 	= $status;
								$infoObject->customer_name	 	= $customerName;
								$infoObject->customer_email	 	= $customerEmail;
								$arrayToSendNotification[]		= array($infoObject, '', 'customer');
//								$this->pretraintmentToSendNotification($infoObject); // отправка уведомление клиенту о новом работнике
							}

					}

					$paymentPrice 		= 0;
				}
			}

			$createdDate 				= HelperTime::getCurrentDateTime();
			// Insert new Customer Appointment
			if(!empty($newCustomers)){
				foreach($newCustomers as $newCustomer){
					$serPrice		  	= $allCustomersAsArray[$newCustomer]['serPrice'];
					$peopleCount 		= $allCustomersAsArray[$newCustomer]['peopleCount'];
					$status				= $allCustomersAsArray[$newCustomer]['status'];
					$extras				= $allCustomersAsArray[$newCustomer]['extras'];
					$paymentPrice		+= ($peopleCount * $serPrice);
					$wpdb->query($wpdb->prepare( "INSERT INTO {$wpdb->prefix}bmify_customer_appointments ( customer_id, appointment_id, number_of_people, price, status, created_date ) VALUES ( %d, %d, %d, %f, %s, %s )", $newCustomer, $appointmentID, $peopleCount, $serPrice, $status, $createdDate ));

					// get new customer appointment id
					$query 			= "SELECT id FROM {$wpdb->prefix}bmify_customer_appointments ORDER BY id DESC LIMIT 1;";
					$results 		= $wpdb->get_results( $query, OBJECT  );
					$custAppID 		= $results[0]->id;

					if($_POST['haveExtras'] == "yes"){
						// if have extras
						foreach($extras as $extra){
							$extraID	= $extra->ID;
							$quantity	= $extra->quantity;
							$price		= $extra->price;
							$wpdb->query($wpdb->prepare( "INSERT INTO {$wpdb->prefix}bmify_customer_appointments_extras ( customer_appointment_id, extra_id, quantity, price ) VALUES ( %d, %d, %d, %f )", $custAppID, $extraID, $quantity, $price ));
						}
						$custAppID			= esc_sql($custAppID);
						$query 				= "SELECT quantity, price FROM {$wpdb->prefix}bmify_customer_appointments_extras WHERE customer_appointment_id=".$custAppID;
						$results 			= $wpdb->get_results( $query, OBJECT  );
						foreach($results as $result){
							$paymentPrice 	+= ($result->quantity * $result->price * $peopleCount);
						}
					}else{
						// if does not exist any extra
						$wpdb->query($wpdb->prepare( "DELETE FROM {$wpdb->prefix}bmify_customer_appointments_extras WHERE customer_appointment_id=%d", $custAppID));
					}
					// insert new payment
					$wpdb->query($wpdb->prepare( "INSERT INTO {$wpdb->prefix}bmify_payments ( total_price, created_date ) VALUES ( %f, %s )", $paymentPrice, $createdDate ));

					// get this payment ID
					$query 			= "SELECT id FROM {$wpdb->prefix}bmify_payments ORDER BY id DESC LIMIT 1;";
					$results 		= $wpdb->get_results( $query, OBJECT  );
					$paymentID 		= $results[0]->id;

					// insert paymentID to last customer appointment 
					$wpdb->query($wpdb->prepare( "UPDATE {$wpdb->prefix}bmify_customer_appointments SET payment_id=%d WHERE id=%d", $paymentID, $custAppID));

					// notification	|| send notification to new customer
					if($checked == 'public'){
						$infoObject						= new \stdClass();
						$serviceName					= Helper::bookmifyGetServiceCol($serID);
						$customerName					= Helper::bookmifyGetCustomerCol($newCustomer);
						$customerEmail					= Helper::bookmifyGetCustomerCol($newCustomer, 'email');
						$infoObject->sendTo				= 'customer';
						$infoObject->appID				= $appointmentID;
						$infoObject->userID				= $newCustomer;
						$infoObject->service_name 		= $serviceName;
						$infoObject->appointment_date 	= $dateApp;
						$infoObject->appointment_time 	= $timeAppStart;
						$infoObject->status			 	= $status;
						$infoObject->customer_name	 	= $customerName;
						$infoObject->customer_email	 	= $customerEmail;
						$arrayToSendNotification[]		= array($infoObject, '', 'customer');
//						$this->pretraintmentToSendNotification($infoObject); // отправка уведомление новому клиенту
					}

					$paymentPrice = 0;
				}
			}

			// Delete released Customer Appointment
			if(!empty($releasedCustomers)){
				foreach($releasedCustomers as $releasedCustomer){
					// получение нужных данных
					$appointmentID 		= esc_sql($appointmentID);
					$releasedCustomer 	= esc_sql($releasedCustomer);
					$query 		= "SELECT payment_id, id FROM {$wpdb->prefix}bmify_customer_appointments WHERE customer_id=".$releasedCustomer." AND appointment_id=".$appointmentID;
					$results 	= $wpdb->get_results( $query, OBJECT  );

					// удаление платежа данной встречи для исключенных клиентов
					$paymentID 	= $results[0]->payment_id;
					$wpdb->query($wpdb->prepare( "DELETE FROM {$wpdb->prefix}bmify_payments WHERE id=%d", $paymentID));

					// удаление всех экстра сервисов
					$cusAppID 	= $results[0]->id;
					$wpdb->query($wpdb->prepare( "DELETE FROM {$wpdb->prefix}bmify_customer_appointments_extras WHERE customer_appointment_id=%d", $cusAppID));

					// удаление встречи для исключенных клиентов
					$wpdb->query($wpdb->prepare( "DELETE FROM {$wpdb->prefix}bmify_customer_appointments WHERE customer_id=%d AND appointment_id=%d", $releasedCustomer, $appointmentID));

					// notification	|| send notification to released customer
					if($checked == 'public'){
						$infoObject						= new \stdClass();
						$serviceName					= Helper::bookmifyGetServiceCol($oldServiceID);
						$customerName					= Helper::bookmifyGetCustomerCol($releasedCustomer);
						$customerEmail					= Helper::bookmifyGetCustomerCol($releasedCustomer, 'email');
						$infoObject->sendTo				= 'customer';
						$infoObject->appID				= $appointmentID;
						$infoObject->userID				= $releasedCustomer;
						$infoObject->service_name 		= $serviceName;
						$infoObject->appointment_date 	= $oldDate;
						$infoObject->appointment_time 	= $oldTime;
						$infoObject->status			 	= 'rejected';
						$infoObject->customer_name	 	= $customerName;
						$infoObject->customer_email	 	= $customerEmail;
						$arrayToSendNotification[]		= array($infoObject, '', 'customer');
//						$this->pretraintmentToSendNotification($infoObject); // отправка уведомление новому клиенту
					}
				}
			}

			// since v1.3.8
			self::sendArrayNotification($arrayToSendNotification);
			
			
			// update google calendar event
			$googleCal 			= new GoogleCalendarProject();
			$googleCal->updateEvent($appointmentID);
		}
			
		$html 			= '';
		if(!empty($_POST['do']) && $hack == 0){
			
			$page			= 1;
			$startDate 		= date('Y-m-d') . ' 00:00:00';
			$endDate  		= date('Y-m-d', strtotime('+'.$this->daterange.' days')).' 23:59:59';
			$filter			= array();
			$order			= 'ASC';
			$startDate 		= esc_sql($startDate);
			$endDate 		= esc_sql($endDate);
			$userID			= esc_sql($this->userID);
			$query 		 	= "SELECT
								a.id appID,
								a.service_id appServiceID,
								a.employee_id appEmployeeID,
								a.location_id appLocationID,
								a.status appStatus,
								a.start_date appStartDate,
								a.end_date appEndDate,
								a.info appInfo,
								a.created_from appCreatedFrom
								
							FROM 	   	   {$wpdb->prefix}bmify_appointments a 
								LEFT JOIN  {$wpdb->prefix}bmify_customer_appointments ca ON ca.appointment_id = a.id
									  
							WHERE (a.start_date BETWEEN '".$startDate."' AND '".$endDate."')"." AND a.employee_id=".$userID;



			$customlist 	= array();
			
			
			
			
			$query = rtrim($query, 'AND');
			$query = rtrim($query, 'WHERE');
			
			
			
			$Querify  		= new Querify( $query, 'user_appointment' );
			$appointments	= $Querify->getData( $this->per_page, $page, $filter, $order );
			
			$customlist 	= array();

			for( $i = 0; $i < count( $appointments->data ); $i++ ){

					$day 	= date_i18n(get_option('bookmify_be_date_format', 'd F, Y'), strtotime($appointments->data[$i]->appStartDate));

					if(!isset($customlist[$day]))
					{
					 $customlist[$day] 	= array();
					}

					$customlist[$day][] = $appointments->data[$i];

			}

			$html .= '<div class="appointments_list bookmify_be_list">';

			foreach($customlist as $day => $appointments){
				$list 		= '';
				$appCount 	= 0;
				$pendCount	= 0;
				foreach($appointments as $appointment){
						$appointmentID	= $appointment->appID;
						$duration 		= HelperAppointments::getDurationForAppointment($appointmentID);
						$duration 		= Helper::bookmifyNumberToDuration($duration);
						$time			= date_i18n(get_option('bookmify_be_time_format', 'h:i a'), strtotime($appointment->appStartDate));

						$price 			= HelperAppointments::getPriceForAppointment($appointmentID);
						$price 			= Helper::bookmifyPriceCorrection($price);

						$status 		= $appointment->appStatus;
						switch($status){
							case 'approved': 	$icon = 'checked'; 	$statusText = esc_html__('Approved', 'bookmify'); $appCount++; break;
							case 'pending': 	$icon = 'circle'; 	$statusText = esc_html__('Pending', 'bookmify'); $pendCount++;  break;
							case 'canceled':	$icon = 'cancel'; 	$statusText = esc_html__('Canceled', 'bookmify'); break;
							case 'rejected': 	$icon = 'cancel'; 	$statusText = esc_html__('Rejected', 'bookmify'); break;
						}
						$statusIcon 	= '<span class="icon"><img class="bookmify_be_svg '.$status.'" src="'.BOOKMIFY_ASSETS_URL.'img/'.$icon.'.svg" alt="" /></span>';
						$statusText 	= $statusIcon.'<span class="text">'.$statusText.'</span>';;
					
						$appointmentStartDate 	= date("Y-m-d H:i:s", strtotime($appointment->appStartDate));
						$today					= HelperTime::getCurrentDateTime();
						if($appointmentStartDate <= $today){
							$appDateStatus		= 'bookmify_be_closed_item';
							$appDateStatusBtn	= 'bookmify_be_closed_btn';
						}else{
							$appDateStatus 		= 'bookmify_be_open_item';
							$appDateStatusBtn 	= 'bookmify_be_open_btn';
						}
						if($this->userSlug == 'customer' && ($status == 'rejected' || $status == 'canceled')){
							$appDateStatus		= 'bookmify_be_closed_item';
							$appDateStatusBtn	= 'bookmify_be_closed_btn';
						}
						$list .= '<div data-entity-id="'.$appointmentID.'" class="bookmify_be_appointment_item bookmify_be_list_item bookmify_be_animated '.$appDateStatus.'">

									<div class="bookmify_be_list_item_in">
										<div class="bookmify_appointment_heading bookmify_be_list_item_header">
											<div class="bookmify_heading_in header_in">


												<div class="appointment_info">
													<span class="appointment_time">
														<span>
															<img class="bookmify_be_svg" src="'.BOOKMIFY_ASSETS_URL.'img/clock.svg" alt="" />'.$time.'
														</span>
													</span>
													<span class="appointment_customer">
														<span>'.HelperCabinet::bookmifyGetCustomersCol($appointmentID, $this->userSlug, $this->userID).'</span>
													</span>
													<span class="appointment_service">
														<span>'.Helper::bookmifyGetServiceCol($appointment->appServiceID).'</span>
													</span>
													<span class="appointment_employee">
														<span>'.Helper::bookmifyGetEmployeeCol($appointment->appEmployeeID).'</span>
													</span>
													<span class="appointment_price">
														<span>'.$price.'</span>
													</span>
													<span class="appointment_duration">
														<span>'.$duration.'</span>
													</span>
													<span class="appointment_status '.$status.'">
														<span>'.$statusText.'</span>
													</span>
												</div>
												
												<div class="buttons_holder">
												
													<div class="btn_item btn_more">
														<a href="#" class="bookmify_be_more" data-entity-id="'.$appointmentID.'">
															<img class="bookmify_be_svg more" src="'.BOOKMIFY_ASSETS_URL.'img/more.svg" alt="" />
															<span class="bookmify_be_loader small">
																<span class="loader_process">
																	<span class="ball"></span>
																	<span class="ball"></span>
																	<span class="ball"></span>
																</span>
															</span>
														</a>
													</div>
													<div class="btn_item btn_edit '.$appDateStatusBtn.'">
														<a href="#" class="bookmify_be_edit">
															<img class="bookmify_be_svg edit" src="'.BOOKMIFY_ASSETS_URL.'img/edit.svg" alt="" />
															<img class="bookmify_be_svg checked" src="'.BOOKMIFY_ASSETS_URL.'img/checked.svg" alt="" />
															<span class="bookmify_be_loader small">
																<span class="loader_process">
																	<span class="ball"></span>
																	<span class="ball"></span>
																	<span class="ball"></span>
																</span>
															</span>
														</a>
													</div>
												</div>

											</div>
										</div>
									</div>';

						$list .= '</div>';

				}
				$html .= 	'<div class="bookmify_be_day_separator">
								<span class="date_holder">'.$day.'</span>
								<span class="status_holder">
									<span class="app_count">
										<span class="app_count_c">'.$appCount.'</span>
										<span class="app_count_t">'.esc_html__('Approved', 'bookmify').'</span>
									</span>
									<span class="pend_count">
										<span class="pend_count_c">'.$pendCount.'</span>
										<span class="pend_count_t">'.esc_html__('Pending', 'bookmify').'</span>
									</span>
								</span>
							</div>';
				$html .= $list;
			}

			$html .= '</div>';
			

			$html .= $Querify->getPagination( 1, 'bookmify_be_pagination user_appointment');
			
			
		}
			
		
		$buffy = '';
		if ( $html != NULL ) {
			$buffy .= $html; 
		}

		// remove whitespaces form the ajax HTML
		$search = array(
			'/\>[^\S ]+/s',  // strip whitespaces after tags, except space
			'/[^\S ]+\</s',  // strip whitespaces before tags, except space
			'/(\s)+/s'       // shorten multiple whitespace sequences
		);
		$replace = array(
			'>',
			'<',
			'\\1'
		);
		$buffy = preg_replace($search, $replace, $buffy);

		$buffyArray = array(
			'bookmify_be_data' 		=> $buffy,
			'hack'					=> $hack,
		);

		if ( true === $isAjaxCall ){die(json_encode($buffyArray));} 
		else{return json_encode($buffyArray);}
	}
	
	
	// commented since v1.3.8
	// подготовка к отправке уведомлении
//	private function pretraintmentToSendNotification($object, $rescheduled = ''){
//		$receiver 		= $object->sendTo;
//		$checkSender 	= Helper::checkForSender();
//		
//		if($checkSender){
//			if($receiver == 'employee'){
//				NotificationManagement::sendInfoToEmployeeAboutAppointment( $object, $rescheduled );
//			}else if($receiver == 'customer'){
//				NotificationManagement::sendInfoToCustomerAboutAppointment( $object, $rescheduled );
//			}
//		}
//		
//		return false;
//    }
	
	// отправка уведомлений
	// since 1.3.8
	public static function sendArrayNotification($array){
		$checkSender 			= Helper::checkForSender();
		if($checkSender){
			foreach($array as $arr){
				$object 		= $arr[0];
				$rescheduled 	= $arr[1];
				$receiver 		= $arr[2];
				if($receiver == 'employee'){
					NotificationManagement::sendInfoToEmployeeAboutAppointment( $object, $rescheduled );
				}else if($receiver == 'customer'){
					NotificationManagement::sendInfoToCustomerAboutAppointment( $object, $rescheduled );
				}
			}
		}
    }
	
	
	public function ajaxQueryUserDetailsAppointment(){
		global $wpdb;
		$isAjaxCall 		= false;
		$html 				= '';
		if (!empty($_POST['bookmify_data'])) {
			$isAjaxCall 	= true;
			$id 			= $_POST['bookmify_data'];

			// SELECT
			$id 			= esc_sql($id);
			$query 			= "SELECT id FROM {$wpdb->prefix}bmify_appointments WHERE id=".$id;
			$appointments 	= $wpdb->get_results( $query, OBJECT  );
			
			
			foreach($appointments as $appointment){
				$appointmentID			= $appointment->id;
				$html .= '<div class="bookmify_be_popup_form_wrap" data-entity-id="'.$appointmentID.'">
							<div class="bookmify_be_popup_form_position_fixer popup_details">
								<div class="bookmify_be_popup_form_bg">
									<div class="bookmify_be_popup_form">

										<div class="bookmify_be_popup_form_header">
											<h3>'.esc_html__('Appointment Details','bookmify').'</h3>
											<span class="closer"></span>
										</div>

										<div class="bookmify_be_popup_form_content">
											<div class="bookmify_be_popup_form_content_in">

												<div class="bookmify_be_popup_form_fields">
													'.HelperCabinet::detailsOfAppointment($appointmentID,$this->userSlug,$this->userID).'
												</div>

											</div>
										</div>
										

									</div>
								</div>
							</div>
						</div>';
			}
		}
		$buffy = '';
		if ( $html != NULL ) {
			$buffy .= $html; 
		}

		// remove whitespaces form the ajax HTML
		$search = array(
			'/\>[^\S ]+/s',  // strip whitespaces after tags, except space
			'/[^\S ]+\</s',  // strip whitespaces before tags, except space
			'/(\s)+/s'       // shorten multiple whitespace sequences
		);
		$replace = array(
			'>',
			'<',
			'\\1'
		);
		$buffy = preg_replace($search, $replace, $buffy);

		$buffyArray = array(
			'bookmify_be_data' 		=> $buffy,
		);

		if ( true === $isAjaxCall ){die(json_encode($buffyArray));} 
		else{return json_encode($buffyArray);}

	}
	public static function ajaxQueryCancelAppointment(){
		global $wpdb;
		$isAjaxCall 		= false;
		$error 				= '';
		if (!empty($_POST['ID'])) {
			$isAjaxCall 	= true;
			$id 			= $_POST['ID'];

			// get old appointment status
			$userID 		= esc_sql($this->userID);
			$id 			= esc_sql($id);
			$query 			= "SELECT status FROM {$wpdb->prefix}bmify_customer_appointments WHERE customer_id=".$userID." AND appointment_id=".$id;
			$results	 	= $wpdb->get_results( $query, OBJECT  );
			$oldStatus		= $results[0]->status;
			
			// if detect bunny error
			if($oldStatus == 'canceled' || $oldStatus == 'rejected'){
				$error 		= 'warning';
			}
			
			// get start date, service ID and employee ID
			$id 			= esc_sql($id);
			$query 			= "SELECT service_id,employee_id,start_date FROM {$wpdb->prefix}bmify_appointments WHERE id=".$id;
			$results	 	= $wpdb->get_results( $query, OBJECT  );
			$serviceID		= $results[0]->service_id;
			$employeeID		= $results[0]->employee_id;
			$startDate		= $results[0]->start_date;
			
			// if detect bunny error
			$appointmentStartDate 	= date("Y-m-d H:i:s", strtotime($startDate));
			$today			= HelperTime::getCurrentDateTime();
			if($appointmentStartDate <= $today){
				$error		= 'warning';
			}
			
			// if no any errors
			if($error == ''){
				// get capacity minimum of selected service ID and employee ID
				$serviceID 		= esc_sql($serviceID);
				$employeeID 	= esc_sql($employeeID);
				$query 			= "SELECT capacity_min FROM {$wpdb->prefix}bmify_employee_services WHERE service_id=".$serviceID." AND employee_id=".$employeeID;
				$results	 	= $wpdb->get_results( $query, OBJECT  );
				$capacityMin	= $results[0]->capacity_min;

				// change status to canceled for selected customer
				$currentDate	= HelperTime::getCurrentDateTime();
				$wpdb->query($wpdb->prepare( "UPDATE {$wpdb->prefix}bmify_customer_appointments SET status=%s, status_changed_at=%d  WHERE customer_id=%d AND appointment_id=%d", 'canceled', $currentDate, $this->userID, $id));

				// get people count after canceling one customer appointment
				$id 			= esc_sql($id);
				$query 			= "SELECT number_of_people,status FROM {$wpdb->prefix}bmify_customer_appointments WHERE appointment_id=".$id;
				$results	 	= $wpdb->get_results( $query, OBJECT  );
				$canceledCount	= 0;
				$rejectedCount	= 0;
				$pendingCount	= 0;
				$approvedCount	= 0;
				$countall		= 0;
				foreach($results as $result){
					$status		= $result->status;
					$qty		= $result->number_of_people;
					switch($status){
						case 'approved': 	$approvedCount 	+= $qty; 	break;
						case 'pending': 	$pendingCount++;		 	break;
						case 'canceled': 	$canceledCount++; 			break;
						case 'rejected': 	$rejectedCount++; 			break;
					}
					$countall++;
				}
				$newStatus = 'pending';
				if($approvedCount >= $capacityMin){$newStatus = 'approved';}
				if(($approvedCount != 0) && ($approvedCount < $capacityMin)){$newStatus = "pending";}
				if($approvedCount == 0){$newStatus = "pending";}
				if($countall == $rejectedCount){$newStatus = "rejected";}
				if($countall == $canceledCount){$newStatus = "canceled";}

				$wpdb->query($wpdb->prepare( "UPDATE {$wpdb->prefix}bmify_appointments SET status=%s  WHERE id=%d", $newStatus, $id));
			}
				
						
		}
		

		// remove whitespaces form the ajax HTML
		$search = array(
			'/\>[^\S ]+/s',  // strip whitespaces after tags, except space
			'/[^\S ]+\</s',  // strip whitespaces before tags, except space
			'/(\s)+/s'       // shorten multiple whitespace sequences
		);
		$replace = array(
			'>',
			'<',
			'\\1'
		);
		$error = preg_replace($search, $replace, $error);

		$buffyArray = array(
			'error' 		=> $error,
		);
		
		if ( true === $isAjaxCall ){die(json_encode($buffyArray));} 
		else{return json_encode($buffyArray);}
	}
	
	/**
	 * @since 1.0.0
	 * @access protected
	*/
	protected function get_page_title() {
		$html = esc_html__( 'Appointments', 'bookmify' );	
		if($this->userSlug == 'employee'){
			$html 	= esc_html__('Your Appointments', 'bookmify');
		}else{
			$html 	= esc_html__('Your Appointments', 'bookmify');
		}
		return $html;
	}
}
	

