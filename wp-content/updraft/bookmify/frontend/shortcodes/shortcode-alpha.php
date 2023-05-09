<?php
namespace Bookmify;


use Bookmify\Helper;
use Bookmify\HelperTime;
use Bookmify\HelperService;
use Bookmify\HelperFrontend;
use Bookmify\NotificationManagement;
use Bookmify\HelperAppointments;
use Bookmify\PHPMailerCustom;
use Bookmify\CronNotification;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) { exit; }



class BookmifyAlpha{
	
	
	public static $args;
	
	
    public function __construct(){
		
		add_shortcode( 'bookmify_app_alpha', [$this, 'render'] );
		
		// get employee list of selected service
		add_action( 'wp_ajax_nopriv_ajaxQueryGetEmployeeList', [$this, 'ajaxQueryGetEmployeeList'] );
		add_action( 'wp_ajax_ajaxQueryGetEmployeeList', [$this, 'ajaxQueryGetEmployeeList'] );
		// check customer email
		add_action( 'wp_ajax_nopriv_ajaxCheckCustomerDetails', [$this, 'ajaxCheckCustomerDetails'] );
		add_action( 'wp_ajax_ajaxCheckCustomerDetails', [$this, 'ajaxCheckCustomerDetails'] );
		// create new appointment
		add_action( 'wp_ajax_nopriv_ajaxCreateNewAppointment', [$this, 'ajaxCreateNewAppointment'] );
		add_action( 'wp_ajax_ajaxCreateNewAppointment', [$this, 'ajaxCreateNewAppointment'] );
		// check login credentials
		add_action( 'wp_ajax_nopriv_ajaxCheckLoginCredentials', [$this, 'ajaxCheckLoginCredentials'] );
		add_action( 'wp_ajax_ajaxCheckLoginCredentials', [$this, 'ajaxCheckLoginCredentials'] );
		// since bookmify v1.3.0
		add_action( 'wp_ajax_nopriv_ajaxQueryGetTimeSlotsOfYear', [$this, 'ajaxQueryGetTimeSlotsOfYear'] );
		add_action( 'wp_ajax_ajaxQueryGetTimeSlotsOfYear', [$this, 'ajaxQueryGetTimeSlotsOfYear'] );

    }
	

