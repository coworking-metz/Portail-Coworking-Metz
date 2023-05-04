<?php

/**
 * PYP Settings Tab.
 * 
 * @class FP_PYP_Settings_Tab
 * @category Class
 */
class FP_PYP_Settings_Tab {

    /**
     * FP_PYP_Settings_Tab constructor.
     */
    public function __construct() {
        // Register a New Tab.
        add_filter( 'woocommerce_settings_tabs_array' , array ( $this , 'init_tab' ) , 100 ) ;
        // Register the Admin Settings.
        add_action( 'woocommerce_settings_tabs_payyourprice' , array ( $this , 'register_admin_settings' ) ) ;
        // Update default settings on Page Load.
        add_action( 'admin_init' , array ( $this , 'init_tab_settings' ) ) ;
        //Update Tab Settings.
        add_action( 'woocommerce_update_options_payyourprice' , array ( $this , 'update_tab_settings' ) ) ;

        //Admin Custom Fields.
        add_action( 'woocommerce_admin_field_existing_product_update_button' , array ( $this , 'save_update_button' ) ) ;
        add_action( 'woocommerce_admin_field_pp_select_particular_products' , array ( $this , 'display_product_selection' ) ) ;
    }

    /**
     * Function to Define Name of the Tab.
     * @param array $setting_tabs
     * @return array
     */
    public static function init_tab( $setting_tabs ) {
        $setting_tabs[ 'payyourprice' ] = __( 'Pay Your Price' , 'sumosubscriptions' ) ;
        return $setting_tabs ;
    }

    /**
     * Registering Custom Field Admin Settings.
     */
    public static function register_admin_settings() {
        woocommerce_admin_fields( FP_PYP_Settings_Tab::default_settings() ) ;
    }

    /**
     * Initialize the Default Settings by looping this function.
     */
    public static function init_tab_settings() {
        if ( version_compare( WC_VERSION , ( float ) '2.4.0' , '<' ) ) {
            add_action( 'woocommerce_update_option_pp_select_particular_products' , array ( 'FP_PYP_Settings_Tab' , 'update_bulk_settings' ) ) ;
        } else {
            add_filter( 'woocommerce_admin_settings_sanitize_option_pp_select_particular_products' , array ( 'FP_PYP_Settings_Tab' , 'update_bulk_settings_hv_wc' ) , 10 , 3 ) ;
        }
        foreach ( FP_PYP_Settings_Tab::default_settings() as $setting )
            if ( isset( $setting[ 'newids' ] ) && isset( $setting[ 'std' ] ) ) {
                add_option( $setting[ 'newids' ] , $setting[ 'std' ] ) ;
            }
    }

    /**
     * Update the Settings on Save Changes.
     */
    public static function update_tab_settings() {
        woocommerce_update_options( FP_PYP_Settings_Tab::default_settings() ) ;
        $pyp_select_particular_products = isset( $_POST[ 'pp_select_particular_products' ] ) ? $_POST[ 'pp_select_particular_products' ] : '' ;

        update_option( 'pp_select_particular_products' , $pyp_select_particular_products ) ;
    }

    /**
     * Woo Backward Compatibility - Update Bulk Settings on Save Changes.
     */
    public static function update_bulk_settings() {
        $pyp_select_particular_products = isset( $_POST[ 'pp_select_particular_products' ] ) ? $_POST[ 'pp_select_particular_products' ] : '' ;
        update_option( 'pp_select_particular_products' , $pyp_select_particular_products ) ;
    }

    public static function update_bulk_settings_hv_wc( $value , $option , $raw_value ) {
        if ( isset( $_POST[ 'pp_select_products_categories' ] ) && $_POST[ 'pp_select_products_categories' ] == '2' ) {
            update_option( $option[ 'id' ] , $value ) ;
        }
    }

