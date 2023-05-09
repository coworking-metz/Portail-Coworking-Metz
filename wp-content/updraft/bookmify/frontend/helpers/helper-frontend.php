<?php
namespace Bookmify;

use Bookmify\Helper;
use Bookmify\HelperEmployees;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) { exit; }



class HelperFrontend{
	
	public static function alphaSucces(){
		$allWords	= self::successConfirmationWords();
		$title 		= $allWords[0];
		$desc 		= $allWords[1];
		$footer 	= $allWords[2];
		$service 	= $allWords[3];
		$html = '<div class="bookmify_fe_success abs">
					<span class="span_bg"></span>
					<div class="success_wrapper">
						<div class="success_in">
							<div class="svg_holder"><span>'.self::bookmifyFeSVG('check-mark').'</span></div>
							<div class="content_holder">
								<div class="success_title">
									<h3>'.$title.'</h3>
									<p>'.$desc.'</p>
								</div>
								<div class="success_content">
									<p>'.$footer.'</p>
								</div>
							</div>
						</div>
						<div class="success_footer">
							<a href="#">'.$service.self::bookmifyFeSVG('right-arrow').'</a>
						</div>
					</div>
				</div>';
		return $html;
	}
	
	/* since bookmify v1.3.1 */
	public static function successConfirmationWords(){
		$successSwitch	= get_option( 'bookmify_be_fe_conf_switcher', '' );
		if($successSwitch == 'on'){
			$title 		= get_option( 'bookmify_be_fe_conf_title', 'Thank you!' );
			$desc 		= get_option( 'bookmify_be_fe_conf_desc', 'Your appointment is succesfully received. Please meet us at your selected date and time.' );
			$footer 	= get_option( 'bookmify_be_fe_conf_footer', 'For any kind of inquiry, please call us at 543-323-3456' );
			$service 	= get_option( 'bookmify_be_fe_conf_service_back', 'Go to services' );
		}else{
			$title 		= esc_html__( 'Thank you!', 'bookmify' );
			$desc 		= esc_html__( 'Your appointment is succesfully received. Please meet us at your selected date and time.', 'bookmify' );
			$footer		= esc_html__( 'For any kind of inquiry, please call us at 543-323-3456', 'bookmify' );
			$service 	= esc_html__( 'Go to services', 'bookmify' );
		}
		return array($title,$desc,$footer,$service);
	}
	
