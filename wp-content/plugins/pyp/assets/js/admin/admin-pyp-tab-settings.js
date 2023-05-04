/* global fp_pyp_tab_settings_obj, woocommerce_admin */

jQuery(function ($) {

    // fp_pyp_tab_settings_obj is required to continue, ensure the object exists
    if (typeof fp_pyp_tab_settings_obj === 'undefined') {
        return false;
    }

    $('#payyourprice_color_selection').each(function (e) {
        if ($(this).val() === 'custom') {
            $(this).closest('tr').next().show();
        } else {
            $(this).closest('tr').next().hide();
        }
    });

    $(document).on('change', '#payyourprice_color_selection', function (e) {
        $(this).closest('tr').next().toggle();
    });

    $('body').on('blur', '#_getminimumprice[type=text],#_getrecommendedprice[type=text],#_getmaximumprice[type=text]', function () {
        $('.wc_error_tip').fadeOut('100', function () {
            $(this).remove();
        });
        return this;
    });

    $('body').on('keyup change', '#_getminimumprice[type=text],#_getrecommendedprice[type=text],#_getmaximumprice[type=text]', function () {
        var value = $(this).val();
        var regex = new RegExp("[^\-0-9\%.\\" + woocommerce_admin.mon_decimal_point + "]+", "gi");
        var newvalue = value.replace(regex, '');

        if (value !== newvalue) {
            $(this).val(newvalue);

            if ($(this).parent().find('.wc_error_tip').size() == 0) {
                var offset = $(this).position();

                $(this).after('<div class="wc_error_tip">' + woocommerce_admin.i18n_mon_decimal_error + '</div>');
                $('.wc_error_tip')
                        .css('left', offset.left + $(this).width() - ($(this).width() / 2) - ($('.wc_error_tip').width() / 2))
                        .css('top', offset.top + $(this).height())
                        .fadeIn('100');
            }
        }
        return this;
    });

    $("body").click(function () {
        $('.wc_error_tip').fadeOut('100', function () {
            $(this).remove();
        });
    });

    if (fp_pyp_tab_settings_obj.is_lower_wc_version) {
        // Ajax Chosen Product Selectors
        $("select.ajax_chosen_select_products").ajaxChosen({
            method: 'GET',
            url: fp_pyp_tab_settings_obj.wp_ajax_url,
            dataType: 'json',
            afterTypeDelay: 100,
            data: {
                action: 'woocommerce_json_search_products_and_variations',
                security: fp_pyp_tab_settings_obj.create_nonce
            }
        }, function (data) {
            var terms = {};

            $.each(data, function (i, val) {
                terms[i] = val;
            });
            return terms;
        });

        $('#pp_select_particular_products').chosen();
        $('#pp_select_particular_category').chosen();
    }

    if ($('.pp_select_products_categories').val() === '1') {
        $('#pp_select_particular_products').closest('tr').hide();
    } else {
        $('#pp_select_particular_products').closest('tr').show();
    }

    $(document).on('change', '.pp_select_products_categories', function () {
        if ($(this).val() === '1') {
            $('#pp_select_particular_products').closest('tr').hide();
        } else {
            $('#pp_select_particular_products').closest('tr').show();
        }
    });

    $('.gif_button').css('display', 'none');

    $(document).on('click', '.pp_update_existing_product', function () {

        $('.gif_button').css('display', 'inline-block');
        $(this).attr('data-clicked', '1');

        $.ajax({
            type: 'POST',
            url: fp_pyp_tab_settings_obj.wp_ajax_url,
            data: ({
                action: 'fp_pyp_updatedpreviousproductvalue',
                proceedanyway: $(this).attr('data-clicked'),
                whichproduct: $('#pp_select_products_categories').val(),
                selectedproducts: $('#pp_select_particular_products').val(),
                enableyourprice: $('#pp_enable_pay_your_price').val(),
                minimumprice: $('#pp_minimum_price').val(),
                hideminimumprice: $('#pp_hide_minimum_price').is(':checked') ? 'yes' : 'no',
                recommendedprice: $('#pp_recommended_price').val(),
                maximumprice: $('#pp_maxmimum_price').val(),
                hidemaximumprice: $('#pp_hide_maximum_price').is(':checked') ? 'yes' : 'no'
            }),
            success: function (response) {
                console.log(response);

                if (response !== 'success') {
                    var j = 1;
                    var i, j, id, chunk = 10;

                    for (i = 0, j = response.length; i < j; i += chunk) {
                        id = response.slice(i, i + chunk);
                        optimizeProductData(id);
                    }

                    $.when(optimizeProductData()).done(function () {
                        if (fp_pyp_tab_settings_obj.reload_after_bulk_update) {
                            location.reload(true);
                        } else {
                            $('.gif_button').css('display', 'none');
                            $(".updated_success").fadeIn("slow");
                            $(".updated_success").fadeOut(3000);
                        }
                    });
                } else if (response.replace(/\s/g, '') === 'success') {
                    if (fp_pyp_tab_settings_obj.reload_after_bulk_update) {
                        location.reload(true);
                    } else {
                        $('.gif_button').css('display', 'none');
                        $(".updated_success").fadeIn("slow");
                        $(".updated_success").fadeOut(3000);
                    }
                }
            },
            dataType: 'json',
            async: false
        });
        return false;
    });

    function optimizeProductData(id) {
        id = id || '';

        return $.ajax({
            type: 'POST',
            url: fp_pyp_tab_settings_obj.wp_ajax_url,
            data: ({
                action: 'fp_pyp_optimizeupdatedpreviousproduct',
                ids: id,
                selectedproducts: $('#pp_select_particular_products').val(),
                enableyourprice: $('#pp_enable_pay_your_price').val(),
                minimumprice: $('#pp_minimum_price').val(),
                hideminimumprice: $('#pp_hide_minimum_price').is(':checked') ? 'yes' : 'no',
                recommendedprice: $('#pp_recommended_price').val(),
                maximumprice: $('#pp_maxmimum_price').val(),
                hidemaximumprice: $('#pp_hide_maximum_price').is(':checked') ? 'yes' : 'no'
            }),
            success: function (response) {
                console.log(response);
            },
            dataType: 'json',
            async: false
        });
    }
});