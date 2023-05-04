<?php
namespace Bookmify;

use Bookmify\HelperTime;
use Bookmify\HelperEmployees;
use Bookmify\HelperAppointments;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {exit; }


/**
 * Class Google Calendar Project
 */
class GoogleCalendarProject{
 
    private $client;
    private $service;
    private $googleData = array();

	/**
     * Construct
	 * @since 1.0.0
     */
	public function __construct() {
		
		require( BOOKMIFY_PATH.'inc/googleAPC/vendor/autoload.php' ); // google api php client;
		$googleClientID 	= get_option( 'bookmify_be_gc_client_id', '' );
		$googleClientSecret = get_option( 'bookmify_be_gc_client_secret', '' );
		$this->client = new \Google_Client();
        $this->client->setClientId($googleClientID);
        $this->client->setClientSecret($googleClientSecret);
		
	}
	
	public static function defineRedirectURI(){
        return admin_url( 'admin.php?page=bookmify_user_profile');
    }
	
	
	/**
     * Create a URL to obtain user authorization.
     */
    public function createAuthUrl($employeeID){
		
        $this->client->setRedirectUri(self::defineRedirectURI());
        $this->client->setState($employeeID);
        $this->client->addScope('https://www.googleapis.com/auth/calendar');
        $this->client->setApprovalPrompt('force');
        $this->client->setAccessType('offline');

        return $this->client->createAuthUrl();
    }

    public function fetchAccessTokenWithAuthCodeCustom($authCode){
		
        $this->client->setRedirectUri(self::defineRedirectURI());
        return $this->client->fetchAccessTokenWithAuthCode($authCode);
    }
	

	public function bookmifyAuthorizeEmployee($employeeID, $authCode)
    {
		global $wpdb;
		$googleClientID 	= get_option( 'bookmify_be_gc_client_id', '' );
		$googleClientSecret = get_option( 'bookmify_be_gc_client_secret', '' );
        $this->client = new \Google_Client();
        $this->client->setClientId($googleClientID);
        $this->client->setClientSecret($googleClientSecret);
		
		$googleData = HelperEmployees::getGoogleData($employeeID);
		
		try{
			

			if($googleData != NULL){
				$googleData		= json_decode(stripslashes($googleData));
				$accessToken 	= $googleData['accessToken'];
			}
			else{
				$accessToken 	= $this->fetchAccessTokenWithAuthCodeCustom($authCode);
			}

				
			$this->client->setAccessToken($accessToken);
			
			$this->googleData['accessToken'] = $this->client->getAccessToken();

			if ($this->client->isAccessTokenExpired()) {
				$this->client->refreshToken($this->client->getRefreshToken());
				$this->googleData['accessToken'] = $this->client->getAccessToken();
			}

			$this->service = new \Google_Service_Calendar($this->client);

			$this->listCalendarList();

			return true;

			
		}
		catch( \Exception $error ){
			return $error->getMessage();
		}
    }
	

	public function listCalendarList()
    {
        $calendars = [];

		$calendarList = $this->service->calendarList->listCalendarList(['minAccessRole' => 'writer']);

		foreach ($calendarList->getItems() as $calendar) {
			$calendars[] = [
				'id'      => $calendar->getId(),
				'primary' => $calendar->getPrimary(),
				'summary' => $calendar->getSummary()
			];
		}
		
		// set calendar id
		$this->googleData['calendarID'] = $calendars[0]['id'];

        return $calendars;
    }
	
	
	public function lastStep($employeeID, $authCode){
		
		$this->bookmifyAuthorizeEmployee($employeeID, $authCode);
		
		$encoded_data = json_encode($this->googleData);
	
		HelperEmployees::updateGoogleData($employeeID, $encoded_data);
		
		
		return $encoded_data; //implode($accessToken);
		
	}
	
	
	
