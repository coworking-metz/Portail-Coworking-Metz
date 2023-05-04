/* global fp_pyp_single_product_page_obj */

jQuery(function ($) {
    // fp_pyp_single_product_page_obj is required to continue, ensure the object exists
    if (typeof fp_pyp_single_product_page_obj === 'undefined') {
        return false;
    }

    var handle_text_box_input_type = function (event) {
        $(event).keydown(function (e) {
            if (jQuery.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 188, 190]) !== -1 ||
                    (e.keyCode == 65 && e.ctrlKey === true) ||
                    (e.keyCode == 67 && e.ctrlKey === true) ||
                    (e.keyCode == 88 && e.ctrlKey === true) ||
                    (e.keyCode >= 35 && e.keyCode <= 39) || (e.keyCode == 44)) {
                return;
            }
            if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
                e.preventDefault();
            }
        });
    }

    if (fp_pyp_single_product_page_obj.is_pyp_product) {

        if (fp_pyp_single_product_page_obj.input_price_type === 'text_input') {

//            handle_text_box_input_type('#text_simple_pdt');
        }


        if (fp_pyp_single_product_page_obj.input_price_type === 'dropdown') {
            $('.pyppledgeamount' + fp_pyp_single_product_page_obj.product_id).val($('.payyourprice_contribution_dropdown').attr('value')).trigger('change');
        }

        $(document).on('click', '.payyourprice_contribution_button', function () {
            $('.pyppledgeamount' + fp_pyp_single_product_page_obj.product_id).val($(this).attr('data-amount')).trigger('change');

            $('.payyourprice_contribution_button').removeClass('switch_color_button');
            $(this).addClass('switch_color_button');
        });

        $(document).on('change', '.payyourprice_contribution_dropdown', function () {
            $('.pyppledgeamount' + fp_pyp_single_product_page_obj.product_id).val($(this).val()).trigger('change');
        });

        if (fp_pyp_single_product_page_obj.product_addons) {
            $(document).find('#product-addons-total').data('price', $('.payyourprice_contribution').val());
            $(this).trigger('woocommerce-product-addons-update');
            $('.payyourprice_contribution').on('change', function (event) {
                $(document).find('#product-addons-total').data('price', $(this).val());
                $(this).trigger('woocommerce-product-addons-update');
            });
        }

        if ($.inArray(fp_pyp_single_product_page_obj.input_price_type, Array('', 'number_input', 'button_text', 'text_input')) !== -1) {
            $('.payyourprice_contribution').closest('tr').show();
        }

        $(document).on('click', '.single_add_to_cart_button', function () {
            if ($(this).parent().find('.payyourprice_customize_class').length) {
                if ($.inArray(fp_pyp_single_product_page_obj.input_price_type, Array('', 'button_text', 'number_input', 'text_input')) !== -1) {

                    var payyourprice = parseFloat($('.payyourprice_contribution').val());
                    var getminimumprice = parseFloat($('.payyourprice_contribution').attr('data-min'));
                    var getmaximumprice = parseFloat($('.payyourprice_contribution').attr('data-max'));

                    if (isNaN(payyourprice)) {
                        $('.payyourprice_message').html(fp_pyp_single_product_page_obj.input_error_message);
                        return false;
                    }

                    if (payyourprice < getminimumprice && getminimumprice > 0) {
                        var beforestrreplace = fp_pyp_single_product_page_obj.min_price_error_message;
                        var afterstrreplace = beforestrreplace.replace('[pyp_min_price]', fp_pyp_single_product_page_obj.wc_currency_symbol + getminimumprice);

                        $('.payyourprice_message').html(afterstrreplace);
                        return false;
                    }

                    if (payyourprice > getmaximumprice && getmaximumprice > getminimumprice) {
                        var beforestrreplacemax = fp_pyp_single_product_page_obj.max_price_error_message;
                        var afterstrreplacemax = beforestrreplacemax.replace('[pyp_max_price]', fp_pyp_single_product_page_obj.wc_currency_symbol + getmaximumprice);

                        $('.payyourprice_message').html(afterstrreplacemax);
                        return false;
                    }
                } else if (fp_pyp_single_product_page_obj.input_price_type === 'radio') {
                    var amount = $('.pyppledgeamount' + fp_pyp_single_product_page_obj.product_id).val();
                    if (!amount > 0) {
                        $('.payyourprice_message').html(fp_pyp_single_product_page_obj.radio_error_message);
                        return false;
                    }
                }
            }
        });
    }

    if (fp_pyp_single_product_page_obj.product_type === 'variable' && $('form.variations_form').length > 0) {
        var payyourpricefields = $('#fp_pyp_variation_data').data('value');

        var up_variations_data = $('form.variations_form').first().attr('data-product_variations');
        var variations_data = JSON.parse(up_variations_data);
//        var variation_id_from_ready = fp_pyp_single_product_page_obj.default_variation_id;
//        $(document).on('change', '.variations select', function () {
        $('input[name="variation_id"]').change(function () {
            var variationid = parseInt($('input:hidden[name=variation_id]').val());
            var myClasses = this.classList;
            if (!isNaN(variationid) && myClasses[0] != 'payyourprice_contribution_dropdown') {
                var selected_value = this.value;
                if (selected_value !== '') {
                    $.each(variations_data, function () {

                        if (variationid == parseInt(this.variation_id)) {
                            var checkisenable = payyourpricefields[this.variation_id]._found;
                            checkisenable = checkisenable == 'false' ? false : true;
                            if (checkisenable !== false) {
                                if (fp_pyp_single_product_page_obj.show_r_hide_price == '2') {
                                    $('.amount').parent('p.price').css('display', 'none');
                                } else {
                                    $('.amount').parent('p.price').css('display', 'block');
                                }

                                console.log('Found inside Statement');

                                var $recommendedprice = payyourpricefields[this.variation_id]._recommendedprice;
                                var $minimumprice = payyourpricefields[this.variation_id]._minimumprice;
                                var $frontendminimumprice = payyourpricefields[this.variation_id]._frontendminimum;
                                var $frontendmaximumprice = payyourpricefields[this.variation_id]._frontendmaximum;
                                var $maximumprice = payyourpricefields[this.variation_id]._maximumprice;
                                var $hideminimumprice = payyourpricefields[this.variation_id]._hideminimum;
                                var $hidemaximumprice = payyourpricefields[this.variation_id]._hidemaximum;
                                var $inputtype = payyourpricefields[this.variation_id]._input_type;
                                var $output_for_except_text = payyourpricefields[this.variation_id]._output_for_except_text;
                                $('.payyourprice_customize_class').css('display', 'block');
                                $('.payyourprice_customize_class').addClass('single_variation_wraps');
                                $('.payyourprice_variation_messages').css('display', 'block');
                                $('.payyourprice_variation_messages').addClass('single_variation_wraps');
                                if ($inputtype == 'number_input') {
                                    $('.payyourprice_contribution').closest('tr').show();
                                    $('#text_id').hide();
                                    $('.button_text_label').hide();
                                    $('.payyourprice_ouput_for_button_dropdown').closest('tr').hide();
                                } else if ($inputtype == 'text_input') {
                                    handle_text_box_input_type('#text_id');
                                    $('#text_id').show();
                                    $('.button_text_label').hide();
                                    $('.payyourprice_contribution').closest('tr').hide();
                                    $('.payyourprice_ouput_for_button_dropdown').closest('tr').hide();
                                } else if ($inputtype == 'button_text') {
                                    $('.payyourprice_contribution').closest('tr').show();
                                    $('#text_id').hide();
                                    $('.button_text_label').show();
                                    $('.payyourprice_ouput_for_button_dropdown').closest('tr').show();
                                    $('.payyourprice_ouput_for_button_dropdown').html($output_for_except_text);
                                } else {
                                    $('.payyourprice_contribution').closest('tr').hide();
                                    $('#text_id').hide();
                                    $('.button_text_label').hide();
                                    $('.payyourprice_ouput_for_button_dropdown').closest('tr').show();
                                    $('.payyourprice_ouput_for_button_dropdown').html($output_for_except_text);
                                }

                                $('.payyourprice_contribution_dropdown').change(function () {
                                    var amount = parseInt($(this).attr('value'));
                                    $('.payyourprice_contribution').val(amount).trigger('change');
                                });

                                $('.payyourprice_contribution_button').click(function () {
                                    var amount = $(this).attr('data-amount');

                                    $('.payyourprice_contribution_button').removeClass('switch_color_button');
                                    $(this).addClass('switch_color_button');
                                    $('.payyourprice_contribution').val(amount).trigger('change');
                                });

                                if (fp_pyp_single_product_page_obj.product_addons) {
                                    $('.payyourprice_contribution').on('change', function (event) {
                                        $(document).find('#product-addons-total').data('price', $(this).val());
                                        $(this).trigger('woocommerce-product-addons-update');
                                    });
                                }

                                var price_html = this.price_html;

                                $('.payyourprice_min').hide();
                                $('.payyourprice_max').hide();

                                if ($hideminimumprice === 'show') {
                                    if ($frontendminimumprice !== 'empty') {
                                        $('.payyourprice_min').html(fp_pyp_single_product_page_obj.min_price_tab_product + " :" + " " + $frontendminimumprice + "<br>").show();
                                    } else {
                                        $('.payyourprice_min').html("").show();
                                    }
                                }
                                if ($hidemaximumprice === 'show') {
                                    if ($frontendmaximumprice !== 'empty') {
                                        $('.payyourprice_max').html(fp_pyp_single_product_page_obj.max_price_tab_product + " :" + " " + $frontendmaximumprice + "<br>").show();
                                    } else {
                                        $('.payyourprice_max').html("").show();
                                    }
                                }

                                $('.single_variation>.price>.amount').css('display', 'none');

                                if (fp_pyp_single_product_page_obj.show_r_hide_price == '2') {
                                    $('.amount').parent('p.price').css('display', 'none');
                                    $('.single_variation_wrap > .single_variation').css('display', 'none');
                                } else {
                                    $('.amount').parent('p.price').css('display', 'block');
                                    $('.single_variation_wrap > .single_variation').css('display', 'block');
                                }

                                $('.payyourprice_contribution').attr('data-min', $minimumprice == 'empty' ? "" : $minimumprice);
                                $('.payyourprice_contribution').attr('data-max', $maximumprice == 'empty' ? "" : $maximumprice);

                                if ($inputtype == 'number_input') {
                                    if ($recommendedprice !== 'empty') {
                                        $('.payyourprice_contribution').val($recommendedprice);
                                    } else {
                                        $('.payyourprice_contribution').val("").trigger('change');
                                    }
                                } else {
                                    var amount = parseInt($('.payyourprice_contribution_dropdown').attr('value'));
                                    $('.payyourprice_contribution').val(amount).trigger('change');
                                }

                                $('.payyourprice_message').hide();

                                $('.single_add_to_cart_button').click(function () {
                                    var array = $.map(fp_pyp_single_product_page_obj.pyp_enabled_variation_ids, function (value, index) {
                                        return [value];
                                    });
                                    if (jQuery.inArray($('input[name="variation_id"]').val(), array)) {
//                                    if ($(this).parent().find('.payyourprice_customize_class').length) {
                                        var variationid = $('input[name="variation_id"]').val();
                                        var $inputtype = payyourpricefields[variationid]._input_type;
                                        var amount = $('#text_id').val();
                                        if ($inputtype == 'number_input' || $inputtype == '' || $inputtype == 'text_input' || $inputtype == 'button_text') {
                                            if ($inputtype == 'text_input') {
                                                var amount = $('#text_id').val();
                                                var payyourprice = parseFloat($('.payyourprice_contribution').val(amount));
                                            }
                                            var payyourprice = parseFloat($('.payyourprice_contribution').attr('value'));
                                            
                                            if (typeof payyourprice !== typeof undefined || payyourprice == undefined) {
                                                payyourprice = parseFloat($('.payyourprice_contribution').val());
                                            }

                                            var getminimumprice = parseFloat($('.payyourprice_contribution').attr('data-min'));
                                            var getmaximumprice = parseFloat($('.payyourprice_contribution').attr('data-max'));
                                            var currencysymbol = fp_pyp_single_product_page_obj.wc_currency_symbol;

                                            if (isNaN(payyourprice)) {
                                                var emptystrreplace = fp_pyp_single_product_page_obj.input_error_message;

                                                $('.payyourprice_message').html(emptystrreplace);
                                                $('.payyourprice_message').show();
                                                return false;
                                            }

                                            if (payyourprice < getminimumprice && getminimumprice > 0) {
                                                var beforestrreplace = fp_pyp_single_product_page_obj.min_price_error_message;
                                                var afterstrreplace = beforestrreplace.replace('[pyp_min_price]', currencysymbol + getminimumprice);

                                                $('.payyourprice_message').html(afterstrreplace);
                                                $('.payyourprice_message').show();
                                                return false;
                                            }

                                            if (payyourprice > getmaximumprice && getmaximumprice > getminimumprice) {
                                                var beforestrreplacemax = fp_pyp_single_product_page_obj.max_price_error_message;
                                                var afterstrreplacemax = beforestrreplacemax.replace('[pyp_max_price]', currencysymbol + getmaximumprice);

                                                $('.payyourprice_message').html(afterstrreplacemax);
                                                $('.payyourprice_message').show();
                                                return false;
                                            }
                                        }
                                        if ($inputtype == 'radio') {
                                            var amount = $('.payyourprice_contribution').val();

                                            if (!amount > 0) {
                                                var emptystrreplace = fp_pyp_single_product_page_obj.radio_error_message;

                                                $('.payyourprice_message').html(emptystrreplace);
                                                $('.payyourprice_message').show();
                                                return false;
                                            }
                                        }
                                    }
                                });
                            } else {
                                console.log('Nothing Found');

                                $('.single_add_to_cart_button').unbind('click');
                                $('.amount').parent('p.price').css('display', 'block');
                                $('.payyourprice_variation_messages').css('display', 'none');
                                $('.payyourprice_variation_messages').removeClass('single_variation_wraps');
                                $('table.payyourprice_customize_class').css('display', 'none');
                                $('.single_variation_wrap > .single_variation').css('display', 'block');
                                $('.payyourprice_customize_class').removeClass('single_variation_wraps');
                                $('.payyourprice_customize_class').removeClass('single_variation_wraps');
                                $('table.payyourprice_customize_class').css('display', 'none');
                            }
                        }
                    });
                } else {
                    $('.single_add_to_cart_button').unbind('click');
                    $('.amount').parent('p.price').css('display', 'block');
                    $('.payyourprice_variation_messages').css('display', 'none');
                    $('.payyourprice_variation_messages').removeClass('single_variation_wraps');
                    $('.single_variation_wrap > .single_variation').css('display', 'block');
                    $('table.payyourprice_customize_class').css('display', 'none');
                    $('.payyourprice_customize_class').removeClass('single_variation_wraps');
                }
            } else {
                $('.single_add_to_cart_button').unbind('click');
                $('.amount').parent('p.price').css('display', 'block');
                $('.payyourprice_variation_messages').css('display', 'none');
                $('.payyourprice_variation_messages').removeClass('single_variation_wraps');
                $('.single_variation_wrap > .single_variation').css('display', 'block');
                $('table.payyourprice_customize_class').css('display', 'none');
                $('.payyourprice_customize_class').removeClass('single_variation_wraps');
            }
        });
    }


});