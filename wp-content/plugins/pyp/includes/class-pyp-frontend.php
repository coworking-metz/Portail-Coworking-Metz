<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

if (!class_exists('FP_PYP_Frontend')) :

    /**
     * Perform Frontend Actions.
     *
     * @class FP_PYP_Frontend
     * @category Class
     */
    class FP_PYP_Frontend {

        /**
         * FP_PYP_Frontend constructor.
         */
        public function __construct() {
            add_filter('woocommerce_get_price_html', array($this, 'remove_product_pricing'), 999, 2);
            add_filter('woocommerce_available_variation', array($this, 'remove_variable_product_pricing'), 10, 3);

            add_filter('add_to_cart_text', array($this, 'payyourprice_addtocart_shop_button_caption')); // < 2.1
            add_filter('woocommerce_product_add_to_cart_text', array($this, 'payyourprice_addtocart_shop_button_caption'), 999); // 2.1 +
            add_filter('woocommerce_product_single_add_to_cart_text', array($this, 'payyourprice_addtocart_shop_button_caption'), 999);

            add_filter('woocommerce_ajax_variation_threshold', array($this, 'threshold_value_change_for_pay_your_price'), 10, 2);

            add_action('woocommerce_add_to_cart', array(__CLASS__, 'process_something_after_add_to_cart'), 1, 5);
            add_action('woocommerce_before_add_to_cart_button', array($this, 'add_field_before_add_to_cart'), 10);
            add_action('woocommerce_before_single_variation', array($this, 'add_field_before_variation_add_to_cart'), 10);
            add_action('woocommerce_before_calculate_totals', array(__CLASS__, 'perform_function_at_cart_checkout'), 10, 1);

            add_filter('woocommerce_cart_product_subtotal', array(__CLASS__, 'perform_function_at_mini_cart'), 10, 4);

            add_filter('woocommerce_loop_add_to_cart_link', array($this, 'redirect_to_product_page_if_pay_your_price_enabled'), 10, 2);
            add_filter('woocommerce_cart_item_price', array($this, 'minicart_price'), 10, 3);
            add_action('woocommerce_before_add_to_cart_button', array($this, 'add_pyp_product_styles'));

            add_filter('woocommerce_quantity_input_args', array($this, 'woocommerce_quantity_input_args_filter'), 10, 2);

            add_filter('woocommerce_available_variation', array($this, 'woocommerce_available_variation_filter'), 10, 3);
        }

        public static function remove_product_pricing($price, $product) {
            $newid        = sumo_pyp_get_product_id($product);
            $new          = '';
            $minimumfield = '';
            $maximumfield = '';

//            if (function_exists('get_product')) {
            $checkvalue        = get_post_meta($newid, '_checkboxvalue', true);
            $minimumvalue      = fp_pyp_wpml_multi_currency(get_post_meta($newid, '_getminimumprice', true));
            $maximumvalue      = fp_pyp_wpml_multi_currency(get_post_meta($newid, '_getmaximumprice', true));
            $minpricelabel     = get_option('min_price_tab_product');
            $maximumpricelabel = get_option('maximum_price_tab_product');
            $hideminimum       = get_post_meta($newid, '_hideminimum', true);
            $hidemaximum       = get_post_meta($newid, '_hidemaximum', true);
            if ($minpricelabel != '') {
                $minpricecaption = $minpricelabel;
                $colonmin        = ":";
            }
            if ($maximumpricelabel != '') {
                $maxpricecaption = $maximumpricelabel;
                $colonmax        = ":";
            }
            if ($hideminimum != 'yes') {
                if ($minimumvalue != '') {
                    $minimumfield = "<p class = 'price pp_minimum_field'><label>" . $minpricecaption . $colonmin . " " . " </label><span class = 'amount'>" . sumo_pyp_wc_price($minimumvalue) . "</span></p>";
                } else {
                    $minimumfield = '';
                }
            }
            if ($hidemaximum != 'yes') {
                if ($maximumvalue != '') {
                    $maximumfield = " <p class = 'price pp_maximum_field'><label>" . $maxpricecaption . $colonmax . " " . " </label><span class = 'amount'>" . sumo_pyp_wc_price($maximumvalue) . "</span></p>";
                } else {
                    $maximumfield = '';
                }
            }
            if ($checkvalue == 'yes') {

                $new = $minimumfield . $maximumfield;
            }
//            }
            if (get_option('pp_show_hide_price') == '2') {
                if (get_post_meta($newid, '_checkboxvalue', true) == 'yes') {
                    return $new;
                } else {
                    return $price;
                }
            } else {

                return $price . $new;
            }
        }

        public static function remove_variable_product_pricing($array, $product, $variation) {
            $condition_checker = self::is_pay_your_product_variable(sumo_pyp_get_product_id($variation));

            if ($condition_checker['_found'] == 'true') {
                if (get_option('pp_show_hide_price') == '2') {
                    $array['price_html'] = '';
                }
                return $array;
            } else {
                return $array;
            }
        }

        public static function payyourprice_addtocart_shop_button_caption($button_text) {
            global $post;

            $getobject = sumo_pyp_get_product($post->ID);

            if ($getobject) {
                if (!$getobject->is_type('variable')) {
                    if (get_post_meta($post->ID, '_checkboxvalue', true) == 'yes') {
                        if (get_option('pp_enable_add_to_cart_button_caption') == 'yes') {
                            if (is_product()) {
                                if (get_option('pp_add_to_cart_button_caption') != '') {
                                    return __(get_option('pp_add_to_cart_button_caption'), 'payyourprice');
                                } else {
                                    return $button_text;
                                }
                            }
                        }
                    } else {
                        return $button_text;
                    }
                } else {
                    if (is_product()) {
                        if (get_option('pp_add_to_cart_button_caption') != '') {
                            return __(get_option('pp_add_to_cart_button_caption'), 'payyourprice');
                        } else {
                            return $button_text;
                        }
                    } else {
                        return $button_text;
                    }
                }
            }
            return $button_text;
        }

        public static function threshold_value_change_for_pay_your_price($threshold, $object) {
            if (sizeof($object->get_children()) >= 30) {
                return $threshold = 200;
            }
            return $threshold;
        }

        public static function minicart_price($price, $cart_item, $cart_item_key) {
            $checkprod = sumo_pyp_get_product($cart_item['product_id']);

            if ($checkprod->is_type('variable')) {
                if (get_post_meta($cart_item['variation_id'], '_selectpayyourprice', true) == 'two') {
                    $price = WC()->session->get($cart_item_key . '_set_payyourprice_contribution');
                    return self::fp_pyp_return_formatted_price($price, $cart_item, $cart_item_key);
                }
            } elseif ($checkprod->is_type('simple')) {
                if (get_post_meta($cart_item['product_id'], '_checkboxvalue', true) == 'yes') {
                    $price = WC()->session->get($cart_item_key . '_set_payyourprice_contribution');
                    return self::fp_pyp_return_formatted_price($price, $cart_item, $cart_item_key);
                }
            }

            return $price;
        }

        public static function redirect_to_product_page_if_pay_your_price_enabled($add_to_cart_text, $product) {
            $postmeta   = get_post_meta(sumo_pyp_get_product_id($product), '_checkboxvalue', true);
            $shop_label = get_option('payyourprice_price_tab_shop');
            if ($postmeta == 'yes') {
                $add_to_cart_text = sprintf('<a href="%s" rel="nofollow" data-product_id="%s" data-product_sku="%s" class="product_type %s add_to_cart_button button %s">%s</a>', esc_url(get_permalink(sumo_pyp_get_product_id($product))), esc_attr(sumo_pyp_get_product_id($product)), esc_attr($product->get_sku()), $product->is_purchasable() && $product->is_in_stock() ? '' : '', esc_attr(sumo_pyp_get_product_type($product)), esc_html($shop_label));
            }
            return $add_to_cart_text;
        }

        public static function add_field_before_variation_add_to_cart() {
            global $post;
            global $woocommerce;
            global $product;
            @$newid = $post->ID;

            $checkvalue        = get_post_meta($newid, '_checkboxvalue', true);
            $payyourpricelabel = get_option('payyourprice_price_tab_product');

            if ($payyourpricelabel != '') {
                $payyourpricecaption = $payyourpricelabel;
                $colonpay            = ":";
            }

            $recommendedprice = fp_pyp_wpml_multi_currency(get_post_meta($newid, '_getrecommendedprice', true));
            $minimumprice     = fp_pyp_wpml_multi_currency(get_post_meta($newid, '_getminimumprice', true));
            $maximumprice     = fp_pyp_wpml_multi_currency(get_post_meta($newid, '_getmaximumprice', true));

//            if (function_exists('get_product')) {
            $products            = sumo_pyp_get_product($newid);
            $payyourpricemessage = "<div class='payyourprice_message'></div>";
            $messagedisplay      = get_option('display_select_box_payyourprices');
            $minimumerrormessage = get_option('min_price_error_msg');
            $maximumerrormessage = get_option('max_price_error_msg');
            if ($products->is_type('variable')) {
                if ($messagedisplay == 'top') {
                    echo $payyourpricemessage;
                }
                ?>
                <div class="payyourprice_variation_messages">
                    <span class="payyourprice_min"></span>
                    <span class="payyourprice_max"></span>
                </div>
                <table class=" pyp_custom_dropdown payyourprice_customize_class" cellspacing="0" style="display:none;">
                    <tbody>
                        <?php
                        $available_variation = $product->get_available_variations();
                        foreach ($available_variation as $eachvariation) {
                            $variation_id = $eachvariation['variation_id'];
                        }
                        if (get_option('pyp_predefined_button_caption_show_hide') == '1') {
                            if (get_post_meta($variation_id, '_input_type', true) == 'button_text') {
                                ?>
                            <td align="center" colspan="3">
                                <?php echo get_option('pyp_predefined_button_caption'); ?>
                            </td>
                            <?php
                        }
                    }
                    ?>
                    <tr>
                        <td class="label"><label><?php echo $payyourpricecaption . $colonpay; ?> <?php echo get_woocommerce_currency_symbol(); ?> </label></td>
                    </tr>
                    <td class="value" colspan="3">
                        <div class="payyourprice_ouput_for_button_dropdown"> </div>

                    </td>
                    <?php
                    if (get_option('pyp_amount_you_wish_show_hide') == '1') {
                        if (get_post_meta($variation_id, '_input_type', true) == 'button_text') {
                            ?>
                            <tr class="button_text_label">
                                <td> <?php echo get_option('pyp_label_for_button_line1'); ?>  <br> <?php echo get_option('pyp_label_for_button_line2'); ?></td>
                            </tr>
                            <?php
                        }
                    }
                    ?>

                    <tr>
                        <td>
                            <label>
                                <?php
                                foreach ($available_variation as $eachvariation) {
                                    $variation_id = $eachvariation['variation_id'];
                                    if (get_post_meta($variation_id, '_input_type', true) == 'text_input') {
                                        ?>
                                        <input type="text" step="any" min="0" data-min="" data-max="" id="text_id" class=' pyppledgeamount<?php echo $post->ID; ?>' name="payyourprice" value=""/>

                                        <?php
                                    }
                                }
                                if (get_option('pp_troubleshoot_form_type') == '1') {
                                    ?>
                                    <input type="number" step="any" min="0" data-min="" data-max=""  class='payyourprice_contribution pyppledgeamount<?php echo $post->ID; ?>' name="payyourprice_contribution" value=""/>

                                <?php } else { ?>
                                    <input type="text" data-min="" data-max=""  class='payyourprice_contribution pyppledgeamount<?php echo $post->ID; ?>' name="payyourprice_contribution" value=""/>
                                <?php } ?>
                            </label>
                        </td>
                    </tr>
                </tbody>
                </table>
                <?php
                $custom_data         = array();
                $available_variation = $product->get_available_variations();

                foreach ($available_variation as $eachvariation) {
                    $variation_id                                = $eachvariation['variation_id'];
                    $custom_data[$eachvariation['variation_id']] = self::is_pay_your_product_variable($variation_id);
                }
                ?>
                <input type="hidden" id="fp_pyp_variation_data" data-value="<?php echo htmlspecialchars(json_encode($custom_data), ENT_QUOTES, 'UTF-8'); ?>"/>
                <?php
                if ($messagedisplay == 'bottom') {
                    echo $payyourpricemessage;
                }
            }
        }

        public static function add_field_before_add_to_cart() {
            global $post;
            global $woocommerce;
            global $product;
            @$newid = $post->ID;

            $checkvalue        = get_post_meta($newid, '_checkboxvalue', true);
            $payyourpricelabel = get_option('payyourprice_price_tab_product');

            if ($payyourpricelabel != '') {
                $payyourpricecaption = $payyourpricelabel;
                $colonpay            = ":";
            }

            $recommendedprice = fp_pyp_wpml_multi_currency(get_post_meta($newid, '_getrecommendedprice', true));
            $minimumprice     = fp_pyp_wpml_multi_currency(get_post_meta($newid, '_getminimumprice', true));
            $maximumprice     = fp_pyp_wpml_multi_currency(get_post_meta($newid, '_getmaximumprice', true));

//            if (function_exists('get_product')) {
            $products            = sumo_pyp_get_product($newid);
            $payyourpricemessage = "<div class='payyourprice_message'></div>";
            $messagedisplay      = get_option('display_select_box_payyourprices');
            $minimumerrormessage = get_option('min_price_error_msg');
            $maximumerrormessage = get_option('max_price_error_msg');
            if (!$products->is_type('variable')) {
                if ($checkvalue == 'yes') {
                    if ($messagedisplay == 'top') {
                        echo $payyourpricemessage;
                    }
                    ?>
                    <table class="variations payyourprice_customize_class" cellspacing="0">
                        <tbody>
                            <?php
                            if (get_option('pyp_predefined_button_caption_show_hide') == '1') {
                                if (get_post_meta($post->ID, '_input_price_type', true) == 'button_text') {
                                    ?>
                                    <tr>
                                        <?php echo get_option('pyp_predefined_button_caption'); ?>
                                    </tr>
                                    <?php
                                }
                            }
                            ?>
                            <tr>
                                <td class="label"><label><?php echo $payyourpricecaption . $colonpay; ?> <?php echo get_woocommerce_currency_symbol(); ?> </label></td>
                                <?php
                                if (get_post_meta($post->ID, '_input_price_type', true) == 'radio') {
                                    ?>
                                    <td class="value">
                                        <?php
                                        $values = get_post_meta($post->ID, '_input_amount_for_pay', true);
                                        $amount = explode(",", $values);
                                        foreach ($amount as $input) {
                                            ?>
                                            <div class="payyourprice_contribution_button pyppledgeamount<?php echo $post->ID; ?>" data-amount ='<?php echo fp_pyp_wpml_multi_currency($input); ?>' ><?php echo wc_price(fp_pyp_wpml_multi_currency($input)); ?></div>
                                        <?php } ?>
                                        <?php
                                    } else if (get_post_meta($post->ID, '_input_price_type', true) == 'button_text') {
                                        ?>
                                    <td class="value">
                                        <?php
                                        $values = get_post_meta($post->ID, '_input_amount_for_pay', true);
                                        $amount = explode(",", $values);
                                        foreach ($amount as $input) {
                                            ?>
                                            <div class="payyourprice_contribution_button pyppledgeamount<?php echo $post->ID; ?>" data-amount ='<?php echo fp_pyp_wpml_multi_currency($input); ?>' ><?php echo wc_price(fp_pyp_wpml_multi_currency($input)); ?></div>
                                        <?php } ?>
                                        <?php
                                    } elseif (get_post_meta($post->ID, '_input_price_type', true) == 'dropdown') {
                                        ?>
                                    <td class="value">
                                        <select class="payyourprice_contribution_dropdown pyppledgeamount<?php echo $post->ID; ?>"  width= name="payyourprice_contribution">
                                            <?php
                                            $values = get_post_meta($post->ID, '_input_amount_for_pay', true);
                                            $amount = explode(",", $values);
                                            foreach ($amount as $input) {
                                                ?>
                                                <option value="<?php echo fp_pyp_wpml_multi_currency($input); ?>"><?php echo wc_price(fp_pyp_wpml_multi_currency($input)); ?></option>
                                            <?php }
                                            ?>
                                        </select>
                                        <?php
                                    }
                                    ?>
                                </td>
                            </tr>
                            <?php
                            if (get_option('pyp_amount_you_wish_show_hide') == '1') {
                                if (get_post_meta($post->ID, '_input_price_type', true) == 'button_text') {
                                    ?>
                                    <tr>
                                        <td>
                                        <td> <?php echo get_option('pyp_label_for_button_line1'); ?>  <br> <?php echo get_option('pyp_label_for_button_line2'); ?></td>
                                        </td>
                                    </tr>
                                    <?php
                                }
                            }
                            ?>
                        </tbody>
                    </table>
                    <table>
                        <tr style="display: none">
                            <td>
                                <label>
                                    <?php
                                    if ((get_post_meta($post->ID, '_input_price_type', true) == '') || (get_post_meta($post->ID, '_input_price_type', true) == 'number_input')) {
                                        if (get_option('pp_troubleshoot_form_type') == '1') {
                                            ?>
                                            <input type="number" min="0" step="any" data-min="<?php echo $minimumprice; ?>" data-max="<?php echo $maximumprice; ?>"  class='payyourprice_contribution pyppledgeamount<?php echo $post->ID; ?>' name="payyourprice_contribution" value="<?php echo $recommendedprice; ?>"/>
                                        <?php } else { ?>
                                            <input type="text" data-min="<?php echo $minimumprice; ?>" data-max="<?php echo $maximumprice; ?>"  class='payyourprice_contribution pyppledgeamount<?php echo $post->ID; ?>' name="payyourprice_contribution" value="<?php echo $recommendedprice; ?>"/>
                                            <?php
                                        }
                                    } elseif (get_post_meta($post->ID, '_input_price_type', true) == 'text_input') {
                                        ?>
                                        <input type="text" data-min="<?php echo $minimumprice; ?>" data-max="<?php echo $maximumprice; ?>"  class='payyourprice_contribution pyppledgeamount<?php echo $post->ID; ?>' id="text_simple_pdt" name="payyourprice_contribution" value="<?php echo $recommendedprice; ?>"/>
                                        <?php
                                    } else {
                                        if (get_option('pp_troubleshoot_form_type') == '1') {
                                            ?>
                                            <input type="number" min="0" step="any"    class='payyourprice_contribution pyppledgeamount<?php echo $post->ID; ?>' name="payyourprice_contribution" value=""/>
                                        <?php } else { ?>
                                            <input type="text" class='payyourprice_contribution pyppledgeamount<?php echo $post->ID; ?>' name="payyourprice_contribution" value=""/>
                                            <?php
                                        }
                                    }
                                    ?>
                                </label>
                            </td>
                        </tr>
                    </table>
                    <?php
                    if ($messagedisplay == 'bottom') {
                        echo $payyourpricemessage;
                    }
                }
            }
//            }
        }

        public static function is_pay_your_product_variable($variation_id) {
            $checkisenable = get_post_meta($variation_id, '_selectpayyourprice', true);

            if ($checkisenable == 'two') {
                $getminimumprice        = fp_pyp_wpml_multi_currency(get_post_meta($variation_id, '_minimumprice', true));
                $hideminimumprice       = get_post_meta($variation_id, '_hideminimumprice', true);
                $getmaximumprice        = fp_pyp_wpml_multi_currency(get_post_meta($variation_id, '_maximumprice', true));
                $hidemaximumprice       = get_post_meta($variation_id, '_hidemaximumprice', true);
                $recommendedprice       = fp_pyp_wpml_multi_currency(get_post_meta($variation_id, '_recommendedprice', true));
                $inputtype              = get_post_meta($variation_id, '_input_type', true);
                $inputamount            = get_post_meta($variation_id, '_input_for_pay', true);
                $output_for_except_text = self::get_input_type($variation_id);


                if ($inputtype == 'number_input' || $inputtype == 'text_input') {
                    $mainarray = array(
                        "_found"                  => 'true',
                        "_minimumprice"           => $getminimumprice != '' ? $getminimumprice : "empty",
                        "_frontendminimum"        => $getminimumprice != '' ? sumo_pyp_wc_price($getminimumprice) : "empty",
                        "_frontendmaximum"        => $getmaximumprice != '' ? sumo_pyp_wc_price($getmaximumprice) : "empty",
                        "_hideminimum"            => $hideminimumprice == '2' ? "hide" : "show",
                        "_recommendedprice"       => $recommendedprice != '' ? $recommendedprice : "empty",
                        "_maximumprice"           => $getmaximumprice != '' ? $getmaximumprice : "empty",
                        "_hidemaximum"            => $hidemaximumprice == '2' ? "hide" : "show",
                        "_input_for_pay"          => $inputamount != '' ? explode(",", $inputamount) : "empty",
                        "_input_type"             => ($inputtype == 'number_input' ? "number_input" : ($inputtype == 'dropdown' ? "dropdown" : ($inputtype == 'radio' ? "radio" : ($inputtype == 'button_text' ? "button_text" : "text_input")))),
                        "_output_for_except_text" => $output_for_except_text,
                    );
                } else {

                    $mainarray = array(
                        "_found"                  => 'true',
                        "_input_for_pay"          => $inputamount != '' ? explode(",", $inputamount) : "empty",
                        "_input_type"             => ($inputtype == 'number_input' ? "number_input" : ($inputtype == 'dropdown' ? "dropdown" : ($inputtype == 'radio' ? "radio" : ($inputtype == 'button_text' ? "button_text" : "text_input")))),
                        "_output_for_except_text" => $output_for_except_text,
                    );
                }
            } else {
                $mainarray = array("_found" => 'false');
            }
            return $mainarray;
        }

        public static function get_input_text($variation_id) {
            ob_start();
            $inputtype        = get_post_meta($variation_id, '_input_type', true);
            $inputamount      = get_post_meta($variation_id, '_input_for_pay', true);
            $input_for_pay    = explode(",", $inputamount);
            $getminimumprice  = get_post_meta($variation_id, '_minimumprice', true);
            $hideminimumprice = get_post_meta($variation_id, '_hideminimumprice', true);
            $getmaximumprice  = get_post_meta($variation_id, '_maximumprice', true);
            $hidemaximumprice = get_post_meta($variation_id, '_hidemaximumprice', true);
            $recommendedprice = get_post_meta($variation_id, '_recommendedprice', true);

            if ($inputtype == 'text_input') {
                ?>
                <div class ='payyourprice_contribution pyppledgeamount<?php echo $variation_id; ?>'>
                    <input type='text' class="payyourprice_contribution pyppledgeamount<?php echo $variation_id; ?>" name='payyourprice_contribution'/>
                </div>
                <?php
            }
            $output = ob_get_contents();
            ob_clean();
            return $output;
        }

        public static function get_input_type($variation_id) {
            ob_start();
            $inputtype     = get_post_meta($variation_id, '_input_type', true);
            $inputamount   = get_post_meta($variation_id, '_input_for_pay', true);
            $input_for_pay = explode(",", $inputamount);



            if ($inputtype == 'radio') {
                foreach ($input_for_pay as $input_amount) {
                    ?>
                    <div class="payyourprice_contribution_button pyppledgeamount<?php echo $variation_id; ?>" data-amount ='<?php echo fp_pyp_wpml_multi_currency($input_amount); ?>'><?php echo fp_pyp_wpml_multi_currency($input_amount); ?></div>
                    <?php
                }
            }

            if ($inputtype == 'button_text') {
                foreach ($input_for_pay as $input_amount) {
                    ?>
                    <div class="payyourprice_contribution_button pyppledgeamount<?php echo $variation_id; ?>" data-amount ='<?php echo fp_pyp_wpml_multi_currency($input_amount); ?>'><?php echo fp_pyp_wpml_multi_currency($input_amount); ?></div>
                    <?php
                }
            } elseif ($inputtype == 'dropdown') {
                ?>
                <select class="payyourprice_contribution_dropdown pyppledgeamount<?php echo $variation_id; ?>"  width= name="payyourprice_contribution_dropdown">
                    <?php foreach ($input_for_pay as $input_amount) { ?>
                        <option value="<?php echo fp_pyp_wpml_multi_currency($input_amount) ?> "><?php echo fp_pyp_wpml_multi_currency($input_amount) ?></option>
                    <?php } ?>
                </select>
                <?php
            }
            $output = ob_get_clean();
            return $output;
        }

        public static function process_something_after_add_to_cart($cart_item_key, $product_id = null, $quantity = null, $variation_id = null, $variation = null) {
            global $woocommerce_wpml;
            $session_currency   = class_exists('WCML_Multi_Currency') && $woocommerce_wpml->settings['enable_multi_currency'] ? WC()->session->get('client_currency') : get_option('woocommerce_currency');
            $woo_multi_currency = get_option('woocommerce_currency');

            if (class_exists('WOOMULTI_CURRENCY')) {
                global $wmc_settings;
                if (isset($wmc_settings['enable']) && $wmc_settings['enable'] == '1') {
                    $settings           = new WOOMULTI_CURRENCY_Data();
                    $woo_multi_currency = $settings->get_current_currency();
                }
            }

            if (isset($_POST['payyourprice_contribution'])) {

                if ($_POST['payyourprice_contribution'] != '') {
                    $pyp_price = str_replace(get_option('woocommerce_price_decimal_sep') , '.' , $_POST['payyourprice_contribution']);
                    WC()->session->__unset($cart_item_key . '_set_payyourprice_contribution');
                    $price                = fp_pyp_compatibility_price($cart_item_key, $pyp_price);
                    $price                = apply_filters('fp_pyp_contribution_amount', $price, $cart_item_key, $product_id, $quantity, $variation_id, $variation);

                    WC()->session->set($cart_item_key . '_set_payyourprice_contribution', $price);
                    WC()->session->set($cart_item_key . 'fp_pyp_get_currency_when_add_to_cart', $session_currency);
                    WC()->session->set($cart_item_key . 'fp_pyp_woo_multi_currency_when_add_to_cart', $woo_multi_currency);
                    
                    $url_redirection_type = get_option('pp_redirect_after_donate');
                    
                    if ($url_redirection_type == '2') {
                        add_filter('woocommerce_add_to_cart_redirect', 'fp_pyp_redirect_to_cart');
                    }
                    
                    if ($url_redirection_type == '1') {
                        add_filter('woocommerce_add_to_cart_redirect', 'fp_pyp_redirect_to_checkout', 1);
                    }
                    
                } else {
                    WC()->session->__unset($cart_item_key . '_set_payyourprice_contribution');
                    WC()->session->__unset($cart_item_key . 'fp_pyp_get_currency_when_add_to_cart');
                    WC()->session->__unset($cart_item_key . 'fp_pyp_woo_multi_currency_when_add_to_cart');
                }
            } else {
                WC()->session->__unset($cart_item_key . '_set_payyourprice_contribution');
                WC()->session->__unset($cart_item_key . 'fp_pyp_get_currency_when_add_to_cart');
                WC()->session->__unset($cart_item_key . 'fp_pyp_woo_multi_currency_when_add_to_cart');
            }
        }

        public static function perform_function_at_cart_checkout($cart_object) {

            foreach ($cart_object->cart_contents as $key => $value) {
                if (WC()->session->__isset($key . '_set_payyourprice_contribution')) {
                    $pyp_price = WC()->session->get($key . '_set_payyourprice_contribution');

                    // Compatible for WPML MultiCurrency Switcher
                    if (class_exists('WCML_Multi_Currency')) {
                        global $woocommerce_wpml;
                        if ($woocommerce_wpml->settings['enable_multi_currency']) {
                            $session_currency = WC()->session->get($key . 'fp_pyp_get_currency_when_add_to_cart');
                            $current_currency = WC()->session->get('client_currency');
                            $session_currency = $session_currency ? $session_currency : $current_currency;
                            $wpml_currency    = 1;
                            if ($current_currency != $session_currency) {
                                $wpml_currency = $woocommerce_wpml->settings['currency_options'][$session_currency]['rate'];
                                $pyp_price     = fp_pyp_wpml_multi_currency_in_cart($pyp_price, $session_currency, $current_currency);
                            }
                        }
                    }

                    // Compatible for Woocommerce MultiCurrency Switcher
                    if (class_exists('WOOMULTI_CURRENCY')) {
                        global $wmc_settings;

                        if (isset($wmc_settings['enable']) && $wmc_settings['enable'] == '1') {
                            $settings           = new WOOMULTI_CURRENCY_Data();
                            $session_currency   = WC()->session->get($key . 'fp_pyp_woo_multi_currency_when_add_to_cart');
                            $session_currency   = $session_currency ? $session_currency : $settings->get_current_currency();
                            $pyp_price          = fp_pyp_wpml_multi_currency_in_cart($pyp_price, $session_currency, $settings->get_current_currency());
                            
                            $list_of_currencies = $settings->get_list_currencies();
                            $price_value        = $list_of_currencies[$settings->get_current_currency()]['rate'];
                            $pyp_price          = $pyp_price / $price_value;
                        }
                    }
                    $value['data']->set_price($pyp_price);
                }
            }
        }

        public static function perform_function_at_mini_cart($product_subtotal, $_product, $quantity, $cart_object) {
            foreach ($cart_object->cart_contents as $cart_item_key => $cart_item_value) {
                if ($cart_item_value['data'] == $_product) {
                    if (WC()->session->__isset($cart_item_key . '_set_payyourprice_contribution')) {
                        $pyp_price = WC()->session->get($cart_item_key . '_set_payyourprice_contribution');
                        // Compatible for WPML MultiCurrency Switcher
                        if (class_exists('WCML_Multi_Currency')) {
                            global $woocommerce_wpml;
                            if ($woocommerce_wpml->settings['enable_multi_currency']) {
                                $session_currency = WC()->session->get($cart_item_key . 'fp_pyp_get_currency_when_add_to_cart');
                                $session_currency = $session_currency ? $session_currency : $current_currency;
                                $current_currency = WC()->session->get('client_currency');
                                $wpml_currency    = 1;
                                if ($current_currency != $session_currency) {
                                    $wpml_currency = $woocommerce_wpml->settings['currency_options'][$session_currency]['rate'];
                                    $pyp_price     = fp_pyp_wpml_multi_currency_in_cart($pyp_price, $session_currency, $current_currency);
                                }
                            }
                        }
                        // Compatible for Woocommerce MultiCurrency Switcher
                        if (class_exists('WOOMULTI_CURRENCY')) {
                            global $wmc_settings;

                            if (isset($wmc_settings['enable']) && $wmc_settings['enable'] == '1') {
                                $settings         = new WOOMULTI_CURRENCY_Data();
                                $session_currency = WC()->session->get($cart_item_key . 'fp_pyp_woo_multi_currency_when_add_to_cart');
                                $session_currency = $session_currency ? $session_currency : $settings->get_current_currency();
                                $pyp_price        = fp_pyp_wpml_multi_currency_in_cart($pyp_price, $session_currency, $settings->get_current_currency());
                            }
                        }
                        $product_subtotal = wc_price($pyp_price * $quantity);
                    }
                }
            }
            return $product_subtotal;
        }

        public static function decimal_function($price) {
            if (is_numeric($price)) {
                return $price + 0;
            }
            return 0;
        }

        public static function add_pyp_product_styles() {
            if (is_product()) {
                echo '<style type="text/css">';

                ob_start();

                include_once FP_PYP_TEMPLATE_PATH . 'pyp-product-styles.php';

                ob_get_contents();

                echo '</style>';
            }
        }

        public static function woocommerce_quantity_input_args_filter($args, $product) {
            global $sumo_variation;
            $key        = $product->is_type('variation') ? '_selectpayyourprice' : '_checkboxvalue';
            $value      = $product->is_type('variation') ? 'two' : 'yes';
            $checkvalue = get_post_meta($product->get_id(), $key, true);
            $condition  = get_option('pp_allow_n_no_of_product');
            $max_qty1   = get_option('pp_maxmimum_quantity');
            $max_qty    = $max_qty1 ? $max_qty1 : false;
            $wc_max_qty = $product->get_max_purchase_quantity();
            $condition2 = ($wc_max_qty == -1 ) ? true : $wc_max_qty > $max_qty;
            if ($checkvalue == $value && $condition == 'yes' && $max_qty && $condition2) {
                $args['max_value'] = $max_qty;
            }
            return $args;
        }

        public static function woocommerce_available_variation_filter($args, $product, $variation) {
            global $sumo_variation;
            $checkvalue     = get_post_meta($variation->get_id(), '_selectpayyourprice', true);
            $sumo_variation = $variation->get_id();
            $condition      = get_option('pp_allow_n_no_of_product');
            $max_qty1       = get_option('pp_maxmimum_quantity');
            $max_qty        = $max_qty1 ? $max_qty1 : false;
            $wc_max_qty     = $variation->get_max_purchase_quantity();
            $condition2     = ($wc_max_qty == -1 ) ? true : $wc_max_qty > $max_qty;
            if ($checkvalue == 'two' && $condition == 'yes' && $max_qty && $condition2) {
                $args['max_qty'] = $max_qty;
            }
            return $args;
        }

        public static function fp_pyp_return_formatted_price($price, $cart_item, $cart_item_key) {

            if (class_exists('WOOMULTI_CURRENCY')) {
                global $wmc_settings, $woocommerce;

                if (isset($wmc_settings['enable']) && $wmc_settings['enable'] == '1') {
                    $settings         = new WOOMULTI_CURRENCY_Data();
                    $session_currency = WC()->session->get($cart_item_key . 'fp_pyp_woo_multi_currency_when_add_to_cart');
                    $session_currency = $session_currency ? $session_currency : $settings->get_current_currency();
                    $price            = fp_pyp_wpml_multi_currency_in_cart($price, $session_currency, $settings->get_current_currency());
                }
            }

            $price = self::decimal_function($price);
            $price = number_format($price, wc_get_price_decimals(), get_option('woocommerce_price_decimal_sep'), get_option('woocommerce_price_thousand_sep'));

            return get_woocommerce_currency_symbol() . $price;
        }

    }

    new FP_PYP_Frontend();