	// Get google events
	public function getGoogleEvents($employeeID,$selectedDay){
		
		$googleMaxNumEvents = 2500;
		
		$output 	= array();
		$minCheck 	= HelperTime::getDateTimeRFC3339($selectedDay.' 00:00:00');
		$maxCheck 	= HelperTime::getDateTimeRFC3339($selectedDay.' 23:59:59');
		$optParams 	= array(
			'maxResults' 	=> $googleMaxNumEvents,
			'orderBy' 		=> 'startTime',
			'singleEvents' 	=> true,
			'timeMin' 		=> $minCheck,
			'timeMax' 		=> $maxCheck,
		);

		$googleData = HelperEmployees::getGoogleData($employeeID);
		
		if($googleData != NULL){
			$googleData 	= json_decode(stripslashes($googleData), true);
			$accessToken 	= $googleData['accessToken'];
			$calID 			= $googleData['calendarID'];
				
			$this->client->setAccessToken($accessToken);
			
			$this->googleData['accessToken'] = $this->client->getAccessToken();

			if ($this->client->isAccessTokenExpired()) {
				$this->client->refreshToken($this->client->getRefreshToken());
				$this->googleData['accessToken'] = $this->client->getAccessToken();
			}
			
			$this->service = new \Google_Service_Calendar($this->client);

			$this->listCalendarList();
			
			$encoded_data = json_encode($this->googleData);
	
			HelperEmployees::updateGoogleData($employeeID, $encoded_data);
			
			
			$events = $this->service->events->listEvents($calID, $optParams);
		
			foreach($events->getItems() as $key => $event){
				
				// Continue if event is set to "Free"
				if ($event->getTransparency() === 'transparent') {
					continue;
				}
				
				// Continue if event is created from Bookmify
				$extendedProperties = $event->getExtendedProperties();
				if ($extendedProperties !== null) {
					$private = $extendedProperties->private;
					if (is_array($private) && array_key_exists('bookmifyEvent', $private)) {
						continue;
					}
				}
				
				$eventStart = self::timeInMinutes(HelperTime::getCustomDateTime($event->getStart()->dateTime));
                $eventEnd 	= self::timeInMinutes(HelperTime::getCustomDateTime($event->getEnd()->dateTime));
				$output[$key]['start'] 	= $eventStart;
				$output[$key]['end'] 	= $eventEnd;
			}
			
		}
				

		return $output;
	}
	
	
	/* since bookmify v1.3.0 */
	// Get google events
	public function getGoogleEventsFromToSimple($startDate,$endDate,$employeeID){
		
		$googleMaxNumEvents = 2500;
		$startDate 			= HelperTime::getDateTimeRFC3339($startDate.' 00:00:00');
		$endDate 			= HelperTime::getDateTimeRFC3339($endDate.' 23:59:59');
		
		$output 	= array();
		$optParams 	= array(
			'maxResults' 	=> $googleMaxNumEvents,
			'orderBy' 		=> 'startTime',
			'singleEvents' 	=> true,
			'timeMin' 		=> $startDate,
			'timeMax' 		=> $endDate,
		);

		$googleData 		= HelperEmployees::getGoogleData($employeeID);
		
		if($googleData != NULL && $googleData != ''){
			$googleData 	= json_decode(stripslashes($googleData), true);
			$accessToken 	= $googleData['accessToken'];
			$calID 			= $googleData['calendarID'];
				
			$this->client->setAccessToken($accessToken);
			
			$this->googleData['accessToken'] = $this->client->getAccessToken();

			if ($this->client->isAccessTokenExpired()) {
				$this->client->refreshToken($this->client->getRefreshToken());
				$this->googleData['accessToken'] = $this->client->getAccessToken();
			}
			
			$this->service = new \Google_Service_Calendar($this->client);

			$this->listCalendarList();
			
			$encoded_data = json_encode($this->googleData);
	
			HelperEmployees::updateGoogleData($employeeID, $encoded_data);
			
			
			$events = $this->service->events->listEvents($calID, $optParams);
		
			foreach($events->getItems() as $key => $event){
				
				// Continue if event is set to "Free"
				if ($event->getTransparency() === 'transparent') {
					continue;
				}
				
				// Continue if event is created from Bookmify
				$extendedProperties = $event->getExtendedProperties();
				if ($extendedProperties !== null) {
					$private = $extendedProperties->private;
					if (is_array($private) && array_key_exists('bookmifyEvent', $private)) {
						continue;
					}
				}
				$start					= $event->getStart()->dateTime;
				$end					= $event->getEnd()->dateTime;
				$startTimeWithoutFormat	= HelperTime::getCustomDateTime($start);
				$endTimeWithoutFormat	= HelperTime::getCustomDateTime($end);
				$eventStart 			= self::timeInMinutes($startTimeWithoutFormat);
                $eventEnd 				= self::timeInMinutes($endTimeWithoutFormat);
				$startDate				= $startTimeWithoutFormat->format('Y-m-d');
				$endDate				= $endTimeWithoutFormat->format('Y-m-d');
				$output[$startDate][] 	= array($eventStart,$eventEnd);
			}
			
		}
				

		return $output;
	}
	
