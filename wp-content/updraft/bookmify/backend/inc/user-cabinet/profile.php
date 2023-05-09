<?php
namespace Bookmify;

use Bookmify\HelperAdmin;
use Bookmify\HelperCabinet;
use Bookmify\GoogleCalendarProject;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


class UserProfileCustom{

	const PAGE_ID 		= 'bookmify_user_profile';
	
	private $userSlug;
	private $userID;
	
	/**
	 * @since 1.0.0
	 * @access public
	*/
	public function __construct() {	
		$this->assignValToVar();
		
		add_action( 'admin_menu', [ $this, 'register_admin_menu' ], 20 );
		
		// update employee information
		add_action( 'wp_ajax_ajaxQueryUpdateEmployeeInfo', [$this, 'ajaxQueryUpdateEmployeeInfo'] );
		// delete google data from employee
		add_action( 'wp_ajax_ajaxQueryDeleteGoogleData', [$this, 'ajaxQueryDeleteGoogleData'] );
		// update customer information
		add_action( 'wp_ajax_ajaxQueryUpdateCustomerInfo', [$this, 'ajaxQueryUpdateCustomerInfo'] );
	}
	
	/**
	 * @since 1.0.0
	 * @access public
	*/
	private function assignValToVar(){
		global $wpdb;
		
		
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
				$user 				= esc_sql($user);
				$WPuserID 			= esc_sql($WPuserID);
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
			esc_html__( 'Profile', 'bookmify' ),
			esc_html__( 'Profile', 'bookmify' ),
			'bookmify_be_read_user_profile',
			self::PAGE_ID,
			[ $this, 'display_user_profile_page' ]
		);
		
		remove_submenu_page(BOOKMIFY_MENU, BOOKMIFY_MENU);
	}
	
	/**
	 * @since 1.0.0
	 * @access public
	*/
	public function display_user_profile_page() {
		global $wpdb;

		echo HelperAdmin::bookmifyAdminContentStart('bookmify-user');
		?>
		
		<div class="bookmify_be_content_wrap bookmify_be_profile_page">
			<div class="bookmify_be_page_title">
				<h3><?php echo esc_html($this->get_page_title()); ?></h3>
				<div class="bookmify_be_add_new_item bookmify_be_add_new_payment"></div>
			</div>
			<div class="bookmify_be_page_content">
				
				<div class="bookmify_be_user_profile">
					<?php 
						if(isset($_GET['code'])){
							$google 			= new GoogleCalendarProject();
							$bookmify_google 	= $google->lastStep($_GET['state'], $_GET['code']);
						}
		
					?>
					<?php echo HelperCabinet::getProfile($this->userID,$this->userSlug); ?>
					
				</div>
				
			</div>
			
		</div>
		<?php echo HelperAdmin::bookmifyAdminContentEnd();
		
	}

	
	
	public function ajaxQueryDeleteGoogleData(){
		global $wpdb;
		$html 		= '';
		$error 		= '';
		
		$isAjaxCall = false;
		
		if (!empty($_POST['bookmify_data'])) 
		{
			$isAjaxCall 		= true;
			$ID 				= $_POST['bookmify_data'];
			
			if($ID != $this->userID){
				$error 			= 'warning';
			}
			
			$userID				= $this->userID;
			
			// DELETE
			$wpdb->query($wpdb->prepare( "UPDATE {$wpdb->prefix}bmify_employees SET google_data=%s WHERE id=%d", '', $userID));

			$google 			= new GoogleCalendarProject();
			
			
			$authURL 			= $google->createAuthUrl($userID);
			$authURL 			= filter_var($authURL, FILTER_SANITIZE_URL);
			$authURL			= '<a href='.$authURL.'>'.esc_html__('Authentification', 'bookmify').'</a>';
			$googleTop      	= '<span>'.esc_html__('Google Profile', 'bookmify').'</span>';
			$googleBottom 		= $authURL;
			
			
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
			$googleTop = preg_replace($search, $replace, $googleTop);
			$googleBottom = preg_replace($search, $replace, $googleBottom);


			$buffyArray = array(
				'top_html'			=> $googleTop,
				'bottom_html'		=> $googleBottom,
				'error'				=> $error,
			);
			if ( true === $isAjaxCall ) {die(json_encode($buffyArray));} 
			else {return json_encode($buffyArray);}
		}
	}
	
	public function ajaxQueryUpdateEmployeeInfo(){
		global $wpdb;
		$isAjaxCall 			= false;
		
		
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
			$employeeDesc 		= $employee->desc;

			$wpdb->query($wpdb->prepare( "UPDATE {$wpdb->prefix}bmify_employees SET first_name=%s, last_name=%s, email=%s, phone=%s, attachment_id=%d, info=%s WHERE id=%d", $employeeFirstName, $employeeLastName, $employeeEmail, $employeePhone, $employeeAttID, $employeeDesc, $employeeID));
			
			$buffyArray = array();
			if ( true === $isAjaxCall ) {die(json_encode($buffyArray));} 
			else {return json_encode($buffyArray);}
		}
	}
	
	
	
	/**
	 * @since 1.0.0
	 * @access public
	*/
	public function ajaxQueryUpdateCustomerInfo(){
		global $wpdb;
		$isAjaxCall = false;
		$error 		= '';
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
			$c_id 			= $customer->id;
			
			// We need to detect if customer with given email is in database or not
			$c_email 		= esc_sql($c_email);
			$c_id 			= esc_sql($c_id);
			$query 			= "SELECT * FROM {$wpdb->prefix}bmify_customers WHERE email='".$c_email."' AND id<>".$c_id;
			$results 		= $wpdb->get_results( $query, OBJECT  );


			if(count($results) == 0){
				$wpdb->query($wpdb->prepare( "UPDATE {$wpdb->prefix}bmify_customers SET first_name=%s, last_name=%s, email=%s, phone=%s, birthday=%s, country=%s, state=%s, city=%s, address=%s, post_code=%d, info=%s WHERE id=%d", $c_fname, $c_lname, $c_email, $c_phone, $c_birthday, $c_country, $c_state, $c_city, $c_address, $c_postcode, $c_info, $c_id));
			}else{
				$error = 'yes';
			}
			
		}
		
		$buffy = '';
		if ( $error != NULL ) {
			$buffy .= $error; 
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
		$html 		= '';
		$WPuserID	= get_current_user_id();
		$userData 	= get_userdata($WPuserID);
		if(in_array('bookmify-employee',$userData->roles)){
			$html 	= esc_html__('Your Profile', 'bookmify');
		}else if(in_array('bookmify-customer',$userData->roles)){
			$html 	= esc_html__('Your Profile', 'bookmify');
		}
		return $html;
	}
}
	

