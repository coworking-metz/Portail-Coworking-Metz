<?php
namespace Bookmify;

use Bookmify;

use Bookmify\NotificationManagement;
use Bookmify\HelperTime;
use Bookmify\Helper;

// Exit if accessed directly.
//if ( ! defined( 'ABSPATH' ) ) {exit; }


require_once __DIR__ . '/../../../../../wp-load.php';
require_once ABSPATH . WPINC . '/formatting.php';
require_once ABSPATH . WPINC . '/general-template.php';
require_once ABSPATH . WPINC . '/pluggable.php';
require_once ABSPATH . WPINC . '/link-template.php';


/**
 * Class Cron Notification
 */
class CronNotification
{
	
	/**
	 * @since 1.0.0
	 * @access public
	 */
	public function __construct(){
	
//		NotificationManagement::sendEmailToEmployee('gho5t7@gmail.com', 'Frenify Cron Test Subject', 'Hi. Do you like a bookmify?');
		
		//wp_mail('gho5t7@gmail.com', 'Frenify Cron Test Subject', 'Hi. Do you like a bookmify?');
		$this->bookmifySendReminder('customer', 'one_day');
		$this->bookmifySendReminder('employee', 'one_day');
		$this->bookmifySendReminder('customer', 'one_hour');
		$this->bookmifySendReminder('employee', 'one_hour');
	}
	
	
	public function bookmifySendReminder($receiver = '', $cron = ''){
		global $wpdb;
		$notificationID 	= '';
		$notificationType	= '';
		$notificationStatus	= 0;
		
		// get notification type by receiver and cron
		if($receiver == 'customer'){
			if($cron == 'one_day'){
				$notificationType = 'customer_reminder_prev_day';
			}else if($cron == 'one_hour'){
				$notificationType = 'customer_reminder_x_before';
			}
		}else if($receiver == 'employee'){
			if($cron == 'one_day'){
				$notificationType = 'employee_reminder_prev_day';
			}else if($cron == 'one_hour'){
				$notificationType = 'employee_reminder_x_before';
			}
		}
		
		// get need notification id
		$query 		 		= "SELECT id,time_interval,check_time,status FROM {$wpdb->prefix}bmify_notifications WHERE type='".$notificationType."'";
		$results 			= $wpdb->get_results( $query );
		if(!empty($results)){
			$notificationID 			= $results[0]->id;
			$notificationStatus			= $results[0]->status;
			$notificationTimeInterval 	= $results[0]->time_interval;
			$notificationCheckTime	 	= $results[0]->check_time;
		}
		
		// check if notification id has
		if($notificationID != '' && $notificationStatus == 1){
			
			if($receiver == 'customer'){
				// get customers appointments as object
				$apps 		= $this->getCustomerAppointment($notificationID,$cron,$notificationTimeInterval,$notificationCheckTime);
				if(!empty($apps)){
					foreach($apps as $app){
						$infoObject							= new \stdClass();
						$infoObject->sendTo					= 'customer';
						$infoObject->userID					= $app->cusID;
						$infoObject->service_name 			= $app->serviceName;
						$infoObject->service_duration 		= $app->serviceDuration;
						$infoObject->appointment_date 		= date_i18n(get_option('bookmify_be_date_format', 'd F, Y'), strtotime($app->startDate));
						$infoObject->appointment_time 		= date_i18n(get_option('bookmify_be_time_format', 'h:i a'), strtotime($app->startDate));
						$infoObject->customer_name	 		= $app->cusFirstName. ' ' . $app->cusLastName;
						$infoObject->customer_email	 		= $app->cusEmail;
						$infoObject->customer_phone	 		= $app->cusPhone;
						$infoObject->appID	 				= $app->appID;
						$infoObject->status	 				= $app->customerAppStatus;
						$infoObject->total_price			= $app->totalPrice;
						$infoObject->customer_first_name	= $app->cusFirstName;
						$infoObject->customer_last_name	 	= $app->cusLastName;
						$this->pretraintmentToSendNotification($infoObject,$cron);
					}
				}
					
			}else if($receiver == 'employee'){
				// get customers appointments as object
				$apps = $this->getEmployeeAppointment($notificationID,$cron,$notificationTimeInterval,$notificationCheckTime);
				if(!empty($apps)){
					foreach($apps as $app){
						$customerIDs 					= explode(',', $app->customerIDs);
						$customerName					= '';
						$customerEmail					= '';
						$customerPhone					= '';
						$customerCount					= count($customerIDs);
						if($customerCount == 1){
							$customerName				= Helper::bookmifyGetCustomerCol($customerIDs[0]);
							$customerEmail				= Helper::bookmifyGetCustomerCol($customerIDs[0], 'email');
							$customerPhone				= Helper::bookmifyGetCustomerCol($customerIDs[0], 'phone');
						}

						$infoObject						= new \stdClass();
						$infoObject->sendTo				= 'employee';
						$infoObject->userID				= $app->employeeID;
						$infoObject->service_name 		= $app->serviceName;
						$infoObject->service_duration 	= $app->serviceDuration;
						$infoObject->appointment_date 	= date_i18n(get_option('bookmify_be_date_format', 'd F, Y'), strtotime($app->startDate));
						$infoObject->appointment_time 	= date_i18n(get_option('bookmify_be_time_format', 'h:i a'), strtotime($app->startDate));
						$infoObject->customer_name	 	= $customerName;
						$infoObject->customer_email	 	= $customerEmail;
						$infoObject->customer_phone	 	= $customerPhone;
						$infoObject->customer_count	 	= $customerCount;
						$infoObject->appID	 			= $app->appID;
						$infoObject->status	 			= $app->appStatus;
						$infoObject->employee_email	 	= $app->employeeEmail;
						$this->pretraintmentToSendNotification($infoObject,$cron);
					}
				}
			}
				
		}
		
	}
	
