<?php
namespace Bookmify;

use Bookmify\Helper;
use Bookmify\HelperTime;
use Bookmify\HelperAdmin;
use Bookmify\HelperPayments;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


class Payments{

	const PAGE_ID 		= 'bookmify_payments';
	
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
		add_action( 'wp_ajax_ajaxQueryOpenPaymentDetails', [$this, 'ajaxQueryOpenPaymentDetails'] );
		add_action( 'wp_ajax_ajaxFilterPaymentList', [$this, 'ajaxFilterPaymentList'] );
		add_action( 'wp_ajax_ajaxQueryUpdatePaymentPaid', [$this, 'ajaxQueryUpdatePaymentPaid'] );
	}
	
	
	/**
	 * @since 1.0.0
	 * @access public
	*/
	public function assignValToVar(){
		$this->per_page 	= get_option('bookmify_be_payments_pp', 10);
		$this->daterange 	= get_option('bookmify_be_payments_daterange', 30) - 1;
		$this->dateformat 	= get_option('bookmify_be_date_format', 'd F, Y');
	}
	

	/**
	 * @since 1.0.0
	 * @access public
	*/
	public function register_admin_menu() {

		add_submenu_page(
			BOOKMIFY_MENU,
			esc_html__( 'Payments', 'bookmify' ),
			esc_html__( 'Payments', 'bookmify' ),
			'bookmify_be_read_payments',
			self::PAGE_ID,
			[ $this, 'display_payments_page' ]
		);
	}
	
	/**
	 * @since 1.0.0
	 * @access public
	*/
	public function display_payments_page() {
		global $wpdb;

		echo HelperAdmin::bookmifyAdminContentStart();
		?>
		
		<div class="bookmify_be_content_wrap bookmify_be_payments_page">
			<div class="bookmify_be_page_title">
				<h3><?php echo esc_html($this->get_page_title()); ?><span class="count"><?php echo Helper::bookmifyItemsCount('payments'); ?></span></h3>
			</div>
			<div class="bookmify_be_page_content">
				
				<div class="bookmify_be_payments">
					
					<div class="bookmify_be_filter_wrap">
						<div class="bookmify_be_filter">
							<div class="bookmify_be_row">
								
								<div class="bookmify_be_filter_list daterange">
									<div class="bookmify_be_filter_list_in">
										<div class="input_wrapper">
											<input type="text" placeholder="<?php esc_html_e('Date', 'bookmify');?>" class="filter_date" autocomplete=off />
										</div>
									</div>
								</div>
								
								<div class="bookmify_be_filter_list services">
									<div class="bookmify_be_filter_list_in">
										<div class="input_wrapper">
											<input readonly data-placeholder="<?php esc_attr_e('Services', 'bookmify');?>" type="text" placeholder="<?php esc_attr_e('Services', 'bookmify');?>" class="filter_list" autocomplete=off />
											<span class="icon">
												<img class="bookmify_be_svg" src="<?php echo BOOKMIFY_ASSETS_URL.'img/down.svg';?>" alt="" />
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

										<?php echo HelperPayments::servicesAsFilter();?>
									
									</div>
								</div>
								
								<div class="bookmify_be_filter_list customers">
									<div class="bookmify_be_filter_list_in">
										<div class="input_wrapper">
											<input data-placeholder="<?php esc_attr_e('Customers', 'bookmify');?>" type="text" placeholder="<?php esc_attr_e('Customers', 'bookmify');?>" class="filter_list" autocomplete=off />
											<span class="icon">
												<img class="bookmify_be_svg" src="<?php echo BOOKMIFY_ASSETS_URL.'img/down.svg';?>" alt="" />
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

										<?php echo HelperPayments::customersAsFilter();?>

									</div>
								</div>
								
								<div class="bookmify_be_filter_list employees">
									<div class="bookmify_be_filter_list_in">
										<div class="input_wrapper">
											<input data-placeholder="<?php esc_attr_e('Employees', 'bookmify');?>" type="text" placeholder="<?php esc_attr_e('Employees', 'bookmify');?>" class="filter_list" autocomplete=off />
											<span class="icon">
												<img class="bookmify_be_svg" src="<?php echo BOOKMIFY_ASSETS_URL.'img/down.svg';?>" alt="" />
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

										<?php echo HelperPayments::employeesAsFilter();?>
									
									</div>
								</div>
								
								<div class="bookmify_be_filter_list status">
									<div class="bookmify_be_filter_list_in">
										<div class="input_wrapper">
											<input data-placeholder="<?php esc_attr_e('Status', 'bookmify');?>" type="text" placeholder="<?php esc_attr_e('Status', 'bookmify');?>" class="filter_list" autocomplete=off />
											<span class="icon">
												<img class="bookmify_be_svg" src="<?php echo BOOKMIFY_ASSETS_URL.'img/down.svg';?>" alt="" />
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

										<?php echo HelperPayments::statusAsFilter();?>
									
									</div>
								</div>
								
							</div>
								
						</div>
					</div>
					
					
					<!-- Payment Header -->
					<div class="bookmify_be_payments_header">
						<div class="bookmify_be_payments_header_in">
							<span class="list_date"><?php esc_html_e('Date', 'bookmify');?></span>
							<span class="list_customer"><?php esc_html_e('Customer', 'bookmify');?></span>
							<span class="list_service"><?php esc_html_e('Service', 'bookmify');?></span>
							<span class="list_paid"><?php esc_html_e('Paid', 'bookmify');?></span>
							<span class="list_total"><?php esc_html_e('Total', 'bookmify');?></span>
							<span class="list_phone"><?php esc_html_e('Status', 'bookmify');?></span>
						</div>
					</div>
					<!-- /Payment Header -->
					
					<!-- Payment List -->
					<div class="bookmify_be_payments_list">
						<div class="bookmify_be_payment_list_content">
							<?php echo $this->paymentsList(); ?>
						</div>
					</div>
					<!-- /Payment List -->
					
				</div>
				
			</div>
			
		</div>
		<?php echo HelperAdmin::bookmifyAdminContentEnd();
		
	}
	

	
	
	/*
	 * @since 1.0.0
	 * @access public
	*/
	public function paymentsList(){
		global $wpdb;
		
		$startDate  = date('Y-m-d', strtotime('-'.$this->daterange.' days')).' 00:00:00';
		$endDate 	= date('Y-m-d').' 23:59:59';
		
		$startDate	= esc_sql($startDate);
		$endDate	= esc_sql($endDate);
		$query 		 = "SELECT
							p.id paymentID,
							p.created_date paymentCreatedDate,
							p.paid paymentPaidSum,
							p.total_price paymentTotalPrice,
							p.status paymentStatus,
							c.first_name customerFirstName, 
							c.last_name customerLastName, 
							s.title serviceTitle,
							a.end_date endDate

						FROM 	   	   {$wpdb->prefix}bmify_payments p 
							INNER JOIN {$wpdb->prefix}bmify_customer_appointments ca 	ON ca.payment_id = p.id
							INNER JOIN {$wpdb->prefix}bmify_customers c 				ON ca.customer_id = c.id 
							INNER JOIN {$wpdb->prefix}bmify_appointments a 				ON ca.appointment_id = a.id 
							INNER JOIN {$wpdb->prefix}bmify_employees e 				ON a.employee_id = e.id 
							INNER JOIN {$wpdb->prefix}bmify_services s 					ON a.service_id = s.id

						WHERE (p.created_date BETWEEN '".$startDate."' AND '".$endDate."')";
		
		$Querify  		= new Querify( $query, 'payment' );
		$payments      	= $Querify->getData( $this->per_page ); // payments per page
		

		$html = '<div class="bookmify_be_list payment_list">';
		if(count( $payments->data ) == 0){
			$html .= Helper::bookmifyBeNoItems();
		}
		for($i = 0; $i < count( $payments->data ); $i++){
			
			$createdDay 	= date_i18n($this->dateformat, strtotime($payments->data[$i]->paymentCreatedDate));
			
			switch($payments->data[$i]->paymentStatus){
				case 'full': 	$statusShort = 'full'; $status = esc_html__('Completed', 'bookmify'); $icon = 'checked'; break;
				case 'not': 	
				default: 		$statusShort = 'not'; $status = esc_html__('Pending', 'bookmify'); $icon = 'circle'; break;
			}
			$statusIcon 		= '<span class="icon"><img class="bookmify_be_svg" src="'.BOOKMIFY_ASSETS_URL.'img/'.$icon.'.svg" alt="" /></span>';
			$statusText 		= '<span class="'.$statusShort.'">'.$statusIcon.'<span class="text">'.$status.'</span></span>';
			
			$appointmentEndDate = date("Y-m-d H:i:s", strtotime($payments->data[$i]->endDate));
			$today				= HelperTime::getCurrentDateTime();
			if($appointmentEndDate <= $today){
				$appDateStatus	= 'app_closed';
			}else{
				$appDateStatus 	= 'app_open';
			}
			

			$html .= '<div class="bookmify_be_list_item payment_item" data-entity-id="'.$payments->data[$i]->paymentID.'">
						<div class="bookmify_be_list_item_in">
							<div class="bookmify_be_list_item_header">
								<div class="header_in">
									<div class="info_holder">
										<div class="info_in">
											<span class="payment_date"><span>'.$createdDay.'</span></span>
											<span class="payment_customer">
												<span class="p_service">'.$payments->data[$i]->serviceTitle.'</span>
												<span>'.$payments->data[$i]->customerFirstName.' '.$payments->data[$i]->customerLastName.'</span>
											</span>
											<span class="payment_service"><span>'.$payments->data[$i]->serviceTitle.'</span></span>
											<span class="payment_paid">
												<span class="p_paid">'.Helper::bookmifyPriceCorrection($payments->data[$i]->paymentPaidSum).'</span>
												<form autocomplete="off">
													<input type="number" name="payment_paid" value="'.$payments->data[$i]->paymentPaidSum.'" />
													<input type="hidden" name="payment_id" value="'.$payments->data[$i]->paymentID.'" />
													<input type="hidden" name="payment_paid_old" value="'.$payments->data[$i]->paymentPaidSum.'" />
												</form>
												<span class="p_bottom">
													<span class="p_paid">'.Helper::bookmifyPriceCorrection($payments->data[$i]->paymentPaidSum).'</span> / 
													<span class="p_total">'.Helper::bookmifyPriceCorrection($payments->data[$i]->paymentTotalPrice).'</span>
												</span>
											</span>
											<span class="payment_price"><span>'.Helper::bookmifyPriceCorrection($payments->data[$i]->paymentTotalPrice).'</span></span>
											<span class="payment_status">'.$statusText.'</span>
										</div>
									</div>

									<div class="buttons_holder">
										<div class="btn_item btn_more">
											<a href="#" class="bookmify_be_more"  data-entity-id="'.$payments->data[$i]->paymentID.'">
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
										<div class="btn_item btn_edit">
											<a href="#" class="bookmify_be_edit"  data-entity-id="'.$payments->data[$i]->paymentID.'">
												<img class="bookmify_be_svg edit" src="'.BOOKMIFY_ASSETS_URL.'img/edit.svg" alt="" /><img class="bookmify_be_svg checked" src="'.BOOKMIFY_ASSETS_URL.'img/checked.svg" alt="" />
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

						</div>
					</div>';
			
		}
		
		$html .= '</div>';
		
		$html .= $Querify->getPagination( 1, 'bookmify_be_pagination payment');

		return $html;
	}
	
	public function ajaxQueryUpdatePaymentPaid(){
		global $wpdb;
		$html 					= '';
		$params 				= array();
		
		$isAjaxCall 			= false;
		if (!empty($_POST['bookmify_data'])) {
			$isAjaxCall 		= true;
			parse_str($_POST['bookmify_data'], $params);
			$paymentPaid 		= $params['payment_paid'];
			$paymentID	 		= $params['payment_id'];
			
			$paymentID			= esc_sql($paymentID);
			$query 				= "SELECT total_price FROM {$wpdb->prefix}bmify_payments WHERE id=".$paymentID;
			$results 			= $wpdb->get_results( $query, OBJECT  );
			$totalPrice 		= $results[0]->total_price;
			
			if($paymentPaid >= $totalPrice){
				$paymentPaid 	= $totalPrice;
				$status			= 'full';
				$statusShort	= 'full';
			}else if($paymentPaid < $totalPrice){
				$status			= 'partly';
				$statusShort	= 'not';
			}
			
			if($paymentPaid == '' || $paymentPaid == 0){
				$paymentPaid	= 0;
				$status			= 'not';
				$statusShort	= 'not';
			}
			
			$wpdb->query($wpdb->prepare( "UPDATE {$wpdb->prefix}bmify_payments SET paid=%f, status=%s WHERE id=%d", $paymentPaid, $status, $paymentID));
			
			
			switch($status){
				case 'full': 	$statusText = esc_html__('Completed', 'bookmify'); $icon = 'checked'; break;
				case 'not': 	
				case 'partly': 	
				default: 		$statusText = esc_html__('Pending', 'bookmify'); $icon = 'circle'; break;
			}
			$statusIcon 		= '<span class="icon"><img class="bookmify_be_svg" src="'.BOOKMIFY_ASSETS_URL.'img/'.$icon.'.svg" alt="" /></span>';
			$statusText 		= '<span class="'.$statusShort.'">'.$statusIcon.'<span class="text">'.$statusText.'</span></span>';
			
			

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
			$html = preg_replace($search, $replace, $html);


			$buffyArray = array(
				'status'			=> $statusText,
				'correct_price'		=> Helper::bookmifyPriceCorrection($paymentPaid),
				'price'				=> $paymentPaid,
			);
			if ( true === $isAjaxCall ) {die(json_encode($buffyArray));} 
			else {return json_encode($buffyArray);}
		}
	}
	
	
	/**
	 * @since 1.0.0
	 * @access public
	*/
	public function ajaxFilterPaymentList(){
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
			if(!empty($_POST['bookmify_services'])){$filterByService = $_POST['bookmify_services'];}
			$filterByCustomer	= $_POST['bookmify_customer'];
			$filterByEmployee	= $_POST['bookmify_employee'];
			$filterByStatus		= $_POST['bookmify_status'];
			$filterDateRange	= $_POST['bookmify_daterange'];
			
			
			
			
			$query 		 = "SELECT
								p.id paymentID,
								p.created_date paymentCreatedDate,
								p.paid paymentPaidSum,
								p.total_price paymentTotalPrice,
								p.status paymentStatus,
								c.first_name customerFirstName, 
								c.last_name customerLastName, 
								s.title serviceTitle
								
							FROM 	   	   {$wpdb->prefix}bmify_payments p 
								INNER JOIN {$wpdb->prefix}bmify_customer_appointments ca 	ON ca.payment_id = p.id
								INNER JOIN {$wpdb->prefix}bmify_customers c 				ON ca.customer_id = c.id 
								INNER JOIN {$wpdb->prefix}bmify_appointments a 				ON ca.appointment_id = a.id 
								INNER JOIN {$wpdb->prefix}bmify_employees e 				ON a.employee_id = e.id 
								INNER JOIN {$wpdb->prefix}bmify_services s 					ON a.service_id = s.id
									  
							WHERE";
			
			
			if(!empty($filterByService)){
				$filterByService = esc_sql($filterByService);
				$query .= " s.id IN (" . implode(",", array_map("intval", $filterByService)) . ") AND";
			}
			if($filterByCustomer != ''){
				$filterByCustomer = esc_sql($filterByCustomer);
				$query .= " c.id = '".$filterByCustomer."' AND";
			}
			if($filterByEmployee != ''){
				$filterByEmployee = esc_sql($filterByEmployee);
				$query .= " e.id = '".$filterByEmployee."' AND";
			}
			if($filterByStatus != ''){
				$filterByStatus = esc_sql($filterByStatus);
				$query .= " p.status = '".$filterByStatus."' AND";
			}
			if(!empty($filterDateRange)){
				$filterDateRange = esc_sql($filterDateRange);
				$query .= " (p.created_date BETWEEN '".$filterDateRange[0]."' AND '".$filterDateRange[1]."') AND";
			}
			
			
			
			
			$query = rtrim($query, 'AND');
			$query = rtrim($query, 'WHERE');
			
			
			
			$Querify  		= new Querify( $query, 'payment' );
			$payments		= $Querify->getData( $this->per_page, $page, $filter );


			$html = '<div class="bookmify_be_list payment_list">';
			if(count( $payments->data ) == 0){
				$html .= Helper::bookmifyBeNoItems();
			}
			for($i = 0; $i < count( $payments->data ); $i++){
				
				
				$createdDay 	= date_i18n($this->dateformat, strtotime($payments->data[$i]->paymentCreatedDate));
			
				switch($payments->data[$i]->paymentStatus){
					case 'full': 	$statusShort = 'full'; $status = esc_html__('Completed', 'bookmify'); $icon = 'checked'; break;
					case 'not': 	
					default: 		$statusShort = 'not'; $status = esc_html__('Pending', 'bookmify'); $icon = 'circle'; break;
				}
				$statusIcon 	= '<span class="icon"><img class="bookmify_be_svg" src="'.BOOKMIFY_ASSETS_URL.'img/'.$icon.'.svg" alt="" /></span>';
				$statusText 	= '<span class="'.$statusShort.'">'.$statusIcon.$status.'</span>';

				$html .= '<div class="bookmify_be_list_item payment_item bookmify_be_animated" data-entity-id="'.$payments->data[$i]->paymentID.'">
						<div class="bookmify_be_list_item_in">
							<div class="bookmify_be_list_item_header">
								<div class="header_in">
									<div class="info_holder">
										<div class="info_in">
											<span class="payment_date"><span>'.$createdDay.'</span></span>
											<span class="payment_customer">
												<span class="p_service">'.$payments->data[$i]->serviceTitle.'</span>
												<span>'.$payments->data[$i]->customerFirstName.' '.$payments->data[$i]->customerLastName.'</span>
											</span>
											<span class="payment_service"><span>'.$payments->data[$i]->serviceTitle.'</span></span>
											<span class="payment_paid">
												<span class="p_paid">'.Helper::bookmifyPriceCorrection($payments->data[$i]->paymentPaidSum).'</span>
												<form autocomplete="off">
													<input type="number" name="payment_paid" value="'.$payments->data[$i]->paymentPaidSum.'" />
													<input type="hidden" name="payment_id" value="'.$payments->data[$i]->paymentID.'" />
													<input type="hidden" name="payment_paid_old" value="'.$payments->data[$i]->paymentPaidSum.'" />
												</form>
												<span class="p_bottom">
													<span class="p_paid">'.Helper::bookmifyPriceCorrection($payments->data[$i]->paymentPaidSum).'</span> / 
													<span class="p_total">'.Helper::bookmifyPriceCorrection($payments->data[$i]->paymentTotalPrice).'</span>
												</span>
											</span>
											<span class="payment_price"><span>'.Helper::bookmifyPriceCorrection($payments->data[$i]->paymentTotalPrice).'</span></span>
											<span class="payment_status">'.$statusText.'</span>
										</div>
									</div>

									<div class="buttons_holder">
										<div class="btn_item btn_more">
											<a href="#" class="bookmify_be_more"  data-entity-id="'.$payments->data[$i]->paymentID.'">
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
										<div class="btn_item btn_edit">
											<a href="#" class="bookmify_be_edit"  data-entity-id="'.$payments->data[$i]->paymentID.'">
												<img class="bookmify_be_svg edit" src="'.BOOKMIFY_ASSETS_URL.'img/edit.svg" alt="" /><img class="bookmify_be_svg checked" src="'.BOOKMIFY_ASSETS_URL.'img/checked.svg" alt="" />
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

						</div>
					</div>';
			}
			$html .= '</div>';
			$html .= $Querify->getPagination( 1, 'bookmify_be_pagination payment');
			
			
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
	
	
	/**
	 * @since 1.0.0
	 * @access public
	*/
	public function ajaxQueryOpenPaymentDetails(){
		global $wpdb;
		$isAjaxCall = false;
		$html = '';
		
		if (!empty($_POST['bookmify_data'])) {
			$isAjaxCall = true;
			$id = $_POST['bookmify_data'];

			// SELECT
			$id = esc_sql($id);
			$query 		 = "SELECT
								p.id paymentID,
								p.created_date paymentCreatedDate,
								p.paid paymentPaidSum,
								p.total_price paymentTotalPrice,
								p.status paymentStatus,
								p.paid_type paymentGateway,
								c.first_name customerFirstName, 
								c.last_name customerLastName, 
								c.email customerEmail, 
								c.phone customerPhone,
								a.start_date appDate,
								s.title serviceTitle,
								e.first_name employeeFirstName, 
								e.last_name employeeLastName,
								ca.number_of_people numberOfPeople,
								ca.price customerAppPrice,
								a.id appointmentID,
								c.id customerID,
								GROUP_CONCAT(cae.quantity ORDER BY cae.id) customerAppExtraQuantities,
								GROUP_CONCAT(cae.price ORDER BY cae.id) customerAppExtraPrices
								
							FROM 	   	   {$wpdb->prefix}bmify_payments p 
								INNER JOIN {$wpdb->prefix}bmify_customer_appointments ca 			ON ca.payment_id = p.id
								LEFT JOIN {$wpdb->prefix}bmify_customer_appointments_extras cae 	ON cae.customer_appointment_id = ca.id
								INNER JOIN {$wpdb->prefix}bmify_customers c 						ON ca.customer_id = c.id 
								INNER JOIN {$wpdb->prefix}bmify_appointments a 						ON ca.appointment_id = a.id 
								INNER JOIN {$wpdb->prefix}bmify_employees e 						ON a.employee_id = e.id 
								INNER JOIN {$wpdb->prefix}bmify_services s 							ON a.service_id = s.id
									  
							WHERE p.id=".$id."";
			
			$payments 	= $wpdb->get_results( $query, OBJECT  );
			
			
			foreach($payments as $payment){
				// ------------------------------------------------------------------------------------------------
				$pAppDate			= $payment->appDate;
				$pCreatedDate		= $payment->paymentCreatedDate;
				$pTotalPrice		= $payment->paymentTotalPrice;
				$pPaidSum			= $payment->paymentPaidSum;
				$pID				= $payment->paymentID;
				$pCustomerFirstName	= $payment->customerFirstName;
				$pCustomerLastName	= $payment->customerLastName;
				$pCustomerEmail		= $payment->customerEmail;
				$pCustomerPhone		= $payment->customerPhone;
				$pEmployeeFirstName	= $payment->employeeFirstName;
				$pEmployeeLastName	= $payment->employeeLastName;
				$pServiceTitle		= $payment->serviceTitle;
				// ------------------------------------------------------------------------------------------------
				$appDate		= date_i18n($this->dateformat, strtotime($pAppDate));
				// ------------------------------------------------------------------------------------------------
				$createdDay 	= date_i18n($this->dateformat, strtotime($pCreatedDate));
				// ------------------------------------------------------------------------------------------------
				$numberofpeople	= $payment->numberOfPeople;
				$priceService	= $payment->customerAppPrice;
				$priceService	= $priceService * $numberofpeople;
				// ------------------------------------------------------------------------------------------------
				$quantityExtras	= $payment->customerAppExtraQuantities; // result: comma separated string
				$quantityExtras = explode(',', $quantityExtras); // creating array from string
				$priceExtrasIn	= $payment->customerAppExtraPrices; // result: comma separated string
				$priceExtrasIn 	= explode(',', $priceExtrasIn); // creating array from string
				$priceExtras	= '';
				for($i=0; $i<count($quantityExtras); $i++){
					$priceExtras += $quantityExtras[$i] * $priceExtrasIn[$i] * $numberofpeople;
				}
				// ------------------------------------------------------------------------------------------------
				$paymentDue		= $pTotalPrice - $pPaidSum >= 0 ? $pTotalPrice - $pPaidSum : 0;
				// ------------------------------------------------------------------------------------------------
				switch($payment->paymentStatus){
					case 'full': 	$paymentStatus = esc_html__('Completed', 'bookmify'); break;
					case 'not': 	
					default: 		$paymentStatus = esc_html__('Pending', 'bookmify'); break;
				}
				// ------------------------------------------------------------------------------------------------
				switch($payment->paymentGateway){
					case 'paypal': 	$paymentGateway = esc_html__('Paypal', 'bookmify'); break;
					case 'stripe': 	$paymentGateway = esc_html__('Stripe', 'bookmify'); break;
					case 'local': 	
					default: 		$paymentGateway = esc_html__('Local', 'bookmify'); break;
				}
				// ------------------------------------------------------------------------------------------------
				$taxCustomer	= HelperAppointments::taxOfCustomer($payment->appointmentID,$payment->customerID);
				// ------------------------------------------------------------------------------------------------
				
				
				$html .= '<div class="bookmify_be_popup_form_wrap" data-entity-id="'.$pID.'">
							<div class="bookmify_be_popup_form_position_fixer">
								<div class="bookmify_be_popup_form_bg">
									<div class="bookmify_be_popup_form">

										<div class="bookmify_be_popup_form_header">
											<h3>'.esc_html__('Payment Details','bookmify').'</h3>
											<span class="closer"></span>
										</div>

										<div class="bookmify_be_popup_form_content">
											<div class="bookmify_be_popup_form_content_in">
											
												<div class="detail_box">
													<div class="detail_box_header"><h4>'.esc_html__('Customer', 'bookmify').'</h4></div>
													<div class="detail_box_content">
														<div class="detail_box_row">
															<div class="detail_box_col col_left"><span>'.esc_html__('Name:', 'bookmify').'</span></div>
															<div class="detail_box_col col_right"><span>'.$pCustomerFirstName.' '.$pCustomerLastName.'</span></div>
														</div>
														<div class="detail_box_row">
															<div class="detail_box_col col_left"><span>'.esc_html__('Email:', 'bookmify').'</span></div>
															<div class="detail_box_col col_right"><span>'.$pCustomerEmail.'</span></div>
														</div>';
											
										if($pCustomerPhone != ''){
											$html .= '	<div class="detail_box_row">
															<div class="detail_box_col col_left"><span>'.esc_html__('Phone:', 'bookmify').'</span></div>
															<div class="detail_box_col col_right"><span>'.$pCustomerPhone.'</span></div>
														</div>';
										}
					
										$html .= '	</div>
												</div>
												
												<div class="detail_box">
													<div class="detail_box_header"><h4>'.esc_html__('Appointment', 'bookmify').'</h4></div>
													<div class="detail_box_content">
														<div class="detail_box_row">
															<div class="detail_box_col col_left"><span>'.esc_html__('Date:', 'bookmify').'</span></div>
															<div class="detail_box_col col_right"><span>'.$appDate.'</span></div>
														</div>
														<div class="detail_box_row">
															<div class="detail_box_col col_left"><span>'.esc_html__('Service:', 'bookmify').'</span></div>
															<div class="detail_box_col col_right"><span>'.$pServiceTitle.'</span></div>
														</div>
														<div class="detail_box_row">
															<div class="detail_box_col col_left"><span>'.esc_html__('Employee:', 'bookmify').'</span></div>
															<div class="detail_box_col col_right"><span>'.$pEmployeeFirstName.' '.$pEmployeeLastName.'</span></div>
														</div>
													</div>
												</div>
												
												<div class="detail_box">
													<div class="detail_box_header"><h4>'.esc_html__('Payment', 'bookmify').'</h4></div>
													<div class="detail_box_content">
														<div class="detail_box_row">
															<div class="detail_box_col col_left"><span>'.esc_html__('Date:', 'bookmify').'</span></div>
															<div class="detail_box_col col_right"><span>'.$createdDay.'</span></div>
														</div>
														<div class="detail_box_row">
															<div class="detail_box_col col_left"><span>'.esc_html__('Payment Method:', 'bookmify').'</span></div>
															<div class="detail_box_col col_right"><span>'.$paymentGateway.'</span></div>
														</div>
														<div class="detail_box_row">
															<div class="detail_box_col col_left"><span>'.esc_html__('Status:', 'bookmify').'</span></div>
															<div class="detail_box_col col_right"><span>'.$paymentStatus.'</span></div>
														</div>
													</div>
												</div>
												
												<div class="detail_box highlighted">
													<div class="detail_box_content">
														<div class="detail_box_row">
															<div class="detail_box_col col_left"><span>'.esc_html__('Service Price:', 'bookmify').'</span></div>
															<div class="detail_box_col col_right"><span>'.Helper::bookmifyPriceCorrection($priceService).'</span></div>
														</div>
														<div class="detail_box_row">
															<div class="detail_box_col col_left"><span>'.esc_html__('Extras Price:', 'bookmify').'</span></div>
															<div class="detail_box_col col_right"><span>'.Helper::bookmifyPriceCorrection($priceExtras).'</span></div>
														</div>
														<div class="detail_box_row">
															<div class="detail_box_col col_left"><span>'.esc_html__('Service Tax:', 'bookmify').'</span></div>
															<div class="detail_box_col col_right"><span>'.Helper::bookmifyPriceCorrection(floor($priceService*$taxCustomer)/100).'</span></div>
														</div>
														<div class="detail_box_row">
															<div class="detail_box_col col_left"><span>'.esc_html__('Extras Tax:', 'bookmify').'</span></div>
															<div class="detail_box_col col_right"><span>'.Helper::bookmifyPriceCorrection(floor($priceExtras*$taxCustomer)/100).'</span></div>
														</div>
														<div class="detail_box_row">
															<div class="detail_box_col col_left"><span>'.esc_html__('Paid:', 'bookmify').'</span></div>
															<div class="detail_box_col col_right"><span>'.Helper::bookmifyPriceCorrection($pPaidSum).'</span></div>
														</div>
														<div class="detail_box_row">
															<div class="detail_box_col col_left"><span>'.esc_html__('Due:', 'bookmify').'</span></div>
															<div class="detail_box_col col_right"><span>'.Helper::bookmifyPriceCorrection($paymentDue).'</span></div>
														</div>
														<div class="detail_box_row total">
															<div class="detail_box_col col_left"><span>'.esc_html__('Total Price:', 'bookmify').'</span></div>
															<div class="detail_box_col col_right"><span>'.Helper::bookmifyPriceCorrection($pTotalPrice).'</span></div>
														</div>
													</div>
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
			'bookmify_be_data' 		=> $buffy
		);

		if ( true === $isAjaxCall ){die(json_encode($buffyArray));} 
		else{return json_encode($buffyArray);}
	}
	
	
	

	/**
	 * @since 1.0.0
	 * @access protected
	*/
	protected function get_page_title() {
		return esc_html__( 'Payments', 'bookmify' );
	}
}
	

