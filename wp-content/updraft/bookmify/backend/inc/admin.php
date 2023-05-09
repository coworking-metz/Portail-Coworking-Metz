<?php
namespace Bookmify;

use Bookmify\Appointments;
use Bookmify\Taxes;
use Bookmify\Coupons;
use Bookmify\Calendar;
use Bookmify\Locations;
use Bookmify\Employees;
use Bookmify\Services;
use Bookmify\Categories;
use Bookmify\Settings_Dayoff_Query;
use Bookmify\Notifications;
use Bookmify\Customfields;
use Bookmify\Customers;
use Bookmify\Payments;
use Bookmify\Settings;
use Bookmify\UserProfileCustom;
use Bookmify\UserRoles;
use Bookmify\HelperAdmin;
use Bookmify\Helper;
use Bookmify\SMSTwilio;
use Bookmify\BackendShortcodes;


// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {exit; }


/**
 * Class Admin
 * @package Bookmify\Admin
 */
class Admin
{
	
	public $plugins_settings;
	
	
    /**
     * Constructor.
     */
    public function __construct()
    {
        // Admin controllers.
		
		
        add_action( 'admin_menu', [ $this, 'add_admin_menu' ] );
        add_action( 'wp_loaded',  [ $this, 'init' ] );
		
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_scripts' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_styles' ] );
		add_action( 'admin_init', [ $this, 'bookmify_options' ] );
		add_action( 'admin_init', [ $this, 'bookmify_custom_editor_styles' ] );
		add_action( 'admin_init', [ $this, 'custom_user_roles'] );
		
		
		
		new Appointments();
		new BackendShortcodes();
		new Calendar();
		new Locations();
		new Employees();
		new Services();
		new Categories();
		new Settings_Dayoff_Query();
		new Notifications();
		new Customfields();
		new Customers();
		new Payments();
		new Taxes();
