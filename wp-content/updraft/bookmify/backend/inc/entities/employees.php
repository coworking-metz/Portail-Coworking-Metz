<?php
namespace Bookmify;


use Bookmify\Helper;
use Bookmify\HelperAdmin;
use Bookmify\HelperTime;
use Bookmify\HelperEmployees;
use Bookmify\NotificationManagement;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


class Employees{
	
	const PAGE_ID = 'bookmify_employees';
	
	private $per_page;
	private $google_data;
	
	public function __construct() {
		$this->assignValToVar();
		
		add_action( 'admin_menu', [ $this, 'register_admin_menu' ], 20 );
		
		
		// pagination employee list
		add_action( 'wp_ajax_ajaxFilterEmployeeList', [$this, 'ajaxFilterEmployeeList'] );
		
		
		
		add_action( 'wp_ajax_ajaxQueryEditEmployee', [$this, 'ajaxQueryEditEmployee'] );
		add_action( 'wp_ajax_ajaxQueryDeleteEmployee', [$this, 'ajaxQueryDeleteEmployee'] );
		add_action( 'wp_ajax_ajaxQueryInsertOrUpdateEmployee', [$this, 'ajaxQueryInsertOrUpdateEmployee'] );
		
		add_action( 'wp_ajax_ajaxQueryAddOffDayTimely', [$this, 'ajaxQueryAddOffDayTimely'] );
		add_action( 'wp_ajax_ajaxQueryDeleteDayOff', [$this, 'ajaxQueryDeleteDayOff'] );
	}
	
	public function assignValToVar(){
		$this->per_page = get_option('bookmify_be_employes_pp', 10);
	}

	/**
	 * @since 1.0.0
	 * @access public
	*/
	public function register_admin_menu() {
		add_submenu_page(
			BOOKMIFY_MENU,
			esc_html__( 'Employees', 'bookmify' ),
			esc_html__( 'Employees', 'bookmify' ),
			'bookmify_be_read_employees',
			self::PAGE_ID,
			[ $this, 'display_employees_page' ]
		);
	}
	
	
	/**
     * Gets google data
     */
    public static function getGoogleData(){
        return $this->google_data;
    }

    /**
     * Sets google data
     */
    public static function setGoogleData( $google_data ){
        $this->google_data = $google_data;
        return $this;
    }
	
	
	public function display_employees_page() {
		global $wpdb;

		echo HelperAdmin::bookmifyAdminContentStart();
		?>
		
		<div class="bookmify_be_content_wrap bookmify_be_employees_page">
			<div class="bookmify_be_page_title">
				<h3><?php echo esc_html($this->get_page_title()); ?><span class="count"><?php echo Helper::bookmifyItemsCount('employees');?></span></h3>
				<div class="bookmify_be_add_new_item bookmify_be_add_new_employee">
					<a href="#" class="bookmify_button add_new bookmify_add_new_button">
						<span class="text"><?php esc_html_e('Add New Employee','bookmify');?></span>
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
			<div class="bookmify_be_page_content">
				
				<div class="bookmify_be_employees">
					

					<!-- Employee Filter -->
					<div class="bookmify_be_filter_wrap">
						
						
						
						<div class="bookmify_be_filter">
							<div class="bookmify_be_row">
								<div class="bookmify_be_filter_search">
									<div class="bookmify_be_filter_list_in">
										<input type="text" placeholder="<?php esc_attr_e('Search Employees', 'bookmify');?>" class="filter_search"/>
										<span class="icon">
											<img class="bookmify_be_svg" src="<?php echo BOOKMIFY_ASSETS_URL.'img/search.svg';?>" alt="" />
											<span class="bookmify_be_loader small">
												<span class="loader_process">
													<span class="ball"></span>
													<span class="ball"></span>
													<span class="ball"></span>
												</span>
											</span>
											<span class="reset"></span>
										</span>
									</div>
								</div>
								<div class="bookmify_be_filter_list">
									<div class="bookmify_be_filter_list_in">
										<div class="input_wrapper">
											<input readonly data-placeholder="<?php esc_attr_e('Services', 'bookmify');?>" type="text" placeholder="<?php esc_attr_e('Services', 'bookmify');?>" class="filter_list"/>
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
										<?php echo HelperEmployees::servicesListAsFilter();?>
									</div>
								</div>
								<div class="bookmify_be_filter_order">
									<div class="bookmify_be_filter_list_in">
										<a href="#" class="filter_order">
											<span class="filter_spans_wrap">
												<span class="fsw_a"></span>
												<span class="fsw_b"></span>
												<span class="fsw_c"></span>
											</span>
										</a>
									</div>
								</div>
							</div>
								
						</div>
					</div>
					<!-- /Employee Filter -->
					
					<!-- Employee Header -->
					<div class="bookmify_be_employees_header">
						<div class="bookmify_be_employees_header_in">
							<span class="list_title"><?php esc_html_e('Name', 'bookmify');?></span>
							<span class="list_email"><?php esc_html_e('Email', 'bookmify');?></span>
							<span class="list_phone"><?php esc_html_e('Phone', 'bookmify');?></span>
						</div>
					</div>
					<!-- /Employee Header -->
					
					<!-- Employee List -->
					<div class="bookmify_be_employees_list">
						
						<div class="bookmify_be_employee_list_content">
							<?php echo $this->employees_list(); ?>
						</div>
					</div>
					<!-- /Employee List -->
					
				</div>
				
			</div>
			<?php echo HelperEmployees::clonableForm(); ?>
		</div>
		<?php echo HelperAdmin::bookmifyAdminContentEnd();
		
	}
	
