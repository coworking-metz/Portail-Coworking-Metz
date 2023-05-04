<?php
namespace Bookmify;

use \Datetime;
use Bookmify\Helper;
use Bookmify\HelperTime;
use Bookmify\HelperAdmin;
use Bookmify\HelperAppointments;
use Bookmify\HelperEmployees;
use Bookmify\NotificationManagement;
use Bookmify\GoogleCalendarProject;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


class Appointments{

	const PAGE_ID = 'bookmify_appointments';
	
	
	private $per_page;
	private $daterange;
	private $dateformat;
	

	/**
	 * @since 1.0.0
	 * @access public
	*/
	public function __construct() {
		
		$this->assignValToVar();
		
		
		add_action( 'admin_menu', [ $this, 'register_admin_menu' ], 20 );
		
		// filter
		add_action( 'wp_ajax_ajaxFilterAppointmentList', [$this, 'ajaxFilterAppointmentList'] );
		
		// get date and time information
		add_action( 'wp_ajax_ajaxQuerydayOffsEmployee', [$this, 'ajaxQuerydayOffsEmployee'] );
		add_action( 'wp_ajax_ajaxQueryTimeSlotsAppointment', [$this, 'ajaxQueryTimeSlotsAppointment'] );
		
		// get extra and payment information again
		add_action( 'wp_ajax_ajaxQueryGetExtrasOfServiceForAppointment', [$this, 'ajaxQueryGetExtrasOfServiceForAppointment'] );
		add_action( 'wp_ajax_ajaxQueryGetPaymentsCustomersForAppointment', [$this, 'ajaxQueryGetPaymentsCustomersForAppointment'] );
		
		
		// main actions
		add_action( 'wp_ajax_ajaxQueryDeleteAppointment', [$this, 'ajaxQueryDeleteAppointment'] );
		add_action( 'wp_ajax_ajaxQueryEditAppointment', [$this, 'ajaxQueryEditAppointment'] );
		add_action( 'wp_ajax_ajaxQueryDetailsAppointment', [$this, 'ajaxQueryDetailsAppointment'] );
		add_action( 'wp_ajax_ajaxQueryInsertOrUpdateAppointment', [$this, 'ajaxQueryInsertOrUpdateAppointment'] );
		
		
		
		add_action( 'wp_ajax_bookmifyCheckVarViaAJAX', [$this, 'bookmifyCheckVarViaAJAX'] );
	}
	