	public static function alphaHiddenInfo($eIDs,$lIDs){
		$activePayment 	= self::activePaymentType();
		$companyName	= get_option( 'bookmify_be_company_info_name', '' );
		if($companyName == ''){
			$companyName = 'Bookmify LTD';
		}
		$phoneAsRequired 			= get_option('bookmify_be_phone_as_required', '');
		$phoneRField				= '';
		$phoneRStar					= '';
		if($phoneAsRequired == 'on'){
			$phoneRField			= 'required_field';
			$phoneRStar				= '*';
		}
		$paymentLocalMethod 		= Helper::enabledPaymentMethods();
		$paymentSectionAction 		= 'default';
		if($paymentLocalMethod == 1){
			$paymentSectionAction 	= get_option('bookmify_be_payment_section', 'default');
		}
		$html = '<div class="bookmify_fe_hidden_info">
					<input class="bf_efids" value="'.$eIDs.'" />
					<input class="bf_lfids" value="'.$lIDs.'" />
					<input class="bf_cname" value="'.$companyName.'" />
					<input class="bf_mttb" value="'.get_option( 'bookmify_be_mintime_tobooking', 'disabled' ).'" type="hidden" />
					<input class="bf_maxttb" value="'.get_option( 'bookmify_be_maxtime_tobooking', 'disabled' ).'" type="hidden" />
					<input class="bf_aptype" value="'.$activePayment.'" type="hidden" />
					<input class="bf_psaction" value="'.$paymentSectionAction.'" type="hidden" />
					<span class="bf_right_arrow_svg">'.self::bookmifyFeSVG('right-arrow').'</span>

					<div class="bf_details_info">
						<div class="item_details">
							<div class="item_row just_info">
								<h3>'.esc_html__('Sign Up', 'bookmify').'</h3>
								<p>
									<span>'.esc_html__('Already have an account?', 'bookmify').'</span>
									<a href="#">'.esc_html__('Sign In.', 'bookmify').'</a>
								</p>
							</div>
							<div class="item_row input_row input_first_name required_field bookmify_fe_moving_input">
								<div class="input_wrapper">
									<input type="text" placeholder="" value="" />
									<span class="moving_placeholder"><span>'.esc_html__('First Name', 'bookmify').'</span> *</span>
								</div>
							</div>
							<div class="item_row input_row input_last_name required_field bookmify_fe_moving_input">
								<div class="input_wrapper">
									<input type="text" placeholder="" value="" />
									<span class="moving_placeholder"><span>'.esc_html__('Last Name', 'bookmify').'</span> *</span>
								</div>
							</div>
							<div class="item_row input_row input_email required_field bookmify_fe_moving_input">
								<div class="input_wrapper">
									<input type="text" placeholder="" value="" />
									<span class="moving_placeholder"><span>'.esc_html__('Email', 'bookmify').'</span> *</span>
								</div>
							</div>
							<div class="item_row input_row input_phone '.$phoneRField.' bookmify_fe_moving_input">
								<div class="input_wrapper">
									<input type="text" placeholder="" value="" />
									<span class="moving_placeholder"><span>'.esc_html__('Phone', 'bookmify').'</span> '.$phoneRStar.'</span>
								</div>
							</div>
							<div class="item_row input_row input_message bookmify_fe_moving_input">
								<div class="input_wrapper">
									<textarea></textarea>
									<span class="moving_placeholder"><span>'.esc_html__('Message', 'bookmify').'</span></span>
								</div>
							</div>
							<div class="item_row input_row input_done">
								<div class="input_wrapper">
									<a href="#">
										<span class="text">'.esc_html__('Sign Up','bookmify').'</span>
										<span class="save_process">
											<span class="ball"></span>
											<span class="ball"></span>
											<span class="ball"></span>
										</span>
									</a>
								</div>
							</div>
						</div>
						<div class="bookmify_fe_alpha_sign_in">
							<div class="sign_form_header">
								<h3>'.esc_html__('Sign In', 'bookmify').'</h3>
								<p>
									<span>'.esc_html__('Are you new here?', 'bookmify').'</span>
									<a href="#">'.esc_html__('Sign Up.', 'bookmify').'</a>
								</p>
							</div>
							<div class="sign_form_content">
								<form autocomplete="off">
									<div class="item_row input_row input_login required_field bookmify_fe_moving_input">
										<div class="input_wrapper">
											<input type="text" placeholder="" value="" />
											<span class="moving_placeholder"><span>'.esc_html__('Login', 'bookmify').'</span> *</span>
										</div>
									</div>
									<div class="item_row input_row input_password required_field bookmify_fe_moving_input">
										<div class="input_wrapper">
											<input type="password" placeholder="" value="" autocomplete="new-password" />
											<span class="moving_placeholder"><span>'.esc_html__('Password', 'bookmify').'</span> *</span>
										</div>
									</div>
								</form>
							</div>
							<div class="sign_form_footer">
								<a class="sign_in" href="#">
									<span class="text">'.esc_html__('Sign In', 'bookmify').'</span>
									<span class="save_process">
										<span class="ball"></span>
										<span class="ball"></span>
										<span class="ball"></span>
									</span>
								</a>
								<a class="sign_forget" target="_blank" href="'.BOOKMIFY_SITE_URL.'/wp-login.php?action=lostpassword">'.esc_html__('Forgot Password?', 'bookmify').'</a>
							</div>
						</div>
					</div>


					<div class="bf_people_count_section">
						<div class="bookmify_fe_main_list_item counter_holder">
							<div class="item_header">
								<div class="header_wrapper">
									<span class="item_label">'.esc_html__('People coming together:','bookmify').'</span>
									<span class="item_result" data-empty="empty"></span>
								</div>
								<span class="check_box"><span></span>'.self::bookmifyFeSVG('check-box').'</span>
								<span class="d_d">'.self::bookmifyFeSVG('drop-down-arrow').'</span>
							</div>
							<div class="item_footer">
								'.self::bookmifyPreloader(1, 'loading').'
							</div>
						</div>
					</div>

					<div class="bookmify_be_months_hidden">
						<input class="jan" type="hidden" value="'.date_i18n( 'F', mktime(0, 0, 0, 1), 1 ).'" />
						<input class="feb" type="hidden" value="'.date_i18n( 'F', mktime(0, 0, 0, 2), 1 ).'" />
						<input class="mar" type="hidden" value="'.date_i18n( 'F', mktime(0, 0, 0, 3), 1 ).'" />
						<input class="apr" type="hidden" value="'.date_i18n( 'F', mktime(0, 0, 0, 4), 1 ).'" />
						<input class="may" type="hidden" value="'.date_i18n( 'F', mktime(0, 0, 0, 5, 1) ).'" />
						<input class="jun" type="hidden" value="'.date_i18n( 'F', mktime(0, 0, 0, 6, 1) ).'" />
						<input class="jul" type="hidden" value="'.date_i18n( 'F', mktime(0, 0, 0, 7, 1) ).'" />
						<input class="aug" type="hidden" value="'.date_i18n( 'F', mktime(0, 0, 0, 8, 1) ).'" />
						<input class="sep" type="hidden" value="'.date_i18n( 'F', mktime(0, 0, 0, 9, 1) ).'" />
						<input class="oct" type="hidden" value="'.date_i18n( 'F', mktime(0, 0, 0, 10, 1) ).'" />
						<input class="nov" type="hidden" value="'.date_i18n( 'F', mktime(0, 0, 0, 11, 1) ).'" />
						<input class="dec" type="hidden" value="'.date_i18n( 'F', mktime(0, 0, 0, 12, 1) ).'" />
						<input class="def" type="hidden" value="'.get_option( 'bookmify_be_date_format', 'd F, Y' ) .'" />
					</div>

					<div class="bf_people_count_content">
						<div class="bookmif_fe_count_list_item">
							<div class="count_item_in">
								<div class="count_label">
									<label>
										<span class="bookmify_fe_checkbox">
											<input class="req" type="checkbox" />
											<span>'.self::bookmifyFeSVG('checked').'</span>
											<span class="checkmark">'.self::bookmifyFeSVG('checked').'</span>
										</span>
										<span class="count_title">
											'.esc_html__('Count', 'bookmify').'
										</span>
									</label>
								</div>
								<div class="extra_qty">
									<div class="bookmify_fe_quantity small disabled">
										<input class="extra_quantity" readonly disabled type="number" min="1" max="" value="1" />
										<span class="increase"><span></span></span>
										<span class="decrease"><span></span></span>
									</div>
								</div>
							</div>
						</div>
						<div class="bookmify_fe_alpha_next_button">
							<a href="#">'.esc_html__('Next', 'bookmify').'</a>
						</div>
					</div>

				</div>
				
				<div class="bookmify_fe_price_hidden">
					<input class="currency_format" type="hidden" value="'.Helper::bookmifyGetIconPrice().'" />
					<input class="currency_position" type="hidden" value="'.get_option( 'bookmify_be_currency_position', 'lspace' ).'" />
					<input class="price_format" type="hidden" value="'.get_option( 'bookmify_be_price_format', 'cd' ).'" />
					<input class="price_decimal" type="hidden" value="'.get_option( 'bookmify_be_price_decimal', 2 ).'" />
				</div>';
		return $html;
	}
	
