<?php
namespace Bookmify;

use Bookmify\Helper;
use Bookmify\HelperAdmin;
use Bookmify\NotificationManagement;
use Bookmify\HelperNotifications;
use Bookmify\Twilio;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Notifications{
	
	const PAGE_ID = 'bookmify_notifications';
	protected $existing_notifications;
	protected $existing_sms_notifications;
	
	/**
	 * @since 1.0.0
	 * @access public
	*/
	public function __construct() 
	{
		add_action( 'admin_menu', [ $this, 'register_admin_menu' ], 20 );
		add_action( 'wp_ajax_querySaveNotification', [$this, 'querySaveNotification'] );
		add_action( 'wp_ajax_queryChangeNotificationStatus', [$this, 'queryChangeNotificationStatus'] );
		add_action( 'wp_ajax_queryEmailTest', [$this, 'queryEmailTest'] );
		$this->load_existing_notifications();
	}
	

	/**
	 * @since 1.0.0
	 * @access public
	*/
	public function register_admin_menu() 
	{
		add_submenu_page(
			BOOKMIFY_MENU,
			esc_html__( 'Notifications', 'bookmify' ),
			esc_html__( 'Notifications', 'bookmify' ),
			'bookmify_be_read_notifications',
			self::PAGE_ID,
			[ $this, 'display_notifications_page' ]
		);
	}
	
	public function display_notifications_page() 
	{
		global $wpdb;

		echo HelperAdmin::bookmifyAdminContentStart();
		?>
		
		<div class="bookmify_be_content_wrap bookmify_be_notifications_page">
			<div class="bookmify_be_page_title">
				<h3><?php echo esc_html($this->get_page_title()); ?></h3>
			</div>
			<div class="bookmify_be_page_content">
				

				<div class="bookmify_be_flex_box bookmify_be_notification_panel">
					<div class="bookmify_be_col main_panel">
						
						<div class="bookmify_be_not_info">
							<div class="bookmify_be_not_info_in">
								<span class="info_icon">
									<img class="bookmify_be_svg" src="<?php echo BOOKMIFY_ASSETS_URL;?>/img/info.svg" alt="" />
								</span>
								<p class="info_text">
									<span class="top_info"><?php esc_html_e('To send scheduled notifications please add the following command line in your cron:','bookmify');?></span>
									<span class="bottom_info">wget -q -O - <?php echo BOOKMIFY_CRON_URL;?></span>
								</p>
							</div>
						</div>
						
						<div class="bookmify_be_notifications">
							<div class="bookmify_be_notifications_list">

								<div class="bookmify_be_tab_wrap">
									<div class="bookmify_be_link_tabs">
										<ul>
											<li class="active"><a href="#" class="bookmify_be_tab_link"><?php esc_html_e('Email to Customer', 'bookmify'); ?></a></li>
											<li><a href="#" class="bookmify_be_tab_link"><?php esc_html_e('Email to Employee', 'bookmify'); ?></a></li>
											<li><a href="#" class="bookmify_be_tab_link"><?php esc_html_e('SMS to Customer', 'bookmify'); ?></a></li>
											<li><a href="#" class="bookmify_be_tab_link"><?php esc_html_e('SMS to Employee', 'bookmify'); ?></a></li>
										</ul>
									</div>
									<div class="bookmify_be_content_tabs">
										<div class="bookmify_be_tab_pane active">
											<?php echo HelperNotifications::notificationsListForCustomer();?>
										</div>
										<div class="bookmify_be_tab_pane">
											<?php echo HelperNotifications::notificationsListForEmployee();?>
										</div>
										<div class="bookmify_be_tab_pane">
											<?php echo HelperNotifications::notificationsListForCustomer('sms');?>
										</div>
										<div class="bookmify_be_tab_pane">
											<?php echo HelperNotifications::notificationsListForEmployee('sms');?>
										</div>
									</div>
								</div>

							</div>
	
						</div>
					</div>
					<div class="bookmify_be_col code_panel">
						
						
<!--						<a href="#" class="send_sms">Send SMS</a> <br /><br />-->
						
						<h3><?php esc_html_e('Shortcode List', 'bookmify'); ?></h3>
						<div class="bookmify_be_note_code_list">
							<?php 
								$array = Helper::bookmifyNotificationPlaceholders();
								$list = '<ul>';
								foreach($array as $key => $arr){
									$list .= '<li><div><p>'.$arr['ct'].'</p><span>{{'.$key.'}}</span></div></li>';
								}
								$list .= '</ul>';
								echo wp_kses_post($list);
								echo HelperNotifications::hoursPopup();
								echo HelperNotifications::xTimeBeforeAppointmentPopup();
							?>
						</div>
					</div>
				</div>
				
				
			</div>
		</div>
		<?php echo HelperAdmin::bookmifyAdminContentEnd();
		
	}
	

	
	
	/**
	 * @since 1.0.0
	 * @access public
	*/
	public function querySaveNotification(){
		global $wpdb;
		
		$isAjaxCall = false;
		$n_id 		= $n_subject = $n_text = '';
		$checkTime 	= '';
		$xBefore 	= '';
		$type 		= '';
		if(!empty($_POST['bookmify_data'])){
			$isAjaxCall = true;

			$notifications = json_decode(stripslashes($_POST['bookmify_data']));
			
			foreach($notifications as $notification){
				
				$n_id 			= $notification->id;
				$n_subject 		= $notification->subject;
				$n_text 		= $notification->text;
			}
		}
		if(isset($_POST['checkTime'])){
			$checkTime = date('H:i:s', strtotime($_POST['checkTime']));
		}
		if(isset($_POST['xBefore'])){
			$xBefore = $_POST['xBefore'];
		}
		if(isset($_POST['type'])){
			$type = $_POST['type'];
		}
		// update notification table
		if($type == 'customer_reminder_prev_day'){
			$wpdb->query($wpdb->prepare( "UPDATE {$wpdb->prefix}bmify_notifications SET subject=%s, message=%s, check_time=%s WHERE id=%d", $n_subject, $n_text, $checkTime, $n_id));
		}else if($type == 'customer_reminder_x_before'){
			$wpdb->query($wpdb->prepare( "UPDATE {$wpdb->prefix}bmify_notifications SET subject=%s, message=%s, time_interval=%d WHERE id=%d", $n_subject, $n_text, $xBefore, $n_id));
		}else{
			$wpdb->query($wpdb->prepare( "UPDATE {$wpdb->prefix}bmify_notifications SET subject=%s, message=%s WHERE id=%d", $n_subject, $n_text, $n_id));
		}
		
	}
	
	
	/**
	 * @since 1.0.0
	 * @access public
	*/
	public function queryChangeNotificationStatus(){
		global $wpdb;
		
		$isAjaxCall = false;
		$n_id = $n_status = '';
	
		if(!empty($_POST['bookmify_data'])){
			$isAjaxCall = true;

			$notifications = json_decode(stripslashes($_POST['bookmify_data']));
			
			foreach($notifications as $notification){
				$n_id 			= $notification->id;
				$n_status 		= $notification->status;
			}
		}
		
		// update notification table
		$wpdb->query($wpdb->prepare( "UPDATE {$wpdb->prefix}bmify_notifications SET status=%s WHERE id=%d", $n_status, $n_id));
	}
	
	
	/**
	 * @since 1.0.0
	 * @access private
	*/
	private function load_existing_notifications()
	{
		global $wpdb;
		
		$numberOfNotification 		= $this->numberOfNotification();
		$numberOfSMSNotification 	= $this->numberOfSMSNotification();
		
		$this->existing_notifications = array(
			
			// customer messages & email platform
			array(
				'platform' 		=> 'email',
				'status'  		=> '1',
                'type'    		=> 'customer_approved_message',
                'subject' 		=> esc_html__('Appointment Approved', 'bookmify'),
                'message' 		=> wpautop( esc_html__( "Dear {{customer_full_name}}.\n\nThis is a confirmation that you have booked {{service_name}}.\n\nWe are waiting you at {{company_address}} on {{appointment_date}} at {{appointment_start_time}}.\n\nThank you for choosing our company.\n\n{{company_name}}\n{{company_phone}}\n{{company_website}}", 'bookmify' ) ),
				'to_customer' 	=> '1',
            ),
			array(
				'platform' 		=> 'email',
				'status'  		=> '1',
                'type'    		=> 'customer_canceled_message',
                'subject' 		=> esc_html__('Appointment Canceled', 'bookmify'),
                'message' 		=> wpautop( esc_html__( "Dear {{customer_full_name}}.\n\nYou have canceled your booking of {{service_name}} on {{appointment_date}} at {{appointment_start_time}}.\n\nThank you for choosing our company.\n\n{{company_name}}\n{{company_phone}}\n{{company_website}}", 'bookmify' ) ),
				'to_customer' 	=> '1',
            ),
            array(
				'platform' 		=> 'email',
				'status'  		=> '1',
                'type'    		=> 'customer_pending_message',
                'subject' 		=> esc_html__('Appointment Pending', 'bookmify'),
                'message' 		=> wpautop( esc_html__( "Dear {{customer_full_name}}.\n\n The {{service_name}} appointment with {{employee_full_name}} at {{company_address}}, scheduled for {{appointment_date}} at {{appointment_start_time}} is waiting for a confirmation.\n\n Thank you for choosing our company.\n\n {{company_name}}\n{{company_phone}}\n{{company_website}}", 'bookmify' ) ),
				'to_customer' 	=> '1',
            ),
			array(
				'platform' 		=> 'email',
				'status'  		=> '1',
                'type'    		=> 'customer_rejected_message',
                'subject' 		=> esc_html__('Appointment Rejected', 'bookmify'),
                'message' 		=> wpautop( esc_html__( "Dear {{customer_full_name}}.\n\nYour booking of {{service_name}} on {{appointment_date}} at {{appointment_start_time}} has been rejected.\n\nThank you for choosing our company.\n\n{{company_name}}\n{{company_phone}}\n{{company_website}}", 'bookmify' ) ),
				'to_customer' 	=> '1',
            ),
			array(
				'platform' 		=> 'email',
				'status'  		=> '1',
                'type'    		=> 'customer_rescheduled_message',
                'subject' 		=> esc_html__('Appointment Rescheduled', 'bookmify'),
                'message' 		=> wpautop( esc_html__( "Dear {{customer_full_name}}.\n\nWere changed the details of your booking on {{service_name}} at {{location_name}} with {{employee_full_name}}. The booking has been rescheduled to {{appointment_date}} at {{appointment_start_time}}.\n\nThank you for choosing our company.\n\n{{company_name}}\n{{company_phone}}\n{{company_website}}", 'bookmify' ) ),
				'to_customer' 	=> '1',
            ),
			array(
				'platform' 		=> 'email',
				'status'  		=> '1',
                'type'    		=> 'customer_reminder_prev_day',
                'subject' 		=> esc_html__('Reminder Day Before Appointment', 'bookmify'),
                'message' 		=> wpautop( esc_html__( "Dear {{customer_full_name}}.\n\nThis is a reminder that you have booked {{service_name}} on {{appointment_date}} at {{appointment_start_time}}.\n\nWe look forward to seeing you!\n\nKind regards.\n\n{{company_name}}\n{{company_phone}}\n{{company_website}}", 'bookmify' ) ),
				'to_customer' 	=> '1',
				'cron' 			=> '1',
				'check_time' 	=> '20:00:00',
            ),
			array(
				'platform' 		=> 'email',
				'status'  		=> '1',
                'type'    		=> 'customer_reminder_x_before',
                'subject' 		=> esc_html__('Reminder Time Before Appointment', 'bookmify'),
                'message' 		=> wpautop( esc_html__( "Dear {{customer_full_name}}.\n\nThis is a reminder that you have booked {{service_name}} on {{appointment_date}} at {{appointment_start_time}}.\n\nWe look forward to seeing you!\n\nKind regards.\n\n{{company_name}}\n{{company_phone}}\n{{company_website}}", 'bookmify' ) ),
				'to_customer' 	=> '1',
				'cron' 			=> '1',
				'time_interval' => '2',
            ),
			array(
				'platform' 		=> 'email',
				'status'  		=> '1',
                'type'    		=> 'customer_login_message',
                'subject' 		=> esc_html__('Thank you for registering!', 'bookmify'),
                'message' 		=> wpautop( esc_html__( "Hello {{customer_full_name}}.\n\nThanks for registering at {{site_address}}\n\nYou can now login to manage your account and appointments using the following credentials:\nuser: {{new_username}}\npassword: {{new_password}}\n\nThanks.", 'bookmify' ) ),
				'to_customer' 	=> '1',
            ),
			
			// employee messages & email platform
            array(
				'platform' 		=> 'email',
				'status'  		=> '1',
                'type'    		=> 'employee_approved_message',
                'subject' 		=> esc_html__( 'Appointment Approved', 'bookmify' ),
                'message' 		=> wpautop( esc_html__( "Hello.\n\nYou have a new booking.\n\nService: {{service_name}}\nDate: {{appointment_date}}\nTime: {{appointment_start_time}}\n\nCustomer name: {{customer_full_name}}\nCustomer email: {{customer_email}}\nCustomer phone: {{customer_phone}}", 'bookmify' ) ),
				'to_employee' 	=> '1',
            ),
            array(
				'platform' 		=> 'email',
				'status'  		=> '1',
                'type'    		=> 'employee_canceled_message',
                'subject' 		=> esc_html__( 'Appointment Canceled', 'bookmify' ),
                'message' 		=> wpautop( esc_html__( "Hello.\n\nThe following booking has been canceled.\n\nService: {{service_name}}\nDate: {{appointment_date}}\nTime: {{appointment_start_time}}\n\nCustomer name: {{customer_full_name}}\nCustomer email: {{customer_email}}\nCustomer phone: {{customer_phone}}", 'bookmify' ) ),
				'to_employee' 	=> '1',
            ),
            array(
				'platform' 		=> 'email',
				'status'  		=> '1',
                'type'    		=> 'employee_pending_message',
                'subject' 		=> esc_html__( 'Appointment Pending', 'bookmify' ),
                'message' 		=> wpautop( esc_html__( "Hello.\n\n You have a new booking.\n\nService: {{service_name}}\nDate: {{appointment_date}}\nTime: {{appointment_start_time}}\n\nCustomer name: {{customer_full_name}}\nCustomer email: {{customer_email}}\nCustomer phone: {{customer_phone}}", 'bookmify' ) ),
				'to_employee' 	=> '1',
            ),
            array(
				'platform' 		=> 'email',
				'status'  		=> '1',
                'type'    		=> 'employee_rejected_message',
                'subject' 		=> esc_html__( 'Appointment Rejected', 'bookmify' ),
                'message' 		=> wpautop( esc_html__( "Hello.\n\nThe following booking has been rejected.\n\nService: {{service_name}}\nDate: {{appointment_date}}\nTime: {{appointment_start_time}}\n\nCustomer name: {{customer_full_name}}\nCustomer email: {{customer_email}}\nCustomer phone: {{customer_phone}}", 'bookmify' ) ),
				'to_employee' 	=> '1',
            ),
			array(
				'platform' 		=> 'email',
				'status'  		=> '1',
                'type'    		=> 'employee_rescheduled_message',
                'subject' 		=> esc_html__('Appointment Rescheduled', 'bookmify'),
                'message' 		=> wpautop( esc_html__( "Hello.\n\nWere changed the details of your booking on {{service_name}} at {{location_name}}. The booking has been rescheduled to {{appointment_date}} at {{appointment_start_time}}.\n\n{{company_name}}\n{{company_phone}}\n{{company_website}}", 'bookmify' ) ),
				'to_employee' 	=> '1',
            ),
			array(
				'platform' 		=> 'email',
				'status'  		=> '1',
                'type'    		=> 'employee_reminder_prev_day',
                'subject' 		=> esc_html__('Reminder Day Before Appointment', 'bookmify'),
                'message' 		=> wpautop( esc_html__( "Reminder:  {{customer_full_name}} has booked an appointment.\n\nService: {{service_name}}\nDate: {{appointment_date}}\nTime: {{appointment_start_time}}\n\nCustomer name: {{customer_full_name}}\nCustomer email: {{customer_email}}\nCustomer phone: {{customer_phone}}", 'bookmify' ) ),
				'to_employee' 	=> '1',
				'cron' 			=> '1',
				'check_time' 	=> '20:00:00',
            ),
			array(
				'platform' 		=> 'email',
				'status'  		=> '1',
                'type'    		=> 'employee_reminder_x_before',
                'subject' 		=> esc_html__('Reminder Time Before Appointment', 'bookmify'),
                'message' 		=> wpautop( esc_html__( "Dear {{employee_full_name}}.\n\nThis is a reminder that you have booked {{service_name}} on {{appointment_date}} at {{appointment_start_time}}.\n\nWe look forward to seeing you!\n\nKind regards.\n\n{{company_name}}\n{{company_phone}}\n{{company_website}}", 'bookmify' ) ),
				'to_employee' 	=> '1',
				'cron' 			=> '1',
				'time_interval' => '2',
            ),
			array(
				'platform' 		=> 'email',
				'status'  		=> '1',
                'type'    		=> 'employee_login_message',
                'subject' 		=> esc_html__('Thank you for registering!', 'bookmify'),
                'message' 		=> wpautop( esc_html__( "Hello {{employee_full_name}}.\n\nThanks for registering at {{site_address}}\n\nYou can now login to manage your account and appointments using the following credentials:\nuser: {{new_username}}\npassword: {{new_password}}\n\nThanks.", 'bookmify' ) ),
				'to_employee' 	=> '1',
            ),
		);
		
		/* since bookmify v1.2.0 */
		$this->existing_sms_notifications = array(
			
			// customer messages & sms platform
			array(
				'platform' 		=> 'sms',
				'status'  		=> '0',
                'type'    		=> 'customer_approved_message',
                'subject' 		=> esc_html__('Appointment Approved', 'bookmify'),
                'message' 		=> wpautop( esc_html__( "Dear {{customer_full_name}}.\n\nYour appointment has been approved.\n\nService: {{service_name}}\n\nDate: {{appointment_date}} at {{appointment_start_time}}.\n\n{{company_name}}", 'bookmify' ) ),
				'to_customer' 	=> '1',
            ),
			array(
				'platform' 		=> 'sms',
				'status'  		=> '0',
                'type'    		=> 'customer_canceled_message',
                'subject' 		=> esc_html__('Appointment Canceled', 'bookmify'),
                'message' 		=> wpautop( esc_html__( "Dear {{customer_full_name}}.\n\nYour appointment has been canceled.\n\nService: {{service_name}}\n\nDate: {{appointment_date}} at {{appointment_start_time}}.\n\n{{company_name}}", 'bookmify' ) ),
				'to_customer' 	=> '1',
            ),
            array(
				'platform' 		=> 'sms',
				'status'  		=> '0',
                'type'    		=> 'customer_pending_message',
                'subject' 		=> esc_html__('Appointment Pending', 'bookmify'),
                'message' 		=> wpautop( esc_html__( "Dear {{customer_full_name}}.\n\nYour appointment waiting for a confirmation.\n\nService: {{service_name}}\n\nDate: {{appointment_date}} at {{appointment_start_time}}.\n\n{{company_name}}", 'bookmify' ) ),
				'to_customer' 	=> '1',
            ),
			array(
				'platform' 		=> 'sms',
				'status'  		=> '0',
                'type'    		=> 'customer_rejected_message',
                'subject' 		=> esc_html__('Appointment Rejected', 'bookmify'),
                'message' 		=> wpautop( esc_html__( "Dear {{customer_full_name}}.\n\nYour appointment has been rejected.\n\nService:{{service_name}}\n\nDate: {{appointment_date}} at {{appointment_start_time}}.\n\n{{company_name}}", 'bookmify' ) ),
				'to_customer' 	=> '1',
            ),
			array(
				'platform' 		=> 'sms',
				'status'  		=> '0',
                'type'    		=> 'customer_rescheduled_message',
                'subject' 		=> esc_html__('Appointment Rescheduled', 'bookmify'),
                'message' 		=> wpautop( esc_html__( "Dear {{customer_full_name}}.\n\nYour appointment has been rescheduled.\n\nService: {{service_name}}\n\nDate: {{appointment_date}} at {{appointment_start_time}}.\n\n{{company_name}}", 'bookmify' ) ),
				'to_customer' 	=> '1',
            ),
			array(
				'platform' 		=> 'sms',
				'status'  		=> '0',
                'type'    		=> 'customer_reminder_prev_day',
                'subject' 		=> esc_html__('Reminder Day Before Appointment', 'bookmify'),
                'message' 		=> wpautop( esc_html__( "Dear {{customer_full_name}}.\n\nThis is a reminder that you have booked {{service_name}} on {{appointment_date}} at {{appointment_start_time}}.\n\n{{company_name}}", 'bookmify' ) ),
				'to_customer' 	=> '1',
				'cron' 			=> '1',
				'check_time' 	=> '20:00:00',
            ),
			array(
				'platform' 		=> 'sms',
				'status'  		=> '0',
                'type'    		=> 'customer_reminder_x_before',
                'subject' 		=> esc_html__('Reminder Time Before Appointment', 'bookmify'),
                'message' 		=> wpautop( esc_html__( "Dear {{customer_full_name}}.\n\nThis is a reminder that you have booked {{service_name}} on {{appointment_date}} at {{appointment_start_time}}.\n\n{{company_name}}", 'bookmify' ) ),
				'to_customer' 	=> '1',
				'cron' 			=> '1',
				'time_interval' => '2',
            ),
			array(
				'platform' 		=> 'sms',
				'status'  		=> '0',
                'type'    		=> 'customer_login_message',
                'subject' 		=> esc_html__('Thank you for registering!', 'bookmify'),
                'message' 		=> wpautop( esc_html__( "Hello {{customer_full_name}}.\n\nThanks for registering at {{site_address}}\n\nYou can now login to manage your account and appointments using the following credentials:\nuser: {{new_username}}\npassword: {{new_password}}\n\nThanks.", 'bookmify' ) ),
				'to_customer' 	=> '1',
            ),
			
			// employee messages & sms platform
            array(
				'platform' 		=> 'sms',
				'status'  		=> '0',
                'type'    		=> 'employee_approved_message',
                'subject' 		=> esc_html__( 'Appointment Approved', 'bookmify' ),
                'message' 		=> wpautop( esc_html__( "Hello.\n\nYou have a new booking.\n\nService: {{service_name}}\nDate: {{appointment_date}}\nTime: {{appointment_start_time}}", 'bookmify' ) ),
				'to_employee' 	=> '1',
            ),
            array(
				'platform' 		=> 'sms',
				'status'  		=> '0',
                'type'    		=> 'employee_canceled_message',
                'subject' 		=> esc_html__( 'Appointment Canceled', 'bookmify' ),
                'message' 		=> wpautop( esc_html__( "Hello.\n\nThe following booking has been canceled.\n\nService: {{service_name}}\nDate: {{appointment_date}}\nTime: {{appointment_start_time}}", 'bookmify' ) ),
				'to_employee' 	=> '1',
            ),
            array(
				'platform' 		=> 'sms',
				'status'  		=> '0',
                'type'    		=> 'employee_pending_message',
                'subject' 		=> esc_html__( 'Appointment Pending', 'bookmify' ),
                'message' 		=> wpautop( esc_html__( "Hello.\n\n You have a new booking.\n\nService: {{service_name}}\nDate: {{appointment_date}}\nTime: {{appointment_start_time}}", 'bookmify' ) ),
				'to_employee' 	=> '1',
            ),
            array(
				'platform' 		=> 'sms',
				'status'  		=> '0',
                'type'    		=> 'employee_rejected_message',
                'subject' 		=> esc_html__( 'Appointment Rejected', 'bookmify' ),
                'message' 		=> wpautop( esc_html__( "Hello.\n\nThe following booking has been rejected.\n\nService: {{service_name}}\nDate: {{appointment_date}}\nTime: {{appointment_start_time}}", 'bookmify' ) ),
				'to_employee' 	=> '1',
            ),
			array(
				'platform' 		=> 'sms',
				'status'  		=> '0',
                'type'    		=> 'employee_rescheduled_message',
                'subject' 		=> esc_html__('Appointment Rescheduled', 'bookmify'),
                'message' 		=> wpautop( esc_html__( "Hello.\n\nWere changed the details of your booking on {{service_name}} at {{location_name}}. The booking has been rescheduled to {{appointment_date}} at {{appointment_start_time}}.\n\n", 'bookmify' ) ),
				'to_employee' 	=> '1',
            ),
			array(
				'platform' 		=> 'sms',
				'status'  		=> '0',
                'type'    		=> 'employee_reminder_prev_day',
                'subject' 		=> esc_html__('Reminder Day Before Appointment', 'bookmify'),
                'message' 		=> wpautop( esc_html__( "Reminder:  {{customer_full_name}} has booked an appointment.\n\nService: {{service_name}}\nDate: {{appointment_date}}\nTime: {{appointment_start_time}}", 'bookmify' ) ),
				'to_employee' 	=> '1',
				'cron' 			=> '1',
				'check_time' 	=> '20:00:00',
            ),
			array(
				'platform' 		=> 'sms',
				'status'  		=> '0',
                'type'    		=> 'employee_reminder_x_before',
                'subject' 		=> esc_html__('Reminder Time Before Appointment', 'bookmify'),
                'message' 		=> wpautop( esc_html__( "Dear {{customer_full_name}}.\n\nThis is a reminder that you have booked {{service_name}} on {{appointment_date}} at {{appointment_start_time}}.\n\nWe look forward to seeing you!\n\nKind regards.", 'bookmify' ) ),
				'to_employee' 	=> '1',
				'cron' 			=> '1',
				'time_interval' => '2',
            ),
			array(
				'platform' 		=> 'sms',
				'status'  		=> '0',
                'type'    		=> 'employee_login_message',
                'subject' 		=> esc_html__('Thank you for registering!', 'bookmify'),
                'message' 		=> wpautop( esc_html__( "Hello {{employee_full_name}}.\n\nThanks for registering at {{site_address}}\n\nYou can now login to manage your account and appointments using the following credentials:\nuser: {{new_username}}\npassword: {{new_password}}\n\nThanks.", 'bookmify' ) ),
				'to_employee' 	=> '1',
            ),
		);
		
		// check if notifications already have been added
		if($numberOfNotification == 0){
			// if not, add them 
			foreach($this->existing_notifications as $notifications){
				$this->insert_existing_notifications($notifications);
			}
		}
		
		// since bookmify v1.2.0
		// check if notifications already have been added
		if($numberOfSMSNotification == 0){
			// if not, add them 
			foreach($this->existing_sms_notifications as $notifications){
				$this->insert_existing_notifications($notifications);
			}
		}
		
		
	}
	
	
	/**
	 * @since 1.0.0
	 * @access private
	*/
	private function insert_existing_notifications($n)
	{
		global $wpdb;
		
		$cols    = array();
        $values  = array();
		
		// create custom cols
		foreach($n as $col=>$value){
			$col 		= esc_sql($col);
			$value 		= esc_sql($value);
			$cols[] 	= $col;
			$values[] 	= $value;
		}

		$cols 	= implode( ', ', $cols );
		$values = implode( '", "', $values );
		
		
	
		$wpdb->query(
			sprintf(
				'INSERT INTO %s (%s) VALUES ("%s")',
				$wpdb->prefix.'bmify_notifications',
				$cols, $values
			)
		);
		
	}
	
	
	
	
	/**
	 * @since 1.0.0
	 * @access private
	*/
    private function numberOfNotification()
    {
        global $wpdb;

		$query = "SELECT COUNT(*) FROM {$wpdb->prefix}bmify_notifications WHERE platform='email'";
		$result = $wpdb->get_var( $query );
		
		return $result;
    }
	
	
	/**
	 * @since 1.2.0
	 * @access private
	*/
    private function numberOfSMSNotification()
    {
        global $wpdb;

		$query = "SELECT COUNT(*) FROM {$wpdb->prefix}bmify_notifications WHERE platform='sms'";
		$result = $wpdb->get_var( $query );
		
		return $result;
    }
	
	
	
	
	
	
	/**
	 * @since 1.0.0
	 * @access public
	*/
	public function queryEmailTest(){
		global $wpdb;
		
		$management = new NotificationManagement();
		
		$isAjaxCall = false;
		$n_id = $n_recipient = $n_subject = $n_text = '';
	
		if(!empty($_POST['bookmify_data'])){
			$isAjaxCall 		= true;

			$notifications 		= json_decode(stripslashes($_POST['bookmify_data']));
			
			foreach($notifications as $notification){
				
				$n_id 			= $notification->id;
				$n_recipient	= $notification->recipient;
				$n_subject 		= $notification->subject;
				$n_text 		= $notification->text;
			}
			
			$placeholders 	= $management::demoPlaceholders();
			$n_subject 		= $management::replacePlaceholders($n_subject, $placeholders);
			$n_text 		= $management::replacePlaceholders($n_text, $placeholders);
			
			$management::_sender($n_recipient, $n_subject, $n_text);
			
		}
		
		
		$buffyArray = array(
			'bookmify_be_data' 		=> $n_text
		);


		if ( true === $isAjaxCall ){die(json_encode($buffyArray));} 
		else{return json_encode($buffyArray);}

	}
	
	

	
	/**
	 * @since 1.0.0
	 * @access protected
	*/
	protected function get_page_title() 
	{
		return esc_html__( 'Notifications', 'bookmify' );
	}
	
}