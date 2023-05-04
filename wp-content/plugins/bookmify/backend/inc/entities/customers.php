<?php
namespace Bookmify;

use Bookmify\Helper;
use Bookmify\HelperTime;
use Bookmify\HelperAdmin;
use Bookmify\HelperCustomers;
use Bookmify\NotificationManagement;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


class Customers{

	const PAGE_ID 		= 'bookmify_customers';
	
	

	/** @var int */
    protected $wp_user_id;
    /** @var string */
    protected $full_name = '';
    /** @var string */
    protected $first_name = '';
    /** @var string */
    protected $last_name = '';
    /** @var string */
    protected $email = '';
    /** @var string */
    protected $phone = '';
	/** @var string */
    protected $birthday;
    /** @var string */
    protected $country = '';
    /** @var string */
    protected $state = '';
    /** @var string */
    protected $postcode = '';
    /** @var string */
    protected $city = '';
    /** @var string */
    protected $street = '';
    /** @var  string */
    protected $info = '';
    /** @var string */
    protected $registration_date;
	
	private $per_page;
	
	
	
	
	/**
	 * @since 1.0.0
	 * @access public
	*/
	public function __construct() {
		
		$this->assignValToVar();
		
		add_action( 'admin_menu', [ $this, 'register_admin_menu' ], 20 );
		add_action( 'wp_ajax_ajaxQueryInsertOrUpdateCustomer', [$this, 'ajaxQueryInsertOrUpdateCustomer'] );
		add_action( 'wp_ajax_ajaxQueryEditCustomer', [$this, 'ajaxQueryEditCustomer'] );
		add_action( 'wp_ajax_ajaxFilterCustomerList', [$this, 'ajaxFilterCustomerList'] );
		add_action( 'wp_ajax_ajaxQueryDeleteCustomer', [$this, 'ajaxQueryDeleteCustomer'] );
		
	}
	
	/**
	 * @since 1.0.0
	 * @access public
	*/
	public function assignValToVar(){
		$this->per_page = get_option('bookmify_be_customers_pp', 10);
	}
	

	/**
	 * @since 1.0.0
	 * @access public
	*/
	public function register_admin_menu() {

		add_submenu_page(
			BOOKMIFY_MENU,
			esc_html__( 'Customers', 'bookmify' ),
			esc_html__( 'Customers', 'bookmify' ),
			'bookmify_be_read_customers',
			self::PAGE_ID,
			[ $this, 'display_customers_page' ]
		);
	}
	