	public function render( $args, $content = '' ) {
	   	$defaults =	shortcode_atts(
			array(
				'class'				=> '',
				'id'				=> '',
				'category_id'		=> '',
				'employee_id'		=> '',
				'service_id'		=> '',
				'location_id'		=> '',
				'order'				=> '',
			), $args
		);
		
		self::$args = $defaults;
		
		$extraClass 	= '';
		$idAttr 		= '';
		if(self::$args['class'] != ''){
			$extraClass	= self::$args['class'];
		}
		if(self::$args['id'] != ''){
			$idAttr		= ' id="'.self::$args['id'].'"';
		}
		
		$cIDs = $eIDs = $sIDs = $lIDs = $order = '';
		if(self::$args['category_id'] != ''){
			$cIDs		= $args['category_id'];
		}
		if(self::$args['employee_id'] != ''){
			$eIDs		= $args['employee_id'];
		}
		if(self::$args['service_id'] != ''){
			$sIDs		= $args['service_id'];
		}
		if(self::$args['location_id'] != ''){
			$lIDs		= $args['location_id'];
		}
		if(self::$args['order'] != ''){
			$order		= $args['order'];
		}
		
		$serviceAndCategoryList = HelperService::alphaServicesAndCategoriesList($cIDs,$sIDs,$eIDs,$lIDs,$order);
		$servicelist 	= $serviceAndCategoryList[0];
		
		$success 		= HelperFrontend::alphaSucces();
		$hiddenInfo		= HelperFrontend::alphaHiddenInfo($eIDs,$lIDs);
		$dropDownBtn	= '<span class="d_d">'.HelperFrontend::bookmifyFeSVG('drop-down-arrow').'</span>';
		$html  = '';
		$html .= '<div class="bookmify_fe_app bookmify_fe_alpha '.$extraClass.'"'.$idAttr.'>';
		$html .= 	$success; 		// success content
		$html .= 	'<div class="bookmify_fe_app_in">';
		$html .= 	$hiddenInfo;	// hidden information
		$html .= 	'<div class="bookmify_fe_wait">'.HelperFrontend::bookmifyPreloader('', 'loading big').'</div>';
		// header
		$html .= 		'<div class="bookmify_fe_app_header">';
		$html .= 			'<span class="span_bg"></span>';
		$html .= 			'<div><div>';
		
		$categoryFilter = get_option( 'bookmify_be_feoption_category_filter_alpha', 'disabled' );
		
		if($categoryFilter == 'enabled' && $serviceAndCategoryList[2] > 1){
			$html .= $serviceAndCategoryList[1];
			$html .= '<script class="script_services_list" type="text/javascript">'.json_encode($serviceAndCategoryList[3]).'</script>';
		}else{
			$html .= '<h3 class="choose">'.esc_html__('Choose a Service','bookmify').'</h3>';
			$html .= '<script class="script_services_list" type="text/javascript">'.json_encode($serviceAndCategoryList[1]).'</script>';
		}
		$html .= '<script class="script_hi" type="text/javascript"></script>';
		
		$html .= 				'<h3 class="back_to">'.esc_html__('Back to Services','bookmify').'</h3>';
		$html .= 				'<span class="arrow">'.HelperFrontend::bookmifyFeSVG('left-arrow').'</span>';
		$html .= 			'</div></div>';
		$html .= 		'</div>';
		
		// content
		$html .= 		'<div class="bookmify_fe_app_content">';
		
		$copyRightSwitch = get_option( 'bookmify_be_feoption_enable_deveoped_fe', 'enabled' );
		
		$html .= 			'<div class="bookmify_fe_alpha_footer" data-copyright-frenify="'.$copyRightSwitch.'">
								<a target="_blank" href="https://codecanyon.net/item/bookmify-appointment-booking-wordpress-plugin/23837899">
									<span class="frenify_developed_text">Developed by</span>
									<span class="frenify">Frenify</span>
								</a>
							</div>';
		$html .= 			'<div class="bookmify_fe_service_list">'.$servicelist.'</div>';
		$html .= 			'<div class="bookmify_fe_main_list abs">';
		
		// ***************************************************************************************************************************
		// ************************************************       SERVICE       ******************************************************
		// ***************************************************************************************************************************
		$html .= 				'<div class="bookmify_fe_main_list_item service_holder">';
		$html .= 					'<div class="item_header">';
		$html .= 						'<div class="info_top">';
		$html .= 							'<div class="img_holder"></div>';
		$html .= 							'<div class="chosen_holder">';
		$html .= 								'<span class="text">'.esc_html__('you have chosen','bookmify').'</span>';
		$html .= 								HelperFrontend::bookmifyFeSVG('check-box');
		$html .= 							'</div>';
		$html .= 						'</div>';
		$html .= 						'<div class="info_bottom">';
		$html .= 							'<h3></h3>'; // will be added
		
		if(get_option( 'bookmify_be_feoption_service_details', 'enabled' ) == 'enabled'){
			$html .= 							'<span></span>'; // will be added
		}
		
		$html .= 							'<p></p>'; // will be added
		$html .= 						'</div>';
		$html .= 					'</div>';
		$html .= 				'</div>';
		// ***************************************************************************************************************************
		// ************************************************       SPECIALIST       ***************************************************
		// ***************************************************************************************************************************
		$html .= 				'<div class="bookmify_fe_main_list_item specialist_holder">';
		$html .= 					'<div class="item_header">';
		$html .= 						'<div class="header_wrapper">';
		$html .= 							'<span class="item_label">'.esc_html__('Specialist:','bookmify').'</span>';
		$html .= 							'<span class="item_result" data-empty="empty"></span>'; // Will be added
		$html .= 						'</div>';
		$html .= 						'<span class="check_box"><span></span>'.HelperFrontend::bookmifyFeSVG('check-box').'</span>';
		$html .= 						$dropDownBtn;
		$html .= 					'</div>';
		$html .= 					'<div class="item_footer">';
		$html .= 						HelperFrontend::bookmifyPreloader(1, 'loading'); 	// this preloader will be changed to its content
		$html .= 					'</div>';
		$html .= 				'</div>';
		// ***************************************************************************************************************************
		// **************************************************       DATE       *******************************************************
		// ***************************************************************************************************************************
		$html .= 				'<div class="bookmify_fe_main_list_item date_holder">';
		$html .= 					'<div class="item_header">';
		$html .= 						'<div class="header_wrapper">';
		$html .= 							'<span class="item_label">'.esc_html__('Date:','bookmify').'</span>';
		$html .= 							'<span class="item_result" data-empty="empty"></span>'; // Will be added
		$html .= 						'</div>';
		$html .= 						'<span class="check_box"><span></span>'.HelperFrontend::bookmifyFeSVG('check-box').'</span>';
		$html .= 						$dropDownBtn;
		$html .= 					'</div>';
		$html .= 					'<div class="item_footer">';
		$html .= 						HelperFrontend::bookmifyPreloader(1, 'loading'); 	// this preloader will be changed to its content
		$html .= 					'</div>';
		$html .= 				'</div>';
		// ***************************************************************************************************************************
		// *************************************************       CUSTOMER       ****************************************************
		// ***************************************************************************************************************************
		$html .= 				'<div class="bookmify_fe_main_list_item customer_holder">';
		$html .= 					'<div class="item_header">';
		$html .= 						'<div class="header_wrapper">';
		$html .= 							'<span class="item_label">'.esc_html__('Customer:','bookmify').'</span>';
		$html .= 							'<span class="item_result" data-empty="empty"></span>'; // Will be added
		$html .= 						'</div>';
		$html .= 						'<span class="check_box"><span></span>'.HelperFrontend::bookmifyFeSVG('check-box').'</span>';
		$html .= 						$dropDownBtn;
		$html .= 					'</div>';
		$html .= 					'<div class="item_footer">';
		$html .= 						HelperFrontend::bookmifyPreloader(1, 'loading'); 	// this preloader will be changed to its content
		$html .= 					'</div>';
		$html .= 				'</div>';
		
		if(Helper::enabledPaymentMethods() == 1 && get_option('bookmify_be_payment_section', 'default') != 'default'){
			$paymentSectionAction = get_option('bookmify_be_payment_section', 'default');
		}else{
			$paymentSectionAction = 'default';
			$html .= 				'<div class="bookmify_fe_main_list_item payment_holder">';
			$html .= 					'<div class="item_header">';
			$html .= 						'<div class="header_wrapper">';
			$html .= 							'<span class="item_label">'.esc_html__('Payment:','bookmify').'</span>';
			$html .= 							'<span class="item_result" data-empty="empty"></span>'; // Will be added
			$html .= 						'</div>';
			$html .= 						'<span class="check_box"><span></span>'.HelperFrontend::bookmifyFeSVG('check-box').'</span>';
			$html .= 						$dropDownBtn;
			$html .= 					'</div>';
			$html .= 					'<div class="item_footer">';
			$html .= 						HelperFrontend::bookmifyPreloader(1, 'loading'); 	// this preloader will be changed to its content
			$html .= 					'</div>';
			$html .= 				'</div>';
		}
			
		// ***************************************************************************************************************************
		// *************************************************       BOTTOM       ******************************************************
		// ***************************************************************************************************************************
		$html .= 				'<div class="bookmify_fe_main_list_item bottom_holder">';
		$html .= 					'<div class="item_header">';
		
		if($paymentSectionAction == 'dis_en'){
			$html .= 						'<div class="price_holder">';
			$html .= 							'<div class="price_in">';
			$html .= 								'<div class="price_wrap">';
			$html .= 									'<span class="total_text">'.esc_html__('Total Price:', 'bookmify').'</span>';
			$html .= 									'<span class="total_price">'.Helper::bookmifyPriceCorrection(0).'</span>';
			$html .= 								'</div>';
			$html .= 							'</div>';
			$html .= 						'</div>';
		}
			
		
		$html .= 						'<div class="submit_holder disabled">';
		$html .= 							'<a href="#" class="bookmify_fe_main_button bookmify_fe_approve_button enabled">
												<span class="text">'.esc_html__('Approve Booking','bookmify').'</span>
												<span class="save_process">
													<span class="ball"></span>
													<span class="ball"></span>
													<span class="ball"></span>
												</span>
											</a>
											<div class="bookmify_be_paypal_payment_button bookmify_fe_approve_button"></div>
											<a href="#" class="bookmify_be_stripe_payment_button bookmify_fe_approve_button"></a>
										</div>';
		
		$html .= 					'</div>';
		$html .= 				'</div>';
		
		$html .= 			'</div>';
		$html .= 		'</div>';
		
		// footer
		$html .= 		'<div class="bookmify_fe_app_footer"></div>';
		
		$html .= 	'</div>';
		$html .= '</div>';
		
	   	return $html;
	}
	
	
	public function ajaxQueryGetEmployeeList(){
		global $wpdb;
		
		$dropDownBtn			= '<span class="d_d">'.HelperFrontend::bookmifyFeSVG('drop-down-arrow').'</span>';
		$eIDs					= '';
		$lIDs					= '';
		$isAjaxCall 			= false;
		if (!empty($_POST['bookmify_data'])) {
			$isAjaxCall 		= true;
			$serviceID			= $_POST['bookmify_data'];
			
			// employee IDS
			if(isset($_POST['employeeIDs'])){
				$eIDs			= $_POST['employeeIDs'];
			}
			if($eIDs != ''){$employeeIDs = explode(',', $eIDs);}else{$employeeIDs = array();}
			
			// location IDS
			if(isset($_POST['locationIDs'])){
				$lIDs			= $_POST['locationIDs'];
			}
			if($lIDs != ''){$locationIDs = explode(',', $lIDs);}else{$locationIDs = array();}
			

			// **************************************************************************************************************
			// GET EMPLOYEE LIST
			// **************************************************************************************************************
			$serviceID 			= esc_sql($serviceID);
			$query 		 = "SELECT
								e.first_name firstName,
								e.last_name lastName,
								e.id empID,
								es.price servicePrice,
								es.capacity_min capacityMin,
								es.capacity_max capacityMax,
								es.deposit deposit

							FROM 	   	   {$wpdb->prefix}bmify_employee_services es 
								INNER 	JOIN {$wpdb->prefix}bmify_employees e 						ON es.employee_id = e.id
								LEFT 	JOIN {$wpdb->prefix}bmify_employee_locations el				ON el.employee_id = e.id

							WHERE e.visibility='public'";
			
			if(!empty($employeeIDs)){
				$query 		.= " AND e.id IN (" . implode(",", array_map("intval", $employeeIDs)) . ")";
			}
			if(!empty($locationIDs)){
				$query 		.= " AND el.location_id IN (" . implode(",", array_map("intval", $locationIDs)) . ")";
			}
			$query 			.= " AND es.service_id=".$serviceID." ORDER BY e.first_name";
			
			$results 		= $wpdb->get_results( $query, OBJECT  );
			
			$safeEmployeesList = array();
			$employeeList 	= '<ul class="bookmify_fe_radio_items employe_list">';
			foreach($results as $key => $result){
				$employeeID 			= $result->empID;
				$employeeName 			= $result->firstName.' '.$result->lastName;
				$employeeServicePrice 	= $result->servicePrice;
				$employeeCapacityMin 	= $result->capacityMin;
				$employeeCapacityMax 	= $result->capacityMax;
				$employeeDeposit	 	= (int)$result->deposit;
				$employeeList .=    '<li data-employee-id="'.$result->empID.'" class="bookmify_fe_radio_item employee_item">
										<div class="radio_inner">
											
											<label>
												<span class="bookmify_be_radiobox">
													<input class="req" type="radio" name="radio" />
													<span></span>
												</span>
												<span class="label_in">
													<span class="e_name">'.$employeeName.'</span>
													<span class="e_symbol">—</span>
													<span class="s_price">'.Helper::bookmifyPriceCorrection($employeeServicePrice, 'frontend').'</span>
												</span>
											</label>
										</div>
									</li>';
				$safeEmployeesList[$key]['id'] 		= $employeeID;
				$safeEmployeesList[$key]['name'] 	= $employeeName;
				$safeEmployeesList[$key]['price'] 	= $employeeServicePrice;
				$safeEmployeesList[$key]['cap_min'] = $employeeCapacityMin;
				$safeEmployeesList[$key]['cap_max'] = $employeeCapacityMax;
				$safeEmployeesList[$key]['deposit'] = $employeeDeposit;
			}
			$employeeCount = count($results);
			$employeeList .= '</ul>';
			
			$employeeList .= '<div class="bookmify_fe_alpha_next_button disabled">
								<a href="#">'.esc_html__('Next', 'bookmify').'</a>
							  </div>';
			
			// **************************************************************************************************************
			// GET EXTRAS LIST
			// **************************************************************************************************************
			$serviceID 		= esc_sql($serviceID);
			$query 		 = "SELECT
								es.title extraTitle,
								es.id extraID,
								es.duration extraDuration,
								es.price extraPrice,
								es.capacity_max extraMax

							FROM 	   	   {$wpdb->prefix}bmify_extra_services es

							WHERE es.service_id=".$serviceID." ORDER BY position, id";
			$results 		= $wpdb->get_results( $query, OBJECT  );
			$extraList		= '';
			$footerPart		= '';
			$safeExtrasList = array();
			if(!empty($results)){
				$extraList .= 				'<div class="bookmify_fe_main_list_item extra_holder">';
				$extraList .= 					'<div class="item_header">';
				$extraList .= 						'<div class="header_wrapper">';
				$extraList .= 							'<span class="item_label">'.esc_html__('Extras:','bookmify').'</span>';
				$extraList .= 							'<span class="item_result" data-empty="empty"></span>'; // Will be added
				$extraList .= 						'</div>';
				$extraList .= 						'<span class="check_box"><span></span>'.HelperFrontend::bookmifyFeSVG('check-box').'</span>';
				$extraList .= 						$dropDownBtn;
				$extraList .= 					'</div>';
				// footer
				$extraList .= 					'<div class="item_footer">';
				$footerPart .= 						'<div class="bookmif_fe_extras_list">';
				foreach($results as $extraKey => $result){
					$extraSingleDuration 	= $result->extraDuration;
					$extraSinglePrice 		= $result->extraPrice;
					$extraSingleTitle 		= $result->extraTitle;
					$extraSingleID 			= $result->extraID;
					$extraSingleCapMax 		= $result->extraMax;
					$footerPart 		   .= 		'<div class="bookmif_fe_extras_list_item">
														<div class="extra_item_in">';
					if($extraSingleDuration == 0){
						$extraDurationContent = '('.Helper::bookmifyPriceCorrection($extraSinglePrice, 'frontend').')';
					}else{
						$extraDurationContent = '('.Helper::bookmifyNumberToDuration($extraSingleDuration).'/'.Helper::bookmifyPriceCorrection($extraSinglePrice, 'frontend').')';
					}
					$footerPart .=	 						'<div class="extra_label">
																<label>
																	<span class="bookmify_fe_checkbox">
																		<input class="req" type="checkbox" />
																		<span>'.HelperFrontend::bookmifyFeSVG('checked').'</span>
																		<span class="checkmark">'.HelperFrontend::bookmifyFeSVG('checked').'</span>
																	</span>
																	<span class="extra_title_duration">
																		<span class="extra_title">'.$extraSingleTitle.'</span>
																		<span class="extra_duration">'.$extraDurationContent.'</span>
																	</span>
																</label>
															</div>
															<div class="extra_qty">
																<div class="bookmify_fe_quantity small disabled">
																	<input class="extra_quantity" readonly disabled type="number" min="1" max="'.$extraSingleCapMax.'" value="1" />
																	<span class="increase"><span></span></span>
																	<span class="decrease"><span></span></span>
																</div>
															</div>';
					$footerPart .= 							'</div>';
					$footerPart .= 						'</div>';
					$safeExtrasList[$extraKey]['id'] 		= $extraSingleID;
					$safeExtrasList[$extraKey]['duration'] 	= $extraSingleDuration;
					$safeExtrasList[$extraKey]['title'] 	= $extraSingleTitle;
					$safeExtrasList[$extraKey]['price'] 	= $extraSinglePrice;
					$safeExtrasList[$extraKey]['max'] 		= $extraSingleCapMax;
				}
				$footerPart .= 						'</div>';
				$footerPart .= 						'<div class="bookmify_fe_alpha_next_button">
														<a href="#">'.esc_html__('Next', 'bookmify').'</a>
													</div>';
				$footerPart .= 					'</div>';
				
				$extraList	.= 					$footerPart;
				// --------------------------------------
				$extraList .= 				'</div>';
			}
			
			
			// **************************************************************************************************************
			// GET CUSTOMFIELDS LIST
			// **************************************************************************************************************
			
			$array = HelperFrontend::cfForAlphaShortcode($serviceID);
			// **************************************************************************************************************
			// GET PAYMENTS INFORMATION
			// **************************************************************************************************************
			$paymentInfo = HelperFrontend::paymentInfo($serviceID);
			
			$employeeList .= '<script class="script_employees_list" type="text/javascript">'.json_encode($safeEmployeesList).'</script>';
			$employeeList .= '<script class="script_extras_list" type="text/javascript">'.json_encode($safeExtrasList).'</script>';
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
			$employeeList 	= preg_replace($search, $replace, $employeeList);
			$extraList 		= preg_replace($search, $replace, $extraList);
			$footerPart 	= preg_replace($search, $replace, $footerPart);
			$paymentInfo 	= preg_replace($search, $replace, $paymentInfo);

			
			$buffyArray = array(
				'html'				=> $employeeList, 			// employee list
				'html2'				=> $extraList,				// extras list
				'html3'				=> $footerPart,				// footer part of extras
				'html4'				=> $array['content'],		// footer part of extras
				'html5'				=> $array['footer'],		// footer part of extras
				'html6'				=> $employeeCount,			// count of employees for selected service
				'html7'				=> get_option( 'bookmify_be_feoption_only_one_emp', 'default' ), // condition for one employee
				'html8'				=> HelperAppointments::taxOfService($serviceID),
				'html9'				=> HelperAppointments::taxOfServiceAsObject($serviceID),
				'html10'			=> $paymentInfo,
			);
			if ( true === $isAjaxCall ) {die(json_encode($buffyArray));} 
			else {return json_encode($buffyArray);}
		}
	}
	
	
	
	
	
	
	/* since bookmify v1.3.0 */
	public function ajaxQueryGetTimeSlotsOfYear(){
		global $wpdb;
		$isAjaxCall 			= false;
		
		if (1) {
			$isAjaxCall				= true;
			
			// ************************************************************************************************************************************
			// 1. Get data from jQuery
			// ************************************************************************************************************************************
			$employeeID				= strval($_POST['employeeID']); 		// ID сотрудника 8
			$serviceID 				= strval($_POST['serviceID']);			// ID сервиса 14
			$extraDuration			= strval($_POST['extraDuration']);		// длительность всех экстра сервсивов в секундах 0
			
			$peopleCount 			= strval($_POST['peopleCount']);		// люди с собой (0 потому что придет один) 0
			$bookingDuration		= strval($_POST['bookingDuration']);	// длительность всего брона в секундах нужно получить из jquery
			
			
			// ************************************************************************************************************************************
			// 2. Get Time Zone Offset
			// ************************************************************************************************************************************
			if(isset($_POST['timezoneOffset'])){$timezoneOffset = intval($_POST['timezoneOffset']);}else{$timezoneOffset = 0;}
			$timezoneOffset			= HelperAppointments::getTimeZoneOffset($timezoneOffset);
			// ************************************************************************************************************************************
			// 3. Summary duration for selected service (здесь учитывается длительность самого сервиса а также время до и после этого сервиса)
			// ************************************************************************************************************************************
			$serviceDetails			= HelperAppointments::serviceDetails($serviceID);
			$serviceBuffBefore 		= $serviceDetails[0];
			$serviceBuffAfter		= $serviceDetails[1];
			$serviceDuration		= $serviceDetails[2];
			$summaryDuration 		= ($serviceDuration+$serviceBuffBefore+$serviceBuffAfter+$extraDuration) / 60; // в минутах
			// ************************************************************************************************************************************
			// 4. Get Time interval: слот времени - по выбранному слоту (в минутах) будет добавляться время, к примеру: 8:00, 8:15, 8:30 и т.д.
			// ************************************************************************************************************************************
			$timeInterval			= HelperAppointments::timeInterval($summaryDuration);
			// ************************************************************************************************************************************
			// 5. Check for minimum time to booking
			// ************************************************************************************************************************************
			$minTimeToBooking		= get_option( 'bookmify_be_mintime_tobooking', 'disabled' );
			$maxTimeToBooking		= get_option( 'bookmify_be_maxtime_tobooking', 'disabled' );
			if($minTimeToBooking !== 'disabled'){
				$startTime			= HelperTime::getCurrentDateTimePlusWithoutFormat($minTimeToBooking*3600); // 3600: because in hours
			}else{
				$startTime			= HelperTime::getCurrentDateTimeWithoutFormat();
			}
			if($maxTimeToBooking !== 0 && $maxTimeToBooking !== 'disabled'){
				$endTime			= HelperTime::getAddedMonth($maxTimeToBooking);
			}else{
				$endTime			= HelperTime::getAddedMonth(12);
			}
			$difference				= HelperTime::dateDifference($startTime,$endTime);
			
			// ************************************************************************************************************************************
			// 6. Get all holidays of selected employee for selected period
			// ************************************************************************************************************************************
			$allHolidays			= HelperAppointments::allHolidaysEmpPeriod($employeeID,$startTime,$endTime);
			
			// ************************************************************************************************************************************
			// 7. Get all breaks of selected employee for a week
			// ************************************************************************************************************************************
			$breakArray				= HelperAppointments::breaksOfEmployee($employeeID);
			
			// ************************************************************************************************************************************
			// 8. Get working hours of selected employee for a week
			// ************************************************************************************************************************************
			$workingHours			= HelperAppointments::workingHoursForWeek($employeeID);
			if(!empty($workingHours)){
				if(array_key_exists(1,$workingHours)){
					$differenceMonday 		= HelperAppointments::timeSlotsOfDayOfTheWeek($workingHours,1,$serviceBuffBefore, $timeInterval, $summaryDuration, $breakArray);
				}
				if(array_key_exists(2,$workingHours)){
					$differenceTuesday 		= HelperAppointments::timeSlotsOfDayOfTheWeek($workingHours,2,$serviceBuffBefore, $timeInterval, $summaryDuration, $breakArray);
				}
				if(array_key_exists(3,$workingHours)){
					$differenceWednesday 	= HelperAppointments::timeSlotsOfDayOfTheWeek($workingHours,3,$serviceBuffBefore, $timeInterval, $summaryDuration, $breakArray);
				}
				if(array_key_exists(4,$workingHours)){
					$differenceThursday 	= HelperAppointments::timeSlotsOfDayOfTheWeek($workingHours,4,$serviceBuffBefore, $timeInterval, $summaryDuration, $breakArray);
				}
				if(array_key_exists(5,$workingHours)){
					$differenceFriday 		= HelperAppointments::timeSlotsOfDayOfTheWeek($workingHours,5,$serviceBuffBefore, $timeInterval, $summaryDuration, $breakArray);
				}
				if(array_key_exists(6,$workingHours)){
					$differenceSaturday 	= HelperAppointments::timeSlotsOfDayOfTheWeek($workingHours,6,$serviceBuffBefore, $timeInterval, $summaryDuration, $breakArray);
				}
				if(array_key_exists(7,$workingHours)){
					$differenceSunday 		= HelperAppointments::timeSlotsOfDayOfTheWeek($workingHours,7,$serviceBuffBefore, $timeInterval, $summaryDuration, $breakArray);
				}
			}
			
			
			// ************************************************************************************************************************************
			// get start date and index
			// ************************************************************************************************************************************
			$startDate				= $startTime->format('Y-m-d');
			$endDate				= $endTime->format('Y-m-d');
			$startDayIndex	 		= date('N', strtotime($startDate)); // 1 - Monday, 2 - Tuesday and etc
			
			// ************************************************************************************************************************************
			$helperArray			= Helper::allAppointmentsToRemove($startDate, $endDate, $employeeID, $serviceID, $peopleCount, $bookingDuration);
			$additionalSlots		= $helperArray[1];
			$helperArray			= $helperArray[0];
			$helperArray2			= Helper::allEventsToRemoveGoogleCalendar($startDate, $endDate, $employeeID, $summaryDuration);
			// ************************************************************************************************************************************
			
			// ************************************************************************************************************************************
			// MAIN LOOP to get MAIN ARRAY
			// ************************************************************************************************************************************
			$allARRAY				= array();
			for($i = 0; $i < $difference; $i++){
				$loopDate			= date('Y-m-d', strtotime($startDate . ' +'.$i.' day'));
				if(in_array($loopDate,$allHolidays)){
					
				}else{
					$y 				= $startTime->format('H')*60 + $startTime->format('i');
					if($i === 0){
						if($startDayIndex == 1){
							if(isset($differenceMonday)){
								$aaa = array_filter($differenceMonday, function ($x) use ($y) { return $x >= $y; });
								$differenceMonday2 = array_values($aaa);
							}
						}else if($startDayIndex == 2){
							if(isset($differenceTuesday)){
								$aaa = array_filter($differenceTuesday, function ($x) use ($y) { return $x >= $y; });
								$differenceTuesday2 = array_values($aaa);
							}
						}else if($startDayIndex == 3){
							if(isset($differenceWednesday)){
								$aaa = array_filter($differenceWednesday, function ($x) use ($y) { return $x >= $y; });
								$differenceWednesday2 = array_values($aaa);
							}
						}else if($startDayIndex == 4){
							if(isset($differenceThursday)){
								$aaa = array_filter($differenceThursday, function ($x) use ($y) { return $x >= $y; });
								$differenceThursday2 = array_values($aaa);
							}
						}else if($startDayIndex == 5){
							if(isset($differenceFriday)){
								$aaa = array_filter($differenceFriday, function ($x) use ($y) { return $x >= $y; });
								$differenceFriday2 = array_values($aaa);
							}
						}else if($startDayIndex == 6){
							if(isset($differenceSaturday)){
								$aaa = array_filter($differenceSaturday, function ($x) use ($y) { return $x >= $y; });
								$differenceSaturday2 = array_values($aaa);
							}
						}else if($startDayIndex == 7){
							if(isset($differenceSunday)){
								$aaa = array_filter($differenceSunday, function ($x) use ($y) { return $x >= $y; });
								$differenceSunday2 = array_values($aaa);
							}
						}
					}else{
						if(isset($differenceMonday)){
							$differenceMonday2 = $differenceMonday;
						}
						if(isset($differenceTuesday)){
							$differenceTuesday2 = $differenceTuesday;
						}
						if(isset($differenceWednesday)){
							$differenceWednesday2 = $differenceWednesday;
						}
						if(isset($differenceThursday)){
							$differenceThursday2 = $differenceThursday;
						}
						if(isset($differenceFriday)){
							$differenceFriday2 = $differenceFriday;
						}
						if(isset($differenceSaturday)){
							$differenceSaturday2 = $differenceSaturday;
						}
						if(isset($differenceSunday)){
							$differenceSunday2 = $differenceSunday;
						}
					}


					if($startDayIndex == 1){
						if(isset($differenceMonday2)){
							$allARRAY[$loopDate] = HelperAppointments::timeSlotsOfSelectedDay($differenceMonday2, $helperArray, $loopDate, $summaryDuration, $serviceBuffBefore, $helperArray2, $additionalSlots, $i, $y);
						}
					}else if($startDayIndex == 2){
						if(isset($differenceTuesday2)){
							$allARRAY[$loopDate] = HelperAppointments::timeSlotsOfSelectedDay($differenceTuesday2, $helperArray, $loopDate, $summaryDuration, $serviceBuffBefore, $helperArray2, $additionalSlots, $i, $y);
						}
					}else if($startDayIndex == 3){
						if(isset($differenceWednesday2)){
							$allARRAY[$loopDate] = HelperAppointments::timeSlotsOfSelectedDay($differenceWednesday2, $helperArray, $loopDate, $summaryDuration, $serviceBuffBefore, $helperArray2, $additionalSlots, $i, $y);
						}
					}else if($startDayIndex == 4){
						if(isset($differenceThursday2)){
							$allARRAY[$loopDate] = HelperAppointments::timeSlotsOfSelectedDay($differenceThursday2, $helperArray, $loopDate, $summaryDuration, $serviceBuffBefore, $helperArray2, $additionalSlots, $i, $y);
						}
					}else if($startDayIndex == 5){
						if(isset($differenceFriday2)){
							$allARRAY[$loopDate] = HelperAppointments::timeSlotsOfSelectedDay($differenceFriday2, $helperArray, $loopDate, $summaryDuration, $serviceBuffBefore, $helperArray2, $additionalSlots, $i, $y);
						}
					}else if($startDayIndex == 6){
						if(isset($differenceSaturday2)){
							$allARRAY[$loopDate] = HelperAppointments::timeSlotsOfSelectedDay($differenceSaturday2, $helperArray, $loopDate, $summaryDuration, $serviceBuffBefore, $helperArray2, $additionalSlots, $i, $y);
						}
					}else if($startDayIndex == 7){
						if(isset($differenceSunday2)){
							$allARRAY[$loopDate] = HelperAppointments::timeSlotsOfSelectedDay($differenceSunday2, $helperArray, $loopDate, $summaryDuration, $serviceBuffBefore, $helperArray2, $additionalSlots, $i, $y);
						}
					}
				}
				
				if(($startDayIndex+1)>7){$startDayIndex = 1;}else{$startDayIndex++;}
			}
			
			
			
			$anotherARRAY 		= array();
			$workingDays		= array();
			$availableTimeSlots = array();
			if($timezoneOffset != 0){
				foreach($allARRAY as $key => $result){
					if(!empty($result)){
						foreach($result as $res){
							$ress	= $res + 1440 - $timezoneOffset;
							if($ress >= 2880){ // если время перешло границу в большую сторону (завтра)
								$loopDate					= date('Y-m-d', strtotime($key . ' +1 day'));
								$ress						= $ress - 2880;
								$anotherARRAY[$loopDate][] 	= $ress;
							}else if($ress < 1440){ // если время перешло границу в меьншую сторону (вчерашний день)
								$loopDate					= date('Y-m-d', strtotime($key . ' -1 day'));
								$ress						= $ress;
								$anotherARRAY[$loopDate][] 	= $ress;
							}else{	// если это сегодняшний день
								$ress						= $ress - 1440;
								$anotherARRAY[$key][] 		= $ress;
							}
						}
					}
				}
				foreach($anotherARRAY as $key => $result){
					if(!empty($result)){
						$workingDays[] = $key;
					}
					foreach($result as $res){
						$resHours 		= intval($res/60);
						if($resHours < 10){$resHours = "0".$resHours;}
						$resMinutes 	= $res % 60;
						if($resMinutes < 10){$resMinutes = "0".$resMinutes;}
						$hourMinutes 	= $resHours.":".$resMinutes;
						$timeHTML 		= date_i18n(get_option('bookmify_be_time_format', 'h:i a'),strtotime($hourMinutes));
						$availableTimeSlots[$key][] = array($timeHTML,$hourMinutes);
					}
				}
			}else{
				foreach($allARRAY as $key => $result){
					if(!empty($result)){
						$workingDays[] = $key;
					}
					foreach($result as $res){
						$resHours 		= intval($res/60);
						if($resHours < 10){$resHours = "0".$resHours;}
						$resMinutes 	= $res % 60;
						if($resMinutes < 10){$resMinutes = "0".$resMinutes;}
						$hourMinutes 	= $resHours.":".$resMinutes;
						$timeHTML 		= date_i18n(get_option('bookmify_be_time_format', 'h:i a'),strtotime($hourMinutes));
						
						$availableTimeSlots[$key][] = array($timeHTML,$hourMinutes);

					}
				}
			}
			
			
			
			// Отправка обработанных данных на jQuery
			$buffyArray = array(
				'timezoneOffset'		=> $timezoneOffset,
				'availableDates'		=> $workingDays,
				'avaliableTimeSlots'	=> $availableTimeSlots,
			);
			
			if ( true === $isAjaxCall ) {die(json_encode($buffyArray));} 
			else {return json_encode($buffyArray);}
			
		}
	}
	
	
	public function ajaxCheckCustomerDetails(){
		global $wpdb;
		$isAjaxCall 				= false;
		
		if (!empty($_POST['email'])) {
			$isAjaxCall				= true;
			
			$email 					= $_POST['email'];
			$firstName 				= $_POST['firstName'];
			$lastName 				= $_POST['lastName'];
			$err 	= '';
			$email	= esc_sql($email);
			$count 	= $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->prefix}bmify_customers WHERE email='".$email."'" );
			if($count > 0){
				$email				= esc_sql($email);
				$query 				= "SELECT first_name,last_name FROM {$wpdb->prefix}bmify_customers WHERE email='".$email."'";
				$results 			= $wpdb->get_results( $query, OBJECT  );
				
				if(strtolower($results[0]->first_name) != strtolower($firstName)){
					$err 			.= 'f';
				}
				if(strtolower($results[0]->last_name) != strtolower($lastName)){
					$err 			.= 'l';
				}
				$exist				= 'exist';
			}else{
				$exist				= 'new';
			}
			switch($err){
				case 'f': 	$errorMessage = esc_html__('This email already exists with different first name!', 'bookmify'); break;
				case 'l': 	$errorMessage = esc_html__('This email already exists with different last name!', 'bookmify'); break;
				case 'fl': 	$errorMessage = esc_html__('This email already exists with different name!', 'bookmify'); break;
				default: 	$errorMessage = ''; break;
			}
			// Отправка обработанных данных на jQuery
			$buffyArray = array(
				'error' 		=> $errorMessage,
				'exist' 		=> $exist,
			);
			
			if ( true === $isAjaxCall ) {die(json_encode($buffyArray));} 
			else {return json_encode($buffyArray);}
			
		}
	}
	
	public function ajaxCreateNewAppointment(){
		global $wpdb;
		$isAjaxCall = false;
		
		if (!empty($_POST['serviceID'])) {
			$isAjaxCall			= true;
			$serID 				= esc_sql($_POST['serviceID']);
			$empID 				= esc_sql($_POST['employeeID']);
			$dateApp 			= $_POST['date']; // the date with timezoneOffset
			$timeApp 			= $_POST['time']; // the time with timezoneOffset
			$details	 		= json_decode(stripslashes($_POST['details']));
			$extras	 			= json_decode(stripslashes($_POST['extras']));
			$status 			= get_option('bookmify_be_default_app_status', 'approved');
			if($status == 'approved'){
				$status 		= 'approved';
			}else{
				$status			= 'pending';
			}
			$timeAppStart		= date("H:i:s",strtotime($timeApp));
			$dateApp 			= date("Y-m-d",strtotime($dateApp));
			$startDate			= $dateApp . " " . $timeAppStart;
			$peopleCount		= intval($_POST['peopleCount'] + 1); // мы прибавляем единицу, так как peopleCount это количество людей, которые придут вместе с клиентом, а это в свою очередь означает, что клиента мы тоже должны добавить к данному количеству
			
			/* since bookmify v1.3.0 */
			$timezoneOffset 	= 0;
			if(isset($_POST['timezoneOffset'])){
				$timezoneOffset	= $_POST['timezoneOffset'];	
			}
			if($timezoneOffset != 0){
				$startDate		= date("Y-m-d H:i:s", (strtotime($startDate) + ($timezoneOffset*60)));
				$dateApp		= date("Y-m-d",strtotime($startDate));
				$timeAppStart	= date("H:i:s",strtotime($startDate));
			}else{
				$startDate		= date("Y-m-d H:i:s", strtotime($startDate));
			}
			$startTimeBeforeChanges = $startDate;
			$startDateBeforeChanges = $dateApp;
			$endDate			= date("Y-m-d H:i:s", (strtotime($startDate) + $_POST['duration']));
			
			
			$customFields		= '';
			$cfDialog			= '';
			if(isset($_POST['customFields'])){
				$customFields	= json_decode(stripslashes($_POST['customFields']));
				$array			= array();
				foreach($customFields as $key => $customField){
					if($key == 'checkbox'){
						foreach($customField as $checkbox){
							$rCheckbox	= esc_sql($checkbox[0]);
							$query 	= "SELECT cf_label,cf_value FROM {$wpdb->prefix}bmify_customfields WHERE id=".$rCheckbox;
							$cfs	= $wpdb->get_results( $query, OBJECT  );
							$label	= $cfs[0]->cf_label;
							$vals	= array();
							$values = unserialize($cfs[0]->cf_value);
							$cfDialog .= '<strong>'.$label.'</strong><br />';
							foreach($checkbox[1] as $cc){
								$vals[] = $values[$cc]['label'];
								$cfDialog .= '-'.$values[$cc]['label'].'<br />';
							}
							$object = new \stdClass();
							$object->label = $label;
							$object->value = $vals;
							$array[] = $object;
						}
					}else if($key == 'radio'){
						foreach($customField as $radio){
							$rRadio	= esc_sql($radio[0]);
							$query 	= "SELECT cf_label,cf_value FROM {$wpdb->prefix}bmify_customfields WHERE id=".$rRadio;
							$cfs	= $wpdb->get_results( $query, OBJECT  );
							$label	= $cfs[0]->cf_label;
							$cfDialog .= '<strong>'.$label.'</strong><br />';
							$values = unserialize($cfs[0]->cf_value);
							$object = new \stdClass();
							$object->label = $label;
							$object->value = $values[$radio[1]]['label'];
							$cfDialog .= '-'.$values[$radio[1]]['label'].'<br />';
							$array[] = $object;
						}
					}else if($key == 'select'){
						foreach($customField as $select){
							$rSelect = esc_sql($select[0]);
							$query 	= "SELECT cf_label,cf_value FROM {$wpdb->prefix}bmify_customfields WHERE id=".$rSelect;
							$cfs	= $wpdb->get_results( $query, OBJECT  );
							$label	= $cfs[0]->cf_label;
							$cfDialog .= '<strong>'.$label.'</strong><br />';
							$values = unserialize($cfs[0]->cf_value);
							$object = new \stdClass();
							$object->label = $label;
							$object->value = $values[((int)$select[1])-1]['label'];
							$cfDialog .= '-'.$values[((int)$select[1])-1]['label'].'<br />';
							$array[] = $object;
						}
					}else if($key == 'text'){
						foreach($customField as $text){
							$rText	= esc_sql($text[0]);
							$query 	= "SELECT cf_label FROM {$wpdb->prefix}bmify_customfields WHERE id=".$rText;
							$cfs	= $wpdb->get_results( $query, OBJECT  );
							$label	= $cfs[0]->cf_label;
							$cfDialog .= '<strong>'.$label.'</strong><br />';
							$object = new \stdClass();
							$object->label = $label;
							$object->value = $text[1];
							$cfDialog .= '-'.$text[1].'<br />';
							$array[] = $object;
						}
					}else if($key == 'textarea'){
						foreach($customField as $textarea){
							$rTextarea	= esc_sql($textarea[0]);
							$query 	= "SELECT cf_label FROM {$wpdb->prefix}bmify_customfields WHERE id=".$rTextarea;
							$cfs	= $wpdb->get_results( $query, OBJECT  );
							$label	= $cfs[0]->cf_label;
							$cfDialog .= '<strong>'.$label.'</strong><br />';
							$object = new \stdClass();
							$object->label = $label;
							$object->value = $textarea[1];
							$cfDialog .= '-'.$textarea[1].'<br />';
							$array[] = $object;
						}
					}
				}
				$customFields 	= serialize(($array));
			}
				
			
			/*************************************************************************************************/
			$paymentStatus		= '';
			if (!empty($_POST['status'])) {
				$paymentStatus	= strtolower($_POST['status']);
			}
			$paymentType		= 'local';
			if (!empty($_POST['paymentType'])) {
				$paymentType	= $_POST['paymentType'];
			}
			$paid				= '';
			if($paymentType == 'paypal' && $paymentStatus	== 'completed'){
				$paid			= 'paid';
				if($_POST['depositSelected'] === 'yes'){
					$paid 		= 'partly';
				}
			}
			if($paymentType == 'stripe'){
				$paid			= 'paid';
				if($_POST['depositSelected'] === 'yes'){
					$paid 		= 'partly';
				}
			}
			/*************************************************************************************************/
			/* CUSTOMERS */
			/*************************************************************************************************/
			$customerExist		= $_POST['customerExist'];
			$c_fname			= $details->first_name;
			$c_lname			= $details->last_name;
			$c_email			= $details->email;
			$c_phone			= $details->phone;
			$c_message			= $details->message;
			$c_fullName			= $c_fname.' '.$c_lname;
			
			$createdDate 		= HelperTime::getCurrentDateTime();
			
			/*************************************************************************************************/
			// 1. CREATE NEW CUSTOMER IF customer email doesn't exist on customers database
			/*************************************************************************************************/
			if($customerExist == 'new'){
				// INSERT (Best Practice)
				$wpdb->query($wpdb->prepare( "INSERT INTO {$wpdb->prefix}bmify_customers ( first_name, last_name, email, phone, registration_date ) VALUES ( %s, %s, %s, %s, %s )", $c_fname, $c_lname, $c_email, $c_phone, $createdDate ));
				
				$autocreateBookmifyUser 	= get_option( 'bookmify_be_feoption_autocreate_bookmify_user', 'enabled' ); 	// since bookmify v1.1.8
				// get this customer ID (for new customer)
				$query 			= "SELECT id FROM {$wpdb->prefix}bmify_customers ORDER BY id DESC LIMIT 1;";
				$results 		= $wpdb->get_results( $query, OBJECT  );
				$customerID 	= $results[0]->id;
				if($autocreateBookmifyUser != 'disabled'){																	// since bookmify v1.1.8
					$c_wp_user_id   = $this->addWPUser($details, $customerID);
					// update customer: add wordpress user id
					$wpdb->query($wpdb->prepare( "UPDATE {$wpdb->prefix}bmify_customers SET wp_user_id=%d WHERE id=%d", $c_wp_user_id, $customerID));
				}
			}else{
				// get this customer ID (for existing customer)
				$c_email		= esc_sql($c_email);
				$query 			= "SELECT id FROM {$wpdb->prefix}bmify_customers WHERE email='".$c_email."'";
				$results 		= $wpdb->get_results( $query, OBJECT  );
				$customerID 	= $results[0]->id;
			}
			/*************************************************************************************************/
			// 2. CREATE NEW APPOINTMENT and send notification to employee
			/*************************************************************************************************/
			
			// get if this appointment is part of existing appointment: upgraded since bookmify v1.3.0
			$newID 				= '';
			$query 				= "SELECT id FROM {$wpdb->prefix}bmify_appointments WHERE service_id=".$serID." AND employee_id=".$empID." AND start_date='".$startDate."'";
			$results 			= $wpdb->get_results( $query, OBJECT  );
			foreach($results as $result){$newID = $result->id;};
			// check if this appointment is part of existing appointment
			if($newID != ''){
				$appointmentID 		= $newID;
			}else{
				
				//
				$wpdb->query($wpdb->prepare( "INSERT INTO {$wpdb->prefix}bmify_appointments ( service_id, employee_id, start_date, end_date, status ) VALUES ( %d, %d, %s, %s, %s )", $serID, $empID, $startDate, $endDate, $status ));
				
				
				// get this appoointment ID
				$query 				= "SELECT id FROM {$wpdb->prefix}bmify_appointments ORDER BY id DESC LIMIT 1;";
				$results 			= $wpdb->get_results( $query, OBJECT  );
				$appointmentID 		= $results[0]->id;
			}
			

			
			
			
			
			/*************************************************************************************************/
			// 3. CREATE NEW CUSTOMER APPOINTMENT
			
			// get service price
			$query 				= "SELECT price FROM {$wpdb->prefix}bmify_employee_services WHERE service_id=".$serID." AND employee_id=".$empID;
			$results 			= $wpdb->get_results( $query, OBJECT  );
			$serPrice 			= $results[0]->price;
			
			// create new customer appointment
			$wpdb->query($wpdb->prepare( "INSERT INTO {$wpdb->prefix}bmify_customer_appointments ( customer_id, appointment_id, number_of_people, price, status, created_date, cf ) VALUES ( %d, %d, %d, %f, %s, %s, %s )", $customerID, $appointmentID, $peopleCount, $serPrice, $status, $createdDate, $customFields ));
			
			
			// get new customer appointment id
			$query 				= "SELECT id FROM {$wpdb->prefix}bmify_customer_appointments ORDER BY id DESC LIMIT 1;";
			$results 			= $wpdb->get_results( $query, OBJECT  );
			$custAppID 			= $results[0]->id;
			
			$taxCustomer		= HelperAppointments::taxOfService($serID);
			// payment price: service price * people count
			$paymentPrice		= ($serPrice * $peopleCount);
			$taxService			= floor($peopleCount * $serPrice * $taxCustomer)/100;
			$taxExtra			= 0;
			
			// create new customer appoinment extras if exist
			if($_POST['haveExtras'] == 'yes'){
				foreach($extras as $extra){
					$extraID	= $extra[0];
					$quantity	= $extra[1];
					// get extra price
					$extraID	= esc_sql($extraID);
					$query 		= "SELECT price FROM {$wpdb->prefix}bmify_extra_services WHERE id=".$extraID;
					$results 	= $wpdb->get_results( $query, OBJECT  );
					$price 		= $results[0]->price;
					
					// create new extras
					$wpdb->query($wpdb->prepare( "INSERT INTO {$wpdb->prefix}bmify_customer_appointments_extras ( customer_appointment_id, extra_id, quantity, price ) VALUES ( %d, %d, %d, %f )", $custAppID, $extraID, $quantity, $price ));
					
					// add to payment price all extras price 
					$paymentPrice 	+= ($quantity * $price * $peopleCount);
					$taxExtra		+= floor($quantity * $price * $peopleCount * $taxCustomer)/100;
				}
			}
			
			$paymentPrice	= $paymentPrice + $taxService + $taxExtra;
			$taxIDsObject	= HelperAppointments::taxIDsObjectCreatorForPayment($serID);
			// insert new payment
			if($paid == 'paid'){
				$wpdb->query($wpdb->prepare( "INSERT INTO {$wpdb->prefix}bmify_payments ( total_price, created_date, paid_type, paid, status, tax_ids ) VALUES ( %f, %s, %s, %f, %s, %s )", $paymentPrice, $createdDate, $paymentType, $paymentPrice, 'full', $taxIDsObject ));
			}else if($paid == 'partly'){
				$paidPrice	= (float)$_POST['deposit'];
				$wpdb->query($wpdb->prepare( "INSERT INTO {$wpdb->prefix}bmify_payments ( total_price, created_date, paid_type, paid, status, tax_ids ) VALUES ( %f, %s, %s, %f, %s, %s )", $paymentPrice, $createdDate, $paymentType, $paidPrice, 'partly', $taxIDsObject ));
			}else if($paid == ''){
				$wpdb->query($wpdb->prepare( "INSERT INTO {$wpdb->prefix}bmify_payments ( total_price, created_date, tax_ids ) VALUES ( %f, %s, %s )", $paymentPrice, $createdDate, $taxIDsObject ));
			}
				

			// get this payment ID
			$query 				= "SELECT id FROM {$wpdb->prefix}bmify_payments ORDER BY id DESC LIMIT 1;";
			$results 			= $wpdb->get_results( $query, OBJECT  );
			$paymentID 			= $results[0]->id;

			// insert paymentID to last customer appointment 
			$wpdb->query($wpdb->prepare( "UPDATE {$wpdb->prefix}bmify_customer_appointments SET payment_id=%d WHERE id=%d", $paymentID, $custAppID));
			
			// get customer information to send a notification to employee
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
			$infoObject->customer_count	 	= 1;
			$infoObject->customer_name	 	= $c_fullName;
			$infoObject->customer_email	 	= $c_email;
			$infoObject->customer_phone	 	= $c_phone;
			$infoObject->cf	 				= $cfDialog;
			$this->pretraintmentToSendNotification($infoObject); // send notification to new employee

			// notification	|| send notification to customer
			$infoObject						= new \stdClass();
			$infoObject->sendTo				= 'customer';
			$infoObject->appID				= $appointmentID;
			$infoObject->userID				= $customerID;
			$infoObject->service_name 		= $serviceName;
			$infoObject->appointment_date 	= $dateApp;
			$infoObject->appointment_time 	= $timeAppStart;
			$infoObject->status			 	= $status;
			$infoObject->customer_name	 	= $c_fullName;
			$infoObject->customer_email	 	= $c_email;
			$infoObject->customer_phone	 	= $c_phone;
			$infoObject->cf	 				= $cfDialog;
			$infoObject->tz	 				= $timezoneOffset;
			$infoObject->tzTime				= $startTimeBeforeChanges; 
			$infoObject->tzDate				= $startDateBeforeChanges;
			$this->pretraintmentToSendNotification($infoObject); // send notification to new customer
			
			// create google calendar event
			$googleCal 						= new GoogleCalendarProject();
			$googleCalEventID 				= $googleCal->insertEvent($appointmentID);

			//update appointment google calendar event id 
			$wpdb->query($wpdb->prepare( "UPDATE {$wpdb->prefix}bmify_appointments SET google_calendar_event_id=%s WHERE id=%d", $googleCalEventID, $appointmentID));
			
			
			$paymentPrice = 0;
		}
		

		$buffyArray = array(
			'startDate'					=> $startDate,
			'endDate'					=> $endDate,
			'timezoneOffset' 			=> $timezoneOffset,
			'newID' 					=> $newID,
		);

		if ( true === $isAjaxCall ){die(json_encode($buffyArray));} 
		else{return json_encode($buffyArray);}
	}
	
	// подготовка к отправке уведомлении
	private function pretraintmentToSendNotification($object){
		$receiver 		= $object->sendTo;
		$checkSender 	= Helper::checkForSender();
		
		if($checkSender){
			if($receiver == 'employee'){
				NotificationManagement::sendInfoToEmployeeAboutAppointment( $object );
			}else if($receiver == 'customer'){
				NotificationManagement::sendInfoToCustomerAboutAppointment( $object );
			}
		}
    }
	
	private function addWPUser($customer, $customerID)
    {
		
        $username 	= $customer->email;
        $password 	= wp_generate_password( 12, true );
        $userid 	= wp_create_user( $username, $password, $customer->email );
        if ( ! $userid instanceof \WP_Error ) {
            $user 	= new \WP_User( $userid );
            $user->set_role( 'bookmify-customer' );
			
            // Send email notification.
            NotificationManagement::sendNewUserCredentials( $customer, $username, $password, $customerID );

            return (int)$userid;
        }

        return false;
    }
	
	public static function ajaxCheckLoginCredentials(){
		global $wpdb;
		$isAjaxCall 			= true;
		$username 				= '';
		$password 				= '';
		$result					= 'no';
		$exist					= 'new';
		
		$firstName 				= '';
		$lastName 				= '';
		$email					= '';
		$ID						= '';
		
		// get login from JS
		if(isset($_POST['login'])){
			$username 			= $_POST['login'];	
		}
		// get password from JS
		if(isset($_POST['password'])){
			$password 			= $_POST['password'];	
		}
		
		// get results
		$buffy					= '';
		if($username != '' && $password != ''){
			$user 				= get_user_by( 'login', $username );
			if ( $user && wp_check_password( $password, $user->data->user_pass, $user->ID) ){
				$query 			= "SELECT id,first_name,last_name,email FROM {$wpdb->prefix}bmify_customers WHERE wp_user_id=".$user->ID;
				$results		= $wpdb->get_results( $query, OBJECT  );
				if(!empty($results)){
					$firstName 	= $results[0]->first_name;
					$lastName 	= $results[0]->last_name;
					$email		= $results[0]->email;
					$ID			= $results[0]->id;
					$result		= 'done';
					$exist		= 'exist';
				}
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
		$buffy = preg_replace($search, $replace, $buffy);

		$buffyArray = array(
			'result' 			=> $result,
			'id' 				=> $ID,
			'firstName'			=> $firstName,
			'lastName'			=> $lastName,
			'email'				=> $email,
			'exist' 			=> $exist,
		);

		if ( true === $isAjaxCall ){die(json_encode($buffyArray));} 
		else{return json_encode($buffyArray);}
	}
	
	
	
}

new BookmifyAlpha;