	public static function paymentInfo($serviceID){
		global $wpdb;
		
		$taxIDs  	   	= [];
		$taxListService	= '';
		$query = "SELECT 
					t.title tTitle,
					t.rate tRate,
					t.id tID

					FROM {$wpdb->prefix}bmify_taxes t
						INNER JOIN {$wpdb->prefix}bmify_services_taxes st					ON t.id = st.tax_id

					WHERE st.service_id=".$serviceID;
		$results = $wpdb->get_results( $query);
		
		$zeroPrice		= Helper::bookmifyPriceCorrection(0);
		if(!empty($results)){
			$taxIDs 		= [];
			foreach($results as $key => $result){
				$taxListService 		.= '<div class="total_item_sub" data-id="'.$result->tID.'">';
				$taxListService 		.= '<div class="left_part">';
				$taxListService 		.= '<span>'.$result->tTitle.'</span>';
				$taxListService 		.= '<span>('.$result->tRate.'%)</span>';
				$taxListService 		.= '</div>';
				$taxListService 		.= '<div class="right_part">';
				$taxListService 		.= '<span>'.$zeroPrice.'</span>';
				$taxListService 		.= '</div>';
				$taxListService 		.= '</div>';
			}
		}
		
		$html = '<div class="bookmify_fe_price_total">
					<div class="bookmify_fe_price_total_content">
						<div class="total_header">
							<div class="left_part"><span>'.esc_html__('Entity', 'bookmify').'</span></div>
							<div class="right_part"><span>'.esc_html__('Amount', 'bookmify').'</span></div>
						</div>
						<div class="total_items">
							<div class="total_item service_item">
								<div class="total_item_sup">
									<div class="left_part"><span>'.esc_html__('Service', 'bookmify').'</span></div>
									<div class="right_part"><span class="price_service_sum">'.$zeroPrice.'</span></div>
								</div>
								'.$taxListService.'
							</div>
							<div class="total_item extras_item">
								<div class="total_item_sup">
									<div class="left_part"><span>'.esc_html__('Extras', 'bookmify').'</span></div>
									<div class="right_part"><span class="price_extras_sum">'.$zeroPrice.'</span></div>
								</div>
								'.$taxListService.'
							</div>
						</div>
					</div>
					<div class="bookmify_fe_price_total_footer">
						<span class="price_total_text">'.esc_html__('Total:', 'bookmify').'</span>
						<span class="price_total_sum">'.$zeroPrice.'</span>
					</div>
				</div>
				<div class="bookmify_fe_alpha_next_button">
					<a href="#">'.esc_html__('Next', 'bookmify').'</a>
				</div>
				<div class="bookmify_fe_price_deposit">
					<div class="bookmify_fe_infobox bookmify_fe_infobox_info">
						<label>
							<span class="total_text">'.esc_html__('Total Price:', 'bookmify').'</span>
							<span class="total_price">'.$zeroPrice.'</span>
						</label>
					</div>
					<div class="payment_wrap">
						<span class="total_text">'.esc_html__('Payment Method', 'bookmify').'</span>';
		
		$paypalON 		= Helper::bookmifyBePaypalCheckout();
		$stripeON 		= Helper::bookmifyBeStripeCheckout('');
		
		$localPayment 	= get_option( 'bookmify_be_local_payment', 'on' );
		
		
		$paymentCount 	= 0;
		
		if($localPayment == 'on'){$paymentCount++;}
		if($paypalON == 'on'){$paymentCount++;}
		if($stripeON == 'on'){$paymentCount++;}
		
		$defaultPayment = '';
		$demo 			= '';
		if(BOOKMIFY_MODE == 'demo' || BOOKMIFY_MODE == 'dev'){$demo = 'demo';}
		if($localPayment == '' && $demo == ''){ // '' должен быть
			if($paypalON == 'on'){
				$defaultPayment = esc_html__('Paypal', 'bookmify');
			}else if($stripeON == 'on'){
				$defaultPayment = esc_html__('Stripe', 'bookmify');
			}
		}else{
			$defaultPayment 	= esc_html__('On-site', 'bookmify');
		}
		if($paymentCount > 1){
			$paymentON	= '';
			
			// dropdown --> start
			$paymentDD 	= '<div class="bookmify_fe_dropdown">
								<div class="bookmify_fe_dropdown_in">
									<span class="dd_triangle"><span></span></span>
									<div class="bookmify_fe_dd_list">';
			
			if($localPayment == 'on' || $demo == 'demo'){
				$paymentDD .= 		'<div class="bookmify_fe_dd_item active" data-id="on-site">
										<span>'.esc_html__('On-site','bookmify').'</span>
										<input type="hidden" value="0" />
									</div>';
				$paypalActive = '';
				$stripeActive = '';
			}else{
				if($paypalON == 'on'){
					$paypalActive = 'active';
					$stripeActive = '';
				}else if($stripeON == 'on'){
					$paypalActive = '';
					$stripeActive = 'active';
				}
			}
			if($paypalON == 'on'){
				$paymentDD .= 			'<div class="bookmify_fe_dd_item '.$paypalActive.'" data-id="paypal">
											<span>'.esc_html__('Paypal','bookmify').'</span>
											<input type="hidden" value="1" />
										</div>';
			}
			if($stripeON == 'on'){
				$paymentDD .= 			'<div class="bookmify_fe_dd_item '.$stripeActive.'" data-id="stripe">
											<span>'.esc_html__('Stripe','bookmify').'</span>
											<input type="hidden" value="2" />
										</div>';
			}
			
			// dropdown --> end
			$paymentDD .=			'</div>
								</div>
							</div>';
		}else{
			$paymentON	= 'disabled';
			$paymentDD	= '';
		}
		$paymentDeposit = '<div class="deposit_wrap">
								<div class="deposit_item deposit_selected">
									<span class="bookmify_be_radiobox">
										<input class="req" type="radio" checked="checked" />
										<span></span>
									</span>
									<label class="deposit_label">
										<span class="deposit_label_text">'.esc_html__('Full Amount', 'bookmify').' — </span>
										<span class="deposit_label_price full_price">'.$zeroPrice.'</span>
									</label>
									<span class="deposit_desc">'.esc_html__('You can make a full payment now', 'bookmify').'</span>
								</div>
								<div class="deposit_item">
									<span class="bookmify_be_radiobox">
										<input class="req" type="radio" />
										<span></span>
									</span>
									<label class="deposit_label">
										<span class="deposit_label_text">'.esc_html__('Deposit', 'bookmify').' — </span>
										<span class="deposit_label_price deposit_price">'.$zeroPrice.'</span>
									</label>
									<span class="deposit_desc">'.esc_html__('Leave a deposit and pay the rest later', 'bookmify').'</span>
								</div>
						   </div>';
		
		$html .= 			'<span class="total_result '.$paymentON.'">
								<span class="t_text">'.$defaultPayment.'</span>
								<span class="d_d">'.self::bookmifyFeSVG('drop-down-arrow').'</span>
								'.$paymentDD.'
							</span>

						</div>
						'.$paymentDeposit.'
				</div>';
		return $html;
	}
	