	/**
	 * @since 1.0.0
	 * @access public
	*/
	public function employees_list(){
		global $wpdb;
		
		$query 		= "SELECT * FROM {$wpdb->prefix}bmify_employees";
		$page       = 1;
		
		$Querify  	= new Querify( $query, 'employee_list' );
		$results  	= $Querify->getData( $this->per_page, $page );
		
		$html 		= '<ul class="employees_list">';
		if(count($results->data) == 0){
			$html .= Helper::bookmifyBeNoItems();
		}
		for( $i = 0; $i < count( $results->data ); $i++ ){
			$ID					= $results->data[$i]->id;
			$firstName			= $results->data[$i]->first_name;
			$lastName			= $results->data[$i]->last_name;
			$email				= $results->data[$i]->email;
			$phone				= $results->data[$i]->phone;
			$attachmentID		= $results->data[$i]->attachment_id;
			$attachmentURL	 	= Helper::bookmifyGetImageByID($attachmentID);
			
			$html .=   '<li data-entity-id="'.$ID.'" class="bookmify_be_list_item">
							<div class="bookmify_be_list_item_in">
								<div class="bookmify_be_list_item_header">
									<div class="header_in">

										<div class="img_and_color_holder">
											<div class="img_holder" style="background-image:url('.$attachmentURL.')"></div>
										</div>
										<div class="employee_first_name employee_info">
											<span class="employee_title">
												<span class="e_name">'.$firstName.' '.$lastName.'</span>
												<span class="e_email">'.$email.'</span>
											</span>
											<span class="employee_email">'.$email.'</span>
											<span class="employee_phone">'.$phone.'</span>
										</div>
										<div class="buttons_holder">
											<div class="btn_item btn_edit">
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
											<div class="btn_item">
												<a href="#" class="bookmify_be_delete" data-entity-id="'.$ID.'">
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
							</div>
						</li>';
		}
		$html .= '</ul>';
		
		$html .= $Querify->getPagination( 1, 'bookmify_be_pagination employee_list');
		return $html;
	}
	
