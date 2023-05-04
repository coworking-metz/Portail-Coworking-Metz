<?php
namespace Bookmify;

use Bookmify\Helper;


// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {exit; }


/**
 * Class HelperSettings
 */
class HelperSettings
{
	public static function bookmifySettingsGeneralTab(){
		global $wpdb;
		$result  = '';
		$result .= '<div class="title_holder">
						<h3>'.esc_html__('General', 'bookmify').'</h3>
					</div>

					<div class="general_items">';
		
		
		// ---------------------------------------
		// TIME OPTIONS START
		// ---------------------------------------
		$result .=		'<div class="general_item_group">';
		// DAY FORMAT
		$result .= 			'<div class="general_item">
								<div class="item_title">
									<label for="date_format" title="'.esc_attr__('Day Format', 'bookmify').'">'.esc_html__('Day Format', 'bookmify').'</label>
								</div>
								<div class="item_content">';

		$dayFormats = Helper::bookmifyDayFormats();
		$result .= 					'<select id="date_format" class="bookmify_be_date_format" name="bookmify_be_date_format">';

										$html = '';
										foreach($dayFormats as $format => $dayFormat){
											$html .= '<option value="'.$format.'" '.bookmify_be_selected(get_option( 'bookmify_be_date_format', 'd F, Y' ), $format).'>'.$dayFormat['ct'].'</option>';
										}
		$result .= 						$html;
		$result .= 					'</select>
								</div>
							</div>';
		// TIME FORMAT
		$result .= 			'<div class="general_item">
								<div class="item_title">
									<label for="time_format" title="'.esc_attr__('Time Format', 'bookmify').'">'.esc_html__('Time Format', 'bookmify').'</label>
								</div>
								<div class="item_content">';
									$timeFormats = Helper::bookmifyTimeFormats();
		$result .= 					'<select id="time_format" class="bookmify_be_time_format" name="bookmify_be_time_format">';

										$html = '';
										foreach($timeFormats as $format => $timeFormat){
											$html .= '<option value="'.$format.'" '.bookmify_be_selected(get_option( 'bookmify_be_time_format', 'h:i a' ), $format).'>'.$timeFormat['ct'].'</option>';
										}
		$result .=						$html;
		$result .= 					'</select>
								</div>
							</div>';

		// MINIMUM TIME TO BOOKING
		$result .=			'<div class="general_item">
								<div class="item_title">
									<label for="mintime_tobooking" title="'.esc_attr__('Minimum time to Booking', 'bookmify').'">'.esc_html__('Minimum time to Booking', 'bookmify').'</label>
								</div>
								<div class="item_content">';

									$minTimes = Helper::bookmifyMinTimeToBooking();

		$result .= 					'<select id="mintime_tobooking" class="bookmify_be_mintime_tobooking" name="bookmify_be_mintime_tobooking">';

										$html = '';
										foreach($minTimes as $format => $minTime){
											$html .= '<option value="'.$format.'" '.bookmify_be_selected(get_option( 'bookmify_be_mintime_tobooking', 'disabled' ), $format).'>'.$minTime['ct'].'</option>';
										}
		$result .= 						$html;
		$result .= 					'</select>
								</div>
							</div>';

		// MAXIMUM TIME TO BOOKING
		$result .=			'<div class="general_item">
								<div class="item_title">
									<label for="maxtime_tobooking" title="'.esc_attr__('Maximum time to Booking', 'bookmify').'">'.esc_html__('Maximum time to Booking', 'bookmify').'</label>
								</div>
								<div class="item_content">';

									$maxTimes = Helper::bookmifyMaxTimeToBooking();

		$result .= 					'<select id="maxtime_tobooking" class="bookmify_be_maxtime_tobooking" name="bookmify_be_maxtime_tobooking">';

										$html = '';
										foreach($maxTimes as $format => $maxTime){
											$html .= '<option value="'.$format.'" '.bookmify_be_selected(get_option( 'bookmify_be_maxtime_tobooking', 'disabled' ), $format).'>'.$maxTime['ct'].'</option>';
										}
		$result .= 						$html;
		$result .= 					'</select>
								</div>
							</div>';

		// MINIMUM TIME TO CANCEL
		$result .=			'<div class="general_item">
								<div class="item_title">
									<label for="mintime_tocancel" title="'.esc_attr__('Minimum time to Cancel', 'bookmify').'">'.esc_html__('Minimum time to Cancel', 'bookmify').'</label>
								</div>
								<div class="item_content">';

		$result .= 					'<select id="mintime_tocancel" class="bookmify_be_mintime_tocancel" name="bookmify_be_mintime_tocancel">';

										$html = '';
										foreach($minTimes as $format => $minTime){
											$html .= '<option value="'.$format.'" '.bookmify_be_selected(get_option( 'bookmify_be_mintime_tocancel', 'disabled' ), $format).'>'.$minTime['ct'].'</option>';
										}
		$result .= 						$html;
		$result .= 					'</select>
								</div>
							</div>';
		// TIME INTERVAL
		$result .=			'<div class="general_item">
								<div class="item_title">
									<label for="time_interval" title="'.esc_attr__('Time Interval', 'bookmify').'">'.esc_html__('Time Interval', 'bookmify').'</label>
								</div>
								<div class="item_content">';
		
		$timeIntervals = Helper::bookmifyTimeInterval();
		$result .= 					'<select id="time_interval" class="bookmify_be_time_interval" name="bookmify_be_time_interval">';

										$html = '';
										foreach($timeIntervals as $format => $timeInterval){
											$html .= '<option value="'.$format.'" '.bookmify_be_selected(get_option( 'bookmify_be_time_interval', '15' ), $format).'>'.$timeInterval['ct'].'</option>';
										}
		$result .= 						$html;
		$result .= 					'</select>
								</div>
							</div>';
		$timeSlotChecked		= '';
		if(get_option('bookmify_be_service_time_as_slot', '') == 'on'){
			$timeSlotChecked	 	= 'checked="checked"';
		}
		$result .= 			'<div class="general_item">
								<div class="item_title" title="'.esc_attr__('Service Duration as Time Slot', 'bookmify').'"><label for="service_time_as_slot">'.esc_html__('Service Duration as Time Slot', 'bookmify').'</label></div>
								<div class="item_content">
									<label class="bookmify_be_switch">
										<input type="checkbox" id="service_time_as_slot" name="bookmify_be_service_time_as_slot" '.esc_attr($timeSlotChecked).'  />
										<span class="slider round"></span>
									</label>
								</div>
							</div>';
		$clientTimeZoneChecked		= '';
		if(get_option('bookmify_be_client_timezone', '') == 'on'){
			$clientTimeZoneChecked	 	= 'checked="checked"';
		}
		$result .= 			'<div class="general_item">
								<div class="item_title" title="'.esc_attr__('Show Front-end Time Slots in Client Time Zone', 'bookmify').'"><label for="client_timezone">'.esc_html__('Time Slots in Client Time Zone', 'bookmify').'</label></div>
								<div class="item_content">
									<label class="bookmify_be_switch">
										<input type="checkbox" id="client_timezone" name="bookmify_be_client_timezone" '.esc_attr($clientTimeZoneChecked).'  />
										<span class="slider round"></span>
									</label>
								</div>
							</div>';
		$oldAppointmentChecked		= '';
		if(get_option('bookmify_be_old_appointment_action', '') == 'on'){
			$oldAppointmentChecked	 	= 'checked="checked"';
		}
		$result .= 			'<div class="general_item">
								<div class="item_title" title="'.esc_attr__('Old Appointments Modifying', 'bookmify').'"><label for="old_appointment_action">'.esc_html__('Old Appointments Modifying', 'bookmify').'</label></div>
								<div class="item_content">
									<label class="bookmify_be_switch">
										<input type="checkbox" id="old_appointment_action" name="bookmify_be_old_appointment_action" '.esc_attr($oldAppointmentChecked).'  />
										<span class="slider round"></span>
									</label>
								</div>
							</div>';
		$oldAppointmentChecked		= '';
		if(get_option('bookmify_be_phone_as_required', '') == 'on'){
			$oldAppointmentChecked	 	= 'checked="checked"';
		}
		$result .= 			'<div class="general_item">
								<div class="item_title" title="'.esc_attr__('Customer Phone as Required Field', 'bookmify').'"><label for="customer_phone_required">'.esc_html__('Customer Phone as Required Field', 'bookmify').'</label></div>
								<div class="item_content">
									<label class="bookmify_be_switch">
										<input type="checkbox" id="customer_phone_required" name="bookmify_be_phone_as_required" '.esc_attr($oldAppointmentChecked).'  />
										<span class="slider round"></span>
									</label>
								</div>
							</div>';
//		$clientTimeZoneOffsetChecked		= '';
//		if(get_option('bookmify_be_client_timezone', '') == 'on'){
//			$clientTimeZoneOffsetChecked	= 'checked="checked"';
//		}
//		$result .= 			'<div class="general_item">
//								<div class="item_title" title="'.esc_attr__('Client Time Zone Depended Time Slots', 'bookmify').'"><label for="client_timezone">'.esc_html__('Client Time Zone Depended Time Slots', 'bookmify').'</label></div>
//								<div class="item_content">
//									<label class="bookmify_be_switch">
//										<input type="checkbox" id="client_timezone" name="bookmify_be_client_timezone" '.esc_attr($clientTimeZoneOffsetChecked).'  />
//										<span class="slider round"></span>
//									</label>
//								</div>
//							</div>';
		$result .=		'</div>';
		// ---------------------------------------
		// TIME OPTIONS END
		// ---------------------------------------

		// ---------------------------------------
		// APPOINTMENT OPTIONS START
		// ---------------------------------------
		$result .=		'<div class="general_item_group">';
		$result .=			'<div class="general_item">
								<div class="item_title">
									<label for="default_app_status" title="'.esc_attr__('Front-end Appointment Status', 'bookmify').'">'.esc_html__('Front-end Appointment Status', 'bookmify').'</label>
								</div>
								<div class="item_content">';

									$statuses = Helper::bookmifyFrontEndAppointmentStatus();

		$result .= 					'<select id="default_app_status" class="bookmify_be_default_app_status" name="bookmify_be_default_app_status">';

										$html = '';
										foreach($statuses as $format => $status){
											$html .= '<option value="'.$format.'" '.bookmify_be_selected(get_option( 'bookmify_be_default_app_status', 'approved' ), $format).'>'.$status['ct'].'</option>';
										}
		$result .= 						$html;
		$result .= 					'</select>
								</div>
							 </div>';
		$result .= 			'<div class="general_item">
								<div class="item_title">
									<label title="'.esc_attr__('Appointments Per Page', 'bookmify').'">'.esc_html__('Appointments Per Page', 'bookmify').'</label>
								</div>
								<div class="item_content">
									<select name="bookmify_be_appointments_pp">';

										$html = '';
										$numbers = array('all' => esc_html__('Show All', 'bookmify'));
										for($i=1; $i <= 500;){$numbers[$i] = $i; $i++;}

											foreach($numbers as $key => $number){
												$html .= '<option value="'.$key.'" '.bookmify_be_selected(get_option( 'bookmify_be_appointments_pp', '10' ), $key).'>'.$number.'</option>';
											}
		$result .= 						$html.'
									</select>
								</div>
							</div>

							<div class="general_item">
								<div class="item_title">
									<label title="'.esc_attr__('Appointments Filter Date Range', 'bookmify').'">'.esc_html__('Appointments Filter Date Range', 'bookmify').'</label>
								</div>
								<div class="item_content">
									<select name="bookmify_be_appointments_daterange">';

										$html = '';
										$numbers = array();
										for($i=1; $i <= 90;){$numbers[$i] = $i; $i++;}

										foreach($numbers as $key => $number){
											$html .= '<option value="'.$key.'" '.bookmify_be_selected(get_option( 'bookmify_be_appointments_daterange', '30' ), $key).'>'.$number.'</option>';
										}
		$result .= 						$html.'
									</select>
								</div>
							</div>
						</div>';
		// ---------------------------------------
		// APPOINTMENT OPTIONS END
		// ---------------------------------------
		
		$result .= '</div>

					<div class="save_btn">
						<a class="bookmify_save_link" href="#">
							<span class="text">'.esc_html__('Save', 'bookmify').'</span>
							<span class="save_process">
								<span class="ball"></span>
								<span class="ball"></span>
								<span class="ball"></span>
							</span>
						</a>
					</div>';
		
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
		$result	= preg_replace($search, $replace, $result);
		return $result;
	}
	public static function bookmifySettingsCompanyInfoTab(){
		global $wpdb;
		$result  = '';
		$result .= '<div class="title_holder">
						<h3>'.esc_html__('Company Info', 'bookmify').'</h3>
					</div>

					<div class="general_items">';

						$attachmentID				= get_option( 'bookmify_be_company_info_img', '' );
						if($attachmentID != ''){
							$attachmentURLLarge		= Helper::bookmifyGetImageByID($attachmentID, 'large');
							$attachmentURL	 		= Helper::bookmifyGetImageByID($attachmentID);
						}else{
							$attachmentURLLarge 	= '';
							$attachmentURL 			= '';
						}

						if($attachmentURL != ''){$attOpened = 'has_image';}else{$attOpened = '';}
						if($attachmentURLLarge == ''){$attachmentURLLarge = $attachmentURL;}
		$result .= 		'<div class="general_item_group">
							<div class="general_item_left">
								<input type="hidden" class="bookmify_be_company_info_img" name="bookmify_be_company_info_img" value="'. esc_attr($attachmentID) .'" />
								<div class="bookmify_thumb_wrap '.esc_attr($attOpened).'" style="background-image:url('. esc_url($attachmentURLLarge) .')">
									<div class="bookmify_thumb_edit">
										<span class="edit">
											<img class="bookmify_be_svg" src="'.BOOKMIFY_ASSETS_URL.'img/image.svg" alt="" />
										</span>
									</div>
									<div class="bookmify_thumb_remove '.esc_attr($attOpened).'">
										<a href="#" class="bookmify_be_delete">
											<img class="bookmify_be_svg" src="'.BOOKMIFY_ASSETS_URL.'img/delete.svg" alt="" />
										</a>
									</div>
								</div>												
							</div>
							<div class="general_item_right">
								<div class="general_item">
									<div class="item_title"><label title="'.esc_attr__('Name', 'bookmify').'">'.esc_html__('Name', 'bookmify').'</label></div>
									<div class="item_content">
										<input id="company_info_name" type="text" name="bookmify_be_company_info_name" value="'.get_option( 'bookmify_be_company_info_name', '' ).'">
									</div>
								</div>
								<div class="general_item">
									<div class="item_title"><label title="'.esc_attr__('Address', 'bookmify').'">'.esc_html__('Address', 'bookmify').'</label></div>
									<div class="item_content">
										<input id="company_info_address" type="text" name="bookmify_be_company_info_address" value="'.get_option( 'bookmify_be_company_info_address', '' ).'">
									</div>
								</div>
								<div class="general_item">
									<div class="item_title"><label title="'.esc_attr__('Website', 'bookmify').'">'.esc_html__('Website', 'bookmify').'</label></div>
									<div class="item_content">
										<input id="company_info_website" type="text" name="bookmify_be_company_info_website" value="'.get_option( 'bookmify_be_company_info_website', '' ).'">
									</div>
								</div>
								<div class="general_item">
									<div class="item_title"><label title="'.esc_attr__('Phone', 'bookmify').'">'.esc_html__('Phone', 'bookmify').'</label></div>
									<div class="item_content">
										<input id="company_info_phone" type="tel" name="bookmify_be_company_info_phone" value="'.get_option( 'bookmify_be_company_info_phone', '' ).'">
										<span class="bot__btn"><img class="bookmify_be_svg" src="'.BOOKMIFY_ASSETS_URL.'img/down.svg" alt="" /></span>
									</div>											
								</div>
							</div>
						</div>


					</div>

					<div class="save_btn">
						<a class="bookmify_save_link" href="#">
							<span class="text">'.esc_html__('Save', 'bookmify').'</span>
							<span class="save_process">
								<span class="ball"></span>
								<span class="ball"></span>
								<span class="ball"></span>
							</span>
						</a>
					</div>';
		
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
		$result	= preg_replace($search, $replace, $result);
		return $result;
	}
	
