<?php
namespace Bookmify;

use Bookmify\Autoloader;
use Bookmify\Admin;
use Bookmify\Frontend;

if ( ! defined( 'ABSPATH' ) ) {	exit; }



/**
 * Bookmify plugin class.
 *
 * The main plugin handler class is responsible for initializing Bookmify. The
 * class registers and all the components required to run the plugin.
 *
 * @since 1.0.0
 */

class Plugin {
	
	/**
	 * Instance.
	 *
	 * Holds the plugin instance.
	 *
	 * @since 1.0.0
	 * @access public
	 * @static
	 *
	 * @var Plugin
	 */
	public static $instance = null;
	
	
	/**
	 * Admin.
	 *
	 * Holds the plugin admin.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @var admin
	 */
	public $dashboard;
	
	
	/**
	 * Frontend.
	 *
	 * Holds the plugin frontend.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @var admin
	 */
	public $frontend;


	/**
	 * Clone.
	 *
	 * Disable class cloning and throw an error on object clone.
	 *
	 * The whole idea of the singleton design pattern is that there is a single
	 * object. Therefore, we don't want the object to be cloned.
	 *
	 * @access public
	 * @since 1.0.0
	 */
	public function __clone() {
		// Cloning instances of the class is forbidden.
		_doing_it_wrong( __FUNCTION__, esc_html__( 'Cheatin&#8217; huh?', 'bookmify' ), '1.0.0' );
	}

	/**
	 * Wakeup.
	 *
	 * Disable unserializing of the class.
	 *
	 * @access public
	 * @since 1.0.0
	 */
	public function __wakeup() {
		// Unserializing instances of the class is forbidden.
		_doing_it_wrong( __FUNCTION__, esc_html__( 'Cheatin&#8217; huh?', 'bookmify' ), '1.0.0' );
	}

	
	
	/**
	 * Plugin constructor.
	 *
	 * Initializing Bookmify plugin.
	 *
	 * @since 1.0.0
	 * @access private
	 */
	private function __construct() 
	{
		$this->register_autoloader();
		$this->register_includes();
		$this->init_all();
	}

	
	/**
	 * Register autoloader.
	 *
	 * Bookmify autoloader loads all the classes needed to run the plugin.
	 *
	 * @since 1.0.0
	 * @access private
	 */
	private function register_autoloader() {
		require( BOOKMIFY_PATH . 'inc/autoloader.php' );
		Autoloader::run();
	}
	
	
	/**
	 * Register includes.
	 *
	 * @since 1.0.0
	 * @access private
	 */
	private function register_includes(){
		require( BOOKMIFY_PATH . 'inc/functions.php' );
	}

	/**
	 * Init.
	 *
	 * Initialize Bookmify Plugin.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function init_all()
	{

		add_action( 'plugins_loaded', [ $this, 'init_components' ] );

	}
	
	/**
	 * Init.
	 *
	 * Initialize Bookmify Plugin.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public static function init_components() 
	{
		
		
		
		new Admin();
		new Frontend();
		
	}



	/**
	 * Instance.
	 *
	 * Ensures only one instance of the plugin class is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 * @access public
	 * @static
	 *
	 * @return Plugin An instance of the class.
	 */
	public static function get_instance() 
	{
		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}
		return self::$instance;
	}

	
}

Plugin::get_instance();