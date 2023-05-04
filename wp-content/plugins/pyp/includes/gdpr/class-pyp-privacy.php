<?php

/*
 * GDPR Compliance
 */
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

if (!class_exists('PYP_Privacy')) :

    /**
     * PYP_Privacy class
     */
    class PYP_Privacy {

        /**
         * PYP_Privacy constructor.
         */
        public function __construct() {
            $this->init_hooks();
        }

        /**
         * Register WooCommerce Pay Your Price
         */
        public function init_hooks() {
            // This hook registers WooCommerce Pay Your Price privacy content
            add_action('admin_init', array(__CLASS__, 'register_privacy_content'), 20);
        }

        /**
         * Register WooCommerce Pay Your Price Privacy Content
         */
        public static function register_privacy_content() {
            if (!function_exists('wp_add_privacy_policy_content')) {
                return;
            }

            $content = self::get_privacy_message();
            if ($content) {
                wp_add_privacy_policy_content(__('WooCommerce Pay Your Price', 'payyourprice'), $content);
            }
        }

        /**
         * Prepare Privacy Content
         */
        public static function get_privacy_message() {
            $content = '<p>' . __('This includes the basics of what personal data your store may be '
                            . 'collecting, storing and sharing. Depending on what settings are enabled and '
                            . 'which additional plugins are used, the specific information shared by your store will vary.', 'payyourprice') . '</p>'
                    . '<h2>' . __('WHAT THE PLUGIN DOES', 'payyourprice') . '</h2>'
                    . '<p>' . __('This plugin allows the users to purchase a product by paying the price they wish.', 'payyourprice') . '</p>'
                    . '<h2>' . __('WHAT WE COLLECT AND STORE', 'payyourprice') . '</h2>'
                    . '<p>' . __('This Plugin does not store any Personal Information from the user.', 'payyourprice') . '</p>';

            return $content;
        }

    }

    new PYP_Privacy();

endif;