endif;

function fp_pyp_redirect_to_cart($url) {
    $url = (float) WC()->version >= (float) '3.0' ? wc_get_cart_url() : WC()->cart->get_cart_url();
    return $url;
}

function fp_pyp_redirect_to_checkout($url) {
    $url = (float) WC()->version >= (float) '3.0' ? wc_get_checkout_url() : WC()->cart->get_checkout_url();
    return $url;
}

function fp_default_variation_id($product) {
    if ($product->is_type('variable')) {
        $available_variation = $product->get_available_variations();
        $default_variations  = array();
        $default_variation1  = sumo_pyp_get_variation_attributes($product);
        $default_variation   = is_array($default_variation1) && !empty($default_variation1) ? $default_variation1 : array();
        foreach ($default_variation as $key => $value) {
            $default_variations['attribute_' . $key] = $value;
        }
        foreach ($available_variation as $each_variation) {
            if ($each_variation['attributes'] == $default_variations) {
                return $each_variation['variation_id'];
            }
        }
    }
}

function fp_pyp_wpml_multi_currency($price) {
    if (class_exists('WCML_Multi_Currency')) {// Compatible for WPML MultiCurrency Switcher
        global $woocommerce_wpml;
        if ($woocommerce_wpml->settings['enable_multi_currency']) {
            $session_currency = is_object(WC()->session) ? WC()->session->get('client_currency') : get_option('woocommerce_currency');
            $site_currency    = get_option('woocommerce_currency');
            $value            = 1;
            if ($site_currency != $session_currency) {
                $value = $woocommerce_wpml->settings['currency_options'][$session_currency]['rate'];
                $price = $price * $value;
            }
        }
    }
    return $price;
}