	public function ajaxFilterEmployeeList(){
		global $wpdb;
		$isAjaxCall 	= false;
		$html 			= '';
		$page 			= 1;
		$filter 		= array();
		$search_text 	= '';
		$employeeIDs	= array();
		$servicesList	= array();
		$toShow			= '';
		if (!empty($_POST['bookmify_page'])) {
			$isAjaxCall 	= true;
			$page 			= $_POST['bookmify_page'];
			$search_text 	= $_POST['bookmify_search'];
			$order 			= $_POST['bookmify_order'];
			if(!empty( $_POST['bookmify_services'])){
				$servicesList	= $_POST['bookmify_services'];
			}
			
			if($search_text != ''){ 
				$filter['search'] = $search_text;
			}
			
			// 
			if(!empty($servicesList)){
				$servicesList = esc_sql($servicesList);
				$query  = "SELECT employee_id FROM {$wpdb->prefix}bmify_employee_services WHERE `service_id` IN (" . implode(",", array_map("intval", $servicesList)) . ")";
				$results = $wpdb->get_results( $query, OBJECT  );
				if(!empty($results)){
					foreach($results as $result){
						$employeeIDs[] = $result->employee_id;
					}
				}
				if(!empty($employeeIDs)){
					$filter['ids'] = $employeeIDs;
				}else{
					$toShow = 'nothing';
				}
			}
			if($toShow == 'nothing'){
				$html = '';
			}else{
				// SELECT
				$query 			= "SELECT * FROM {$wpdb->prefix}bmify_employees";
				$Querify  		= new Querify( $query, 'employee_list' );
				$results		= $Querify->getData( $this->per_page, $page, $filter, $order );


				$html 			= '<ul class="employees_list">';
				if(count($results->data) == 0){
					$html .= Helper::bookmifyBeNoItems();
				}
				for( $i = 0; $i < count( $results->data ); $i++ ){
					$ID					= $results->data[$i]->id;
					$firstName			= $results->data[$i]->first_name;
					$lastName			= $results->data[$i]->last_name;
					$email				= $results->data[$i]->email;
					$phone				= $results->data[$i]->phone;
					$attachmentID		= $results->data[$i]->attachment_id;
					$attachmentURL	 	= Helper::bookmifyGetImageByID($attachmentID);

					$html .=   '<li data-entity-id="'.$ID.'" class="bookmify_be_list_item bookmify_be_animated">
									<div class="bookmify_be_list_item_in">
										<div class="bookmify_be_list_item_header">
											<div class="header_in">

												<div class="img_and_color_holder">
													<div class="img_holder" style="background-image:url('.$attachmentURL.')"></div>
												</div>
												<div class="employee_first_name employee_info">
													<span class="employee_title">
														<span class="e_name">'.$firstName.' '.$lastName.'</span>
														<span class="e_email">'.$email.'</span>
													</span>
													<span class="employee_email">'.$email.'</span>
													<span class="employee_phone">'.$phone.'</span>
												</div>
												<div class="buttons_holder">
													<div class="btn_item btn_edit">
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
													<div class="btn_item">
														<a href="#" class="bookmify_be_delete" data-entity-id="'.$ID.'">
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
									</div>
								</li>';
				}
				$html .= '</ul>';
				$html .= $Querify->getPagination( 1, 'bookmify_be_pagination employee_list');
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
			'bookmify_be_data' 		=> $buffy // employeeIDs filter['list']
		);

		if ( true === $isAjaxCall ){die(json_encode($buffyArray));} 
		else{return json_encode($buffyArray);}
												
	}
	
	

	
	
	/**
	 * @since 1.0.0
	 * @access public
	*/
	public function ajaxQueryDeleteEmployee(){
		global $wpdb;
		$isAjaxCall 	= false;
		$employeeID 	= '';
		
		if (!empty($_POST['bookmify_employee_id'])){
			$isAjaxCall = true;
			$employeeID = $_POST['bookmify_employee_id'];
			
			
			$now		= HelperTime::getCurrentDateTime();
			$employeeID = esc_sql($employeeID);
			$now 		= esc_sql($now);
			$count 		= $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->prefix}bmify_appointments WHERE employee_id=".$employeeID." AND end_date > '".$now."'" );
			
			
			if($count == 0){
				$wpdb->query($wpdb->prepare( "DELETE FROM {$wpdb->prefix}bmify_employees WHERE id=%d", $employeeID));
			}

			$buffyArray = array(
				'number'				=> Helper::bookmifyItemsCount('employees'),
				'count'					=> $count,
			);

			if ( true === $isAjaxCall ){die(json_encode($buffyArray));} 
			else{return json_encode($buffyArray);}
		}
	}
	
	
	
	
	public function ajaxQueryInsertOrUpdateEmployee(){
		global $wpdb;
		$isAjaxCall 			= false;
		$demo 					= '';
		
		// **************************************************************************************************************************
		// UPDATE EXISTING EMPLOYEE
		// **************************************************************************************************************************
		if ($_POST['insertOrUpdate'] == 'update') {
			
			// update details
			if (!empty($_POST['bookmify_data'])) {
				$isAjaxCall			= true;
				$employee 			= json_decode(stripslashes($_POST['bookmify_data']));
				$employeeID 		= $employee->id;
				$employeeFirstName 	= $employee->first_name;
				$employeeLastName 	= $employee->last_name;
				$employeeEmail 		= $employee->email;
				$employeePhone 		= $employee->phone;
				$employeeAttID 		= $employee->att_id;
				$employeeWPUserID 	= $employee->wp_user_id;
				$demoWPID		 	= $employee->wp_user_id;
				$employeeDesc 		= $employee->desc;
				$employeeLocID		= $employee->location_id;
				$employeeChecked	= $employee->checked;
				
				
				if($employeeWPUserID == 'n' && $demo == ''){
					$employeeWPUserID   = $this->addWPUser($employee, $employeeID);
				}
				if($demo != ''){
					$employeeWPUserID 	= 0;
				}

				$wpdb->query($wpdb->prepare( "UPDATE {$wpdb->prefix}bmify_employees SET first_name=%s, last_name=%s, email=%s, phone=%s, attachment_id=%d, wp_user_id=%d, info=%s, visibility=%s WHERE id=%d", $employeeFirstName, $employeeLastName, $employeeEmail, $employeePhone, $employeeAttID, $employeeWPUserID, $employeeDesc, $employeeChecked, $employeeID));

				if($employeeLocID != ''){
					$employeeID = esc_sql($employeeID);
					$count = $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->prefix}bmify_employee_locations WHERE employee_id=".$employeeID );

					if($count == 0){
						$wpdb->query($wpdb->prepare( "INSERT INTO {$wpdb->prefix}bmify_employee_locations ( location_id, employee_id ) VALUES ( %d, %d )", $employeeLocID, $employeeID ));
					}else{
						$wpdb->query($wpdb->prepare( "UPDATE {$wpdb->prefix}bmify_employee_locations SET location_id=%d WHERE employee_id=%d", $employeeLocID, $employeeID));
					}
				}else{
					$wpdb->query($wpdb->prepare( "DELETE FROM {$wpdb->prefix}bmify_employee_locations WHERE employee_id=%d", $employeeID));
				}
				
			}
			

			// update services
			$old_dis				= array();
			$chosen_dis 			= array();

			if (!empty($_POST['bookmify_service_data'])) {
				$isAjaxCall			= true;
				$extraSer 			= json_decode(stripslashes($_POST['bookmify_service_data']));


				foreach($extraSer as $extras){
					foreach($extras->employeeID as $extra){
						$employeeID = $extra;
					}
				}
				// Get all existing business hours
				$employeeID = esc_sql($employeeID);
				$query = "SELECT service_id FROM {$wpdb->prefix}bmify_employee_services WHERE employee_id=".$employeeID;
				$eebhs = $wpdb->get_results( $query, OBJECT  );


				foreach($eebhs as $eebh){
					$old_dis[] = $eebh->service_id;
				}
				foreach($extras->allextra as $extra){
					$chosen_dis[] = $extra->id;
				}
				$existing_dis 		= array_values(array_intersect($old_dis, $chosen_dis)); 	// existing location ids
				$new_dis 			= array_values(array_diff($chosen_dis,$old_dis)); 			// new location ids
				$released_dis 		= array_values(array_diff($old_dis,$chosen_dis)); 			// released location ids


				// Update existing day indexes
				if(!empty($existing_dis)){
					foreach($existing_dis as $existing_di){
						foreach($extras->allextra as $struct) {
							if ($existing_di == $struct->id) {
								$item = $struct;
								break;
							}
						}
						$price 		= $item->price;
						$min_cap 	= $item->min_cap;
						$max_cap 	= $item->max_cap;
						$deposit 	= (int)$item->deposit;
						if (!(is_numeric($min_cap))) {$min_cap = 1;}
						if (!(is_numeric($max_cap))) {$min_cap = 1;$max_cap = 1;}
						if($min_cap > $max_cap){$min_cap = $max_cap;}
						if($deposit < 0){$deposit = 0;}
						if($deposit > 100){$deposit = 100;}
						$deposit	= $deposit.'%';

						$wpdb->query($wpdb->prepare( "UPDATE {$wpdb->prefix}bmify_employee_services SET price=%f, capacity_min=%d, capacity_max=%d, deposit=%s WHERE employee_id=%d AND service_id=%d", $price, $min_cap, $max_cap, $deposit, $employeeID, $existing_di));
					}
				}

				// Insert new day indexes
				if(!empty($new_dis)){
					foreach($new_dis as $new_di){
						foreach($extras->allextra as $struct) {
							if ($new_di == $struct->id) {
								$item = $struct;
								break;
							}
						}
						$price 		= $item->price;
						$min_cap 	= $item->min_cap;
						$max_cap 	= $item->max_cap;
						$deposit 	= (int)$item->deposit;
						if (!(is_numeric($min_cap))) {$min_cap = 1;}
						if (!(is_numeric($max_cap))) {$min_cap = 1;$max_cap = 1;}
						if($min_cap > $max_cap){$min_cap = $max_cap;}
						if($deposit < 0){$deposit = 0;}
						if($deposit > 100){$deposit = 100;}
						$deposit	= $deposit.'%';

						$wpdb->query($wpdb->prepare( "INSERT INTO {$wpdb->prefix}bmify_employee_services ( price, capacity_min, capacity_max, employee_id, service_id, deposit ) VALUES ( %f, %d, %d, %d, %d, %s )", $price, $min_cap, $max_cap, $employeeID, $new_di, $deposit ));
					}
				}

				// Delete released day indexes
				if(!empty($released_dis)){
					foreach($released_dis as $released_di){
						$wpdb->query($wpdb->prepare( "DELETE FROM {$wpdb->prefix}bmify_employee_services WHERE employee_id=%d AND service_id=%d", $employeeID, $released_di));
					}
				}

			}



			
			$employeeID				= '';


			$oldDayOffIndexes		= array();
			$chosendDayOffIndexes	= array();
			$dayswh					= array();

			if (1) {
				$isAjaxCall 		= true;
				$employeeID 		= $_POST['employeeID'];
				
				// working hours and breaks, modified since 1.3.3
				$workingHours		= $_POST['bookmify_wh_data'];
				if(!empty($_POST['allBreaks'])){
					$allBreaks		= $_POST['allBreaks'];
				}
				
				// AFTER HARD WORK: delete ALL breaks of selected employee
				$wpdb->query($wpdb->prepare( "DELETE FROM {$wpdb->prefix}bmify_employee_business_hours_breaks WHERE employee_id=%d",$employeeID));
				
				for($i = 0; $i < 7; $i++){
					$whKey			= (int)$i + 1;
					if($workingHours[$i][0] == 1){
						// working hours
						$start	= $workingHours[$i][1];
						$end	= $workingHours[$i][2];
						if($start != '' && $end != ''){
							array_push($chosendDayOffIndexes,$whKey);
							$dayswh[$whKey] = array('start_time' => $start,'end_time' => $end);
						}
						// breaks
						if(isset($allBreaks)){
							if(!empty($allBreaks[$i])){
								foreach($allBreaks[$i] as $mykey => $item){
									if(($mykey % 2) == 0){$monday_start = $item;}else{
										$monday_end = $item;
										$wpdb->query($wpdb->prepare( "INSERT INTO {$wpdb->prefix}bmify_employee_business_hours_breaks (employee_id, start_time, end_time, day_index) VALUES ( %s, %s, %s, %d )", $employeeID, $monday_start, $monday_end, $whKey));
									}
								}
							}
						}
					}
				}

				// Get all existing business hours
				$query = "SELECT day_index FROM {$wpdb->prefix}bmify_employee_business_hours WHERE employee_id=".$employeeID; 
				$eebhs = $wpdb->get_results( $query, OBJECT  );
				
				foreach($eebhs as $eebh){
					$oldDayOffIndexes[] = $eebh->day_index;
				}


				$existing_dis 		= array_values(array_intersect($oldDayOffIndexes, $chosendDayOffIndexes)); 	// existing day indexes
				$new_dis 			= array_values(array_diff($chosendDayOffIndexes, $oldDayOffIndexes)); 		// new day indexes
				$released_dis 		= array_values(array_diff($oldDayOffIndexes, $chosendDayOffIndexes)); 		// released day indexes


				// Update existing day indexes
				if(!empty($existing_dis)){
					foreach($existing_dis as $existing_di){
						$start 	= $dayswh[$existing_di]['start_time'];
						$end 	= $dayswh[$existing_di]['end_time'];
						$start 	= date('H:i', strtotime($start));
						$end 	= date('H:i', strtotime($end));
						
						$wpdb->query($wpdb->prepare( "UPDATE {$wpdb->prefix}bmify_employee_business_hours SET start_time=%s, end_time=%s WHERE employee_id=%d AND day_index=%d", $start, $end, $employeeID, $existing_di));
					}
				}

				// Insert new day indexes
				if(!empty($new_dis)){
					foreach($new_dis as $new_di){
						$start 	= $dayswh[$new_di]['start_time'];
						$end 	= $dayswh[$new_di]['end_time'];
						$start 	= date('H:i', strtotime($start));
						$end 	= date('H:i', strtotime($end));
						
						$wpdb->query($wpdb->prepare( "INSERT INTO {$wpdb->prefix}bmify_employee_business_hours ( employee_id, day_index, start_time, end_time ) VALUES ( %d, %d, %s, %s)", $employeeID, $new_di, $start, $end));
					}
				}

				// Delete released day indexes
				if(!empty($released_dis)){
					foreach($released_dis as $released_di){
						$wpdb->query($wpdb->prepare( "DELETE FROM {$wpdb->prefix}bmify_employee_business_hours WHERE employee_id=%d AND day_index=%d", $employeeID, $released_di));
					}
				}
				
			}

			if (!empty($_POST['bookmify_do_data'])) {
				$isAjaxCall			= true;
				$dayOffObject		= json_decode(stripslashes($_POST['bookmify_do_data']));
				$employeeID 		= $_POST['employeeID'];


				foreach($dayOffObject as $dayOffs){
					foreach($dayOffs->alldayOffs as $dayOff){
						$dayOffID		= '';
						if(isset($dayOff->id)){
							$dayOffID 	= $dayOff->id;
						}
						$dayOffDateOff 	= $dayOff->dateOff;
						$dayOffTitle 	= $dayOff->title;
						$dayOffChecked 	= $dayOff->checked;
						if($dayOffID == 'undefined' || $dayOffID == ''){
							$wpdb->query($wpdb->prepare( "INSERT INTO {$wpdb->prefix}bmify_dayoff (employee_id, title, date, every_year) VALUES ( %d, %s, %s, %d )", $employeeID, $dayOffTitle, $dayOffDateOff, $dayOffChecked));
						}else{
							$wpdb->query($wpdb->prepare( "UPDATE {$wpdb->prefix}bmify_dayoff SET title=%s, date=%s, every_year=%d WHERE id=%d", $dayOffTitle, $dayOffDateOff, $dayOffChecked, $dayOffID));
						}
					}
				}

			}
		}else{
			// **************************************************************************************************************************
			// INSERT NEW EMPLOYEE
			// **************************************************************************************************************************
			
			// insert details
			$employeeID = '';
			if (!empty($_POST['bookmify_data'])) {
				$isAjaxCall			= true;
				$employee 			= json_decode(stripslashes($_POST['bookmify_data']));
				
				$employeeFirstName 	= $employee->first_name;
				$employeeLastName 	= $employee->last_name;
				$employeeEmail 		= $employee->email;
				$employeePhone 		= $employee->phone;
				$employeeAttID 		= $employee->att_id;
				$employeeWPUserID 	= $employee->wp_user_id;
				$demoWPID		 	= $employee->wp_user_id;
				$employeeDesc 		= $employee->desc;
				$employeeLocID		= $employee->location_id;
				$employeeChecked	= $employee->checked;
				
				
				
				
				$wpdb->query($wpdb->prepare( "INSERT INTO {$wpdb->prefix}bmify_employees ( first_name, last_name, email, phone, attachment_id, info, visibility ) VALUES ( %s, %s, %s, %s, %d, %s, %s )", $employeeFirstName, $employeeLastName, $employeeEmail, $employeePhone, $employeeAttID, $employeeDesc, $employeeChecked ));
				
				// get this employee ID
				$query 		= "SELECT id FROM {$wpdb->prefix}bmify_employees ORDER BY id DESC LIMIT 1";
				$results 	= $wpdb->get_results( $query, OBJECT  );
				$employeeID = $results[0]->id;
				
				
				if($employeeWPUserID == 'n' && $demo == ''){
					$employeeWPUserID   = $this->addWPUser($employee, $employeeID);
				}
				if($demo != ''){
					$employeeWPUserID 	= 0;
				}
				// insert wordpress user id to new employee
				$wpdb->query($wpdb->prepare( "UPDATE {$wpdb->prefix}bmify_employees SET wp_user_id=%d WHERE id=%d", $employeeWPUserID, $employeeID));
				
				if($employeeLocID != ''){
					$wpdb->query($wpdb->prepare( "INSERT INTO {$wpdb->prefix}bmify_employee_locations ( location_id, employee_id ) VALUES ( %d, %d )", $employeeLocID, $employeeID ));
				}

			}
			
			
			// insert services
			if (!empty($_POST['bookmify_service_data'])) {
				$isAjaxCall			= true;
				$extraSer 			= json_decode(stripslashes($_POST['bookmify_service_data']));


				foreach($extraSer as $extras){
					foreach($extras->allextra as $extra){
						$price 		= $extra->price;
						$min_cap 	= (int)$extra->min_cap;
						$max_cap 	= (int)$extra->max_cap;
						$serviceID 	= (int)$extra->id;
						$deposit 	= (int)$extra->deposit;
						if (!(is_numeric($min_cap))) {$min_cap = 1;}
						if (!(is_numeric($max_cap))) {$min_cap = 1;$max_cap = 1;}
						if($min_cap > $max_cap){$min_cap = $max_cap;}
						if($deposit < 0){$deposit = 0;}
						if($deposit > 100){$deposit = 100;}
						$deposit	= $deposit.'%';

						$wpdb->query($wpdb->prepare( "INSERT INTO {$wpdb->prefix}bmify_employee_services ( price, capacity_min, capacity_max, employee_id, service_id, deposit ) VALUES ( %f, %d, %d, %d, %d, %s )", $price, $min_cap, $max_cap, $employeeID, $serviceID, $deposit ));
					}
				}

			}
			
			// insert working hours			
			if (!empty($_POST['bookmify_wh_data'])){
				$isAjaxCall = true;
				
				// insert working hours and breaks, modified since 1.3.3
				$workingHours		= $_POST['bookmify_wh_data'];
				if(!empty($_POST['allBreaks'])){
					$allBreaks		= $_POST['allBreaks'];
				}
				
				for($i = 0; $i < 7; $i++){
					$whKey			= (int)$i + 1;
					if($workingHours[$i][0] == 1){
						// working hours
						$start	= $workingHours[$i][1];
						$end	= $workingHours[$i][2];
						if($start != '' && $end != ''){
							$wpdb->query($wpdb->prepare( "INSERT INTO {$wpdb->prefix}bmify_employee_business_hours ( employee_id, day_index, start_time, end_time ) VALUES ( %d, %d, %s, %s)", $employeeID, $whKey, $start, $end));
						}
						// breaks
						if(isset($allBreaks)){
							if(!empty($allBreaks[$i])){
								foreach($allBreaks[$i] as $mykey => $item){
									if(($mykey % 2) == 0){$monday_start = $item;}else{
										$monday_end = $item;
										$wpdb->query($wpdb->prepare( "INSERT INTO {$wpdb->prefix}bmify_employee_business_hours_breaks (employee_id, start_time, end_time, day_index) VALUES ( %s, %s, %s, %d )", $employeeID, $monday_start, $monday_end, $whKey));
									}
								}
							}
						}
					}
				}
				
			}
			
			// insert days off
			if (!empty($_POST['bookmify_do_data'])) {
				$isAjaxCall			= true;
				$dayOffObject		= json_decode(stripslashes($_POST['bookmify_do_data']));

				foreach($dayOffObject as $dayOffs){
					foreach($dayOffs->alldayOffs as $dayOff){
						$dayOffDateOff 	= $dayOff->dateOff;
						$dayOffTitle 	= $dayOff->title;
						$dayOffChecked 	= $dayOff->checked;
						
						$wpdb->query($wpdb->prepare( "INSERT INTO {$wpdb->prefix}bmify_dayoff (employee_id, title, date, every_year) VALUES ( %d, %s, %s, %d )", $employeeID, $dayOffTitle, $dayOffDateOff, $dayOffChecked));
					}
				}

			}
		}
		