	public static function bookmifySettingsNotificationTab(){
		global $wpdb;
		$result  = '';
		$result .= '<div class="title_holder">
						<h3>'.esc_html__('Notifications', 'bookmify').'</h3>
					</div>

					<div class="general_items">


						<div class="general_item_group">
							<div class="general_item">
								<div class="item_title"><label for="mail_service" title="'.esc_attr__('Mail Service', 'bookmify').'">'.esc_html__('Mail Service', 'bookmify').'</label></div>
								<div class="item_content">';
		$mailServices = Helper::bookmifyMailServices();
		$result .= 					'<select id="mail_service" class="bookmify_be_not_mail_service" name="bookmify_be_not_mail_service">';
										$html = '';
										foreach($mailServices as $format => $mailService){
											$html .= '<option value="'.$format.'" '.bookmify_be_selected(get_option( 'bookmify_be_not_mail_service', 'php' ), $format).'>'.$mailService['ct'].'</option>';
										}
		$result .= 					$html.'
									</select>
								</div>
							</div>
						</div>

						<div class="general_item_group">
							<div class="general_item">
								<div class="item_title"><label for="sender_name" title="'.esc_attr__('Sender Name', 'bookmify').'">'.esc_html__('Sender Name', 'bookmify').'</label></div>
								<div class="item_content">
									<input autocomplete="off" id="sender_name" name="bookmify_be_not_sender_name" type="text" value="'.get_option('bookmify_be_not_sender_name', '').'" />
								</div>
							</div>
							<div class="general_item">
								<div class="item_title"><label for="sender_email" title="'.esc_attr__('Sender Email', 'bookmify').'">'.esc_html__('Sender Email', 'bookmify').'</label></div>
								<div class="item_content">
									<input autocomplete="off" id="sender_email" name="bookmify_be_not_sender_email" type="text" value="'.get_option('bookmify_be_not_sender_email', '').'" />
								</div>
							</div>
						</div>';

						$smtpDisabled 		= '';
						if(get_option( 'bookmify_be_not_mail_service', 'php') != 'smtp'){
							$smtpDisabled 	= 'disabled';
						}

		$result .=		'<div class="general_item_group smtp_options '.esc_attr($smtpDisabled).'">
							<div class="general_item">
								<div class="item_title"><label for="smtp_host" title="'.esc_attr__('SMTP Host', 'bookmify').'">'.esc_html__('SMTP Host', 'bookmify').'</label></div>
								<div class="item_content">
									<input autocomplete="off" id="smtp_host" name="bookmify_be_not_smtp_host" type="text" value="'.get_option('bookmify_be_not_smtp_host', '').'" />
								</div>
							</div>
							<div class="general_item">
								<div class="item_title"><label for="smtp_port" title="'.esc_attr__('SMTP Port', 'bookmify').'">'.esc_html__('SMTP Port', 'bookmify').'</label></div>
								<div class="item_content">
									<input autocomplete="off" id="smtp_port" name="bookmify_be_not_smtp_port" type="text" value="'.get_option('bookmify_be_not_smtp_port', '').'" />
								</div>
							</div>
							<div class="general_item">
								<div class="item_title"><label for="smtp_username" title="'.esc_attr__('SMTP Username', 'bookmify').'">'.esc_html__('SMTP Username', 'bookmify').'</label></div>
								<div class="item_content">
									<input autocomplete="off" id="smtp_username" name="bookmify_be_not_smtp_username" type="text" value="'.get_option('bookmify_be_not_smtp_username', '').'" />
								</div>
							</div>
							<div class="general_item">
								<div class="item_title"><label for="smtp_password" title="'.esc_attr__('SMTP Password', 'bookmify').'">'.esc_html__('SMTP Password', 'bookmify').'</label></div>
								<div class="item_content">
									<input autocomplete="new-password" id="smtp_password" name="bookmify_be_not_smtp_pass" type="password" value="'.get_option('bookmify_be_not_smtp_pass', '').'" />
								</div>
							</div>
							<div class="general_item">
								<div class="item_title"><label for="smtp_secure" title="'.esc_attr__('SMTP Secure', 'bookmify').'">'.esc_html__('SMTP Secure', 'bookmify').'</label></div>
								<div class="item_content">';
		$smtpSecures = Helper::bookmifySMTPSecure();
		$result .=					'<select id="smtp_secure" class="bookmify_be_not_smtp_secure" name="bookmify_be_not_smtp_secure">';
										$html = '';
										foreach($smtpSecures as $format => $smtpSecure){
											$html .= '<option value="'.$format.'" '.bookmify_be_selected(get_option( 'bookmify_be_not_smtp_secure', 'disabled' ), $format).'>'.$smtpSecure['ct'].'</option>';
										}
		$result .=						$html.'
									</select>
								</div>
							</div>
						</div>
						
						
						<div class="general_item_group">
							<div class="general_item">
								<div class="item_title"><label for="twilio_account_sid" title="'.esc_attr__('Twilio Account SID', 'bookmify').'">'.esc_html__('Twilio Account SID', 'bookmify').'</label></div>
								<div class="item_content">
									<input autocomplete="off" id="twilio_account_sid" name="bookmify_be_twilio_account_sid" type="text" value="'.get_option('bookmify_be_twilio_account_sid', '').'" />
								</div>
							</div>
							<div class="general_item">
								<div class="item_title"><label for="twilio_auth_token" title="'.esc_attr__('Twilio Auth Token', 'bookmify').'">'.esc_html__('Twilio Auth Token', 'bookmify').'</label></div>
								<div class="item_content">
									<input autocomplete="off" id="twilio_auth_token" name="bookmify_be_twilio_auth_token" type="text" value="'.get_option('bookmify_be_twilio_auth_token', '').'" />
								</div>
							</div>
							<div class="general_item">
								<div class="item_title"><label for="twilio_number" title="'.esc_attr__('Twilio Number', 'bookmify').'">'.esc_html__('Twilio Number', 'bookmify').'</label></div>
								<div class="item_content">
									<input autocomplete="off" id="twilio_number" name="bookmify_be_twilio_number" type="text" value="'.get_option('bookmify_be_twilio_number', '').'" />
								</div>
							</div>
						</div>
			
					</div>




					<div class="save_btn">
						<a class="bookmify_save_link" href="#">
							<span class="text">'.esc_html__('Save', 'bookmify').'</span>
							<span class="save_process">
								<span class="ball"></span>
								<span class="ball"></span>
								<span class="ball"></span>
							</span>
						</a>
					</div>';
		
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
		$result	= preg_replace($search, $replace, $result);
		return $result;
	}
	
