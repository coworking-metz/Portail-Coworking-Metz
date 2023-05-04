<?php
namespace Bookmify;

use Bookmify\Helper;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {exit; }


/**
 * Class Helper Appointments
 */
class HelperAppointments
{
	
	public static function clonableForm(){
		$extrasTabContent 		= self::getExtraServicesOnEdit();
		$paymentTabContent 		= self::getPaymentsOnEdit();
		$detailsTabContent 		= self::detailsTabContent();	
		
		$html = '<div class="bookmify_be_popup_form_wrap">
					'.self::serviceListAsNano().'
					'.self::categoryListAsNano().'
					'.self::customerListAsNano().'
					'.self::employeeListAsNano().'
					'.self::locationListAsNano().'
					'.self::numberListForPeople().'
					'.self::appointmentTime('appointment_time').'
					<div class="bookmify_be_popup_form_position_fixer">
						<div class="bookmify_be_popup_form_bg">
							<div class="bookmify_be_popup_form">

								<div class="bookmify_be_popup_form_header">
									<h3>'.esc_html__('New Appointment','bookmify').'</h3>
									<span class="closer"></span>
								</div>

								<div class="bookmify_be_popup_form_content">
									<div class="bookmify_be_popup_form_content_in">

										<div class="bookmify_be_popup_form_fields">

											<div class="bookmify_be_appointmentstabs_wrap bookmify_be_tab_wrap">
												<div class="bookmify_be_link_tabs">
													<ul class="bookmify_be_appointmentstabs_nav">
														<li class="active"><a class="bookmify_be_tab_link" href="#">'.esc_html__('Details','bookmify').'</a></li>
														<li><a class="bookmify_be_tab_link" href="#">'.esc_html__('Extras','bookmify').'</a></li>
														<li><a class="bookmify_be_tab_link" href="#">'.esc_html__('Payment','bookmify').'</a></li>
													</ul>
												</div>
												<div class="bookmify_be_appointmentstabs_content bookmify_be_content_tabs">
													<div class="bookmify_be_tab_pane active">'.$detailsTabContent.'</div>
													<div class="bookmify_be_tab_pane">'.$extrasTabContent.'</div>
													<div class="bookmify_be_tab_pane">'.$paymentTabContent.'</div>
												</div>
											</div>


										</div>

									</div>
								</div>

								'.Helper::bookmifyPopupSaveSection().'

							</div>
						</div>
					</div>
				</div>';
		
		return $html;
	}
	public static function extraServiceCheckbyCustomerAndAppointmentID($appointmentID = '',$customerID,$serviceID,$people_count,$employeeID){
		global $wpdb;
		
		$list					= '';
		$extra_services_ids		= array();
		$extra_quantity_price	= array();
		if($appointmentID != ''){
			$appointmentID 		= esc_sql($appointmentID);
			$customerID 		= esc_sql($customerID);
			$query = "SELECT 
							cae.extra_id 		extraID,
							cae.price 			extraPrice,
							cae.quantity 		extraQuantity,
							ca.number_of_people peopleCount
						
						FROM 			{$wpdb->prefix}bmify_customer_appointments ca 
							INNER JOIN 	{$wpdb->prefix}bmify_customer_appointments_extras cae ON ca.id = cae.customer_appointment_id 
						
						WHERE ca.customer_id=".$customerID." AND ca.appointment_id=".$appointmentID;
			$results = $wpdb->get_results( $query);
			foreach($results as $result){
				$extra_services_ids[] 									= $result->extraID;
				$extra_quantity_price[$result->extraID]['quantity'] 	= $result->extraQuantity;
				$extra_quantity_price[$result->extraID]['price'] 		= $result->extraPrice;
				$extra_quantity_price[$result->extraID]['people_count']	= $result->peopleCount;
			}
		}
		$count 		= $people_count['name'][$customerID];
		if($count == '' || $count == 'undefined'){
			$count 	= 1;
		}
		
		$serviceID 	= esc_sql($serviceID);
		$query 		= "SELECT * FROM {$wpdb->prefix}bmify_extra_services WHERE service_id=".$serviceID." ORDER BY position, title";
		$results 	= $wpdb->get_results( $query);
		$subtotal 	= 0;
		$peopleCount = 1;
		foreach($results as $result){
			$disabled 	= 'disabled';
			$checked 	= '';
			$active 	= '';
			$price 		= $result->price;
			$quantity 	= 1;
			$extraServiceID = $result->id;
			if($appointmentID != ''){
				if(in_array( $extraServiceID, $extra_services_ids )){

					$checked 	 = 'checked="checked"';
					$disabled 	 = '';
					$active 	 = 'active';
					$price		 = $extra_quantity_price[$extraServiceID]['price'];
					$quantity	 = $extra_quantity_price[$extraServiceID]['quantity'];
					$peopleCount = $extra_quantity_price[$extraServiceID]['people_count'];
					$subtotal 	+= ($quantity*$price*$peopleCount);
					
				}
			}
			$new_price = $price * $quantity * $peopleCount;
			$list .=   '<div class="bookmify_extra_service_item_app '.$active.'" data-id="'.$extraServiceID.'">
							<span class="bookmify_be_checkbox">
								<input class="req" type="checkbox" '.$checked.'>
								<span class="checkmark">
									<img class="bookmify_be_svg" src="'.BOOKMIFY_ASSETS_URL.'/img/checked.svg" alt="" />
								</span>
							</span>
							<div class="info_holder">
								<div class="title_holder">
									<input type="hidden" class="extra_duration" value="'.$result->duration.'" />
									<label>'.$result->title.'</label>
								</div>
								<div class="price_holder">
									<div class="bookmify_be_quantity '.$disabled.'">
										<input type="number" min="1" max="'.$result->capacity_max.'" name="extra_quantity" value="'.$quantity.'" readonly  />
										<span class="increase"><span></span></span>
										<span class="decrease"><span></span></span>
									</div>
									<div class="price">
										<input type="hidden" name="extra_price" value="'.$price.'" />
										<span class="price_span">'.Helper::bookmifyPriceCorrection($new_price).'</span>
									</div>
								</div>
							</div>	
						</div>';
		}
		// если выбранный сервис совпадает с сервисом из существующего заказа
		$existServiceID = '';
		$serviceTotal 	= 0;
		$servicePrice 	= 0;
		if($appointmentID != ''){
			$appointmentID 	= esc_sql($appointmentID);
			$query 		= "SELECT service_id FROM {$wpdb->prefix}bmify_appointments WHERE id=".$appointmentID;
			$results 	= $wpdb->get_results( $query);
			foreach($results as $result){
				$existServiceID = $result->service_id;
			}
		}
		$serviceID 		= esc_sql($serviceID);
		$employeeID 	= esc_sql($employeeID);
		$query 			= "SELECT price FROM {$wpdb->prefix}bmify_employee_services WHERE service_id=".$serviceID." AND employee_id=".$employeeID;
		$results 		= $wpdb->get_results( $query);
		foreach($results as $result){
			$servicePrice = $result->price;
		}
		if($appointmentID != '' && ($existServiceID == $serviceID)){
			$appointmentID 	= esc_sql($appointmentID);
			$customerID 	= esc_sql($customerID);
			$query 		= "SELECT * FROM {$wpdb->prefix}bmify_customer_appointments WHERE appointment_id=".$appointmentID." AND customer_id=".$customerID;
			$results 	= $wpdb->get_results( $query);
			if(!empty($results)){
				foreach($results as $result){
					$serviceTotal += $result->price;
				}
			}else{
				$serviceTotal += $servicePrice;
			}
		}else{
			$serviceTotal += $servicePrice;
		}
		
		$list .= '<div class="sub_total">
					<input type="hidden" name="subtotal_value" value="'.$subtotal.'" />
					<input type="hidden" name="service_price_for_customer" value="'.$serviceTotal.'" />
					<input type="hidden" name="people_count" value="'.$count.'" />
					<span class="sub_text">'.esc_html__('Subtotal:', 'bookmify').'</span>
					<span class="sub_price">'.Helper::bookmifyPriceCorrection($subtotal).'</span>
				</div>';
		
		
		return $list;
	}
	public static function totalServicePriceForAppointment($appointmentID = '',$serviceID,$employeeID,$total,$customersArray, $taxSummary = 0){
		global $wpdb;
		
		$taxServiceTotal 		= 0;
		$taxExtraTotal 			= 0;
		
		$onlyTaxService			= 0;
		$onlyTaxExtra			= 0;
		
		
		// если выбранный сервис совпадает с сервисом из существующего заказа
		$existServiceID 		= '';
		if($appointmentID != ''){
			$appointmentID 		= esc_sql($appointmentID);
			$query 				= "SELECT service_id FROM {$wpdb->prefix}bmify_appointments WHERE id=".$appointmentID;
			$results 			= $wpdb->get_results( $query);
			$existServiceID 	= $results[0]->service_id;
		}
		// итоговая сумма на выбранные ЭКСТРА СЕРВИСЫ
		if($total != 'service' || $total == 'extra_tax'){
			$extraTotal	= 0;
			if($appointmentID != '' && ($existServiceID == $serviceID)){
				
				$appointmentID 	= esc_sql($appointmentID);
				$query = "SELECT 
								cae.price 			extraPrice,
								cae.quantity 		extraQuantity,
								ca.number_of_people peopleCount,
								ca.customer_id		customerID

							FROM 			{$wpdb->prefix}bmify_customer_appointments ca 
								INNER JOIN 	{$wpdb->prefix}bmify_customer_appointments_extras cae ON ca.id = cae.customer_appointment_id 

							WHERE ca.appointment_id=".$appointmentID." AND ca.status in ('pending', 'approved')";
				$results = $wpdb->get_results( $query);
				foreach($results as $result){
					$quantity 		= $result->extraQuantity;
					$price 			= $result->extraPrice;
					$peopleCount 	= $result->peopleCount;
					$customerID		= $result->customerID;
					$taxCustomer	= self::taxOfCustomer($appointmentID,$customerID);
					$extraTotal 	+= ($quantity * $price * $peopleCount);
					$taxExtraTotal 	+= ($quantity * $price * $peopleCount * (($taxCustomer + 100)/100));
					$onlyTaxExtra 	+= ($quantity * $price * $peopleCount * (($taxCustomer)/100));
				}
			}
		}
		
		
		// итоговая сумма на выбранный СЕРВИС
		if($total != 'extra' || $total == 'service_tax'){
			$serviceTotal 	= 0;
			$servicePrice 	= 0;
			
			$serviceID 		= esc_sql($serviceID);
			$employeeID 	= esc_sql($employeeID);
			$query 			= "SELECT price FROM {$wpdb->prefix}bmify_employee_services WHERE service_id=".$serviceID." AND employee_id=".$employeeID;
			$results 		= $wpdb->get_results( $query);
			foreach($results as $result){
				$servicePrice = $result->price;
			}
			
			
			if($appointmentID != '' && ($existServiceID == $serviceID)){
				foreach($customersArray as $customerID){
					$appointmentID 	= esc_sql($appointmentID);
					$customerID		= esc_sql($customerID);
					$query 			= "SELECT price,number_of_people FROM {$wpdb->prefix}bmify_customer_appointments WHERE appointment_id=".$appointmentID." AND customer_id=".$customerID." AND status in ('pending', 'approved')";
					$results 		= $wpdb->get_results($query);
					$taxCustomer	= self::taxOfCustomer($appointmentID,$customerID);
					foreach($results as $result){
						$serviceTotal 		+= ($result->price * $result->number_of_people);
						$taxServiceTotal 	+= ($result->price * $result->number_of_people * (($taxCustomer + 100) / 100));
						$onlyTaxService 	+= ($result->price * $result->number_of_people * (($taxCustomer) / 100));
					}
				}
				
			}else{
				$serviceTotal 		+= (count($customersArray)*$servicePrice);
				$taxServiceTotal 	+= (count($customersArray)*$servicePrice)*(($taxSummary + 100)/100);
				$onlyTaxService 	+= (count($customersArray)*$servicePrice)*(($taxSummary)/100);
			}
		}
		
		// вывод
		$output = '';
		if($total == 'total'){
			$totalPrice 	= ($taxServiceTotal + $taxExtraTotal);
			$inputHidden 	= '<input type="hidden" name="total_price" value="'.$totalPrice.'" />';
			$totalPrice	 	= '<span>'.Helper::bookmifyPriceCorrection($totalPrice).'</span>';
			$output 		= $totalPrice.$inputHidden;
		}else if($total == 'service'){
			$inputHidden 	= '<input type="hidden" name="total_service" value="'.$serviceTotal.'" />';
			$serviceTotal 	= '<span>'.Helper::bookmifyPriceCorrection($serviceTotal).'</span>';
			$output 		= $serviceTotal.$inputHidden;
		}else if($total == 'extra'){
			$inputHidden 	= '<input type="hidden" name="total_extra" value="'.$extraTotal.'" />';
			$extraTotal 	= '<span>'.Helper::bookmifyPriceCorrection($extraTotal).'</span>';
			$output 		= $extraTotal.$inputHidden;
		}else if($total == 'service_tax'){
			$inputHidden 	= '<input type="hidden" name="total_service_tax" value="'.$onlyTaxService.'" />';
			$serviceTotal 	= '<span>'.Helper::bookmifyPriceCorrection($onlyTaxService).'</span>';
			$output 		= $serviceTotal.$inputHidden;
		}else if($total == 'extra_tax'){
			$inputHidden 	= '<input type="hidden" name="total_extra_tax" value="'.$onlyTaxExtra.'" />';
			$extraTotal 	= '<span>'.Helper::bookmifyPriceCorrection($onlyTaxExtra).'</span>';
			$output 		= $extraTotal.$inputHidden;
		}
		return $output;
	}
	/*
		Получение всевозможных экстра сервисов,по сервису
	*/
	public static function getExtraServicesOnEdit($customerIDs = '',$serviceID = '',$employeeID = '',$appointmentID = '',$people_count = ''){
		global $wpdb;
		$output = '<div class="bookmify_be_appointment_extras">';
		$list 	= '';
		if($customerIDs == '' || $serviceID == '' || $employeeID == ''){
			$output .= '<div class="bookmify_be_infobox danger"><label>';	
			$output 	.= esc_html__('Select customer, employee and service', 'bookmify');	
			$output .= '</label></div>';
		}else{
			$serviceID 	= esc_sql($serviceID);
			$count		= $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->prefix}bmify_extra_services WHERE service_id=".$serviceID );
			if($count == 0){
				
				// уведомление, если не имеются экстра сервисы у выбранного сервиса
				$output .= '<div class="bookmify_be_infobox"><label>';	
				$output .= esc_html__('This service doesn\'t have extra services', 'bookmify');	
				$output .= '</label></div>';
			}else{
				
				$list   .= '<div class="bookmify_be_customer_extras_list">';
				// получить все необходимые данные если, экстра сервисы существуют для выбранного сервиса
				$customersArray = explode(",",$customerIDs);
				foreach($customersArray as $customerID){
					$list  	.= '<div class="bookmify_be_extras_customer_appointment item_'.self::getCustomerAppointmentStatus($customerID,$appointmentID).'" data-customer-id="'.$customerID.'">';
					$list  	.= '<div class="name_holder">';
					$list  	.= '<span class="name">'.Helper::bookmifyGetCustomerCol($customerID, 'full_name').'</span>';
					$list  	.= '<span class="email">'.Helper::bookmifyGetCustomerCol($customerID, 'email').'</span>';
					$list  	.= '</div>';
					$list	.= '<input type="hidden" value="'.self::taxOfCustomer($appointmentID,$customerID).'" class="tax_h_customer" />';
					$list  	.= self::extraServiceCheckbyCustomerAndAppointmentID($appointmentID,$customerID,$serviceID,$people_count,$employeeID);
					$list  	.= '</div>';
				}
				$list   	.= '</div>';
				$output 	.= $list;
				$output 	.= '<div class="total">';
				$output 	.= '<div class="service_price">';
				$output 	.= '<div class="total_text">'.esc_html__('Service Price:', 'bookmify').'</div>';
				$output 	.= '<div class="price">'.self::totalServicePriceForAppointment($appointmentID,$serviceID,$employeeID, 'service', $customersArray).'</div>';
				$output 	.= '</div>';
				$output 	.= '<div class="extra_price">';
				$output 	.= '<div class="total_text">'.esc_html__('Extras Price:', 'bookmify').'</div>';
				$output 	.= '<div class="price">'.self::totalServicePriceForAppointment($appointmentID,$serviceID,$employeeID, 'extra', $customersArray).'</div>';
				$output 	.= '</div>';
				
				
				$taxSummary  = self::checkPaymentForTaxes($appointmentID,$customersArray);
				$output 	.= '<div class="service_tax_price">';
				$output 	.= '<div class="total_text">'.esc_html__('Service Tax:', 'bookmify').'</div>';
				$output 	.= '<div class="price">'.self::totalServicePriceForAppointment($appointmentID,$serviceID,$employeeID, 'service_tax', $customersArray, $taxSummary).'</div>';
				$output 	.= '</div>';
				$output 	.= '<div class="extra_tax_price">';
				$output 	.= '<div class="total_text">'.esc_html__('Extras Tax:', 'bookmify').'</div>';
				$output 	.= '<div class="price">'.self::totalServicePriceForAppointment($appointmentID,$serviceID,$employeeID, 'extra_tax', $customersArray, $taxSummary).'</div>';
				$output 	.= '</div>';
				
				$output 	.= '<div class="total_price">';
				$output 	.= '<div class="total_text">'.esc_html__('Total Price:', 'bookmify').'</div>';
				$output 	.= '<div class="price">'.self::totalServicePriceForAppointment($appointmentID,$serviceID,$employeeID, 'total', $customersArray, $taxSummary).'</div>';
				$output 	.= '</div>';
				$output 	.= '</div>';
				
			}
		}
		$output .= '</div>';
		return $output;
	}
	
	public static function appointmentTime($class = NULL){
		$html	 = '<div class="nano scrollbar-inner '.$class.'"><div class="nano-content">';
		$html 	.= '<div class="nodata">'.esc_html__('Nothing to choose', 'bookmify').'</div>';
		$html 	.= '</div></div>';
		return $html;
	}
	public static function paymentForCustomer($appointmentID = '',$customerID,$serviceID,$employeeID,$extra){
		global $wpdb;
		$list					= '';
		$taxSummary 			= 0;
		$taxServiceTotal 		= 0;
		$taxExtraTotal 			= 0;
		$onlyTaxService			= 0;
		$onlyTaxExtra			= 0;
		$taxCustomer			= self::taxOfCustomer($appointmentID,$customerID);
		
		if($appointmentID != '' && $extra != ''){
			$customerID 		= esc_sql($customerID);
			$appointmentID 		= esc_sql($appointmentID);
			$query 		= "SELECT 
								cae.price 			extraPrice,
								cae.quantity 		extraQuantity,
								cae.extra_id 		extraID,
								ca.number_of_people peopleCount,
								ca.customer_id		customerID

							FROM 			{$wpdb->prefix}bmify_customer_appointments ca 
								INNER JOIN 	{$wpdb->prefix}bmify_customer_appointments_extras cae ON ca.id = cae.customer_appointment_id 

							WHERE ca.customer_id=".$customerID." AND ca.appointment_id=".$appointmentID;
			$results = $wpdb->get_results( $query);

			$extra_services_ids		= array();
			$extra_quantity_price	= array();
			foreach($results as $result){
				$extra_services_ids[] 									= $result->extraID;
				$extra_quantity_price[$result->extraID]['quantity'] 	= $result->extraQuantity;
				$extra_quantity_price[$result->extraID]['price'] 		= $result->extraPrice;
				$extra_quantity_price[$result->extraID]['peopleCount'] 	= $result->peopleCount;
			}
		}
		$extraTotal = 0;
		if($extra != ''){
			$serviceID 		= esc_sql($serviceID);
			$query 			= "SELECT * FROM {$wpdb->prefix}bmify_extra_services WHERE service_id=".$serviceID." ORDER BY position, title";
			$results 		= $wpdb->get_results( $query);
			foreach($results as $result){
				$price 		= $result->price;
				$quantity 	= 1;
				if($appointmentID != ''){
					if(in_array( $result->id, $extra_services_ids )){
						$price		 		 = $extra_quantity_price[$result->id]['price'];
						$quantity	 		 = $extra_quantity_price[$result->id]['quantity'];
						$peopleCount	 	 = $extra_quantity_price[$result->id]['peopleCount'];
						$extraTotal		 	+= ($quantity*$price*$peopleCount);
						$onlyTaxExtra		+= ($quantity*$price*$peopleCount*($taxCustomer/100));
						$taxExtraTotal		+= ($quantity*$price*$peopleCount*(($taxCustomer+100)/100));
					}
				}
			}
		}
			
		
		// цена на сервис
		$serviceTotal 	= 0;
		$servicePrice 	= 0;
		// если выбранный сервис совпадает с сервисом из существующего заказа
		$existServiceID = '';
		if($appointmentID != ''){
			$appointmentID = esc_sql($appointmentID);
			$query 		= "SELECT * FROM {$wpdb->prefix}bmify_appointments WHERE id=".$appointmentID;
			$results 	= $wpdb->get_results( $query);
			foreach($results as $result){
				$existServiceID 	= $result->service_id;
				$existEmployeeID 	= $result->employee_id;
			}
		}
		
		$serviceID 	= esc_sql($serviceID);
		$employeeID = esc_sql($employeeID);
		$query 		= "SELECT price FROM {$wpdb->prefix}bmify_employee_services WHERE service_id=".$serviceID." AND employee_id=".$employeeID;
		$results 	= $wpdb->get_results( $query);
		foreach($results as $result){
			$servicePrice = $result->price;
		}
		$peopleCount = 1;
		$refresh = '';
		if($appointmentID != '' && ($existServiceID == $serviceID) && ($existEmployeeID == $employeeID)){
			
			$appointmentID 	= esc_sql($appointmentID);
			$customerID 	= esc_sql($customerID);
			$query 			= "SELECT * FROM {$wpdb->prefix}bmify_customer_appointments WHERE appointment_id=".$appointmentID." AND customer_id=".$customerID;
			$results 		= $wpdb->get_results( $query);
			foreach($results as $result){
				$serviceTotal 		= $result->price;
				$onlyTaxService 	= ($serviceTotal*$taxCustomer)/100;
				$taxServiceTotal 	= ($serviceTotal*($taxCustomer+100))/100;
				$paymentID 			= $result->payment_id;
				$peopleCount		= $result->number_of_people;
			}
			if(empty($results)){
				$refresh 		= 'refresh';
			}
			if($paymentID){
				$paymentID 	= esc_sql($paymentID);
				$query 		= "SELECT * FROM {$wpdb->prefix}bmify_payments WHERE id=".$paymentID;
				$results 	= $wpdb->get_results( $query);
				foreach($results as $result){
					$createdDay = date_i18n(get_option('bookmify_be_date_format', 'd F, Y'), strtotime($result->created_date));
					$paidType	= $result->paid_type;
				}
			}else{
				$createdDay 	= '<span>'.date_i18n(get_option('bookmify_be_date_format', 'd F, Y')).'</span>';
				$paidType		= esc_html__('Local', 'bookmify');
			}
		}else{
			$serviceTotal 		= $servicePrice;
			$onlyTaxService 	= ($serviceTotal*$taxCustomer)/100;
			$taxServiceTotal 	= ($serviceTotal*($taxCustomer+100))/100;
			$createdDay 		= '<span>'.date_i18n(get_option('bookmify_be_date_format', 'd F, Y')).'</span>';
			$paidType			= esc_html__('Local', 'bookmify');
		}
		if($refresh == 'refresh'){
			$serviceTotal 		= $servicePrice;
			$onlyTaxService 	= ($serviceTotal*$taxCustomer)/100;
			$taxServiceTotal 	= ($serviceTotal*($taxCustomer+100))/100;
		}
		$serviceSingle 			= $serviceTotal;
		$serviceTotal 			*= $peopleCount;
		$onlyTaxService 		*= $peopleCount;
		$list .= '<div class="payment_date">
					<input type="hidden" name="payment_date" value="" />
					<span class="date_text">'.esc_html__('Date:', 'bookmify').'</span>
					<span class="date_span">'.$createdDay.'</span>
				  </div>';
		
		$list .= '<div class="payment_method">
					<input type="hidden" name="payment_method" value="" />
					<span class="method_text">'.esc_html__('Payment Method:', 'bookmify').'</span>
					<span class="method_span">'.$paidType.'</span>
				  </div>';
		
		$list .= '<div class="extra_total">
					<input type="hidden" name="extra_total" value="'.$extraTotal.'" />
					<span class="sub_text">'.esc_html__('Extras Price:', 'bookmify').'</span>
					<span class="extra_price">'.Helper::bookmifyPriceCorrection($extraTotal).'</span>
				  </div>';
		
		$list .= '<div class="service_total">
					<input type="hidden" name="service_total" value="'.$serviceTotal.'" />
					<span class="sub_text">'.esc_html__('Service Price:', 'bookmify').'</span>
					<span class="service_price">'.Helper::bookmifyPriceCorrection($serviceTotal).'</span>
				  </div>';
		
		$list .= '<div class="extra_tax_total">
					<input type="hidden" name="extra_tax_total" value="'.$onlyTaxExtra.'" />
					<span class="sub_text">'.esc_html__('Extra Tax:', 'bookmify').'</span>
					<span class="extra_tax_price">'.Helper::bookmifyPriceCorrection($onlyTaxExtra).'</span>
				  </div>';
		
		$list .= '<div class="service_tax_total">
					<input type="hidden" name="service_tax_total" value="'.$onlyTaxService.'" />
					<span class="sub_text">'.esc_html__('Service Tax:', 'bookmify').'</span>
					<span class="service_tax_price">'.Helper::bookmifyPriceCorrection($onlyTaxService).'</span>
				  </div>';
		
		$subTotal = $extraTotal + $serviceTotal + $onlyTaxExtra + $onlyTaxService;
		
		$list .= '<div class="sub_total">
					<input type="hidden" name="sub_total" value="'.$subTotal.'" />
					<input type="hidden" name="service_price_for_customer" value="'.$serviceSingle.'" />
					<input type="hidden" name="people_count" value="'.$peopleCount.'" />
					<span class="sub_text">'.esc_html__('Total:', 'bookmify').'</span>
					<span class="sub_price">'.Helper::bookmifyPriceCorrection($subTotal).'</span>
				  </div>';
		
		return $list;
	}
	public static function getPaymentsOnEdit($customerIDs = '',$serviceID = '',$employeeID = '',$appointmentID = ''){
		global $wpdb;
		$output = '<div class="bookmify_be_appointment_payments">';
		$list 	= '';
		if($customerIDs == '' || $serviceID == '' || $employeeID == ''){
			$output .= '<div class="bookmify_be_infobox danger"><label>';	
			$output .= esc_html__('Select customer, employee and service', 'bookmify');	
			$output .= '</label></div>';
		}else{
			$extra 		= 'extra';
			$serviceID 	= esc_sql($serviceID);
			$count		= $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->prefix}bmify_extra_services WHERE service_id=".$serviceID );
			if($count == 0){
				$extra = '';
			}
			
			$list   .= '<div class="bookmify_be_customer_payments_list">';
			// получить все необходимые данные если, экстра сервисы существуют для выбранного сервиса
			$customersArray = explode(",",$customerIDs);
			foreach($customersArray as $customerID){
				$list  .= '<div class="bookmify_be_extras_customer_appointment item_'.self::getCustomerAppointmentStatus($customerID,$appointmentID).'" data-customer-id="'.$customerID.'">';
				$list  .= '<input type="hidden" value="'.self::taxOfCustomer($appointmentID,$customerID).'" class="tax_h_customer" />';
				$list  .= '<div class="name_holder">';
				$list  .= '<span class="name">'.Helper::bookmifyGetCustomerCol($customerID, 'full_name').'</span>';
				$list  .= '<span class="email">'.Helper::bookmifyGetCustomerCol($customerID, 'email').'</span>';
				$list  .= '</div>';
				$list  .= self::paymentForCustomer($appointmentID,$customerID,$serviceID,$employeeID,$extra);
				$list  .= '</div>';
			}
			$list   .= '</div>';
			$output .= $list;
		}
		$output .= '</div>';
		return $output;
	}
	
	/**
     * Get Locations as nano list.
	 * @since 1.0.0
     */
    public static function locationListAsNano()
    {
        global $wpdb;
		
		$output 	= '<div class="nano scrollbar-inner location_list"><div class="nano-content">';
		
		$query 		= "SELECT 
								l.id	 			locationID,
								l.title 			locationTitle,
								l.address	 		locationAddress,
								el.employee_id	 	employeeID

							FROM 			{$wpdb->prefix}bmify_locations l 
								INNER JOIN 	{$wpdb->prefix}bmify_employee_locations el ON l.id = el.location_id ORDER BY l.title";
		$results = $wpdb->get_results( $query);
		
		$resultArray = array();
		foreach($results as $result){
			$resultArray[$result->locationID]['id1'] 			= $result->locationID;
			$resultArray[$result->locationID]['id2'][] 			= $result->employeeID;
			$resultArray[$result->locationID]['title']			= $result->locationTitle;
			$resultArray[$result->locationID]['address']		= $result->locationAddress;
		}
		
		
		foreach($resultArray as $item){
			$employeeIDs 		= implode(',', $item['id2']);
			$serviceList 		= array();
			$categoryList 		= array();
			foreach($item['id2'] as $id2){
				$id2 			= esc_sql($id2);
				$query 			= "SELECT service_id FROM {$wpdb->prefix}bmify_employee_services WHERE employee_id=".$id2;
				$results		= $wpdb->get_results( $query);
				foreach($results as $result){
					$rServiceID			= $result->service_id;
					$serviceList[] 		= $rServiceID;
					$rServiceID 		= esc_sql($rServiceID);
					$query2 			= "SELECT category_id FROM {$wpdb->prefix}bmify_services WHERE id=".$rServiceID;
					$resultCats	 		= $wpdb->get_results( $query2);
					foreach($resultCats as $resultCat){
						$categoryList[] = $resultCat->category_id;
					}
				}
			}
			$serviceList 		= array_unique($serviceList);
			$categoryList 		= array_unique($categoryList);
			$serviceList 		= implode(',', $serviceList);
			$categoryList 		= implode(',', $categoryList);
			$output 	.= '<div data-location-id="'.$item['id1'].'" data-employee-id="'.$employeeIDs.'" data-service-id="'.$serviceList.'" data-category-id="'.$categoryList.'">'.$item['title'].'</div>';
		}
		
		$output .= '</div></div>';
		return $output;
    }
	
	/**
     * Get Services as nano list.
	 * @since 1.0.0
     */
    public static function serviceListAsNano()
    {
        global $wpdb;
		
		$output 		= '<div class="nano scrollbar-inner service_list"><div class="nano-content">';
		
		$query 			= "SELECT 
								s.id	 			serviceID,
								s.capacity_min 		serviceCapMin,
								s.capacity_max 		serviceCapMax,
								s.title 			serviceTitle,
								s.category_id		categoryID,
								es.employee_id		employeeID
								
							FROM 			{$wpdb->prefix}bmify_services s 
								INNER JOIN 	{$wpdb->prefix}bmify_employee_services es ON s.id = es.service_id ORDER BY s.title";
		$results 		= $wpdb->get_results( $query);
		
		$resultArray 	= array();
		foreach($results as $result){
			$resultArray[$result->serviceID]['id1'] 				= $result->serviceID;
			$resultArray[$result->serviceID]['id2'] 				= $result->categoryID;
			$resultArray[$result->serviceID]['id3'][] 				= $result->employeeID;
			$resultArray[$result->serviceID]['title']				= $result->serviceTitle;
			$resultArray[$result->serviceID]['capacity_min']		= $result->serviceCapMin;
			$resultArray[$result->serviceID]['capacity_max']		= $result->serviceCapMax;
		}
		
		foreach($resultArray as $item){
			$employeeIDs 		= implode(',', $item['id3']);
			$locationList 		= array();
			foreach($item['id3'] as $id3){
				$id3 			= esc_sql($id3);
				$query 			= "SELECT location_id FROM {$wpdb->prefix}bmify_employee_locations WHERE employee_id=".$id3;
				$results 		= $wpdb->get_results( $query);
				foreach($results as $result){
					$locationList[] = $result->location_id;
				}
			}
			$locationList 		= array_unique($locationList);
			$locationList 		= implode(',', $locationList);
			$output 			.= '<div data-service-id="'.$item['id1'].'" data-category-id="'.$item['id2'].'" data-employee-id="'.$employeeIDs.'" data-location-id="'.$locationList.'" data-capacity-min="'.$item['capacity_min'].'" data-capacity-max="'.$item['capacity_max'].'">'.Helper::titleDecryption($item['title']).'</div>';
		}
		
		
		$output .= '</div></div>';
		return $output;
    }
	
	/**
     * Get Services' Employees as nano list.
	 * @since 1.0.0
     */
    public static function employeeListAsNano()
    {
        global $wpdb;
		
		$output 		= '<div class="nano scrollbar-inner employee_list"><div class="nano-content">';
		
		$query 			= "SELECT 
								e.id	 			employeeID,
								e.first_name 		employeeFirstName,
								e.last_name 		employeeLastName,
								es.service_id 		serviceID 
								
							FROM 			{$wpdb->prefix}bmify_employees e 
								INNER JOIN 	{$wpdb->prefix}bmify_employee_services es ON e.id = es.employee_id ORDER BY e.first_name, e.last_name";
		$results 		= $wpdb->get_results( $query);
		
		$resultArray 	= array();
		foreach($results as $result){
			$resultArray[$result->employeeID]['id1'] 				= $result->employeeID;
			$resultArray[$result->employeeID]['id2'][] 				= $result->serviceID;
			$resultArray[$result->employeeID]['first_name']			= $result->employeeFirstName;
			$resultArray[$result->employeeID]['last_name']			= $result->employeeLastName;
		}
		
		$locationID 		= '';
		foreach($resultArray as $item){
			$serviceIDs 	= implode(',', $item['id2']);
			$categoryIDs 	= array();
			foreach($item['id2'] as $id2){
				$id2 		= esc_sql($id2);
				$query 		= "SELECT category_id FROM {$wpdb->prefix}bmify_services WHERE id=".$id2;
				$results 	= $wpdb->get_results( $query);
				foreach($results as $result){
					$categoryIDs[] = $result->category_id;
				}
			}
			$categoryIDs 		= array_unique($categoryIDs);
			$categoryIDsAsText 	= implode(',', $categoryIDs);
			
			$itemID1	= $item['id1'];
			$itemID1 	= esc_sql($itemID1);
			$query 		= "SELECT location_id FROM {$wpdb->prefix}bmify_employee_locations WHERE employee_id=".$itemID1;
			$results 	= $wpdb->get_results( $query);
			foreach($results as $result){
				$locationID = $result->location_id;
			}
			
			
			$output 	.= '<div data-employee-id="'.$item['id1'].'" data-service-id="'.$serviceIDs.'" data-category-id="'.$categoryIDsAsText.'" data-location-id="'.$locationID.'">'.$item['first_name'].' '.$item['last_name'].'</div>';
		}
		
		$output .= '</div></div>';
		return $output;
    }
	/**
     * Get Services' Categories as nano list.
	 * @since 1.0.0
     */
    public static function categoryListAsNano()
    {
        global $wpdb;
		
		$output = '<div class="nano scrollbar-inner category_list"><div class="nano-content">';
		
		$query 			= "SELECT 
								c.id	 			categoryID,
								c.title 			categoryTitle,
								s.id		 		serviceID 
								
							FROM 			{$wpdb->prefix}bmify_categories c 
								INNER JOIN 	{$wpdb->prefix}bmify_services s 	ON c.id = s.category_id ORDER BY c.title";
		$results = $wpdb->get_results( $query);
		
		$resultArray = array();
		foreach($results as $category){
			$resultArray[$category->categoryID]['id1'] 			= $category->categoryID;
			$resultArray[$category->categoryID]['id2'][] 		= $category->serviceID;
			$resultArray[$category->categoryID]['title']		= $category->categoryTitle;
		}
		
		
		foreach($resultArray as $item){
			$serviceIDs 			= implode(',', $item['id2']);
			$employeeIDs 			= array();
			foreach($item['id2'] as $id2){
				$id2 				= esc_sql($id2);
				$query 				= "SELECT employee_id FROM {$wpdb->prefix}bmify_employee_services WHERE service_id=".$id2;
				$results 			= $wpdb->get_results( $query);
				foreach($results as $result){
					$employeeIDs[] 	= $result->employee_id;
				}
			}
			$employeeIDs 			= array_unique($employeeIDs);
			$employeeIDsAsText 		= implode(',', $employeeIDs);
			if(!empty($employeeIDs)){
				$locationList = array();
				foreach($employeeIDs as $employeeID){
					$employeeID	= esc_sql($employeeID);
					$query 		= "SELECT location_id FROM {$wpdb->prefix}bmify_employee_locations WHERE employee_id=".$employeeID;
					$results 	= $wpdb->get_results( $query);
					foreach($results as $result){
						$locationList[] = $result->location_id;
					}
				}
				$locationList = array_unique($locationList);
				$locationList = implode(',', $locationList);
				$output 	.= '<div data-category-id="'.$item['id1'].'" data-service-id="'.$serviceIDs.'" data-employee-id="'.$employeeIDsAsText.'" data-location-id="'.$locationList.'">'.$item['title'].'</div>';
			}
			
		}
		
		$output .= '</div></div>';
		return $output;
    }
	/**
     * Get Customers Ids as string by Appointment ID.
	 * @since 1.0.0
     */
    public static function bookmifyCustomerIdsByAppointmentID( $id = NULL )
    {
        global $wpdb;
		$result = '';
		
		if($id == NULL || $id == ''){
			
		}else{
			$id			= esc_sql($id);
			$query 		= "SELECT customer_id FROM {$wpdb->prefix}bmify_customer_appointments WHERE appointment_id=".$id;
			$results 	= $wpdb->get_results( $query);
			foreach($results as $service){
				$result .= $service->customer_id;
				$result .= ',';
			}
			$result = rtrim($result,",");
		}
		return $result;
    }
	/**
     * Get Customers Ids as string by Appointment ID.
	 * @since 1.0.0
     */
    public static function peopleCountByAppointmentID( $id = NULL, $code = 'encode' )
    {
        global $wpdb;
		$result = array();
		
		if($id == NULL || $id == ''){
			
		}else{
			$id			= esc_sql($id);
			$query 		= "SELECT * FROM {$wpdb->prefix}bmify_customer_appointments WHERE appointment_id=".$id;
			$results 	= $wpdb->get_results( $query);
			foreach($results as $service){
				$result[$service->customer_id] = $service->number_of_people;
			}
		}
		
		$buffy = array(
				'name' => $result	
		);
		if($code == 'encode'){
			$buffy = htmlspecialchars(json_encode($buffy));
		}
		return $buffy;
    }
	
	
	/**
     * Get First Customer Name and other customers count
	 * @since 1.0.0
     */
	public static function firstCustomerNameOthersNumber($appointment_id){
		global $wpdb;
		
		$output = '';
		$assigned_locations_ids = array();
		$checked = '';
		
		$query 			= "SELECT * FROM {$wpdb->prefix}bmify_customers ORDER BY id, first_name";
		$locations 		= $wpdb->get_results( $query, OBJECT  );
		
		$appointment_id = esc_sql($appointment_id);
		$query 			= "SELECT * FROM {$wpdb->prefix}bmify_customer_appointments WHERE appointment_id=".$appointment_id;
		$group 			= $wpdb->get_results( $query, OBJECT  );
		
		foreach($group as $provider){
			$assigned_locations_ids[] = $provider->customer_id;
		}
		$count 	= '';
		$key 	= 0;
		$myKey 	= 0;
		$ofKey 	= 0;
		
		// experimental types: +3 or 3/4
		$type = 'of'; // plus
		foreach($locations as $location){
			$ofKey++;
			if(in_array( $location->id, $assigned_locations_ids )){$checked = 'checked';}
			
			if($checked == 'checked'){
				$key++;
				if($key == 1){
					$output .= '<span class="text">'.$location->first_name.' '.$location->last_name.'</span>';
				}else{
					$myKey++;
				}
			}
			$checked = '';
		}
		if($output == ''){
			$output = '';
		}else{
			if($myKey > 0){
				if($type == 'of'){
					$output .= '<span class="number">'.($myKey+1).' / '.($ofKey).'</span>';
				}else{
					$output .= '<span class="number">+'.($myKey).'</span>';
				}
			}
		}
			
		return $output;
	}
	
	/**
     * Get Added Customers List.
	 * @since 1.0.0
     */
	public static function addedCustomerList($appoinment_id = ''){
		global $wpdb;
		$output = '';
		
		if($appoinment_id != ''){
			$appoinment_id = esc_sql($appoinment_id);
			$query 		= "SELECT * FROM {$wpdb->prefix}bmify_customer_appointments WHERE appointment_id=".$appoinment_id;
			$customers 	= $wpdb->get_results( $query, OBJECT  );
			
			foreach($customers as $customer){
				
				$output .=     '<div class="item" data-customer-id="'.$customer->customer_id.'">
									<div class="name_holder">
										<span class="name">'.Helper::bookmifyGetCustomerCol($customer->customer_id, 'full_name').'</span>
										<span class="email">'.Helper::bookmifyGetCustomerCol($customer->customer_id, 'email').'</span>
									</div>
									<div class="detail_holder">
										<div class="status">
											'.self::statusList($customer->status).'
										</div>
										<div class="no_people">
											<span class="person_icon"><img class="bookmify_be_svg" src="'.BOOKMIFY_ASSETS_URL.'/img/add-user.svg" alt="" /></span>
											<span class="down_icon"><img class="bookmify_be_svg" src="'.BOOKMIFY_ASSETS_URL.'/img/down.svg" alt="" /></span>
											<input type="number" name="number_of_people" value="'.$customer->number_of_people.'" readonly  onfocus="this.blur()" />
										</div>
										<div class="info">
											<div class="f_tooltip"><span>?</span><div class="f_tooltip_content">'.esc_html__('This is the number of people who will come with this customer including the customer. This number varies depending on the selected service and employee.', 'bookmify').'</div></div>
										</div>
										<div class="remover">
											<span><span></span></span>
										</div>
									</div>
								</div>';

					
			}
		}
		return $output;
	}
	
	public static function statusList($status = 'pending'){
		global $wpdb;
		$approved_icon 		= '<img class="bookmify_be_svg" src="'.BOOKMIFY_ASSETS_URL.'/img/checked.svg" alt="" />';
		$pending_icon 		= '<img class="bookmify_be_svg" src="'.BOOKMIFY_ASSETS_URL.'/img/circle.svg" alt="" />';
		$canceled_icon 		= '<img class="bookmify_be_svg" src="'.BOOKMIFY_ASSETS_URL.'/img/cancel.svg" alt="" />';
		$rejected_icon 		= '<img class="bookmify_be_svg" src="'.BOOKMIFY_ASSETS_URL.'/img/cancel.svg" alt="" />';
		switch($status){
			case 'approved':
				$status2 	= $approved_icon;
				break;
			case 'pending':
				$status2 	= $pending_icon;
				break;
			case 'canceled':
				$status2 	= $canceled_icon;
				break;
			case 'rejected':
				$status2 	= $rejected_icon;
				break;
		}
		$output  = '<div class="bookmify_be_ministatus">';
		$output .= '<div class="label"><label class="'.$status.'">'.$status2.'</label></div>';
		$output .= '<div class="ministatus_dd">
						<div class="approved"><span>'.$approved_icon.'</span></div>
						<div class="pending"><span>'.$pending_icon.'</span></div>
						<div class="canceled"><span>'.$canceled_icon.'</span></div>
						<div class="rejected"><span>'.$rejected_icon.'</span></div>
					</div></div>';
		
		
		return $output;
	}
	
	public static function numberListForPeople(){
		global $wpdb;
		
		$output = '<div class="nano scrollbar-inner number_list"><div class="nano-content">';
		$output .= '<div class="nodata">'.esc_html__('Nothing to choose', 'bookmify').'</div>';
		$output .= '</div></div>';
		return $output;
	}
	public static function customerListAsNano(){
		global $wpdb;
		
		$output = '<div class="nano scrollbar-inner app_customers" data-id=""><div class="nano-content">';
		$output .= self::customerListWithCheckboxes();
		$output .= '</div></div>';
		return $output;
	}
	public static function customerListWithCheckboxes(){
		global $wpdb;
		
		$query 		= "SELECT first_name,last_name,email,id FROM {$wpdb->prefix}bmify_customers ORDER BY first_name,last_name,id";
		$results 	= $wpdb->get_results( $query, OBJECT  );

		$output 	= '<ul class="customers_nano_list">';

		foreach($results as $result){
			$firstName 	= $result->first_name;
			$lastName 	= $result->last_name;
			$email 		= $result->email;
			$ID 		= $result->id;
			$output .= '<li>
							<div class="item">
								<label>
									<input type="checkbox" name="customer_id" class="bookmify_be_check_item" value="'.$ID.'">
									<input type="hidden" name="full_name" value="'.$firstName.' '.$lastName.'">
									<input type="hidden" name="email" value="'.$email.'">
									<span class="checkmark">
										<img class="bookmify_be_svg" src="'.BOOKMIFY_ASSETS_URL.'img/checked.svg" alt="" />
									</span>
								</label>
								<span class="name">'.$firstName.' '.$lastName.'</span>
							</div>
						</li>';
		}

		$output .= '</ul>';

		
		return $output;
	}
	public static function getServiceExtraSingle($serviceID,$employeeID){
		global $wpdb;
		$serviceID 	 = esc_sql($serviceID);
		$query 		 = "SELECT * FROM {$wpdb->prefix}bmify_extra_services WHERE service_id=".$serviceID." ORDER BY position, title";
		$results 	 = $wpdb->get_results( $query);
		$html 		 = '';
		if(!empty($results)){
			$html  	.= '<div class="bookmify_be_extras_customer_appointment item_approved">';
			$html	.= '<input type="hidden" value="'.self::taxOfService($serviceID).'" class="tax_h_customer" />';
			$html  	.= '<div class="name_holder">';
			$html  	.= '<span class="name"></span>';
			$html 	.= '<span class="email"></span>';
			$html  	.= '</div>';
			foreach($results as $result){
				$ID 			= $result->id;
				$price 			= $result->price;
				$capacityMax 	= $result->capacity_max;
				$position 		= $result->position;
				$duration 		= $result->duration;
				$title 			= $result->title;
				$html 		   .= '<div class="bookmify_extra_service_item_app" data-id="'.$ID.'">
									<span class="bookmify_be_checkbox">
										<input class="req" type="checkbox">
										<span class="checkmark">
											<img class="bookmify_be_svg" src="'.BOOKMIFY_ASSETS_URL.'/img/checked.svg" alt="" />
										</span>
									</span>
									<div class="info_holder">
										<div class="title_holder">
											<input type="hidden" class="extra_duration" value="'.$result->duration.'" />
											<label>'.$result->title.'</label>
										</div>
										<div class="price_holder">
											<div class="bookmify_be_quantity disabled">
												<input type="number" min="1" max="'.$capacityMax.'" name="extra_quantity" value="1" readonly />
												<span class="increase"><span></span></span>
												<span class="decrease"><span></span></span>
											</div>
											<div class="price">
												<input type="hidden" name="extra_price" value="'.$price.'" />
												<span class="price_span">'.Helper::bookmifyPriceCorrection($price).'</span>
											</div>
										</div>
									</div>	
								</div>';
			}
			$serviceID 	 		= esc_sql($serviceID);
			$employeeID 	 	= esc_sql($employeeID);
			$query 				= "SELECT price FROM {$wpdb->prefix}bmify_employee_services WHERE service_id=".$serviceID." AND employee_id=".$employeeID;
			$results 			= $wpdb->get_results( $query);
			$servicePrice 		= 0;
			foreach($results as $result){
				$servicePrice 	= $result->price;
			}

			$html .= '<div class="sub_total">
						<input type="hidden" name="subtotal_value" value="0" />
						<input type="hidden" name="service_price_for_customer" value="'.$servicePrice.'" />
						<input type="hidden" name="people_count" value="1" />
						<span class="sub_text">'.esc_html__('Subtotal:', 'bookmify').'</span>
						<span class="sub_price">'.Helper::bookmifyPriceCorrection($servicePrice).'</span>
					</div>';
			$html  	   .= '</div>';
		}
		return $html;
	}
	public static function getServiceExtraTotal($serviceID,$employeeID){
		global $wpdb;
		$serviceID 	 = esc_sql($serviceID);
		$query 		 = "SELECT * FROM {$wpdb->prefix}bmify_extra_services WHERE service_id=".$serviceID." ORDER BY position, title";
		$results 	 = $wpdb->get_results( $query);
		$html 		 = '';
		if(!empty($results)){
			$html .= '<div class="total">';

			$html .= '<div class="service_price">';
			$html .= '<div class="total_text">'.esc_html__('Service Price:', 'bookmify').'</div>';
			$html .= '<div class="price">';
			$html .= '<span>'.Helper::bookmifyPriceCorrection(0).'</span>';
			$html .= '<input type="hidden" name="total_service" value="0" />';
			$html .= '</div>';
			$html .= '</div>';

			$html .= '<div class="extra_price">';
			$html .= '<div class="total_text">'.esc_html__('Extras Price:', 'bookmify').'</div>';
			$html .= '<div class="price">';
			$html .= '<span>'.Helper::bookmifyPriceCorrection(0).'</span>';
			$html .= '<input type="hidden" name="total_extra" value="0" />';
			$html .= '</div>';
			$html .= '</div>';

			$html .= '<div class="service_tax_price">';
			$html .= '<div class="total_text">'.esc_html__('Service Tax:', 'bookmify').'</div>';
			$html .= '<div class="price">';
			$html .= '<span>'.Helper::bookmifyPriceCorrection(0).'</span>';
			$html .= '<input type="hidden" name="total_service_tax" value="0" />';
			$html .= '</div>';
			$html .= '</div>';

			$html .= '<div class="extra_tax_price">';
			$html .= '<div class="total_text">'.esc_html__('Extras Tax:', 'bookmify').'</div>';
			$html .= '<div class="price">';
			$html .= '<span>'.Helper::bookmifyPriceCorrection(0).'</span>';
			$html .= '<input type="hidden" name="total_extra_tax" value="0" />';
			$html .= '</div>';
			$html .= '</div>';

			$html .= '<div class="total_price">';
			$html .= '<div class="total_text">'.esc_html__('Total Price:', 'bookmify').'</div>';
			$html .= '<div class="price">';
			$html .= '<span>'.Helper::bookmifyPriceCorrection(0).'</span>';
			$html .= '<input type="hidden" name="total_price" value="0" />';
			$html .= '</div>';
			$html .= '</div>';

			$html .= '</div>';
		}
			
		return $html;
	}
	public static function getPaymentSingle($serviceID, $employeeID){
		global $wpdb;
		$html  			= '';
		$createdDay 	= '<span>'.date_i18n(get_option('bookmify_be_date_format', 'd F, Y')).'</span>';
		$price			= 0;
		$taxService		= self::taxOfService($serviceID);
		if($serviceID != '' && $employeeID != ''){
			$serviceID 	= esc_sql($serviceID);
			$employeeID = esc_sql($employeeID);
			$query 		= "SELECT price FROM {$wpdb->prefix}bmify_employee_services WHERE service_id=".$serviceID." AND employee_id=".$employeeID;
			$results 	= $wpdb->get_results( $query);
			foreach($results as $result){
				$price = $result->price;
			}
		}
		$html .= '<div class="bookmify_be_extras_customer_appointment item_approved">';
		$html .= '<input type="hidden" value="'.self::taxOfService($serviceID).'" class="tax_h_customer" />';
		$html .= '<div class="name_holder">';
		$html .= '<span class="name"></span>';
		$html .= '<span class="email"></span>';
		$html .= '</div>';
		$html .= '<div class="payment_date">
					<input type="hidden" name="payment_date" value="" />
					<span class="date_text">'.esc_html__('Date:', 'bookmify').'</span>
					<span class="date_span">'.$createdDay.'</span>
				  </div>';
		
		$html .= '<div class="payment_method">
					<input type="hidden" name="payment_method" value="" />
					<span class="method_text">'.esc_html__('Payment Method:', 'bookmify').'</span>
					<span class="method_span">'.esc_html__('Local', 'bookmify').'</span>
				  </div>';
		
		$html .= '<div class="extra_total">
					<input type="hidden" name="extra_total" value="0" />
					<span class="sub_text">'.esc_html__('Extras Price:', 'bookmify').'</span>
					<span class="extra_price">'.Helper::bookmifyPriceCorrection(0).'</span>
				  </div>';
		
		$html .= '<div class="service_total">
					<input type="hidden" name="service_total" value="'.$price.'" />
					<span class="sub_text">'.esc_html__('Service Price:', 'bookmify').'</span>
					<span class="service_price">'.Helper::bookmifyPriceCorrection($price).'</span>
				  </div>';
		
		$html .= '<div class="extra_tax_total">
					<input type="hidden" name="extra_tax_total" value="0" />
					<span class="sub_text">'.esc_html__('Extras Tax:', 'bookmify').'</span>
					<span class="extra_tax_price">'.Helper::bookmifyPriceCorrection(0).'</span>
				  </div>';
		
		$html .= '<div class="service_tax_total">
					<input type="hidden" name="service_tax_total" value="'.($price*$taxService/100).'" />
					<span class="sub_text">'.esc_html__('Service Tax:', 'bookmify').'</span>
					<span class="service_tax_price">'.Helper::bookmifyPriceCorrection($price*$taxService/100).'</span>
				  </div>';
		
		$html .= '<div class="sub_total">
					<input type="hidden" name="sub_total" value="'.($price*($taxService+100)/100).'" />
					<input type="hidden" name="service_price_for_customer" value="'.$price.'" />
					<input type="hidden" name="people_count" value="1" />
					<span class="sub_text">'.esc_html__('Total:', 'bookmify').'</span>
					<span class="sub_price">'.Helper::bookmifyPriceCorrection(($price*($taxService+100)/100)).'</span>
				  </div>';
		
		$html  .= '</div>';
		return $html;
	}
	public static function getHiddenValues($serviceID,$employeeID){
		global $wpdb;
		
		$html  				= '';
		$serviceID 			= esc_sql($serviceID);
		$query 				= "SELECT duration FROM {$wpdb->prefix}bmify_services WHERE id=".$serviceID;
		$results 			= $wpdb->get_results( $query);
		if(!empty($results)){
			$html 		   .= '<input type="hidden" class="service_duration" value="'.$results[0]->duration.'" />';
		}
		$serviceID 			= esc_sql($serviceID);
		$employeeID 		= esc_sql($employeeID);
		$query 				= "SELECT capacity_min,capacity_max FROM {$wpdb->prefix}bmify_employee_services WHERE service_id=".$serviceID." AND employee_id=".$employeeID;
		$results 			= $wpdb->get_results( $query);
		if(!empty($results)){
			$html 		   .= '<input type="hidden" class="service_min" value="'.$results[0]->capacity_min.'" />';
			$html 		   .= '<input type="hidden" class="service_max" value="'.$results[0]->capacity_max.'" />';
		}
		return $html;
	}
	public static function getHiddenValuesAgain($serviceID,$employeeID,$value = "duration"){
		global $wpdb;
		
		$result  			= '';
		if($value == "duration"){
			$serviceID 			= esc_sql($serviceID);
			$query 				= "SELECT duration FROM {$wpdb->prefix}bmify_services WHERE id=".$serviceID;
			$results 			= $wpdb->get_results( $query);
			if(!empty($results)){
				$result		   	= $results[0]->duration;
			}
		}else{
			$serviceID 			= esc_sql($serviceID);
			$employeeID 		= esc_sql($employeeID);
			$query 				= "SELECT capacity_min,capacity_max FROM {$wpdb->prefix}bmify_employee_services WHERE service_id=".$serviceID." AND employee_id=".$employeeID;
			$results 			= $wpdb->get_results( $query);
			if(!empty($results)){
				if($value == "min"){
					$result 	= $results[0]->capacity_min;
				}else{
					$result 	= $results[0]->capacity_max;
				}
			}
		}
			
			
		return $result;
	}
	public static function getPaymentAgain($customerIDs,$serviceID,$employeeID,$customerData){
		global $wpdb;
		
		$output = '';
		$list 	= '';
		if($customerIDs == '' || $serviceID == '' || $employeeID == ''){
			$output .= '<div class="bookmify_be_infobox danger"><label>';	
			$output .= esc_html__('Select customer, employee and service', 'bookmify');	
			$output .= '</label></div>';
		}else{
			$extra 		= 'extra';
			$serviceID 	= esc_sql($serviceID);
			$count		= $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->prefix}bmify_extra_services WHERE service_id=".$serviceID );
			if($count == 0){
				$extra = '';
			}
			$list   .= '<div class="bookmify_be_customer_payments_list">';
			// получить все необходимые данные если, экстра сервисы существуют для выбранного сервиса
			foreach($customerData as $customerID){
				$list  .= '<div class="bookmify_be_extras_customer_appointment item_'.$customerID[2].'" data-customer-id="'.$customerID[0].'">';
				$list  .= '<input type="hidden" value="'.self::taxOfService($serviceID).'" class="tax_h_customer" />';
				$list  .= '<div class="name_holder">';
				$list  .= '<span class="name">'.Helper::bookmifyGetCustomerCol($customerID[0], 'full_name').'</span>';
				$list  .= '<span class="email">'.Helper::bookmifyGetCustomerCol($customerID[0], 'email').'</span>';
				$list  .= '</div>';
				$list  .= self::paymentForCustomerAgain($customerID[0],$serviceID,$employeeID,$extra,$customerID[1]);
				$list  .= '</div>';
			}
			$list   .= '</div>';
			$output .= $list;
		}
		return $output;
	}
	public static function paymentForCustomerAgain($customerID,$serviceID,$employeeID,$extra,$peopleCount){
		global $wpdb;
		$extraTotal 	= 0;
		$list			= '';
		
		// цена на сервис
		$serviceTotal 	= 0;
		$servicePrice 	= 0;
		
		$taxService		= self::taxOfService($serviceID);
		
		$serviceID 		= esc_sql($serviceID);
		$employeeID 	= esc_sql($employeeID);
		$query 			= "SELECT price,capacity_max FROM {$wpdb->prefix}bmify_employee_services WHERE service_id=".$serviceID." AND employee_id=".$employeeID;
		$results 		= $wpdb->get_results( $query);
		foreach($results as $result){
			$servicePrice 	= $result->price;
			$max 			= $result->capacity_max;
		}
		if($peopleCount > $max){
			$peopleCount	= $max;
		}
		$serviceTotal 	= $servicePrice * $peopleCount;
		$createdDay 	= '<span>'.date_i18n(get_option('bookmify_be_date_format', 'd F, Y')).'</span>';
		$paidType		= esc_html__('Local', 'bookmify');
		$list 		   .= '<div class="payment_date">
							<input type="hidden" name="payment_date" value="" />
							<span class="date_text">'.esc_html__('Date:', 'bookmify').'</span>
							<span class="date_span">'.$createdDay.'</span>
						   </div>';
		
		$list 		   .= '<div class="payment_method">
							<input type="hidden" name="payment_method" value="" />
							<span class="method_text">'.esc_html__('Payment Method:', 'bookmify').'</span>
							<span class="method_span">'.$paidType.'</span>
						   </div>';
		
		$list 		   .= '<div class="extra_total">
							<input type="hidden" name="extra_total" value="'.$extraTotal.'" />
							<span class="sub_text">'.esc_html__('Extras Price:', 'bookmify').'</span>
							<span class="extra_price">'.Helper::bookmifyPriceCorrection($extraTotal).'</span>
						   </div>';
		
		$list 		   .= '<div class="service_total">
							<input type="hidden" name="service_total" value="'.$serviceTotal.'" />
							<span class="sub_text">'.esc_html__('Service Price:', 'bookmify').'</span>
							<span class="service_price">'.Helper::bookmifyPriceCorrection($serviceTotal).'</span>
						   </div>';
		
		$list 		   .= '<div class="extra_tax_total">
							<input type="hidden" name="extra_tax_total" value="'.$extraTotal.'" />
							<span class="sub_text">'.esc_html__('Extras Tax:', 'bookmify').'</span>
							<span class="extra_tax_price">'.Helper::bookmifyPriceCorrection($extraTotal).'</span>
						   </div>';
		
		$list 		   .= '<div class="service_tax_total">
							<input type="hidden" name="service_tax_total" value="'.($serviceTotal*($taxService/100)).'" />
							<span class="sub_text">'.esc_html__('Service Tax:', 'bookmify').'</span>
							<span class="service_tax_price">'.Helper::bookmifyPriceCorrection(($serviceTotal*($taxService/100))).'</span>
						   </div>';
		
		$subTotal 		= $extraTotal + $serviceTotal + ($serviceTotal*($taxService/100));
		
		$list 		   .= '<div class="sub_total">
							<input type="hidden" name="sub_total" value="'.$subTotal.'" />
							<input type="hidden" name="service_price_for_customer" value="'.$servicePrice.'" />
							<input type="hidden" name="people_count" value="'.$peopleCount.'" />
							<span class="sub_text">'.esc_html__('Total:', 'bookmify').'</span>
							<span class="sub_price">'.Helper::bookmifyPriceCorrection($subTotal).'</span>
						   </div>';
		
		return $list;
	}
	public static function getExtrasAgain($customerIDs,$serviceID,$employeeID,$customerData){
		global $wpdb;
		$output = '';
		$list 	= '';
		if($customerIDs == '' || $serviceID == '' || $employeeID == ''){
			$output .= '<div class="bookmify_be_infobox danger"><label>';	
			$output .= esc_html__('Select customer, employee and service', 'bookmify');	
			$output .= '</label></div>';
		}else{
			$serviceID 	= esc_sql($serviceID);
			$count		= $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->prefix}bmify_extra_services WHERE service_id=".$serviceID );
			if($count == 0){
				// уведомление, если не имеются экстра сервисы у выбранного сервиса
				$output .= '<div class="bookmify_be_infobox"><label>';	
				$output .= esc_html__('This service doesn\'t have extra services', 'bookmify');	
				$output .= '</label></div>';
			}else{
				$peopleTotalCount = 0;
				$list   .= '<div class="bookmify_be_customer_extras_list">';
				// получить все необходимые данные если, экстра сервисы существуют для выбранного сервиса
				foreach($customerData as $customerID){
					$list  .= '<div class="bookmify_be_extras_customer_appointment item_'.$customerID[2].'" data-customer-id="'.$customerID[0].'">';
					$list  .= '<input type="hidden" value="'.self::taxOfService($serviceID).'" class="tax_h_customer" />';
					$list  .= '<div class="name_holder">';
					$list  .= '<span class="name">'.Helper::bookmifyGetCustomerCol($customerID[0], 'full_name').'</span>';
					$list  .= '<span class="email">'.Helper::bookmifyGetCustomerCol($customerID[0], 'email').'</span>';
					$list  .= '</div>';
					$list  .= self::extrasForCustomerAgain($customerID[0],$serviceID,$employeeID,$customerID[1]);
					$list  .= '</div>';
					$peopleTotalCount += $customerID[1];
				}
				$list   .= '</div>';
				$output .= $list;
				
				$output .= '<div class="total">';
				
				$output .= '<div class="service_price">';
				$output .= '<div class="total_text">'.esc_html__('Service Price:', 'bookmify').'</div>';
				$output .= '<div class="price">'.self::totalServicePriceForCustomerAgain($serviceID,$employeeID,'service',$peopleTotalCount).'</div>';
				$output .= '</div>';
				
				$output .= '<div class="extra_price">';
				$output .= '<div class="total_text">'.esc_html__('Extras Price:', 'bookmify').'</div>';
				$output .= '<div class="price">'.self::totalServicePriceForCustomerAgain($serviceID,$employeeID,'extra',$peopleTotalCount).'</div>';
				$output .= '</div>';
				
				$output .= '<div class="service_tax_price">';
				$output .= '<div class="total_text">'.esc_html__('Service Tax:', 'bookmify').'</div>';
				$output .= '<div class="price">'.self::totalServicePriceForCustomerAgain($serviceID,$employeeID,'service_tax',$peopleTotalCount).'</div>';
				$output .= '</div>';
				
				$output .= '<div class="extra_tax_price">';
				$output .= '<div class="total_text">'.esc_html__('Extras Tax:', 'bookmify').'</div>';
				$output .= '<div class="price">'.self::totalServicePriceForCustomerAgain($serviceID,$employeeID,'extra_tax',$peopleTotalCount).'</div>';
				$output .= '</div>';
				
				$output .= '<div class="total_price">';
				$output .= '<div class="total_text">'.esc_html__('Total Price:', 'bookmify').'</div>';
				$output .= '<div class="price">'.self::totalServicePriceForCustomerAgain($serviceID,$employeeID,'total',$peopleTotalCount).'</div>';
				$output .= '</div>';
				
				$output .= '</div>';
				
			}
		}
		return $output;
	}
	public static function extrasForCustomerAgain($customerID,$serviceID,$employeeID,$peopleCount){
		global $wpdb;
		$list		= '';
		$serviceID 	= esc_sql($serviceID);
		$query 		= "SELECT * FROM {$wpdb->prefix}bmify_extra_services WHERE service_id=".$serviceID." ORDER BY position, title";
		$results 	= $wpdb->get_results( $query);
		$subtotal 	= 0;
		foreach($results as $result){
			$disabled 	= 'disabled';
			$checked 	= '';
			$price 		= $result->price;
			$quantity 	= 1;
			$new_price 	= $price * $quantity;
			$list .=   '<div class="bookmify_extra_service_item_app" data-id="'.$result->id.'">
							<span class="bookmify_be_checkbox">
								<input class="req" type="checkbox" '.$checked.'>
								<span class="checkmark">
									<img class="bookmify_be_svg" src="'.BOOKMIFY_ASSETS_URL.'/img/checked.svg" alt="" />
								</span>
							</span>
							<div class="info_holder">
								<div class="title_holder">
									<input type="hidden" class="extra_duration" value="'.$result->duration.'" />
									<label>'.$result->title.'</label>
								</div>
								<div class="price_holder">
									<div class="bookmify_be_quantity '.$disabled.'">
										<input type="number" min="1" max="'.$result->capacity_max.'" name="extra_quantity" value="'.$quantity.'" readonly />
										<span class="increase"><span></span></span>
										<span class="decrease"><span></span></span>
									</div>
									<div class="price">
										<input type="hidden" name="extra_price" value="'.$price.'" />
										<span class="price_span">'.Helper::bookmifyPriceCorrection($new_price).'</span>
									</div>
								</div>
							</div>	
						</div>';
		}
		$serviceTotal 	= 0;
		$servicePrice 	= 0;
		$serviceID 		= esc_sql($serviceID);
		$employeeID 	= esc_sql($employeeID);
		$query 			= "SELECT price FROM {$wpdb->prefix}bmify_employee_services WHERE service_id=".$serviceID." AND employee_id=".$employeeID;
		$results 		= $wpdb->get_results( $query);
		foreach($results as $result){
			$servicePrice = $result->price;
		}
		
		$serviceTotal 	= $servicePrice;
		
		$list .= '<div class="sub_total">
					<input type="hidden" name="subtotal_value" value="'.$subtotal.'" />
					<input type="hidden" name="service_price_for_customer" value="'.$serviceTotal.'" />
					<input type="hidden" name="people_count" value="'.$peopleCount.'" />
					<span class="sub_text">'.esc_html__('Subtotal:', 'bookmify').'</span>
					<span class="sub_price">'.Helper::bookmifyPriceCorrection($subtotal).'</span>
				</div>';
		
		
		return $list;
	}
	public static function totalServicePriceForCustomerAgain($serviceID,$employeeID,$total,$peopleTotalCount){
		global $wpdb;
		
		$taxService		= self::taxOfService($serviceID);
		
		// итоговая сумма на выбранный СЕРВИС
		if($total != 'extra' || $total == 'service_tax'){
			$serviceTotal = 0;
			$servicePrice = 0;
			
			$employeeID = esc_sql($employeeID);
			$serviceID 	= esc_sql($serviceID);
			$query 		= "SELECT price FROM {$wpdb->prefix}bmify_employee_services WHERE service_id=".$serviceID." AND employee_id=".$employeeID;
			$results 	= $wpdb->get_results( $query);
			foreach($results as $result){
				$servicePrice = $result->price;
			}
			
			$serviceTotal += ($peopleTotalCount*$servicePrice);
		}
		
		// итоговая сумма на выбранные ЭКСТРА СЕРВИСЫ
		if($total != 'service' || $total == 'extra_tax'){
			$extraTotal	= 0;
		}
		
		// вывод
		$output = '';
		if($total == 'total'){
			$totalPrice 	= $serviceTotal + $extraTotal + $extraTotal*($taxService/100) + $serviceTotal*($taxService/100);
			$inputHidden 	= '<input type="hidden" name="total_price" value="'.$totalPrice.'" />';
			$totalPrice	 	= '<span>'.Helper::bookmifyPriceCorrection($totalPrice).'</span>';
			$output 		= $totalPrice.$inputHidden;
		}else if($total == 'service'){
			$inputHidden 	= '<input type="hidden" name="total_service" value="'.$serviceTotal.'" />';
			$serviceTotal 	= '<span>'.Helper::bookmifyPriceCorrection($serviceTotal).'</span>';
			$output 		= $serviceTotal.$inputHidden;
		}else if($total == 'extra'){
			$inputHidden 	= '<input type="hidden" name="total_extra" value="'.$extraTotal.'" />';
			$extraTotal 	= '<span>'.Helper::bookmifyPriceCorrection($extraTotal).'</span>';
			$output 		= $extraTotal.$inputHidden;
		}else if($total == 'extra_tax'){
			$inputHidden 	= '<input type="hidden" name="total_extra_tax" value="'.($extraTotal*($taxService/100)).'" />';
			$extraTotal 	= '<span>'.Helper::bookmifyPriceCorrection(($extraTotal*($taxService/100))).'</span>';
			$output 		= $extraTotal.$inputHidden;
		}else if($total == 'service_tax'){
			$inputHidden 	= '<input type="hidden" name="total_service_tax" value="'.($serviceTotal*($taxService/100)).'" />';
			$serviceTotal 	= '<span>'.Helper::bookmifyPriceCorrection(($serviceTotal*($taxService/100))).'</span>';
			$output 		= $serviceTotal.$inputHidden;
		}
		return $output;
	}
	public static function detailsTabContent($appointmentID = '', $status = ''){
		global $wpdb;
		if($appointmentID != ''){
			$appointmentID 	= esc_sql($appointmentID);
			$query 			= "SELECT * FROM {$wpdb->prefix}bmify_appointments WHERE id=".$appointmentID;
			$appointments 	= $wpdb->get_results( $query, OBJECT  );
			foreach($appointments as $appointment){
				// FOR customers list popup
				$customer_ids 			= self::bookmifyCustomerIdsByAppointmentID($appointmentID);
				$customer_list 			= self::firstCustomerNameOthersNumber($appointmentID);
				$customerPlaceholder 	= '';
				if($customer_list == ''){
					$customerPlaceholder = esc_attr__('Select from Existing Customers','bookmify');
				}

				// FOR category and service section
				$serviceID			 	= $appointment->service_id;
				$serviceValue			= Helper::titleDecryption(Helper::bookmifyGetServiceCol($serviceID));
				$categoryID				= Helper::bookmifyGetServiceCol($serviceID, 'category_id');
				$categoryValue			= Helper::bookmifyGetCategoryCol($categoryID);

				// FOR employee and location section
				$employeeID				= $appointment->employee_id;
				$employeeValue			= Helper::bookmifyGetEmployeeCol($employeeID);
				$locationID				= Helper::getLocationDataByEmployeeID($employeeID, 'id');
				$locationValue			= '';
				if($locationID != ''){$locationValue = Helper::bookmifyGetLocationCol($locationID);}


				// FOR date and time section
				$date					= date('Y-m-d', strtotime($appointment->start_date));
				$time					= date('H:i', strtotime($appointment->start_date));
				$startDate				= $appointment->start_date;

				// FOR info section
				$info					= $appointment->info;
			}
		}else{
			$customerPlaceholder 		= esc_attr__('Select from Existing Customers','bookmify');
			$customer_ids			 	= '';
			$customer_list			 	= '';
			$serviceID				 	= '';
			$employeeID				 	= '';
			$serviceValue				= '';
			$categoryID				 	= '';
			$categoryValue				= '';
			$employeeValue				= '';
			$locationID				 	= '';
			$locationValue				= '';
			$date				 		= '';
			$time				 		= '';
			$info				 		= '';
			$startDate					= '';
		}
			
		$html = '<div class="bookmify_be_appointment_details">
				
					<div class="bookmify_be_ad_customers">
						'.self::detailsCustomer($appointmentID,$customer_ids,$customer_list,$customerPlaceholder,$serviceID,$employeeID).'
					</div>

					<div class="bookmify_be_ad_cat_ser">
						'.self::detailsCategoryAndService($serviceID,$serviceValue,$categoryID,$categoryValue).'
					</div>

					<div class="bookmify_be_ad_emp_loc">
						'.self::detailsEmployeeAndLocation($employeeID,$employeeValue,$locationID,$locationValue).'
					</div>

					<div class="bookmify_be_ad_date_time">
						'.self::detailsDayAndTime($appointmentID,$date,$time,$status,$startDate).'
					</div>

					<div class="bookmify_be_ad_info">
						'.self::detailsInfo($appointmentID, $info).'
					</div>

				 </div>';
		return $html;
	}
	public static function detailsCustomer($appointmentID = '',$customer_ids = '',$customer_list = '',$customerPlaceholder = '',$serviceID = '',$employeeID = ''){
		$html = self::customersInfo($serviceID,$employeeID).'
				
		
				<div class="ad_customers_list">
					'.self::customersListPopup($appointmentID,$customer_ids,$customer_list,$customerPlaceholder).'
				</div>
				<div class="ad_customer_list_with_info">
					'.self::addedCustomerList($appointmentID).'
				</div>';
		return $html;
	}
	public static function customersListPopup($appointmentID = '',$customer_ids = '',$customer_list = '',$customerPlaceholder = ''){
		$html = '<div class="customer">
					<label><span class="title">'.esc_html__('Customers', 'bookmify').'<span>*</span></span></label>
					<input type="text" name="customer" data-placeholder="'.esc_attr__('Select from Existing Customers','bookmify').'" placeholder="'.$customerPlaceholder.'" readonly />
					<input class="required_field" type="hidden" name="customer_ids" value="'.$customer_ids.'">
					<span class="bot_btn"><span></span><img class="bookmify_be_svg" src="'.BOOKMIFY_ASSETS_URL.'img/down.svg" alt="" /></span>
					<div class="bookmify_be_new_value">'.$customer_list.'</div>
				</div>
				<div class="notify">
					<span class="bookmify_be_checkbox">
						<input class="req" type="checkbox" id="notify_checkbox_'.$appointmentID.'" checked="checked">
						<span class="checkmark">
							<img class="bookmify_be_svg" src="'.BOOKMIFY_ASSETS_URL.'/img/checked.svg" alt="" />
						</span>
					</span>
					<label for="notify_checkbox_'.$appointmentID.'">'.esc_html__('Notify the Customer(s)', 'bookmify').'<div class="f_tooltip"><span>?</span><div class="f_tooltip_content">'.esc_html__('By ticking you indicate to send notifications to all customers about this appointment.', 'bookmify').'</div></div></label>
				</div>';
		return $html;
	}
	public static function customersInfo($serviceID = '',$employeeID = ''){
		global $wpdb;
		$list 			= '';
		$class 			= '';
		if (is_numeric($serviceID) && is_numeric($employeeID) ) {
			$serviceID 	= esc_sql($serviceID);
			$employeeID = esc_sql($employeeID);
			$query 		= "SELECT * FROM {$wpdb->prefix}bmify_employee_services WHERE service_id=".$serviceID." AND employee_id=".$employeeID;
			$results 	= $wpdb->get_results( $query);
			foreach($results as $result){
				$list  .= '<label>'.esc_html__('Minimum Capacity:', 'bookmify').'</label>';
				$list  .= '<span class="min">'.$result->capacity_min.'.</span>';
				$list  .= '<label>'.esc_html__('Maximum Capacity:', 'bookmify').'</label>';
				$list  .= '<span class="max">'.$result->capacity_max.'.</span>';
			}
		}
		if($list == ''){
			$list 	   .= '<label>'.esc_html__('Choose Service and Employee!', 'bookmify').'</label>';
			$class		= 'danger';
		}
		$html = '<div class="ad_customers_info bookmify_be_infobox '.$class.'">';
		$html .= $list;
		
		$html .= '</div>';
		return $html;
	}
	public static function detailsCategoryAndService($serviceID = '',$serviceValue = '',$categoryID = '',$categoryValue = ''){
		$html = '<div class="category_wrap">
					<label>
						<span class="title">'.esc_html__('Category','bookmify').'</span>
					</label>
					<input type="text" name="category" placeholder="'.esc_attr__('Select from Categories','bookmify').'" readonly value="'.$categoryValue.'">
					<input type="hidden" name="category_id" value="'.$categoryID.'">
					<span class="bot_btn"><span></span><img class="bookmify_be_svg" src="'.BOOKMIFY_ASSETS_URL.'img/down.svg" alt="" /></span>
				</div>
				<div class="service_wrap">
					<label>
						<span class="title">'.esc_html__('Service','bookmify').'<span>*</span></span>
					</label>
					<input type="text" name="service" placeholder="'.esc_attr__('Select from Services','bookmify').'" readonly value="'.$serviceValue.'">
					<input class="required_field" type="hidden" name="service_id" value="'.$serviceID.'">
					<span class="bot_btn"><span></span><img class="bookmify_be_svg" src="'.BOOKMIFY_ASSETS_URL.'img/down.svg" alt="" /></span>
				</div>';
		return $html;
	}
	public static function detailsEmployeeAndLocation($employeeID = '', $employeeValue = '', $locationID = '', $locationValue = ''){
		$html = '<div class="employee_wrap">
					<label>
						<span class="title">'.esc_html__('Employee','bookmify').'<span>*</span></span>
					</label>
					<input type="text" name="employee" placeholder="'.esc_attr__('Select from Employees','bookmify').'" readonly value="'.$employeeValue.'">
					<input class="required_field" type="hidden" name="employee_id" value="'.$employeeID.'">
					<span class="bot_btn"><span></span><img class="bookmify_be_svg" src="'.BOOKMIFY_ASSETS_URL.'img/down.svg" alt="" /></span>
				</div>
				<div class="location_wrap">
					<label>
						<span class="title">'.esc_html__('Location','bookmify').'</span>
					</label>
					<input type="text" name="location" placeholder="'.esc_attr__('Select from Locations','bookmify').'" readonly value="'.$locationValue.'">
					<input type="hidden" name="location_id" value="'.$locationID.'">
					<span class="bot_btn"><span></span><img class="bookmify_be_svg" src="'.BOOKMIFY_ASSETS_URL.'img/down.svg" alt="" /></span>
				</div>';
		return $html;
	}
	public static function detailsDayAndTime($appointmentID = '', $date = '', $time = '', $status = '', $startDate = ''){
		switch($status){
			case 'canceled':
			case 'rejected': $time = '';break;
		}
		$appointmentStartDate 	= date("Y-m-d H:i:s", strtotime($startDate));
		$today					= HelperTime::getCurrentDateTime();
		if($appointmentStartDate <= $today && (get_option('bookmify_be_old_appointment_action', '') == 'on')){ // if closed item do nothing
			$time = '';
		}
		$html = '<div class="day_wrap">
					<label>
						<span class="title">'.esc_html__('Date','bookmify').'<span>*</span></span>
					</label>
					<input class="required_field" data-id="'.$appointmentID.'" data-selected-day="'.$date.'"  type="text" name="appointment_day" placeholder="'.esc_attr__('yy-mm-dd', 'bookmify').'" readonly />
					<input type="hidden" name="appointment_day_hidden" id="appointment_day_hidden_'.$appointmentID.'" />
					<span class="bot_btn">
						<span></span>
						<img class="bookmify_be_svg" src="'.BOOKMIFY_ASSETS_URL.'img/calendar.svg" alt="" />
						<span class="bookmify_be_loader small">
							<span class="loader_process">
								<span class="ball"></span>
								<span class="ball"></span>
								<span class="ball"></span>
							</span>
						</span>
					</span>
				</div>

				<div class="time_wrap">
					<label>
						<span class="title">'.esc_html__('Time','bookmify').'<span>*</span></span>
					</label>
					<input class="required_field" data-selected-time="'.$time.'" type="text" name="appointment_time" placeholder="'.esc_attr__('hh:ii', 'bookmify').'" value="'.$time.'" readonly />
					<input type="hidden" name="appointment_time_hidden" id="appointment_time_hidden_'.$appointmentID.'" />
					<span class="bot_btn">
						<span></span>
						<img class="bookmify_be_svg" src="'.BOOKMIFY_ASSETS_URL.'img/clock.svg" alt="" />
						<span class="bookmify_be_loader small">
								<span class="loader_process">
									<span class="ball"></span>
									<span class="ball"></span>
									<span class="ball"></span>
								</span>
							</span>
					</span>
				</div>';
		return $html;
	}
	public static function detailsInfo($appointmentID = '',$info = ''){
		$html = '
				<label for="info_'.$appointmentID.'">'.esc_html__('Info','bookmify').'</label>
				<textarea id="info_'.$appointmentID.'" name="info" placeholder="'.esc_attr__('Some info for internal usage','bookmify').'">'.$info.'</textarea>
				';
		return $html;
	}
	public static function getPriceForAppointment($ID){
		global $wpdb;
		
		$html		= '';
		$price		= 0;
		$paymentIDs	= array();
		$ID 		= esc_sql($ID);
		$query 		= "SELECT payment_id FROM {$wpdb->prefix}bmify_customer_appointments WHERE appointment_id=".$ID." AND status IN ('approved','pending')";
		$results 	= $wpdb->get_results( $query, OBJECT  );
		foreach($results as $result){
			$paymentIDs[] = $result->payment_id;
		}
		if(!empty($paymentIDs)){
			$paymentIDs = esc_sql($paymentIDs);
			$query 		= "SELECT total_price FROM {$wpdb->prefix}bmify_payments WHERE `id` IN (" . implode(',', array_map('intval', $paymentIDs)) . ")";
			$results 	= $wpdb->get_results( $query, OBJECT  );
			foreach($results as $result){
				$price	+= $result->total_price;
			}
		}
		return $price;
	}
	public static function getDurationForAppointment($ID, $res = 'duration'){
		global $wpdb;
		
		// get service ID
		$ID 			= esc_sql($ID);
		$query 			= "SELECT service_id,start_date,end_date FROM {$wpdb->prefix}bmify_appointments WHERE id=".$ID;
		$results 		= $wpdb->get_results( $query, OBJECT  );
		$serviceID		= $results[0]->service_id;
		$startDate		= $results[0]->start_date;
		$endDate		= $results[0]->end_date;
		
		// get duration of this service
		$serviceID 		= esc_sql($serviceID);
		$query 			= "SELECT duration,buffer_before,buffer_after FROM {$wpdb->prefix}bmify_services WHERE id=".$serviceID;
		$results 		= $wpdb->get_results( $query, OBJECT  );
		$serDuration	= $results[0]->duration;
		$bufferBefore	= $results[0]->buffer_before;
		$bufferAfter	= $results[0]->buffer_after;
		
		// get appointment duration
		$startTimeInMinutes = date('H',strtotime($startDate))*60 + date('i',strtotime($startDate));
		$endTimeInMinutes 	= date('H',strtotime($endDate))*60 	+ date('i',strtotime($endDate));
		$duration 			= ($endTimeInMinutes - $startTimeInMinutes)*60;
		
		// get extra duration
		$extraDuration		= $duration - $serDuration;
		
		switch($res){
			case 'duration': return $duration; break;
			case 'after': return $bufferAfter; break;
			case 'before': return $bufferBefore; break;
			case 'extra': return $extraDuration; break;
		}
	}
	public static function detailsOfAppointment($appointmentID){
		global $wpdb;
		$html			= '';
		$appointmentID 	= esc_sql($appointmentID);
		
		$query 		= "SELECT
							s.title serviceTitle,
							e.first_name empFirstName,
							e.last_name empLastName,
							a.start_date startDate,
							a.end_date endDate,
							a.status status,
							a.info info,
							a.service_id serviceID,
							a.employee_id employeeID,
							s.buffer_before bufferBefore,
							s.buffer_after bufferAfter,
							GROUP_CONCAT(ca.customer_id ORDER BY ca.id) customerIDs,
							GROUP_CONCAT(ca.number_of_people ORDER BY ca.id) customerPeopleCounts,
							GROUP_CONCAT(ca.price ORDER BY ca.id) customerServicesPrice,
							GROUP_CONCAT(ca.status ORDER BY ca.id) customerStatuses,
							GROUP_CONCAT(ca.id ORDER BY ca.id) customerAppointmentIDs,
							GROUP_CONCAT(p.id) customerPaymentIDs

						FROM 	   	   {$wpdb->prefix}bmify_appointments a 
							INNER JOIN {$wpdb->prefix}bmify_customer_appointments ca 			ON ca.appointment_id = a.id
							INNER JOIN {$wpdb->prefix}bmify_employees e 						ON a.employee_id = e.id 
							INNER JOIN {$wpdb->prefix}bmify_services s 							ON a.service_id = s.id
							INNER JOIN {$wpdb->prefix}bmify_payments p 							ON ca.payment_id = p.id

						WHERE a.id=".$appointmentID;
		$results 	= $wpdb->get_results( $query, OBJECT  );
		
		foreach($results as $result){
			$servicesAllTotal 	= 0;
			$extrasAllTotal 	= 0;
			$paidAllTotal		= 0;
			$dueAllTotal		= 0;
			$paymentTotal		= 0;
			$status 			= $result->status;
			switch($status){
				case 'approved': 	$icon = 'checked'; 	$statusText = esc_html__('Approved', 'bookmify'); break;
				case 'pending': 	$icon = 'circle'; 	$statusText = esc_html__('Pending', 'bookmify');  break;
				case 'canceled':	$icon = 'cancel'; 	$statusText = esc_html__('Canceled', 'bookmify'); break;
				case 'rejected': 	$icon = 'cancel'; 	$statusText = esc_html__('Rejected', 'bookmify'); break;
			}
			$customerIDs	= $result->customerIDs;
			$customerIDs 	= explode(',', $customerIDs); 				// creating array from string
			
			$serviceID		= $result->serviceID;
			$employeeID		= $result->employeeID;
			// get people count with approved and pending statuses
			$appointmentID 	= esc_sql($appointmentID);
			$customerIDs 	= esc_sql($customerIDs);
			$query 			= "SELECT number_of_people FROM {$wpdb->prefix}bmify_customer_appointments WHERE `customer_id` IN (" . implode(',', array_map('intval', $customerIDs)) . ") AND appointment_id=".$appointmentID." AND status IN ('approved','pending')";
			$res 			= $wpdb->get_results( $query, OBJECT  );
			$peopleCount	= 0;
			foreach($res as $re){
				$peopleCount += $re->number_of_people;
			}
			// get capacity min and capacity max of selected service and employee
			$serviceID 		= esc_sql($serviceID);
			$employeeID 	= esc_sql($employeeID);
			$query 			= "SELECT capacity_min,capacity_max FROM {$wpdb->prefix}bmify_employee_services WHERE service_id=".$serviceID." AND employee_id=".$employeeID;
			$res 			= $wpdb->get_results( $query, OBJECT  );
			$capacityMin	= $res[0]->capacity_min;
			$capacityMax	= $res[0]->capacity_max;
			
			// get service buffer before and after
			$bufferBefore 	= Helper::bookmifyNumberToDuration($result->bufferBefore);
			$bufferAfter 	= Helper::bookmifyNumberToDuration($result->bufferAfter);
			
			// **************************************************************************************************************************
			// DETAILS
			// **************************************************************************************************************************
			$details = '<div class="detail_box">
							<div class="detail_box_header"><h4>'.esc_html__('Appointment Details', 'bookmify').'</h4></div>
							<div class="detail_box_content">
								<div class="detail_box_row">
									<div class="detail_box_col col_left"><span>'.esc_html__('Date:', 'bookmify').'</span></div>
									<div class="detail_box_col col_right"><span>'.date_i18n(get_option('bookmify_be_date_format', 'd F, Y'), strtotime($result->startDate)).'</span></div>
								</div>
								<div class="detail_box_row">
									<div class="detail_box_col col_left"><span>'.esc_html__('Time:', 'bookmify').'</span></div>
									<div class="detail_box_col col_right"><span class="app_time">'.date('H:i', strtotime($result->startDate)).' - '.date('H:i', strtotime($result->endDate)).'</span></div>
								</div>
								<div class="detail_box_row">
									<div class="detail_box_col col_left"><span>'.esc_html__('Status:', 'bookmify').'</span></div>
									<div class="detail_box_col col_right"><span class="status '.$status.'">'.$statusText.'</span></div>
								</div>
								<div class="detail_box_row">
									<div class="detail_box_col col_left"><span>'.esc_html__('Employee:', 'bookmify').'</span></div>
									<div class="detail_box_col col_right"><span>'.$result->empFirstName.' '.$result->empLastName.'</span></div>
								</div>
								<div class="detail_box_row">
									<div class="detail_box_col col_left"><span>'.esc_html__('Service:', 'bookmify').'</span></div>
									<div class="detail_box_col col_right"><span>'.Helper::titleDecryption($result->serviceTitle).'</span></div>
								</div>
								<div class="detail_box_row">
									<div class="detail_box_col col_left"><span>'.esc_html__('Buffer Before/After:', 'bookmify').'</span></div>
									<div class="detail_box_col col_right"><span>'.$bufferBefore.' / '.$bufferAfter.'</span></div>
								</div>
								<div class="detail_box_row">
									<div class="detail_box_col col_left"><span>'.esc_html__('Capacity Min/Max:', 'bookmify').'</span></div>
									<div class="detail_box_col col_right"><span>'.$capacityMin.' / '.$capacityMax.'</span></div>
								</div>
								<div class="detail_box_row">
									<div class="detail_box_col col_left"><span>'.esc_html__('People Count:', 'bookmify').'</span></div>
									<div class="detail_box_col col_right"><span>'.$peopleCount.' '.esc_html__('People', 'bookmify').'</span></div>
								</div>';
			if($result->info != ''){
				$details 	.= '<div class="detail_box_row">
									<div class="detail_box_col col_left"><span>'.esc_html__('Info:', 'bookmify').'</span></div>
									<div class="detail_box_col col_right"><span>'.$result->info.'</span></div>
								</div>';
			}
				$details 	.= '</div>
							</div>';
			
			
			// **************************************************************************************************************************
			// CUSTOMERS
			// **************************************************************************************************************************
			$customers 					= '';
			$customerStatuses 			= explode(',', $result->customerStatuses); 			// creating array from string
			$customerAppointmentIDs 	= explode(',', $result->customerAppointmentIDs); 	// creating array from string
			$customerPeopleCounts 		= explode(',', $result->customerPeopleCounts); 		// creating array from string
			$customerServicesPrice 		= explode(',', $result->customerServicesPrice); 	// creating array from string
			$customerPaymentIDs 		= explode(',', $result->customerPaymentIDs); 		// creating array from string
			foreach($customerIDs as $key => $customerID){
				$customerFullName 		= Helper::bookmifyGetCustomerCol($customerID);
				$customerEmail	 		= Helper::bookmifyGetCustomerCol($customerID, 'email');
				$customerPhone	 		= Helper::bookmifyGetCustomerCol($customerID, 'phone');
				
				$customerAppointmentID 	= $customerAppointmentIDs[$key];
				$customerPeopleCount 	= $customerPeopleCounts[$key];
				$customerServicePrice 	= $customerServicesPrice[$key];
				$customerServiceTotal	= $customerPeopleCount * $customerServicePrice;
				
				$query 			= "SELECT * FROM {$wpdb->prefix}bmify_customer_appointments_extras WHERE customer_appointment_id=".$customerAppointmentID;
				$res 			= $wpdb->get_results( $query, OBJECT  );
				$extras			= '';
				if(count($res) != 0){
					$extras .= '<div class="detail_box_row">
									<div class="detail_box_col col_left"><span class="sub_title">'.esc_html__('Extras', 'bookmify').'</span></div>
								</div>';
				}
				$extraTotalForCustomer = 0;
				foreach($res as $re){
					$extraID 		= $re->extra_id;
					$extraQuantity	= $re->quantity;
					$extraPrice		= $re->price;
					$extraTotal		= $extraPrice * $extraQuantity * $customerPeopleCount;
					$extraTotalForCustomer += $extraTotal;
					$extras .= '<div class="detail_box_row">
									<div class="detail_box_col col_left"><span>'.Helper::bookmifyGetExtraServicesCol($re->extra_id).'</span></div>
									<div class="detail_box_col col_right"><span><span class="price_calc">'.$customerPeopleCount.' x '.$extraQuantity.' x '.Helper::bookmifyPriceCorrection($extraPrice).' = </span><span class="price_eq">'.Helper::bookmifyPriceCorrection($extraTotal).'</span></span></div>
								</div>';
				}
				
				$customerPaymentIDs[$key] 	= esc_sql($customerPaymentIDs[$key]);
				$query 			= "SELECT * FROM {$wpdb->prefix}bmify_payments WHERE id=".$customerPaymentIDs[$key];
				$payments 		= $wpdb->get_results( $query, OBJECT  );
				$pPaidType		= $payments[0]->paid_type;
				$pCreatedDate	= $payments[0]->created_date;
				$pPaid 			= $payments[0]->paid;
				$pTotalPrice	= $payments[0]->total_price;
				
				switch($customerStatuses[$key]){
					case 'approved': 	$icon = 'checked'; 	$statusText = esc_html__('Approved', 'bookmify'); break;
					case 'pending': 	$icon = 'circle'; 	$statusText = esc_html__('Pending', 'bookmify');  break;
					case 'canceled':	$icon = 'cancel'; 	$statusText = esc_html__('Canceled', 'bookmify'); break;
					case 'rejected': 	$icon = 'cancel'; 	$statusText = esc_html__('Rejected', 'bookmify'); break;
				}
				if($customerStatuses[$key] == 'approved' || $customerStatuses[$key] == 'pending'){
					$servicesAllTotal			+= $customerServiceTotal;
					$extrasAllTotal				+= $extraTotalForCustomer;
					$paidAllTotal 				+= $pPaid;
					$paymentTotal				+= $pTotalPrice;
					$dueAllTotal 				+= ($pTotalPrice - $pPaid);
				}else{
					$customerServicePrice 		= 0;
					$customerServiceTotal 		= 0;
					$extraTotalForCustomer 		= 0;
					$pTotalPrice 				= 0;
					$pPaid 						= 0;
				}
				
				$taxCustomer	= self::taxOfCustomer($appointmentID,$customerID);
				switch($pPaidType){
					case 'paypal': 	$paymentGateway = esc_html__('Paypal', 'bookmify'); break;
					case 'stripe': 	$paymentGateway = esc_html__('Stripe', 'bookmify'); break;
					case 'local': 	
					default: 		$paymentGateway = esc_html__('Local', 'bookmify'); break;
				}
				
				// since 1.3.6
				$customFields	= '';
				$query 			= "SELECT cf FROM {$wpdb->prefix}bmify_customer_appointments WHERE id=".$customerAppointmentID;
				$res 			= $wpdb->get_results( $query, OBJECT  );
				if(count($res) != 0){
					if($res[0]->cf != ''){
						$values = $res[0]->cf;
						$values 	= unserialize($values);
						if ($values !== false) {
							if(!empty($values)){
								$customFields .= '<div class="detail_box_row">
													<div class="detail_box_col col_left"><span class="sub_title">'.esc_html__('Custom Fields', 'bookmify').'</span></div>
												</div>';
								foreach($values as $value){
									$cfValue = '';
									if(is_array($value->value)){
										foreach($value->value as $cfKey => $val){
											$cfValue .= ($cfKey + 1) . '. ' . $val . '; ';
										}
									}else{
										$cfValue .= $value->value . '; ';
									}
									$cfValue = substr($cfValue, 0, -2);
									$customFields .= '<div class="detail_box_row">
										<div class="detail_box_col col_left"><span>'.$value->label.'</span></div>
										<div class="detail_box_col col_right"><span>'.$cfValue.'</span></div>
									</div>';
								}
							}
						}
							
					}
					
				}
				
				$extras .= 	   '<div class="detail_box_row">
									<div class="detail_box_col col_left"><span class="sub_title">'.esc_html__('Payment', 'bookmify').'</span></div>
								</div>

								<div class="detail_box_row">
									<div class="detail_box_col col_left"><span>'.esc_html__('Created Date:', 'bookmify').'</span></div>
									<div class="detail_box_col col_right"><span>'.date_i18n(get_option('bookmify_be_date_format', 'd F, Y'), strtotime($pCreatedDate)).'</span></div>
								</div>
								<div class="detail_box_row">
									<div class="detail_box_col col_left"><span>'.esc_html__('Payment Method:', 'bookmify').'</span></div>
									<div class="detail_box_col col_right"><span>'.$paymentGateway.'</span></div>
								</div>
								<div class="detail_box_row">
									<div class="detail_box_col col_left"><span>'.esc_html__('Service Price:', 'bookmify').'</span></div>
									<div class="detail_box_col col_right"><span><span class="price_calc">'.$customerPeopleCount.' x '.Helper::bookmifyPriceCorrection($customerServicePrice).' = </span><span class="price_eq">'.Helper::bookmifyPriceCorrection($customerServiceTotal).'</span></span></div>
								</div>
								<div class="detail_box_row">
									<div class="detail_box_col col_left"><span>'.esc_html__('Extras Price:', 'bookmify').'</span></div>
									<div class="detail_box_col col_right"><span><span class="price_eq">'.Helper::bookmifyPriceCorrection($extraTotalForCustomer).'</span></span></div>
								</div>
								<div class="detail_box_row">
									<div class="detail_box_col col_left"><span>'.esc_html__('Service Tax:', 'bookmify').'</span></div>
									<div class="detail_box_col col_right"><span><span class="price_calc">'.$customerPeopleCount.' x '.Helper::bookmifyPriceCorrection($customerServicePrice).' x  '.$taxCustomer.'% = </span><span class="price_eq">'.Helper::bookmifyPriceCorrection(floor($customerServiceTotal*$taxCustomer)/100).'</span></span></div>
								</div>
								<div class="detail_box_row">
									<div class="detail_box_col col_left"><span>'.esc_html__('Extras Tax:', 'bookmify').'</span></div>
									<div class="detail_box_col col_right"><span><span class="price_eq">'.Helper::bookmifyPriceCorrection(floor($extraTotalForCustomer*$taxCustomer)/100).'</span></span></div>
								</div>
								<div class="detail_box_row">
									<div class="detail_box_col col_left"><span>'.esc_html__('Paid:', 'bookmify').'</span></div>
									<div class="detail_box_col col_right"><span><span class="price_eq">'.Helper::bookmifyPriceCorrection($pPaid).'</span></span></div>
								</div>
								<div class="detail_box_row">
									<div class="detail_box_col col_left"><span>'.esc_html__('Due:', 'bookmify').'</span></div>
									<div class="detail_box_col col_right"><span><span class="price_eq">'.Helper::bookmifyPriceCorrection($pTotalPrice - $pPaid).'</span></span></div>
								</div>
								<div class="detail_box_row sub_total">
									<div class="detail_box_col col_left"><span>'.esc_html__('Subtotal:', 'bookmify').'</span></div>
									<div class="detail_box_col col_right"><span><span class="price_eq">'.Helper::bookmifyPriceCorrection($pTotalPrice).'</span></span></div>
								</div>';
				
				
				
				$customers .= '<div class="detail_box has_subtotal">
								<div class="detail_box_header"><h4>'.esc_html__('Customer', 'bookmify').' #'.($key+1).'</h4></div>
								<div class="detail_box_content">
									<div class="detail_box_row">
										<div class="detail_box_col col_left"><span class="sub_title">'.esc_html__('Info', 'bookmify').'</span></div>
									</div>
									<div class="detail_box_row">
										<div class="detail_box_col col_left"><span>'.esc_html__('Name:', 'bookmify').'</span></div>
										<div class="detail_box_col col_right"><span>'.$customerFullName.'</span></div>
									</div>
									<div class="detail_box_row">
										<div class="detail_box_col col_left"><span>'.esc_html__('Email:', 'bookmify').'</span></div>
										<div class="detail_box_col col_right"><span>'.$customerEmail.'</span></div>
									</div>';
				if($customerPhone != ''){
				$customers 	.= 		'<div class="detail_box_row">
										<div class="detail_box_col col_left"><span>'.esc_html__('Phone:', 'bookmify').'</span></div>
										<div class="detail_box_col col_right"><span>'.$customerPhone.'</span></div>
									</div>';
				}
				
				$customers .=	   '<div class="detail_box_row">
										<div class="detail_box_col col_left"><span class="sub_title">'.esc_html__('Appointment', 'bookmify').'</span></div>
									</div>
									<div class="detail_box_row">
										<div class="detail_box_col col_left"><span>'.esc_html__('Status:', 'bookmify').'</span></div>
										<div class="detail_box_col col_right"><span class="status '.$customerStatuses[$key].'">'.$statusText.'</span></div>
									</div>
									<div class="detail_box_row">
										<div class="detail_box_col col_left"><span>'.esc_html__('People Count:', 'bookmify').'</span></div>
										<div class="detail_box_col col_right"><span>'.$customerPeopleCount.' '.esc_html__('People', 'bookmify').'</span></div>
									</div>
									'.$customFields.'
									'.$extras.'
						</div>
					</div>';
			}
			$total = '';
			$total = '<div class="detail_box highlighted">
							<div class="detail_box_content">
								<div class="detail_box_row">
									<div class="detail_box_col col_left"><span>'.esc_html__('Services Price:', 'bookmify').'</span></div>
									<div class="detail_box_col col_right"><span><span class="price_eq">'.Helper::bookmifyPriceCorrection($servicesAllTotal).'</span></span></div>
								</div>
								<div class="detail_box_row">
									<div class="detail_box_col col_left"><span>'.esc_html__('Extras Price:', 'bookmify').'</span></div>
									<div class="detail_box_col col_right"><span><span class="price_eq">'.Helper::bookmifyPriceCorrection($extrasAllTotal).'</span></span></div>
								</div>
								<div class="detail_box_row">
									<div class="detail_box_col col_left"><span>'.esc_html__('Paid:', 'bookmify').'</span></div>
									<div class="detail_box_col col_right"><span><span class="price_eq">'.Helper::bookmifyPriceCorrection($paidAllTotal).'</span></span></div>
								</div>
								<div class="detail_box_row">
									<div class="detail_box_col col_left"><span>'.esc_html__('Due:', 'bookmify').'</span></div>
									<div class="detail_box_col col_right"><span><span class="price_eq">'.Helper::bookmifyPriceCorrection($dueAllTotal).'</span></span></div>
								</div>
								<div class="detail_box_row total">
									<div class="detail_box_col col_left"><span>'.esc_html__('Total Price:', 'bookmify').'</span></div>
									<div class="detail_box_col col_right"><span><span class="price_eq">'.Helper::bookmifyPriceCorrection($paymentTotal).'</span></span></div>
								</div>
							</div>
						</div>';
		}
		$html .= $details.$customers.$total;
		
		return $html;
	}
	
	/**
     * Public Funtion.
	 * @since 1.0.0
     */
	public static function servicesAsFilter(){
		global $wpdb;
		$query 		= "SELECT title, id FROM {$wpdb->prefix}bmify_services ORDER BY title, id";
		$results	= $wpdb->get_results( $query, OBJECT  );
		$html 		= '<div class="bookmify_be_services_filter_list">';
		foreach ( $results as $result ) {
			$html  .= '<div data-id="'.$result->id.'">'.Helper::titleDecryption($result->title).'</div>';
		}
		$html 	   .= '</div>';
		return $html;
	}
	
	/**
     * Public Funtion.
	 * @since 1.0.0
     */
	public static function customersAsFilter(){
		global $wpdb;
		$query 		= "SELECT first_name, last_name, id FROM {$wpdb->prefix}bmify_customers ORDER BY first_name, last_name, id";
		$results	= $wpdb->get_results( $query, OBJECT  );
		$html 		= '<div class="bookmify_be_filter_popup_list customers">
							<div class="bookmify_be_filter_popup_list_in">';
		foreach ( $results as $result ) {
			$html  .= '<div data-id="'.$result->id.'" class="item"><span>'.esc_html( $result->first_name.' '.$result->last_name ).'</span></div>';
		}
		$html 	   .= '</div></div>';
		return $html;
	}
	
	/**
     * Public Funtion.
	 * @since 1.0.0
     */
	public static function employeesAsFilter(){
		global $wpdb;
		$query 		= "SELECT first_name,last_name,id FROM {$wpdb->prefix}bmify_employees ORDER BY first_name,last_name,id";
		$results	= $wpdb->get_results( $query, OBJECT  );
		$html 		= '<div class="bookmify_be_filter_popup_list employees">
							<div class="bookmify_be_filter_popup_list_in">';
		foreach ( $results as $result ) {
			$html  .= '<div data-id="'.$result->id.'" class="item"><span>'.esc_html( $result->first_name.' '.$result->last_name ).'</span></div>';
		}
		$html 	   .= '</div></div>';
		return $html;
	}
	/**
     * Public Funtion.
	 * @since 1.0.0
     */
	public static function statusAsFilter(){
		
		$html 		= '<div class="bookmify_be_filter_popup_list status">
							<div class="bookmify_be_filter_popup_list_in">';
		
		$html 				.= '<div data-status="approved" class="item"><span>'.esc_html__('Approved', 'bookmify').'</span></div>';
		$html 				.= '<div data-status="pending" class="item"><span>'.esc_html__('Pending', 'bookmify').'</span></div>';
		$html 				.= '<div data-status="canceled" class="item"><span>'.esc_html__('Canceled', 'bookmify').'</span></div>';
		$html 				.= '<div data-status="rejected" class="item"><span>'.esc_html__('Rejected', 'bookmify').'</span></div>';
		
		$html 	   .= '</div></div>';
		return $html;
	}
	
	public static function allFilter(){
		
		$html = '<div class="bookmify_be_appointments_filter" data-filter-status="">
						<div class="bookmify_be_filter_wrap">
							<div class="bookmify_be_filter">
								<div class="bookmify_be_row">

									<div class="bookmify_be_filter_list daterange">
										<div class="bookmify_be_filter_list_in">
											<div class="input_wrapper">
												<input type="text" placeholder="'.esc_attr__('Date Filter', 'bookmify').'" class="filter_date" autocomplete=off />
											</div>
										</div>
									</div>

									<div class="bookmify_be_filter_list services">
										<div class="bookmify_be_filter_list_in">
											<div class="input_wrapper">
												<input readonly data-placeholder="'.esc_attr__('All Services', 'bookmify').'" type="text" placeholder="'.esc_attr__('All Services', 'bookmify').'" class="filter_list" autocomplete=off />
												<span class="icon">
													<img class="bookmify_be_svg" src="'.BOOKMIFY_ASSETS_URL.'img/down.svg'.'" alt="" />
													<span class="bookmify_be_loader small">
														<span class="loader_process">
															<span class="ball"></span>
															<span class="ball"></span>
															<span class="ball"></span>
														</span>
													</span>
													<span class="reset"></span>
												</span>
												<div class="bookmify_be_new_value"></div>
											</div>

											'.self::servicesAsFilter().'

										</div>
									</div>

									<div class="bookmify_be_filter_list customers">
										<div class="bookmify_be_filter_list_in">
											<div class="input_wrapper">
												<input data-placeholder="'.esc_attr__('All Customers', 'bookmify').'" type="text" placeholder="'.esc_attr__('All Customers', 'bookmify').'" class="filter_list" autocomplete=off />
												<span class="icon">
													<img class="bookmify_be_svg" src="'.BOOKMIFY_ASSETS_URL.'img/down.svg'.'" alt="" />
													<span class="bookmify_be_loader small">
														<span class="loader_process">
															<span class="ball"></span>
															<span class="ball"></span>
															<span class="ball"></span>
														</span>
													</span>
													<span class="reset"></span>
												</span>
												<div class="bookmify_be_new_value"></div>
											</div>

											'.self::customersAsFilter().'
										</div>
									</div>

									<div class="bookmify_be_filter_list employees">
										<div class="bookmify_be_filter_list_in">
											<div class="input_wrapper">
												<input data-placeholder="'.esc_attr__('All Employees', 'bookmify').'" type="text" placeholder="'.esc_attr__('All Employees', 'bookmify').'" class="filter_list" autocomplete=off />
												<span class="icon">
													<img class="bookmify_be_svg" src="'.BOOKMIFY_ASSETS_URL.'img/down.svg'.'" alt="" />
													<span class="bookmify_be_loader small">
														<span class="loader_process">
															<span class="ball"></span>
															<span class="ball"></span>
															<span class="ball"></span>
														</span>
													</span>
													<span class="reset"></span>
												</span>
												<div class="bookmify_be_new_value"></div>
											</div>

											'.self::employeesAsFilter().'

										</div>
									</div>

									<div class="bookmify_be_filter_list status">
										<div class="bookmify_be_filter_list_in">
											<div class="input_wrapper">
												<input data-placeholder="'.esc_attr__('All Statuses', 'bookmify').'" type="text" placeholder="'.esc_attr__('All Statuses', 'bookmify').'" class="filter_list" autocomplete=off />
												<span class="icon">
													<img class="bookmify_be_svg" src="'.BOOKMIFY_ASSETS_URL.'img/down.svg'.'" alt="" />
													<span class="bookmify_be_loader small">
														<span class="loader_process">
															<span class="ball"></span>
															<span class="ball"></span>
															<span class="ball"></span>
														</span>
													</span>
													<span class="reset"></span>
												</span>
												<div class="bookmify_be_new_value"></div>
											</div>

											'.self::statusAsFilter().'

										</div>
									</div>
									
									<div class="bookmify_be_filter_list reset">
										<div class="bookmify_be_filter_list_in">
											<div class="input_wrapper">
												<a href="#">'.esc_html__('Reset', 'bookmify').'</a>
											</div>
										</div>
									</div>

								</div>

							</div>
						</div>
					</div>';
		return $html;
	}
	public static function getCustomerAppointmentStatus($customerID,$appointmentID){
		global $wpdb;
		$customerID 	= esc_sql($customerID);
		$appointmentID 	= esc_sql($appointmentID);
		$query 			= "SELECT status FROM {$wpdb->prefix}bmify_customer_appointments WHERE customer_id=".$customerID." AND appointment_id=".$appointmentID;
		$results 		= $wpdb->get_results( $query);
		return $results[0]->status;
	}
	
	public static function getAppDataForGoogle($appID, $col = ''){
		global $wpdb;
		
		$appID = esc_sql($appID);
		$query = "SELECT 
					a.id appID,
					a.start_date appStartDate,
					a.end_date appEndDate,
					a.google_calendar_event_id googleCalendarEventID,
					GROUP_CONCAT(ca.customer_id ORDER BY ca.id) customerIDs,
					GROUP_CONCAT(ca.status ORDER BY ca.id) customerAppStatuses,
					s.title serviceTitle,
					a.employee_id employeeID,
					e.first_Name employeeFName,
					e.last_Name employeeLName,
					l.address location
					
				FROM 		   {$wpdb->prefix}bmify_appointments a
					INNER JOIN {$wpdb->prefix}bmify_services s					ON s.id = a.service_id
					INNER JOIN {$wpdb->prefix}bmify_employees e					ON e.id = a.employee_id
					LEFT  JOIN {$wpdb->prefix}bmify_employee_locations el		ON el.employee_id = a.employee_id
					LEFT  JOIN {$wpdb->prefix}bmify_locations l					ON l.id = el.location_id
					LEFT  JOIN  {$wpdb->prefix}bmify_customer_appointments ca 	ON ca.appointment_id = a.id
				
				WHERE a.id=".$appID;
		$results = $wpdb->get_results( $query);
		
		$result		= '';
		
		foreach($results as $app){
			switch($col){
				default:
				case 'service': 				$result = $app->serviceTitle; break;
				case 'employee': 				$result = $app->employeeFName.' '.$app->employeeLName; break;
				case 'employeeID': 				$result = $app->employeeID; break;
				case 'location': 				$result = $app->location; break;
				case 'startDate': 				$result = $app->appStartDate; break;
				case 'endDate': 				$result = $app->appEndDate; break;
				case 'googleCalendarEventID': 	$result = $app->googleCalendarEventID; break;
				case 'customerAppStatus': 		$result = explode(',', $app->customerAppStatuses); break;
				case 'customerIDs': 			$result = explode(',', $app->customerIDs); break;
			}
			
		}
		
		return $result;
	}
	
	
	public static function getCustomerInfo($customerID, $col=""){
		global $wpdb;
		
		$customerID = esc_sql($customerID);
		$query = "SELECT 
					c.first_name firstName,
					c.last_name lastName,
					c.email
					
				FROM {$wpdb->prefix}bmify_customers c
				
				WHERE c.id=".$customerID;
		$results = $wpdb->get_results( $query);
		
		foreach($results as $customer){
			switch($col){
				default:
				case 'name': 			$result = $customer->firstName.' '.$customer->lastName; break;
				case 'email': 			$result = $customer->email; break;
			}
		}
		
		return $result;
	}
	
	public static function checkPaymentForTaxes($appointmentID, $customersArray){
		global $wpdb;
		
		$taxSummary = 0;
		
		
		if($appointmentID != ''){
			
			$query = "SELECT 
						p.tax_ids taxIDs

					FROM {$wpdb->prefix}bmify_payments p
						INNER JOIN {$wpdb->prefix}bmify_customer_appointments ca					ON ca.payment_id = p.id

					WHERE ca.customer_id IN (" . implode(",", array_map("intval", $customersArray)) . ") AND ca.appointment_id=".$appointmentID;
			$results = $wpdb->get_results( $query);
			if(!empty($results)){
				foreach($results as $result){
					$taxes = $result->taxIDs;
					$taxes = unserialize($taxes);
					if(!empty($taxes)){
						foreach($taxes as $tax){
							$taxSummary += (float) $tax['rate'];
						}
					}
					$taxes = NULL;
				}

			}
			
		}
		
		return $taxSummary;
	}
	
	public static function taxOfCustomer($appointmentID, $customerID){
		global $wpdb;
		$taxSummary = 0;
		
		if($appointmentID != ''){
			$query = "SELECT 
						p.tax_ids taxIDs

					FROM {$wpdb->prefix}bmify_payments p
						INNER JOIN {$wpdb->prefix}bmify_customer_appointments ca					ON ca.payment_id = p.id

					WHERE ca.customer_id=".$customerID." AND ca.appointment_id=".$appointmentID;
			$results = $wpdb->get_results( $query);
			if(!empty($results)){
				foreach($results as $result){
					$taxes = $result->taxIDs;
					$taxes = unserialize($taxes);
					if(!empty($taxes)){
						foreach($taxes as $tax){
							$taxSummary += (float) $tax['rate'];
						}
					}
					$taxes = NULL;
				}

			}
		}
			
		
		return $taxSummary;
	}
	
	public static function taxOfService($serviceID){
		global $wpdb;
		$taxSummary = 0;
		
		if($serviceID != ''){
			$query = "SELECT 
						t.rate tRate

						FROM {$wpdb->prefix}bmify_taxes t
							INNER JOIN {$wpdb->prefix}bmify_services_taxes st					ON t.id = st.tax_id

						WHERE st.service_id=".$serviceID;
			$results = $wpdb->get_results( $query);
			if(!empty($results)){
				foreach($results as $result){
					$taxes = $result->tRate;
					$taxSummary += (float)$taxes;
				}

			}
		}
			
		
		return $taxSummary;
	}
	
	public static function taxIDsObjectCreatorForPayment($serviceID){
		global $wpdb;
		
		$object  			= '';
		
		if($serviceID != ''){
			$query = "SELECT 
						t.rate tRate,
						t.title tTitle

						FROM {$wpdb->prefix}bmify_taxes t
							INNER JOIN {$wpdb->prefix}bmify_services_taxes st					ON t.id = st.tax_id

						WHERE st.service_id=".$serviceID;
			$results = $wpdb->get_results( $query);
			
			if(!empty($results)){
				$taxIDs 		= [];
				foreach($results as $key => $result){
					$taxIDs[$key]['rate'] 	= $result->tRate;
					$taxIDs[$key]['title'] 	= $result->tTitle;
				}
				$object = serialize($taxIDs);
			}
		}
		
		return $object;
	}
	
	public static function taxOfServiceAsObject($serviceID){
		global $wpdb;
		
		$taxIDs  	   = [];
		
		if($serviceID != ''){
			$query = "SELECT 
						t.title tTitle,
						t.id tID,
						t.rate tRate

						FROM {$wpdb->prefix}bmify_taxes t
							INNER JOIN {$wpdb->prefix}bmify_services_taxes st					ON t.id = st.tax_id

						WHERE st.service_id=".$serviceID;
			$results = $wpdb->get_results( $query);
			
			if(!empty($results)){
				$taxIDs 		= [];
				foreach($results as $key => $result){
					$taxIDs[$key]['id'] 	= $result->tID;
					$taxIDs[$key]['title'] 	= $result->tTitle;
					$taxIDs[$key]['rate'] 	= $result->tRate;
				}
			}
		}
		
		return $taxIDs;
	}
	
	
	
	public static function getNumberOfPeople($ID){
		global $wpdb;
		$query 		= "SELECT number_of_people FROM {$wpdb->prefix}bmify_customer_appointments WHERE appointment_id=".$ID." AND status IN ('approved','pending')";
		$results 	= $wpdb->get_results( $query, OBJECT  );
		$count		= 0;
		foreach($results as $result){
			$count += $result->number_of_people;
		}
		return $count;
	}
	
	public static function getTotalPriceForAppointmentByID($appointmentID){
		global $wpdb;
		$paymentTotal		= 0;
		$appointmentID 		= esc_sql($appointmentID);
		$query 		= "SELECT
							GROUP_CONCAT(ca.customer_id ORDER BY ca.id) customerIDs,
							GROUP_CONCAT(p.id) customerPaymentIDs

						FROM 	   	   {$wpdb->prefix}bmify_appointments a 
							INNER JOIN {$wpdb->prefix}bmify_customer_appointments ca 			ON ca.appointment_id = a.id
							INNER JOIN {$wpdb->prefix}bmify_payments p 							ON ca.payment_id = p.id

						WHERE a.id=".$appointmentID." AND ca.status IN ('approved','pending')";
		$results 	= $wpdb->get_results( $query, OBJECT  );

		foreach($results as $result){
			$customerIDs 					= explode(',', $result->customerIDs); 				// creating array from string
			$customerPaymentIDs 			= explode(',', $result->customerPaymentIDs); 		// creating array from string
			foreach($customerIDs as $key => $customerID){
				$customerPaymentIDs[$key] 	= esc_sql($customerPaymentIDs[$key]);
				$query 						= "SELECT total_price FROM {$wpdb->prefix}bmify_payments WHERE id=".$customerPaymentIDs[$key];
				$payments 					= $wpdb->get_results( $query, OBJECT  );
				$paymentTotal				+= $payments[0]->total_price;
			}
		}
		return $paymentTotal;
	}
	
	public static function getExtraForAppointmentByID($appointmentID){
		global $wpdb;
		$html			= '';
		$appointmentID 	= esc_sql($appointmentID);
		
		$query 			= "SELECT
		
							GROUP_CONCAT(ca.customer_id ORDER BY ca.id) customerIDs,
							GROUP_CONCAT(ca.number_of_people ORDER BY ca.id) customerPeopleCounts,
							GROUP_CONCAT(ca.id ORDER BY ca.id) customerAppointmentIDs

						FROM 	   	   {$wpdb->prefix}bmify_appointments a 
							INNER JOIN {$wpdb->prefix}bmify_customer_appointments ca 			ON ca.appointment_id = a.id

						WHERE a.id=".$appointmentID;
		$results 	= $wpdb->get_results( $query, OBJECT  );
		
		foreach($results as $result){
			$customerIDs 				= explode(',', $result->customerIDs);
			$customerAppointmentIDs 	= explode(',', $result->customerAppointmentIDs); 	// creating array from string
			$customerPeopleCounts 		= explode(',', $result->customerPeopleCounts); 		// creating array from string
			foreach($customerIDs as $key => $customerID){
				$customerAppointmentID 	= $customerAppointmentIDs[$key];
				$customerPeopleCount 	= $customerPeopleCounts[$key];
				$customerAppointmentID 	= esc_sql($customerAppointmentID);
				$query 					= "SELECT * FROM {$wpdb->prefix}bmify_customer_appointments_extras WHERE customer_appointment_id=".$customerAppointmentID;
				$res 					= $wpdb->get_results( $query, OBJECT  );
				$customerFullName		= Helper::bookmifyGetCustomerCol($customerID);
				if(count($res) != 0){
					$html .= $customerFullName . ': ';
				}
				$extraTotalForCustomer = 0;
				foreach($res as $keyy => $re){
					$keyy++;
					$extraID 		= $re->extra_id;
					$extraQuantity	= $re->quantity;
					$extraPrice		= $re->price;
					$extraTitle		= Helper::bookmifyGetExtrasCol($extraID);
					$extraTotal		= $extraQuantity * $customerPeopleCount;
					$extraTotalForCustomer += $extraTotal;
					$html .= $keyy . '. ' . $extraTitle . ' (' .$extraTotal . '); ';
				}				
			}
			
		}
		return $html;
	}
	
	/* since bookmify v1.3.0 */
	public static function workingHoursForWeek($employeeID){
		global $wpdb;
		$html				= array();	
		// get business hours
		$query 				= "SELECT day_index,start_time,end_time FROM {$wpdb->prefix}bmify_employee_business_hours WHERE employee_id=".$employeeID." ORDER BY day_index";
		$results 			= $wpdb->get_results( $query);
		
		foreach($results as $result){
			$dayIndex		= $result->day_index;
			$startTime		= $result->start_time;
			$endTime		= $result->end_time;
			$html[$dayIndex] = array($startTime,$endTime);
		}
		return $html;
	}
	/* since bookmify v1.3.0 */
	public static function getTimeZoneOffset($timezoneOffset){
		$timezoneSwitch			= get_option( 'bookmify_be_client_timezone', '' );
//		$timezoneSwitch			= 'on';			// нужно удалить
		if($timezoneSwitch == 'on'){
//			$timezoneOffset		= -180;			// нужно удалить
			$timezoneOffset		= $timezoneOffset * (-1); 					// тайм зона клиента. умнажаем на -1 потому что мы получаем отрицательный результа
			$wpTimezoneOffset 	= intval(get_option('gmt_offset')) * 60; 	// тайм зона владельца Bookmify (в минутах) ! нужно настроить в Dashboard -> Settings -> General -> Timezone

			$timezoneOffset 	= $wpTimezoneOffset - $timezoneOffset;

			// если оффсет больше нуля, нам нужно вычесть из слотов в направлении назад
			// нам нужно прибавить на слоты в направлении вперед
		}else{
			$timezoneOffset		= 0;
		}
		return $timezoneOffset;
	}
	/* since bookmify v1.3.0 */
	public static function serviceDetails($serviceID){
		global $wpdb;
		$serviceBuffBefore 		= 0;
		$serviceBuffAfter		= 0;
		$serviceDuration		= 0;
		$query 					= "SELECT duration, buffer_before, buffer_after FROM {$wpdb->prefix}bmify_services WHERE id=".$serviceID;
		$results 				= $wpdb->get_results( $query, OBJECT  );
		foreach($results as $result){
			$serviceDuration 	= $result->duration; 			// в секундах
			$serviceBuffBefore 	= $result->buffer_before;		// в секундах
			$serviceBuffAfter 	= $result->buffer_after;		// в секундах
		}
		return array($serviceBuffBefore,$serviceBuffAfter,$serviceDuration);
	}
	/* since bookmify v1.3.0 */
	public static function timeInterval($summaryDuration){
		if(get_option('bookmify_be_service_time_as_slot', '') == 'on'){		// check if service duration selected as time interval
			return $summaryDuration;
		}else{
			return get_option( 'bookmify_be_time_interval', '15' );			// get from Bookmify Settings
		}
	}
	/* since bookmify v1.3.0 */
	public static function breaksOfEmployee($employeeID){
		global $wpdb;
		$breakArray 		= array();
		$select 			= "SELECT start_time,end_time,day_index FROM {$wpdb->prefix}bmify_employee_business_hours_breaks WHERE employee_id=".$employeeID." ORDER BY day_index";
		$breaks 			= $wpdb->get_results( $select, OBJECT  );
		foreach($breaks as $key => $break){
			$startBreak 	= HelperTime::timeToMinutes($break->start_time);
			$endBreak 		= HelperTime::timeToMinutes($break->end_time);
			$breakArray[$break->day_index][] 	= array($startBreak,$endBreak);
		}
		return $breakArray;
	}
	/* since bookmify v1.3.0 */
	public static function timeSlotsOfDayOfTheWeek($workingHours,$dayIndex,$serviceBuffBefore,$timeInterval,$summaryDuration, $breakArray){
		$mondayStartTime 			= $workingHours[$dayIndex][0];
		$mondayEndTime 				= $workingHours[$dayIndex][1];

		// start and end time in minutes
		$mondayStartTimeInMinutes 	= HelperTime::timeToMinutes($mondayStartTime);
		$mondayEndTimeCheck 		= HelperTime::timeToMinutes($mondayEndTime);
		if($mondayEndTimeCheck == 0){
			$mondayEndTimeCheck 	= 1440;
		}
		$mondayEndTimeInMinutes 	= $mondayEndTimeCheck - ($serviceBuffBefore / 60);

		// начало работы выбранного работника в секундах
		$mondayStartTime 			= strtotime($mondayStartTime) + $serviceBuffBefore; // здесь мы прибавляем время buffer_before для того, чтобы работник смог подготовиться к работе в начале работы

		// количество слотов без каких либо учетов
		$to = intval(($mondayEndTimeInMinutes - $mondayStartTimeInMinutes) / $timeInterval);
		// ОБЩИЙ массив без каких либо учетов
		$allArrayForMonday = array();
		for($i = 0; $i < $to; $i++){
			$firstTime = $i*$timeInterval + $mondayStartTimeInMinutes;
			if($firstTime <= ($mondayEndTimeInMinutes - $summaryDuration)){
				$allArrayForMonday[] = date("H", strtotime('+'.($i*$timeInterval).' minutes', $mondayStartTime))*60 + date("i", strtotime('+'.($i*$timeInterval).' minutes', $mondayStartTime));
			}
		}

		// получение всевозможных слотов, которых нужно удалить из ОБЩЕГО масива (все ПЕРЕРЫВЫ того дня недели)
		$removableValues = array();
		if(isset($breakArray[$dayIndex])){
			foreach($breakArray[$dayIndex] as $key => $result){
				$min 	= intval($result[0]) - $summaryDuration;
				$max 	= intval($result[1]) + ($serviceBuffBefore / 60); // здесь мы прибавляем время buffer_before для того, чтобы работник смог подготовиться к работе после каждого перерыва
				$removableValues[] 	= array_filter($allArrayForMonday, function ($value) use($min,$max)  { return ($value > $min && $value < $max); });
			}
		}

		$removableArr = array();
		foreach($removableValues as $results){
			foreach($results as $result){
				$removableArr[] = $result;
			}
		}

		// удаление полученных слотов, которых нужно было удалить из ОБЩЕГО массива (все ПЕРЕРЫВЫ того дня недели)
		return array_diff($allArrayForMonday,$removableArr);
	}
	/* since bookmify v1.3.0 */
	public static function timeSlotsOfSelectedDay($allArrayForMonday,$helperArray,$loopDate,$summaryDuration,$serviceBuffBefore,$helperArray2, $additionalSlots, $i, $y){
		// remove all appointments time from main array
		$differenceArray		= $allArrayForMonday;
		if(isset($helperArray[$loopDate])){
			$appointmentArray 	= $helperArray[$loopDate];
			// remove appointments from main array
			$removableValues 	= array();
			if(!empty($appointmentArray)){
				foreach($appointmentArray as $result){
					$min 		= intval($result[0]) - $summaryDuration;
					$max 		= intval($result[1]) + ($serviceBuffBefore / 60); // здесь мы прибавляем время buffer_before для того, чтобы работник смог подготовиться к работе после каждого перерыва
					$removableValues[] 	= array_filter($allArrayForMonday, function ($value) use($min,$max)  { return ($value > $min && $value < $max); });
				}
				$removableArr = array();
				if(!empty($removableValues)){
					foreach($removableValues as $results){
						foreach($results as $result){
							$removableArr[] = $result;
						}
					}
				}
				// удаление полученных слотов, которых нужно было удалить из ОБЩЕГО массива (все ВСТРЕЧИ того дня)
				$differenceArray = array_diff($differenceArray,$removableArr);
			}
		}
		// remove all google events (NOT Bookmify Events) from main array
		if(!empty($helperArray2)){
			if(isset($helperArray2[$loopDate])){
				$events		= $helperArray2[$loopDate];
				if(!empty($events)){
					$removableValues = array();
					foreach($events as $result){
						$min 	= intval($result[0]) - $summaryDuration;
						$max 	= intval($result[1]);
						$removableValues[] 	= array_filter($allArrayForMonday, function ($value) use($min,$max)  { return ($value > $min && $value < $max); });
					}
					$removableArr = array();
					if(!empty($removableValues)){
						foreach($removableValues as $results){
							foreach($results as $result){
								$removableArr[] = $result;
							}
						}
					}
					// удаление полученных слотов, которых нужно было удалить из ОБЩЕГО массива (все ВСТРЕЧИ того дня)
					$differenceArray = array_diff($differenceArray,$removableArr);
				}

			}
		}
		if(array_key_exists($loopDate,$additionalSlots)){
			if($i == 0){
				$array 				= $additionalSlots[$loopDate];
				$filteredArray 		= array_filter($array, function ($x) use ($y) { return $x >= $y; });
				$keySortedArray 	= array_values($filteredArray);
				
				$differenceArray 	= array_merge($differenceArray,$keySortedArray); // добавление в массив, тех встреч, где есть допольнительные места
				$aaaa 				= array_values($differenceArray);
				asort($aaaa); // сортировка массива после добавления
				return $aaaa;
			}else{
				$differenceArray 	= array_merge($differenceArray,$additionalSlots[$loopDate]); // добавление в массив, тех встреч, где есть допольнительные места
				$aaaa 				= array_values($differenceArray);
				asort($aaaa); // сортировка массива после добавления
				return $aaaa;
			}
			
		}else{
			return $differenceArray;
		}
	}
	/* since bookmify v1.3.0 */
	public static function allHolidaysEmpPeriod($employeeID,$startTime,$endTime){
		global $wpdb;
		$html				= array();
		
		// 
		$startYear			= $startTime->format('Y');
		
		// start and end dates
		$startDate			= $startTime->format('Y-m-d');
		$endDate			= $endTime->format('Y-m-d');
		
		// employee ID
		$employeeID			= esc_sql($employeeID);
		
		// get day offs
		$query 				= "SELECT * FROM {$wpdb->prefix}bmify_dayoff WHERE (employee_id=".$employeeID." AND date >= '".$startDate."' AND date <= '".$endDate."') OR (employee_id=".$employeeID." AND every_year = 1) OR employee_id IS NULL";
		$results 			= $wpdb->get_results( $query);
		
		foreach($results as $holiday){
			$holidayRepeat	= $holiday->every_year;
			$holidayDate	= $holiday->date;
			
			if($holidayRepeat == 1){ // if day off has repeat option
				$holidayMonth	= date('m',strtotime($holidayDate));
				$holidayDay		= date('d',strtotime($holidayDate));
				if($holidayDate == 29 && $holidayMonth == 2){ // check for 29 February
					if($startYear%4 == 0){
						$extra		= $startYear . "-" . $holidayMonth . "-" . $holidayDay;
					}else if(($startYear+1)%4 == 0){
						$extra		= ($startYear+1) . "-" . $holidayMonth . "-" . $holidayDay;
					}else if(($startYear+2)%4 == 0){
						$extra		= ($startYear+2) . "-" . $holidayMonth . "-" . $holidayDay;
					}
					$html[]		= $extra;
				}else{
					$extra		= $startYear . "-" . $holidayMonth . "-" . $holidayDay;
					$html[]		= $extra;
					$extra		= ($startYear+1) . "-" . $holidayMonth . "-" . $holidayDay;
					$html[]		= $extra;
					$extra		= ($startYear+2) . "-" . $holidayMonth . "-" . $holidayDay;
					$html[]		= $extra;
				}
			}else{
				$html[]			= $holidayDate;
			}
		}
		$html				= array_unique($html);
		return $html;
	}
}