	public static function bookmifyFeSVG($icon = '', $class = ''){
		return '<img class="bookmify_fe_svg '.$class.'" src="'.BOOKMIFY_ASSETS_URL.'img/'.$icon.'.svg" alt="" />';
	}
	
	public static function bookmifyPreloader($size = '', $extraClass = ''){
		if($size == 1){$size = 'small';}
		$html = '<span class="bookmify_fe_loader '.$size.' '.$extraClass.'">
					<span class="loader_process">
						<span class="ball"></span>
						<span class="ball"></span>
						<span class="ball"></span>
					</span>
				</span>';
		return $html;
	}
	
	public static function cfForAlphaShortcode($serviceID){
		global $wpdb;
		$query 		= "SELECT id,services_ids FROM {$wpdb->prefix}bmify_customfields";
		$results 	= $wpdb->get_results( $query, OBJECT  );
		$cfIDs		= array();
		foreach($results as $result){
			$arr 	= explode(',', $result->services_ids);
			if(in_array($serviceID, $arr)){
				$cfIDs[] = $result->id;
			}
		}
		$cfList				= '';
		$cfFooterPart		= '';
		if(!empty($cfIDs)){
			$query 		= "SELECT * FROM {$wpdb->prefix}bmify_customfields WHERE `id` IN (" . implode(',', array_map('intval', $cfIDs)) . ") ORDER BY position, id";
			$results 	= $wpdb->get_results( $query, OBJECT  );

			if(!empty($results)){
				$cfList .= 				'<div class="bookmify_fe_main_list_item details_holder">';
				$cfList .= 					'<div class="item_header">';
				$cfList .= 						'<div class="header_wrapper">';
				$cfList .= 							'<span class="item_label">'.esc_html__('Details:','bookmify').'</span>';
				$cfList .= 							'<span class="item_result" data-empty="empty"></span>'; // Will be added
				$cfList .= 						'</div>';
				$cfList .= 						'<span class="check_box"><span></span>'.self::bookmifyFeSVG('check-box').'</span>';
				$cfList .= 						'<span class="d_d">'.self::bookmifyFeSVG('drop-down-arrow').'</span>';
				$cfList .= 					'</div>';
				// footer
				$cfList .= 					'<div class="item_footer">';
				$cfFooterPart .= 				'<div class="bookmif_fe_cf_list">';
				$requiredKey = 0;
				foreach($results as $result){
					$cfID		= $result->id;
					$cfType 	= $result->cf_type;
					$cfRequired = $result->cf_required;
					$values 	= unserialize($result->cf_value);
					$options 	= '';
					if(!empty($values)){
						foreach($values as $keyy => $value){
							if($cfType == 'checkbox'){
								$options .= '<label>
												<span class="bookmify_fe_checkbox">
													<input class="req" type="checkbox" />
													<span>'.self::bookmifyFeSVG('checked').'</span>
													<span class="checkmark">'.self::bookmifyFeSVG('checked').'</span>
												</span>
												<span class="count_title">
													'.$value['label'].'
												</span>
											</label>';
							}else if($cfType == 'radiobuttons'){
								$options .= '<label>
												<span class="bookmify_be_radiobox">
													<input class="req" type="radio" name="radio" />
													<span></span>
												</span>
												<span class="label_in">
													<span class="e_name">'.$value['label'].'</span>
												</span>
											</label>';
							}else if($cfType == 'textcontent'){
								$options .= '<div class="bookmify_fe_infobox"><label>'.$value['label'].'</label></div>';
							}else if($cfType == 'selectbox'){
								$options .= '<option value="t'.$keyy.'">'.$value['label'].'</option>';
							}
						}
					}
					$itemIn = '';
					$requiredStar = '';
					$cfRequiredClass = '';
					if($cfRequired == 1){
						$requiredKey++; // all required customfields
						$requiredStar = '<span class="reqq">*</span>';
						$cfRequiredClass = 'required_cf';
					}
					if($cfType == 'checkbox'){
						$itemIn .= '<div class="bookmify_fe_cf_checkbox bookmify_fe_cf_item">';
						$itemIn .= 		'<div class="bookmify_fe_cf_checkbox_top bookmify_fe_cf_top">';
						$itemIn .= 			'<label>'.$result->cf_label.$requiredStar.'</label>';
						$itemIn .= 		'</div>';
						$itemIn .= 		'<div class="bookmify_fe_cf_checkbox_bot bookmify_fe_cf_bot">';
						$itemIn .= 			$options;
						$itemIn .= 		'</div>';
						$itemIn .= '</div>';
					}else if($cfType == 'radiobuttons'){
						$itemIn .= '<div class="bookmify_fe_cf_radiobox bookmify_fe_cf_item">';
						$itemIn .= 		'<div class="bookmify_fe_cf_radiobox_top bookmify_fe_cf_top">';
						$itemIn .= 			'<label>'.$result->cf_label.$requiredStar.'</label>';
						$itemIn .= 		'</div>';
						$itemIn .= 		'<div class="bookmify_fe_cf_radiobox_bot bookmify_fe_cf_bot">';
						$itemIn .= 			$options;
						$itemIn .= 		'</div>';
						$itemIn .= '</div>';
					}else if($cfType == 'text'){
						$itemIn .= '<div class="bookmify_fe_cf_text bookmify_fe_cf_item">';
						$itemIn .= 		'<div class="bookmify_fe_cf_text_top bookmify_fe_cf_top">';
						$itemIn .= 			'<label>'.$result->cf_label.$requiredStar.'</label>';
						$itemIn .= 		'</div>';
						$itemIn .= 		'<div class="bookmify_fe_cf_text_bot bookmify_fe_cf_bot">';
						$itemIn .= 			'<input type="text" value="" />';
						$itemIn .= 		'</div>';
						$itemIn .= '</div>';
					}else if($cfType == 'textarea'){
						$itemIn .= '<div class="bookmify_fe_cf_textarea bookmify_fe_cf_item">';
						$itemIn .= 		'<div class="bookmify_fe_cf_textarea_top bookmify_fe_cf_top">';
						$itemIn .= 			'<label>'.$result->cf_label.$requiredStar.'</label>';
						$itemIn .= 		'</div>';
						$itemIn .= 		'<div class="bookmify_fe_cf_textarea_bot bookmify_fe_cf_bot">';
						$itemIn .= 			'<textarea></textarea>';
						$itemIn .= 		'</div>';
						$itemIn .= '</div>';
					}else if($cfType == 'textcontent'){
						$itemIn .= '<div class="bookmify_fe_cf_textcontent bookmify_fe_cf_item">';
						$itemIn .= 		'<div class="bookmify_fe_infobox bookmify_fe_cf_top">';
						$itemIn .= 			'<label>'.$result->cf_label.$requiredStar.'</label>';
						$itemIn .= 		'</div>';
						$itemIn .= '</div>';
					}else if($cfType == 'selectbox'){
						$itemIn .= '<div class="bookmify_fe_cf_select bookmify_fe_cf_item">';
						$itemIn .= 		'<div class="bookmify_fe_cf_select_top bookmify_fe_cf_top">';
						$itemIn .= 			'<label>'.$result->cf_label.$requiredStar.'</label>';
						$itemIn .= 		'</div>';
						$itemIn .= 		'<div class="bookmify_fe_cf_select_bot bookmify_fe_cf_bot">';
						$itemIn .= 			'<select>
												<option disabled selected value>'.esc_html__('Select an option', 'bookmify').'</option>
												'.$options.'
											 </select>';
						$itemIn .= 		'</div>';
						$itemIn .= '</div>';
					}
					$itemIn .= '<input type="hidden" value="'.$cfID.'" class="bookmify_fe_cf_id" />';
					
					$cfFooterPart .= 				'<div class="bookmify_fe_cf_list_item '.$cfRequiredClass.'">
														<div class="cf_item_in">';
					$cfFooterPart .=	 					$itemIn;
					$cfFooterPart .= 					'</div>';
					$cfFooterPart .= 				'</div>';
				}
				$cfFooterPart .= 					'</div>';
				
				$disabled = '';
				if($requiredKey > 0){
					$disabled = 'disabled';
				}
				$cfFooterPart .= 					'<div class="bookmify_fe_alpha_next_button cf_button">
														<a href="#">'.esc_html__('Next', 'bookmify').'</a>
													</div>';
				$cfFooterPart .= 				'</div>';

				$cfList	.= 					$cfFooterPart;
				// --------------------------------------
				$cfList .= 				'</div>';
			}
		}
		
		
		// remove whitespaces form the HTML
		$search = array(
			'/\>[^\S ]+/s',  // strip whitespaces after tags, except space
			'/[^\S ]+\</s',  // strip whitespaces before tags, except space
			'/(\s)+/s'       // shorten multiple whitespace sequences
		);
		$replace = array(
			'>',
			'<',
			'\\1'
		);
		$cfList 			= preg_replace($search, $replace, $cfList);
		$cfFooterPart 		= preg_replace($search, $replace, $cfFooterPart);
		$array = array();
		$array['content'] 	= $cfList;
		$array['footer'] 	= $cfFooterPart;
		return $array;
	}
	