	public static function timeInMinutes($time){
		return ($time->format('H') * 60 + $time->format('i'));
	}
	
	public function updateEvent($appointmentID)
    {
		$gcEventID 		= HelperAppointments::getAppDataForGoogle($appointmentID, 'googleCalendarEventID');
		$employeeID 	= HelperAppointments::getAppDataForGoogle($appointmentID, 'employeeID');
		$googleData 	= HelperEmployees::getGoogleData($employeeID);
		
		if($googleData != NULL){
			$googleData 	= json_decode(stripslashes($googleData), true);
			$accessToken 	= $googleData['accessToken'];
			$calID 			= $googleData['calendarID'];
				
			$this->client->setAccessToken($accessToken);
			
			$this->googleData['accessToken'] = $this->client->getAccessToken();

			if ($this->client->isAccessTokenExpired()) {
				$this->client->refreshToken($this->client->getRefreshToken());
				$this->googleData['accessToken'] = $this->client->getAccessToken();
			}
			
			$this->service = new \Google_Service_Calendar($this->client);

			$this->listCalendarList();
			
			$encoded_data = json_encode($this->googleData);
	
			HelperEmployees::updateGoogleData($employeeID, $encoded_data);
			
			$event = $this->createEvent($appointmentID);
        	$this->service->events->update($calID, $gcEventID, $event);
		}
    }