	public static function bookmifySettingsCalendarTab(){
		global $wpdb;
		$result  = '';
		$result .= '<div class="title_holder">
						<h3>'.esc_html__('Calendars', 'bookmify').'</h3>
					</div>

					<div class="general_items">';
			// ---------------------------------------
			// CALENDAR HOTKEYS OPTIONS START
			// ---------------------------------------
			$result .= 	'<div class="general_item_group hot_keys">';

			$hotkeyChecked			= '';
			$hotkeySwitch			= 'disabled';
			if(get_option('bookmify_be_calendar_hotkeys', '') == 'on'){
				$hotkeyChecked	 	= 'checked="checked"';
				$hotkeySwitch		= 'enabled';
			}
			$result .=						   '<div class="general_item">
													<div class="item_title"><label for="calendar_hotkeys" title="'.esc_attr__('Calendar Hot Keys', 'bookmify').'">'.esc_html__('Calendar Hot Keys', 'bookmify').'</label></div>
													<div class="item_content">
														<label class="bookmify_be_switch">
															<input type="checkbox" id="calendar_hotkeys" name="bookmify_be_calendar_hotkeys" '.esc_attr($hotkeyChecked).'  />
															<span class="slider round"></span>
														</label>
													</div>
												</div>

												<div class="general_item calendar_hotkeys '.esc_attr($hotkeySwitch).'">
													<div class="item_title">
														<label for="calendar_hotkeys_today" title="'.esc_attr__('Go To Today', 'bookmify').'">'.esc_html__('Go To Today', 'bookmify').'</label>
													</div>
													<div class="item_content">
														<input id="calendar_hotkeys_today" name="bookmify_be_calendar_hotkeys_today" type="text" value="'.get_option('bookmify_be_calendar_hotkeys_today', 't').'" maxlength="1" />
														<span></span>
													</div>
												</div>

												<div class="general_item calendar_hotkeys '.esc_attr($hotkeySwitch).'">
													<div class="item_title">
														<label for="calendar_hotkeys_month" title="'.esc_attr__('Month View', 'bookmify').'">'.esc_html__('Month View', 'bookmify').'</label>
													</div>
													<div class="item_content">
														<input id="calendar_hotkeys_month" name="bookmify_be_calendar_hotkeys_month" type="text" value="'.get_option('bookmify_be_calendar_hotkeys_month', 'm').'" maxlength="1" />
														<span></span>
													</div>
												</div>

												<div class="general_item calendar_hotkeys '.esc_attr($hotkeySwitch).'">
													<div class="item_title">
														<label for="calendar_hotkeys_week" title="'.esc_attr__('Week View', 'bookmify').'">'.esc_html__('Week View', 'bookmify').'</label>
													</div>
													<div class="item_content">
														<input id="calendar_hotkeys_week" name="bookmify_be_calendar_hotkeys_week" type="text" value="'.get_option('bookmify_be_calendar_hotkeys_week', 'w').'" maxlength="1" />
														<span></span>
													</div>
												</div>

												<div class="general_item calendar_hotkeys '.esc_attr($hotkeySwitch).'">
													<div class="item_title">
														<label for="calendar_hotkeys_day" title="'.esc_attr__('Day View', 'bookmify').'">'.esc_html__('Day View', 'bookmify').'</label>
													</div>
													<div class="item_content">
														<input id="calendar_hotkeys_day" name="bookmify_be_calendar_hotkeys_day" type="text" value="'.get_option('bookmify_be_calendar_hotkeys_day', 'd').'" maxlength="1" />
														<span></span>
													</div>
												</div>

												<div class="general_item calendar_hotkeys '.esc_attr($hotkeySwitch).'">
													<div class="item_title">
														<label for="calendar_hotkeys_list" title="'.esc_attr__('List View', 'bookmify').'">'.esc_html__('List View', 'bookmify').'</label>
													</div>
													<div class="item_content">
														<input id="calendar_hotkeys_list" name="bookmify_be_calendar_hotkeys_list" type="text" value="'.get_option('bookmify_be_calendar_hotkeys_list', 'l').'" maxlength="1" />
														<span></span>
													</div>
												</div>

												<div class="general_item calendar_hotkeys '.esc_attr($hotkeySwitch).'">
													<div class="item_title">
														<label for="calendar_hotkeys_prev" title="'.esc_attr__('Go To Previous', 'bookmify').'">'.esc_html__('Go To Previous', 'bookmify').'</label>
													</div>
													<div class="item_content">
														<input id="calendar_hotkeys_prev" name="bookmify_be_calendar_hotkeys_prev" type="text" value="'.get_option('bookmify_be_calendar_hotkeys_prev', 'ArrowLeft').'" maxlength="1" />
														<span></span>
													</div>
												</div>

												<div class="general_item calendar_hotkeys '.esc_attr($hotkeySwitch).'">
													<div class="item_title">
														<label for="calendar_hotkeys_next" title="'.esc_attr__('Go To Next', 'bookmify').'">'.esc_html__('Go To Next', 'bookmify').'</label>
													</div>
													<div class="item_content">
														<input id="calendar_hotkeys_next" name="bookmify_be_calendar_hotkeys_next" type="text" value="'.get_option('bookmify_be_calendar_hotkeys_next', 'ArrowRight').'" maxlength="1" />
														<span></span>
													</div>
												</div>

												<div class="general_item calendar_hotkeys '.esc_attr($hotkeySwitch).'">
													<div class="item_title">
														<label for="calendar_hotkeys_reset" title="'.esc_attr__('Reset All Filters', 'bookmify').'">'.esc_html__('Reset All Filters', 'bookmify').'</label>
													</div>
													<div class="item_content">
														<input id="calendar_hotkeys_reset" name="bookmify_be_calendar_hotkeys_reset" type="text" value="'.get_option('bookmify_be_calendar_hotkeys_reset', 'r').'" maxlength="1" />
														<span></span>
													</div>
												</div>


											</div>';
			// ---------------------------------------
			// CALENDAR HOTKEYS OPTIONS END
			// ---------------------------------------
			$calAppPendingChecked		= '';
			if(get_option('bookmify_be_calendar_app_pending', 'on') == 'on'){
				$calAppPendingChecked	= 'checked="checked"';
			}
			$calAppCanceledChecked		= '';
			if(get_option('bookmify_be_calendar_app_canceled', 'on') == 'on'){
				$calAppCanceledChecked	= 'checked="checked"';
			}
			$calAppRejectedChecked		= '';
			if(get_option('bookmify_be_calendar_app_rejected', 'on') == 'on'){
				$calAppRejectedChecked	= 'checked="checked"';
			}
			$result 	.= '<div class="general_item_group">
			
								<div class="general_item">
									<div class="item_title"><label for="calendar_app_pending" title="'.esc_attr__('Add pending appointments to Calendar', 'bookmify').'">'.esc_html__('Add pending appointments to Calendar', 'bookmify').'</label></div>
									<div class="item_content">
										<label class="bookmify_be_switch">
											<input type="checkbox" id="calendar_app_pending" name="bookmify_be_calendar_app_pending" '.esc_attr($calAppPendingChecked).'  />
											<span class="slider round"></span>
										</label>
									</div>
								</div>
			
								<div class="general_item">
									<div class="item_title"><label for="calendar_app_canceled" title="'.esc_attr__('Add canceled appointments to Calendar', 'bookmify').'">'.esc_html__('Add canceled appointments to Calendar', 'bookmify').'</label></div>
									<div class="item_content">
										<label class="bookmify_be_switch">
											<input type="checkbox" id="calendar_app_canceled" name="bookmify_be_calendar_app_canceled" '.esc_attr($calAppCanceledChecked).'  />
											<span class="slider round"></span>
										</label>
									</div>
								</div>
			
								<div class="general_item">
									<div class="item_title"><label for="calendar_app_rejected" title="'.esc_attr__('Add rejected appointments to Calendar', 'bookmify').'">'.esc_html__('Add rejected appointments to Calendar', 'bookmify').'</label></div>
									<div class="item_content">
										<label class="bookmify_be_switch">
											<input type="checkbox" id="calendar_app_rejected" name="bookmify_be_calendar_app_rejected" '.esc_attr($calAppRejectedChecked).'  />
											<span class="slider round"></span>
										</label>
									</div>
								</div>
								
							</div>';
			
			$result .= '</div>




					<div class="save_btn">
						<a class="bookmify_save_link" href="#">
							<span class="text">'.esc_html__('Save', 'bookmify').'</span>
							<span class="save_process">
								<span class="ball"></span>
								<span class="ball"></span>
								<span class="ball"></span>
							</span>
						</a>
					</div>';
		
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
		$result	= preg_replace($search, $replace, $result);
		return $result;
	}
	