	public static function activePaymentType(){
		$active 		= '';
		$localPayment 	= get_option( 'bookmify_be_local_payment', 'on' );
		$paypalON 		= Helper::bookmifyBePaypalCheckout();
		$stripeON 		= Helper::bookmifyBeStripeCheckout('');
		
		$demo 			= '';
		if(BOOKMIFY_MODE == 'demo' || BOOKMIFY_MODE == 'dev'){$demo = 'demo';}
		
		if($localPayment == 'on'|| $demo == 'demo'){$active = 'local';}else{
			if($paypalON == 'on'){
				$active = 'paypal';
			}else if($stripeON == 'on'){
				$active = 'stripe';
			}
		}
		return $active;
	}
	
	public function timeSlotsCOPY(){
		global $wpdb;
		$isAjaxCall 			= false;
		
		if (!empty($_POST['serID'])) {
			$isAjaxCall			= true;
			$serviceID 			= $_POST['serID'];
			$employeeID 		= $_POST['empID'];
			$dateValue 			= $_POST['dateVal'];
			$date 				= $_POST['dateVal'];
			$selDayBetween 		= $_POST['selDayBetween'];
			$newHours 			= $_POST['newHours'];
			$newMinutes 		= $_POST['newMinutes'];
			$selectedDayIndex 	= date('N', strtotime($dateValue));
			$extraDuration 		= $_POST['extraDuration'];
			if(!$extraDuration){
				$extraDuration 	= 0;
			}
			if(isset($_POST['timezoneOffset'])){
				$timezoneOffset = intval($_POST['timezoneOffset']); 			// тайм зона клиента.
			}
			
			$timezoneOffset		= 0;
			
			// время работы (от и до) выбранного работника, для выбранной даты по индексу дня в формате чч:мм
			$startTime 			= Helper::bookmifyWorkingHoursOfEmployee($employeeID,$selectedDayIndex,'start_time');
			$endTime 			= Helper::bookmifyWorkingHoursOfEmployee($employeeID,$selectedDayIndex,'end_time');
			
			
			// суммарное время которое уйдет на выбранный сервис (здесь учитывается длительность самого сервиса а также время до и после этого сервиса)
			$serviceBuffBefore 	= 0;
			$serviceBuffAfter	= 0;
			$query 				= "SELECT duration, buffer_before, buffer_after FROM {$wpdb->prefix}bmify_services WHERE id=".$serviceID;
			$results 			= $wpdb->get_results( $query, OBJECT  );
			foreach($results as $result){
				$serviceDuration 	= $result->duration; 			// в секундах
				$serviceBuffBefore 	= $result->buffer_before;		// в секундах
				$serviceBuffAfter 	= $result->buffer_after;		// в секундах
			}
			$summaryDuration 		= ($serviceDuration+$serviceBuffBefore+$serviceBuffAfter+$extraDuration) / 60; // в минутах
			
			
			// слот времени: по выбранному слоту (в минутах) будет добавляться время, к примеру: 8:00, 8:15, 8:30 и т.д.
			$timeSlot 				= get_option( 'bookmify_be_time_interval', '15' ); // получить тайм интервал из настроек
			// проверить включена ли время сервиса как интервал в настройках, в случае положительного ответа применить его как слот времени
			if(get_option('bookmify_be_service_time_as_slot', '') == 'on'){
				$timeSlot 			= $summaryDuration;
			}
			
			// время работы (от и до) для выбранного работника, для выбранной даты по индексу дня в минутах
			$startTimeInMinutes = date('H',strtotime($startTime))*60 + date('i',strtotime($startTime)) + $timezoneOffset;
			$endTimeCheck 		= date('H',strtotime($endTime))*60 + date('i',strtotime($endTime));
			if($endTimeCheck == 0){
				$endTimeCheck = 24*60;
			}
			$endTimeInMinutes 	= $endTimeCheck - ($serviceBuffBefore / 60) + $timezoneOffset;
			
			// данная проверка добавлена из-за добавления рассчета тайм зону клиента. Пояснение: при получении слотов время начала и конца работы может выйти из рамки от 00:00 до 24:00.
			if($endTimeInMinutes > 1440){
				$endTimeInMinutes = 1440;
			}
			if($startTimeInMinutes < 0){
				$startTimeInMinutes = 0;
			}
			
			// если выбран сегодняшний или завтрашний день, установить минимум время для подсчета слотов
			$minTimeInMinutes = 0;
			if($selDayBetween == 0){
				$minTimeInMinutes = intval($newHours * 60 + $newMinutes);
			}else if(($selDayBetween == 1) && ($newHours >=24)){
				$minTimeInMinutes = intval(($newHours - 24) * 60 + $newMinutes);
			}
			
			
			// получение всевозможных перерывов выбранного работника для выбранной даты по индексу дня в массиве
			$breakArray = array();
			$selectedDayIndex 	= esc_sql($selectedDayIndex);
			$employeeID 		= esc_sql($employeeID);
			$select 			= "SELECT start_time,end_time FROM {$wpdb->prefix}bmify_employee_business_hours_breaks WHERE day_index=".$selectedDayIndex." AND employee_id=".$employeeID;
			$breaks 			= $wpdb->get_results( $select, OBJECT  );
			foreach($breaks as $key => $break){
				$startBreak = date('H', strtotime($break->start_time))*60 + date('i', strtotime($break->start_time)) + $timezoneOffset;
				$endBreak 	= date('H', strtotime($break->end_time))*60 + date('i', strtotime($break->end_time)) + $timezoneOffset;
				$breakArray[$key]['start'] 	= $startBreak;
				$breakArray[$key]['end'] 	= $endBreak;
			}
			
			// начало работы выбранного работника в секундах
			$startTime = strtotime($startTime) + $serviceBuffBefore + ($timezoneOffset * 60); // здесь мы прибавляем время buffer_before для того, чтобы работник смог подготовиться к работе в начале работы
			
			// данная проверка добавлена из-за добавления рассчета тайм зону клиента. Пояснение: при получении слотов время начала и конца работы может выйти из рамки от 00:00 до 24:00.
			if($startTime < 0){
				$startTime = 0;
			}
			
			// количество слотов без каких либо учетов
			$to = intval(($endTimeInMinutes - $startTimeInMinutes) / $timeSlot);
			// ОБЩИЙ массив без каких либо учетов
			$allArray = array();
			for($i = 0; $i < $to; $i++){
				$firstTime = $i*$timeSlot + $startTimeInMinutes;
				if($firstTime <= ($endTimeInMinutes - $summaryDuration)){
					$allArray[] = date("H", strtotime('+'.($i*$timeSlot).' minutes', $startTime))*60 + date("i", strtotime('+'.($i*$timeSlot).' minutes', $startTime));
				}
			}
			
			
			// получение всевозможных слотов, которых нужно удалить из ОБЩЕГО масива (все ПЕРЕРЫВЫ того дня недели)
			$removableValues = array();
			foreach($breakArray as $key => $result){
				$min 	= intval($result['start']) - $summaryDuration;
				$max 	= intval($result['end']) + ($serviceBuffBefore / 60); // здесь мы прибавляем время buffer_before для того, чтобы работник смог подготовиться к работе после каждого перерыва
				$removableValues[] 	= array_filter($allArray, function ($value) use($min,$max)  { return ($value > $min && $value < $max); });
			}
			$removableArr = array();
			foreach($removableValues as $results){
				foreach($results as $result){
					$removableArr[] = $result;
				}
			}
			
			// удаление полученных слотов, которых нужно было удалить из ОБЩЕГО массива (все ПЕРЕРЫВЫ того дня недели)
			$difference = array_diff($allArray,$removableArr);
			
			
			//****************************************************************************************************************************************
			// получение всевозможных ВСТРЕЧ выбранного работника для выбранной даты в массиве
			
			// добавлена, для того, чтобы получить слот, существующих встреч, если количество людей не достигнуто
			
			/////////////////////////////////////////////////////
			$capacityMax 			= $_POST['capacityMax'];////
			$peopleCount 			= $_POST['peopleCount'];///
			$duration 				= $_POST['duration'];/////
			/////////////////////////////////////////////////
			
			$appointmentArray 		= array();
			$chosenDay 				= date("Y-m-d",strtotime($dateValue));
			$nextDay 				= date("Y-m-d",strtotime($dateValue."+1 days"));
			$startDate				= $chosenDay . " 00:00:00";
			$endDate				= $nextDay . " 00:00:00";
			$startDate				= date("Y-m-d H:i:s", strtotime($startDate));
			$endDate				= date("Y-m-d H:i:s", strtotime($endDate));
			$employeeID 			= esc_sql($employeeID);
			$startDate	 			= esc_sql($startDate);
			$endDate	 			= esc_sql($endDate);
			$select	 				= "SELECT 
											a.start_date appStartDate,
											a.end_date appEndDate,
											a.service_id appServiceID,
											es.capacity_min serviceCapacityMin,
											es.capacity_max serviceCapacityMax,
											GROUP_CONCAT(ca.customer_id ORDER BY ca.id) customerIDs,
											GROUP_CONCAT(ca.number_of_people ORDER BY ca.id) customerPeopleCounts,
											GROUP_CONCAT(ca.status ORDER BY ca.id) customerStatuses

										FROM 	   	   {$wpdb->prefix}bmify_appointments a 
											INNER JOIN {$wpdb->prefix}bmify_customer_appointments ca 			ON ca.appointment_id = a.id 
											INNER JOIN {$wpdb->prefix}bmify_employee_services es 				ON a.service_id = es.service_id AND a.employee_id = es.employee_id
										
										WHERE a.employee_id=".$employeeID." AND a.start_date>='".$startDate."' AND a.start_date<'".$endDate."' AND a.status in ('pending', 'approved')  GROUP BY a.id ORDER BY a.start_date";
			$appointments 			= $wpdb->get_results( $select, OBJECT  );
			$additionalTimeSlots = array();
			foreach($appointments as $key => $appointment){
				$hasSlot = 0;
				$newServiceID					= $appointment->appServiceID;
				if($newServiceID == $serviceID){
					$serviceCapacityMax			= $appointment->serviceCapacityMax;
					$approvedPeopleCount 		= 0;
					
					$customerIDs 				= explode(',', $appointment->customerIDs); 					// creating array from string
					$customerStatuses 			= explode(',', $appointment->customerStatuses); 			// creating array from string
					$customerPeopleCounts 		= explode(',', $appointment->customerPeopleCounts); 		// creating array from string
					foreach($customerIDs as $key2 => $customerID){
						if($customerStatuses[$key2] == 'approved' || $customerStatuses[$key2] == 'pending'){
							$approvedPeopleCount += $customerPeopleCounts[$key2];
						}
					}
					if($serviceCapacityMax >= ($approvedPeopleCount + $peopleCount + 1)){
						$hasSlot = 1;
					}
					$approvedPeopleCount 		= 0;
				}
				if(is_numeric($newServiceID)){
					$newServiceID		= esc_sql($newServiceID);
					$select 			= "SELECT buffer_before, buffer_after FROM {$wpdb->prefix}bmify_services WHERE id=".$newServiceID;
					$results 			= $wpdb->get_results( $select, OBJECT  );
					$bufferBefore		= $results[0]->buffer_before / 60;
					$bufferAfter		= $results[0]->buffer_after / 60;
					$startDateInMinutes = date('H', strtotime($appointment->appStartDate))*60 + date('i', strtotime($appointment->appStartDate));
					$endDateInMinutes 	= date('H', strtotime($appointment->appEndDate))*60 + date('i', strtotime($appointment->appEndDate));
					if($hasSlot == 1){
						if(($endDateInMinutes - $startDateInMinutes) >= ($duration / 60)){
							$additionalTimeSlots[] = $startDateInMinutes;
						}
					}
					$startAppointment 	= $startDateInMinutes - $bufferBefore;
					$endAppointment		= $endDateInMinutes + $bufferAfter;
					$appointmentArray[$key]['start'] 	= $startAppointment;
					$appointmentArray[$key]['end'] 		= $endAppointment;
				}
					
			}
			// получение всевозможных слотов, которых нужно удалить из ОБЩЕГО масива (все ВСТРЕЧИ того дня)
			$removableValues = array();
			foreach($appointmentArray as $result){
				$min 	= intval($result['start']) - $summaryDuration;
				$max 	= intval($result['end']) + ($serviceBuffBefore / 60); // здесь мы прибавляем время buffer_before для того, чтобы работник смог подготовиться к работе после каждого перерыва
				$removableValues[] 	= array_filter($allArray, function ($value) use($min,$max)  { return ($value > $min && $value < $max); });
			}
			$removableArr = array();
			foreach($removableValues as $results){
				foreach($results as $result){
					$removableArr[] = $result;
				}
			}
			// удаление полученных слотов, которых нужно было удалить из ОБЩЕГО массива (все ВСТРЕЧИ того дня)
			$difference = array_diff($difference,$removableArr);
			//****************************************************************************************************************************************
			//****************************************************************************************************************************************
			// получение всевозможных GOOGLE встреч без учета встреч, созданных Bookmify, выбранного работника для выбранной даты в массиве
			$googleData 	= HelperEmployees::getGoogleData($employeeID);
			$accessToken	= '';
			$calID          = '';
			if($googleData != NULL){
				$googleData 	= json_decode(stripslashes($googleData), true);
				$accessToken 	= $googleData['accessToken'];
				$calID 			= $googleData['calendarID'];
				
				$google 		= new GoogleCalendarProject();
				if($accessToken != ''){
					$events		= $google->getGoogleEvents($employeeID,$chosenDay);

					// получение всевозможных слотов, которых нужно удалить из ОБЩЕГО масива (все ВСТРЕЧИ того дня)
					$removableValues = array();
					foreach($events as $result){
						$min 	= intval($result['start']) - $summaryDuration;
						$max 	= intval($result['end']);
						$removableValues[] 	= array_filter($allArray, function ($value) use($min,$max)  { return ($value > $min && $value < $max); });
					}
					$removableArr = array();
					foreach($removableValues as $results){
						foreach($results as $result){
							$removableArr[] = $result;
						}
					}
					// удаление полученных слотов, которых нужно было удалить из ОБЩЕГО массива (все ВСТРЕЧИ того дня)
					$difference = array_diff($difference,$removableArr);
				}
			}
			
			$html = '';
			
			
			// получение ГОТОВОГО массива с учетом полученного времени с минимум установленным временем до заказа, если выбран сегодняшний или же завтрашний день
			if($minTimeInMinutes != 0){
				$minTimeArray 		= array_filter($difference, function($value) use($minTimeInMinutes) {return ($value >= $minTimeInMinutes); });
				$minTimeArray 		= array_merge($minTimeArray,$additionalTimeSlots); // добавление в массив, тех встреч, где есть допольнительные места
				asort($minTimeArray); // сортировка массива после добавления
				foreach($minTimeArray as $result){
					$resHours 		= intval($result/60);
					if($resHours < 10){$resHours = "0".$resHours;}
					$resMinutes 	= $result % 60;
					if($resMinutes < 10){$resMinutes = "0".$resMinutes;}
					$hourMinutes 	= $resHours.":".$resMinutes;
					$timeHTML 		= date_i18n(get_option('bookmify_be_time_format', 'h:i a'),strtotime($hourMinutes));
					$html .= '<li>';
					$html .= 	'<div class="time_item">';
					$html .= 		'<input class="time_val" type="hidden" value="'.$hourMinutes.'" />';
					$html .= 		'<span>'.$timeHTML.'</span>';
					$html .= 	'</div>';
					$html .= '</li>';
				}
			}
			
			// получение ГОТОВОГО массива, если выбранная дата не явлется ни сегодняшней и ни завтрашней
			if($minTimeInMinutes == 0){
				$difference 		= array_merge($difference,$additionalTimeSlots); // добавление в массив, тех встреч, где есть допольнительные места
				asort($difference); // сортировка массива после добавления
				foreach($difference as $result){
					$resHours 		= intval($result/60);
					if($resHours < 10){$resHours = "0".$resHours;}
					$resMinutes 	= $result % 60;
					if($resMinutes < 10){$resMinutes = "0".$resMinutes;}
					$hourMinutes 	= $resHours.":".$resMinutes;
					$timeHTML 		= date_i18n(get_option('bookmify_be_time_format', 'h:i a'),strtotime($hourMinutes));
					$html .= '<li>';
					$html .= 	'<div class="time_item">';
					$html .= 		'<input class="time_val" type="hidden" value="'.$hourMinutes.'" />';
					$html .= 		'<span>'.$timeHTML.'</span>';
					$html .= 	'</div>';
					$html .= '</li>';
				}
			}
			
			
			if($html == ''){
				$timeResultHTML = '<div class="bookmify_fe_infobox danger"><label>'.esc_html__('Busy Day. Please, select another day.', 'bookmify').'</label></div>';
			}else{
				$timeResultHTML = '<ul>'.$html.'</ul>';
			}
			
			// ОТПРАВКА РЕЗУЛЬТАТА
			$timeResult  = '';
			$timeResult .= '<div class="time_header">';
			$timeResult .= 		'<h3>'.esc_html__('Time Slots', 'bookmify').'</h3>';
			$timeResult .= '</div>';
			$timeResult .= '<div class="time_content" id="bokmify_fe_alpha_time_content">';
			$timeResult .= 		$timeResultHTML;
			$timeResult .= '</div>';
			
			
			// remove whitespaces form the ajax HTML
			$search = array(
				'/\>[^\S ]+/s',  // strip whitespaces after tags, except space
				'/[^\S ]+\</s',  // strip whitespaces before tags, except space
				'/(\s)+/s'       // shorten multiple whitespace sequences
			);
			$replace = array(
				'>',
				'<',
				'\\1'
			);
			$timeResult = preg_replace($search, $replace, $timeResult);
			
			
			// Отправка обработанных данных на jQuery
			$buffyArray = array(
				'bookmify_be_data' 		=> $timeResult,//timeResult timezoneOffset timeSlots
				'extra_slots'			=> count($additionalTimeSlots),
			);
			
			if ( true === $isAjaxCall ) {die(json_encode($buffyArray));} 
			else {return json_encode($buffyArray);}
			
		}
	}
	
}