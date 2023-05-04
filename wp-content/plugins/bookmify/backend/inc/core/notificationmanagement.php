<?php
namespace Bookmify;
use Bookmify;

use Bookmify\Helper;
use Bookmify\HelperTime;
use Bookmify\HelperLocations;
use Bookmify\HelperAppointments;
use Bookmify\PHPMailerCustom;
use Bookmify\SMSTwilio;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class NotificationManagement{
	
	
	/**
	 * @since 1.0.0
	 * @access public
	*/
	public static function replacePlaceholders($text, $codes)
    {
        $placeholders = array_map(
            function ($placeholder) {
                return "{{{$placeholder}}}";
            },
            array_keys($codes)
        );

        return str_replace($placeholders, array_values($codes), $text);
    }
	
	
	
	/**
	 * @since 1.0.0
	 * @access public
	*/
	public static function demoPlaceholders()
    {
        $placeholders = [
			'appointment_date' 			=> 'March 12, 2020',
			'appointment_start_time' 	=> '10:30 am',
			'company_address' 			=> '9500 Euclid Avenue Cleveland, OH 44195-5108',
			'company_name' 				=> 'Frenify Health Clinic',
			'company_phone' 			=> '(216) 444–2200',
			'company_website' 			=> 'https://codecanyon.net/user/frenify/portfolio',
			'customer_full_name' 		=> 'Aron Beltran',
			'customer_first_name' 		=> 'Aron',
			'customer_last_name' 		=> 'Beltran',
			'customer_phone' 			=> '532-3243-3445',
			'customer_email' 			=> 'beltran@mail.com',
			'employee_full_name' 		=> 'Dr. Ramos Cejudo',
			'employee_email' 			=> 'cejudor@gmail.com',
			'employee_phone' 			=> '877-463-2010',
			'service_name' 				=> 'Pediatric Cardiology',
			'site_address' 				=> BOOKMIFY_SITE_URL,
			'new_username' 				=> 'Falimaya',
			'new_password' 				=> 'passwordtest2018!',
		];
		
        return $placeholders;
    }
	


	/**
	 * @since 1.0.0
	 * @access public
	*/
	public static function _sender($receiver, $subject, $content)
	{
		
		$checkSender		= Helper::checkForSender();
		if($checkSender){
			
			$mailService 	= get_option('bookmify_be_not_mail_service', 'php');
			$senderEmail 	= get_option('bookmify_be_not_sender_email', '');
			$senderName 	= get_option('bookmify_be_not_sender_name', '');
			$from 			= array($senderName,$senderEmail);
			$headers 		= "MIME-Version: 1.0\r\n" .
			"From: " .$senderName . " <". $senderEmail . ">\r\n" .
			"Content-Type: text/html; charset=\"" . get_option('blog_charset') . "\"\r\n";

			

			if(BOOKMIFY_MODE == 'demo' || BOOKMIFY_MODE == 'dev'){$mailService = 'smtp';}
			
			
			
			if($mailService == 'wp'){
				wp_mail( $receiver, $subject, $content, $headers );
			}else if($mailService == 'smtp'){
				$phpmailer 		= new PHPMailerCustom();
				$phpmailer->mailer($receiver, $from, $subject, $content, $headers);
			}else if($mailService == 'php'){
				mail( $receiver, $subject, $content, $headers );
			}else{
				mail( $receiver, $subject, $content, $headers );
			}
		}
	}
	
	
	/**
	 * @since 1.0.0
	 * @access public
	*/
	public static function sendEmailToCustomer($receiver, $subject, $content, $notificationID, $customerID, $appID, $platform = 'email')
	{
		if($notificationID != ''){
			if($platform == 'email'){
				$checkSender	= Helper::checkForSender();
				if($checkSender){
					self::sendEmailCustomer($receiver, $subject, $content, $notificationID, $customerID, $appID);
				}
			}else{
				$checkSenderSMS	= Helper::checkForSenderSMS($customerID,'customer');
				if($checkSenderSMS[0] == 1){
					SMSTwilio::sendSMS($content,$checkSenderSMS[1]);
				}
			}
		}
	}
	
	
	
	
	
	/**
	 * @since 1.0.0
	 * @access public
	*/
	public static function sendEmailToEmployee($receiver, $subject, $content, $notificationID, $employeeID, $appID = '', $platform = 'email')
	{
		if($notificationID != ''){
			if($platform == 'email'){
				$checkSender	= Helper::checkForSender();
				if($checkSender){
					self::sendEmailEmployee($receiver, $subject, $content, $notificationID, $employeeID, $appID);
				}
			}else{
				$checkSenderSMS	= Helper::checkForSenderSMS($employeeID,'employee');
				if($checkSenderSMS[0] == 1){
					SMSTwilio::sendSMS($content,$checkSenderSMS[1]);
				}
			}
		}
	}
	
	
	
	/**
	 * @since 1.0.0
	 * @access public
	*/
	public static function sendNewUserCredentials( $customer, $username, $password, $customerID )
    {

		$placeholders = [
			'customer_first_name' 	=> $customer->first_name,
			'customer_last_name'  	=> $customer->last_name,
			'customer_full_name'  	=> $customer->first_name. ' ' .$customer->last_name,
			'customer_email'      	=> $customer->email,
			'customer_phone'      	=> $customer->phone,
			'new_username'      	=> $username,
			'new_password'      	=> $password,
			'site_address'      	=> BOOKMIFY_SITE_URL,
			'company_address'		=> get_option( 'bookmify_be_company_info_address', '' ),
			'company_name'			=> get_option( 'bookmify_be_company_info_name', '' ),
			'company_phone'			=> get_option( 'bookmify_be_company_info_phone', '' ),
			'company_website'		=> get_option( 'bookmify_be_company_info_website', '' ),
		];
		
		// via email
		$notification_subject 		= self::getSubject('customer_login_message', 'email', 1);
		$notification_message 		= self::getMessage('customer_login_message', 'email', 1);
		$notification_id	 		= self::getID('customer_login_message', 'email', 1);

		$notification_subject 		= self::replacePlaceholders($notification_subject, $placeholders);
		$notification_message 		= self::replacePlaceholders($notification_message, $placeholders);

		self::sendEmailToCustomer($customer->email, $notification_subject, $notification_message, $notification_id, $customerID, 'email');
		
		
		// via sms
		$notification_subject 		= self::getSubject('customer_login_message', 'sms', 1);
		$notification_message 		= self::getMessage('customer_login_message', 'sms', 1);
		$notification_id	 		= self::getID('customer_login_message', 'sms', 1);

		$notification_subject 		= self::replacePlaceholders($notification_subject, $placeholders);
		$notification_message 		= self::replacePlaceholders($notification_message, $placeholders);
		
		self::sendEmailToCustomer($customer->email, $notification_subject, $notification_message, $notification_id, $customerID, 'sms');
		
    }
	/**
	 * @since 1.0.0
	 * @access public
	*/
	public static function sendNewEmployeeCredentials( $employee, $username, $password, $employeeID )
    {

		$placeholders = [
			'employee_first_name' 	=> $employee->first_name,
			'employee_last_name'  	=> $employee->last_name,
			'employee_full_name'  	=> $employee->first_name. ' ' .$employee->last_name,
			'employee_email'      	=> $employee->email,
			'employee_phone'      	=> $employee->phone,
			'new_username'      	=> $username,
			'new_password'      	=> $password,
			'site_address'      	=> BOOKMIFY_SITE_URL,
			'company_address'		=> get_option( 'bookmify_be_company_info_address', '' ),
			'company_name'			=> get_option( 'bookmify_be_company_info_name', '' ),
			'company_phone'			=> get_option( 'bookmify_be_company_info_phone', '' ),
			'company_website'		=> get_option( 'bookmify_be_company_info_website', '' ),
		];
		
		// for email
		$notification_subject 		= self::getSubject('employee_login_message', 'email', 1);
		$notification_message 		= self::getMessage('employee_login_message', 'email', 1);
		$notification_id	 		= self::getID('employee_login_message', 'email', 1);

		$notification_subject 		= self::replacePlaceholders($notification_subject, $placeholders);
		$notification_message 		= self::replacePlaceholders($notification_message, $placeholders);

		self::sendEmailToEmployee($employee->email, $notification_subject, $notification_message, $notification_id, $employeeID, 'email');
		
		
		// for sms
		$notification_subject 		= self::getSubject('employee_login_message', 'sms', 1);
		$notification_message 		= self::getMessage('employee_login_message', 'sms', 1);
		$notification_id	 		= self::getID('employee_login_message', 'sms', 1);

		$notification_subject 		= self::replacePlaceholders($notification_subject, $placeholders);
		$notification_message 		= self::replacePlaceholders($notification_message, $placeholders);

		self::sendEmailToEmployee($employee->email, $notification_subject, $notification_message, $notification_id, $employeeID, 'sms');
		
    }
	
	public static function sendInfoToEmployeeAboutAppointment($object, $rescheduled = '', $scheduled = ''){
		
		$customerCount	 				=  $object->customer_count;
		if($customerCount == 1){
			$customerName				= $object->customer_name;
			$customerEmail				= $object->customer_email;
			$customerPhone				= $object->customer_phone;
		}else{
			$customerName				= $customerCount 			. ' ' . esc_html__('Customers', 'bookmify');
			$customerEmail				= $object->customer_email 	. ' ' . esc_html__('Emails', 'bookmify');
			$customerPhone				= $object->customer_phone 	. ' ' . esc_html__('Phones', 'bookmify');
		}
		
		// variables
		$appointmentID					= '';
		$cf 							= '';
		$employeeID						= $object->userID;
		$employeeEmail					= $object->employee_email;
		$appointmentDate				= $object->appointment_date;
		$appointmentTime				= $object->appointment_time;
		$serviceTitle					= $object->service_name;
		$appointmentStatus				= $object->status;
		if(isset($object->appID)){
			$appointmentID				= $object->appID;
		}
		if(isset($object->cf)){
			$cf 						= $object->cf;
		}
		
		if($appointmentID != ''){
			$totalPrice = HelperAppointments::getTotalPriceForAppointmentByID($appointmentID); // 0
			$extraText 	= HelperAppointments::getExtraForAppointmentByID($appointmentID); // ''
		}else{
			$totalPrice	= 0;
			$extraText	= '';
		}
		$number_of_people				= HelperAppointments::getNumberOfPeople($appointmentID);
		$locationName 					= Helper::getLocationDataByEmployeeID($employeeID);
		$locationAddress				= Helper::getLocationDataByEmployeeID($employeeID, 'address');
		$placeholders = [
			'service_name' 				=> $serviceTitle,
			'appointment_date'  		=> date(get_option('bookmify_be_date_format', 'd F, Y'), strtotime($appointmentDate)),
			'appointment_start_time'  	=> date(get_option('bookmify_be_time_format', 'h:i a'), strtotime($appointmentTime)),
			'customer_full_name'   		=> $customerName,
			'customer_phone'      		=> $customerPhone,
			'customer_email'      		=> $customerEmail,
			'company_address'			=> get_option( 'bookmify_be_company_info_address', '' ),
			'company_name'				=> get_option( 'bookmify_be_company_info_name', '' ),
			'company_phone'				=> get_option( 'bookmify_be_company_info_phone', '' ),
			'company_website'			=> get_option( 'bookmify_be_company_info_website', '' ),
			'custom_fields'				=> $cf,
			'total_price'				=> $totalPrice,
			'extras'					=> $extraText,
			'number_of_people'			=> $number_of_people,
			'location_name'				=> $locationName,
			'location_address'			=> $locationAddress,
		];
		
		switch($appointmentStatus){
			case 'approved':
				// for email
				$notification_subject 	= self::getSubject('employee_approved_message', 'email', 1);
				$notification_message 	= self::getMessage('employee_approved_message', 'email', 1);
				$notification_id	 	= self::getID('employee_approved_message', 'email', 1);
				// for sms
				$notification_subject2 	= self::getSubject('employee_approved_message', 'sms', 1);
				$notification_message2 	= self::getMessage('employee_approved_message', 'sms', 1);
				$notification_id2	 	= self::getID('employee_approved_message', 'sms', 1);
				break;
			case 'pending':
				// for email
				$notification_subject 	= self::getSubject('employee_pending_message', 'email', 1);
				$notification_message 	= self::getMessage('employee_pending_message', 'email', 1);
				$notification_id	 	= self::getID('employee_pending_message', 'email', 1);
				// for sms
				$notification_subject2 	= self::getSubject('employee_pending_message', 'sms', 1);
				$notification_message2 	= self::getMessage('employee_pending_message', 'sms', 1);
				$notification_id2	 	= self::getID('employee_pending_message', 'sms', 1);
				break;
			case 'canceled':
				// for email
				$notification_subject 	= self::getSubject('employee_canceled_message', 'email', 1);
				$notification_message 	= self::getMessage('employee_canceled_message', 'email', 1);
				$notification_id	 	= self::getID('employee_canceled_message', 'email', 1);
				// for sms
				$notification_subject2 	= self::getSubject('employee_canceled_message', 'sms', 1);
				$notification_message2 	= self::getMessage('employee_canceled_message', 'sms', 1);
				$notification_id2	 	= self::getID('employee_canceled_message', 'sms', 1);
				break;
			case 'rejected':
				// for email
				$notification_subject 	= self::getSubject('employee_rejected_message', 'email', 1);
				$notification_message 	= self::getMessage('employee_rejected_message', 'email', 1);
				$notification_id	 	= self::getID('employee_rejected_message', 'email', 1);
				// for sms
				$notification_subject2 	= self::getSubject('employee_rejected_message', 'sms', 1);
				$notification_message2 	= self::getMessage('employee_rejected_message', 'sms', 1);
				$notification_id2	 	= self::getID('employee_rejected_message', 'sms', 1);
				break;
		}
		
		if($rescheduled == 'rescheduled'){
			// for email
			$notification_subject 	= self::getSubject('employee_rescheduled_message', 'email', 1);
			$notification_message 	= self::getMessage('employee_rescheduled_message', 'email', 1);
			$notification_id	 	= self::getID('employee_rescheduled_message', 'email', 1);
			// for sms
			$notification_subject2 	= self::getSubject('employee_rescheduled_message', 'sms', 1);
			$notification_message2 	= self::getMessage('employee_rescheduled_message', 'sms', 1);
			$notification_id2	 	= self::getID('employee_rescheduled_message', 'sms', 1);
		}

		if($scheduled == 'one_day'){
			// for email
			$notification_subject 	= self::getSubject('employee_reminder_prev_day', 'email', 1);
			$notification_message 	= self::getMessage('employee_reminder_prev_day', 'email', 1);
			$notification_id	 	= self::getID('employee_reminder_prev_day', 'email', 1);
			// for sms
			$notification_subject2 	= self::getSubject('employee_reminder_prev_day', 'sms', 1);
			$notification_message2 	= self::getMessage('employee_reminder_prev_day', 'sms', 1);
			$notification_id2	 	= self::getID('employee_reminder_prev_day', 'sms', 1);
		}
		
		if($scheduled == 'one_hour'){
			// for email
			$notification_subject 	= self::getSubject('employee_reminder_x_before', 'email', 1);
			$notification_message 	= self::getMessage('employee_reminder_x_before', 'email', 1);
			$notification_id	 	= self::getID('employee_reminder_x_before', 'email', 1);
			// for sms
			$notification_subject2 	= self::getSubject('employee_reminder_x_before', 'sms', 1);
			$notification_message2 	= self::getMessage('employee_reminder_x_before', 'sms', 1);
			$notification_id2	 	= self::getID('employee_reminder_x_before', 'sms', 1);
		}
		
		// for email
		$notification_subject 		= self::replacePlaceholders($notification_subject, $placeholders);
		$notification_message 		= self::replacePlaceholders($notification_message, $placeholders);
		self::sendEmailToEmployee($employeeEmail,$notification_subject,$notification_message,$notification_id,$employeeID,$appointmentID, 'email');
		
		// for sms
		$notification_subject2 		= self::replacePlaceholders($notification_subject2, $placeholders);
		$notification_message2 		= self::replacePlaceholders($notification_message2, $placeholders);
		self::sendEmailToEmployee($employeeEmail,$notification_subject2,$notification_message2,$notification_id2,$employeeID,$appointmentID, 'sms');
	}
	
	public static function sendInfoToCustomerAboutAppointment($object, $rescheduled = '', $scheduled = ''){
		global $wpdb;
		$cf 					= '';
		$appID 					= '';
		$customerEmail			= $object->customer_email;
		if(isset($object->cf)){
			$cf 				= $object->cf;
		}
		// since bookmify v1.3.0
		$appDate				= date(get_option('bookmify_be_date_format', 'd F, Y'), strtotime($object->appointment_date));
		$appTime				= date(get_option('bookmify_be_time_format', 'h:i a'), strtotime($object->appointment_time));
		$tz 					= '';
		if(isset($object->tz)){
			if($object->tz != 0){
				$tz				= $object->tz;
				$tzDate			= date(get_option('bookmify_be_date_format', 'd F, Y'), strtotime($object->tzDate));
				$tzTime			= date(get_option('bookmify_be_time_format', 'h:i a'), strtotime($object->tzTime));
				$appDate		= $tzDate . ' ( '.esc_html__('Local Date:', 'bookmify').' '.$appDate.' )';
				$appTime		= $tzTime . ' ( '.esc_html__('Local Time:', 'bookmify').' '.$appTime.' )';
			}
		}
		if(isset($object->appID)){
			$appID 				= $object->appID;
			$query 				= "SELECT employee_id FROM {$wpdb->prefix}bmify_appointments WHERE id=".$appID;
			$results	 		= $wpdb->get_results( $query, OBJECT  );
			$employeeID			= $results[0]->employee_id;
			
			$employeeFullName 	= Helper::bookmifyGetEmployeeCol($employeeID);
			$locationName 		= Helper::getLocationDataByEmployeeID($employeeID);
			$locationAddress	= Helper::getLocationDataByEmployeeID($employeeID, 'address');
			$totalPrice		 	= HelperAppointments::getTotalPriceForAppointmentByID($appID); // 0
			$extraText 			= HelperAppointments::getExtraForAppointmentByID($appID); // ''
		}else{
			$employeeFullName 	= '';
			$locationAddress 	= '';
			$locationName		= '';
			$totalPrice			= 0;
			$extraText			= '';
		}
		$number_of_people		= HelperAppointments::getNumberOfPeople($appID);
		$placeholders = [
			'service_name' 				=> $object->service_name,
			'customer_full_name'		=> $object->customer_name,
			'appointment_date'  		=> $appDate,
			'appointment_start_time'  	=> $appTime,
			'company_address'			=> get_option( 'bookmify_be_company_info_address', '' ),
			'company_name'				=> get_option( 'bookmify_be_company_info_name', '' ),
			'company_phone'				=> get_option( 'bookmify_be_company_info_phone', '' ),
			'company_website'			=> get_option( 'bookmify_be_company_info_website', '' ),
			'custom_fields'				=> $cf,
			'location_name'				=> $locationName,
			'location_address'			=> $locationAddress,
			'employee_full_name'		=> $employeeFullName,
			'total_price'				=> $totalPrice,
			'extras'					=> $extraText,
			'number_of_people'			=> $number_of_people,
		];
		
		switch($object->status){
			case 'approved':
				// for email
				$notification_subject 	= self::getSubject('customer_approved_message', 'email', 1);
				$notification_message 	= self::getMessage('customer_approved_message', 'email', 1);
				$notification_id	 	= self::getID('customer_approved_message', 'email', 1);
				// for sms
				$notification_subject2 	= self::getSubject('customer_approved_message', 'sms', 1);
				$notification_message2 	= self::getMessage('customer_approved_message', 'sms', 1);
				$notification_id2	 	= self::getID('customer_approved_message', 'sms', 1);
				break;
			case 'pending':
				// for email
				$notification_subject 	= self::getSubject('customer_pending_message', 'email', 1);
				$notification_message 	= self::getMessage('customer_pending_message', 'email', 1);
				$notification_id	 	= self::getID('customer_pending_message', 'email', 1);
				// for sms
				$notification_subject2 	= self::getSubject('customer_pending_message', 'sms', 1);
				$notification_message2 	= self::getMessage('customer_pending_message', 'sms', 1);
				$notification_id2	 	= self::getID('customer_pending_message', 'sms', 1);
				break;
			case 'canceled':
				// for email
				$notification_subject 	= self::getSubject('customer_canceled_message', 'email', 1);
				$notification_message 	= self::getMessage('customer_canceled_message', 'email', 1);
				$notification_id	 	= self::getID('customer_canceled_message', 'email', 1);
				// for sms
				$notification_subject2 	= self::getSubject('customer_canceled_message', 'sms', 1);
				$notification_message2 	= self::getMessage('customer_canceled_message', 'sms', 1);
				$notification_id2	 	= self::getID('customer_canceled_message', 'sms', 1);
				break;
			case 'rejected':
				// for email
				$notification_subject 	= self::getSubject('customer_rejected_message', 'email', 1);
				$notification_message 	= self::getMessage('customer_rejected_message', 'email', 1);
				$notification_id	 	= self::getID('customer_rejected_message', 'email', 1);
				// for sms
				$notification_subject2 	= self::getSubject('customer_rejected_message', 'sms', 1);
				$notification_message2 	= self::getMessage('customer_rejected_message', 'sms', 1);
				$notification_id2	 	= self::getID('customer_rejected_message', 'sms', 1);
				break;
		}
		
		if($rescheduled == 'rescheduled'){
			// for email
			$notification_subject 		= self::getSubject('customer_rescheduled_message', 'email', 1);
			$notification_message 		= self::getMessage('customer_rescheduled_message', 'email', 1);
			$notification_id	 		= self::getID('customer_rescheduled_message', 'email', 1);
			// for sms
			$notification_subject2 		= self::getSubject('customer_rescheduled_message', 'sms', 1);
			$notification_message2 		= self::getMessage('customer_rescheduled_message', 'sms', 1);
			$notification_id2	 		= self::getID('customer_rescheduled_message', 'sms', 1);
		}
		if($scheduled == 'one_day'){
			// for email
			$notification_subject 		= self::getSubject('customer_reminder_prev_day', 'email', 1);
			$notification_message 		= self::getMessage('customer_reminder_prev_day', 'email', 1);
			$notification_id	 		= self::getID('customer_reminder_prev_day', 'email', 1);
			// for sms
			$notification_subject2 		= self::getSubject('customer_reminder_prev_day', 'sms', 1);
			$notification_message2 		= self::getMessage('customer_reminder_prev_day', 'sms', 1);
			$notification_id2	 		= self::getID('customer_reminder_prev_day', 'sms', 1);
		}
		
		if($scheduled == 'one_hour'){
			// for email
			$notification_subject 	= self::getSubject('customer_reminder_x_before', 'email', 1);
			$notification_message 	= self::getMessage('customer_reminder_x_before', 'email', 1);
			$notification_id	 	= self::getID('customer_reminder_x_before', 'email', 1);
			// for sms
			$notification_subject2 	= self::getSubject('customer_reminder_x_before', 'sms', 1);
			$notification_message2 	= self::getMessage('customer_reminder_x_before', 'sms', 1);
			$notification_id2	 	= self::getID('customer_reminder_x_before', 'sms', 1);
		}
		
		$notification_subject 			= self::replacePlaceholders($notification_subject, $placeholders);
		$notification_message 			= self::replacePlaceholders($notification_message, $placeholders);

		self::sendEmailToCustomer($customerEmail, $notification_subject, $notification_message, $notification_id, $object->userID, $appID, 'email');
		
		
		$notification_subject2 			= self::replacePlaceholders($notification_subject2, $placeholders);
		$notification_message2 			= self::replacePlaceholders($notification_message2, $placeholders);
		self::sendEmailToCustomer($customerEmail, $notification_subject2, $notification_message2, $notification_id2, $object->userID, $appID, 'sms');

		
	}
	
	
	
	
	
	/**
	 * @since 1.0.0
	 * @access public
	*/
	public static function getSubject($type, $platform, $status){
		global $wpdb; $html = '';
		
		$type 			= esc_sql($type);
		$platform 		= esc_sql($platform);
		$status 		= esc_sql($status);
		$query 			= "SELECT subject FROM {$wpdb->prefix}bmify_notifications WHERE type='".$type."' AND platform='".$platform."' AND status='".$status."'";
		$results 		= $wpdb->get_results( $query, OBJECT );
		
		foreach($results as $result){
			$html .= $result->subject;
		}
		
		return $html;
	}
	
	/**
	 * @since 1.0.0
	 * @access public
	*/
	public static function getMessage($type, $platform, $status){
		global $wpdb; $html = '';
		
		$type 			= esc_sql($type);
		$platform 		= esc_sql($platform);
		$status 		= esc_sql($status);
		$query 			= "SELECT message FROM {$wpdb->prefix}bmify_notifications WHERE type='".$type."' AND platform='".$platform."' AND status='".$status."'";
		$results 		= $wpdb->get_results( $query, OBJECT );
		
		foreach($results as $result){
			$html .= $result->message;
		}
		
		return $html;
	}
	
	/**
	 * @since 1.0.0
	 * @access public
	*/
	public static function getID($type, $platform, $status){
		global $wpdb; $html = '';
		
		$type 			= esc_sql($type);
		$platform 		= esc_sql($platform);
		$status 		= esc_sql($status);
		$query 			= "SELECT id FROM {$wpdb->prefix}bmify_notifications WHERE type='".$type."' AND platform='".$platform."' AND status='".$status."'";
		$results 		= $wpdb->get_results( $query, OBJECT );
		
		foreach($results as $result){
			$html .= $result->id;
		}
		
		return $html;
	}
	
	/**
	 * @since 1.2.0
	 * @access public
	*/
	public static function sendEmailCustomer($receiver, $subject, $content, $notificationID, $customerID, $appID){
		global $wpdb;
		$mailService 	= get_option('bookmify_be_not_mail_service', 'php');
		$senderEmail 	= get_option('bookmify_be_not_sender_email', '');
		$senderName 	= get_option('bookmify_be_not_sender_name', '');
		$from 			= array($senderName,$senderEmail);
		$headers 		= "MIME-Version: 1.0\r\n" .
		"From: " .$senderName . " <". $senderEmail . ">\r\n" .
		"Content-Type: text/html; charset=\"" . get_option('blog_charset') . "\"\r\n";
	
		
		$customerID		= (int)$customerID;
		
		$sendDate 		= HelperTime::getCurrentDateTime();
		if($appID == ''){
			$wpdb->query($wpdb->prepare( "INSERT INTO {$wpdb->prefix}bmify_notifications_sent ( notification_id, customer_id, sent_date ) VALUES ( %d, %d, %s )", $notificationID, $customerID, $sendDate));
		}else{
			$wpdb->query($wpdb->prepare( "INSERT INTO {$wpdb->prefix}bmify_notifications_sent ( notification_id, appointment_id, customer_id, sent_date ) VALUES ( %d, %d, %d, %s )", $notificationID, $appID, $customerID, $sendDate));
		}
		
		if(BOOKMIFY_MODE == 'demo' || BOOKMIFY_MODE == 'dev'){$mailService = 'smtp';}
		

		if($mailService == 'wp'){
			wp_mail( $receiver, $subject, $content, $headers );
		}else if($mailService == 'smtp'){
			$phpmailer 		= new PHPMailerCustom();
			$phpmailer->mailer($receiver, $from, $subject, $content, $headers);
		}else if($mailService == 'php'){
			mail( $receiver, $subject, $content, $headers );
		}else{
			mail( $receiver, $subject, $content, $headers );
		}
	}

	/**
	 * @since 1.2.0
	 * @access public
	*/
	public static function sendEmailEmployee($receiver,$subject,$content,$notificationID,$employeeID,$appID){
		global $wpdb;
		
		$mailService 	= get_option('bookmify_be_not_mail_service', 'php');
		$senderEmail 	= get_option('bookmify_be_not_sender_email', '');
		$senderName 	= get_option('bookmify_be_not_sender_name', '');
		$from 			= array($senderName,$senderEmail);
		$headers 		= "MIME-Version: 1.0\r\n" .
		"From: " .$senderName . " <". $senderEmail . ">\r\n" .
		"Content-Type: text/html; charset=\"" . get_option('blog_charset') . "\"\r\n";




		$sendDate 		= HelperTime::getCurrentDateTime();
		if($appID == ''){
			$wpdb->query($wpdb->prepare( "INSERT INTO {$wpdb->prefix}bmify_notifications_sent ( notification_id, employee_id, sent_date ) VALUES ( %d, %d, %s )", $notificationID, $employeeID, $sendDate));
		}else{
			$wpdb->query($wpdb->prepare( "INSERT INTO {$wpdb->prefix}bmify_notifications_sent ( notification_id, appointment_id, employee_id, sent_date ) VALUES ( %d, %d, %d, %s )", $notificationID, $appID, $employeeID, $sendDate));
		}


		if(BOOKMIFY_MODE == 'demo' || BOOKMIFY_MODE == 'dev'){$mailService = 'smtp';}


		
		if($mailService == 'wp'){
			wp_mail( $receiver, $subject, $content, $headers );
		}else if($mailService == 'smtp'){
			$phpmailer 		= new PHPMailerCustom();
			$phpmailer->mailer($receiver, $from, $subject, $content, $headers);
		}else if($mailService == 'php'){
			mail( $receiver, $subject, $content, $headers );
		}else{
			mail( $receiver, $subject, $content, $headers );
		}
	}


}