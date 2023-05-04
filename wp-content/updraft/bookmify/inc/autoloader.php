<?php
namespace Bookmify;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Bookmify autoloader class.
 *
 * Bookmify autoloader handler class is responsible for loading the different
 * classes needed to run the plugin.
 *
 * @since 1.0.0
 */
class Autoloader {

	/**
	 * Classes map.
	 *
	 * Maps Bookmify classes to file names.
	 *
	 * @since 1.0.0
	 * @access private
	 * @static
	 *
	 * @var array Classes used by elementor.
	 */
	private static $classes_map = [ 
		
		
		'Querify' 						=> 'inc/querify.php',
		'GoogleCalendarProject' 		=> 'inc/google/google-calendar.php',
		'PHPMailerCustom' 				=> 'inc/phpmailer/phpmailer.php',
		'SMSTwilio' 				    => 'inc/twilio/twiliosms.php',
		
		// BACKEND FILES
		'Admin' 						=> 'backend/inc/admin.php',
		'UserRoles' 					=> 'backend/inc/userroles.php',
		
		'Helper' 						=> 'backend/inc/helpers/helper.php',
		'HelperAdmin' 					=> 'backend/inc/helpers/helper-admin.php',
		'HelperAppointments' 			=> 'backend/inc/helpers/helper-appointments.php',
		'HelperCalendar' 				=> 'backend/inc/helpers/helper-calendar.php',
		'HelperServices' 				=> 'backend/inc/helpers/helper-services.php',
		'HelperCustomers' 				=> 'backend/inc/helpers/helper-customers.php',
		'HelperEmployees' 				=> 'backend/inc/helpers/helper-employees.php',
		'HelperLocations' 				=> 'backend/inc/helpers/helper-locations.php',
		'HelperCustomfields' 			=> 'backend/inc/helpers/helper-customfields.php',
		'HelperNotifications' 			=> 'backend/inc/helpers/helper-notifications.php',
		'HelperPayments' 				=> 'backend/inc/helpers/helper-payments.php',
		'HelperTime' 					=> 'backend/inc/helpers/helper-time.php',
		'HelperCabinet' 				=> 'backend/inc/helpers/helper-cabinet.php',
		'HelperSettings' 				=> 'backend/inc/helpers/helper-settings.php',
		'HelperShortcodes' 				=> 'backend/inc/helpers/helper-shortcodes.php',
		'HelperCoupons' 				=> 'backend/inc/helpers/helper-coupons.php',
		
		'Calendar' 						=> 'backend/inc/calendar/calendar.php',
		'NotificationManagement' 		=> 'backend/inc/core/notificationmanagement.php',
		
		'Settings' 						=> 'backend/inc/settings/settings.php',
		'Settings_Page' 				=> 'backend/inc/settings/settings-page.php',
		'Settings_Dayoff_Query' 		=> 'backend/inc/settings/settings-dayoff-query.php',
		
		'Services' 						=> 'backend/inc/entities/services.php',
		'Categories' 					=> 'backend/inc/entities/categories.php',
		'Locations' 					=> 'backend/inc/entities/locations.php',
		'Employees' 					=> 'backend/inc/entities/employees.php',
		'Appointments' 					=> 'backend/inc/entities/appointments.php',
		'Notifications' 				=> 'backend/inc/entities/notifications.php',
		'Customfields' 					=> 'backend/inc/entities/customfields.php',
		'Customers' 					=> 'backend/inc/entities/customers.php',
		'Payments' 						=> 'backend/inc/entities/payments.php',
		'Taxes' 						=> 'backend/inc/entities/taxes.php',
		'Coupons' 						=> 'backend/inc/entities/coupons.php',
		'BackendShortcodes' 			=> 'backend/inc/entities/shortcodes.php',
		
		
		'UserProfileCustom' 			=> 'backend/inc/user-cabinet/profile.php',
		'UserAppointments' 				=> 'backend/inc/user-cabinet/appointments.php',
		
		
		// FRONTEND FILES
		'Frontend' 						=> 'frontend/frontend.php',
	];

	/**
	 * Classes aliases.
	 *
	 * Maps Bookmify classes to aliases.
	 *
	 * @since 1.0.0
	 * @access private
	 * @static
	 *
	 * @var array Classes aliases.
	 */
	private static $classes_aliases = [
		//'Control_Base' => 'Base_Data_Control',
	];

	/**
	 * Run autoloader.
	 *
	 * Register a function as `__autoload()` implementation.
	 *
	 * @since 1.0.0
	 * @access public
	 * @static
	 */
	public static function run() {
		spl_autoload_register( [ __CLASS__, 'autoload' ] );
	}

	/**
	 * Get classes aliases.
	 *
	 * Retrieve the classes aliases names.
	 *
	 * @since 1.0.0
	 * @access public
	 * @static
	 *
	 * @return array Classes aliases.
	 */
	public static function get_classes_aliases() {
		return self::$classes_aliases;
	}

	/**
	 * Load class.
	 *
	 * For a given class name, require the class file.
	 *
	 * @since 1.0.0
	 * @access private
	 * @static
	 *
	 * @param string $relative_class_name Class name.
	 */
	private static function load_class( $relative_class_name ) {
		if ( isset( self::$classes_map[ $relative_class_name ] ) ) {
			$file = BOOKMIFY_PATH . '/' . self::$classes_map[ $relative_class_name ];
		} else {
			$filename = strtolower(
				preg_replace(
					[ '/([a-z])([A-Z])/', '/_/', '/\\\/' ],
					[ '$1-$2', '-', DIRECTORY_SEPARATOR ],
					$relative_class_name
				)
			);

			$file = BOOKMIFY_PATH . $filename . '.php';
		}

		if ( is_readable( $file ) ) {
			require $file;
		}
	}
	


	/**
	 * Autoload.
	 *
	 * For a given class, check if it exist and load it.
	 *
	 * @since 1.0.0
	 * @access private
	 * @static
	 *
	 * @param string $class Class name.
	 */
	private static function autoload( $class ) {
		if ( 0 !== strpos( $class, __NAMESPACE__ . '\\' ) ) {
			return;
		}

		$relative_class_name = preg_replace( '/^' . __NAMESPACE__ . '\\\/', '', $class );

		$has_class_alias = isset( self::$classes_aliases[ $relative_class_name ] );

		// Backward Compatibility: Save old class name for set an alias after the new class is loaded
		if ( $has_class_alias ) {
			$relative_class_name = self::$classes_aliases[ $relative_class_name ];
		}

		$final_class_name = __NAMESPACE__ . '\\' . $relative_class_name;

		if ( ! class_exists( $final_class_name ) ) {
			self::load_class( $relative_class_name );
		}

		if ( $has_class_alias ) {
			class_alias( $final_class_name, $class );
		}
	}
}