    /**
     * Add Default Settings.
     * @return array
     */
    public static function default_settings() {
        return apply_filters( 'woocommerce_payyourprice_settings' , array (
            array (
                'name' => __( 'Single Product Page Label' , 'payyourprice' ) ,
                'type' => 'title' ,
                'desc' => '' ,
                'id'   => '_product_button_text'
            ) ,
            array (
                'name'     => __( 'Minimum Price Label' , 'payyourprice' ) ,
                'desc'     => __( 'Please Enter Minimum Price Label for Product Page' ) ,
                'tip'      => '' ,
                'id'       => 'min_price_tab_product' ,
                'css'      => 'min-width:550px;' ,
                'std'      => 'Minimum Price' ,
                'type'     => 'text' ,
                'newids'   => 'min_price_tab_product' ,
                'desc_tip' => true ,
            ) ,
            array (
                'name'     => __( 'Maximum Price Label' , 'payyourprice' ) ,
                'desc'     => __( 'Please Enter Maximum Price Label for Product Page' ) ,
                'tip'      => '' ,
                'css'      => 'min-width:550px;' ,
                'id'       => 'maximum_price_tab_product' ,
                'std'      => 'Maximum Price' ,
                'type'     => 'text' ,
                'newids'   => 'maximum_price_tab_product' ,
                'desc_tip' => true ,
            ) ,
            array (
                'name'     => __( 'Pay Your Price Label' , 'payyourprice' ) ,
                'desc'     => __( 'Please Enter Pay Your Price Label' ) ,
                'tip'      => '' ,
                'css'      => 'min-width:550px;margin-bottom:30px;' ,
                'id'       => 'payyourprice_price_tab_product' ,
                'std'      => 'Pay Your Price' ,
                'type'     => 'text' ,
                'newids'   => 'payyourprice_price_tab_product' ,
                'desc_tip' => true ,
            ) ,
            array (
                'name'    => __( 'Label for Predefined Buttons Show/Hide' , 'payyourprice' ) ,
                'desc'    => __( 'Show or Hide the Caption for Predefined Buttons' , 'payyourprice' ) ,
                'tip'     => '' ,
                'id'      => 'pyp_predefined_button_caption_show_hide' ,
                'css'     => 'min-width:150px;' ,
                'std'     => '2' , // WooCommerce < 2.0
                'default' => '2' , // WooCommerce >= 2.0
                'newids'  => 'pyp_predefined_button_caption_show_hide' ,
                'type'    => 'select' ,
                'options' => array (
                    '1' => __( 'Show' , 'payyourprice' ) ,
                    '2' => __( 'Hide' , 'payyourprice' ) ,
                ) ,
            ) ,
            array (
                'name'     => __( 'Predefined Buttons Label' , 'payyourprice' ) ,
                'desc'     => __( 'Change Predefined Buttons Caption in Single Product Page by your Custom Words' , 'payyourprice' ) ,
                'tip'      => '' ,
                'css'      => 'min-width:550px;' ,
                'id'       => 'pyp_predefined_button_caption' ,
                'std'      => 'Contribute from any of the Predefined Amounts' ,
                'type'     => 'text' ,
                'newids'   => 'pyp_predefined_button_caption' ,
                'desc_tip' => true ,
            ) ,
            array (
                'name'    => __( 'Amount You Wish to Contribute Label Show/Hide' , 'payyourprice' ) ,
                'desc'    => __( 'Show or Hide the Label for Amount You Wish to Contribute' , 'payyourprice' ) ,
                'id'      => 'pyp_amount_you_wish_show_hide' ,
                'css'     => 'min-width:150px;' ,
                'std'     => '2' , // WooCommerce < 2.0
                'default' => '2' , // WooCommerce >= 2.0
                'newids'  => 'pyp_amount_you_wish_show_hide' ,
                'type'    => 'select' ,
                'options' => array (
                    '1' => __( 'Show' , 'galaxyfunder' ) ,
                    '2' => __( 'Hide' , 'galaxyfunder' ) ,
                ) ,
            ) ,
            array (
                'name'     => __( 'Predefined Buttons & Editable Texbox Label Line1' , 'payyourprice' ) ,
                'tip'      => '' ,
                'css'      => 'min-width:550px;' ,
                'id'       => 'pyp_label_for_button_line1' ,
                'std'      => 'or' ,
                'type'     => 'text' ,
                'newids'   => 'pyp_label_for_button_line1' ,
                'desc_tip' => true ,
            ) ,
            array (
                'name'     => __( 'Predefined Buttons & Editable Texbox Label Line2' , 'payyourprice' ) ,
                'desc'     => __( 'Change the Amount you Wish to Contribute Label in Single Product Page by your Custom Words' , 'payyourprice' ) ,
                'tip'      => '' ,
                'css'      => 'min-width:550px;' ,
                'id'       => 'pyp_label_for_button_line2' ,
                'std'      => 'Contribute the Amount You Wish' ,
                'type'     => 'text' ,
                'newids'   => 'pyp_label_for_button_line2' ,
                'desc_tip' => true ,
            ) ,
            array ( 'type' => 'sectionend' , 'id' => '_product_button_text' ) ,
            array (
                'name' => __( 'Shop Page Label' , 'payyourprice' ) ,
                'type' => 'title' ,
                'desc' => '' ,
                'id'   => 'shop_button_text'
            ) ,
            array (
                'name'     => __( 'Pay Your Price Label' , 'payyourprice' ) ,
                'desc'     => __( 'Please Enter Pay Your Price Label' ) ,
                'tip'      => '' ,
                'css'      => 'min-width:550px;margin-bottom:30px;' ,
                'id'       => 'payyourprice_price_tab_shop' ,
                'std'      => 'Pay Your Price' ,
                'type'     => 'text' ,
                'newids'   => 'payyourprice_price_tab_shop' ,
                'desc_tip' => true ,
            ) ,
            array ( 'type' => 'sectionend' , 'id' => 'shop_button_text' ) ,
            array (
                'name' => __( 'Button Settings' , 'payyourprice' ) ,
                'type' => 'title' ,
                'desc' => 'This settings applicable only when you choose Button Type in Single Product Page' ,
                'id'   => '_pyp_button_settings'
            ) ,
            array (
                'name'     => __( 'Button color ' , 'payyourprice' ) ,
                'desc'     => __( 'Please enter the Button color' , 'payyourprice' ) ,
                'tip'      => '' ,
                'id'       => 'pyp_button_color' ,
                'css'      => 'min-width:75px;' ,
                'std'      => 'DDDDDD' ,
                'default'  => 'DDDDDD' ,
                'newids'   => 'pyp_button_color' ,
                'type'     => 'text' ,
                'class'    => 'color' ,
                'desc_tip' => true ,
            ) ,
            array (
                'name'     => __( 'Button Text color ' , 'payyourprice' ) ,
                'desc'     => __( 'Please enter the Button Text color' , 'payyourprice' ) ,
                'tip'      => '' ,
                'id'       => 'pyp_button_text_color' ,
                'css'      => 'min-width:75px;' ,
                'std'      => '#000' ,
                'default'  => '#000' ,
                'newids'   => 'pyp_button_text_color' ,
                'type'     => 'text' ,
                'class'    => 'color' ,
                'desc_tip' => true ,
            ) ,
            array (
                'name'     => __( 'Selected Button color ' , 'payyourprice' ) ,
                'desc'     => __( 'Please enter the Selected Button color' , 'payyourprice' ) ,
                'tip'      => '' ,
                'id'       => 'pyp_selected_button_color' ,
                'css'      => 'min-width:75px;' ,
                'std'      => '#f00' ,
                'default'  => '#f00' ,
                'newids'   => 'pyp_selected_button_color' ,
                'type'     => 'text' ,
                'class'    => 'color' ,
                'desc_tip' => true ,
            ) ,
            array (
                'name'     => __( 'Selected Button Text color ' , 'payyourprice' ) ,
                'desc'     => __( 'Please enter the Selected Button Text color' , 'payyourprice' ) ,
                'tip'      => '' ,
                'id'       => 'pyp_selected_button_text_color' ,
                'css'      => 'min-width:75px;' ,
                'std'      => '#fff' ,
                'default'  => '#fff' ,
                'newids'   => 'pyp_selected_button_text_color' ,
                'type'     => 'text' ,
                'class'    => 'color' ,
                'desc_tip' => true ,
            ) ,
            array (
                'name'     => __( 'Button Shadow' , 'payyourprice' ) ,
                'desc'     => __( 'Button Shadow option will only when you choose Button Selection in Single Product Page' , 'payyourprice' ) ,
                'tip'      => '' ,
                'id'       => 'pyp_button_box_shadow' ,
                'std'      => '1' ,
                'type'     => 'radio' ,
                'options'  => array ( '1' => 'Show' , '2' => 'Hide' ) ,
                'newids'   => 'pyp_button_box_shadow' ,
                'desc_tip' => true ,
            ) ,
            array ( 'type' => 'sectionend' , 'id' => '_pyp_button_settings' ) ,
            array (
                'name' => __( 'Error Message Settings' , 'payyourprice' ) ,
                'type' => 'title' ,
                'desc' => 'Shortcode Available (Supported for Simple Products)<br> <pre> [pyp_min_price] => Minimum Price </pre><pre> [pyp_max_price] => Maximum Price </pre>' ,
                'id'   => '_product_error_settings'
            ) ,
            array (
                'name'     => __( 'Minimum Price Error Message' , 'payyourprice' ) ,
                'desc'     => __( 'Please Enter Minimum Price Error Message' ) ,
                'tip'      => '' ,
                'id'       => 'min_price_error_msg' ,
                'css'      => 'min-width:550px;' ,
                'std'      => 'Please Enter Minimum Price' ,
                'type'     => 'text' ,
                'newids'   => 'min_price_error_msg' ,
                'desc_tip' => true ,
            ) ,
            array (
                'name'     => __( 'Maximum Price Error Message' , 'payyourprice' ) ,
                'desc'     => __( 'Please Enter Maximum Price Error Message' ) ,
                'tip'      => '' ,
                'css'      => 'min-width:550px;' ,
                'id'       => 'max_price_error_msg' ,
                'std'      => 'Price should not be more than Maximum Price' ,
                'type'     => 'text' ,
                'newids'   => 'max_price_error_msg' ,
                'desc_tip' => true ,
            ) ,
            array (
                'name'     => __( 'Input Price Error Message' , 'payyourprice' ) ,
                'desc'     => __( 'Please Enter your Input Error Message' ) ,
                'tip'      => '' ,
                'id'       => 'input_price_error_message' ,
                'css'      => 'min-width:550px;' ,
                'std'      => 'Please Enter Only Numbers' ,
                'type'     => 'text' ,
                'newids'   => 'input_price_error_message' ,
                'desc_tip' => true ,
            ) ,
            array (
                'name'     => __( 'Empty Field Error Message' , 'payyourprice' ) ,
                'desc'     => __( 'Please Enter Empty Field Error Message' , 'payyourprice' ) ,
                'tip'      => '' ,
                'id'       => 'empty_input_field_error_message' ,
                'css'      => 'min-width:550px;' ,
                'std'      => 'Contribution Field cannot be empty' ,
                'type'     => 'text' ,
                'newids'   => 'empty_input_field_error_message' ,
                'desc_tip' => true ,
            ) ,
            array (
                'name'     => __( 'Button Error Message' , 'payyourprice' ) ,
                'desc'     => __( 'Button Error Message works only when you choose Button Type from Single Product Page, It works when user haven\'t select any amount in frontend' , 'payyourprice' ) ,
                'tip'      => '' ,
                'id'       => 'empty_radio_error_message' ,
                'css'      => 'min-width:550px;' ,
                'std'      => 'Please Select the Amount' ,
                'type'     => 'text' ,
                'newids'   => 'empty_radio_error_message' ,
                'desc_tip' => true ,
            ) ,
            array (
                'name'     => __( 'Display Error Message on' , 'payyourprice' ) ,
                'desc'     => __( 'This Controls where the Error Message should be Displayed' , 'woocommerce' ) ,
                'tip'      => '' ,
                'id'       => 'display_select_box_payyourprices' ,
                'css'      => 'min-width:150px;' ,
                'std'      => 'bottom' , // WooCommerce < 2.0
                'default'  => 'bottom' , // WooCommerce >= 2.0
                'newids'   => 'display_select_box_payyourprices' ,
                'type'     => 'select' ,
                'options'  => array (
                    'top'    => __( 'Above Price Text Box' , 'payyourprice' ) ,
                    'bottom' => __( 'Below Price Text Box' , 'payyourprice' ) ,
                ) ,
                'desc_tip' => true ,
            ) ,
            array (
                'name'     => __( 'Color Selection' , 'payyourprice' ) ,
                'desc'     => __( 'This Controls what Color Selection you want' , 'payyourprice' ) ,
                'tip'      => '' ,
                'id'       => 'payyourprice_color_selection' ,
                'css'      => 'min-width:150px;' ,
                'std'      => 'default' ,
                'default'  => 'default' ,
                'newids'   => 'payyourprice_color_selection' ,
                'type'     => 'select' ,
                'options'  => array (
                    'default' => __( 'Default Color' , 'payyourprice' ) ,
                    'custom'  => __( 'Custom Color' , 'payyourprice' ) ,
                ) ,
                'desc_tip' => true ,
            ) ,
            array (
                'name'     => __( 'Error Message Color' , 'payyourprice' ) ,
                'desc'     => __( 'This Controls how the Error Message is appear click to change the color' , 'payyourprice' ) ,
                'id'       => 'error_message_color_picker' ,
                'class'    => 'color' ,
                'css'      => 'min-width:150px; margin-bottom:30px;' ,
                'std'      => 'FF0000' ,
                'type'     => 'text' ,
                'newids'   => 'error_message_color_picker' ,
                'desc_tip' => true ,
            ) ,
            array ( 'type' => 'sectionend' , 'id' => '_product_error_settings' ) ,
            array (
                'name' => __( 'Single Product Page' , 'payyourprice' ) ,
                'type' => 'title' ,
                'id'   => '_product_redirection' ,
                'css'  => 'margin-bottom:700px;' ,
            ) ,
            array (
                'name'     => __( 'After Add to Cart Redirect To' , 'payyourprice' ) ,
                'tip'      => '' ,
                'id'       => 'pp_redirect_after_donate' ,
                'std'      => '1' ,
                'type'     => 'radio' ,
                'options'  => array ( '2' => 'Cart Page' , '1' => 'Checkout Page' , '3' => 'No Redirection' ) ,
                'newids'   => 'pp_redirect_after_donate' ,
                'desc_tip' => true ,
            ) ,
            array (
                'name'     => __( 'Show/Hide Regular/Sale Price in Product Page' , 'payyourprice' ) ,
                'tip'      => '' ,
                'id'       => 'pp_show_hide_price' ,
                'std'      => '2' ,
                'type'     => 'select' ,
                'options'  => array ( '1' => 'Show' , '2' => 'Hide' ) ,
                'newids'   => 'pp_show_hide_price' ,
                'desc_tip' => true ,
            ) ,
            array ( 'type' => 'sectionend' , 'id' => '_product_redirection' ) ,
            array ( 'type' => 'sectionend' , 'id' => '_product_error_settings' ) ,
            array (
                'name' => __( 'Add to Cart Button Settings' , 'payyourprice' ) ,
                'type' => 'title' ,
                'desc' => '' ,
                'id'   => '_product_buttons'
            ) ,
            array (
                'name'     => __( 'Enable Custom Add to Cart Button Caption' , 'payyourprice' ) ,
                'tip'      => '' ,
                'id'       => 'pp_enable_add_to_cart_button_caption' ,
                'css'      => '' ,
                'std'      => '' ,
                'type'     => 'checkbox' ,
                'newids'   => 'pp_enable_add_to_cart_button_caption' ,
                'desc_tip' => '' ,
            ) ,
            array (
                'name'     => __( 'Add to Cart Button Caption for Single Product' , 'payyourprice' ) ,
                'desc'     => __( 'Change the Caption for Add to Cart in Single Product Page' , 'payyourprice' ) ,
                'tip'      => '' ,
                'id'       => 'pp_add_to_cart_button_caption' ,
                'std'      => 'Add to cart' ,
                'type'     => 'text' ,
                'newids'   => 'pp_add_to_cart_button_caption' ,
                'desc_tip' => '' ,
            ) ,
            array ( 'type' => 'sectionend' , 'id' => '_product_buttons' ) ,
            array (
                'name' => __( 'Custom CSS' , 'payyourprice' ) ,
                'type' => 'title' ,
                'desc' => '' ,
                'id'   => '_product_custom_css'
            ) ,
            array (
                'name'     => __( 'Custom CSS' , 'payyourprice' ) ,
                'desc'     => __( 'Customize Pay Your Price Fields using Custom CSS' , 'payyourprice' ) ,
                'tip'      => '' ,
                'css'      => 'min-width:550px;min-height:260px;margin-bottom:80px;' ,
                'id'       => 'pp_product_custom_css' ,
                'std'      => '' ,
                'type'     => 'textarea' ,
                'newids'   => 'pp_product_custom_css' ,
                'desc_tip' => true ,
            ) ,
            array ( 'type' => 'sectionend' , 'id' => '_product_custom_css' ) ,
            array (
                'name' => __( 'Update Setting For Existing Products' , 'payyourprice' ) ,
                'type' => 'title' ,
                'desc' => __( 'Here you can update the following options for Existing Products' , 'payyourprice' ) ,
                'id'   => 'pp_update_settings_existing_products'
            ) ,
            array (
                'name'     => __( 'Select Products' , 'payyourprice' ) ,
                'desc'     => __( 'Enable/Disable Product Selection in Pay Your Price' , 'payyourprice' ) ,
                'id'       => 'pp_select_products_categories' ,
                'css'      => 'min-width:150px;' ,
                'std'      => '1' ,
                'class'    => 'pp_select_products_categories' ,
                'default'  => '1' ,
                'newids'   => 'pp_select_products_categories' ,
                'type'     => 'select' ,
                'options'  => array (
                    '1' => __( 'All Products' , 'payyourprice' ) ,
                    '2' => __( 'Selected Products' , 'payyourprice' )
                ) ,
                'desc_tip' => true ,
            ) ,
            array (
                'name'    => __( 'Select Particular Products' , 'payyourprice' ) ,
                'id'      => 'pp_select_particular_products' ,
                'desc'    => __( 'Select Particular Products in Pay Your Price' , 'payyourprice' ) ,
                'class'   => 'pp_select_particular_products ajax_chosen_select_products' ,
                'css'     => 'min-width:350px;' ,
                'std'     => array () ,
                'default' => array () ,
                'newids'  => 'pp_select_particular_products' ,
                'type'    => 'pp_select_particular_products' ,
            ) ,
            array (
                'name'     => __( 'Enable Pay Your Price' , 'payyourprice' ) ,
                'desc'     => __( 'Enable/Disable Product Selection in Pay Your Price' , 'payyourprice' ) ,
                'id'       => 'pp_enable_pay_your_price' ,
                'css'      => 'min-width:150px;' ,
                'std'      => 'no' ,
                'class'    => 'pp_enable_pay_your_price' ,
                'default'  => 'no' ,
                'newids'   => 'pp_enable_pay_your_price' ,
                'type'     => 'select' ,
                'options'  => array (
                    ''    => __( 'Choose Option' , 'payyourprice' ) ,
                    'yes' => __( 'Enable' , 'payyourprice' ) ,
                    'no'  => __( 'Disable' , 'payyourprice' ) ,
                ) ,
                'desc_tip' => true ,
            ) ,
            array (
                'name'     => __( 'Minimum Price(' . get_woocommerce_currency_symbol() . ')' , 'payyourprice' ) ,
                'desc'     => __( 'Please Enter Your Minimum Price' , 'payyourprice' ) ,
                'id'       => 'pp_minimum_price' ,
                'css'      => 'min-width:150px;' ,
                'std'      => '' ,
                'class'    => 'pp_minimum_price' ,
                'newids'   => 'pp_minimum_price' ,
                'type'     => 'text' ,
                'desc_tip' => true ,
            ) ,
            array (
                'name'     => __( 'Hide Minimum Price' , 'payyourprice' ) ,
                'tip'      => '' ,
                'id'       => 'pp_hide_minimum_price' ,
                'css'      => '' ,
                'std'      => 'no' ,
                'type'     => 'checkbox' ,
                'newids'   => 'pp_hide_minimum_price' ,
                'desc_tip' => true ,
            ) ,
            array (
                'name'     => __( 'Recommended Price(' . get_woocommerce_currency_symbol() . ')' , 'payyourprice' ) ,
                'desc'     => __( 'Please Enter Your Recommended Price' , 'payyourprice' ) ,
                'id'       => 'pp_recommended_price' ,
                'css'      => 'min-width:150px;' ,
                'std'      => '' ,
                'class'    => 'pp_recommended_price' ,
                'newids'   => 'pp_recommended_price' ,
                'type'     => 'text' ,
                'desc_tip' => true ,
            ) ,
            array (
                'name'     => __( 'Maximum Price(' . get_woocommerce_currency_symbol() . ')' , 'payyourprice' ) ,
                'desc'     => __( 'Please Enter Your Maximum Price' , 'payyourprice' ) ,
                'id'       => 'pp_maxmimum_price' ,
                'css'      => 'min-width:150px;' ,
                'std'      => '' ,
                'class'    => 'pp_maxmimum_price' ,
                'newids'   => 'pp_maxmimum_price' ,
                'type'     => 'text' ,
                'desc_tip' => true ,
            ) ,
            array (
                'name'   => __( 'Hide Maximum Price' , 'payyourprice' ) ,
                'id'     => 'pp_hide_maximum_price' ,
                'css'    => '' ,
                'std'    => 'no' ,
                'class'  => 'pp_hide_maximum_price' ,
                'newids' => 'pp_hide_maximum_price' ,
                'type'   => 'checkbox' ,
            ) ,
            array (
                'type' => 'existing_product_update_button' ,
            ) ,
            array ( 'type' => 'sectionend' , 'id' => 'pp_update_settings_existing_products' ) ,
            array (
                'name' => __( 'Pay Your Price Product Settings' , 'payyourprice' ) ,
                'type' => 'title' ,
                'desc' => '' ,
                'id'   => 'pyp_product_settings'
            ) ,
            array (
                'name'   => __( 'Limit the User Not to Purchase More than Maximum Quantity Per Pay Your Price enabled Product in an Order' , 'payyourprice' ) ,
                'id'     => 'pp_allow_n_no_of_product' ,
                'css'    => '' ,
                'std'    => 'no' ,
                'class'  => 'pp_allow_n_no_of_product' ,
                'newids' => 'pp_allow_n_no_of_product' ,
                'type'   => 'checkbox' ,
            ) ,
            array (
                'name'     => __( 'Maximum Quantity Purchase Per Pay Your Price enabled Product in an Order' , 'payyourprice' ) ,
                'desc'     => '' ,
                'id'       => 'pp_maxmimum_quantity' ,
                'css'      => 'min-width:150px;' ,
                'std'      => '' ,
                'class'    => 'pp_maxmimum_quantity' ,
                'newids'   => 'pp_maxmimum_quantity' ,
                'type'     => 'number' ,
                'step'     => 1 ,
                'desc_tip' => true ,
            ) ,
            array ( 'type' => 'sectionend' , 'id' => 'pyp_product_settings' ) ,
            array (
                'name' => __( 'Troubleshoot' , 'payyourprice' ) ,
                'type' => 'title' ,
                'desc' => '' ,
                'id'   => 'pp_update_settings_existing_products'
            ) ,
            array (
                'name'     => __( 'Load Template' , 'payyourprice' ) ,
                'desc'     => __( 'Troubleshooting the Problem by change the Option to load Template Files from Various Places' , 'payyourprice' ) ,
                'tip'      => '' ,
                'id'       => 'pp_load_woocommerce_template' ,
                'css'      => '' ,
                'std'      => '1' ,
                'type'     => 'radio' ,
                'options'  => array ( '1' => 'From Plugin' , '2' => 'From Theme' ) ,
                'newids'   => 'pp_load_woocommerce_template' ,
                'desc_tip' => true ,
            ) ,
            array (
                'name'     => __( 'Form Input type' , 'payyourprice' ) ,
                'tip'      => '' ,
                'id'       => 'pp_troubleshoot_form_type' ,
                'css'      => '' ,
                'std'      => '1' ,
                'type'     => 'radio' ,
                'options'  => array ( '1' => 'Number' , '2' => 'Text' ) ,
                'newids'   => 'pp_troubleshoot_form_type' ,
                'desc_tip' => true ,
            ) ,
            array (
                'name'     => __( 'Page Reloading after Bulk Update for Existing Products' , 'payyourprice' ) ,
                'tip'      => '' ,
                'id'       => 'pp_reload_page_after_bulk_update' ,
                'css'      => '' ,
                'std'      => '1' ,
                'type'     => 'radio' ,
                'options'  => array ( '1' => 'Yes' , '2' => 'No' ) ,
                'newids'   => 'pp_reload_page_after_bulk_update' ,
                'desc_tip' => true ,
            ) ,
            array ( 'type' => 'sectionend' , 'id' => '_product_troubleshoot' ) ,
        ) ) ;
    }