	private function pretraintmentToSendNotification($object,$cron){
		$receiver 		= $object->sendTo;
		$checkSender 	= Helper::checkForSender();
		
		if($checkSender){
			if($receiver == 'employee'){
				NotificationManagement::sendInfoToEmployeeAboutAppointment( $object, '', $cron );
			}else if($receiver == 'customer'){
				NotificationManagement::sendInfoToCustomerAboutAppointment( $object, '', $cron );
			}
		}
    }
	
	
	/**
     * Get Customer Appointments.
	 * @since 1.0.0
     */
    public function getCustomerAppointment($nID,$cron,$nTimeInterval,$nCheckTime){
		global $wpdb;
		$doCheck 			= '';
		$results 			= array();
		if($nTimeInterval == 0){
			$currentTime 	= HelperTime::getCurrentDateTimeWithoutFormat();
			$currentTime 	= $currentTime->format('H:i:s');
			$oneHourLater 	= HelperTime::getCurrentDateTimePlusWithoutFormat(3600);
			$oneHourLater 	= $oneHourLater->format('H:i:s');
			if($nCheckTime >= $currentTime && $nCheckTime <= $oneHourLater){
				$doCheck 	= 'do';
			}
		}else{
			$doCheck 		= 'do';
		}
		
		if($cron == 'one_hour'){
			$startDate  	= HelperTime::getCurrentDateTime();
			$endDate  		= HelperTime::getCurrentDateTimePlus(3600*$nTimeInterval); // 3600 seconds = 1 hour
		}else{
			$startDate  	= date('Y-m-d', strtotime('+1 days')).' 00:00:00';
			$endDate  		= date('Y-m-d', strtotime('+1 days')).' 23:59:59';
		}
		
		if($doCheck == 'do'){
			$query 			= "SELECT
									a.id appID,
									a.start_date startDate,
									c.first_name cusFirstName,
									c.id cusID,
									c.last_name cusLastName,
									c.email cusEmail,
									c.phone cusPhone,
									s.title serviceName,
									s.duration serviceDuration,
									ca.status customerAppStatus,
									p.total_price totalPrice

								FROM 	   	   {$wpdb->prefix}bmify_customer_appointments ca 
									LEFT JOIN  {$wpdb->prefix}bmify_appointments a  			ON a.id = ca.appointment_id
									LEFT JOIN  {$wpdb->prefix}bmify_customers c					ON c.id = ca.customer_id
									LEFT JOIN  {$wpdb->prefix}bmify_payments p					ON p.id = ca.payment_id
									INNER JOIN {$wpdb->prefix}bmify_services s					ON s.id = a.service_id

								WHERE ca.status IN ('approved','pending') 
									AND (a.start_date BETWEEN '".$startDate."' AND '".$endDate."') 
									AND NOT EXISTS (
										SELECT 
												ns.notification_id,
												ns.appointment_id,
												ns.customer_id

											FROM {$wpdb->prefix}bmify_notifications_sent ns
											WHERE ns.notification_id = ".$nID."
												AND ns.appointment_id = a.id
												AND ns.customer_id = c.id
									)

								ORDER BY a.start_date";


			$results = $wpdb->get_results( $query, OBJECT );
		}
		
			
		
		return $results;
		
    }
	
