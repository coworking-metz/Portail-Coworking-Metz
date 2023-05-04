<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

if (!class_exists('FP_PYP_Product_Settings')) :

    /**
     * Handle PYP Settings in Product Backend.
     *
     * @class FP_PYP_Product_Settings
     * @category Class
     */
    class FP_PYP_Product_Settings {

        /**
         * FP_PYP_Product_Settings constructor.
         */
        public function __construct() {
            //For Simple Product.
            add_action('woocommerce_product_options_general_product_data', array($this, 'product_settings'));
            add_action('woocommerce_process_product_meta', array($this, 'save_product_data'));

            //For Variable Product.
            add_action('woocommerce_product_after_variable_attributes', array($this, 'variation_product_settings'), 10, 3);
            add_action('woocommerce_save_product_variation', array($this, 'save_variation_data'), 10, 2);

            add_filter('wpml-translation-editor-fetch-job', array($this, 'save_variation_data_on_translated_products'), 999, 2);
        }

        /**
         * Display Product Setting Fields.
         */
        public static function product_settings() {
            ?>
            <div class="show_if_simple">
                <?php
                woocommerce_wp_select(array(
                    'id' => '_checkboxvalue',
                    'wrapper_class' => '',
                    'label' => __('Enable Pay Your Price', 'payyourprice'),
                    'description' => __('Enable Pay Your Price if you want users to pay their own price for this product', 'payyourprice'),
                    'options' => array(
                        '' => __('Choose Option', 'payyourprice'),
                        'yes' => __('Enable', 'payyourprice'),
                        'no' => __('Disable', 'payyourprice')
                    )
                ));

                woocommerce_wp_select(array(
                    'id' => '_input_price_type',
                    'wrapper_class' => '',
                    'label' => __('Input Field Type', 'payyourprice'),
                    'description' => __('Select input field type', 'payyourprice'),
                    'options' => array(
                        'number_input' => __('Number (or) Text Field (Default)', 'payyourprice'),
                        'text_input' => __('Text Field', 'payyourprice'),
                        'radio' => __('Button', 'payyourprice'),
                        'dropdown' => __('Dropdown', 'payyourprice'),
                        'button_text' => __('Button & Text', 'payyourprice'),
                    )
                ));

                woocommerce_wp_text_input(array(
                    'id' => '_input_amount_for_pay',
                    'name' => '_input_amount_for_pay',
                    'wrapper class' => '',
                    'description' => __('comma seperated values', 'payyourprice'),
                    'label' => __('Amount (' . get_woocommerce_currency_symbol() . ')', 'payyourprice')
                ));

                woocommerce_wp_text_input(array(
                    'id' => '_getminimumprice',
                    'name' => '_getminimumprice',
                    'label' => __('Minimum Price (' . get_woocommerce_currency_symbol() . ')', 'payyourprice')
                ));

                woocommerce_wp_checkbox(array(
                    'id' => '_hideminimum',
                    'wrapper_class' => '',
                    'label' => __('Hide Minimum Price', 'payyourprice'),
                    'description' => ''
                ));

                woocommerce_wp_text_input(array(
                    'id' => '_getrecommendedprice',
                    'name' => '_getrecommendedprice',
                    'label' => __('Recommended Price (' . get_woocommerce_currency_symbol() . ')', 'payyourprice')
                ));

                woocommerce_wp_text_input(array(
                    'id' => '_getmaximumprice',
                    'name' => '_getmaximumprice',
                    'label' => __('Maximum Price (' . get_woocommerce_currency_symbol() . ')', 'payyourprice')
                ));

                woocommerce_wp_checkbox(array(
                    'id' => '_hidemaximum',
                    'wrapper_class' => '',
                    'label' => __('Hide Maximum Price', 'payyourprice'),
                    'description' => ''
                ));
                ?>
            </div>
            <?php
        }

        /**
         * Display Variation Product Setting Fields.
         * @param int $loop
         * @param mixed $variation_data
         * @param object $variation The Variation post ID
         */
        public static function variation_product_settings($loop, $variation_data, $variation) {
            $variation_data = get_post_meta($variation->ID);
            ?>
            <tr>
                <td>
                    <?php
                    woocommerce_wp_select(array(
                        'id' => '_selectpayyourprice[' . $loop . ']',
                        'label' => __('Enable Pay Your Price:', 'woocommerce'),
                        'value' => isset($variation_data['_selectpayyourprice'][0]) ? $variation_data['_selectpayyourprice'][0] : '',
                        'options' => array(
                            'one' => __('Choose Option', 'woocommerce'),
                            'two' => __('Enable', 'woocommerce'),
                            'three' => __('Disable', 'woocommerce')
                        )
                    ));

                    woocommerce_wp_select(array(
                        'id' => '_input_type[' . $loop . ']',
                        'wrapper_class' => '',
                        'value' => isset($variation_data['_input_type'][0]) ? $variation_data['_input_type'][0] : '',
                        'label' => __('Input Field Type:', 'payyourprice'),
                        'description' => __('Select input field type', 'payyourprice'),
                        'options' => array(
                            'number_input' => __('Number (or) Text Field (Default)', 'payyourprice'),
                            'text_input' => __('Text Field', 'payyourprice'),
                            'radio' => __('Button', 'payyourprice'),
                            'dropdown' => __('Dropdown', 'payyourprice'),
                            'button_text' => __('Button & Text', 'payyourprice'),
                        )
                    ));

                    woocommerce_wp_text_input(array(
                        'id' => '_input_for_pay[' . $loop . ']',
                        'value' => isset($variation_data['_input_for_pay'][0]) ? $variation_data['_input_for_pay'][0] : '',
                        'class' => 'input_for_pay',
                        'wrapper class' => '',
                        'description' => __('comma seperated values', 'payyourprice'),
                        'label' => __('Amount: (' . get_woocommerce_currency_symbol() . ')', 'payyourprice')
                    ));

                    woocommerce_wp_text_input(array(
                        'id' => '_minimumprice[' . $loop . ']',
                        'label' => __('Minimum Price:(' . get_woocommerce_currency_symbol() . ')', 'woocommerce'),
                        'class' => 'Miniumprice',
                        'description' => __('Please Enter Minimum Price', 'woocommerce'),
                        'value' => isset($variation_data['_minimumprice'][0]) ? $variation_data['_minimumprice'][0] : '',
                        'desc_tip' => 'true'
                    ));

                    woocommerce_wp_select(array(
                        'id' => '_hideminimumprice[' . $loop . ']',
                        'label' => __('Hide Minimum Price:', 'woocommerce'),
                        'value' => isset($variation_data['_hideminimumprice'][0]) ? $variation_data['_hideminimumprice'][0] : '',
                        'class' => 'Miniumprice_show/hide',
                        'options' => array(
                            '1' => __('Show', 'woocommerce'),
                            '2' => __('Hide', 'woocommerce')
                        )
                    ));

                    woocommerce_wp_text_input(array(
                        'id' => '_recommendedprice[' . $loop . ']',
                        'label' => __('Recommended Price:(' . get_woocommerce_currency_symbol() . ')', 'woocommerce'),
                        'desc_tip' => 'true',
                        'class' => 'Recommendprice',
                        'description' => __('Please Enter Recommended Price', 'woocommerce'),
                        'value' => isset($variation_data['_recommendedprice'][0]) ? $variation_data['_recommendedprice'][0] : ''
                    ));

                    woocommerce_wp_text_input(array(
                        'id' => '_maximumprice[' . $loop . ']',
                        'label' => __('Maximum Price:(' . get_woocommerce_currency_symbol() . ')', 'woocommerce'),
                        'desc_tip' => 'true',
                        'class' => 'Maximumprice',
                        'description' => __('Please Enter Maximum Price', 'woocommerce'),
                        'value' => isset($variation_data['_maximumprice'][0]) ? $variation_data['_maximumprice'][0] : ''
                    ));

                    woocommerce_wp_select(array(
                        'id' => '_hidemaximumprice[' . $loop . ']',
                        'label' => __('Hide Maximum Price:', 'woocommerce'),
                        'value' => isset($variation_data['_hidemaximumprice'][0]) ? $variation_data['_hidemaximumprice'][0] : '',
                        'class' => 'Maximumprice_show/hide',
                        'options' => array(
                            '1' => __('Show', 'woocommerce'),
                            '2' => __('Hide', 'woocommerce'),
                        )
                    ));
                    ?>
                </td>
            </tr>
            <?php
        }

        public static function fp_pyp_update_variation_metas($variation_id, $i) {
            global $sitepress;
            if (is_plugin_active('sitepress-multilingual-cms/sitepress.php') && is_object($sitepress)) {
                $trid = $sitepress->get_element_trid($variation_id);
                $translations = $sitepress->get_element_translations($trid);
                foreach ($translations as $translation) {
                    $id_from_other_lang = $translation->element_id;
                    self::save('', $id_from_other_lang, $i);
                }
            } else {
                self::save('', $variation_id, $i);
            }
        }

        /**
         * Save Product Data.
         * @param int $product_id The Product post ID
         */
        public static function save_product_data($product_id) {

            self::save($product_id);
        }

        /**
         * Save Variation Product Data.
         * @param int $variation_id The Variation post ID
         * @param int $i
         */
        public static function save_variation_data($variation_id, $i) {

            self::fp_pyp_update_variation_metas($variation_id, $i);
        }

        public static function save_variation_data_on_translated_products($job, $job_details) {
            if ($job_details['job_type'] == 'post_product') {
                if ($job_details['job_id']) {
                    $product_id = $job_details['job_id'];
                    $product = sumo_pyp_get_product($product_id);
                    $type = sumo_pyp_get_product_type($product);
                    if ($type == 'variable') {
                        $variations = $product->get_children();
                        foreach ($variations as $each_variation) {
                            $_selectpayyourprice = get_post_meta($each_variation, '_selectpayyourprice', true);
                            $_input_type = get_post_meta($each_variation, '_input_type', true);
                            $_input_for_pay = get_post_meta($each_variation, '_input_for_pay', true);
                            $_hideminimumprice = get_post_meta($each_variation, '_hideminimumprice', true);
                            $_hidemaximumprice = get_post_meta($each_variation, '_hidemaximumprice', true);
                            $_recommendedprice = get_post_meta($each_variation, '_recommendedprice', true);
                            $_minimumprice = get_post_meta($each_variation, '_minimumprice', true);
                            $_maximumprice = get_post_meta($each_variation, '_maximumprice', true);
                            global $sitepress;
                            if (is_plugin_active('sitepress-multilingual-cms/sitepress.php') && is_object($sitepress)) {
                                $trid = $sitepress->get_element_trid($each_variation);
                                $translations = $sitepress->get_element_translations($trid);
                                foreach ($translations as $translation) {
                                    $id_from_other_lang = $translation->element_id;
                                    update_post_meta($id_from_other_lang, '_selectpayyourprice', $_selectpayyourprice);
                                    update_post_meta($id_from_other_lang, '_input_type', $_input_type);
                                    update_post_meta($id_from_other_lang, '_input_for_pay', $_input_for_pay);
                                    update_post_meta($id_from_other_lang, '_hideminimumprice', $_hideminimumprice);
                                    update_post_meta($id_from_other_lang, '_hidemaximumprice', $_hidemaximumprice);
                                    update_post_meta($id_from_other_lang, '_recommendedprice', $_recommendedprice);
                                    update_post_meta($id_from_other_lang, '_minimumprice', $_minimumprice);
                                    update_post_meta($id_from_other_lang, '_maximumprice', $_maximumprice);
                                }
                            }
                        }
                    }
                }
            }
            return $job;
        }

        /**
         * Save datas.
         * @param int $product_id The Product post ID
         * @param int $variation_id The Variation post ID
         * @param int $i The Variation loop
         */
        public static function save($product_id, $variation_id = '', $i = '') {

            update_post_meta($product_id, '_checkboxvalue', $_POST['_checkboxvalue']);
            update_post_meta($product_id, '_input_price_type', $_POST['_input_price_type']);
            update_post_meta($product_id, '_input_amount_for_pay', $_POST['_input_amount_for_pay']);

            update_post_meta($product_id, '_getrecommendedprice', $_POST['_getrecommendedprice']);
            update_post_meta($product_id, '_getminimumprice', $_POST['_getminimumprice']);
            update_post_meta($product_id, '_getmaximumprice', $_POST['_getmaximumprice']);

            update_post_meta($product_id, '_hideminimum', isset($_POST['_hideminimum']) ? 'yes' : 'no' );
            update_post_meta($product_id, '_hidemaximum', isset($_POST['_hidemaximum']) ? 'yes' : 'no' );

            if (!empty($variation_id)) {
                if (isset($_POST['_selectpayyourprice'][$i])) {
                    update_post_meta($variation_id, '_selectpayyourprice', stripslashes($_POST['_selectpayyourprice'][$i]));
                }
                if (isset($_POST['_input_type'][$i])) {
                    update_post_meta($variation_id, '_input_type', stripslashes($_POST['_input_type'][$i]));
                }
                if (isset($_POST['_input_for_pay'][$i])) {
                    update_post_meta($variation_id, '_input_for_pay', stripslashes($_POST['_input_for_pay'][$i]));
                }
                if (isset($_POST['_hideminimumprice'][$i])) {
                    update_post_meta($variation_id, '_hideminimumprice', stripslashes($_POST['_hideminimumprice'][$i]));
                }
                if (isset($_POST['_hidemaximumprice'][$i])) {
                    update_post_meta($variation_id, '_hidemaximumprice', stripslashes($_POST['_hidemaximumprice'][$i]));
                }
                if (isset($_POST['_recommendedprice'][$i])) {
                    update_post_meta($variation_id, '_recommendedprice', stripslashes($_POST['_recommendedprice'][$i]));
                }
                if (isset($_POST['_minimumprice'][$i])) {
                    update_post_meta($variation_id, '_minimumprice', stripslashes($_POST['_minimumprice'][$i]));
                }
                if (isset($_POST['_maximumprice'][$i])) {
                    update_post_meta($variation_id, '_maximumprice', stripslashes($_POST['_maximumprice'][$i]));
                }
            }
        }

    }

    new FP_PYP_Product_Settings();

    function sumo_pyp_get_product($product_id) {
        if (function_exists('wc_get_product')) {
            $product = wc_get_product($product_id);
        } else {
            if (function_exists('get_product')) {
                $product = get_product($product_id);
            }
        }
        return $product;
    }

    function sumo_pyp_get_product_id($product) {
        if ((float) WC()->version >= (float) '3.0.0') {
            $product_id = $product->get_id();
        } else {
            $product_id = $product->variation_id ? $product->variation_id : $product->id;
        }
        return $product_id;
    }

    function sumo_pyp_get_product_type($product) {
        if ((float) WC()->version >= (float) '3.0.0') {
            $type = $product->get_type();
        } else {
            $type = $product->product_type;
        }
        return $type;
    }

    function sumo_pyp_wc_price($price) {
        if (function_exists('wc_price')) {
            $wc_price = wc_price($price);
        } else {
            if (function_exits('woocommerce_price')) {
                $wc_price = woocommerce_price($price);
            }
        }
        return $wc_price;
    }

    function sumo_pyp_get_variation_attributes($product) {
        if ((float) WC()->version < (float) '3.0.0') {
            $attributes = $product->get_variation_default_attributes();
        } else {
            $attributes = $product->get_default_attributes();
        }
        return $attributes;
    }







































































endif;
