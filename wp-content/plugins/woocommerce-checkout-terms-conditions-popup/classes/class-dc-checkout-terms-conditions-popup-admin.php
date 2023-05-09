<?php
class DC_Checkout_Terms_Conditions_Popup_Admin {
  
  public $settings;

	public function __construct() {
		//admin script and style
		add_action('admin_enqueue_scripts', array(&$this, 'enqueue_admin_script'));
		
		$this->load_class('settings');
		$this->settings = new DC_Checkout_Terms_Conditions_Popup_Settings();
	}

	function load_class($class_name = '') {
	  global $DC_Checkout_Terms_Conditions_Popup;
		if ('' != $class_name) {
			require_once ($DC_Checkout_Terms_Conditions_Popup->plugin_path . '/admin/class-' . esc_attr($DC_Checkout_Terms_Conditions_Popup->token) . '-' . esc_attr($class_name) . '.php');
		} // End If Statement
	}// End load_class()
	

	/**
	 * Admin Scripts
	 */

	public function enqueue_admin_script() {
		global $DC_Checkout_Terms_Conditions_Popup;
		$screen = get_current_screen();
		
		// Enqueue admin script and stylesheet from here
		if (in_array( $screen->id, array( 'woocommerce_page_wc-settings' ))) :   
		  $DC_Checkout_Terms_Conditions_Popup->library->load_colorpicker_lib();
		  wp_enqueue_script('admin_js', $DC_Checkout_Terms_Conditions_Popup->plugin_url.'assets/admin/js/admin.js', array('jquery'), $DC_Checkout_Terms_Conditions_Popup->version, true);
		  wp_enqueue_style('admin_css',  $DC_Checkout_Terms_Conditions_Popup->plugin_url.'assets/admin/css/admin.css', array(), $DC_Checkout_Terms_Conditions_Popup->version);
	  endif;
	}
}