	/**
     * Get Employee Appointments.
	 * @since 1.0.0
     */
    public function getEmployeeAppointment($nID,$cron,$nTimeInterval,$nCheckTime){
		global $wpdb;
		
		$doCheck 			= '';
		$results 			= array();
		if($nTimeInterval == 0){
			$currentTime 	= HelperTime::getCurrentDateTimeWithoutFormat();
			$currentTime 	= $currentTime->format('H:i:s');
			$oneHourLater 	= HelperTime::getCurrentDateTimePlusWithoutFormat(3600);
			$oneHourLater 	= $oneHourLater->format('H:i:s');
			if(($nCheckTime >= $currentTime) && ($nCheckTime <= $oneHourLater)){
				$doCheck 	= 'do';
			}
		}else{
			$doCheck 		= 'do';
		}
		
		if($cron == 'one_hour'){
			$startDate  	= HelperTime::getCurrentDateTime();
			$endDate  		= HelperTime::getCurrentDateTimePlus(3600*$nTimeInterval); // 3600 seconds = 1 hour
		}else{
			$startDate  	= date('Y-m-d', strtotime('+1 days')).' 00:00:00';
			$endDate  		= date('Y-m-d', strtotime('+1 days')).' 23:59:59';
		}
		
		if($doCheck == 'do'){
		
		
			$query 		 = "SELECT
									a.id appID,
									a.start_date startDate,
									s.title serviceName,
									s.duration serviceDuration,
									a.status appStatus,
									a.employee_id employeeID,
									e.email employeeEmail,
									GROUP_CONCAT(ca.customer_id ORDER BY ca.id) customerIDs

								FROM 	   	   {$wpdb->prefix}bmify_appointments a
									LEFT JOIN  {$wpdb->prefix}bmify_customer_appointments ca	ON ca.appointment_id = a.id
									INNER JOIN {$wpdb->prefix}bmify_employees e					ON e.id = a.employee_id
									INNER JOIN {$wpdb->prefix}bmify_services s					ON s.id = a.service_id

								WHERE ca.status IN ('approved','pending') 
									AND (a.start_date BETWEEN '".$startDate."' AND '".$endDate."') 
									AND NOT EXISTS (
										SELECT 
												ns.notification_id,
												ns.appointment_id,
												ns.customer_id

											FROM {$wpdb->prefix}bmify_notifications_sent ns
											WHERE ns.notification_id = ".$nID."
												AND ns.appointment_id = a.id
												AND ns.employee_id = a.employee_id
									)

								GROUP BY a.id ORDER BY a.start_date";


			$results = $wpdb->get_results( $query, OBJECT );
		}
		
		return $results;
		
    }
	
}



	add_action( 'bookmify_send_notifications', function() { new CronNotification(); } );
	do_action( 'bookmify_send_notifications' );