	public function display_customers_page() {
		global $wpdb;

		echo HelperAdmin::bookmifyAdminContentStart();
		?>
		
		<div class="bookmify_be_content_wrap bookmify_be_customers_page">
			<div class="bookmify_be_page_title">
				<h3><?php echo esc_html($this->get_page_title()); ?><span class="count"><?php echo Helper::bookmifyItemsCount('customers'); ?></span></h3>
				<div class="bookmify_be_add_new_item bookmify_be_add_new_customer">
					<a href="#" class="bookmify_button add_new bookmify_add_new_button">
						<span class="text"><?php esc_html_e('Add New Customer','bookmify');?></span>
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
				
				<div class="bookmify_be_customers">
					
					<div class="bookmify_be_filter_wrap">
						<div class="bookmify_be_filter">
							<div class="bookmify_be_row">
								<div class="bookmify_be_filter_search">
									<input type="text" placeholder="<?php esc_html_e('Search Customer', 'bookmify');?>" class="filter_search"/>
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
								<div class="bookmify_be_filter_order">
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
					
					
					<!-- Customer Header -->
					<div class="bookmify_be_customers_header">
						<span class="list_title"><?php esc_html_e('Name', 'bookmify');?></span>
						<span class="list_email"><?php esc_html_e('Email', 'bookmify');?></span>
						<span class="list_phone"><?php esc_html_e('Phone', 'bookmify');?></span>
					</div>
					<!-- /Customer Header -->
					
					<!-- Customer List -->
					<div class="bookmify_be_customers_list">
						<div class="bookmify_be_customer_list_content">
							<?php echo $this->customersList(); ?>
						</div>
					</div>
					<!-- /Customer List -->
					
				</div>
				
			</div>
			
			<?php echo HelperCustomers::clonableForm(); ?>
			
		</div>
		<?php echo HelperAdmin::bookmifyAdminContentEnd();
		
	}
	
	
	/*
	 * @since 1.0.0
	 * @access public
	*/
	public function customersList(){
		global $wpdb;
		
		$query 			= "SELECT * FROM {$wpdb->prefix}bmify_customers";
		
		$Querify  		= new Querify( $query, 'customer' );
		$customers      = $Querify->getData( $this->per_page ); // customers per page
		

		$html = '<div class="bookmify_be_list customer_list">';
		if(count($customers->data) == 0){
			$html .= Helper::bookmifyBeNoItems();
		}
		for($i = 0; $i < count( $customers->data ); $i++){
			
			$opened 			= '';
			$customerID 		= $customers->data[$i]->id;
			$customerFirstName 	= $customers->data[$i]->first_name;
			$customerLastName 	= $customers->data[$i]->last_name;
			$customerEmail 		= $customers->data[$i]->email;
			$customerPhone 		= $customers->data[$i]->phone;
			
			// FIELD MAIN HTML
			$html .= '<div class="bookmify_be_list_item customer_item" data-entity-id="'.$customerID.'">
						<div class="bookmify_be_list_item_in">
							<div class="bookmify_be_list_item_header">
								<div class="header_in">
									<div class="info_holder">
										<div class="info_in">
											<span class="customer_title">
												<span class="c_top">'.$customerFirstName.' '.$customerLastName.'</span>
												<span class="c_bottom">'.$customerEmail.'</span>
											</span>
											<span class="customer_email">'.$customerEmail.'</span>
											<span class="customer_phone">'.$customerPhone.'</span>
										</div>
									</div>

									<div class="buttons_holder">
										<div class="btn_item btn_edit">
											<a href="#" class="bookmify_be_edit" data-entity-id="'.$customerID.'">
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
										<div class="btn_item">
											<a href="#" class="bookmify_be_delete" data-entity-id="'.$customerID.'">
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
					</div>';
			
		}
		
		$html .= '</div>';
		
		$html .= $Querify->getPagination( 1, 'bookmify_be_pagination customer');

		return $html;
	}
	
	
	/**
	 * @since 1.0.0
	 * @access public
	*/
	public function ajaxQueryInsertOrUpdateCustomer(){
		global $wpdb;
		$isAjaxCall 		= false;
		$html 				= '';
		$page 				= 1;
		$demo 				= '';
		if (!empty($_POST['bookmify_data'])) {
			$isAjaxCall = true;
			
			$customer 		= json_decode(stripslashes($_POST['bookmify_data']));
			$c_fname 		= $customer->first_name;
			$c_lname 		= $customer->last_name;
			$c_email 		= $customer->email;
			$c_phone 		= $customer->phone;
			$c_birthday 	= $customer->birthday;
			$c_country 		= $customer->country;
			$c_state	 	= $customer->state;
			$c_city	 		= $customer->city;
			$c_address	 	= $customer->address;
			$c_postcode 	= $customer->postcode;
			$c_info 		= $customer->info;
			
			$c_wp_user_id	= $customer->wp_user_id;
			$demoWPID		= $customer->wp_user_id;
			

			

			
			if($_POST['bookmify_update'] == 1){
				
				$c_id 			= $customer->id;
				
				// We need to detect if customer with given email is in database or not
				$c_email		= esc_sql($c_email);		
				$c_id			= esc_sql($c_id);		
				$count			= $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->prefix}bmify_customers WHERE email='".$c_email."' AND id <> '".$c_id."'" );
				
				if($c_wp_user_id == 'n' && $demo == ''){
					$c_wp_user_id   = $this->addWPUser($customer, $c_id);
				}
				if($demo != ''){
					$c_wp_user_id 	= 0;
				}
				
				if($count == 0){
					// UPDATE
					$wpdb->query($wpdb->prepare( "UPDATE {$wpdb->prefix}bmify_customers SET wp_user_id=%d, first_name=%s, last_name=%s, email=%s, phone=%s, birthday=%s, country=%s, state=%s, city=%s, address=%s, post_code=%d, info=%s WHERE id=%d", $c_wp_user_id, $c_fname, $c_lname, $c_email, $c_phone, $c_birthday, $c_country, $c_state, $c_city, $c_address, $c_postcode, $c_info, $c_id));
				}

			}else{
				
				// We need to detect if customer with given email is in database or not		
				$c_email		= esc_sql($c_email);
				$count			= $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->prefix}bmify_customers WHERE email='".$c_email."'" );
				
				
				// DETECT THIS EMAIL EXIST IN DATABASE OR NOT
				if($count == 0){
					
					
					// INSERT (Best Practice)
					$wpdb->query($wpdb->prepare( "INSERT INTO {$wpdb->prefix}bmify_customers ( first_name, last_name, email, phone, birthday, country, state, city, address, post_code, info ) VALUES ( %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s )", $c_fname, $c_lname, $c_email, $c_phone, $c_birthday, $c_country, $c_state, $c_city, $c_address, $c_postcode, $c_info ));
					
					
					// get new customer id
					$query 			= "SELECT id FROM {$wpdb->prefix}bmify_customers ORDER BY id DESC LIMIT 1;";
					$results 		= $wpdb->get_results( $query, OBJECT  );
					$customerID 	= $results[0]->id;
					
					
					
					
					if($c_wp_user_id == 'n' && $demo == ''){
						$c_wp_user_id   = $this->addWPUser($customer, $customerID);
					}
					if($demo != ''){
						$c_wp_user_id 	= 0;
					}
					// update customer: add wordpress user id
					$wpdb->query($wpdb->prepare( "UPDATE {$wpdb->prefix}bmify_customers SET wp_user_id=%d WHERE id=%d", $c_wp_user_id, $customerID));
				}
			
			}
			$page 			= 1;
			$filter 		= array();
			$search_text 	= '';