	public static function bookmifyOnlyOneEmployee(){
		$result = array(
			'default' 			=> array( 'ct' => esc_html__('Enable select', 'bookmify')),
			'remove' 			=> array( 'ct' => esc_html__('Disable the section', 'bookmify')),
			'close' 			=> array( 'ct' => esc_html__('Enable the section', 'bookmify')),
		);
		
		return $result;
	}
	
	public static function bookmifyDefaultFonts(){
		$result = array(
			'arial' 			=> array( 'ct' => esc_html__('Arial', 'bookmify')),
			'roboto' 			=> array( 'ct' => esc_html__('Roboto', 'bookmify')),
			'times_new_roman' 	=> array( 'ct' => esc_html__('Times New Roman', 'bookmify')),
			'times' 			=> array( 'ct' => esc_html__('Times', 'bookmify')),
			'courier_new' 		=> array( 'ct' => esc_html__('Courier New', 'bookmify')),
			'courier' 			=> array( 'ct' => esc_html__('Courier', 'bookmify')),
			'verdana' 			=> array( 'ct' => esc_html__('Verdana', 'bookmify')),
			'georgia' 			=> array( 'ct' => esc_html__('Georgia', 'bookmify')),
			'palatino' 			=> array( 'ct' => esc_html__('Palatino', 'bookmify')),
			'garamond' 			=> array( 'ct' => esc_html__('Garamond', 'bookmify')),
			'bookman' 			=> array( 'ct' => esc_html__('Bookman', 'bookmify')),
			'comic_sans_ms' 	=> array( 'ct' => esc_html__('Comic Sans MS', 'bookmify')),
			'candara' 			=> array( 'ct' => esc_html__('Candara', 'bookmify')),
			'arial_black' 		=> array( 'ct' => esc_html__('Arial Black', 'bookmify')),
			'impact' 			=> array( 'ct' => esc_html__('Impact', 'bookmify')),
		);
		
		return $result;
	}
	public static function bookmifyEnabledDisabled()
	{
		$result = array(
			'enabled' 		=> array( 'ct' => esc_html__('Enabled', 'bookmify')),
			'disabled' 		=> array( 'ct' => esc_html__('Disabled', 'bookmify')),
		);
		
		return $result;
	}
	
