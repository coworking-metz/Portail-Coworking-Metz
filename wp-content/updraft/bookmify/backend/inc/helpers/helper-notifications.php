<?php
namespace Bookmify;

use Bookmify\Helper;


// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {exit; }


/**
 * Class Helper Notifications
 */
class HelperNotifications
{
	
	public static function xTimeBeforeAppointmentPopup(){
		global $wpdb;
		$array			= Helper::bookmifyTimeBeforeAppointment();
		$timeDiv		= '<div class="nano scrollbar-inner scheduled_nano_2"><div class="nano-content">';
		foreach($array as $format => $arr){
			$timeDiv .= '<div data-val="'.$format.'">'.$arr['ct'].'</div>';
		}
		$timeDiv .= '</div></div>';
		return $timeDiv;
	}
	
	public static function hoursPopup(){
		global $wpdb;
		$timeInterval 	= 30;
		$countInterval	= 1440 / $timeInterval;
		$timeDiv		= '<div class="nano scrollbar-inner scheduled_nano"><div class="nano-content">';
		$startTime 		= strtotime('00:00');
		for($i = 0; $i < $countInterval; $i++){
			$timeDiv .= '<div data-val="'.date('H:i', strtotime('+'.$i*$timeInterval.' minutes', $startTime)).'">'.date_i18n(get_option('bookmify_be_time_format', 'h:i a'), strtotime('+'.$i*$timeInterval.' minutes', $startTime)).'</div>';
		}
		$timeDiv .= '</div></div>';
		return $timeDiv;
	}
	/**
	 * @since 1.0.0
	 * @access public
	*/
	public static function notificationsListForCustomer($platform = 'email')
	{
		global $wpdb;
		
		$query 	= "SELECT * FROM {$wpdb->prefix}bmify_notifications WHERE to_customer=1 AND platform='".$platform."'";
		$result = $wpdb->get_results( $query );
		
		
		$html = '<div class="bookmify_be_list notifications_list">';
		
		foreach($result as $notifications){
			$notificationID 			= $notifications->id;
			$notificationMessage 		= $notifications->message;
			$notificationStatus 		= $notifications->status;
			$notificationSubject 		= $notifications->subject;
			$notificationType 			= $notifications->type;
			$notificationCron 			= $notifications->cron;
			$notificationCheckTime		= $notifications->check_time;
			$notificationTimeInterval	= $notifications->time_interval;
			$notTimeIntervalInTimeFormat = Helper::bookmifyNumberToDuration($notificationTimeInterval*3600);
			
			$notCheckTimeInHi			= date('H:i', strtotime($notificationCheckTime));
			$notCheckTimeInTimeFormat 	= date_i18n(get_option('bookmify_be_time_format', 'h:i a'), strtotime($notificationCheckTime));
			$wpEditor 					= Helper::bookmifyBeWpHtmlEditor($notificationID, $notificationMessage, $platform);
			
			$cronClass					= 'notification_item platform_'.$platform;
			if($notificationCron == 1){
				$cronClass				= 'notification_item notification_cron_item platform_'.$platform;
			}
			$html .= '<div class="bookmify_be_list_item '.$cronClass.'" data-notification-id="'.$notificationID.'">
						<div class="bookmify_be_list_item_in">
							<div class="bookmify_be_list_item_header">
								<div class="header_in">
									<div class="info_holder">
										<div class="info_in">
											<span class="notification_status">
												<span class="bookmify_be_checkbox">
													<input class="status" type="checkbox" '.checked( $notificationStatus, 1, false ).'>
													<span class="checkmark">
														<img class="bookmify_be_svg" src="'.BOOKMIFY_ASSETS_URL.'/img/checked.svg" alt="" />
													</span>
												</span>
											</span>
											<span class="notification_subject">'.$notificationSubject.'</span>
										</div>
									</div>

									<div class="buttons_holder">';
			if($notificationCron == 1){
				$html .=				'<div class="btn_item btn_scheduled">
											<a class="bookmify_be_scheduled protip" data-pt-target="true" data-pt-title="'.esc_attr__('Scheduled Notification', 'bookmify').'" data-pt-gravity="left -4 0">
												<img class="bookmify_be_svg" src="'.BOOKMIFY_ASSETS_URL.'img/countdown.svg" alt="" />
											</a>
										</div>';
			}
			$html .=					'<div class="btn_item btn_edit">
											<a href="#" class="bookmify_be_edit">
												<img class="bookmify_be_svg edit" src="'.BOOKMIFY_ASSETS_URL.'img/edit.svg" alt="" />
												<img class="bookmify_be_svg checked" src="'.BOOKMIFY_ASSETS_URL.'img/checked.svg" alt="" />
											</a>
										</div>
									</div>
								</div>
							</div>

							<div class="bookmify_be_list_item_content">
								<div class="bookmify_be_list_item_content_in">
								
									<div class="closer_button"><a href="#"><img class="bookmify_be_svg" src="'.BOOKMIFY_ASSETS_URL.'/img/cancel.svg" alt="" /></a></div>

									<div class="bookmify_be_form_wrap">
										
										<div class="input_wrap_row ';if($notificationCron == 1){$html .= 'double_input';}$html.='">
											<div class="input_wrap subject_holder">
												<label><span class="title">'.esc_html__('Subject','bookmify').'<span>*</span></span></label>
												<input type="text" class="notification_subject"  value="'.$notificationSubject.'" />
											</div>';
							if($notificationType == 'customer_reminder_prev_day'){
							$html .= 		'<div class="input_wrap scheduled_holder one_day">
												<label><span class="title">'.esc_html__('Scheduled For','bookmify').'<span>*</span></span></label>
												<input type="text" name="scheduled_for" placeholder="'.esc_attr__('Select Time','bookmify').'" value="'.$notCheckTimeInTimeFormat.'" readonly />
												<input class="required_field" type="hidden" name="scheduled_for_hidden" value="'.$notCheckTimeInHi.'">
											</div>';
							}else if($notificationType == 'customer_reminder_x_before'){
							$html .= 		'<div class="input_wrap scheduled_holder x_time">
												<label><span class="title">'.esc_html__('Before...','bookmify').'<span>*</span></span></label>
												<input type="text" name="scheduled_for" placeholder="'.esc_attr__('Select Time','bookmify').'" value="'.$notTimeIntervalInTimeFormat.'" readonly />
												<input class="required_field" type="hidden" name="scheduled_for_hidden" value="'.$notificationTimeInterval.'">
											</div>';
							}
							$html .= 		'<input type="hidden" value="'.$notificationType.'" class="not_hid_type" />
										</div>
										
										<div class="input_wrap_row">
											<div class="input_wrap content_holder">
												<label><span class="title">'.esc_html__('Message','bookmify').'</span></label>
												'.$wpEditor.'
											</div>
										</div>
										
										<div class="input_wrap_row notifications_buttons_holder">
											<div class="bookmify_be_main_save_button">
												<a href="#">
													<span class="text">'.esc_html__('Save', 'bookmify').'</span>
													<span class="save_process">
														<span class="ball"></span>
														<span class="ball"></span>
														<span class="ball"></span>
													</span>
												</a>
												'.self::emailTestHtml($platform).'
											</div>
											
											
										</div>
									</div>
								
								</div>
								
							</div>
						</div>
					</div>';
			
		}
		
		$html .= '</div>';
		
		
		return $html;
	}
	
