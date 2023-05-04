<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

if (!class_exists('FP_PYP_Enqueues')) :

    /**
     * Handle PYP enqueues.
     * 
     * @class FP_PYP_Enqueues
     * @category Class
     */
    class FP_PYP_Enqueues {

        /**
         * FP_PYP_Enqueues constructor.
         */
        public function __construct() {
            add_action('admin_enqueue_scripts', array($this, 'admin_script'));
            add_action('wp_enqueue_scripts', array($this, 'frontend_script'));
        }

        /**
         * Perform script localization in backend.
         * @global object $post
         */
        public function admin_script() {
            global $post, $woocommerce;

            wp_enqueue_script('jquery');

            if (isset($post->ID) && get_post_type($post->ID) === 'product') {
                wp_register_script('fp_pyp_product_settings', FP_PYP_PLUGIN_URL . '/assets/js/admin/admin-pyp-product-settings.js');

                wp_localize_script('fp_pyp_product_settings', 'fp_pyp_product_settings_obj', array());

                wp_enqueue_script('fp_pyp_product_settings');
            }

            if (isset($_GET['tab']) && $_GET['tab'] === 'payyourprice') {
                wp_enqueue_script('fp_pyp_jscolor', FP_PYP_PLUGIN_URL . '/assets/js/jscolor/jscolor.js');

                wp_register_script('fp_pyp_tab_settings', FP_PYP_PLUGIN_URL . '/assets/js/admin/admin-pyp-tab-settings.js');

                wp_localize_script('fp_pyp_tab_settings', 'fp_pyp_tab_settings_obj', array(
                    'wp_ajax_url' => admin_url('admin-ajax.php'),
                    'create_nonce' => wp_create_nonce("search-products"),
                    'reload_after_bulk_update' => get_option('pp_reload_page_after_bulk_update') != '2' ? true : false,
                    'is_lower_wc_version' => (float) $woocommerce->version <= (float) ('2.2.0')
                ));

                wp_enqueue_script('fp_pyp_tab_settings');
            }
        }

        /**
         * Perform script localization in frontend.
         */
        public function frontend_script() {
            global $post;

            wp_enqueue_script('jquery');

            if (is_shop()) {
                wp_register_script('fp_pyp_shop_page', FP_PYP_PLUGIN_URL . '/assets/js/frontend/shop-page.js', array('jquery'));

                wp_localize_script('fp_pyp_shop_page', 'fp_pyp_shop_page_obj', array(
                    'wp_ajax_url' => admin_url('admin-ajax.php'),
                    'product_id' => $post->ID,
                    'product_url' => get_permalink($post->ID),
                    'is_pyp_product' => get_post_meta($post->ID, '_checkboxvalue', true) === 'yes'
                ));

                wp_enqueue_script('fp_pyp_shop_page');
            }

            if (is_product()) {
                pyp_enqueue_single_product_page($post->ID);
                wp_register_style('pyp_enqueue_styles', plugins_url('pyp/assets/css/frontend.css'));
                wp_enqueue_style('pyp_enqueue_styles');
            } elseif (is_page()) {
                if (strpos($post->post_content, 'product_page id') !== false) {
                    $start = "product_page id=";
                    $end = "]";
                    $id = pyp_get_product_id_from_shortcode_page($post->post_content, $start, $end);
                    $product_id = intval(preg_replace('/[^0-9]+/', '', $id));
                    pyp_enqueue_single_product_page($product_id);
                }
            }
        }

    }

    new FP_PYP_Enqueues();

    function pyp_get_product_id_from_shortcode_page($string, $start, $end) {
        $pos = stripos($string, $start);
        $str = substr($string, $pos);
        $str_two = substr($str, strlen($start));
        $second_pos = stripos($str_two, $end);
        $str_three = substr($str_two, 0, $second_pos);
        $unit = trim($str_three); // remove whitespaces
        return $unit;
    }

    function pyp_enqueue_single_product_page($post_id) {
        if (get_post_type($post_id) == 'product') {
            $product = sumo_pyp_get_product($post_id);
            $product_type = sumo_pyp_get_product_type($product);
            $product_addons = fp_pyp_check_product_addons_enabled($post_id);
            $default_variation_id = (int) fp_default_variation_id($product);
            $pyp_enabled_variation_ids = fp_pyp_enabled_variation_ids($product);
            wp_register_script('fp_pyp_single_product_page', FP_PYP_PLUGIN_URL . '/assets/js/frontend/single-product-page.js', array('jquery'), FP_PYP_VERSION);
            wp_localize_script('fp_pyp_single_product_page', 'fp_pyp_single_product_page_obj', array(
                'wp_ajax_url' => admin_url('admin-ajax.php'),
                'product_id' => $post_id,
                'default_variation_id' => $default_variation_id,
                'is_pyp_product' => get_post_meta($post_id, '_checkboxvalue', true) === 'yes',
                'pyp_enabled_variation_ids' => $pyp_enabled_variation_ids,
                'product_type' => $product_type,
                'wc_currency_symbol' => get_woocommerce_currency_symbol(),
                'input_price_type' => get_post_meta($post_id, '_input_price_type', true),
                'show_r_hide_price' => get_option('pp_show_hide_price'),
                'min_price_tab_product' => get_option('min_price_tab_product'),
                'max_price_tab_product' => get_option('maximum_price_tab_product'),
                'min_price_error_message' => get_option('min_price_error_msg'),
                'max_price_error_message' => get_option('max_price_error_msg'),
                'input_error_message' => get_option('empty_input_field_error_message'),
                'radio_error_message' => get_option('empty_radio_error_message'),
                'product_addons' => $product_addons, //product Addon Comaptibility
            ));

            wp_enqueue_script('fp_pyp_single_product_page');
        }
    }

    function fp_pyp_enabled_variation_ids($product) {
        $array = array();
        if ($product->is_type('variable')) {
            $available_variation = $product->get_available_variations();
            foreach ($available_variation as $each_variation) {
                if (get_post_meta($each_variation['variation_id'], '_selectpayyourprice', true) == 'two') {
                    $array[] = $each_variation['variation_id'];
                }
            }
        }
        return $array;
    }

    function fp_pyp_check_product_addons_enabled($post_id) {

        if (!function_exists('get_product_addons'))
            return false;

        $product_addons = get_product_addons($post_id);
        if (!is_array($product_addons) || !count($product_addons) > 0)
            return false;

        return true;
    }







endif;