    public function deleteEvent($appointmentID, $newEmployeeID = '')
    {
		$employeeID	= $newEmployeeID;
		if($employeeID == ''){
			$employeeID = HelperAppointments::getAppDataForGoogle($appointmentID, 'employeeID');
		}
		$gcEventID 		= HelperAppointments::getAppDataForGoogle($appointmentID, 'googleCalendarEventID');
		$googleData 	= HelperEmployees::getGoogleData($employeeID);
		
		if($googleData != NULL){
			$googleData 	= json_decode(stripslashes($googleData), true);
			$accessToken 	= $googleData['accessToken'];
			$calID 			= $googleData['calendarID'];
				
			$this->client->setAccessToken($accessToken);
			
			$this->googleData['accessToken'] = $this->client->getAccessToken();

			if ($this->client->isAccessTokenExpired()) {
				$this->client->refreshToken($this->client->getRefreshToken());
				$this->googleData['accessToken'] = $this->client->getAccessToken();
			}
			
			$this->service = new \Google_Service_Calendar($this->client);

			$this->listCalendarList();
			
			$encoded_data = json_encode($this->googleData);
	
			HelperEmployees::updateGoogleData($employeeID, $encoded_data);
			
			$this->service->events->delete($calID, $gcEventID);
		}
		
        
    }
	
	
	public function insertEvent($appointmentID,$newEmployeeID = '')
    {
		$employeeID	= $newEmployeeID;
		if(!is_numeric($newEmployeeID)){
			$employeeID = HelperAppointments::getAppDataForGoogle($appointmentID, 'employeeID');
		}
		
		$googleData 	= HelperEmployees::getGoogleData($employeeID);
		
		if($googleData != NULL){
			$googleData 	= json_decode(stripslashes($googleData), true);
			$accessToken 	= $googleData['accessToken'];
			$calID 			= $googleData['calendarID'];
				
			$this->client->setAccessToken($accessToken);
			
			$this->googleData['accessToken'] = $this->client->getAccessToken();

			if ($this->client->isAccessTokenExpired()) {
				$this->client->refreshToken($this->client->getRefreshToken());
				$this->googleData['accessToken'] = $this->client->getAccessToken();
			}
			
			$this->service = new \Google_Service_Calendar($this->client);

			$this->listCalendarList();
			
			$encoded_data = json_encode($this->googleData);
	
			HelperEmployees::updateGoogleData($employeeID, $encoded_data);
		
			// create event
        	$event 	= $this->createEvent($appointmentID);
//			// insert event
			$event = $this->service->events->insert($calID,	$event);
			
			
			return $event->getId();
			//return 'Event created: '.$event->htmlLink;
			
			

		}else{
			return '';
		}
    }
	
	
	public function createEvent($appointmentID)
    {
		$service 	= HelperAppointments::getAppDataForGoogle($appointmentID, 'service');
		$location 	= HelperAppointments::getAppDataForGoogle($appointmentID, 'location');
		$startDate 	= HelperAppointments::getAppDataForGoogle($appointmentID, 'startDate');
		$endDate 	= HelperAppointments::getAppDataForGoogle($appointmentID, 'endDate');

		$attendees = $this->getAttendees($appointmentID);
		
		$googleSendEventInvitation 	= get_option( 'bookmify_be_gc_send_invitaion', 'on' );
		if($googleSendEventInvitation == 'on'){
			$googleSendEventInvitation = 'all';
		}else{
			$googleSendEventInvitation = 'none';
		}
		$event = new \Google_Service_Calendar_Event(array(
			'summary'            => $service,
			'location'           => $location,
			'attendees'          => $attendees,
			'sendUpdates'        => $googleSendEventInvitation,
			'description'        => get_option( 'bookmify_be_company_info_name', '' ),
			'start'              => [
				'dateTime' 			=> HelperTime::getDateTimeRFC3339($startDate),
			],
			'end'              	 => [
				'dateTime' 			=> HelperTime::getDateTimeRFC3339($endDate),
			],
			'extendedProperties' => [
				'private' 			=> [
					'bookmifyEvent'         => true,
					'bookmifyAppointmentID' => $appointmentID
				]
			],
			'status'             => 'tentative',
		));

			
		return $event;
        
    }
	
	
	private function getAttendees($appointmentID)
    {
        $attendees = [];
		
		$employeeID 	= HelperAppointments::getAppDataForGoogle($appointmentID, 'employeeID');
		$googleData 	= HelperEmployees::getGoogleData($employeeID);
		$googleData 	= json_decode(stripslashes($googleData), true);
		$employee 		= HelperAppointments::getAppDataForGoogle($appointmentID, 'employee');
		
		$customerIDs 	= HelperAppointments::getAppDataForGoogle($appointmentID, 'customerIDs');
		$appStatuses 	= HelperAppointments::getAppDataForGoogle($appointmentID, 'customerAppStatus');

		$googleAttendees 	= get_option( 'bookmify_be_gc_add_attendees', 'on' );
		$googleAddPending 	= get_option( 'bookmify_be_gc_add_pending', '' );
		
        if ($googleAttendees == 'on') {
           
            $attendees[] = [
                'displayName'    => $employee,
                'email'          => $googleData['calendarID'],
                'responseStatus' => 'accepted'
            ];

            foreach ($customerIDs as $key=>$customerID) {
                $appStatus = $appStatuses[$key];

                if ($appStatus == 'approved' || ($appStatus == 'pending' && $googleAddPending == 'on')) 
				{
                    $attendees[] = [
                        'displayName'    => HelperAppointments::getCustomerInfo($customerID, 'name'),
                        'email'          => HelperAppointments::getCustomerInfo($customerID, 'email'),
                        'responseStatus' => 'needsAction'
                    ];
                }
            }
        }

        return $attendees;
    }

	
}

