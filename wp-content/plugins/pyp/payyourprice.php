<?php

/*
 * Plugin Name: WooCommerce Pay Your Price
 * Description: Pay Your Price is a Woocommerce Extension Plugin using which users can pay their own price for the products. You can force users to pay within a range by giving a Minimum and Maximum Price.
 * Version: 8.5
 * Author: Fantastic Plugins
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

if (!class_exists('PayYourPrice')) :

    /** WooCommerce Pay Your Price.
     * 
     * @class PayYourPrice
     * @category Class
     */
    final class PayYourPrice {

        /**
         * WooCommerce PayYourPrice version.
         * 
         * @var string 
         */
        public $version = '8.5';

        /**
         * PayYourPrice constructor.
         */
        public function __construct() {

            //Include once will help to avoid fatal error by load the files when you call init hook.
            include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
            //Prevent Header Problem.
            add_action('init', array($this, 'prevent_header_already_sent_problem'), 1);
            //Display warning if woocommerce is not active.
            add_action('init', array($this, 'woocommerce_dependency_warning_message'));

            if (!$this->is_woocommerce_active()) {
                return;  // Return to stop the existing function to be call 
            }
            //Register String Translation.
            add_action('plugins_loaded', array($this, 'set_language_to_translate'));

            $this->define_constants();
            $this->include_files();

            //For Troubleshooting alter WooCommerce Template Path.
            if ('1' === get_option('pp_load_woocommerce_template')) {
                add_filter('woocommerce_locate_template', array($this, 'alter_woocommerce_template_path'), 10, 3);
            }
        }

        public function woocommerce_dependency_warning_message() {
            if (!$this->is_woocommerce_active() && is_admin()) {
                $error = "<div class='error'><p> WooCommerce Pay Your Price will not work until WooCommerce Plugin is Activated. Please Activate the WooCommerce Plugin !!! </p></div>";
                echo $error;
            }
            return;
        }

        /**
         * Prevent header problem while plugin activates.
         */
        public function prevent_header_already_sent_problem() {
            ob_start();
        }

        /**
         * Check WooCommerce Plugin is Active.
         * @return boolean
         */
        public function is_woocommerce_active() {
            if (is_multisite() && !is_plugin_active_for_network('woocommerce/woocommerce.php') && !is_plugin_active('woocommerce/woocommerce.php')) {
                return false;
            } else if (!is_plugin_active('woocommerce/woocommerce.php')) {
                return false;
            }
            return true;
        }

        /**
         * Define constants.
         */
        public function define_constants() {
            define('FP_PYP_PLUGIN_FILE', __FILE__);
            define('FP_PYP_TEMPLATE_PATH', plugin_dir_path(__FILE__) . 'templates/');
            define('FP_PYP_PLUGIN_URL', untrailingslashit(plugins_url('/', __FILE__)));
            define('FP_PYP_VERSION', $this->version);
        }

        /**
         *  Load language files. 
         */
        public function set_language_to_translate() {
            load_plugin_textdomain('payyourprice', false, dirname(plugin_basename(__FILE__)) . '/languages');
        }

        /**
         * Include Plugin files.
         */
        public function include_files() {
            include_once "includes/gdpr/class-pyp-privacy.php";
            include_once "includes/tabs/class-pyp-settings-tab.php";
            include_once "includes/class-pyp-frontend.php";
            include_once "includes/class-pyp-enqueues.php";
            include_once "includes/class-pyp-load-ajax.php";
            include_once "includes/class-pyp-product-settings.php";
        }

        /**
         * Troubleshoot - Alter Woocommerce template path.
         * @param string $template
         * @param string $template_name
         * @param string $template_path
         * @return string
         */
        public function alter_woocommerce_template_path($template, $template_name, $template_path) {
            if (is_product()) {
                $plugin_path = untrailingslashit(WP_PLUGIN_DIR) . '/woocommerce/templates/';

                if (file_exists($plugin_path . $template_name)) {
                    $template = $plugin_path . $template_name;
                    return $template;
                }
            }
            return $template;
        }

    }

    new PayYourPrice();
endif ;