	/**
	 * @since 1.0.0
	 * @access public
	*/
	public function assignValToVar(){
		$this->per_page 	= get_option('bookmify_be_appointments_pp', 10);
		$this->daterange 	= get_option('bookmify_be_appointments_daterange', 30) - 1;
		$this->dateformat 	= get_option('bookmify_be_date_format', 'd F, Y');
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
			'bookmify_be_read_appointments',
			self::PAGE_ID,
			[ $this, 'display_appointments_page' ]
		);
	}
	
	public function display_appointments_page() {
		global $wpdb;

		echo HelperAdmin::bookmifyAdminContentStart();
		?>
		
		<div class="bookmify_be_content_wrap bookmify_be_appointments_page">
			<!-- PAGE TITLE -->
			<div class="bookmify_be_page_title">
				<h3><?php echo esc_html($this->get_page_title()); ?><span class="count"><?php echo Helper::bookmifyItemsCount('appointments');?></span></h3>
				<div class="bookmify_be_add_new_item bookmify_be_add_new_appointment">
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
			</div>
			<!-- /PAGE TITLE -->
			
			
			<!-- PAGE CONTENT -->
			<div class="bookmify_be_page_content">
				
				
				<div class="bookmify_be_appointments">
					
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
									<div class="f_tooltip_content"><?php esc_html_e('This is the number of people who will come with this customer including the customer. This number varies depending on the selected service and employee.', 'bookmify');?></div>
								</div>
							</div>
							<div class="remover"><span><span></span></span></div>
						</div>
						<div class="no_extras">
							<div class="bookmify_be_infobox">
								<label><?php esc_html_e('This service doesn\'t have extra services', 'bookmify'); ?></label>
							</div>
						</div>
					</div>
					
					
					<!-- PAGE FILTER -->
					<?php echo HelperAppointments::allFilter();?>
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
						<?php echo $this->appointments_list(); ?>
					</div>
					<!-- /PAGE LIST -->
					
					
				</div>
				
			</div>
			<!-- PAGE CONTENT -->
			
			<?php echo HelperAppointments::clonableForm(); ?>
		</div>
		<?php echo HelperAdmin::bookmifyAdminContentEnd();
		
	}
	
	
	
	
	
	/**
	 * @since 1.0.0
	 * @access public
	*/
	public function appointments_list(){
		global $wpdb;
		
		$startDate 		= HelperTime::getCurrentDateTime();
		$startDate  	= date('Y-m-d').' 00:00:00';
		$endDate  		= date('Y-m-d', strtotime('+'.$this->daterange.' days')).' 23:59:59';
		
		$startDate 		= esc_sql($startDate);
		$endDate 		= esc_sql($endDate);
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
									  
							WHERE (a.start_date BETWEEN '".$startDate."' AND '".$endDate."')";
		
		$Querify  		= new Querify( $query, 'appointment' );
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

		if(count($customlist) == 0){
			$html .= Helper::bookmifyBeNoItems();
		}
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
				$statusText 	= $statusIcon.'<span class="text">'.$statusText.'</span>';
				
				
				$appointmentStartDate 	= date("Y-m-d H:i:s", strtotime($appointment->appStartDate));
				$today					= HelperTime::getCurrentDateTime();
				if(get_option('bookmify_be_old_appointment_action', '') == 'on'){
					$appDateStatus 		= 'bookmify_be_open_item';
					$appDateStatusBtn 	= 'bookmify_be_open_btn';
				}else{
					if($appointmentStartDate <= $today){
						$appDateStatus		= 'bookmify_be_closed_item';
						$appDateStatusBtn	= 'bookmify_be_closed_btn';
					}else{
						$appDateStatus 		= 'bookmify_be_open_item';
						$appDateStatusBtn 	= 'bookmify_be_open_btn';
					}
				}
					
				
				$list .= '<div data-entity-id="'.$appointmentID.'" class="bookmify_be_appointment_item bookmify_be_list_item '.$appDateStatus.'">
							
							<div class="bookmify_be_list_item_in">
								<div class="bookmify_appointment_heading bookmify_be_list_item_header">
									<div class="bookmify_heading_in header_in">


										<div class="appointment_info">
											<span class="appointment_time"><span><img class="bookmify_be_svg" src="'.BOOKMIFY_ASSETS_URL.'img/clock.svg" alt="" />'.$time.'</span></span>
											<span class="appointment_customer"><span>'.Helper::bookmifyGetCustomersCol($appointmentID).'</span></span>
											<span class="appointment_service"><span>'.Helper::titleDecryption(Helper::bookmifyGetServiceCol($appointment->appServiceID)).'</span></span>
											<span class="appointment_employee"><span>'.Helper::bookmifyGetEmployeeCol($appointment->appEmployeeID).'</span></span>
											<span class="appointment_price"><span>'.$price.'</span></span>
											<span class="appointment_duration"><span>'.$duration.'</span></span>
											<span class="appointment_status '.$status.'"><span>'.$statusText.'</span></span>
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
											<div class="btn_item btn_delete '.$appDateStatusBtn.'">
												<a href="#" class="bookmify_be_delete" data-entity-id="'.$appointmentID.'">
													<img class="bookmify_be_svg" src="'.BOOKMIFY_ASSETS_URL.'img/delete.svg" alt="" />
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
		
		$html .= $Querify->getPagination( 1, 'bookmify_be_pagination appointment');
		
		return $html;
	}
	
	
	
	
	
	
	
	/**
	 * @since 1.0.0
	 * @access public
	*/
	public function ajaxFilterAppointmentList(){		
		global $wpdb;
		$isAjaxCall 		= false;
		$html 				= '';
		$page 				= 1;
		$filter 			= array();
		$search_text 		= '';
		$employeeIDs		= array();
		$filterByService	= array();
		$filterDateRange 	= array();
		
		if (!empty($_POST['bookmify_page'])) {
			$isAjaxCall 	= true;
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
									  
							WHERE";



			$html 			= '';
			$customlist 	= array();
			
			
			if(!empty($filterByService)){
				$filterByService = esc_sql($filterByService);
				$query .= " a.service_id IN (" . implode(",", array_map("intval", $filterByService)) . ") AND";
			}
			if($filterByCustomer != ''){
				$filterByCustomer = esc_sql($filterByCustomer);
				$query .= " ca.customer_id = '".$filterByCustomer."' AND";
			}
			if($filterByEmployee != ''){
				$filterByEmployee = esc_sql($filterByEmployee);
				$query .= " a.employee_id = '".$filterByEmployee."' AND";
			}
			if($filterByStatus != ''){
				$filterByStatus = esc_sql($filterByStatus);
				$query .= " a.status = '".$filterByStatus."' AND";
			}
			if(!empty($filterDateRange)){
				$filterDateRange = esc_sql($filterDateRange);
				$query .= " (a.start_date BETWEEN '".$filterDateRange[0]."' AND '".$filterDateRange[1]."') AND";
			}
			
			
			
			
			$query = rtrim($query, 'AND');
			$query = rtrim($query, 'WHERE');
			
			
			
			$Querify  		= new Querify( $query, 'appointment' );
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
			if(count($customlist) == 0){
				$html .= Helper::bookmifyBeNoItems();
			}
			foreach($customlist as $day => $appointments){

				$list = '';
				$appCount = 0;
				$pendCount = 0;
				
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
						$statusText 	= $statusIcon.'<span class="text">'.$statusText.'</span>';
					
						$appointmentStartDate 		= date("Y-m-d H:i:s", strtotime($appointment->appStartDate));
						$today						= HelperTime::getCurrentDateTime();
						if(get_option('bookmify_be_old_appointment_action', '') == 'on'){
							$appDateStatus 			= 'bookmify_be_open_item';
							$appDateStatusBtn 		= 'bookmify_be_open_btn';
						}else{
							if($appointmentStartDate <= $today){
								$appDateStatus		= 'bookmify_be_closed_item';
								$appDateStatusBtn	= 'bookmify_be_closed_btn';
							}else{
								$appDateStatus 		= 'bookmify_be_open_item';
								$appDateStatusBtn 	= 'bookmify_be_open_btn';
							}
						}

						$list .= '<div data-entity-id="'.$appointmentID.'" class="bookmify_be_appointment_item bookmify_be_list_item bookmify_be_animated '.$appDateStatus.'">

									<div class="bookmify_be_list_item_in">
										<div class="bookmify_appointment_heading bookmify_be_list_item_header">
											<div class="bookmify_heading_in header_in">


												<div class="appointment_info">
													<span class="appointment_time"><span><img class="bookmify_be_svg" src="'.BOOKMIFY_ASSETS_URL.'img/clock.svg" alt="" />'.$time.'</span></span>
													<span class="appointment_customer"><span>'.Helper::bookmifyGetCustomersCol($appointmentID).'</span></span>
													<span class="appointment_service"><span>'.Helper::titleDecryption(Helper::bookmifyGetServiceCol($appointment->appServiceID)).'</span></span>
													<span class="appointment_employee"><span>'.Helper::bookmifyGetEmployeeCol($appointment->appEmployeeID).'</span></span>
													<span class="appointment_price"><span>'.$price.'</span></span>
													<span class="appointment_duration"><span>'.$duration.'</span></span>
													<span class="appointment_status '.$status.'"><span>'.$statusText.'</span></span>
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
													<div class="btn_item btn_delete '.$appDateStatusBtn.'">
														<a href="#" class="bookmify_be_delete" data-entity-id="'.$appointmentID.'">
															<img class="bookmify_be_svg" src="'.BOOKMIFY_ASSETS_URL.'img/delete.svg" alt="" />
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
			

			$html .= $Querify->getPagination( 1, 'bookmify_be_pagination appointment');
			
			
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
			'bookmify_be_data' 		=> $buffy, // employeeIDs filter['list']
			'bookmify_be_p' 		=> $query
		);

		if ( true === $isAjaxCall ){die(json_encode($buffyArray));}
		else{return json_encode($buffyArray);}
	}
	
	
	public function ajaxQuerydayOffsEmployee(){
		global $wpdb;
		$isAjaxCall 			= false;
		
		if (!empty($_POST['bookmify_employee_id'])) {
			$isAjaxCall			= true;
			$employeeID 		= $_POST['bookmify_employee_id'];
			
			// получение неежегодных выходных дней (по дням) из БД
			$html				= array();
			$today				= HelperTime::getCurrentDateTimeWithoutFormat();
			$today				= $today->format('Y-m-d');
			$employeeID 		= esc_sql($employeeID);
			$query 				= "SELECT date FROM {$wpdb->prefix}bmify_dayoff WHERE (employee_id=".$employeeID." OR employee_id IS NULL) AND date >= '".$today."' AND every_year=0";
			$results 			= $wpdb->get_results( $query, OBJECT  );
			foreach($results as $result){
				$html[] 		= $result->date;
			}
			// получение ежегодных выходных дней (по дням) из БД
			$arr2				= array();
			$employeeID 		= esc_sql($employeeID);
			$query 				= "SELECT date FROM {$wpdb->prefix}bmify_dayoff WHERE (employee_id=".$employeeID." OR employee_id IS NULL) AND date >= '".$today."' AND every_year=1";
			$results 			= $wpdb->get_results( $query, OBJECT  );
			foreach($results as $result){
				$arr2[] 		= date('m-d',strtotime($result->date)); // month/day
			}
			
			// получение выходных дней недели (по каким дням недели отдыхает работник) из БД
			$employeeID 		= esc_sql($employeeID);
			$query 				= "SELECT start_time,end_time,day_index FROM {$wpdb->prefix}bmify_employee_business_hours WHERE employee_id=".$employeeID;
			$results 			= $wpdb->get_results( $query, OBJECT  );
			$dayIndex 			= [1,2,3,4,5,6,7];
			foreach($results as $result){
				$endTimeInMinutes 	= date('H', strtotime($result->end_time))*60+date('i', strtotime($result->end_time));
				if($endTimeInMinutes == 0){
					$endTimeInMinutes = 24 * 60;
				}
				$startTimeInMinutes = date('H', strtotime($result->start_time))*60+date('i', strtotime($result->start_time));
				$endTimeInMinutes  	= $endTimeInMinutes - $startTimeInMinutes;
				if($endTimeInMinutes > 0){
					$del_val 		= $result->day_index;
					if (($key = array_search($del_val, $dayIndex)) !== false) {
						unset($dayIndex[$key]);
					}
				}
			}
			
			
			// отправка обработанных данных
			$buffyArray = array(
				'bookmify_be_data' 		=> $html,
				'bookmify_be_time' 		=> $dayIndex,
				'arr2' 					=> $arr2,
			);
			
			if ( true === $isAjaxCall ) {die(json_encode($buffyArray));} 
			else {return json_encode($buffyArray);}
			
		}
	}
	public function ajaxQueryTimeSlotsAppointment(){
		global $wpdb;
		$isAjaxCall 			= false;
		
		if (!empty($_POST['serID'])) {
			$isAjaxCall			= true;
			$serviceID 			= $_POST['serID'];
			$appID 				= $_POST['appID'];
			$employeeID 		= $_POST['empID'];
			$dateValue 			= $_POST['dateVal'];
			$day 				= $_POST['dateVal'];
			$selDayBetween 		= $_POST['selDayBetween'];
			$newHours 			= $_POST['newHours'];
			$newMinutes 		= $_POST['newMinutes'];
			$selectedDayIndex 	= date('N', strtotime($dateValue));
			$extraDuration 		= $_POST['extraDuration'];
			if(!$extraDuration){
				$extraDuration 	= 0;
			}
			
			// время работы (от и до) выбранного работника, для выбранной даты по индексу дня в формате чч:мм
			$startTime 			= Helper::bookmifyWorkingHoursOfEmployee($employeeID,$selectedDayIndex,'start_time');
			$endTime 			= Helper::bookmifyWorkingHoursOfEmployee($employeeID,$selectedDayIndex,'end_time');
			
			
			// суммарное время которое уйдет на выбранный сервис (здесь учитывается длительность самого сервиса а также время до и после этого сервиса)
			$serviceBuffBefore 	= 0;
			$serviceBuffAfter	= 0;
			$serviceID 			= esc_sql($serviceID);
			$query 				= "SELECT duration, buffer_before, buffer_after FROM {$wpdb->prefix}bmify_services WHERE id=".$serviceID;
			$results 			= $wpdb->get_results( $query, OBJECT  );
			foreach($results as $result){
				$serviceDuration 	= $result->duration; 			// в секундах
				$serviceBuffBefore 	= $result->buffer_before;		// в секундах
				$serviceBuffAfter 	= $result->buffer_after;		// в секундах
			}
			$summaryDuration 		= ($serviceDuration+$serviceBuffBefore+$serviceBuffAfter+$extraDuration) / 60; // в минутах
			
			
			// слот времени: по выбранному слоту (в минутах) будет добавляться время, к примеру: 8:00, 8:15, 8:30 и т.д.
			$timeSlot 				= get_option( 'bookmify_be_time_interval', '15' ); // получить тайм интервал из настроек
			// проверить включена ли время сервиса как интервал в настройках, в случае положительного ответа применить его как слот времени
			if(get_option('bookmify_be_service_time_as_slot', '') == 'on'){
				$timeSlot 			= $summaryDuration;
			}
			
			// время работы (от и до) для выбранного работника, для выбранной даты по индексу дня в минутах
			$startTimeInMinutes = date('H',strtotime($startTime))*60 + date('i',strtotime($startTime));
			$endTimeCheck 		= date('H',strtotime($endTime))*60 + date('i',strtotime($endTime));
			if($endTimeCheck == 0){
				$endTimeCheck = 24*60;
			}
			$endTimeInMinutes 	= $endTimeCheck - ($serviceBuffBefore / 60);
			
			
			// если выбран сегодняшний или завтрашний день, установить минимум время для подсчета слотов
			$minTimeInMinutes = 0;
			if($selDayBetween == 0){
				$minTimeInMinutes = intval($newHours * 60 + $newMinutes);
			}
			if(($selDayBetween == 1) && ($newHours >=24)){
				$minTimeInMinutes = intval(($newHours - 24) * 60 + $newMinutes);
			}
			
			// начало работы выбранного работника в секундах
			$startTime = strtotime($startTime) + $serviceBuffBefore; // здесь мы прибавляем время buffer_before для того, чтобы работник смог подготовиться к работе в начале работы
			
			// количество слотов без каких либо учетов
			$to = intval(($endTimeInMinutes - $startTimeInMinutes) / $timeSlot);
			// ОБЩИЙ массив без каких либо учетов
			$allArray = array();
			for($i = 0; $i < $to; $i++){
				$firstTime = $i*$timeSlot + $startTimeInMinutes;
				if($firstTime <= ($endTimeInMinutes - $summaryDuration)){
					$allArray[] = date("H", strtotime('+'.($i*$timeSlot).' minutes', $startTime))*60 + date("i", strtotime('+'.($i*$timeSlot).' minutes', $startTime));
				}
			}
			
			
			
			// получение всевозможных перерывов выбранного работника для выбранной даты по индексу дня в массиве
			$breakArray = array();
			$selectedDayIndex 		= esc_sql($selectedDayIndex);
			$employeeID 			= esc_sql($employeeID);
			$select = "SELECT start_time,end_time FROM {$wpdb->prefix}bmify_employee_business_hours_breaks WHERE day_index=".$selectedDayIndex." AND employee_id=".$employeeID;
			$breaks = $wpdb->get_results( $select, OBJECT  );
			foreach($breaks as $key => $break){
				$startBreak = date('H', strtotime($break->start_time))*60 + date('i', strtotime($break->start_time));
				$endBreak 	= date('H', strtotime($break->end_time))*60 + date('i', strtotime($break->end_time));
				$breakArray[$key]['start'] 	= $startBreak;
				$breakArray[$key]['end'] 	= $endBreak;
			}
			
			// получение всевозможных слотов, которых нужно удалить из ОБЩЕГО масива (все ПЕРЕРЫВЫ того дня недели)
			$removableValues = array();
			foreach($breakArray as $key => $result){
				$min 	= intval($result['start']) - $summaryDuration;
				$max 	= intval($result['end']) + ($serviceBuffBefore / 60); // здесь мы прибавляем время buffer_before для того, чтобы работник смог подготовиться к работе после каждого перерыва
				$removableValues[] 	= array_filter($allArray, function ($value) use($min,$max)  { return ($value > $min && $value < $max); });
			}
			$removableArr = array();
			foreach($removableValues as $results){
				foreach($results as $result){
					$removableArr[] = $result;
				}
			}
			
			// удаление полученных слотов, которых нужно было удалить из ОБЩЕГО массива (все ПЕРЕРЫВЫ того дня недели)
			$difference = array_diff($allArray,$removableArr);
			
			
			//****************************************************************************************************************************************
			// получение всевозможных ВСТРЕЧ выбранного работника для выбранной даты в массиве
			$appointmentArray 		= array();
			$chosenDay 				= date("Y-m-d",strtotime($dateValue));
			$nextDay 				= date("Y-m-d",strtotime($dateValue."+1 days"));
			$startDate				= $chosenDay . " 00:00:00";
			$endDate				= $nextDay . " 00:00:00";
			$startDate				= date("Y-m-d H:i:s", strtotime($startDate));
			$endDate				= date("Y-m-d H:i:s", strtotime($endDate));
			$newOption				= "";
			if($appID != 0){
				$appID 				= esc_sql($appID);
				$newOption			= " AND id!=".$appID;
			}
			$employeeID 			= esc_sql($employeeID);
			$startDate 				= esc_sql($startDate);
			$select	 				= "SELECT service_id,start_date,end_date FROM {$wpdb->prefix}bmify_appointments WHERE employee_id=".$employeeID." AND start_date>='".$startDate."' AND start_date<'".$endDate."' AND status in ('pending', 'approved')".$newOption;
			$appointments 			= $wpdb->get_results( $select, OBJECT  );
			foreach($appointments as $key => $appointment){
				$newServiceID		= $appointment->service_id;
				$newServiceID 		= esc_sql($newServiceID);
				$select 			= "SELECT buffer_before, buffer_after FROM {$wpdb->prefix}bmify_services WHERE id=".$newServiceID;
				$results 			= $wpdb->get_results( $select, OBJECT  );
				$bufferBefore		= $results[0]->buffer_before / 60;
				$bufferAfter		= $results[0]->buffer_after / 60;
				$startAppointment 	= date('H', strtotime($appointment->start_date))*60 + date('i', strtotime($appointment->start_date)) - $bufferBefore;
				$endAppointment		= date('H', strtotime($appointment->end_date))*60 + date('i', strtotime($appointment->end_date)) + $bufferAfter;
				$appointmentArray[$key]['start'] 	= $startAppointment;
				$appointmentArray[$key]['end'] 		= $endAppointment;
			}
			// получение всевозможных слотов, которых нужно удалить из ОБЩЕГО масива (все ВСТРЕЧИ того дня)
			$removableValues = array();
			foreach($appointmentArray as $result){
				$min 	= intval($result['start']) - $summaryDuration;
				$max 	= intval($result['end']) + ($serviceBuffBefore / 60); // здесь мы прибавляем время buffer_before для того, чтобы работник смог подготовиться к работе после каждого перерыва
				$removableValues[] 	= array_filter($allArray, function ($value) use($min,$max)  { return ($value > $min && $value < $max); });
			}
			$removableArr = array();
			foreach($removableValues as $results){
				foreach($results as $result){
					$removableArr[] = $result;
				}
			}
			// удаление полученных слотов, которых нужно было удалить из ОБЩЕГО массива (все ВСТРЕЧИ того дня)
			$difference = array_diff($difference,$removableArr);
			//****************************************************************************************************************************************
			
			//****************************************************************************************************************************************
			// получение всевозможных GOOGLE встреч без учета встреч, созданных Bookmify, выбранного работника для выбранной даты в массиве
			$googleData 	= HelperEmployees::getGoogleData($employeeID);
			$accessToken	= '';
			$calID          = '';
			if($googleData != NULL){
				$googleData 	= json_decode(stripslashes($googleData), true);
				$accessToken 	= $googleData['accessToken'];
				$calID 			= $googleData['calendarID'];
				
				$google 		= new GoogleCalendarProject();
				if($accessToken != ''){
					$events		= $google->getGoogleEvents($employeeID,$chosenDay);

					// получение всевозможных слотов, которых нужно удалить из ОБЩЕГО масива (все ВСТРЕЧИ того дня)
					$removableValues = array();
					foreach($events as $result){
						$min 	= intval($result['start']) - $summaryDuration;
						$max 	= intval($result['end']);
						$removableValues[] 	= array_filter($allArray, function ($value) use($min,$max)  { return ($value > $min && $value < $max); });
					}
					$removableArr = array();
					foreach($removableValues as $results){
						foreach($results as $result){
							$removableArr[] = $result;
						}
					}
					// удаление полученных слотов, которых нужно было удалить из ОБЩЕГО массива (все ВСТРЕЧИ того дня)
					$difference = array_diff($difference,$removableArr);
				}
			}
			//****************************************************************************************************************************************
			
			$html = '';
			
			
			// получение ГОТОВОГО массива с учетом полученного времени с минимум установленным временем до заказа, если выбран сегодняшний или же завтрашний день
			if($minTimeInMinutes != 0){
			
				$minTimeArray = array_filter($difference, function($value) use($minTimeInMinutes) {return ($value >= $minTimeInMinutes); });
				foreach($minTimeArray as $result){
					$resHours = intval($result/60);
					if($resHours < 10){$resHours = "0".$resHours;}
					$resMinutes = $result % 60;
					if($resMinutes < 10){$resMinutes = "0".$resMinutes;}
					$hourMinutes = $resHours.":".$resMinutes;
					$html .= '<div>'.$hourMinutes.'</div>';
				}
			}

			
			// получение ГОТОВОГО массива, если выбранная дата не явлется ни сегодняшней и ни завтрашней
			if($minTimeInMinutes == 0){
				foreach($difference as $result){
					$resHours = intval($result/60);
					if($resHours < 10){$resHours = "0".$resHours;}
					$resMinutes = $result % 60;
					if($resMinutes < 10){$resMinutes = "0".$resMinutes;}
					$hourMinutes = $resHours.":".$resMinutes;
					$html .= '<div>'.$hourMinutes.'</div>';
				}
			}
			if($html == ''){
				$html .= '<div class="nodata">'.esc_html__('Busy Day', 'bookmify').'</div>';
			}
			
			
			// Отправка обработанных данных на jQuery
			$buffyArray = array(
				'bookmify_be_data' 		=> $html,
				'html2' 				=> $appointmentArray,
			);
			
			if ( true === $isAjaxCall ) {die(json_encode($buffyArray));} 
			else {return json_encode($buffyArray);}
			
		}
	}
	
	
	
	
	public function ajaxQueryGetExtrasOfServiceForAppointment(){
		global $wpdb;
		$isAjaxCall 			= false;
		$customerData 			= array();
		
		if (!empty($_POST['serviceID'])) {
			$isAjaxCall			= true;
			$customerIDs 		= $_POST['customerIDs'];
			$serviceID 			= $_POST['serviceID'];
			$employeeID 		= $_POST['employeeID'];
			if(!empty($_POST['customerData'])){
				$customerData 		= $_POST['customerData'];
			}
			
			$html				= HelperAppointments::getExtrasAgain($customerIDs,$serviceID,$employeeID,$customerData);
			
			$buffy2 			= '';
			$buffy3 			= '';
			$duration			= '';
			$min				= '';
			$max				= '';
			if($serviceID != '' && $employeeID != ''){
				$buffy2 		= HelperAppointments::getServiceExtraSingle($serviceID, $employeeID);
				$buffy3	 		= HelperAppointments::getServiceExtraTotal($serviceID, $employeeID);
				$duration		= HelperAppointments::getHiddenValuesAgain($serviceID,$employeeID,"duration");
				$min			= HelperAppointments::getHiddenValuesAgain($serviceID,$employeeID,"min");
				$max			= HelperAppointments::getHiddenValuesAgain($serviceID,$employeeID,"max");
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
			$buffy2 = preg_replace($search, $replace, $buffy2);
			$buffy3 = preg_replace($search, $replace, $buffy3);


			$buffyArray = array(
				'bookmify_be_data' 		=> $html,
				'buffy2' 				=> $buffy2,
				'buffy3' 				=> $buffy3,
				'duration' 				=> $duration,
				'min' 					=> $min,
				'max' 					=> $max,
			);
			
			if ( true === $isAjaxCall ) {die(json_encode($buffyArray));} 
			else {return json_encode($buffyArray);}
			
		}
	}
	
	public function ajaxQueryGetPaymentsCustomersForAppointment(){
		global $wpdb;
		$isAjaxCall 			= false;
		$customerData 			= array();
		
		if (!empty($_POST['serviceID'])) {
			$isAjaxCall			= true;
			$customerIDs 		= $_POST['customerIDs'];
			$serviceID 			= $_POST['serviceID'];
			$employeeID 		= $_POST['employeeID'];
			
			if(!empty($_POST['customerData'])){
				$customerData 	= $_POST['customerData'];
			}
			
			$allPayments		= HelperAppointments::getPaymentAgain($customerIDs,$serviceID,$employeeID,$customerData);
			
			$paymentSingle 		= '';
			if($serviceID != '' && $employeeID != ''){
				$paymentSingle 	= HelperAppointments::getPaymentSingle($serviceID, $employeeID);
			}
			
			
			$buffyArray = array(
				'bookmify_be_data' 		=> $allPayments,
				'buffy4' 				=> $paymentSingle,
			);
			
			if ( true === $isAjaxCall ) {die(json_encode($buffyArray));} 
			else {return json_encode($buffyArray);}
			
		}
	}
	public function ajaxQueryEditAppointment(){
		global $wpdb;
		$isAjaxCall 	= false;
		$html 			= '';
		$serviceID 		= '';
		$employeeID 	= '';
		if (!empty($_POST['bookmify_data'])) {
			$isAjaxCall = true;
			$id 		= $_POST['bookmify_data'];
			
			// SELECT
			$id 			= esc_sql($id);
			$query 			= "SELECT id,service_id,employee_id,status,start_date FROM {$wpdb->prefix}bmify_appointments WHERE id=".$id;
			$appointments 	= $wpdb->get_results( $query, OBJECT  );
			
			$appointmentStartDate 	= date("Y-m-d H:i:s", strtotime($appointments[0]->start_date));
			$today					= HelperTime::getCurrentDateTime();
			if($appointmentStartDate <= $today && (get_option('bookmify_be_old_appointment_action', '') == '')){ // if closed item do nothing
				$html				= 'warning';
			}else{
			
			
				foreach($appointments as $appointment){
					$appointmentID			= $appointment->id;
					$customerIDs 			= HelperAppointments::bookmifyCustomerIdsByAppointmentID($appointmentID);
					$peopleCountDecode		= HelperAppointments::peopleCountByAppointmentID($appointmentID, 'decode');

					$serviceID			 	= $appointment->service_id;
					$serviceValue			= Helper::bookmifyGetServiceCol($serviceID);
					$employeeID				= $appointment->employee_id;

					$status					= $appointment->status;


					$extrasTabContent 		= HelperAppointments::getExtraServicesOnEdit($customerIDs, $serviceID, $employeeID, $appointmentID, $peopleCountDecode);
					$paymentTabContent 		= HelperAppointments::getPaymentsOnEdit($customerIDs,$serviceID,$employeeID,$appointmentID,$peopleCountDecode);

					$detailsTabContent 		= HelperAppointments::detailsTabContent($appointmentID,$status);
					$html .= '<div class="bookmify_be_popup_form_wrap" data-entity-id="'.$appointmentID.'">
								'.HelperAppointments::serviceListAsNano().'
								'.HelperAppointments::categoryListAsNano().'
								'.HelperAppointments::customerListAsNano().'
								'.HelperAppointments::employeeListAsNano().'
								'.HelperAppointments::locationListAsNano().'
								'.HelperAppointments::numberListForPeople().'
								'.HelperAppointments::appointmentTime('appointment_time').'
								<div class="bookmify_be_popup_form_position_fixer">
									<div class="bookmify_be_popup_form_bg">
										<div class="bookmify_be_popup_form">

											<div class="bookmify_be_popup_form_header">
												<h3>'.esc_html__('Edit Appointment','bookmify').'</h3>
												<span class="closer"></span>
											</div>

											<div class="bookmify_be_popup_form_content">
												<div class="bookmify_be_popup_form_content_in">

													<div class="bookmify_be_popup_form_fields">


														'.HelperAppointments::getHiddenValues($serviceID,$employeeID).'

														<div class="bookmify_be_appointmentstabs_wrap bookmify_be_tab_wrap">
															<div class="bookmify_be_link_tabs">
																<ul class="bookmify_be_appointmentstabs_nav">
																	<li class="active"><a class="bookmify_be_tab_link" href="#">'.esc_html__('Details','bookmify').'</a></li>
																	<li><a class="bookmify_be_tab_link" href="#">'.esc_html__('Extras','bookmify').'</a></li>
																	<li><a class="bookmify_be_tab_link" href="#">'.esc_html__('Payment','bookmify').'</a></li>
																</ul>
															</div>
															<div class="bookmify_be_appointmentstabs_content bookmify_be_content_tabs">
																<div class="bookmify_be_tab_pane active">'.$detailsTabContent.'</div>
																<div class="bookmify_be_tab_pane">'.$extrasTabContent.'</div>
																<div class="bookmify_be_tab_pane">'.$paymentTabContent.'</div>
															</div>
														</div>


													</div>

												</div>
											</div>

											'.Helper::bookmifyPopupSaveSection().'

										</div>
									</div>
								</div>
							</div>';
				}
			}
		}
		$buffy2 = '';
		$buffy3 = '';
		$buffy4 = '';
		if($html != 'warning'){
			if($serviceID != '' && $employeeID != ''){
				$buffy2 = HelperAppointments::getServiceExtraSingle($serviceID, $employeeID);
				$buffy3 = HelperAppointments::getServiceExtraTotal($serviceID, $employeeID);
				$buffy4 = HelperAppointments::getPaymentSingle($serviceID, $employeeID);
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
		$buffy2 = preg_replace($search, $replace, $buffy2);
		$buffy3 = preg_replace($search, $replace, $buffy3);
		$buffy4 = preg_replace($search, $replace, $buffy4);

		$buffyArray = array(
			'bookmify_be_data' 		=> $buffy,
			'bookmify_be_id' 		=> $id,
			'buffy2' 				=> $buffy2,
			'buffy3' 				=> $buffy3,
			'buffy4' 				=> $buffy4,
		);

		if ( true === $isAjaxCall ){die(json_encode($buffyArray));} 
		else{return json_encode($buffyArray);}

	}
	
	public function ajaxQueryDetailsAppointment(){
		global $wpdb;
		$isAjaxCall 	= false;
		$html 			= '';
		$serviceID 		= '';
		$employeeID 	= '';
		if (!empty($_POST['bookmify_data'])) {
			$isAjaxCall 	= true;
			$id 			= $_POST['bookmify_data'];

			// SELECT
			$id 			= esc_sql($id);
			$query 			= "SELECT id FROM {$wpdb->prefix}bmify_appointments WHERE id=".$id;
			$appointments 	= $wpdb->get_results( $query, OBJECT  );
			if(count($appointments) > 0){
				$html .= '<div class="bookmify_be_popup_form_wrap" data-entity-id="'.$id.'">
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
													'.HelperAppointments::detailsOfAppointment($id).'
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
	
	
	public function ajaxQueryInsertOrUpdateAppointment(){
		global $wpdb;
		$isAjaxCall 					= true;
		$arrayToSendNotification		= array();
		
		// **************************************************************************************************************************
		// UPDATE EXISTING APPOINTMENT
		// **************************************************************************************************************************
		if ($_POST['insertOrUpdate'] 	== 'update') {
			
			
			// update details
			if (!empty($_POST['details'])) {
				$isAjaxCall				= true;
				$details 				= json_decode(stripslashes($_POST['details']));
				$appointmentID 			= $details->ID;
				$serID 					= $details->serID;
				$empID 					= $details->empID;
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
				
				// get old information
				$appointmentID			= esc_sql($appointmentID);
				$query 					= "SELECT service_id,employee_id,status,start_date FROM {$wpdb->prefix}bmify_appointments WHERE id=".$appointmentID;
				$results 				= $wpdb->get_results( $query, OBJECT  );
				$oldServiceID			= $results[0]->service_id;
				$oldEmployeeID			= $results[0]->employee_id;
				$oldStatus				= $results[0]->status;
				$oldDate 				= date("Y-m-d",strtotime($results[0]->start_date));
				$oldTime 				= date("H:i:s",strtotime($results[0]->start_date));
				
				
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
					
					
					// если работник тотже
					if($oldEmployeeID == $empID){
						// 1. если сервис изменился
						if($oldServiceID != $serID){
							$serviceName					= Helper::bookmifyGetServiceCol($oldServiceID);
							$employeeEmail					= Helper::bookmifyGetEmployeeCol($oldEmployeeID, 'email');
//							$infoObject->sendTo				= 'employee';
							$infoObject->appID				= (int)$appointmentID;
							$infoObject->userID				= (int)$oldEmployeeID;
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
//							$infoObject->sendTo				= 'employee';
							$infoObject->appID				= (int)$appointmentID;
							$infoObject->userID				= (int)$empID;
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
//							$infoObject->sendTo				= 'employee';
							$infoObject->appID				= (int)$appointmentID;
							$infoObject->userID				= (int)$empID;
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
//							$infoObject->sendTo				= 'employee';
							$infoObject->appID				= (int)$appointmentID;
							$infoObject->userID				= (int)$empID;
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
//							$infoObject->sendTo				= 'employee';
							$infoObject->appID				= (int)$appointmentID;
							$infoObject->userID				= (int)$empID;
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
					}else{ // если работник изменился
						$serviceName					= Helper::bookmifyGetServiceCol($oldServiceID);
						$employeeEmail					= Helper::bookmifyGetEmployeeCol($oldEmployeeID, 'email');
//						$infoObject->sendTo				= 'employee';
						$infoObject->appID				= (int)$appointmentID;
						$infoObject->userID				= (int)$oldEmployeeID;
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
//						$this->pretraintmentToSendNotification($infoObject); // отправка уведомление старому работнику об отмене встречи
						
						$infoObject						= new \stdClass();
						$serviceName					= Helper::bookmifyGetServiceCol($serID);
						$employeeEmail					= Helper::bookmifyGetEmployeeCol($empID, 'email');
//						$infoObject->sendTo				= 'employee';
						$infoObject->appID				= (int)$appointmentID;
						$infoObject->userID				= (int)$empID;
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
//						$this->pretraintmentToSendNotification($infoObject); // отправка уведомление новому работнику
					}
				}
				
				$wpdb->query($wpdb->prepare( "UPDATE {$wpdb->prefix}bmify_appointments SET service_id=%d, employee_id=%d, start_date=%s, end_date=%s, info=%s, status=%s WHERE id=%d", $serID, $empID, $startDate, $endDate, $info, $status, $appointmentID));
			}
			
			// update customers and their extra services
			
			if (!empty($_POST['allCustomers'])) {
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
					if($_POST['haveExtras'] == "yes"){
						$allCustomersAsArray[$customerID]['extras']		= $customer->extras;
					}
				}
				
				
				$appointmentID			= esc_sql($appointmentID);
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
						if($_POST['haveExtras'] == "yes"){
							$extras			= $allCustomersAsArray[$existingCustomer]['extras'];
						}
						$paymentPrice		+= ($peopleCount * $serPrice);
						
						$appointmentID		= esc_sql($appointmentID);
						$existingCustomer	= esc_sql($existingCustomer);
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
							$custAppID			= esc_sql($custAppID);
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
						
						// get payment ID
						$custAppID		= esc_sql($custAppID);
						$query 			= "SELECT payment_id FROM {$wpdb->prefix}bmify_customer_appointments WHERE id=".$custAppID;
						$results 		= $wpdb->get_results( $query, OBJECT  );
						$paymentID 		= $results[0]->payment_id;
						
						// get OLD total price
						$paymentID		= esc_sql($paymentID);
						$query 			= "SELECT total_price FROM {$wpdb->prefix}bmify_payments WHERE id=".$paymentID;
						$results 		= $wpdb->get_results( $query, OBJECT  );
						$oldTotalPrice	= $results[0]->total_price;
						
						$taxCustomer	= HelperAppointments::taxOfCustomer($appointmentID,$existingCustomer);
						$paymentPrice	= $paymentPrice * (($taxCustomer+100)/100);
						$paymentPrice	= floor($paymentPrice*100)/100;
						// change price
						$wpdb->query($wpdb->prepare( "UPDATE {$wpdb->prefix}bmify_payments SET total_price=%f WHERE id=%d", $paymentPrice, $paymentID));
						
						
						// notification	|| send notification to existing customer
						if($checked == 'public'){
							$infoObject			= new \stdClass();
							// если работник тотже
							if($oldEmployeeID == $empID){
								// 1. если сервис изменился
								if($oldServiceID != $serID){									
									$serviceName					= Helper::bookmifyGetServiceCol($serID);
									$customerName					= Helper::bookmifyGetCustomerCol($existingCustomer);
									$customerEmail					= Helper::bookmifyGetCustomerCol($existingCustomer, 'email');
//									$infoObject->sendTo				= 'customer';
									$infoObject->appID				= (int)$appointmentID;
									$infoObject->userID				= (int)$existingCustomer;
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
//									$infoObject->sendTo				= 'customer';
									$infoObject->appID				= (int)$appointmentID;
									$infoObject->userID				= (int)$existingCustomer;
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
//									$infoObject->sendTo				= 'customer';
									$infoObject->appID				= (int)$appointmentID;
									$infoObject->userID				= (int)$existingCustomer;
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
//									$infoObject->sendTo				= 'customer';
									$infoObject->appID				= (int)$appointmentID;
									$infoObject->userID				= (int)$existingCustomer;
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
//									$infoObject->sendTo				= 'customer';
									$infoObject->appID				= (int)$appointmentID;
									$infoObject->userID				= (int)$existingCustomer;
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
//								$infoObject->sendTo				= 'customer';
								$infoObject->appID				= (int)$appointmentID;
								$infoObject->userID				= (int)$existingCustomer;
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
//								$infoObject->sendTo				= 'customer';
								$infoObject->appID				= (int)$appointmentID;
								$infoObject->userID				= (int)$existingCustomer;
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
						if($_POST['haveExtras'] == "yes"){
							$extras			= $allCustomersAsArray[$newCustomer]['extras'];
						}
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
						$taxCustomer	= HelperAppointments::taxOfService($serID);
						$paymentPrice	= $paymentPrice * (($taxCustomer+100)/100);
						$paymentPrice	= floor($paymentPrice*100)/100;
						$taxIDsObject	= HelperAppointments::taxIDsObjectCreatorForPayment($serID);
						$wpdb->query($wpdb->prepare( "INSERT INTO {$wpdb->prefix}bmify_payments ( total_price, created_date, tax_ids ) VALUES ( %f, %s, %s )", $paymentPrice, $createdDate, $taxIDsObject ));
						
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
//							$infoObject->sendTo				= 'customer';
							$infoObject->appID				= (int)$appointmentID;
							$infoObject->userID				= (int)$newCustomer;
							$infoObject->service_name 		= $serviceName;
							$infoObject->appointment_date 	= $dateApp;
							$infoObject->appointment_time 	= $timeAppStart;
							$infoObject->status			 	= $status;
							$infoObject->customer_name	 	= $customerName;
							$infoObject->customer_email	 	= $customerEmail;
							$arrayToSendNotification[]		= array($infoObject, '', 'customer');
//							$this->pretraintmentToSendNotification($infoObject); // отправка уведомление новому клиенту
						}
						
						$paymentPrice = 0;
					}
				}

				// Delete released Customer Appointment
				if(!empty($releasedCustomers)){
					foreach($releasedCustomers as $releasedCustomer){
						// получение нужных данных
						$releasedCustomer	= esc_sql($releasedCustomer);
						$appointmentID		= esc_sql($appointmentID);
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
//							$infoObject->sendTo				= 'customer';
							$infoObject->appID				= (int)$appointmentID;
							$infoObject->userID				= (int)$releasedCustomer;
							$infoObject->service_name 		= $serviceName;
							$infoObject->appointment_date 	= $oldDate;
							$infoObject->appointment_time 	= $oldTime;
							$infoObject->status			 	= 'rejected';
							$infoObject->customer_name	 	= $customerName;
							$infoObject->customer_email	 	= $customerEmail;
							$arrayToSendNotification[]		= array($infoObject, '', 'customer');
//							$this->pretraintmentToSendNotification($infoObject); // отправка уведомление новому клиенту
						}
					}
				}
				
				// since v1.3.8
				self::sendArrayNotification($arrayToSendNotification);
				
				// если работник тотже
				if($oldEmployeeID == $empID){
					if(($status == 'rejected') || ($status == 'canceled')){
						$googleCal = new GoogleCalendarProject();
						$googleCal->deleteEvent($appointmentID);
					}else{
						$googleCal = new GoogleCalendarProject();
						$googleCal->updateEvent($appointmentID);
					}
				}else{ // если работник изменился
					$googleCal = new GoogleCalendarProject();
					$googleCal->deleteEvent($appointmentID,$oldEmployeeID);

					$googleCal = new GoogleCalendarProject();
					$googleCal->insertEvent($appointmentID,$empID);
				}
			}
			
			
		}else{
			// **************************************************************************************************************************
			// INSERT NEW APPOINTMENT
			// **************************************************************************************************************************
			
			// insert details
			if (!empty($_POST['details'])) {
				$isAjaxCall				= true;
				$details 				= json_decode(stripslashes($_POST['details']));
				$serID 					= $details->serID;
				$empID 					= $details->empID;
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
				
				
				$wpdb->query($wpdb->prepare( "INSERT INTO {$wpdb->prefix}bmify_appointments ( service_id, employee_id, start_date, end_date, info, status ) VALUES ( %d, %d, %s, %s, %s, %s )", $serID, $empID, $startDate, $endDate, $info, $status ));
				
				// get this appoointment ID
				$query 					= "SELECT id FROM {$wpdb->prefix}bmify_appointments ORDER BY id DESC LIMIT 1;";
				$results 				= $wpdb->get_results( $query, OBJECT  );
				$appointmentID 			= $results[0]->id;
				
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
					$infoObject						= new \stdClass();
					$serviceName					= Helper::bookmifyGetServiceCol($serID);
					$employeeEmail					= Helper::bookmifyGetEmployeeCol($empID, 'email');
//					$infoObject->sendTo				= 'employee';
					$infoObject->appID				= (int)$appointmentID;
					$infoObject->userID				= (int)$empID;
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
//					$this->pretraintmentToSendNotification($infoObject); // отправка уведомление новому работнику
				}
			}
			
			// insert customer appoinments and extras
			if (!empty($_POST['allCustomers'])) {
				$isAjaxCall				= true;
				$allCustomers 			= json_decode(stripslashes($_POST['allCustomers']));
				$paymentPrice		 	= 0;
				
				
				$createdDate 			= HelperTime::getCurrentDateTime();
				foreach($allCustomers as $customer){
					$customerID			= $customer->ID;
					$serPrice			= $customer->serPrice;
					$peopleCount 		= $customer->peopleCount;
					$status				= $customer->status;
					$taxCustomer		= HelperAppointments::taxOfService($serID);
					if($_POST['haveExtras'] == "yes"){
						$extras				= $customer->extras;
					}
					$createdFrom		= 'backend';
					$paymentPrice		+= ($peopleCount * $serPrice);
					$taxService			= floor($peopleCount * $serPrice * $taxCustomer)/100;
					$taxExtra			= 0;
					
					$wpdb->query($wpdb->prepare( "INSERT INTO {$wpdb->prefix}bmify_customer_appointments ( customer_id, appointment_id, number_of_people, price, status, created_from, created_date ) VALUES ( %d, %d, %d, %f, %s, %s, %s )", $customerID, $appointmentID, $peopleCount, $serPrice, $status, $createdFrom, $createdDate ));

					// get new customer appointment id
					$query 				= "SELECT id FROM {$wpdb->prefix}bmify_customer_appointments ORDER BY id DESC LIMIT 1;";
					$results 			= $wpdb->get_results( $query, OBJECT  );
					$custAppID 			= $results[0]->id;
					
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
							$taxExtra		+= floor($result->quantity * $result->price * $peopleCount * $taxCustomer)/100;
						}
					}
					// insert new payment
					$paymentPrice	= $paymentPrice + $taxService + $taxExtra;
					$taxIDsObject	= HelperAppointments::taxIDsObjectCreatorForPayment($serID);
					$wpdb->query($wpdb->prepare( "INSERT INTO {$wpdb->prefix}bmify_payments ( total_price, created_date, tax_ids ) VALUES ( %f, %s, %s )", $paymentPrice, $createdDate, $taxIDsObject ));

					// get this payment ID
					$query 				= "SELECT id FROM {$wpdb->prefix}bmify_payments ORDER BY id DESC LIMIT 1;";
					$results 			= $wpdb->get_results( $query, OBJECT  );
					$paymentID 			= $results[0]->id;

					// insert paymentID to last customer appointment 
					$wpdb->query($wpdb->prepare( "UPDATE {$wpdb->prefix}bmify_customer_appointments SET payment_id=%d WHERE id=%d", $paymentID, $custAppID));
					
					// notification	|| send notification to new customer
					if($checked == 'public'){
						$infoObject						= new \stdClass();
						$serviceName					= Helper::bookmifyGetServiceCol($serID);
						$customerName					= Helper::bookmifyGetCustomerCol($customerID);
						$customerEmail					= Helper::bookmifyGetCustomerCol($customerID, 'email');
//						$infoObject->sendTo				= 'customer';
						$infoObject->appID				= (int)$appointmentID;
						$infoObject->userID				= (int)$customerID;
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
				
				// since v1.3.8
				self::sendArrayNotification($arrayToSendNotification);
				
				
				// create google calendar event
				$googleCal 			= new GoogleCalendarProject();
				$googleCalEventID 	= $googleCal->insertEvent($appointmentID);
				
				//update appointment google calendar event id 
				$wpdb->query($wpdb->prepare( "UPDATE {$wpdb->prefix}bmify_appointments SET google_calendar_event_id=%s WHERE id=%d", $googleCalEventID, $appointmentID));
				
			}
			
			
		}
		$page 				= 1;
		$filter 			= array();
		$search_text 		= '';
		$employeeIDs		= array();
		$filterByService	= array();
		$filterDateRange 	= array();
		$order				= 'ASC';
		$html 				= '';
		if(!empty($_POST['do'])){
			
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
									  
							WHERE";



			$html 			= '';
			$customlist 	= array();
			
			
			if(!empty($filterByService)){
				$filterByService = esc_sql($filterByService);
				$query .= " a.service_id IN (" . implode(",", array_map("intval", $filterByService)) . ") AND";
			}
			if($filterByCustomer != ''){
				$filterByCustomer = esc_sql($filterByCustomer);
				$query .= " ca.customer_id = '".$filterByCustomer."' AND";
			}
			if($filterByEmployee != ''){
				$filterByEmployee = esc_sql($filterByEmployee);
				$query .= " a.employee_id = '".$filterByEmployee."' AND";
			}
			if($filterByStatus != ''){
				$filterByStatus = esc_sql($filterByStatus);
				$query .= " a.status = '".$filterByStatus."' AND";
			}
			if(!empty($filterDateRange)){
				$filterDateRange = esc_sql($filterDateRange);
				$query .= " (a.start_date BETWEEN '".$filterDateRange[0]."' AND '".$filterDateRange[1]."') AND";
			}

			
			
			$query = rtrim($query, 'AND');
			$query = rtrim($query, 'WHERE');
			
			
			
			$Querify  		= new Querify( $query, 'appointment' );
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
			if(count($customlist) == 0){
				$html .= Helper::bookmifyBeNoItems();
			}
			foreach($customlist as $day => $appointments){
				$list = '';
				$appCount = 0;
				$pendCount = 0;
				
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
						if(get_option('bookmify_be_old_appointment_action', '') == 'on'){
							$appDateStatus 		= 'bookmify_be_open_item';
							$appDateStatusBtn 	= 'bookmify_be_open_btn';
						}else{
							if($appointmentStartDate <= $today){
								$appDateStatus		= 'bookmify_be_closed_item';
								$appDateStatusBtn	= 'bookmify_be_closed_btn';
							}else{
								$appDateStatus 		= 'bookmify_be_open_item';
								$appDateStatusBtn 	= 'bookmify_be_open_btn';
							}
						}

						$list .= '<div data-entity-id="'.$appointmentID.'" class="bookmify_be_appointment_item bookmify_be_list_item bookmify_be_animated '.$appDateStatus.'">

									<div class="bookmify_be_list_item_in">
										<div class="bookmify_appointment_heading bookmify_be_list_item_header">
											<div class="bookmify_heading_in header_in">


												<div class="appointment_info">
													<span class="appointment_time"><span><img class="bookmify_be_svg" src="'.BOOKMIFY_ASSETS_URL.'img/clock.svg" alt="" />'.$time.'</span></span>
													<span class="appointment_customer"><span>'.Helper::bookmifyGetCustomersCol($appointmentID).'</span></span>
													<span class="appointment_service"><span>'.Helper::titleDecryption(Helper::bookmifyGetServiceCol($appointment->appServiceID)).'</span></span>
													<span class="appointment_employee"><span>'.Helper::bookmifyGetEmployeeCol($appointment->appEmployeeID).'</span></span>
													<span class="appointment_price"><span>'.$price.'</span></span>
													<span class="appointment_duration"><span>'.$duration.'</span></span>
													<span class="appointment_status '.$status.'"><span>'.$statusText.'</span></span>
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
													<div class="btn_item btn_delete '.$appDateStatusBtn.'">
														<a href="#" class="bookmify_be_delete" data-entity-id="'.$appointmentID.'">
															<img class="bookmify_be_svg" src="'.BOOKMIFY_ASSETS_URL.'img/delete.svg" alt="" />
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
			

			$html .= $Querify->getPagination( 1, 'bookmify_be_pagination appointment');
			
			
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
			'number'				=> Helper::bookmifyItemsCount('appointments'),
		);

		if ( true === $isAjaxCall ){die(json_encode($buffyArray));} 
		else{return json_encode($buffyArray);}
	}
	
	// commented since 1.3.8
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
		return $array;
    }
	
	public function bookmifyCheckVarViaAJAX(){
		global $wpdb;
		$isAjaxCall 	= true;
		$checkSender 	= Helper::checkForSender();
		$buffyArray 	= array(
			'bookmify_be_data' 		=> $checkSender,
		);

		if ( true === $isAjaxCall ){die(json_encode($buffyArray));} 
		else{return json_encode($buffyArray);}
	
	}
	
	/**
	 * @since 1.0.0
	 * @access public
	*/
	public function ajaxQueryDeleteAppointment(){
		global $wpdb;
		$isAjaxCall 	= false;
		$appID 			= '';
		
		if (!empty($_POST['ID'])){
			$isAjaxCall = true;
			$appID 		= $_POST['ID'];
			
			$appID		= esc_sql($appID);
			$query 		= "SELECT start_date FROM {$wpdb->prefix}bmify_appointments WHERE id=".$appID;
			$results 	= $wpdb->get_results( $query, OBJECT  );
			
			$appointmentStartDate 	= date("Y-m-d H:i:s", strtotime($results[0]->start_date));
			$today					= HelperTime::getCurrentDateTime();
			if($appointmentStartDate <= $today && (get_option('bookmify_be_old_appointment_action', '') == '')){ // if closed item do nothing
				$html				= 'warning';
			}else{
				$html 				= '';
				
				
				// DELETE
				$appID				= esc_sql($appID);
				$query 				= "SELECT payment_id,id FROM {$wpdb->prefix}bmify_customer_appointments WHERE appointment_id=".$appID;
				$results 			= $wpdb->get_results( $query, OBJECT  );
				foreach($results as $result){
					$wpdb->query($wpdb->prepare( "DELETE FROM {$wpdb->prefix}bmify_customer_appointments_extras WHERE id=%d",$result->id));
					$wpdb->query($wpdb->prepare( "DELETE FROM {$wpdb->prefix}bmify_payments WHERE id=%d",$result->payment_id));
				}

				$wpdb->query($wpdb->prepare( "DELETE FROM {$wpdb->prefix}bmify_customer_appointments WHERE appointment_id=%d", $appID));
				$wpdb->query($wpdb->prepare( "DELETE FROM {$wpdb->prefix}bmify_appointments WHERE id=%d", $appID));
				
				
				// DELETE google calendar event
//				$googleCal 			= new GoogleCalendarProject();
//				$googleCal->deleteEvent($appID);
			}
			
			$htmlResult			= '';
			$page 				= 1;
			$filter 			= array();
			$search_text 		= '';
			$employeeIDs		= array();
			$filterByService	= array();
			$filterDateRange 	= array();
			$order				= 'ASC';
			$html 				= '';

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

							WHERE";



			$html 			= '';
			$customlist 	= array();


			if(!empty($filterByService)){
				$filterByService = esc_sql($filterByService);
				$query .= " a.service_id IN (" . implode(",", array_map("intval", $filterByService)) . ") AND";
			}
			if($filterByCustomer != ''){
				$filterByCustomer = esc_sql($filterByCustomer);
				$query .= " ca.customer_id = '".$filterByCustomer."' AND";
			}
			if($filterByEmployee != ''){
				$filterByEmployee = esc_sql($filterByEmployee);
				$query .= " a.employee_id = '".$filterByEmployee."' AND";
			}
			if($filterByStatus != ''){
				$filterByStatus = esc_sql($filterByStatus);
				$query .= " a.status = '".$filterByStatus."' AND";
			}
			if(!empty($filterDateRange)){
				$filterDateRange = esc_sql($filterDateRange);
				$query .= " (a.start_date BETWEEN '".$filterDateRange[0]."' AND '".$filterDateRange[1]."') AND";
			}



			$query = rtrim($query, 'AND');
			$query = rtrim($query, 'WHERE');



			$Querify  		= new Querify( $query, 'appointment' );
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

			$htmlResult .= '<div class="appointments_list bookmify_be_list">';
			if(count($customlist) == 0){
				$html .= Helper::bookmifyBeNoItems();
			}
			foreach($customlist as $day => $appointments){
				$list = '';
				$appCount = 0;
				$pendCount = 0;
				
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
						$statusIcon 				= '<span class="icon"><img class="bookmify_be_svg '.$status.'" src="'.BOOKMIFY_ASSETS_URL.'img/'.$icon.'.svg" alt="" /></span>';
						$statusText 				= $statusIcon.'<span class="text">'.$statusText.'</span>';;
					
						$appointmentStartDate 		= date("Y-m-d H:i:s", strtotime($appointment->appStartDate));
						$today						= HelperTime::getCurrentDateTime();
						if(get_option('bookmify_be_old_appointment_action', '') == 'on'){
							$appDateStatus 			= 'bookmify_be_open_item';
							$appDateStatusBtn 		= 'bookmify_be_open_btn';
						}else{
							if($appointmentStartDate <= $today){
								$appDateStatus		= 'bookmify_be_closed_item';
								$appDateStatusBtn	= 'bookmify_be_closed_btn';
							}else{
								$appDateStatus 		= 'bookmify_be_open_item';
								$appDateStatusBtn 	= 'bookmify_be_open_btn';
							}
						}

						$list .= '<div data-entity-id="'.$appointmentID.'" class="bookmify_be_appointment_item bookmify_be_list_item bookmify_be_animated '.$appDateStatus.'">

									<div class="bookmify_be_list_item_in">
										<div class="bookmify_appointment_heading bookmify_be_list_item_header">
											<div class="bookmify_heading_in header_in">


												<div class="appointment_info">
													<span class="appointment_time"><span><img class="bookmify_be_svg" src="'.BOOKMIFY_ASSETS_URL.'img/clock.svg" alt="" />'.$time.'</span></span>
													<span class="appointment_customer"><span>'.Helper::bookmifyGetCustomersCol($appointmentID).'</span></span>
													<span class="appointment_service"><span>'.Helper::titleDecryption(Helper::bookmifyGetServiceCol($appointment->appServiceID)).'</span></span>
													<span class="appointment_employee"><span>'.Helper::bookmifyGetEmployeeCol($appointment->appEmployeeID).'</span></span>
													<span class="appointment_price"><span>'.$price.'</span></span>
													<span class="appointment_duration"><span>'.$duration.'</span></span>
													<span class="appointment_status '.$status.'"><span>'.$statusText.'</span></span>
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
													<div class="btn_item btn_delete '.$appDateStatusBtn.'">
														<a href="#" class="bookmify_be_delete" data-entity-id="'.$appointmentID.'">
															<img class="bookmify_be_svg" src="'.BOOKMIFY_ASSETS_URL.'img/delete.svg" alt="" />
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
				$htmlResult .= 	'<div class="bookmify_be_day_separator">
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
				$htmlResult .= $list;

			}

			$htmlResult .= '</div>';
			

			$htmlResult .= $Querify->getPagination( 1, 'bookmify_be_pagination appointment');
			
			
			$buffy = '';
			$buffy .= $html;

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
			$buffy 		= preg_replace($search, $replace, $buffy);
			$htmlResult = preg_replace($search, $replace, $htmlResult);

			$buffyArray = array(
				'buffy'					=> $buffy,
				'number'				=> Helper::bookmifyItemsCount('appointments'),
				'list'					=> $htmlResult,
			);

			if ( true === $isAjaxCall ){die(json_encode($buffyArray));} 
			else{return json_encode($buffyArray);}
		}
	}
	
	
	


	/**
	 * @since 1.0.0
	 * @access protected
	*/
	protected function get_page_title() {
		return esc_html__( 'Appointments', 'bookmify' );
	}
}
	