	public static function bookmifySettingsFrontEndTab(){
		$result  				= '';
		
		$successChecked			= '';
		$successSwitch			= 'disabled';
		if(get_option('bookmify_be_fe_conf_switcher', '') == 'on'){
			$successChecked	 	= 'checked="checked"';
			$successSwitch		= 'enabled';
		}
		
		$result .= '<div class="title_holder">
						<h3>'.esc_html__('Front-end Customizations', 'bookmify').'</h3>
					</div>

					<div class="general_items">
						
						<div class="general_item_group">
						
							<div class="general_item">
								<div class="item_title"><label for="fe_conf_switcher" title="'.esc_attr__('Confirmation Section', 'bookmify').'">'.esc_html__('Confirmation Section', 'bookmify').'</label></div>
								<div class="item_content">
									<label class="bookmify_be_switch">
										<input type="checkbox" id="fe_conf_switcher" name="bookmify_be_fe_conf_switcher" '.esc_attr($successChecked).'  />
										<span class="slider round"></span>
									</label>
								</div>
							</div>
						
							<div class="general_item confirm_item '.$successSwitch.'">
								<div class="item_title"><label for="fe_conf_title" title="'.esc_attr__('Success Title', 'bookmify').'">'.esc_html__('Success Title', 'bookmify').'</label></div>
								<div class="item_content">
									<input id="fe_conf_title" type="text" name="bookmify_be_fe_conf_title" value="'.get_option( 'bookmify_be_fe_conf_title', 'Thank you!').'">
								</div>
							</div>
						
							<div class="general_item confirm_item '.$successSwitch.'">
								<div class="item_title"><label for="fe_conf_desc" title="'.esc_attr__('Success Description', 'bookmify').'">'.esc_html__('Success Description', 'bookmify').'</label></div>
								<div class="item_content">
									<input id="fe_conf_desc" type="text" name="bookmify_be_fe_conf_desc" value="'.get_option( 'bookmify_be_fe_conf_desc', 'Your appointment is succesfully received. Please meet us at your selected date and time.').'">
								</div>
							</div>
						
							<div class="general_item confirm_item '.$successSwitch.'">
								<div class="item_title"><label for="fe_conf_footer" title="'.esc_attr__('Success Footer', 'bookmify').'">'.esc_html__('Success Footer', 'bookmify').'</label></div>
								<div class="item_content">
									<input id="fe_conf_footer" type="text" name="bookmify_be_fe_conf_footer" value="'.get_option( 'bookmify_be_fe_conf_footer', 'For any kind of inquiry, please call us at 543-323-3456' ).'">
								</div>
							</div>
						
							<div class="general_item confirm_item '.$successSwitch.'">
								<div class="item_title"><label for="fe_conf_service_back" title="'.esc_attr__('Back to services', 'bookmify').'">'.esc_html__('Back to services', 'bookmify').'</label></div>
								<div class="item_content">
									<input id="fe_conf_service_back" type="text" name="bookmify_be_fe_conf_service_back" value="'.get_option( 'bookmify_be_fe_conf_service_back', 'Go to services' ).'">
								</div>
							</div>
							
						</div>
						

						<div class="general_item_group">
							<div class="general_item">
								<div class="item_title"><label for="only_one_employee" title="'.esc_attr__('Single Employee Condition', 'bookmify').'">'.esc_html__('Single Employee Condition', 'bookmify').'</label></div>
								<div class="item_content">';
		$onlyOneEmployee = self::bookmifyOnlyOneEmployee();
		$result .= 					'<select id="only_one_employee" class="bookmify_be_feoption_only_one_emp" name="bookmify_be_feoption_only_one_emp">';
										$html = '';
										foreach($onlyOneEmployee as $format => $res){
											$html .= '<option value="'.$format.'" '.bookmify_be_selected(get_option( 'bookmify_be_feoption_only_one_emp', 'default' ), $format).'>'.$res['ct'].'</option>';
										}
		$result .= 					$html.'
									</select>
								</div>
							</div>
							<div class="general_item">
								<div class="item_title"><label for="enable_developed_fe" title="'.esc_attr__('"Developed By" Section', 'bookmify').'">'.esc_html__('"Developed By" Section', 'bookmify').'</label></div>
								<div class="item_content">';
		$enabledDisabled = self::bookmifyEnabledDisabled();
		$result .= 					'<select id="enable_developed_fe" class="bookmify_be_feoption_enable_deveoped_fe" name="bookmify_be_feoption_enable_deveoped_fe">';
										$html = '';
										foreach($enabledDisabled as $format => $res){
											$html .= '<option value="'.$format.'" '.bookmify_be_selected(get_option( 'bookmify_be_feoption_enable_deveoped_fe', 'enabled' ), $format).'>'.$res['ct'].'</option>';
										}
		$result .= 					$html.'
									</select>
								</div>
							</div>
							<div class="general_item">
								<div class="item_title"><label for="enable_category_fe_alpha" title="'.esc_attr__('Category Filter', 'bookmify').'">'.esc_html__('Category Filter', 'bookmify').'</label></div>
								<div class="item_content">';
		$result .= 					'<select id="enable_category_fe_alpha" class="bookmify_be_feoption_category_filter_alpha" name="bookmify_be_feoption_category_filter_alpha">';
										$html = '';
										foreach($enabledDisabled as $format => $res){
											$html .= '<option value="'.$format.'" '.bookmify_be_selected(get_option( 'bookmify_be_feoption_category_filter_alpha', 'enabled' ), $format).'>'.$res['ct'].'</option>';
										}
		$result .= 					$html.'
									</select>
								</div>
							</div>
							<div class="general_item">
								<div class="item_title"><label for="enable_service_details" title="'.esc_attr__('Service Details', 'bookmify').'">'.esc_html__('Service Details', 'bookmify').'</label></div>
								<div class="item_content">';
		$result .= 					'<select id="enable_service_details" class="bookmify_be_feoption_service_details" name="bookmify_be_feoption_service_details">';
										$html = '';
										foreach($enabledDisabled as $format => $res){
											$html .= '<option value="'.$format.'" '.bookmify_be_selected(get_option( 'bookmify_be_feoption_service_details', 'disabled' ), $format).'>'.$res['ct'].'</option>';
										}
		$result .= 					$html.'
									</select>
								</div>
							</div>
							<div class="general_item">
								<div class="item_title"><label for="autocreate_bookmify_user" title="'.esc_attr__('Create Bookmify customer by default', 'bookmify').'">'.esc_html__('Create Bookmify customer by default', 'bookmify').'</label></div>
								<div class="item_content">';
		$enabledDisabled = self::bookmifyEnabledDisabled();
		$result .= 					'<select id="autocreate_bookmify_user" class="bookmify_be_feoption_autocreate_bookmify_user" name="bookmify_be_feoption_autocreate_bookmify_user">';
										$html = '';
										foreach($enabledDisabled as $format => $res){
											$html .= '<option value="'.$format.'" '.bookmify_be_selected(get_option( 'bookmify_be_feoption_autocreate_bookmify_user', 'enabled' ), $format).'>'.$res['ct'].'</option>';
										}
		$result .= 					$html.'
									</select>
								</div>
							</div>
						</div>';
						
						
		$successChecked			= '';
		$successSwitch			= 'enabled';
		if(get_option('bookmify_be_feoption_gfont_switcher', 'on') == 'on'){
			$successChecked	 	= 'checked="checked"';
			$successSwitch		= 'disabled';
		}
						
		$result		.=	'<div class="general_item_group">
							<div class="general_item">
								<div class="item_title"><label for="gfont_switcher" title="'.esc_attr__('Google Font', 'bookmify').'">'.esc_html__('Google Font', 'bookmify').'</label></div>
								<div class="item_content">
									<label class="bookmify_be_switch">
										<input type="checkbox" id="gfont_switcher" name="bookmify_be_feoption_gfont_switcher" '.esc_attr($successChecked).'  />
										<span class="slider round"></span>
									</label>
								</div>
							</div>
						
							<div class="general_item gfont_item '.$successSwitch.'">
								<div class="item_title"><label for="default_body_font" title="'.esc_attr__('Default Body Font', 'bookmify').'">'.esc_html__('Default Body Font', 'bookmify').'</label></div>
								<div class="item_content">';
		$defaultFonts = self::bookmifyDefaultFonts();
		$result .= 					'<select id="default_body_font" class="bookmify_be_feoption_default_body_font" name="bookmify_be_feoption_default_body_font">';
										$html = '';
										foreach($defaultFonts as $format => $res){
											$html .= '<option value="'.$format.'" '.bookmify_be_selected(get_option( 'bookmify_be_feoption_default_body_font', 'arial' ), $format).'>'.$res['ct'].'</option>';
										}
		$result .= 					$html.'
									</select>
								</div>
							</div>
						
							<div class="general_item gfont_item '.$successSwitch.'">
								<div class="item_title"><label for="default_title_font" title="'.esc_attr__('Default Title Font', 'bookmify').'">'.esc_html__('Default Title Font', 'bookmify').'</label></div>
								<div class="item_content">';
		$defaultFonts = self::bookmifyDefaultFonts();
		$result .= 					'<select id="default_title_font" class="bookmify_be_feoption_default_title_font" name="bookmify_be_feoption_default_title_font">';
										$html = '';
										foreach($defaultFonts as $format => $res){
											$html .= '<option value="'.$format.'" '.bookmify_be_selected(get_option( 'bookmify_be_feoption_default_title_font', 'courier' ), $format).'>'.$res['ct'].'</option>';
										}
		$result .= 					$html.'
									</select>
								</div>
							</div>
						</div>
						
						<div class="general_item_group">
							<div class="general_item">
								<div class="item_title"><label for="main_color_1" title="'.esc_attr__('Main Color #1', 'bookmify').'">'.esc_html__('Main Color #1', 'bookmify').'</label></div>
								<div class="item_content">
									<input id="main_color_1" type="text" name="bookmify_be_feoption_main_color_1" class="bookmify_be_feoption_main_color_1 bookmify_color_picker" value="'.get_option('bookmify_be_feoption_main_color_1', '#5473e8').'" />
								</div>
							</div>
							<div class="general_item">
								<div class="item_title"><label for="main_color_2" title="'.esc_attr__('Main Color #2', 'bookmify').'">'.esc_html__('Main Color #2', 'bookmify').'</label></div>
								<div class="item_content">
									<input id="main_color_2" type="text" name="bookmify_be_feoption_main_color_2" class="bookmify_be_feoption_main_color_2 bookmify_color_picker" value="'.get_option('bookmify_be_feoption_main_color_2', '#35d8ac').'" />
								</div>
							</div>
							<div class="general_item">
								<div class="item_title"><label for="main_color_3" title="'.esc_attr__('Main Color #3', 'bookmify').'">'.esc_html__('Main Color #3', 'bookmify').'</label></div>
								<div class="item_content">
									<input id="main_color_3" type="text" name="bookmify_be_feoption_main_color_3" class="bookmify_be_feoption_main_color_3 bookmify_color_picker" value="'.get_option('bookmify_be_feoption_main_color_3', '#7e849b').'" />
								</div>
							</div>
						</div>
						
					</div>




					<div class="save_btn">
						<a class="bookmify_save_link" href="#">
							<span class="text">'.esc_html__('Save', 'bookmify').'</span>
							<span class="save_process">
								<span class="ball"></span>
								<span class="ball"></span>
								<span class="ball"></span>
							</span>
						</a>
					</div>';
		return $result;
	}
	
	
	public static function getDefaultFontForFrontEnd(){
		if(get_option('bookmify_be_feoption_gfont_switcher', 'on') != 'on'){
			$selectedBodyFont = get_option('bookmify_be_feoption_default_body_font', 'arial');
			switch($selectedBodyFont){
				default:
				case 'arial': $resultBodyFont = 'Arial'; break;
				case 'roboto': $resultBodyFont = 'Roboto'; break;
				case 'times_new_roman': $resultBodyFont = 'Times New Roman'; break;
				case 'times': $resultBodyFont = 'Times'; break;
				case 'courier_new': $resultBodyFont = 'Courier New'; break;
				case 'courier': $resultBodyFont = 'Courier'; break;
				case 'verdana': $resultBodyFont = 'Verdana'; break;
				case 'georgia': $resultBodyFont = 'Georgia'; break;
				case 'garamond': $resultBodyFont = 'Garamond'; break;
				case 'bookman': $resultBodyFont = 'Bookman'; break;
				case 'comic_sans_ms': $resultBodyFont = 'Comic Sans MS'; break;
				case 'candara': $resultBodyFont = 'Candara'; break;
				case 'arial_black': $resultBodyFont = 'Arial Black'; break;
				case 'impact': $resultBodyFont = 'Impact'; break;
			}
			$selectedTitleFont = get_option('bookmify_be_feoption_default_title_font', 'courier');
			switch($selectedTitleFont){
				default:
				case 'arial': $resultTitleFont = 'Arial'; break;
				case 'roboto': $resultTitleFont = 'Roboto'; break;
				case 'times_new_roman': $resultTitleFont = 'Times New Roman'; break;
				case 'times': $resultTitleFont = 'Times'; break;
				case 'courier_new': $resultTitleFont = 'Courier New'; break;
				case 'courier': $resultTitleFont = 'Courier'; break;
				case 'verdana': $resultTitleFont = 'Verdana'; break;
				case 'georgia': $resultTitleFont = 'Georgia'; break;
				case 'garamond': $resultTitleFont = 'Garamond'; break;
				case 'bookman': $resultTitleFont = 'Bookman'; break;
				case 'comic_sans_ms': $resultTitleFont = 'Comic Sans MS'; break;
				case 'candara': $resultTitleFont = 'Candara'; break;
				case 'arial_black': $resultTitleFont = 'Arial Black'; break;
				case 'impact': $resultTitleFont = 'Impact'; break;
			}
			return array($resultBodyFont,$resultTitleFont);
		}else{
			return '';
		}
	}
	
}