    public static function save_update_button() {
        ?>
        <tr valign="top">
            <th class="titledesc" scope="row">
                <label for="pp_update_existing_product"></label>
            </th>
            <td class="forminp forminp-select">
                <input type="submit" class="pp_update_existing_product button-primary" value="<?php _e( 'Save and Update' , 'payyourprice' ) ; ?>"/>
                <img class="gif_button" src="<?php echo FP_PYP_PLUGIN_URL ; ?>/assets/images/update.gif" style="width:32px;height:32px;position:absolute"/>
                <div class="updated_success" style="display:none"><?php echo __( 'Updated Successfully' , 'payyourprice' ) ?></div>
            </td>
        </tr>
        <?php
    }

    public static function display_product_selection() {
        global $woocommerce ;
        ?>
        <tr valign="top">
            <th class="titledesc" scope="row">
                <label for="pp_select_particular_products"><?php _e( 'Select Particular Products' , 'payyourprice' ) ; ?></label>
            </th>
            <td class="forminp forminp-select">
                <?php if ( ( float ) $woocommerce->version >= ( float ) ('3.0.0') ) { ?>
                    <select multiple="multiple" class="wc-product-search" name="pp_select_particular_products[]" style='width:550px;' id='pp_select_particular_products'></select>
                <?php } elseif ( ( float ) $woocommerce->version > ( float ) ('2.2.0') ) { ?>
                    <input type="hidden" class="wc-product-search" style="width: 100%;" id="pp_select_particular_products"  name="pp_select_particular_products" data-placeholder="<?php _e( 'Search for a product&hellip;' , 'payyourprice' ) ; ?>" data-action="woocommerce_json_search_products_and_variations" data-multiple="true" />
                <?php } else { ?>
                    <select multiple="multiple" name="pp_select_particular_products" style='width:550px;' id='pp_select_particular_products' class="pp_select_particular_products ajax_chosen_select_products"></select>
                <?php } ?>
            </td>
        </tr>
        <?php
    }

}

new FP_PYP_Settings_Tab() ;