			if (!empty($_POST['bookmify_page'])) {
				$page 			= $_POST['bookmify_page'];
			}
			if (!empty($_POST['bookmify_search'])) {
				$search_text 	= $_POST['bookmify_search'];
			}
			if (!empty($_POST['bookmify_order'])) {
				$order 			= $_POST['bookmify_order'];
			}
			if($search_text != ''){ 
				$filter['search'] = $search_text;
			}
			

			// SELECT
			$query 			= "SELECT * FROM {$wpdb->prefix}bmify_customers";
			$Querify  		= new Querify( $query, 'customer' );
			$customers		= $Querify->getData( $this->per_page, $page, $filter, $order );


			$html = '<div class="bookmify_be_list customer_list">';
			if(count($customers->data) == 0){
				$html .= Helper::bookmifyBeNoItems();
			}
			for( $i = 0; $i < count( $customers->data ); $i++ ){
				$customerID 		= $customers->data[$i]->id;
				$customerFirstName 	= $customers->data[$i]->first_name;
				$customerLastName 	= $customers->data[$i]->last_name;
				$customerEmail 		= $customers->data[$i]->email;
				$customerPhone 		= $customers->data[$i]->phone;
				
				$html .= '<div class="bookmify_be_list_item bookmify_be_animated customer_item" data-entity-id="'.$customerID.'">
							<div class="bookmify_be_list_item_in">
								<div class="bookmify_be_list_item_header">
									<div class="header_in">
										<div class="info_holder">
											<div class="info_in">
												<span class="customer_title">
													<span class="c_top">'.$customerFirstName.' '.$customerLastName.'</span>
													<span class="c_bottom">'.$customerEmail.'</span>
												</span>
												<span class="customer_email">'.$customerEmail.'</span>
												<span class="customer_phone">'.$customerPhone.'</span>
											</div>
										</div>

										<div class="buttons_holder">
											<div class="btn_item btn_edit">
												<a href="#" class="bookmify_be_edit"  data-entity-id="'.$customerID.'">
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
											<div class="btn_item">
												<a href="#" class="bookmify_be_delete" data-entity-id="'.$customerID.'">
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
						</div>';
			}
			$html .= '</div>';
			$html .= $Querify->getPagination( 1, 'bookmify_be_pagination customer');
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
			'number'				=> $customer,
			'itemsCount'			=> Helper::bookmifyItemsCount('customers'),
			'demo_check'			=> $demo,
		);

		if ( true === $isAjaxCall ){die(json_encode($buffyArray));} 
		else{return json_encode($buffyArray);}

	}
	
	
	/**
	 * @since 1.0.0
	 * @access public
	*/
	public function ajaxQueryEditCustomer(){
		global $wpdb;
		$isAjaxCall = false;
		$html = '';
		
		if (!empty($_POST['bookmify_data'])) {
			$isAjaxCall 				= true;
			$id 						= $_POST['bookmify_data'];

			// SELECT
			$id							= esc_sql($id);
			$query 						= "SELECT * FROM {$wpdb->prefix}bmify_customers WHERE id=".$id;
			$customers 					= $wpdb->get_results( $query, OBJECT  );
			
			$phoneAsRequired 			= get_option('bookmify_be_phone_as_required', '');
			$phoneRField				= '';
			$phoneRStar					= '';
			if($phoneAsRequired == 'on'){
				$phoneRField			= 'required_field';
				$phoneRStar				= '<span>*</span>';
			}
			
			foreach($customers as $customer){
				
				$wpUserID				= $customer->wp_user_id;
				if(Helper::bookmifyDoesWPUserExist($wpUserID)){
					$wpUserName			= Helper::bookmifyWPUserNamebyID($wpUserID);
				}else{
					$wpUserID 			= '';
					$wpUserName 		= '';
					$wpdb->query($wpdb->prepare( "UPDATE {$wpdb->prefix}bmify_customers SET wp_user_id=%s WHERE id=%d", '', $id));
				}
				
				
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

				
				$html .= '<div class="bookmify_be_popup_form_wrap" data-entity-id="'.$customer->id.'">
							'.HelperCustomers::allNanoInOne($customer->id).'
							<div class="bookmify_be_popup_form_position_fixer">
								<div class="bookmify_be_popup_form_bg">
									<div class="bookmify_be_popup_form">

										<div class="bookmify_be_popup_form_header">
											<h3>'.esc_html__('Edit Customer','bookmify').'</h3>
											<span class="closer"></span>
										</div>

										<div class="bookmify_be_popup_form_content">
											<div class="bookmify_be_popup_form_content_in">

												<div class="bookmify_be_popup_form_fields">

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
															<label><span class="title">'.esc_html__('Phone Number','bookmify').$phoneRStar.'</span></label>
															<input class="customer_phone '.$phoneRField.'" type="tel" value="'.$customer->phone.'" />
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
															<input type="text" name="wp_user" placeholder="'.esc_attr__('Select from WP users','bookmify').'" readonly value="'.$wpUserName.'">
															<input type="hidden" name="wp_user_id" value="'.$wpUserID.'">
															<span class="bot_btn"><span></span><img class="bookmify_be_svg" src="'.BOOKMIFY_ASSETS_URL.'img/down.svg" alt="" /></span>
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

													<div class="input_wrap_row">
														<div class="input_wrap info_holder">
															<label><span class="title">'.esc_html__('Info','bookmify').'</span></label>
															<textarea class="customer_info" placeholder="'.esc_attr__('Some info for internal usage','bookmify').'">'.$customer->info.'</textarea>
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
			'bookmify_be_id' 		=> $id
			
		);

		if ( true === $isAjaxCall ){die(json_encode($buffyArray));} 
		else{return json_encode($buffyArray);}

	}
	
	
	/*
	 * @since 1.0.0
	 * @access private
	*/
	
	
	
	public function ajaxFilterCustomerList(){
		global $wpdb;
		$isAjaxCall 	= false;
		$html 			= '';
		$page 			= 1;
		$filter 		= array();
		$search_text 	= '';
		
		if (!empty($_POST['bookmify_page'])) {
			$isAjaxCall 	= true;
			$page 			= $_POST['bookmify_page'];
			$search_text 	= $_POST['bookmify_search'];
			$order 			= $_POST['bookmify_order'];
			
			if($search_text != ''){ 
				$filter['search'] = $search_text;
			}

			// SELECT
			$query 			= "SELECT * FROM {$wpdb->prefix}bmify_customers";
			$Querify  		= new Querify( $query, 'customer' );
			$customers		= $Querify->getData( $this->per_page, $page, $filter, $order );


			$html = '<div class="bookmify_be_list customer_list">';
			if(count($customers->data) == 0){
				$html .= Helper::bookmifyBeNoItems();
			}
			for( $i = 0; $i < count( $customers->data ); $i++ ){
				$customerID 		= $customers->data[$i]->id;
				$customerFirstName 	= $customers->data[$i]->first_name;
				$customerLastName 	= $customers->data[$i]->last_name;
				$customerEmail 		= $customers->data[$i]->email;
				$customerPhone 		= $customers->data[$i]->phone;
				$html .= '<div class="bookmify_be_list_item bookmify_be_animated customer_item" data-entity-id="'.$customerID.'">
							<div class="bookmify_be_list_item_in">
								<div class="bookmify_be_list_item_header">
									<div class="header_in">
										<div class="info_holder">
											<div class="info_in">
												<span class="customer_title">
													<span class="c_top">'.$customerFirstName.' '.$customerLastName.'</span>
													<span class="c_bottom">'.$customerEmail.'</span>
												</span>
												<span class="customer_email">'.$customerEmail.'</span>
												<span class="customer_phone">'.$customerPhone.'</span>
											</div>
										</div>

										<div class="buttons_holder">
											<div class="btn_item btn_edit">
												<a href="#" class="bookmify_be_edit"  data-entity-id="'.$customerID.'">
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
											<div class="btn_item">
												<a href="#" class="bookmify_be_delete" data-entity-id="'.$customerID.'">
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
						</div>';
			}
			$html .= '</div>';
			$html .= $Querify->getPagination( 1, 'bookmify_be_pagination customer');
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
	 * @access public
	*/
	public function ajaxQueryDeleteCustomer(){
		global $wpdb;
		$customerID 		= '';
		$isAjaxCall			= false;
		
		if (!empty($_POST['bookmify_data'])) {
			$isAjaxCall		= true;
			$customerID 	= $_POST['bookmify_data'];
			
			$now			= HelperTime::getCurrentDateTime();
			$customerID		= esc_sql($customerID);
			$count 			= $wpdb->get_var( "SELECT COUNT(*) 
								
							FROM 	   	   {$wpdb->prefix}bmify_customer_appointments ca 
								LEFT JOIN  {$wpdb->prefix}bmify_appointments a  			ON a.id = ca.appointment_id
							
							WHERE ca.customer_id=".$customerID." AND end_date > '".$now."'");
			if($count == 0){
				HelperCustomers::deleteCustomerAppointments($customerID);
			}
			
			$buffyArray = array(
				'number'				=> Helper::bookmifyItemsCount('customers'),
				'count'					=> $count
			);

			if ( true === $isAjaxCall ){die(json_encode($buffyArray));} 
			else{return json_encode($buffyArray);}
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
	

	/**
	 * @since 1.0.0
	 * @access protected
	*/
	protected function get_page_title() {
		return esc_html__( 'Customers', 'bookmify' );
	}
}
	