function fp_pyp_wpml_multi_currency_in_cart($price, $previous_currency, $current_currency) {
    $return = $price;
    // Compatible for WPML MultiCurrency Switcher
    if (class_exists('WCML_Multi_Currency')) {
        global $woocommerce_wpml;
        if ($woocommerce_wpml->settings['enable_multi_currency']) {
            $site_currency = get_option('woocommerce_currency');
            if ($site_currency != $previous_currency) {
                $previous_value  = $woocommerce_wpml->settings['currency_options'][$previous_currency]['rate'];
                $original_amount = $price / $previous_value;
            } else {
                $original_amount = $price;
            }
            if ($site_currency != $current_currency) {
                $current_value = $woocommerce_wpml->settings['currency_options'][$current_currency]['rate'];
                $return        = $original_amount * $current_value;
            } else {
                $return = $original_amount;
            }
        }
    }

    if (class_exists('WOOMULTI_CURRENCY')) {
        global $wmc_settings;

        if (isset($wmc_settings['enable']) && $wmc_settings['enable'] == '1') {
            $settings           = new WOOMULTI_CURRENCY_Data();
            $list_of_currencies = $settings->get_list_currencies();
            $site_currency      = get_option('woocommerce_currency');

            if ($site_currency != $previous_currency) {
                $previous_value = $list_of_currencies[$previous_currency]['rate'];
                $price          = $price / $previous_value;
            }

            if ($site_currency != $current_currency) {
                $return = $price * $list_of_currencies[$current_currency]['rate'];
            } else {
                $return = $price;
            }
        }
    }

    return $return;
}

function fp_pyp_compatibility_price($cartitem_key, $pyp_price) {

    $cart_contents = WC()->cart->get_cart();
    
    if (!isset($cart_contents[$cartitem_key]))
        return $pyp_price;

    $_price   = 0;
    $cart_obj = $cart_contents[$cartitem_key];
    if (isset($cart_obj["addons"]) && !empty($cart_obj["addons"])) {
        $_price        = (float) $cart_obj['data']->get_price('edit');
        $product_price = (float) ($cart_obj['data']->get_sale_price('edit')) ? $cart_obj['data']->get_sale_price('edit') : $cart_obj['data']->get_regular_price('edit');
        $_price        = (float) ($_price - $product_price);
    }

    $pyp_price = $pyp_price + $_price;

    return $pyp_price;
}