	/**
	 * @since 1.0.0
	 * @access private
	*/
    public static function emailTestHtml($platform = 'email'){
        global $wpdb;
		$html = '';
		if($platform == 'email'){
			$html .= 	'<div class="bookmify_be_test_email_form_wrap">
							<input class="recipient_email" type="text" placeholder="'.esc_attr__('Recipient Email', 'bookmify').'">
							<a href="#" class="te_send">
								<span class="text">'.esc_html__('Send Test Email', 'bookmify').'</span>
								<span class="save_process">
									<span class="ball"></span>
									<span class="ball"></span>
									<span class="ball"></span>
								</span>
							</a>
						</div>';
		}else{
			$html .= 	'<div class="bookmify_be_test_email_form_wrap">
							<input class="recipient_phone" type="text" placeholder="'.esc_attr__('Recipient Number', 'bookmify').'">
							<a href="#" class="te_send_sms">
								<span class="text">'.esc_html__('Send Test SMS', 'bookmify').'</span>
								<span class="save_process">
									<span class="ball"></span>
									<span class="ball"></span>
									<span class="ball"></span>
								</span>
							</a>
						</div>';
		}
		
		return $html;
    }
	/**
	 * @since 1.0.0
	 * @access public
	*/
	public static function notificationsListForEmployee($platform = 'email')
	{
		global $wpdb;
		
		$query 	= "SELECT * FROM {$wpdb->prefix}bmify_notifications WHERE to_employee=1 AND platform='".$platform."'";
		$result = $wpdb->get_results( $query );
		
		
		$html = '<div class="bookmify_be_list notifications_list">';
		
		foreach($result as $notifications){
			$notificationID 			= $notifications->id;
			$notificationMessage 		= $notifications->message;
			$notificationStatus 		= $notifications->status;
			$notificationSubject 		= $notifications->subject;
			$notificationType 			= $notifications->type;
			$notificationCron 			= $notifications->cron;
			$notificationCheckTime		= $notifications->check_time;
			$notificationTimeInterval	= $notifications->time_interval;
			$notTimeIntervalInTimeFormat = Helper::bookmifyNumberToDuration($notificationTimeInterval*3600);
			
			$notCheckTimeInHi			= date('H:i', strtotime($notificationCheckTime));
			$notCheckTimeInTimeFormat 	= date(get_option('bookmify_be_time_format', 'h:i a'), strtotime($notificationCheckTime));
			$wpEditor 					= Helper::bookmifyBeWpHtmlEditor($notificationID, $notificationMessage, $platform);
			
			$cronClass					= 'notification_item platform_'.$platform;
			if($notificationCron == 1){
				$cronClass				= 'notification_item notification_cron_item platform_'.$platform;
			}
			$html .= '<div class="bookmify_be_list_item  '.$cronClass.'" data-notification-id="'.$notificationID.'">
						<div class="bookmify_be_list_item_in">
							<div class="bookmify_be_list_item_header">
								<div class="header_in">
									<div class="info_holder">
										<div class="info_in">
											<span class="notification_status">
												<span class="bookmify_be_checkbox">
													<input class="status" type="checkbox" '.checked( $notificationStatus, 1, false ).'>
													<span class="checkmark">
														<img class="bookmify_be_svg" src="'.BOOKMIFY_ASSETS_URL.'/img/checked.svg" alt="" />
													</span>
												</span>
											</span>
											<span class="notification_subject">'.$notificationSubject.'</span>
										</div>
									</div>

									<div class="buttons_holder">';
			if($notificationCron == 1){
				$html .=				'<div class="btn_item btn_scheduled">
											<a class="bookmify_be_scheduled protip" data-pt-target="true" data-pt-title="'.esc_attr__('Scheduled Notification', 'bookmify').'" data-pt-gravity="left -4 0">
												<img class="bookmify_be_svg" src="'.BOOKMIFY_ASSETS_URL.'img/countdown.svg" alt="" />
											</a>
										</div>';
			}
			$html .=					'<div class="btn_item btn_edit">
											<a href="#" class="bookmify_be_edit">
												<img class="bookmify_be_svg edit" src="'.BOOKMIFY_ASSETS_URL.'img/edit.svg" alt="" />
												<img class="bookmify_be_svg checked" src="'.BOOKMIFY_ASSETS_URL.'img/checked.svg" alt="" />
											</a>
										</div>
									</div>
								</div>
							</div>

							<div class="bookmify_be_list_item_content">
								<div class="bookmify_be_list_item_content_in">
								
									<div class="closer_button"><a href="#"><img class="bookmify_be_svg" src="'.BOOKMIFY_ASSETS_URL.'/img/cancel.svg" alt="" /></a></div>

									<div class="bookmify_be_form_wrap">
										
										<div class="input_wrap_row ';if($notificationCron == 1){$html .= 'double_input';}$html.='">
											<div class="input_wrap subject_holder">
												<label><span class="title">'.esc_html__('Subject','bookmify').'<span>*</span></span></label>
												<input type="text" class="notification_subject"  value="'.$notifications->subject.'" />
											</div>';if($notificationType == 'employee_reminder_prev_day'){
							$html .= 		'<div class="input_wrap scheduled_holder one_day">
												<label><span class="title">'.esc_html__('Scheduled For','bookmify').'<span>*</span></span></label>
												<input type="text" name="scheduled_for" placeholder="'.esc_attr__('Select Time','bookmify').'" value="'.$notCheckTimeInTimeFormat.'" readonly />
												<input class="required_field" type="hidden" name="scheduled_for_hidden" value="'.$notCheckTimeInHi.'">
												<span class="bot_btn"><span></span><img class="bookmify_be_svg" src="'.BOOKMIFY_ASSETS_URL.'img/down.svg" alt="" /></span></div>';
							}else if($notificationType == 'employee_reminder_x_before'){
							$html .= 		'<div class="input_wrap scheduled_holder x_time">
												<label><span class="title">'.esc_html__('Before...','bookmify').'<span>*</span></span></label>
												<input type="text" name="scheduled_for" placeholder="'.esc_attr__('Select Time','bookmify').'" value="'.$notTimeIntervalInTimeFormat.'" readonly />
												<input class="required_field" type="hidden" name="scheduled_for_hidden" value="'.$notificationTimeInterval.'">
												<span class="bot_btn"><span></span><img class="bookmify_be_svg" src="'.BOOKMIFY_ASSETS_URL.'img/down.svg" alt="" /></span></div>';
							}
							$html .= 		'<input type="hidden" value="'.$notificationType.'" class="not_hid_type" />
										</div>
										
										<div class="input_wrap_row">
											<div class="input_wrap content_holder">
												<label><span class="title">'.esc_html__('Message','bookmify').'</span></label>
												'.$wpEditor.'
											</div>
										</div>
										
										<div class="input_wrap_row notifications_buttons_holder">
											<div class="bookmify_be_main_save_button">
												<a href="#">
													<span class="text">'.esc_html__('Save', 'bookmify').'</span>
													<span class="save_process">
														<span class="ball"></span>
														<span class="ball"></span>
														<span class="ball"></span>
													</span>
												</a>
											</div>
										</div>
									</div>
								
								</div>
								
							</div>
						</div>
					</div>';
			
		}
		
		$html .= '</div>';
		
		
		return $html;
	}
}