		$page 			= 1;
		$filter 		= array();
		$search_text 	= '';
		$employeeIDs	= array();
		$servicesList	= array();
		$toShow			= '';
		if(!empty($_POST['do'])){
			$page 			= $_POST['bookmify_page'];
			$search_text 	= $_POST['bookmify_search'];
			$order 			= $_POST['bookmify_order'];
			if(!empty( $_POST['bookmify_services'])){
				$servicesList	= $_POST['bookmify_services'];
			}
			if($search_text != ''){ 
				$filter['search'] = $search_text;
			}
			if(!empty($servicesList)){
				$servicesList = esc_sql($servicesList);
				$query  = "SELECT employee_id FROM {$wpdb->prefix}bmify_employee_services WHERE `service_id` IN (" . implode(",", array_map("intval", $servicesList)) . ")";
				$results = $wpdb->get_results( $query, OBJECT  );
				if(!empty($results)){
					foreach($results as $result){
						$employeeIDs[] = $result->employee_id;
					}
				}
				if(!empty($employeeIDs)){
					$filter['ids'] = $employeeIDs;
				}else{
					$toShow = 'nothing';
				}
			}
			if($toShow == 'nothing'){
				$html = '';
			}else{
				// SELECT
				$query 			= "SELECT * FROM {$wpdb->prefix}bmify_employees";
				$Querify  		= new Querify( $query, 'employee_list' );
				$results		= $Querify->getData( $this->per_page, $page, $filter, $order );


				$html 			= '<ul class="employees_list">';
				if(count($results->data) == 0){
					$html .= Helper::bookmifyBeNoItems();
				}
				for( $i = 0; $i < count( $results->data ); $i++ ){
					$ID					= $results->data[$i]->id;
					$firstName			= $results->data[$i]->first_name;
					$lastName			= $results->data[$i]->last_name;
					$email				= $results->data[$i]->email;
					$phone				= $results->data[$i]->phone;
					$attachmentID		= $results->data[$i]->attachment_id;
					$attachmentURL	 	= Helper::bookmifyGetImageByID($attachmentID);

					$html .=   '<li data-entity-id="'.$ID.'" class="bookmify_be_list_item bookmify_be_animated">
									<div class="bookmify_be_list_item_in">
										<div class="bookmify_be_list_item_header">
											<div class="header_in">
												<div class="img_and_color_holder">
													<div class="img_holder" style="background-image:url('.$attachmentURL.')"></div>
												</div>
												<div class="employee_first_name employee_info">
													<span class="employee_title">
														<span class="e_name">'.$firstName.' '.$lastName.'</span>
														<span class="e_email">'.$email.'</span>
													</span>
													<span class="employee_email">'.$email.'</span>
													<span class="employee_phone">'.$phone.'</span>
												</div>
												<div class="buttons_holder">
													<div class="btn_item btn_edit">
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
													<div class="btn_item">
														<a href="#" class="bookmify_be_delete" data-entity-id="'.$ID.'">
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
									</div>

								</li>';
				}
				$html .= '</ul>';

				$html .= $Querify->getPagination( 1, 'bookmify_be_pagination employee_list');
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
		
		if(($demoWPID == 'n' || is_numeric($demoWPID)) && $demo == 'demo' && $demoWPID != 0){
			$demo = 'cant';
		}
		$buffyArray = array(
			'bookmify_be_data' 		=> $buffy,
			'number'				=> Helper::bookmifyItemsCount('employees'),
			'demo_check'			=> $demo,
			'asd'					=> $workingHours[0][0],
		);

		if ( true === $isAjaxCall ){die(json_encode($buffyArray));} 
		else{return json_encode($buffyArray);}
	}
	
	
	public function ajaxQueryEditEmployee(){
		global $wpdb;
		$isAjaxCall 	= false;
		$html 			= '';
		$authCode 		= '';
		
		if (!empty($_POST['bookmify_data'])) {
			$isAjaxCall = true;
			$id 		= $_POST['bookmify_data'];
			$authCode 	= $_POST['bookmify_authcode'];

			// SELECT
			$id 			= esc_sql($id);
			$query 			= "SELECT id FROM {$wpdb->prefix}bmify_employees WHERE id=".$id;
			$employees	 	= $wpdb->get_results( $query, OBJECT  );
			
			
			foreach($employees as $employee){
				$ID			= $employee->id;			
			
				
				
				$html .= '<div class="bookmify_be_popup_form_wrap" data-entity-id="'.$ID.'">
							'.HelperEmployees::allNanoInOne($ID).'
							
							<div class="bookmify_be_popup_form_position_fixer">
								<div class="bookmify_be_popup_form_bg">
									<div class="bookmify_be_popup_form">

										<div class="bookmify_be_popup_form_header">
											<h3>'.esc_html__('Edit Employee','bookmify').'</h3>
											<span class="closer"></span>
										</div>

										<div class="bookmify_be_popup_form_content">
											<div class="bookmify_be_popup_form_content_in">

												<div class="bookmify_be_popup_form_fields">

													
													<div class="bookmify_be_employeestabs_wrap bookmify_be_tab_wrap">
														<div class="bookmify_be_link_tabs">
															<ul class="bookmify_be_employeestabs_nav">
																<li class="active"><a class="bookmify_be_tab_link" href="#">'.esc_html__('Details','bookmify').'</a></li>
																<li><a class="bookmify_be_tab_link" href="#">'.esc_html__('Services','bookmify').'</a></li>
																<li><a class="bookmify_be_tab_link" href="#">'.esc_html__('Working Hours','bookmify').'</a></li>
																<li><a class="bookmify_be_tab_link" href="#">'.esc_html__('Days Off','bookmify').'</a></li>
															</ul>
														</div>
														<div class="bookmify_be_employeestabs_content bookmify_be_content_tabs">
															<div class="bookmify_be_tab_pane active employee_tab">'.HelperEmployees::getDetailsEmployeeTab($ID).'
															</div>
															<div class="bookmify_be_tab_pane employee_tab">'.HelperEmployees::getServicesEmployeeTab($ID).'</div>
															<div class="bookmify_be_tab_pane bookmify_be_wh_wrapper employee_tab">'.HelperEmployees::getWorkingHoursEmployeeTab($ID).'</div>
															<div class="bookmify_be_tab_pane bookmify_be_day_off_wrapper emloyee_tab">'.HelperEmployees::getDaysOffEmployeeTab($ID).'</div>
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
			'bookmify_be_id' 		=> $id,
			
		);

		if ( true === $isAjaxCall ){die(json_encode($buffyArray));} 
		else{return json_encode($buffyArray);}
	}
	
	
	
	
	public function ajaxQueryAddOffDayTimely(){
		global $wpdb;
		$html = '';
		
		$params = array();
		
		$isAjaxCall = false;

		if (!empty($_POST['bookmify_data'])) {
			$isAjaxCall = true;
			
			// we need to convert form data to array from string
 			parse_str($_POST['bookmify_data'], $params);
			$off_day_name 	= $params['offday_name'];
			$offday_days 	= $params['offday_days'];//'2018-01-01';//
			
			$offday_repeat 	= 0;
			if(isset($params['offday_repeat'])){
				$offday_repeat 	= $params['offday_repeat'];//'2018-01-01';//
			}
			
		}
		$offDays 		= explode(', ', $offday_days);
		$numberOfPost 	= 0;
		$result 		= '';
		foreach($offDays as $offDay){
			$every_year = 'no';
			$checked 	= '';
			$title 		= '';
			$date 		= '';
			if($offday_repeat == 1){
				$every_year = 'yes';
			}
			if($every_year == 'yes'){
				$checked = 'checked';
			}
			$randonNumber1 		= rand(10,9999);
			$randonNumber2 		= rand(10,9999);
			$update_block		= '<div class="bookmify_day_off_edit_dd">
									<form autocomplete="off">
										<div class="do_item">
											<label for="mdp-do-month-'.$randonNumber1.'-'.$randonNumber2.'">'.esc_html__('Date', 'bookmify').'<span>*</span></label>
											<input data-selected-day="'.$offDay.'" class="mdp-do-hidden required_field" id="mdp-do-month-'.$randonNumber1.'-'.$randonNumber2.'" type="text" name="offday_days" placeholder="'.esc_attr__('yy-mm-dd', 'bookmify').'" />
											<input class="offday_hidden_day" type="hidden" name="offday_hidden_day" id="offday_hidden_day_'.$randonNumber1.'-'.$randonNumber2.'" />
										</div>
										<div class="do_item">
											<label for="offday_name-'.$randonNumber1.'-'.$randonNumber2.'">'.esc_html__('Title', 'bookmify').'<span>*</span></label>
											<input class="required_field" id="offday_name-'.$randonNumber1.'-'.$randonNumber2.'" type="text" name="offday_name" placeholder="'.esc_attr__('Enter Off Day Title...', 'bookmify').'" value="'.$off_day_name.'" />
										</div>
										<div class="do_dd_footer">
											<div class="left_part">
												<label class="switch">
													<input type="checkbox" id="repeat-'.$randonNumber1.'-'.$randonNumber2.'" value="1" name="offday_repeat" '.$checked.' />
													<span class="slider round"></span>
												</label>
												<label class="repeater" for="repeat-'.$randonNumber1.'-'.$randonNumber2.'">'.esc_html__('Repeat Every Year', 'bookmify').'</label>
											</div>
										</div>
									</form>
								</div>';
			$edit_delete_panel = '<div class="buttons_holder">
										<div class="btn_item btn_edit">
											<a href="#" class="bookmify_be_edit">
												<img class="bookmify_be_svg edit" src="'.BOOKMIFY_ASSETS_URL.'img/edit.svg" alt="" />
												<img class="bookmify_be_svg checked" src="'.BOOKMIFY_ASSETS_URL.'img/checked.svg" alt="" />
											</a>
										</div>
										<div class="btn_item">
											<a href="#" class="bookmify_be_delete" data-entity-id="'.$randonNumber1.'-'.$randonNumber2.'">
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
									</div>';
			// date format URI: https://codex.wordpress.org/Formatting_Date_and_Time
			$date 	= '<span class="list_date">'.date_i18n(get_option('bookmify_be_date_format', 'd F, Y'), strtotime($offDay)).'</span>';
			$title 	= '<span class="list_title">'.$off_day_name.'</span>';
			$result .= '<li class="bookmify_be_list_item">
							<div class="bookmify_be_list_item_in">
								<div class="bookmify_be_list_item_header">
									<div class="header_in item" data-yearly="'.$every_year.'">
										<div class="header_info">
											<span class="f_year"></span>
											'.$date.$title.'
										</div>
										'.$edit_delete_panel.'
									</div>
								</div>
								'.$update_block.'
							</div>
						</li>';
		}
		$html .= $result;
		
		
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
			'html'					=> $html
		);
		if ( true === $isAjaxCall ) {die(json_encode($buffyArray));} 
		else {return json_encode($buffyArray);}

	}
	
	/**
	 * @since 1.0.0
	 * @access public
	*/
	
	public function ajaxQueryDeleteDayOff(){
		global $wpdb;
		
		if (!empty($_POST['bookmify_day_off_id'])) 
		{
			$id = $_POST['bookmify_day_off_id'];
			
			// DELETE
			$wpdb->query($wpdb->prepare( "DELETE FROM {$wpdb->prefix}bmify_dayoff WHERE id=%d", $id));
		}
	}
	
	/**
	 * @since 1.0.0
	 * @access public
	*/
	
	private function addWPUser($employee, $employeeID){
		
        $username 	= $employee->email;
        $password 	= wp_generate_password( 12, true );
        $userid 	= wp_create_user( $username, $password, $employee->email );
        if ( ! $userid instanceof \WP_Error ) {
            $user 	= new \WP_User( $userid );
            $user->set_role( 'bookmify-employee' );
			
            // Send email notification.
            NotificationManagement::sendNewEmployeeCredentials( $employee, $username, $password, $employeeID );

            return (int)$userid;
        }

        return false;
    }
	

	/**
	 * @since 1.0.0
	 * @access protected
	*/
	protected function get_page_title() {
		return esc_html__( 'Employees', 'bookmify' );
	}
}
	

