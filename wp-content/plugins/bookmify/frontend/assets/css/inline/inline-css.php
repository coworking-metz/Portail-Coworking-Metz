<?php

use Bookmify\HelperSettings;

function bookmifyBeFrontEndStyle() {
	$bookmifyBeFrontEndStyle = "";
	
	if(is_array(HelperSettings::getDefaultFontForFrontEnd())){
		$bookmifyBeFrontEndStyle .= "
			.bookmify_fe_app,
			.bookmify_fe_app p,
			.bookmify_fe_cf_top label{
				font-family: '".HelperSettings::getDefaultFontForFrontEnd()[0]."', sans-serif;
			}
			.bookmify_fe_app h1,
			.bookmify_fe_app h2,
			.bookmify_fe_app h3,
			.bookmify_fe_app h4,
			.bookmify_fe_app h5,
			.bookmify_fe_app h6,
			.bookmify_fe_alpha .bookmify_fe_app_content .bookmify_fe_list_item_header .service_title,
			.bookmify_fe_app.bookmify_fe_alpha a.bookmify_fe_link,
			.bookmify_fe_alpha .bookmify_fe_app_content .service_info .service_hover,
			.bookmify_fe_main_list_item.service_holder .info_bottom p,
			.bookmify_fe_main_list_item .item_header .header_wrapper > span,
			.bookmify_fe_main_list_item .item_header .header_wrapper .extra_items_dd .ei_dd span.extra_item,
			.bookmify_fe_main_list_item .bookmify_fe_alpha_next_button a,
			.bookmify_fe_main_list_item .bookmify_fe_price_deposit .payment_wrap span,
			.bookmify_fe_price_deposit .deposit_item .deposit_label,
			.bookmify_fe_dropdown .bookmify_fe_dd_item span,
			.bookmif_fe_count_list_item .count_label .count_title,
			.bookmify_fe_main_list_item .time_wrap .time_content .time_item,
			.bookmify_fe_moving_input span.moving_placeholder,
			.bookmify_fe_moving_input .input_wrapper input[type],
			.bookmify_fe_moving_input .input_wrapper textarea,
			.bookmify_fe_main_list_item .item_details .item_row.input_done a,
			.bookmify_fe_infobox.bookmify_fe_infobox_info label,
			.bookmify_fe_app .ui-datepicker td span,
			.bookmify_fe_app .ui-datepicker td a,
			.bookmif_fe_extras_list_item .extra_label .extra_title_duration,
			.bookmify_fe_app a.bookmify_fe_main_button,
			.bookmify_fe_app li.bookmify_fe_radio_item .radio_inner .label_in,
			.bookmify_fe_alpha .bookmify_fe_price_total .total_header span,
			.bookmify_fe_alpha .bookmify_fe_price_total_footer span,
			.bookmify_fe_alphafilter_dd .bookmify_fe_alphafilter_dd_list > span,
			.bookmify_fe_alpha .bookmify_fe_alpha_sign_in .sign_form_footer a.sign_in,
			.bookmify_fe_alpha .bookmify_fe_alpha_sign_in .sign_form_footer a.forgot_pass{
				font-family: '".HelperSettings::getDefaultFontForFrontEnd()[1]."', sans-serif;
			}
		";
	}
	
	$bookmifyBeFrontEndStyle .= "
		.bookmify_fe_success .svg_holder span,
		.bookmify_fe_success .success_footer a,
		.bookmify_fe_alpha .bookmify_fe_app_header .span_bg,
		.bookmify_fe_main_list_item.service_holder .info_top .img_holder,
		.bookmify_fe_main_list_item .bookmify_fe_alpha_next_button a,
		.bookmify_fe_main_list_item .time_wrap .time_content .time_item.active,
		.bookmify_fe_main_list_item .item_details .item_row.input_done a,
		.bookmify_fe_app .ui-datepicker .ui-datepicker-prev,
		.bookmify_fe_app .ui-datepicker .ui-datepicker-next,
		.bookmify_fe_app .ui-datepicker a.ui-state-active,
		.bookmify_fe_app span.bookmify_fe_checkbox input:checked ~ .checkmark,
		.bookmify_fe_alpha .bookmify_fe_app_header .span_bg:before,
		.bookmify_fe_alpha .img_and_color_holder .img_holder,
		.bookmify_fe_wait,
		.bookmify_fe_alpha .img_and_color_holder .img_holder,
		.bookmify_fe_alpha .bookmify_fe_alpha_sign_in .sign_form_footer a.sign_in,
		.bookmify_fe_alpha .bookmify_fe_app_content ul.bookmify_fe_list li.clicked .bookmify_fe_list_item_header,
		.bookmify_fe_main_list_item.service_holder .info_bottom span:after,
		.bookmify_fe_app a.bookmify_fe_main_button{background-color:".get_option('bookmify_be_feoption_main_color_1', '#5473e8').";}
		
		
		.bookmify_fe_app .bookmify_fe_main_list_item.step_closed:hover span.d_d,
		.bookmify_fe_main_list_item.service_holder .info_top .chosen_holder img,
		.bookmify_fe_main_list_item.service_holder .info_top .chosen_holder svg,
		.bookmify_fe_main_list_item .item_details .item_row.just_info p a,
		.bookmify_fe_alpha .bookmify_fe_alpha_sign_in .sign_form_header p a,
		.bookmify_fe_alpha .bookmify_fe_alpha_sign_in .sign_form_footer a.sign_forget,
		.bookmify_fe_moving_input.active span.moving_placeholder{color:".get_option('bookmify_be_feoption_main_color_1', '#5473e8').";}
		
		
		.bookmify_fe_app span.bookmify_fe_checkbox input:checked ~ .checkmark{border-color:".get_option('bookmify_be_feoption_main_color_1', '#5473e8').";}
		
		
		
		.bookmify_fe_app.bookmify_fe_alpha a.bookmify_fe_link,
		.bookmify_fe_alpha .bookmify_fe_app_content ul.bookmify_fe_list li span.service_price,
		.bookmify_fe_alpha .bookmify_fe_app_content .service_info .service_hover,
		.bookmify_fe_main_list_item .item_header .header_wrapper span.item_result,
		.bookmify_fe_main_list_item.bottom_holder .price_holder .price_wrap .total_price,
		.bookmify_fe_app li.bookmify_fe_radio_item .radio_inner .s_price{color:".get_option('bookmify_be_feoption_main_color_2', '#35d8ac').";}
		
		
		
		
		
		.bookmify_fe_alpha .bookmify_fe_app_content ul.bookmify_fe_list li span.service_title,
		.bookmify_fe_alpha .bookmify_fe_app_content ul.bookmify_fe_list li span.service_duration,
		.bookmify_fe_main_list_item.service_holder .info_top .chosen_holder .text,
		.bookmify_fe_main_list_item.service_holder .info_bottom h3,
		.bookmify_fe_main_list_item.service_holder .info_bottom p,
		.bookmify_fe_main_list_item .item_header .header_wrapper span.item_label,
		.bookmify_fe_main_list_item .item_header .header_wrapper span.item_result span.app_time,
		.bookmify_fe_main_list_item .item_header .header_wrapper .extra_items_dd .ei_dd span.extra_item,
		.bookmify_fe_main_list_item .item_header .header_wrapper span.item_result .extra_item .extra_count,
		.bookmify_fe_app span.d_d,
		.bookmify_fe_main_list_item.bottom_holder .price_holder .t_text,
		.bookmify_fe_app .bookmify_fe_main_list_item.bottom_holder .price_holder span.d_d,
		.bookmify_fe_main_list_item.bottom_holder .price_holder .total_price,
		.bookmif_fe_count_list_item .count_label .count_title,
		.bookmify_fe_main_list_item .time_wrap .time_header h3,
		.bookmify_fe_main_list_item .time_wrap .time_content .time_item,
		.bookmify_fe_main_list_item .item_details .item_row.just_info span,
		.bookmify_fe_app .ui-datepicker .ui-datepicker-title,
		.bookmify_fe_app .ui-datepicker th,
		.bookmify_fe_app .ui-datepicker td span,
		.bookmify_fe_app .ui-datepicker td a,
		.bookmif_fe_extras_list_item .extra_label .extra_title_duration,
		.bookmify_fe_app .bookmify_fe_quantity input[type='number'],
		.bookmify_fe_app li.bookmify_fe_radio_item .radio_inner .label_in,
		.bookmify_fe_cf_top label,
		.bookmify_fe_cf_radiobox_bot label,
		.bookmify_fe_cf_bot label,
		.bookmify_fe_app span.check_box,
		.bookmify_fe_alpha .bookmify_fe_alpha_sign_in .sign_form_header p,
		.bookmify_fe_alpha .bookmify_fe_alpha_sign_in .sign_form_header h3,
		.bookmify_fe_alpha .bookmify_fe_price_total .total_item_sub span, 
		.bookmify_fe_alpha .bookmify_fe_price_total .total_item_sup span,
		.bookmify_fe_main_list_item .item_details .item_row.just_info h3,
		.bookmify_fe_alpha .bookmify_fe_price_total_footer span,
		.bookmify_fe_main_list_item .bookmify_fe_price_deposit .payment_wrap .t_text,
		.bookmify_fe_cf_text .bookmify_fe_cf_text_bot input[type=text],
		.bookmify_fe_main_list_item.service_holder .info_bottom span,
		.bookmify_fe_alpha p.bookmify_fe_no_payment_method,
		.bookmify_fe_main_list_item.bottom_holder .price_holder .price_wrap .total_text,
		.bookmify_fe_cf_textarea .bookmify_fe_cf_textarea_bot textarea{color:".get_option('bookmify_be_feoption_main_color_3', '#7e849b').";}
		
		.bookmify_fe_dropdown .bookmify_fe_dd_item span,
		.bookmify_fe_moving_input .input_wrapper input[type],
		.bookmify_fe_moving_input .input_wrapper textarea{color:".get_option('bookmify_be_feoption_main_color_3', '#7e849b')." !important;}
		
		.bookmify_fe_app span.check_box span,
		.bookmify_fe_app span.bookmify_fe_checkbox .checkmark,
		span.bookmify_be_radiobox{border-color:".get_option('bookmify_be_feoption_main_color_3', '#7e849b').";}
		
		span.bookmify_be_radiobox span{background-color:".get_option('bookmify_be_feoption_main_color_3', '#7e849b').";}
		";
	

	wp_add_inline_style( 'bookmify-app', $bookmifyBeFrontEndStyle );

			
}

	add_action( 'wp_enqueue_scripts', 'bookmifyBeFrontEndStyle' );
?>