//		new Coupons();
        new Settings();
        new UserProfileCustom(); // only employees & customers have permission to that page
        new UserAppointments(); // only employees & customers have permission to that page
        new SMSTwilio();
		
    }
	public static function bookmify_options(){
		// General Options
		register_setting('bookmify_be_options', 'bookmify_be_date_format', array('default' => 'd F, Y'));				// date format
		register_setting('bookmify_be_options', 'bookmify_be_time_format', array('default' => 'h:i a'));				// time formatinterval
		register_setting('bookmify_be_options', 'bookmify_be_client_timezone', array('default' => ''));					// client's timezone
		register_setting('bookmify_be_options', 'bookmify_be_appointments_pp', array('default' => '10'));				// appointments perpage
		register_setting('bookmify_be_options', 'bookmify_be_appointments_daterange', array('default' => '30'));		// appointments daterange (in days)
		register_setting('bookmify_be_options', 'bookmify_be_default_app_status', array('default' => 'approved'));		// default appointment status
		register_setting('bookmify_be_options', 'bookmify_be_mintime_tobooking', array('default' => 'disabled'));		// min time to booking
		register_setting('bookmify_be_options', 'bookmify_be_maxtime_tobooking', array('default' => 'disabled'));		// max time to booking
		register_setting('bookmify_be_options', 'bookmify_be_mintime_tocancel', array('default' => 'disabled'));		// min time to cancel
		register_setting('bookmify_be_options', 'bookmify_be_time_interval', array('default' => '15'));					// time interval
		register_setting('bookmify_be_options', 'bookmify_be_service_time_as_slot', array('default' => ''));			// service duration as time 
		register_setting('bookmify_be_options', 'bookmify_be_old_appointment_action', array('default' => ''));			// old appointment action
		register_setting('bookmify_be_options', 'bookmify_be_phone_as_required', array('default' => ''));				// old appointment action
	
		
		register_setting('bookmify_be_calendar_options', 'bookmify_be_calendar_hotkeys', array('default' => ''));					// calendar hotkeys switcher
		register_setting('bookmify_be_calendar_options', 'bookmify_be_calendar_hotkeys_today', array('default' => 't'));			// today hotkey
		register_setting('bookmify_be_calendar_options', 'bookmify_be_calendar_hotkeys_month', array('default' => 'm'));			// month hotkey
		register_setting('bookmify_be_calendar_options', 'bookmify_be_calendar_hotkeys_week', array('default' => 'w'));				// week hotkey
		register_setting('bookmify_be_calendar_options', 'bookmify_be_calendar_hotkeys_day', array('default' => 'd'));				// day hotkey
		register_setting('bookmify_be_calendar_options', 'bookmify_be_calendar_hotkeys_list', array('default' => 'l'));				// list hotkey
		register_setting('bookmify_be_calendar_options', 'bookmify_be_calendar_hotkeys_prev', array('default' => 'ArrowLeft'));		// previous action hotkey
		register_setting('bookmify_be_calendar_options', 'bookmify_be_calendar_hotkeys_next', array('default' => 'ArrowRight'));	// next action hotkey
		register_setting('bookmify_be_calendar_options', 'bookmify_be_calendar_hotkeys_reset', array('default' => 'r'));			// reset hotkey
		
		register_setting('bookmify_be_calendar_options', 'bookmify_be_calendar_app_pending', array('default' => 'on'));				// since bookmify v1.2.1
		register_setting('bookmify_be_calendar_options', 'bookmify_be_calendar_app_canceled', array('default' => 'on'));			// since bookmify v1.2.1
		register_setting('bookmify_be_calendar_options', 'bookmify_be_calendar_app_rejected', array('default' => 'on'));			// since bookmify v1.2.1
		
		// another options
		register_setting('bookmify_be_customer_options', 'bookmify_be_customers_pp', array('default' => '10'));
		register_setting('bookmify_be_service_options', 'bookmify_be_services_pp', array('default' => '10'));
		register_setting('bookmify_be_employee_options', 'bookmify_be_employes_pp', array('default' => '10'));
		register_setting('bookmify_be_location_options', 'bookmify_be_locations_pp', array('default' => '10'));
		
		// payment options
		register_setting('bookmify_be_payment_options', 'bookmify_be_paypal_switch', array('default' => ''));
		register_setting('bookmify_be_payment_options', 'bookmify_be_local_payment', array('default' => 'on'));
		register_setting('bookmify_be_payment_options', 'bookmify_be_payments_pp', array('default' => '10'));
		register_setting('bookmify_be_payment_options', 'bookmify_be_payments_daterange', array('default' => '30'));
		register_setting('bookmify_be_payment_options', 'bookmify_be_paypal_sandbox_mode', array('default' => 'on'));
		register_setting('bookmify_be_payment_options', 'bookmify_be_paypal_client_id', array('default' => ''));
		register_setting('bookmify_be_payment_options', 'bookmify_be_paypal_client_secret', array('default' => ''));
		register_setting('bookmify_be_payment_options', 'bookmify_be_paypal_client_id_live', array('default' => ''));
		register_setting('bookmify_be_payment_options', 'bookmify_be_paypal_client_secret_live', array('default' => ''));
		register_setting('bookmify_be_payment_options', 'bookmify_be_stripe_switch', array('default' => ''));
		register_setting('bookmify_be_payment_options', 'bookmify_be_stripe_test_mode', array('default' => 'on'));
		register_setting('bookmify_be_payment_options', 'bookmify_be_stripe_publishable_key', array('default' => ''));
		register_setting('bookmify_be_payment_options', 'bookmify_be_stripe_secret_key', array('default' => ''));
		register_setting('bookmify_be_payment_options', 'bookmify_be_stripe_test_publishable_key', array('default' => ''));
		register_setting('bookmify_be_payment_options', 'bookmify_be_stripe_test_secret_key', array('default' => ''));
		register_setting('bookmify_be_payment_options', 'bookmify_be_currency_format', array('default' => 'USD'));
		register_setting('bookmify_be_payment_options', 'bookmify_be_currency_position', array('default' => 'lspace'));
		register_setting('bookmify_be_payment_options', 'bookmify_be_price_format', array('default' => 'cd'));
		register_setting('bookmify_be_payment_options', 'bookmify_be_price_decimal', array('default' => '2'));
		register_setting('bookmify_be_payment_options', 'bookmify_be_payment_section', array('default' => 'default')); // since bookmify v1.1.7
			
		// google calendar options
		register_setting('bookmify_be_google_options', 'bookmify_be_gc_max_num_events', array('default' => 40));
		register_setting('bookmify_be_google_options', 'bookmify_be_gc_client_secret', array('default' => ''));
		register_setting('bookmify_be_google_options', 'bookmify_be_gc_client_id', array('default' => ''));
		register_setting('bookmify_be_google_options', 'bookmify_be_gc_add_pending', array('default' => ''));
		register_setting('bookmify_be_google_options', 'bookmify_be_gc_add_attendees', array('default' => 'on'));
		register_setting('bookmify_be_google_options', 'bookmify_be_gc_send_invitaion', array('default' => 'on'));
		
		// notifications options
		register_setting('bookmify_be_notification_options', 'bookmify_be_not_mail_service', array('default' => 'php'));
		register_setting('bookmify_be_notification_options', 'bookmify_be_not_sender_name', array('default' => ''));
		register_setting('bookmify_be_notification_options', 'bookmify_be_not_sender_email', array('default' => ''));
		register_setting('bookmify_be_notification_options', 'bookmify_be_not_smtp_host', array('default' => ''));
		register_setting('bookmify_be_notification_options', 'bookmify_be_not_smtp_port', array('default' => ''));
		register_setting('bookmify_be_notification_options', 'bookmify_be_not_smtp_secure', array('default' => 'disabled'));
		register_setting('bookmify_be_notification_options', 'bookmify_be_not_smtp_username', array('default' => ''));
		register_setting('bookmify_be_notification_options', 'bookmify_be_not_smtp_pass', array('default' => ''));
		register_setting('bookmify_be_notification_options', 'bookmify_be_twilio_account_sid', array('default' => '')); 	// since bookmify v1.2.0
		register_setting('bookmify_be_notification_options', 'bookmify_be_twilio_auth_token', array('default' => '')); 		// since bookmify v1.2.0
		register_setting('bookmify_be_notification_options', 'bookmify_be_twilio_number', array('default' => '')); 			// since bookmify v1.2.0
		
		// company info options
		register_setting('bookmify_be_company_info_options', 'bookmify_be_company_info_name', array('default' => ''));
		register_setting('bookmify_be_company_info_options', 'bookmify_be_company_info_address', array('default' => ''));
		register_setting('bookmify_be_company_info_options', 'bookmify_be_company_info_website', array('default' => ''));
		register_setting('bookmify_be_company_info_options', 'bookmify_be_company_info_phone', array('default' => ''));
		register_setting('bookmify_be_company_info_options', 'bookmify_be_company_info_img', array('default' => ''));
		
		// working schedule options
		register_setting('bookmify_be_whours', 'bookmify_be_monday_start', array('default' => '08:00'));
		register_setting('bookmify_be_whours', 'bookmify_be_monday_end', array('default' => '18:00'));
		register_setting('bookmify_be_whours', 'bookmify_be_tuesday_start', array('default' => '08:00'));
		register_setting('bookmify_be_whours', 'bookmify_be_tuesday_end', array('default' => '18:00'));
		register_setting('bookmify_be_whours', 'bookmify_be_wednesday_start', array('default' => '08:00'));
		register_setting('bookmify_be_whours', 'bookmify_be_wednesday_end', array('default' => '18:00'));
		register_setting('bookmify_be_whours', 'bookmify_be_thursday_start', array('default' => '08:00'));
		register_setting('bookmify_be_whours', 'bookmify_be_thursday_end', array('default' => '18:00'));
		register_setting('bookmify_be_whours', 'bookmify_be_friday_start', array('default' => '08:00'));
		register_setting('bookmify_be_whours', 'bookmify_be_friday_end', array('default' => '18:00'));
		register_setting('bookmify_be_whours', 'bookmify_be_saturday_start', array('default' => '08:00'));
		register_setting('bookmify_be_whours', 'bookmify_be_saturday_end', array('default' => '18:00'));
		register_setting('bookmify_be_whours', 'bookmify_be_sunday_start', array('default' => '08:00'));
		register_setting('bookmify_be_whours', 'bookmify_be_sunday_end', array('default' => '18:00'));
		
		// frontend options
		register_setting('bookmify_be_frontend_options', 'bookmify_be_feoption_gfont_switcher', array('default' => 'on')); // since bookmify v1.3.5
		register_setting('bookmify_be_frontend_options', 'bookmify_be_feoption_default_body_font', array('default' => 'arial')); // since bookmify v1.3.5
		register_setting('bookmify_be_frontend_options', 'bookmify_be_feoption_default_title_font', array('default' => 'courier')); // since bookmify v1.3.5
		register_setting('bookmify_be_frontend_options', 'bookmify_be_fe_conf_switcher', array('default' => '')); // since bookmify v1.3.1
		register_setting('bookmify_be_frontend_options', 'bookmify_be_fe_conf_title', array('default' => 'Thank you!')); // since bookmify v1.3.1
		register_setting('bookmify_be_frontend_options', 'bookmify_be_fe_conf_desc', array('default' => 'Your appointment is succesfully received. Please meet us at your selected date and time.')); // since bookmify v1.3.1
		register_setting('bookmify_be_frontend_options', 'bookmify_be_fe_conf_footer', array('default' => 'For any kind of inquiry, please call us at 543-323-3456')); // since bookmify v1.3.1
		register_setting('bookmify_be_frontend_options', 'bookmify_be_fe_conf_service_back', array('default' => 'Go to services')); // since bookmify v1.3.1
		
		register_setting('bookmify_be_frontend_options', 'bookmify_be_feoption_service_details', array('default' => 'disabled'));
		register_setting('bookmify_be_frontend_options', 'bookmify_be_feoption_enable_deveoped_fe', array('default' => 'enabled'));
		register_setting('bookmify_be_frontend_options', 'bookmify_be_feoption_autocreate_bookmify_user', array('default' => 'enabled'));
		register_setting('bookmify_be_frontend_options', 'bookmify_be_feoption_category_filter_alpha', array('default' => 'disabled'));
		register_setting('bookmify_be_frontend_options', 'bookmify_be_feoption_only_one_emp', array('default' => 'default'));
		
		register_setting('bookmify_be_frontend_options', 'bookmify_be_feoption_main_color_1', array('default' => '#5473e8'));
		register_setting('bookmify_be_frontend_options', 'bookmify_be_feoption_main_color_2', array('default' => '#35d8ac'));
		register_setting('bookmify_be_frontend_options', 'bookmify_be_feoption_main_color_3', array('default' => '#7e849b'));
	}
    /**
     * Init.
     */
    public function init()
    {
        if ( ! session_id() ) {
            @session_start();
        }
    }
	public function custom_user_roles(){
		UserRoles::init();
	}
    /**
     * Bookmify Admin Menu
	 * @since 1.0.0
     */
    public function add_admin_menu()
    {
		// Add Main Menu
		add_menu_page( 'Bookmify', 'Bookmify', 'bookmify_be_read_menu', BOOKMIFY_MENU, [ $this, 'bookmify_welcome_page' ], BOOKMIFY_ASSETS_URL.'img/bookmify_logo.png', '3' );

    }
	
	/**
     * Bookmify Welcome Page
	 * @since 1.0.0
     */
	public static function bookmify_welcome_page(){
		
		echo HelperAdmin::bookmifyAdminContentStart();
		echo HelperAdmin::bookmifyAdminContentEnd();
	}
	
	
	
	/**
     * Admin Scripts.
	 * @since 1.0.0
     */
	public function enqueue_scripts() {
		
		global $wp_locale;
		
		wp_register_script('bookmify-admin-plugins', BOOKMIFY_VER_BACKEND_URL . 'js/admin-plugins.js',['jquery',], BOOKMIFY_VERSION, true);
		wp_register_script('select2', BOOKMIFY_ASSETS_URL . 'js/select2.js',['jquery',], BOOKMIFY_VERSION, true);
		wp_register_script('intlTelInput-jquery.min', BOOKMIFY_ASSETS_URL . 'js/intlTelInput-jquery.min.js',['jquery',], BOOKMIFY_VERSION, true);
		wp_register_script('bookmify-admin-app', BOOKMIFY_VER_BACKEND_URL . 'js/admin.js',['jquery',], BOOKMIFY_VERSION, true);
		wp_register_script('bookmify-service', BOOKMIFY_VER_BACKEND_URL . 'js/service.js',['jquery',], BOOKMIFY_VERSION, true);
		wp_register_script('bookmify-settings', BOOKMIFY_VER_BACKEND_URL . 'js/settings.js',['jquery',], BOOKMIFY_VERSION, true);
		wp_register_script('bookmify-employees', BOOKMIFY_VER_BACKEND_URL . 'js/employees.js',['jquery',], BOOKMIFY_VERSION, true);
		wp_register_script('bookmify-customers', BOOKMIFY_VER_BACKEND_URL . 'js/customers.js',['jquery',], BOOKMIFY_VERSION, true);
		wp_register_script('bookmify-appointments', BOOKMIFY_VER_BACKEND_URL . 'js/appointments.js',['jquery',], BOOKMIFY_VERSION, true);
		wp_register_script('bookmify-locations', BOOKMIFY_VER_BACKEND_URL . 'js/locations.js',['jquery',], BOOKMIFY_VERSION, true);
		wp_register_script('bookmify-payments', BOOKMIFY_VER_BACKEND_URL . 'js/payments.js',['jquery',], BOOKMIFY_VERSION, true);
		wp_register_script('bookmify-notifications', BOOKMIFY_VER_BACKEND_URL . 'js/notifications.js',['jquery',], BOOKMIFY_VERSION, true);
		wp_register_script('bookmify-taxes', BOOKMIFY_VER_BACKEND_URL . 'js/taxes.js',['jquery',], BOOKMIFY_VERSION, true);
		wp_register_script('bookmify-coupons', BOOKMIFY_VER_BACKEND_URL . 'js/coupons.js',['jquery',], BOOKMIFY_VERSION, true);
		wp_register_script('bookmify-shortcodes', BOOKMIFY_VER_BACKEND_URL . 'js/shortcodes.js',['jquery',], BOOKMIFY_VERSION, true);
		wp_register_script('multidatespicker', BOOKMIFY_ASSETS_URL . 'js/multidatespicker.js',['jquery',], BOOKMIFY_VERSION, true);
		wp_register_script('utils', BOOKMIFY_ASSETS_URL . 'js/utils.js',['jquery',], BOOKMIFY_VERSION, true);
		wp_register_script('bookmify-cf', BOOKMIFY_VER_BACKEND_URL . 'js/customfields.js',['jquery',], BOOKMIFY_VERSION, true);
		wp_register_script('bookmify-user-profile', BOOKMIFY_VER_BACKEND_URL . 'js/user-profile.js',['jquery',], BOOKMIFY_VERSION, true);
		wp_register_script('bookmify-user-appointments', BOOKMIFY_VER_BACKEND_URL . 'js/user-appointments.js',['jquery',], BOOKMIFY_VERSION, true);
		
		wp_localize_script(
			'bookmify-admin-app',
			'bookmifyAdminConfig',
			[
				'ajaxUrl' 				=> admin_url( 'admin-ajax.php' ),
				'resetConfirmMessage' 	=> esc_html__( 'Are you sure? Resetting will lose all custom values in all section', 'bookmify' ),
				'resetSuccessMessage' 	=> esc_html__( 'Defaults Restored', 'bookmify' ),
				'saveSuccessMessage' 	=> esc_html__( 'Settings Saved!', 'bookmify' ),
				'saveFailedMessage' 	=> esc_html__( 'Save Process Failed!', 'bookmify' ),
			]
		);
		
		wp_localize_script(
			'bookmify-service',
			'bookmifyConfig',
			[
				'ajaxUrl' 				=> admin_url( 'admin-ajax.php' ),
				'deleteConfirmMessage' 	=> esc_html__( 'Are you sure?', 'bookmify' ),
				'updatedCategory' 		=> esc_html__( 'A category has been updated!', 'bookmify' ),
				'updateWarningCategory' => esc_html__( 'Please enter at least one character!', 'bookmify' ),
				'addedService' 			=> esc_html__( 'A service has been added!', 'bookmify' ),
				'addedCategory' 		=> esc_html__( 'A category has been added!', 'bookmify' ),
				'orderedCategory' 		=> esc_html__( 'Categories\' order have been changed!', 'bookmify' ),
				'deletedCategory' 		=> esc_html__( 'A category has been deleted!', 'bookmify' ),
				'updatedService' 		=> esc_html__( 'A service has been updated!', 'bookmify' ),
				'orderedExtra' 			=> esc_html__( 'Extra Services\' order have been changed!', 'bookmify' ),
				'deletedService' 		=> esc_html__( 'A service has been deleted!', 'bookmify' ),
				'errorField' 			=> esc_html__( 'Please fill out this field!', 'bookmify' ),
				'cantDeleteCategory'	=> esc_html__( 'Please make sure that this category is not assigned to any services. Number of services related to this category:', 'bookmify' ),
				'cantDeleteService'		=> esc_html__( 'Please make sure that there are no appointments with this service. Number of expected appointments with this service:', 'bookmify' ),
				'assetsURL'				=> BOOKMIFY_ASSETS_URL,
			]
		);
		
		wp_localize_script(
			'bookmify-settings',
			'bookmifyConfig',
			[
				'ajaxUrl' 				=> admin_url( 'admin-ajax.php' ),
				'deleteConfirmMessage' 	=> esc_html__( 'Are you sure?', 'bookmify' ),
				'addedDay' 				=> esc_html__( 'A holiday has been added!', 'bookmify' ),
				'deletedDay' 			=> esc_html__( 'A holiday has been deleted!', 'bookmify' ),
				'noDay' 				=> esc_html__( 'Please choose at least one day!', 'bookmify' ),
				'noTitle' 				=> esc_html__( 'Please enter title of offday!', 'bookmify' ),
				'updatedDay' 			=> esc_html__( 'A holiday has been updated!', 'bookmify' ),
				'updatedDay' 			=> esc_html__( 'A holiday has been updated!', 'bookmify' ),
				'saveWorkingHours' 		=> esc_html__( 'Working hours and breaks have been saved!', 'bookmify' ),
				'settingsSaved' 		=> esc_html__( 'Settings successfully saved!', 'bookmify' ),
			]
		);
		
		wp_localize_script(
			'bookmify-employees',
			'bookmifyConfig',
			[
				'ajaxUrl' 				=> admin_url( 'admin-ajax.php' ),
				'deleteConfirmMessage' 	=> esc_html__( 'Are you sure?', 'bookmify' ),
				'deletedText' 			=> esc_html__( 'Successfully deleted', 'bookmify' ),
				'addedText' 			=> esc_html__( 'Successfully added', 'bookmify' ),
				'noDateEnteredText' 	=> esc_html__( 'Please choose at least one day!', 'bookmify' ),
				'noTitleEnteredText' 	=> esc_html__( 'Please enter title of offday!', 'bookmify' ),
				'errorField' 			=> esc_html__( 'Please fill out this field!', 'bookmify' ),
				'invalidEmail' 			=> esc_html__( 'Invalid Email.', 'bookmify' ),
				'updatedText' 			=> esc_html__( 'An Employee has been updated!', 'bookmify' ),
				'cantDeleteEmployee'	=> esc_html__( 'Please make sure that this employee does not have scheduled appointments. The number of scheduled appointments with this employee:', 'bookmify' ),
				'demoWpUserID'			=> esc_html__( 'Unable to add the wordpress user, because of it is demo version.', 'bookmify' ),
			]
		);
		
		wp_localize_script(
			'bookmify-appointments',
			'bookmifyConfig',
			[
				'ajaxUrl' 				=> admin_url( 'admin-ajax.php' ),
				'errorField' 			=> esc_html__( 'Please fill out this field!', 'bookmify' ),
				'clearCapacity' 		=> esc_html__( 'Choose Service and Employee!', 'bookmify' ),
				'deletedText' 			=> esc_html__( 'Successfully deleted', 'bookmify' ),
				'savedText' 			=> esc_html__( 'Successfully saved', 'bookmify' ),
				'addedText' 			=> esc_html__( 'Successfully added', 'bookmify' ),
				'updatedText' 			=> esc_html__( 'Successfully updated', 'bookmify' ),
				'reorderedText' 		=> esc_html__( 'Successfully reordered', 'bookmify' ),
				'errorDelete' 			=> esc_html__( 'You are trying to delete an appointment that has already taken place.', 'bookmify' ),
				'errorEdit' 			=> esc_html__( 'You are trying to change an appointment that has already taken place.', 'bookmify' ),
				'appointmentStartDate' 	=> date('Y-m-d').' 00:00:00',
				'appointmentEndDate' 	=> date('Y-m-d', strtotime('+'.(get_option('bookmify_be_appointments_daterange', 30) - 1).' days')).' 23:59:59',
				'appointmentDateRange'	=> get_option('bookmify_be_appointments_daterange', 30) - 1,
				'momentDateFormat'		=> Helper::convertDateFormat('date', 'momentFormat'),
				'calendar'      		=> array(
					'monthsLong'  			=> array_values( $wp_locale->month ),
					'monthsShort' 			=> array_values( $wp_locale->month_abbrev ),
					'daysLong'    			=> array_values( $wp_locale->weekday ),
					'daysShort'   			=> array_values( $wp_locale->weekday_abbrev ),
					'firstDay'				=> (int) get_option( 'start_of_week', 1 )
				),
				'noRecords' 			=> esc_html__( 'No Records', 'bookmify' ),
				'minCapacity' 			=> esc_html__( 'Minimum Capacity:', 'bookmify' ),
				'maxCapacity' 			=> esc_html__( 'Maximum Capacity:', 'bookmify' ),
			]
		);
		
		wp_localize_script(
			'bookmify-locations',
			'bookmifyConfig',
			[
				'ajaxUrl' 				=> admin_url( 'admin-ajax.php' ),
				'deletedText' 			=> esc_html__( 'Successfully deleted', 'bookmify' ),
				'savedText' 			=> esc_html__( 'Successfully saved', 'bookmify' ),
				'addedText' 			=> esc_html__( 'Successfully added', 'bookmify' ),
				'reorderedText' 		=> esc_html__( 'Successfully reordered', 'bookmify' ),
				'errorField' 			=> esc_html__( 'Please fill out this field!', 'bookmify' ),
			]
		);
		
		wp_localize_script(
			'bookmify-notifications',
			'bookmifyConfig',
			[
				'ajaxUrl' 				=> admin_url( 'admin-ajax.php' ),
				'deletedText' 			=> esc_html__( 'Successfully deleted', 'bookmify' ),
				'savedText' 			=> esc_html__( 'Successfully saved', 'bookmify' ),
				'addedText' 			=> esc_html__( 'Successfully added', 'bookmify' ),
				'reorderedText' 		=> esc_html__( 'Successfully reordered', 'bookmify' ),
				'invalidEmail' 			=> esc_html__( 'Invalid Email', 'bookmify' ),
				'invalidPhone' 			=> esc_html__( 'Invalid Phone', 'bookmify' ),
				'testSent' 				=> esc_html__( 'Test email successfully has been sent', 'bookmify' ),
				'testSentSMS' 			=> esc_html__( 'Test SMS successfully has been sent', 'bookmify' ),
				'testNotSend' 			=> esc_html__( 'Mail warning', 'bookmify' ),
				'errorField' 			=> esc_html__( 'Please fill out this field!', 'bookmify' ),
			]
		);
		
		wp_localize_script(
			'bookmify-payments',
			'bookmifyConfig',
			[
				'ajaxUrl' 				=> admin_url( 'admin-ajax.php' ),
				'deletedText' 			=> esc_html__( 'Successfully deleted', 'bookmify' ),
				'savedText' 			=> esc_html__( 'Successfully saved', 'bookmify' ),
				'addedText' 			=> esc_html__( 'Successfully added', 'bookmify' ),
				'updatedText' 			=> esc_html__( 'Successfully updated', 'bookmify' ),
				'reorderedText' 		=> esc_html__( 'Successfully reordered', 'bookmify' ),
				'paymentStartDate' 		=> date('Y-m-d', strtotime('-'.(get_option('bookmify_be_payments_daterange', 30) - 1).' days')).' 00:00:00',
				'paymentEndDate' 		=> date('Y-m-d').' 23:59:59',
				'paymentDateRange'		=> get_option('bookmify_be_payments_daterange', 30) - 1,
				'momentDateFormat'		=> Helper::convertDateFormat('date', 'momentFormat'),
				'calendar'      		=> array(
					'monthsLong'  			=> array_values( $wp_locale->month ),
					'monthsShort' 			=> array_values( $wp_locale->month_abbrev ),
					'daysLong'    			=> array_values( $wp_locale->weekday ),
					'daysShort'   			=> array_values( $wp_locale->weekday_abbrev ),
					'firstDay'				=> (int) get_option( 'start_of_week', 1 )
				),
				'noRecords' 			=> esc_html__( 'No Records', 'bookmify' ),
			]
		);
		
		wp_localize_script(
			'bookmify-cf',
			'bookmifyConfig',
			[
				'ajaxUrl' 				=> admin_url( 'admin-ajax.php' ),
				'deletedTextCF' 		=> esc_html__( 'Successfully deleted', 'bookmify' ),
				'savedTextCF' 			=> esc_html__( 'Successfully saved', 'bookmify' ),
				'addedTextCF' 			=> esc_html__( 'Successfully added', 'bookmify' ),
				'reorderedTextCF' 		=> esc_html__( 'Successfully reordered', 'bookmify' ),
				'errorField' 			=> esc_html__( 'Please fill out this field!', 'bookmify' ),
			]
		);
		
		wp_localize_script(
			'bookmify-taxes',
			'bookmifyConfig',
			[
				'ajaxUrl' 				=> admin_url( 'admin-ajax.php' ),
				'deletedTax' 			=> esc_html__( 'Successfully deleted', 'bookmify' ),
				'savedTax' 				=> esc_html__( 'Successfully saved', 'bookmify' ),
				'addedTax' 				=> esc_html__( 'Successfully added', 'bookmify' ),
				'errorField' 			=> esc_html__( 'Please fill out this field!', 'bookmify' ),
			]
		);
		
		wp_localize_script(
			'bookmify-coupons',
			'bookmifyConfig',
			[
				'ajaxUrl' 				=> admin_url( 'admin-ajax.php' ),
				'deletedCoupon' 		=> esc_html__( 'Successfully deleted', 'bookmify' ),
				'savedCoupon' 			=> esc_html__( 'Successfully saved', 'bookmify' ),
				'addedCoupon' 			=> esc_html__( 'Successfully added', 'bookmify' ),
				'errorField' 			=> esc_html__( 'Please fill out this field!', 'bookmify' ),
				'errorZero' 			=> esc_html__( 'Enter a non-zero value!', 'bookmify' ),
				'couponExist' 			=> esc_html__( 'This coupon code already exists.', 'bookmify' ),
				'appointmentDateRange'	=> get_option('bookmify_be_appointments_daterange', 30) - 1,
				'momentDateFormat'		=> Helper::convertDateFormat('date', 'momentFormat'),
				'calendar'      		=> array(
					'monthsLong'  			=> array_values( $wp_locale->month ),
					'monthsShort' 			=> array_values( $wp_locale->month_abbrev ),
					'daysLong'    			=> array_values( $wp_locale->weekday ),
					'daysShort'   			=> array_values( $wp_locale->weekday_abbrev ),
					'firstDay'				=> (int) get_option( 'start_of_week', 1 )
				),
			]
		);
		
		wp_localize_script(
			'bookmify-shortcodes',
			'bookmifyConfig',
			[
				'ajaxUrl' 				=> admin_url( 'admin-ajax.php' ),
				'deletedShortcode' 		=> esc_html__( 'Successfully deleted', 'bookmify' ),
				'savedShortcode' 		=> esc_html__( 'Successfully saved', 'bookmify' ),
				'addedShortcode' 		=> esc_html__( 'Successfully added', 'bookmify' ),
				'errorField' 			=> esc_html__( 'Please fill out this field!', 'bookmify' ),
			]
		);
		
		
		wp_localize_script(
			'bookmify-customers',
			'bookmifyConfig',
			[
				'ajaxUrl' 				=> admin_url( 'admin-ajax.php' ),
				'deleteConfirmMessage' 	=> esc_html__( 'Are you sure?', 'bookmify' ),
				'deletedText' 			=> esc_html__( 'Successfully deleted', 'bookmify' ),
				'savedText' 			=> esc_html__( 'Successfully saved', 'bookmify' ),
				'addedText' 			=> esc_html__( 'Successfully added', 'bookmify' ),
				'months' 				=> array(
					'january' 		=> esc_html__( 'January', 'bookmify' ),
					'february' 		=> esc_html__( 'February', 'bookmify' ),
					'march' 		=> esc_html__( 'March', 'bookmify' ),
					'april' 		=> esc_html__( 'April', 'bookmify' ),
					'may' 			=> esc_html__( 'May', 'bookmify' ),
					'june' 			=> esc_html__( 'June', 'bookmify' ),
					'july' 			=> esc_html__( 'July', 'bookmify' ),
					'august' 		=> esc_html__( 'August', 'bookmify' ),
					'september' 	=> esc_html__( 'September', 'bookmify' ),
					'october' 		=> esc_html__( 'October', 'bookmify' ),
					'november' 		=> esc_html__( 'November', 'bookmify' ),
					'december' 		=> esc_html__( 'December', 'bookmify' ),
				),
				'cantDeleteCustomer'	=> esc_html__( 'Please make sure that this customer does not have scheduled appointments. The number of scheduled appointments with this customer:', 'bookmify' ),
				'emptyField'			=> esc_html__( 'Please fill out this field', 'bookmify' ),
				'invalidPhone'			=> esc_html__( 'Invalid Phone', 'bookmify' ),
				'invalidEmail'			=> esc_html__( 'Invalid Email', 'bookmify' ),
				'invalidDate'			=> esc_html__( 'Invalid Date', 'bookmify' ),
				'emailExist'			=> esc_html__( 'Email already exist!', 'bookmify' ),
				'demoWpUserID'			=> esc_html__( 'Unable to add the wordpress user, because of it is demo version.', 'bookmify' ),
			]
		);
		
		wp_localize_script(
			'bookmify-user-profile',
			'bookmifyConfig',
			[
				'ajaxUrl' 				=> admin_url( 'admin-ajax.php' ),
				'errorField' 			=> esc_html__( 'Please fill out this field!', 'bookmify' ),
				'invalidEmail' 			=> esc_html__( 'Invalid Email.', 'bookmify' ),
				'invalidDate' 			=> esc_html__( 'Invalid Date.', 'bookmify' ),
				'updatedText' 			=> esc_html__( 'Details have been updated!', 'bookmify' ),
				'disconnect' 			=> esc_html__( 'Your google calendar has been disconnected.', 'bookmify' ),
				'connected' 			=> esc_html__( 'Google Calendar has been connected.', 'bookmify' ),
				'emailUsed' 			=> esc_html__( 'This email already used by another customer.', 'bookmify' ),
				'emailExist' 			=> esc_html__( 'Email exist.', 'bookmify' ),
				'hackText' 				=> esc_html__( 'Bunny error.', 'bookmify' ),
				'months' 				=> array(
					'january' 		=> esc_html__( 'January', 'bookmify' ),
					'february' 		=> esc_html__( 'February', 'bookmify' ),
					'march' 		=> esc_html__( 'March', 'bookmify' ),
					'april' 		=> esc_html__( 'April', 'bookmify' ),
					'may' 			=> esc_html__( 'May', 'bookmify' ),
					'june' 			=> esc_html__( 'June', 'bookmify' ),
					'july' 			=> esc_html__( 'July', 'bookmify' ),
					'august' 		=> esc_html__( 'August', 'bookmify' ),
					'september' 	=> esc_html__( 'September', 'bookmify' ),
					'october' 		=> esc_html__( 'October', 'bookmify' ),
					'november' 		=> esc_html__( 'November', 'bookmify' ),
					'december' 		=> esc_html__( 'December', 'bookmify' ),
				),
			]
		);
		
		wp_localize_script(
			'bookmify-user-appointments',
			'bookmifyConfig',
			[
				'ajaxUrl' 				=> admin_url( 'admin-ajax.php' ),
				'errorField' 			=> esc_html__( 'Please fill out this field!', 'bookmify' ),
				'clearCapacity' 		=> esc_html__( 'Choose Service and Employee!', 'bookmify' ),
				'deletedText' 			=> esc_html__( 'Successfully deleted', 'bookmify' ),
				'savedText' 			=> esc_html__( 'Successfully saved', 'bookmify' ),
				'addedText' 			=> esc_html__( 'Successfully added', 'bookmify' ),
				'updatedText' 			=> esc_html__( 'Successfully updated', 'bookmify' ),
				'canceledText' 			=> esc_html__( 'Successfully canceled', 'bookmify' ),
				'hackText' 				=> esc_html__( 'Bunny error.', 'bookmify' ),
				'errorDelete' 			=> esc_html__( 'You are trying to delete an appointment that has already taken place.', 'bookmify' ),
				'errorEdit' 			=> esc_html__( 'You are trying to change an appointment that has already taken place.', 'bookmify' ),
				'errorCancel' 			=> esc_html__( 'You are trying to cancel an appointment that has already taken place or has same status.', 'bookmify' ),
				'appointmentStartDate' 	=> date('Y-m-d').' 00:00:00',
				'appointmentEndDate' 	=> date('Y-m-d', strtotime('+'.(get_option('bookmify_be_appointments_daterange', 30) - 1).' days')).' 23:59:59',
				'appointmentDateRange'	=> get_option('bookmify_be_appointments_daterange', 30) - 1,
				'momentDateFormat'		=> Helper::convertDateFormat('date', 'momentFormat'),
				'calendar'      		=> array(
					'monthsLong'  			=> array_values( $wp_locale->month ),
					'monthsShort' 			=> array_values( $wp_locale->month_abbrev ),
					'daysLong'    			=> array_values( $wp_locale->weekday ),
					'daysShort'   			=> array_values( $wp_locale->weekday_abbrev ),
					'firstDay'				=> (int) get_option( 'start_of_week', 1 )
				),
				'noRecords' 			=> esc_html__( 'No Records', 'bookmify' ),
				'minCapacity' 			=> esc_html__( 'Minimum Capacity:', 'bookmify' ),
				'maxCapacity' 			=> esc_html__( 'Maximum Capacity:', 'bookmify' ),
			]
		);
		
		wp_enqueue_media();
		wp_enqueue_script( 'bookmify-admin-plugins' );
		wp_enqueue_script( 'select2' );
		wp_enqueue_script( 'utils' );
		wp_enqueue_script( 'intlTelInput-jquery.min' );
		wp_enqueue_script( 'jquery-ui-sortable' );
		wp_enqueue_script( 'jquery-ui-datepicker' );
        wp_enqueue_script( 'wp-color-picker');
		wp_enqueue_script( 'multidatespicker' );
		wp_enqueue_script( 'bookmify-admin-app' );
		wp_enqueue_script( 'bookmify-service' );
		wp_enqueue_script( 'bookmify-settings' );
		wp_enqueue_script( 'bookmify-employees' );
		wp_enqueue_script( 'bookmify-customers' );
		wp_enqueue_script( 'bookmify-appointments' );
		wp_enqueue_script( 'bookmify-locations' );
		wp_enqueue_script( 'bookmify-payments' );
		wp_enqueue_script( 'bookmify-notifications' );
		wp_enqueue_script( 'bookmify-taxes' );
		wp_enqueue_script( 'bookmify-coupons' );
		wp_enqueue_script( 'bookmify-shortcodes' );
		wp_enqueue_script( 'bookmify-cf' );
		wp_enqueue_script( 'bookmify-user-profile' );
		wp_enqueue_script( 'bookmify-user-appointments' );
		
	}
	
	
	/**
     * Admin Styles.
	 * @since 1.0.0
     */
	public function enqueue_styles() {
		wp_register_style( 'bookmify-admin-app', BOOKMIFY_VER_BACKEND_URL . 'css/admin.css', '', BOOKMIFY_VERSION);
		wp_register_style( 'select2', BOOKMIFY_ASSETS_URL . 'css/select2.css', '', BOOKMIFY_VERSION);
		wp_register_style( 'bookmify-admin2', BOOKMIFY_ASSETS_URL . 'css/admin2.css', '', BOOKMIFY_VERSION);
		wp_register_style( 'multidatespicker', BOOKMIFY_ASSETS_URL . 'css/multidatespicker.css', '', BOOKMIFY_VERSION);
		wp_register_style( 'daterangepicker', BOOKMIFY_ASSETS_URL . 'css/daterangepicker.css', '', BOOKMIFY_VERSION);
		wp_register_style( 'iaoalert', BOOKMIFY_ASSETS_URL . 'css/iaoalert.css', '', BOOKMIFY_VERSION);
		wp_register_style( 'malihu', BOOKMIFY_ASSETS_URL . 'css/malihu.css', '', BOOKMIFY_VERSION);
		wp_register_style( 'tooltipster', BOOKMIFY_ASSETS_URL . 'css/tooltipster.css', '', BOOKMIFY_VERSION);
		wp_register_style( 'fontello', BOOKMIFY_ASSETS_URL . 'css/fontello.css', '', BOOKMIFY_VERSION);
		wp_register_style( 'owlcarousel', BOOKMIFY_ASSETS_URL . 'css/owlcarousel.css', '', BOOKMIFY_VERSION);
		wp_register_style( 'intlTelInput.min', BOOKMIFY_ASSETS_URL . 'css/intlTelInput.min.css', '', BOOKMIFY_VERSION);
		
		wp_enqueue_style( 'wp-color-picker');
		wp_enqueue_style( 'select2' );
		wp_enqueue_style( 'multidatespicker' );
		wp_enqueue_style( 'daterangepicker' );
		wp_enqueue_style( 'intlTelInput.min' );
		wp_enqueue_style( 'iaoalert' );
		wp_enqueue_style( 'malihu' );
		wp_enqueue_style( 'tooltipster' );
		wp_enqueue_style( 'fontello' );
		wp_enqueue_style( 'owlcarousel' );
		wp_enqueue_style( 'bookmify-admin-app' );
		wp_enqueue_style( 'bookmify-admin2' );
		
	}
	
	/**
     * Admin Styles.
	 * @since 1.0.0
     */
	function bookmify_custom_editor_styles() {
		add_editor_style(BOOKMIFY_ASSETS_URL . 'css/tinymce.css');
	}

}
