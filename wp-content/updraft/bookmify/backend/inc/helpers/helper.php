<?php
namespace Bookmify;


use Bookmify\HelperService;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {exit; }


/**
 * Class Helper
 */
class Helper
{
	
	public static function titleEncryption($text = ''){
		return str_replace("'", "''", $text);
	}
	
	public static function titleDecryption($text = ''){
		return str_replace("\'", "", $text);
	}
	
	/**
     * Submit Button.
	 * @since 1.0.0
     */
    public static function saveButton( $id = 'bookmify_save', $class = '', $title = '' )
    {
        printf(
            '<button %s type="submit" class="button bookmify_button bookmify_save save %s"><span class="label">%s</span></button>',
            empty( $id ) ? null : ' id="' . $id . '"',
            empty( $class ) ? null : ' ' . $class,
            $title ?: esc_html__( 'Save', 'bookify' )
        );
    }

    /**
     * Reset Button.
	 * @since 1.0.0
     */
    public static function resetButton( $id = '', $class = '' )
    {
        printf(
            '<button %s %s class="button bookmify_button bookmify_reset reset %s" type="reset">' . esc_html__( 'Reset', 'bookmify' ) . '</button>',
            empty( $id ) ? null : ' id="' . $id . '"',
			empty( $id ) ? null : ' data-page-id="' . $id . '"',
            empty( $class ) ? null : ' ' . $class
        );
    }

    /**
     * Delete Button.
	 * @since 1.0.0
     */
    public static function deleteButton( $id = 'bookmify_delete', $class = '', $modal = null )
    {
        printf(
            '<button type="button" %s class="button bookmify_button bookmify_delete delete %s" ><span class="label"> ' . esc_html__( 'Delete', 'bookmify' ) . '</span></button>',
            empty( $id ) ? null : ' id="' . $id . '"',
            empty( $class ) ? null : ' ' . $class
        );
    }
	/**
     * Get Category Name by ID.
	 * @since 1.0.0
     */
    public static function bookmifyWorkingHoursOfEmployee( $id = '', $dayIndex = 1, $start = 'start_time' )
    {
        global $wpdb;
		$result = '';
		if($id != ''){
			$start		= esc_sql($start);
			$id			= esc_sql($id);
			$dayIndex	= esc_sql($dayIndex);
			$query 		= "SELECT $start FROM {$wpdb->prefix}bmify_employee_business_hours WHERE employee_id=".$id." AND day_index=".$dayIndex;
			$results 	= $wpdb->get_results( $query);
			if(!empty($results)){
				foreach($results as $service){
					$result = date('H:i', strtotime($service->$start));
					if($start == 'end_time' && $result == '00:00'){
						$result = '24:00';
					}
				}
			}
		}
			
		
		return $result;
    }
	
	
	/**
	 * @since 1.0.0
	 * @access protected
	*/
	public static function bookmifyGetImageByID($id, $size = 'thumbnail'){
		$url = wp_get_attachment_image_src($id, $size);
		if(is_array($url)){
			return $url[0];
		}else{
			return '';
		}
	}
	
	/**
	 * @since 1.0.0
	 * @access protected
	*/
	public static function bookmifyItemsCount($tableName = ''){
		global $wpdb;
		$count = 0;
		if($tableName != ''){
			$hasTable = 'no';
			switch($tableName){
				case 'services':
				case 'appointments':
				case 'customfields':
				case 'customers':
				case 'notifications':
				case 'employees':
				case 'payments':
				case 'taxes':
				case 'shortcodes':
				case 'coupons':
				case 'locations': $hasTable = 'yes'; break;
			}
			if($hasTable == 'yes'){
				$tableName	= esc_sql($tableName);
				$count		= $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->prefix}bmify_".$tableName );
				if($tableName == 'shortcodes'){
					$count++;
				}
			}
		}
		return $count;
	}
	
	/*
	 * @since 1.0.2
	 * @access private
	*/
	public static function clonableFormTax(){
		
		
		$html = '<div class="bookmify_be_popup_form_wrap">
					<div class="bookmify_be_popup_form_position_fixer">
						<div class="bookmify_be_popup_form_bg">
							<div class="bookmify_be_popup_form">

								<div class="bookmify_be_popup_form_header">
									<h3>'.esc_html__('New Tax','bookmify').'</h3>
									<span class="closer"></span>
								</div>
								
								<div class="bookmify_be_popup_form_content">
									<div class="bookmify_be_popup_form_content_in">
									
										<div class="bookmify_be_popup_form_fields">
										
											<form autocomplete="off">
												<div class="input_wrap_row">
													<div class="input_wrap">
														<label><span class="title">'.esc_html__('Title','bookmify').'<span>*</span></span></label>
														<input class="tax_title required_field" type="text" value="" />
													</div>
												</div>

												<div class="input_wrap_row">
													<div class="input_wrap">
														<label><span class="title">'.esc_html__('Rate','bookmify').'<span>*</span></span></label>
														<input class="tax_rate required_field" type="number" value="" />
													</div>
												</div>
												
											</form>
											
										</div>
										
									</div>
								</div>
								
								<div class="bookmify_be_popup_form_button">
									<a class="save" href="#">
										<span class="text">'.esc_html__('Save','bookmify').'</span>
										<span class="save_process">
											<span class="ball"></span>
											<span class="ball"></span>
											<span class="ball"></span>
										</span>
									</a>
									<a class="cancel" href="#">'.esc_html__('Cancel','bookmify').'</a>
								</div>

							</div>
						</div>
					</div>
				</div>';
		
		return $html;
	}
	
	/*
	 * @since 1.3.6
	 * @access private
	*/
	public static function clonableFormCoupon(){
		$cuurencyIcon			= self::bookmifyGetIconPrice();
		$saleTypeChecked		= 'checked="checked"';
		$dateChecked			= '';
		$discount				= 'disabled';
		$deduction				= 'enabled';
		$daterange				= 'disabled';
		
		$html = '<div class="bookmify_be_popup_form_wrap">
					<div class="bookmify_be_popup_form_position_fixer">
						<div class="bookmify_be_popup_form_bg">
							<div class="bookmify_be_popup_form">

								<div class="bookmify_be_popup_form_header">
									<h3>'.esc_html__('New Coupon','bookmify').'</h3>
									<span class="closer"></span>
								</div>
								
								<div class="bookmify_be_popup_form_content">
									<div class="bookmify_be_popup_form_content_in">
									
										<div class="bookmify_be_popup_form_fields">
										
											<form autocomplete="off">
												<div class="input_wrap_row">
													<div class="input_wrap">
														<label><span class="title">'.esc_html__('Title','bookmify').'<span>*</span></span></label>
														<input class="coupon_title required_field" type="text" value="" />
													</div>
												</div>

												<div class="input_wrap_row">
													<div class="input_wrap">
														<label><span class="title">'.esc_html__('Code','bookmify').'<span>*</span></span></label>
														<input class="coupon_code required_field" type="text" value="" />
													</div>
												</div>
												
												<div class="input_wrap_row">
													<div class="input_wrap">
														<label><span class="title">'.esc_html__('Limit Usage','bookmify').'</span></label>
														<input class="coupon_limit" type="number" value="1" min="1" />
													</div>
												</div>
												
												<div class="input_wrap_row be_boxed be_dis_ded">
													<div class="input_wrap_row be_compare">
														<div class="input_wrap">
															<div class="bookmify_be_compare">
																<p>'.esc_html__('Discount','bookmify').'<span>(%)</span></p>
																<label class="bookmify_be_switch">
																	<input type="checkbox" id="coupon_sale_type" 
																	'.esc_attr($saleTypeChecked).'  />
																	<span class="slider round"></span>
																</label>
																<p>'.esc_html__('Deduction','bookmify').'<span>('.$cuurencyIcon.')</span></p>
															</div>
														</div>
													</div>

													<div class="input_wrap_row input_discount '.$discount.'">
														<div class="input_wrap">
															<label><span class="title">'.esc_html__('Discount','bookmify').'<span class="title_icon">(%)</span></span></label>
															<input class="coupon_discount" type="number" value="0" min="0" max="100" />
														</div>
													</div>

													<div class="input_wrap_row input_deduction '.$deduction.'">
														<div class="input_wrap">
															<label><span class="title">'.esc_html__('Deduction','bookmify').'<span class="title_icon">('.$cuurencyIcon.')</span></span></label>
															<input class="coupon_deduction" type="number" value="0" min="0" />
														</div>
													</div>
												</div>
												
												
												<div class="input_wrap_row be_boxed be_date">
													<div class="input_wrap_row be_compare">
														<div class="input_wrap">
															<p>'.esc_html__('Date Limit','bookmify').'</p>
															<label class="bookmify_be_switch">
																<input type="checkbox" id="coupon_date_limit" 
																'.esc_attr($dateChecked).'  />
																<span class="slider round"></span>
															</label>
														</div>
													</div>
													<div class="input_wrap_row daterange '.$daterange.'">
														<div class="input_wrap">
															<input class="coupon_date_limit" type="text" value="" />
														</div>
														<input type="hidden" class="date_limit_start" value="" />
														<input type="hidden" class="date_limit_end" value="" />
													</div>
												</div>
												
												
												<div class="input_wrap_row">
													<div class="input_wrap">
														<label><span class="title">'.esc_html__('Info','bookmify').'</span></label>
														<textarea class="coupon_info" placeholder="'.esc_html__('Some info for internal usage', 'bookmify').'"></textarea>
													</div>
												</div>
												
											</form>
											
										</div>
										
									</div>
								</div>
								
								<div class="bookmify_be_popup_form_button">
									<a class="save" href="#">
										<span class="text">'.esc_html__('Save','bookmify').'</span>
										<span class="save_process">
											<span class="ball"></span>
											<span class="ball"></span>
											<span class="ball"></span>
										</span>
									</a>
									<a class="cancel" href="#">'.esc_html__('Cancel','bookmify').'</a>
								</div>

							</div>
						</div>
					</div>
				</div>';
		
		return $html;
	}
	
	
	/**
     * Get Service Col.
	 * @since 1.0.0
     */
    public static function bookmifyWPUserNamebyID( $id = NULL ){
        global $wpdb;
		$result = '';
		
		if($id == NULL || $id == ''){}else{
			$user_info 	= get_userdata($id);
			$result 	= $user_info->display_name;
		}
		return $result;
    }
	
	
	public static function bookmifyDoesWPUserExist( $userID = '' ){
		global $wpdb;
		
		$count = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $wpdb->users WHERE ID=%d", $userID));

		if($count == 1){ return true; }else{ return false; }
	}
	
	
	/**
     * Get Service Col.
	 * @since 1.0.0
     */
    public static function bookmifyGetServiceCol( $id, $col = 'title' )
    {
        global $wpdb;
		
		$id			= esc_sql($id);
		$query 		= "SELECT * FROM {$wpdb->prefix}bmify_services WHERE id=".$id;
		$results 	= $wpdb->get_results( $query);
		
		foreach($results as $service){
			switch($col){
				default:
				case 'title': 		$result = $service->title; 			break;
				case 'price':	 	$result = $service->price; 			break;
				case 'duration': 	$result = $service->duration; 		break;
				case 'color': 		$result = $service->color; 			break;
				case 'category_id': $result = $service->category_id;	 break;
			}
			
		}
		
		return $result;
    }
	
	/**
     * Get Extra Services Col.
	 * @since 1.0.0
     */
    public static function bookmifyGetExtraServicesCol( $id, $col = 'title' )
    {
        global $wpdb;
		
		$id			= esc_sql($id);
		$query 		= "SELECT * FROM {$wpdb->prefix}bmify_extra_services WHERE id=".$id;
		$results 	= $wpdb->get_results( $query);
		
		foreach($results as $service){
			switch($col){
				default:
				case 'title': 			$result = $service->title; 			break;
				case 'price': 			$result = $service->price; 			break;
				case 'duration': 		$result = $service->duration; 		break;
				case 'attachment_id': 	$result = $service->attachment_id;	break;
				case 'capacity_max': 	$result = $service->capacity_max; 	break;
				case 'info': 			$result = $service->info; 			break;
			}
			
		}
		
		return $result;
    }
	/**
     * Get Category Col.
	 * @since 1.0.0
     */
    public static function bookmifyGetCategoryCol( $id, $col = 'title' )
    {
        global $wpdb;
		
		$id			= esc_sql($id);
		$query 		= "SELECT * FROM {$wpdb->prefix}bmify_categories WHERE id=".$id;
		$results 	= $wpdb->get_results( $query);
		
		foreach($results as $service){
			switch($col){
				default:
				case 'title': 			$result = $service->title; 			break;
				case 'attachment_id': 	$result = $service->attachment_id; 	break;
				case 'color': 			$result = $service->color; 			break;
				case 'icon': 			$result = $service->icon; 			break;
				case 'position': 		$result = $service->position; 		break;
			}
			
		}
		
		return $result;
    }
	
    public static function getLocationDataByEmployeeID( $employeeID, $col = 'title' )
    {
        global $wpdb;
		
		$html 			= '';
		if($employeeID != ''){
			$employeeID	= esc_sql($employeeID);
			$query 		= "SELECT location_id FROM {$wpdb->prefix}bmify_employee_locations WHERE employee_id=".$employeeID;
			$results 	= $wpdb->get_results( $query);
			$locationID = '';
			foreach($results as $result){
				$locationID = $result->location_id;
			}
			if($locationID != ''){
				$html = self::bookmifyGetLocationCol($locationID, $col);
			}
		}
		
		return $html;
    }
	

	
	/**
     * Get Employee Col.
	 * @since 1.0.0
     */
    public static function bookmifyGetEmployeeCol( $id, $col = 'full_name' )
    {
        global $wpdb;
		
		$id			= esc_sql($id);
		$query 		= "SELECT * FROM {$wpdb->prefix}bmify_employees WHERE id=".$id;
		$results 	= $wpdb->get_results( $query );
		$result 	= '';
		
		foreach($results as $employee){
			switch($col){
				default:
				case 'full_name': 		$result = $employee->first_name.' '.$employee->last_name; break;
				case 'first_name': 		$result = $employee->first_name; 	break;
				case 'last_name': 		$result = $employee->last_name; 	break;
				case 'img': 			$result = $employee->attachment_id; break;
				case 'email': 			$result = $employee->email; 		break;
				case 'phone': 			$result = $employee->phone; 		break;
				case 'info': 			$result = $employee->info; 			break;
				case 'visibility': 		$result = $employee->visibility; 	break;
			}
		}
		
		return $result;
    }
	
	
	
	/**
     * Get Customer Col.
	 * @since 1.0.0
     */
    public static function bookmifyGetCustomerCol( $id, $col = 'full_name' )
    {
        global $wpdb;
		
		$id			= esc_sql($id);
		$query 		= "SELECT * FROM {$wpdb->prefix}bmify_customers WHERE id=".$id;
		$results 	= $wpdb->get_results( $query );
		$result 	= '';
		
		foreach($results as $customer){
			switch($col){
				default:
				case 'full_name': 			$result = $customer->first_name.' '.$customer->last_name; break;
				case 'first_name': 			$result = $customer->first_name; 	break;
				case 'last_name': 			$result = $customer->last_name; 	break;
				case 'email': 				$result = $customer->email; 		break;
				case 'phone': 				$result = $customer->phone;		 	break;
				case 'info': 				$result = $customer->info; 			break;
				case 'birthday': 			$result = $customer->birthday; 		break;
				case 'country': 			$result = $customer->country; 		break;
				case 'state': 				$result = $customer->state; 		break;
				case 'city': 				$result = $customer->city; 			break;
				case 'address': 			$result = $customer->address; 		break;
				case 'post_code': 			$result = $customer->post_code; 	break;
				case 'registration_date':   $result = $customer->registration_date; break;
			}
		}
		return $result;
    }
	
	/**
     * Get Extras Col.
	 * @since 1.0.0
     */
    public static function bookmifyGetExtrasCol( $id, $col = 'title' )
    {
        global $wpdb;
		
		$id			= esc_sql($id);
		$query 		= "SELECT * FROM {$wpdb->prefix}bmify_extra_services WHERE id=".$id;
		$results 	= $wpdb->get_results( $query );
		$result 	= '';
		
		foreach($results as $extra){
			switch($col){
				default:
				case 'title': 				$result = $extra->title; 				break;
				case 'service_id': 			$result = $extra->service_id; 			break;
				case 'price':  			 	$result = $extra->price; 				break;
				case 'duration':  			$result = $extra->duration; 			break;
				case 'attachment_id':  		$result = $extra->attachment_id; 		break;
				case 'capacity_max':  		$result = $extra->capacity_max; 		break;
				case 'info':  				$result = $extra->info; 				break;
				case 'position':  			$result = $extra->position; 			break;
			}
		}
		return $result;
    }
	
	
	/**
     * Get Location Col.
	 * @since 1.0.0
     */
    public static function bookmifyGetLocationCol( $id, $col = 'title' )
    {
        global $wpdb;
		
		$id			= esc_sql($id);
		$query 		= "SELECT * FROM {$wpdb->prefix}bmify_locations WHERE id=".$id;
		$results 	= $wpdb->get_results( $query );
		$result 	= '';
		
		foreach($results as $location){
			switch($col){
				default:
				case 'full': 			$result = $location->title . ' ' . $location->address; break;
				case 'title': 			$result = $location->title; 		break;
				case 'address': 		$result = $location->address; 		break;
				case 'info': 			$result = $location->info; 			break;
				case 'img': 			$result = $location->attachment_id; break;
				case 'id': 				$result = $location->id; 			break;
			}
		}
		return $result;
    }
	
	/**
     * Get Customers Col.
	 * @since 1.0.0
     */
    public static function bookmifyGetCustomersCol( $id, $col = 'full_name' )
    {
        global $wpdb;
		$customerIDs 		= array();
		$html 				= '';
		$id					= esc_sql($id);
		$query 				= "SELECT customer_id FROM {$wpdb->prefix}bmify_customer_appointments WHERE appointment_id=".$id;
		$results 			= $wpdb->get_results( $query );
		foreach($results as $result){
			$customerIDs[] 	= $result->customer_id;
		}
		$count 	= count($customerIDs);
		if($count == 1){
			$customerIDs	= esc_sql($customerIDs);
			$query 			= "SELECT first_name, last_name FROM {$wpdb->prefix}bmify_customers WHERE id=".$customerIDs[0];
			$results 		= $wpdb->get_results( $query );
			if(!empty($results)){
				$html 		.= '<span><span class="full_name only_one">'.$results[0]->first_name.' '.$results[0]->last_name.'</span></span>';
			}
		}else if($count > 1){
			$customerIDs	= esc_sql($customerIDs);
			$query 			= "SELECT first_name, last_name FROM {$wpdb->prefix}bmify_customers WHERE id=".$customerIDs[0];
			$results 		= $wpdb->get_results( $query );
			if(!empty($results)){
				$html 		.= '<span><span class="full_name only_one">'.$results[0]->first_name.' '.$results[0]->last_name.'</span><span class="plus">+'.($count-1).'</span></span>';
			}
		}
		
			
		return $html;
    }
	
	
	
	/**
     * Save and Cancel for POPUP
	 * @since 1.0.0
     */
	public static function bookmifyPopupSaveSection($save = 'save',$cancel = 'cancel')
	{
		$saveText 		= esc_html__('Save','bookmify');
		$cancelText 	= esc_html__('Cancel','bookmify');
		if($save == 'generate'){
			$saveText 	= esc_html__('Generate','bookmify');
		}
		$html = '<div class="bookmify_be_popup_form_button">
					<a class="save" href="#">
						<span class="text">'.$saveText.'</span>
						<span class="save_process">
							<span class="ball"></span>
							<span class="ball"></span>
							<span class="ball"></span>
						</span>
					</a>
					<a class="cancel" href="#">'.$cancelText.'</a>
				</div>';
		return $html;
	}
	
	/**
     * Convert Seconds to Interval
	 * @since 1.0.0
     */
	
	public static function bookmifyNumberToDuration( $duration )
    {
        $duration = (int) $duration;
        $month_in_seconds = 30 * DAY_IN_SECONDS;
        $years   = (int)($duration / YEAR_IN_SECONDS);
        $months  = (int)(($duration % YEAR_IN_SECONDS) / $month_in_seconds);
        $weeks   = (int)((($duration % YEAR_IN_SECONDS) % $month_in_seconds) / WEEK_IN_SECONDS);
        $days    = (int)(((($duration % YEAR_IN_SECONDS) % $month_in_seconds) % WEEK_IN_SECONDS) / DAY_IN_SECONDS);
        $hours   = (int)(((($duration % YEAR_IN_SECONDS) % $month_in_seconds) % DAY_IN_SECONDS) / HOUR_IN_SECONDS);
        $minutes = (int)(((($duration % YEAR_IN_SECONDS) % $month_in_seconds) % HOUR_IN_SECONDS) / MINUTE_IN_SECONDS);

        $parts = array();

        if($years > 0){		$parts[] = sprintf( _n('%dyear', '%dyears', $years, 'bookmify'), $years );}
        if($months > 0){	$parts[] = sprintf( _n('%dmonth', '%dmonths', $months, 'bookmify'), $months );}
        if($weeks > 0){		$parts[] = sprintf( _n('%dweek', '%dweeks', $weeks, 'bookmify'), $weeks );}
        if($days > 0){		$parts[] = sprintf( _n('%dday', '%ddays', $days, 'bookmify'), $days );}
        if($hours > 0){		$parts[] = sprintf( _n('%dh', '%dh', $hours, 'bookmify'), $hours );}
        if($minutes > 0){	$parts[] = sprintf( _n('%dmin', '%dmin', $minutes, 'bookmify'), $minutes );}
		if($duration == 0){	$parts[] = sprintf( _n('%dmin', '%dmin', $minutes, 'bookmify'), $minutes );}

        return implode( ' ', $parts );
    }
	
	
	
	
	/**
     * Price Correction
	 * @since 1.0.0
     */
	public static function bookmifyPriceCorrection($price, $from = '')
	{
		if($from == 'frontend' && (int)$price == 0){
			return esc_html__('Free', 'bookmify');
		}
		if($from == 'coupons' && (float)$price == 0){
			return '-';
		}
		$position 		= get_option( 'bookmify_be_currency_position', 'left' );
		$format 		= get_option( 'bookmify_be_currency_format', 'USD' );
		$currencies     = self::bookmifyCurrencies();
		$price			= self::bookmifyPriceFormats($price);
		$symbol 		= '';
		$html 			= '';
		
		foreach($currencies as $key => $currency){
			if($format == $key ){
				$symbol = $currency['sb'];
			}
		}
		
		switch($position){
			default:
			case 'left': 	$html = $symbol.$price; 		break;
			case 'lspace': 	$html = $symbol.' '.$price; 	break;
			case 'right': 	$html = $price.$symbol; 		break;
			case 'rspace': 	$html = $price.' '.$symbol; 	break;
		}
		return $html;
	}
	
	
	
	/**
     * Price Formats
	 * @since 1.0.0
     */
	public static function bookmifyPriceFormats($price)
	{
		$format 		= get_option( 'bookmify_be_price_format', 'cd' );
		$decimal		= get_option( 'bookmify_be_price_decimal', '2' );
		
		switch($format){
			default:
			case 'cd': $html = number_format($price, $decimal); break;
			case 'dc': $html = number_format($price, $decimal, ',', '.'); break;
			case 'sd': $html = number_format($price, $decimal, '.', ' '); break;
			case 'sc': $html = number_format($price, $decimal, ',', ' '); break;
		}
		
		return $html;
	}
	
	
	/**
     * Currencies
	 * @since 1.0.0
     */
	public static function bookmifyGetIconPrice()
	{
		$format 		= get_option( 'bookmify_be_currency_format', 'USD' );
		$currencies     = self::bookmifyCurrencies();
		$symbol 		= '';
		
		foreach($currencies as $key => $currency){
			if($format == $key ){
				$symbol = $currency['sb'];
			}
		}
		return $symbol;
	}
	
	
	/**
     * Currencies
	 * @since 1.0.0
     */
	public static function bookmifyCurrencies()
	{
		$currencies = array(
			'AED' => array( 'sb' => '&#x62f;.&#x625;',  	'ct' => esc_html__( 'UAE dirham', 'bookmify' )),
			'AFN' => array( 'sb' => '&#x60b;',  			'ct' => esc_html__( 'Afghan afghani', 'bookmify' )),
			'ALL' => array( 'sb' => 'L',  					'ct' => esc_html__( 'Albanian lek', 'bookmify' )),
			'AMD' => array( 'sb' => 'AMD',  				'ct' => esc_html__( 'Armenian dram', 'bookmify' )),
			'ANG' => array( 'sb' => '&fnof;',  				'ct' => esc_html__( 'Netherlands Antillean guilder', 'bookmify' )),
			'AOA' => array( 'sb' => 'Kz',  					'ct' => esc_html__( 'Angolan kwanza', 'bookmify' )),
			'ARS' => array( 'sb' => '&#36;',  				'ct' => esc_html__( 'Argentine peso', 'bookmify' )),
			'AUD' => array( 'sb' => '&#36;',  				'ct' => esc_html__( 'Australian dollar', 'bookmify' )),
			'AWG' => array( 'sb' => 'Afl.',  				'ct' => esc_html__( 'Aruban florin', 'bookmify' )),
			'AZN' => array( 'sb' => 'AZN',  				'ct' => esc_html__( 'Azerbaijani manat', 'bookmify' )),
			'BAM' => array( 'sb' => 'KM',  					'ct' => esc_html__( 'B&H convertible mark', 'bookmify' )),
			'BBD' => array( 'sb' => '&#36;',  				'ct' => esc_html__( 'Barbadian dollar', 'bookmify' )),
			'BDT' => array( 'sb' => '&#2547;&nbsp;',  		'ct' => esc_html__( 'Bangladeshi taka', 'bookmify' )),
			'BGN' => array( 'sb' => '&#1083;&#1074;.',  	'ct' => esc_html__( 'Bulgarian lev', 'bookmify' )),
			'BHD' => array( 'sb' => '.&#x62f;.&#x628;',  	'ct' => esc_html__( 'Bahraini dinar', 'bookmify' )),
			'BIF' => array( 'sb' => 'Fr',  					'ct' => esc_html__( 'Burundian franc', 'bookmify' )),
			'BMD' => array( 'sb' => '&#36;',  				'ct' => esc_html__( 'Bermudian dollar', 'bookmify' )),
			'BND' => array( 'sb' => '&#36;',  				'ct' => esc_html__( 'Brunei dollar', 'bookmify' )),
			'BOB' => array( 'sb' => 'Bs.',  				'ct' => esc_html__( 'Bolivian boliviano', 'bookmify' )),
			'BRL' => array( 'sb' => '&#82;&#36;',  			'ct' => esc_html__( 'Brazilian real', 'bookmify' )),
			'BSD' => array( 'sb' => '&#36;',  				'ct' => esc_html__( 'Bahamian dollar', 'bookmify' )),
			'BTC' => array( 'sb' => '&#3647;',  			'ct' => esc_html__( 'Bitcoin', 'bookmify' )),
			'BTN' => array( 'sb' => 'Nu.',  				'ct' => esc_html__( 'Bhutanese ngultrum', 'bookmify' )),
			'BWP' => array( 'sb' => 'P',  					'ct' => esc_html__( 'Botswana pula', 'bookmify' )),
			'BYR' => array( 'sb' => 'Br',  					'ct' => esc_html__( 'Belarusian ruble (old)', 'bookmify' )),
			'BYN' => array( 'sb' => 'Br',  					'ct' => esc_html__( 'Belarusian ruble', 'bookmify' )),
			'BZD' => array( 'sb' => '&#36;',  				'ct' => esc_html__( 'Belize dollar', 'bookmify' )),
			'CAD' => array( 'sb' => '&#36;',  				'ct' => esc_html__( 'Canadian dollar', 'bookmify' )),
			'CDF' => array( 'sb' => 'Fr',  					'ct' => esc_html__( 'Congolese franc', 'bookmify' )),
			'CHF' => array( 'sb' => '&#67;&#72;&#70;',  	'ct' => esc_html__( 'Swiss franc', 'bookmify' )),
			'CLP' => array( 'sb' => '&#36;',  				'ct' => esc_html__( 'Chilean peso', 'bookmify' )),
			'CNY' => array( 'sb' => '&yen;',  				'ct' => esc_html__( 'Chinese yuan', 'bookmify' )),
			'COP' => array( 'sb' => '&#36;',  				'ct' => esc_html__( 'Colombian peso', 'bookmify' )),
			'CRC' => array( 'sb' => '&#x20a1;',  			'ct' => esc_html__( 'Costa Rican col&oacute;n', 'bookmify' )),
			'CUC' => array( 'sb' => '&#36;',  				'ct' => esc_html__( 'Cuban convertible peso', 'bookmify' )),
			'CUP' => array( 'sb' => '&#36;',  				'ct' => esc_html__( 'Cuban peso', 'bookmify' )),
			'CVE' => array( 'sb' => '&#36;',  				'ct' => esc_html__( 'Cape Verdean escudo', 'bookmify' )),
			'CZK' => array( 'sb' => '&#75;&#269;',  		'ct' => esc_html__( 'Czech koruna', 'bookmify' )),
			'DJF' => array( 'sb' => 'Fr',  					'ct' => esc_html__( 'Djiboutian franc', 'bookmify' )),
			'DKK' => array( 'sb' => 'DKK',  				'ct' => esc_html__( 'Danish krone', 'bookmify' )),
			'DOP' => array( 'sb' => 'RD&#36;',  			'ct' => esc_html__( 'Dominican peso', 'bookmify' )),
			'DZD' => array( 'sb' => '&#x62f;.&#x62c;',  	'ct' => esc_html__( 'Algerian dinar', 'bookmify' )),
			'EGP' => array( 'sb' => 'EGP',  				'ct' => esc_html__( 'Egyptian pound', 'bookmify' )),
			'ERN' => array( 'sb' => 'Nfk',  				'ct' => esc_html__( 'Eritrean nakfa', 'bookmify' )),
			'ETB' => array( 'sb' => 'Br',  					'ct' => esc_html__( 'Ethiopian birr', 'bookmify' )),
			'EUR' => array( 'sb' => '&euro;',  				'ct' => esc_html__( 'Euro', 'bookmify' )),
			'FJD' => array( 'sb' => '&#36;',  				'ct' => esc_html__( 'Fijian dollar', 'bookmify' )),
			'FKP' => array( 'sb' => '&pound;',  			'ct' => esc_html__( 'Falkland Islands pound', 'bookmify' )),
			'GBP' => array( 'sb' => '&pound;',  			'ct' => esc_html__( 'Pound sterling', 'bookmify' )),
			'GEL' => array( 'sb' => '&#x20be;',  			'ct' => esc_html__( 'Georgian lari', 'bookmify' )),
			'GGP' => array( 'sb' => '&pound;',  			'ct' => esc_html__( 'Guernsey pound', 'bookmify' )),
			'GHS' => array( 'sb' => '&#x20b5;',  			'ct' => esc_html__( 'Ghana cedi', 'bookmify' )),
			'GIP' => array( 'sb' => '&pound;',  			'ct' => esc_html__( 'Gibraltar pound', 'bookmify' )),
			'GMD' => array( 'sb' => 'D',  					'ct' => esc_html__( 'Gambian dalasi', 'bookmify' )),
			'GNF' => array( 'sb' => 'Fr',  					'ct' => esc_html__( 'Guinean franc', 'bookmify' )),
			'GTQ' => array( 'sb' => 'Q',  					'ct' => esc_html__( 'Guatemalan quetzal', 'bookmify' )),
			'GYD' => array( 'sb' => '&#36;',  				'ct' => esc_html__( 'Guyanese dollar', 'bookmify' )),
			'HKD' => array( 'sb' => '&#36;',  				'ct' => esc_html__( 'Hong Kong dollar', 'bookmify' )),
			'HNL' => array( 'sb' => 'L',  					'ct' => esc_html__( 'Honduran lempira', 'bookmify' )),
			'HRK' => array( 'sb' => 'Kn',  					'ct' => esc_html__( 'Croatian kuna', 'bookmify' )),
			'HTG' => array( 'sb' => 'G',  					'ct' => esc_html__( 'Haitian gourde', 'bookmify' )),
			'HUF' => array( 'sb' => '&#70;&#116;',  		'ct' => esc_html__( 'Hungarian forint', 'bookmify' )),
			'IDR' => array( 'sb' => 'Rp',  					'ct' => esc_html__( 'Indonesian rupiah', 'bookmify' )),
			'ILS' => array( 'sb' => '&#8362;',  			'ct' => esc_html__( 'Israeli new shekel', 'bookmify' )),
			'IMP' => array( 'sb' => '&pound;',  			'ct' => esc_html__( 'Manx pound', 'bookmify' )),
			'INR' => array( 'sb' => '&#8377;',  			'ct' => esc_html__( 'Indian rupee', 'bookmify' )),
			'IQD' => array( 'sb' => '&#x639;.&#x62f;',  	'ct' => esc_html__( 'Iraqi dinar', 'bookmify' )),
			'IRR' => array( 'sb' => '&#xfdfc;',  			'ct' => esc_html__( 'Iranian rial', 'bookmify' )),
			'IRT' => array( 'sb' => '&#x062A;&#x0648;&#x0645;&#x0627;&#x0646;','ct' => esc_html__( 'Iranian toman', 'bookmify' )),
			'ISK' => array( 'sb' => 'kr.',  				'ct' => esc_html__( 'Icelandic kr&oacute;na', 'bookmify' )),
			'JEP' => array( 'sb' => '&pound;',  			'ct' => esc_html__( 'Jersey pound', 'bookmify' )),
			'JMD' => array( 'sb' => '&#36;',  				'ct' => esc_html__( 'Jamaican dollar', 'bookmify' )),
			'JOD' => array( 'sb' => '&#x62f;.&#x627;',  	'ct' => esc_html__( 'Jordanian dinar', 'bookmify' )),
			'JPY' => array( 'sb' => '&yen;',  				'ct' => esc_html__( 'Japanese yen', 'bookmify' )),
			'KES' => array( 'sb' => 'KSh',  				'ct' => esc_html__( 'Kenyan shilling', 'bookmify' )),
			'KGS' => array( 'sb' => '&#x441;&#x43e;&#x43c;','ct' => esc_html__( 'Kyrgyzstani som', 'bookmify' )),
			'KHR' => array( 'sb' => '&#x17db;',  			'ct' => esc_html__( 'Cambodian riel', 'bookmify' )),
			'KMF' => array( 'sb' => 'Fr',  					'ct' => esc_html__( 'Comorian franc', 'bookmify' )),
			'KPW' => array( 'sb' => '&#x20a9;',  			'ct' => esc_html__( 'North Korean won', 'bookmify' )),
			'KRW' => array( 'sb' => '&#8361;',  			'ct' => esc_html__( 'South Korean won', 'bookmify' )),
			'KWD' => array( 'sb' => '&#x62f;.&#x643;',  	'ct' => esc_html__( 'Kuwaiti dinar', 'bookmify' )),
			'KYD' => array( 'sb' => '&#36;',  				'ct' => esc_html__( 'Cayman Islands dollar', 'bookmify' )),
			'KZT' => array( 'sb' => 'KZT',  				'ct' => esc_html__( 'Kazakhstani tenge', 'bookmify' )),
			'LAK' => array( 'sb' => '&#8365;',  			'ct' => esc_html__( 'Lao kip', 'bookmify' )),
			'LBP' => array( 'sb' => '&#x644;.&#x644;',  	'ct' => esc_html__( 'Lebanese pound', 'bookmify' )),
			'LKR' => array( 'sb' => '&#xdbb;&#xdd4;',  		'ct' => esc_html__( 'Sri Lankan rupee', 'bookmify' )),
			'LRD' => array( 'sb' => '&#36;',  				'ct' => esc_html__( 'Liberian dollar', 'bookmify' )),
			'LSL' => array( 'sb' => 'L',  					'ct' => esc_html__( 'Lesotho loti', 'bookmify' )),
			'LYD' => array( 'sb' => '&#x644;.&#x62f;',  	'ct' => esc_html__( 'Libyan dinar', 'bookmify' )),
			'MAD' => array( 'sb' => '&#x62f;.&#x645;.',  	'ct' => esc_html__( 'Moroccan dirham', 'bookmify' )),
			'MDL' => array( 'sb' => 'MDL',  				'ct' => esc_html__( 'Moldovan leu', 'bookmify' )),
			'MGA' => array( 'sb' => 'Ar',  					'ct' => esc_html__( 'Malagasy ariary', 'bookmify' )),
			'MKD' => array( 'sb' => '&#x434;&#x435;&#x43d;','ct' => esc_html__( 'Macedonian denar', 'bookmify' )),
			'MMK' => array( 'sb' => 'Ks',  					'ct' => esc_html__( 'Burmese kyat', 'bookmify' )),
			'MNT' => array( 'sb' => '&#x20ae;',  			'ct' => esc_html__( 'Mongolian t&ouml;gr&ouml;g', 'bookmify' )),
			'MOP' => array( 'sb' => 'P',  					'ct' => esc_html__( 'Macanese pataca', 'bookmify' )),
			'MRO' => array( 'sb' => 'UM',  					'ct' => esc_html__( 'Mauritanian ouguiya', 'bookmify' )),
			'MUR' => array( 'sb' => '&#x20a8;',  			'ct' => esc_html__( 'Mauritian rupee', 'bookmify' )),
			'MVR' => array( 'sb' => '.&#x783;',  			'ct' => esc_html__( 'Maldivian rufiyaa', 'bookmify' )),
			'MWK' => array( 'sb' => 'MK',  					'ct' => esc_html__( 'Malawian kwacha', 'bookmify' )),
			'MXN' => array( 'sb' => '&#36;',  				'ct' => esc_html__( 'Mexican peso', 'bookmify' )),
			'MYR' => array( 'sb' => '&#82;&#77;',  			'ct' => esc_html__( 'Malaysian ringgit', 'bookmify' )),
			'MZN' => array( 'sb' => 'MT',  					'ct' => esc_html__( 'Mozambican metical', 'bookmify' )),
			'NAD' => array( 'sb' => '&#36;',  				'ct' => esc_html__( 'Namibian dollar', 'bookmify' )),
			'NGN' => array( 'sb' => '&#8358;',  			'ct' => esc_html__( 'Nigerian naira', 'bookmify' )),
			'NIO' => array( 'sb' => '&#36;',  				'ct' => esc_html__( 'Nicaraguan c&oacute;rdoba', 'bookmify' )),
			'NOK' => array( 'sb' => '&#107;&#114;',  		'ct' => esc_html__( 'Norwegian krone', 'bookmify' )),
			'NPR' => array( 'sb' => '&#8360;',  			'ct' => esc_html__( 'Nepalese rupee', 'bookmify' )),
			'NZD' => array( 'sb' => '&#36;',  				'ct' => esc_html__( 'New Zealand dollar', 'bookmify' )),
			'OMR' => array( 'sb' => '&#x631;.&#x639;.',  	'ct' => esc_html__( 'Omani rial', 'bookmify' )),
			'PAB' => array( 'sb' => 'B/.',  				'ct' => esc_html__( 'Panamanian balboa', 'bookmify' )),
			'PEN' => array( 'sb' => 'S/.',  				'ct' => esc_html__( 'Peruvian nuevo sol', 'bookmify' )),
			'PGK' => array( 'sb' => 'K',  					'ct' => esc_html__( 'Papua New Guinean kina', 'bookmify' )),
			'PHP' => array( 'sb' => '&#8369;',  			'ct' => esc_html__( 'Philippine peso', 'bookmify' )),
			'PKR' => array( 'sb' => '&#8360;',  			'ct' => esc_html__( 'Pakistani rupee', 'bookmify' )),
			'PLN' => array( 'sb' => '&#122;&#322;',  		'ct' => esc_html__( 'Polish z&#x142;oty', 'bookmify' )),
			'PRB' => array( 'sb' => '&#x440;.',  			'ct' => esc_html__( 'Transnistrian ruble', 'bookmify' )),
			'PYG' => array( 'sb' => '&#8370;',  			'ct' => esc_html__( 'Paraguayan guaran&iacute;', 'bookmify' )),
			'QAR' => array( 'sb' => '&#x631;.&#x642;',  	'ct' => esc_html__( 'Qatari riyal', 'bookmify' )),
			'RMB' => array( 'sb' => '&yen;',  				'ct' => esc_html__( 'Chinese yuan', 'bookmify' )),
			'RON' => array( 'sb' => 'lei',  				'ct' => esc_html__( 'Romanian leu', 'bookmify' )),
			'RSD' => array( 'sb' => '&#x434;&#x438;&#x43d;.','ct' => esc_html__( 'Serbian dinar', 'bookmify' )),
			'RUB' => array( 'sb' => '&#8381;',  			'ct' => esc_html__( 'Russian ruble', 'bookmify' )),
			'RWF' => array( 'sb' => 'Fr',  					'ct' => esc_html__( 'Rwandan franc', 'bookmify' )),
			'SAR' => array( 'sb' => '&#x631;.&#x633;',  	'ct' => esc_html__( 'Saudi riyal', 'bookmify' )),
			'SBD' => array( 'sb' => '&#36;',  				'ct' => esc_html__( 'Solomon Islands dollar', 'bookmify' )),
			'SCR' => array( 'sb' => '&#x20a8;',  			'ct' => esc_html__( 'Seychellois rupee', 'bookmify' )),
			'SDG' => array( 'sb' => '&#x62c;.&#x633;.',  	'ct' => esc_html__( 'Sudanese pound', 'bookmify' )),
			'SEK' => array( 'sb' => '&#107;&#114;',  		'ct' => esc_html__( 'Swedish krona', 'bookmify' )),
			'SGD' => array( 'sb' => '&#36;',  				'ct' => esc_html__( 'Singapore dollar', 'bookmify' )),
			'SHP' => array( 'sb' => '&pound;',  			'ct' => esc_html__( 'Saint Helena pound', 'bookmify' )),
			'SLL' => array( 'sb' => 'Le',  					'ct' => esc_html__( 'Sierra Leonean leone', 'bookmify' )),
			'SOS' => array( 'sb' => 'Sh',  					'ct' => esc_html__( 'Somali shilling', 'bookmify' )),
			'SRD' => array( 'sb' => '&#36;',  				'ct' => esc_html__( 'Surinamese dollar', 'bookmify' )),
			'SSP' => array( 'sb' => '&pound;',  			'ct' => esc_html__( 'South Sudanese pound', 'bookmify' )),
			'STD' => array( 'sb' => 'Db',  					'ct' => esc_html__( 'S&atilde;o Tom&eacute; and Pr&iacute;ncipe dobra', 'bookmify' )),
			'SYP' => array( 'sb' => '&#x644;.&#x633;',  	'ct' => esc_html__( 'Syrian pound', 'bookmify' )),
			'SZL' => array( 'sb' => 'L',  					'ct' => esc_html__( 'Swazi lilangeni', 'bookmify' )),
			'THB' => array( 'sb' => '&#3647;',  			'ct' => esc_html__( 'Thai baht', 'bookmify' )),
			'TJS' => array( 'sb' => '&#x405;&#x41c;',  		'ct' => esc_html__( 'Tajikistani somoni', 'bookmify' )),
			'TMT' => array( 'sb' => 'm',  					'ct' => esc_html__( 'Turkmenistan manat', 'bookmify' )),
			'TND' => array( 'sb' => '&#x62f;.&#x62a;',  	'ct' => esc_html__( 'Tunisian dinar', 'bookmify' )),
			'TOP' => array( 'sb' => 'T&#36;',  				'ct' => esc_html__( 'Tongan pa&#x2bb;anga', 'bookmify' )),
			'TRY' => array( 'sb' => '&#8378;',  			'ct' => esc_html__( 'Turkish lira', 'bookmify' )),
			'TTD' => array( 'sb' => '&#36;',  				'ct' => esc_html__( 'Trinidad and Tobago dollar', 'bookmify' )),
			'TWD' => array( 'sb' => '&#78;&#84;&#36;',  	'ct' => esc_html__( 'New Taiwan dollar', 'bookmify' )),
			'TZS' => array( 'sb' => 'Sh',  					'ct' => esc_html__( 'Tanzanian shilling', 'bookmify' )),
			'UAH' => array( 'sb' => '&#8372;',  			'ct' => esc_html__( 'Ukrainian hryvnia', 'bookmify' )),
			'UGX' => array( 'sb' => 'UGX',  				'ct' => esc_html__( 'Ugandan shilling', 'bookmify' )),
			'USD' => array( 'sb' => '&#36;',  				'ct' => esc_html__( 'United States (US) dollar', 'bookmify' )),
			'UYU' => array( 'sb' => '&#36;',  				'ct' => esc_html__( 'Uruguayan peso', 'bookmify' )),
			'UZS' => array( 'sb' => 'UZS',  				'ct' => esc_html__( 'Uzbekistani som', 'bookmify' )),
			'VEF' => array( 'sb' => 'Bs F',  				'ct' => esc_html__( 'Venezuelan bol&iacute;var', 'bookmify' )),
			'VND' => array( 'sb' => '&#8363;',  			'ct' => esc_html__( 'Vietnamese &#x111;&#x1ed3;ng', 'bookmify' )),
			'VUV' => array( 'sb' => 'Vt',  					'ct' => esc_html__( 'Vanuatu vatu', 'bookmify' )),
			'WST' => array( 'sb' => 'T',  					'ct' => esc_html__( 'Samoan t&#x101;l&#x101;', 'bookmify' )),
			'XAF' => array( 'sb' => 'CFA',  				'ct' => esc_html__( 'Central African CFA franc', 'bookmify' )),
			'XCD' => array( 'sb' => '&#36;',  				'ct' => esc_html__( 'East Caribbean dollar', 'bookmify' )),
			'XOF' => array( 'sb' => 'CFA',  				'ct' => esc_html__( 'West African CFA franc', 'bookmify' )),
			'XPF' => array( 'sb' => 'Fr',  					'ct' => esc_html__( 'CFP franc', 'bookmify' )),
			'YER' => array( 'sb' => '&#xfdfc;',  			'ct' => esc_html__( 'Yemeni rial', 'bookmify' )),
			'ZAR' => array( 'sb' => '&#82;',  				'ct' => esc_html__( 'South African rand', 'bookmify' )),
			'ZMW' => array( 'sb' => 'ZK',  					'ct' => esc_html__( 'Zambian kwacha', 'bookmify' )),
		);
		
		return $currencies;
	}
	/**
     * Payment section action
	 * @since 1.1.7
     */
	public static function bookmifyPaymentSectionAction()
	{
		
		$array = array(
			'default' 	=> array( 'ct' => esc_html__( 'Enable section', 'bookmify' )),
			'dis_en' 	=> array( 'ct' => esc_html__( 'Disable section, enable total price', 'bookmify' )),
			'dis_dis' 	=> array( 'ct' => esc_html__( 'Disable section, disable total price', 'bookmify' )),
		);
		
		return $array;
	}
	
	/**
     * Minimum Times To Booking
	 * @since 1.0.0
     */
	public static function bookmifyMinTimeToBooking()
	{
		$array = array(
			'0' 	=> array( 'ct' => esc_html__( 'Disabled', 'bookmify' )),
			'0.25' 	=> array( 'ct' => esc_html__( '15 Minutes', 'bookmify' )),
			'0.5' 	=> array( 'ct' => esc_html__( '30 Minutes', 'bookmify' )),
			'0.75' 	=> array( 'ct' => esc_html__( '45 Minutes', 'bookmify' )),
			'1' 	=> array( 'ct' => esc_html__( '1 Hour', 'bookmify' )),
			'2' 	=> array( 'ct' => esc_html__( '2 Hours', 'bookmify' )),
			'3' 	=> array( 'ct' => esc_html__( '3 Hours', 'bookmify' )),
			'4' 	=> array( 'ct' => esc_html__( '4 Hours', 'bookmify' )),
			'5' 	=> array( 'ct' => esc_html__( '5 Hours', 'bookmify' )),
			'6' 	=> array( 'ct' => esc_html__( '6 Hours', 'bookmify' )),
			'7' 	=> array( 'ct' => esc_html__( '7 Hours', 'bookmify' )),
			'8' 	=> array( 'ct' => esc_html__( '8 Hours', 'bookmify' )),
			'9' 	=> array( 'ct' => esc_html__( '9 Hours', 'bookmify' )),
			'10' 	=> array( 'ct' => esc_html__( '10 Hours', 'bookmify' )),
			'11' 	=> array( 'ct' => esc_html__( '11 Hours', 'bookmify' )),
			'12' 	=> array( 'ct' => esc_html__( '12 Hours', 'bookmify' )),
			'24' 	=> array( 'ct' => esc_html__( '1 Day', 'bookmify' )),
			'48' 	=> array( 'ct' => esc_html__( '2 Days', 'bookmify' )),
			'72' 	=> array( 'ct' => esc_html__( '3 Days', 'bookmify' )),
			'96' 	=> array( 'ct' => esc_html__( '4 Days', 'bookmify' )),
			'120' 	=> array( 'ct' => esc_html__( '5 Days', 'bookmify' )),
			'144' 	=> array( 'ct' => esc_html__( '6 Days', 'bookmify' )),
			'168' 	=> array( 'ct' => esc_html__( '1 Week', 'bookmify' )),
			'336' 	=> array( 'ct' => esc_html__( '2 Weeks', 'bookmify' )),
			'504' 	=> array( 'ct' => esc_html__( '3 Weeks', 'bookmify' )),
			'672' 	=> array( 'ct' => esc_html__( '4 Weeks', 'bookmify' )),
		);
		
		return $array;
	}
	
	/**
     * Maximum Times To Booking
	 * @since 1.0.0
     */
	public static function bookmifyMaxTimeToBooking()
	{
		$array = array(
			'0' 	=> array( 'ct' => esc_html__( 'Disabled', 'bookmify' )),
			'1' 	=> array( 'ct' => esc_html__( '1 Month', 'bookmify' )),
			'2' 	=> array( 'ct' => esc_html__( '2 Months', 'bookmify' )),
			'3' 	=> array( 'ct' => esc_html__( '3 Months', 'bookmify' )),
			'4' 	=> array( 'ct' => esc_html__( '4 Months', 'bookmify' )),
			'5' 	=> array( 'ct' => esc_html__( '5 Months', 'bookmify' )),
			'6' 	=> array( 'ct' => esc_html__( '6 Months', 'bookmify' )),
			'7' 	=> array( 'ct' => esc_html__( '7 Months', 'bookmify' )),
			'8' 	=> array( 'ct' => esc_html__( '8 Months', 'bookmify' )),
			'9' 	=> array( 'ct' => esc_html__( '9 Months', 'bookmify' )),
			'10' 	=> array( 'ct' => esc_html__( '10 Months', 'bookmify' )),
			'11' 	=> array( 'ct' => esc_html__( '11 Months', 'bookmify' )),
			'12' 	=> array( 'ct' => esc_html__( '1 year', 'bookmify' )),
			'18' 	=> array( 'ct' => esc_html__( '1 year 6 months', 'bookmify' )),
			'24' 	=> array( 'ct' => esc_html__( '2 years', 'bookmify' )),
		);
		
		return $array;
	}
	
	/**
     * Time Slots
	 * @since 1.0.0
     */
	public static function bookmifyTimeInterval(){
		$array = array(
			'1' 	=> array( 'ct' => esc_html__( '1 Minute', 'bookmify' )),
			'2' 	=> array( 'ct' => esc_html__( '2 Minutes', 'bookmify' )),
			'3' 	=> array( 'ct' => esc_html__( '3 Minutes', 'bookmify' )),
			'4' 	=> array( 'ct' => esc_html__( '4 Minutes', 'bookmify' )),
			'5' 	=> array( 'ct' => esc_html__( '5 Minutes', 'bookmify' )),
			'10' 	=> array( 'ct' => esc_html__( '10 Minutes', 'bookmify' )),
			'15' 	=> array( 'ct' => esc_html__( '15 Minutes', 'bookmify' )),
			'20' 	=> array( 'ct' => esc_html__( '20 Minutes', 'bookmify' )),
			'30' 	=> array( 'ct' => esc_html__( '30 Minutes', 'bookmify' )),
			'45' 	=> array( 'ct' => esc_html__( '45 Minutes', 'bookmify' )),
			'60' 	=> array( 'ct' => esc_html__( '1 Hour', 'bookmify' )),
			'90' 	=> array( 'ct' => esc_html__( '1 Hour 30 Minutes', 'bookmify' )),
			'120' 	=> array( 'ct' => esc_html__( '2 Hours', 'bookmify' )),
			'180' 	=> array( 'ct' => esc_html__( '3 Hours', 'bookmify' )),
			'240' 	=> array( 'ct' => esc_html__( '4 Hours', 'bookmify' )),
			'300' 	=> array( 'ct' => esc_html__( '5 Hours', 'bookmify' )),
			'360' 	=> array( 'ct' => esc_html__( '6 Hours', 'bookmify' )),
			'480' 	=> array( 'ct' => esc_html__( '8 Hours', 'bookmify' )),
			'720' 	=> array( 'ct' => esc_html__( '12 Hours', 'bookmify' )),
		);
		
		return $array;
	}
	
	/**
     * X Time Before Appointment Notification
	 * @since 1.0.0
     */
	public static function bookmifyTimeBeforeAppointment()
	{
		$array = array(
			'1' 	=> array( 'ct' => esc_html__( '1h', 'bookmify' )),
			'2' 	=> array( 'ct' => esc_html__( '2h', 'bookmify' )),
			'3' 	=> array( 'ct' => esc_html__( '3h', 'bookmify' )),
			'4' 	=> array( 'ct' => esc_html__( '4h', 'bookmify' )),
			'5' 	=> array( 'ct' => esc_html__( '5h', 'bookmify' )),
			'6' 	=> array( 'ct' => esc_html__( '6h', 'bookmify' )),
			'7' 	=> array( 'ct' => esc_html__( '7h', 'bookmify' )),
			'8' 	=> array( 'ct' => esc_html__( '8h', 'bookmify' )),
			'9' 	=> array( 'ct' => esc_html__( '9h', 'bookmify' )),
			'10' 	=> array( 'ct' => esc_html__( '10h', 'bookmify' )),
			'11' 	=> array( 'ct' => esc_html__( '11h', 'bookmify' )),
			'12' 	=> array( 'ct' => esc_html__( '12h', 'bookmify' )),
			'24' 	=> array( 'ct' => esc_html__( '1day', 'bookmify' )),
			'48' 	=> array( 'ct' => esc_html__( '2days', 'bookmify' )),
			'72' 	=> array( 'ct' => esc_html__( '3days', 'bookmify' )),
			'96' 	=> array( 'ct' => esc_html__( '4days', 'bookmify' )),
			'120' 	=> array( 'ct' => esc_html__( '5days', 'bookmify' )),
			'144' 	=> array( 'ct' => esc_html__( '6days', 'bookmify' )),
			'168' 	=> array( 'ct' => esc_html__( '1week', 'bookmify' )),
			'336' 	=> array( 'ct' => esc_html__( '2weeks', 'bookmify' )),
			'504' 	=> array( 'ct' => esc_html__( '3weks', 'bookmify' )),
			'672' 	=> array( 'ct' => esc_html__( '4weeks', 'bookmify' )),
		);
		
		return $array;
	}
	
	/**
     * Front-end default appointment status
	 * @since 1.0.0
     */
	public static function bookmifyFrontEndAppointmentStatus()
	{
		$array = array(
			'approved' 	=> array( 'ct' => esc_html__( 'Approved', 'bookmify' )),
			'pending' 	=> array( 'ct' => esc_html__( 'Pending', 'bookmify' )),
		);
		
		return $array;
	}
	
	/**
     * Day Formats
	 * @since 1.0.0
     */
	public static function bookmifyDayFormats()
	{
		$dayFormats = array(
			'F d, Y' 	=> array( 'ct' => date_i18n( 'F d, Y' )),
			'd F, Y' 	=> array( 'ct' => date_i18n( 'd F, Y' )),
			'Y-m-d' 	=> array( 'ct' => date_i18n( 'Y-m-d' )),
			'm/d/y' 	=> array( 'ct' => date_i18n( 'm/d/y' )),
			'd/m/y' 	=> array( 'ct' => date_i18n( 'd/m/y' )),
		);
		
		return $dayFormats;
	}
	
	/**
     * Mail Services
	 * @since 1.0.0
     */
	public static function bookmifyMailServices()
	{
		$result = array(
			'php' 		=> array( 'ct' => esc_html__('PHP Mail', 'bookmify')),
			'wp' 		=> array( 'ct' => esc_html__('WP Mail', 'bookmify')),
			'smtp' 		=> array( 'ct' => esc_html__('SMTP', 'bookmify')),
		);
		
		return $result;
	}
	
	/**
     * Mail Services
	 * @since 1.0.0
     */
	public static function bookmifySMTPSecure()
	{
		$result = array(
			'disabled' 	=> array( 'ct' => esc_html__('Disabled', 'bookmify')),
			'ssl' 		=> array( 'ct' => esc_html__('SSL', 'bookmify')),
			'tls' 		=> array( 'ct' => esc_html__('TLS', 'bookmify')),
		);
		
		return $result;
	}
	
	/**
     * Time Formats
	 * @since 1.0.0
     */
	public static function bookmifyTimeFormats()
	{
		$timeFormats = array(
			'h:i a' 	=> array( 'ct' => date_i18n( 'h:i a' )),
			'h:i A' 	=> array( 'ct' => date_i18n( 'h:i A' )),
			'H:i' 		=> array( 'ct' => date_i18n( 'H:i' )),
		);
		
		return $timeFormats;
	}
	
	
	/**
     * WP HTML EDITOR
	 * @since 1.0.0
     */
	public static function bookmifyBeWpHtmlEditor($id, $content, $platform)
	{
		$editor_id 				= 'bookmify_be_tinymce_'.$id.'_'.$platform;	
		if($platform == 'email'){
			// Turn on the output buffer
			ob_start();
			
			$editor_settings 	= array('media_buttons' => false , 'textarea_rows' => 17 , 'teeny' =>true, 'quicktags' => false, 'editor_height'=>'300px'); 

			wp_editor( $content, $editor_id , $editor_settings);

			// Store the contents of the buffer in a variable
			$editor_contents 	= ob_get_clean();
		}else{
			$editor_contents 	= '<textarea id="'.$editor_id.'">'.$content.'</textarea>';
		}
			
		
		// Return the content you want to the calling function
		return $editor_contents;
	}
	
	// Date Converter (PHP to JS)
	public static function convertDateFormat($format, $jsFormat)
    {
        switch ($format) {
            case 'date': $phpFormat = get_option('bookmify_be_date_format', 'd F, Y'); break;
			case 'time': $phpFormat = get_option('bookmify_be_time_format', 'h:i a'); break;    
            default: $phpFormat = $format;     
        }

        switch($jsFormat){
            case 'momentFormat':
                $replacements = array(
                    'd' => 'DD',   	'\d' => '[d]',
                    'D' => 'ddd',  	'\D' => '[D]',
                    'j' => 'D',    	'\j' => 'j',
                    'l' => 'dddd', 	'\l' => 'l',
                    'N' => 'E',    	'\N' => 'N',
                    'S' => 'o',    	'\S' => '[S]',
                    'w' => 'e',    	'\w' => '[w]',
                    'z' => 'DDD',  	'\z' => '[z]',
                    'W' => 'W',    	'\W' => '[W]',
                    'F' => 'MMMM', 	'\F' => 'F',
                    'm' => 'MM',   	'\m' => '[m]',
                    'M' => 'MMM',  	'\M' => '[M]',
                    'n' => 'M',    	'\n' => 'n',
                    't' => '',     	'\t' => 't',
                    'L' => '',     	'\L' => 'L',
                    'o' => 'YYYY', 	'\o' => 'o',
                    'Y' => 'YYYY', 	'\Y' => 'Y',
                    'y' => 'YY',   	'\y' => 'y',
                    'a' => 'a',    	'\a' => '[a]',
                    'A' => 'A',    	'\A' => '[A]',
                    'B' => '',     	'\B' => 'B',
                    'g' => 'h',    	'\g' => 'g',
                    'G' => 'H',    	'\G' => 'G',
                    'h' => 'hh',   	'\h' => '[h]',
                    'H' => 'HH',   	'\H' => '[H]',
                    'i' => 'mm',   	'\i' => 'i',
                    's' => 'ss',   	'\s' => '[s]',
                    'u' => 'SSS',  	'\u' => 'u',
                    'e' => 'zz',   	'\e' => '[e]',
                    'I' => '',     	'\I' => 'I',
                    'O' => '',     	'\O' => 'O',
                    'P' => '',     	'\P' => 'P',
                    'T' => '',     	'\T' => 'T',
                    'Z' => '',     	'\Z' => '[Z]',
                    'c' => '',     	'\c' => 'c',
                    'r' => '',     	'\r' => 'r',
                    'U' => 'X',    	'\U' => 'U',
                    '\\' => '',
                );
                return strtr($phpFormat, $replacements);
			break;

            case 'datepickerFormat':
                $replacements = array(
                    // Day
                    'd' => 'dd', 	'\d' => '\'d\'',
                    'j' => 'd',  	'\j' => 'j',
                    'l' => 'DD', 	'\l' => 'l',
                    'D' => 'D',  	'\D' => '\'D\'',
                    'z' => 'o',  	'\z' => 'z',
                    'm' => 'mm', 	'\m' => '\'m\'',
                    'n' => 'm',  	'\n' => 'n',
                    'F' => 'MM', 	'\F' => 'F',
                    'Y' => 'yy', 	'\Y' => 'Y',
                    'y' => 'y',  	'\y' => '\'y\'',
                    'S' => '',   	'\S' => 'S',
                    'o' => 'yy', 	'\o' => '\'o\'',
                    '\\' => '',
                );
                return str_replace('\'\'', '', strtr($phpFormat, $replacements));
			break;
        }

        return $phpFormat;
    }
	
	public static function bookmifyBePaypalCheckout(){
		// paypal checkout
		$paypalCheckout				= 'off';
		$paypalSwitch 				= get_option('bookmify_be_paypal_switch', '');
		$sandboxMode 				= get_option('bookmify_be_paypal_sandbox_mode', 'on');
		$cuurencyFormat				= get_option('bookmify_be_currency_format', 'USD');
		$paypalClientID				= '';
		if($sandboxMode == 'on'){
			$paypalClientID 		= get_option('bookmify_be_paypal_client_id', '');
		}else{
			$paypalClientID 		= get_option('bookmify_be_paypal_client_id_live', '');
		}
		if($paypalClientID != '' && $paypalSwitch == 'on'){ // disconnect paypal checkout
			$availabeCurrencies 	= array('ARS', 'AUD', 'BRL', 'CAD', 'CHF', 'CZK', 'DKK', 'EUR', 'GBP', 'HKD', 'HUF', 'INR', 'ILS', 'JPY', 'MXN', 'MYR', 'NOK', 'NZD', 'PHP', 'PLN', 'RUB', 'SEK', 'SGD', 'THB', 'TWD', 'USD');
			if(in_array($cuurencyFormat, $availabeCurrencies)){
				$paypalCheckout 	= 'on';
			}
		}
		return $paypalCheckout;
	}
	
	public static function bookmifyBeStripeCheckout($key = 'p'){
		// stripe checkout
		$stripeCheckout				= 'off';
		$stripeSwitch 				= get_option('bookmify_be_stripe_switch', '');
		$cuurencyFormat				= get_option('bookmify_be_currency_format', 'USD');
		
		$sandboxMode 				= get_option('bookmify_be_stripe_test_mode', 'on');
		if($sandboxMode == 'on'){
			$stripePublishableKey	= get_option('bookmify_be_stripe_test_publishable_key', '');
		}else{
			$stripePublishableKey	= get_option('bookmify_be_stripe_publishable_key', '');
		}
		if($stripeSwitch == 'on' && $stripePublishableKey != ''){
			$availabeCurrencies 	= array('USD' , 'AED', 'AFN', 'ALL', 'AMD', 'ANG', 'AOA', 'ARS', 'AUD', 'AWG', 'AZN', 'BAM', 'BBD', 'BDT', 'BGN', 'BIF', 'BMD', 'BND', 'BOB', 'BRL', 'BSD', 'BWP', 'BZD', 'CAD', 'CDF', 'CHF', 'CLP', 'CNY', 'COP', 'CRC', 'CVE', 'CZK', 'DJF', 'DKK', 'DOP', 'DZD', 'EGP', 'ETB', 'EUR', 'FJD', 'FKP', 'GBP', 'GEL', 'GIP', 'GMD', 'GNF', 'GTQ', 'GYD', 'HKD', 'HNL', 'HRK', 'HTG', 'HUF', 'IDR', 'ILS', 'INR', 'ISK', 'JMD', 'JPY', 'KES', 'KGS', 'KHR', 'KMF', 'KRW', 'KYD', 'KZT', 'LAK', 'LBP', 'LKR', 'LRD', 'LSL', 'MAD', 'MDL', 'MGA', 'MKD', 'MMK', 'MNT', 'MOP', 'MRO', 'MUR', 'MVR', 'MWK', 'MXN', 'MYR', 'MZN', 'NAD', 'NGN', 'NIO', 'NOK', 'NPR', 'NZD', 'PAB', 'PEN', 'PGK', 'PHP', 'PKR', 'PLN', 'PYG', 'QAR', 'RON', 'RSD', 'RUB', 'RWF', 'SAR', 'SBD', 'SCR', 'SEK', 'SGD', 'SHP', 'SLL', 'SOS', 'SRD', 'STD', 'SZL', 'THB', 'TJS', 'TOP', 'TRY', 'TTD', 'TWD', 'TZS', 'UAH', 'UGX', 'UYU', 'UZS', 'VND', 'VUV', 'WST', 'XAF', 'XCD', 'XOF', 'XPF', 'YER', 'ZAR', 'ZMW');
			if(in_array($cuurencyFormat, $availabeCurrencies)){
				$stripeCheckout 	= 'on';
			}
		}
		if($key == 'p'){
			return $stripePublishableKey;
		}else{
			return $stripeCheckout;
		}
	}
	public static function bookmifyNotificationPlaceholders(){
		global $wpdb;
		$array = array(
			'appointment_date' 			=> array( 'ct' => esc_html__( 'Appointment Date', 'bookmify' )),
			'appointment_start_time' 	=> array( 'ct' => esc_html__( 'Appointment Start Time', 'bookmify' )),
//			'appointment_end_time' 		=> array( 'ct' => esc_html__( 'Appointment End Time', 'bookmify' )), // will be added
//			'appointment_notes' 		=> array( 'ct' => esc_html__( 'Appointment Notes', 'bookmify' )), // will be added
//			'appointment_approve_url' 	=> array( 'ct' => esc_html__( 'Appointment Approve URL', 'bookmify' )), // will be added
//			'appointment_cancel_url' 	=> array( 'ct' => esc_html__( 'Appointment Cancel URL', 'bookmify' )), // will be added
//			'category_name' 			=> array( 'ct' => esc_html__( 'Category Name', 'bookmify' )), // will be added
//			'customer_address' 			=> array( 'ct' => esc_html__( 'Customer address', 'bookmify' )), // will be added
			'customer_email' 			=> array( 'ct' => esc_html__( 'Customer email', 'bookmify' )),
			'customer_first_name' 		=> array( 'ct' => esc_html__( 'Customer First Name', 'bookmify' )),
			'customer_last_name' 		=> array( 'ct' => esc_html__( 'Customer Last Name', 'bookmify' )),
			'customer_full_name' 		=> array( 'ct' => esc_html__( 'Customer Full Name', 'bookmify' )),
			'customer_phone' 			=> array( 'ct' => esc_html__( 'Customer Phone', 'bookmify' )),
//			'customer_timezone' 		=> array( 'ct' => esc_html__( 'Customer Timezone', 'bookmify' )), // will be added
			'custom_fields' 			=> array( 'ct' => esc_html__( 'Custom Fields', 'bookmify' )),
			'company_address' 			=> array( 'ct' => esc_html__( 'Company Address', 'bookmify' )),
//			'company_logo' 				=> array( 'ct' => esc_html__( 'Company Logo', 'bookmify' )), // will be added
			'company_name' 				=> array( 'ct' => esc_html__( 'Company Name', 'bookmify' )),
			'company_phone' 			=> array( 'ct' => esc_html__( 'Company Phone', 'bookmify' )),
			'company_website' 			=> array( 'ct' => esc_html__( 'Company Website', 'bookmify' )),
			'employee_email' 			=> array( 'ct' => esc_html__( 'Employee email', 'bookmify' )),
			'employee_first_name' 		=> array( 'ct' => esc_html__( 'Employee First Name', 'bookmify' )),
			'employee_last_name' 		=> array( 'ct' => esc_html__( 'Employee Last Name', 'bookmify' )),
			'employee_full_name' 		=> array( 'ct' => esc_html__( 'Employee Full Name', 'bookmify' )),
			'employee_phone' 			=> array( 'ct' => esc_html__( 'Employee Phone', 'bookmify' )),
			'extras' 					=> array( 'ct' => esc_html__( 'Extras', 'bookmify' )),
//			'google_calendar_url' 		=> array( 'ct' => esc_html__( 'Google Calendar URL', 'bookmify' )), // will be added
//			'location_info' 			=> array( 'ct' => esc_html__( 'Location Info', 'bookmify' )), // will be added
			'location_name' 			=> array( 'ct' => esc_html__( 'Location Name', 'bookmify' )),
			'location_address' 			=> array( 'ct' => esc_html__( 'Location Address', 'bookmify' )),
			'number_of_people' 			=> array( 'ct' => esc_html__( 'Number Of People', 'bookmify' )),
			'service_duration' 			=> array( 'ct' => esc_html__( 'Service Duration', 'bookmify' )),
			'service_name' 				=> array( 'ct' => esc_html__( 'Service Name', 'bookmify' )),
			'total_price' 				=> array( 'ct' => esc_html__( 'Total Price', 'bookmify' )),
		);
		ksort($array);
		return $array;
	}
	
	public static function bookmifyBeNoItems($class = ''){
		$result = '';
		$result .= '<div class="bookmify_be_no_items '.$class.'">';
		$result .= 	'<div class="bookmify_be_no_items_in">';
		$result .= 		'<img class="bookmify_be_svg" src="'.BOOKMIFY_ASSETS_URL.'img/pencil.svg" alt="" />';
		$result .= 		'<span>'.esc_html__('No Results', 'bookmify').'</span>';
		$result .= 	'</div>';
		$result .= '</div>';
		return $result;
	}
	
	public static function checkForSender(){
		$senderEmail 	= get_option('bookmify_be_not_sender_email', '');
		$senderName 	= get_option('bookmify_be_not_sender_name', '');
		
		if($senderEmail != '' && $senderName != ''){
			$email 		= filter_var($senderEmail, FILTER_SANITIZE_EMAIL);

			// Validate e-mail
			if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
				return true;
			}else{
				return false;
			}
		}else{
			return false;
		}
	}
	
	/* since bookmify v1.2.0 */
	public static function checkForSenderSMS($userID,$role){
		global $wpdb;
		$accountSID 	= get_option('bookmify_be_twilio_account_sid', '');
		$authToken	 	= get_option('bookmify_be_twilio_auth_token', '');
		$number		 	= get_option('bookmify_be_twilio_number', '');
		
		if($accountSID != '' && $authToken != '' && $number != ''){
			$query 		= "SELECT phone FROM {$wpdb->prefix}bmify_".$role."s WHERE id=".$userID;
			$results	= $wpdb->get_results( $query, OBJECT  );
			$phone		= $results[0]->phone;
			
			if($phone == '' || $phone == NULL){return array(0,'');}else{ return array(1,$phone);}
		}
		else{return array(0,'');}
	}
	
	/* since bookmify v1.1.7 */
	public static function enabledPaymentMethods(){
		$localPayment 	= get_option( 'bookmify_be_local_payment', 'on' );
		$paypalON 		= Helper::bookmifyBePaypalCheckout();
		$stripeON 		= Helper::bookmifyBeStripeCheckout('');
		
		$activePM		= 0;
		
		if($localPayment 	== 'on'){$activePM++;}
		if($paypalON 		== 'on'){$activePM--;}
		if($stripeON 		== 'on'){$activePM--;}
		
		return $activePM;
	}
	
	/* since bookmify v1.2.0 */
	public static function localize(){
		$result			= 'en-GB';
		$localize 		= get_locale();
		$localize		= substr($localize, 0, 2);
		$array 			= array('af' , 'ar', 'ar-DZ', 'az', 'be', 'bg', 'bg', 'ca', 'cs', 'cy-GB', 'da', 'de', 'el', 'en-AU', 'en-GB', 'en-NZ', 'eo', 'es', 'et', 'eu', 'fa', 'fi', 'fo', 'fr', 'fr-CA', 'fr-CH', 'gl', 'he', 'hi', 'hr', 'hu', 'hy', 'id', 'is', 'it', 'it-CH', 'ja', 'ka', 'kk', 'km', 'ko', 'ky', 'lb', 'lt', 'lv', 'mk', 'ml', 'ms', 'nb', 'nl', 'nl-BE', 'nn', 'no', 'pl', 'pt', 'pt-BR', 'rm', 'ro', 'ru', 'sk', 'sl', 'sq', 'sr', 'sr-SR', 'sv', 'ta', 'th', 'tj', 'tr', 'uk', 'vi', 'zh-CN', 'zh-HK', 'zh-TW');
		if(in_array($localize, $array)){
			$result 	= $localize;
		}
		return $result;
	}
	/* since bookmify v1.2.3 */
	public static function localizeMin(){
		$result			= 'en-gb';
		$localize 		= get_locale();
		$localize		= substr($localize, 0, 2);
		$array 			= array('af' , 'ar', 'ar-dz', 'az', 'be', 'bg', 'bg', 'ca', 'cs', 'cy-gb', 'da', 'de', 'el', 'en-au', 'en-gb', 'en-nz', 'eo', 'es', 'et', 'eu', 'fa', 'fi', 'fo', 'fr', 'fr-ca', 'fr-ch', 'gl', 'he', 'hi', 'hr', 'hu', 'hy', 'id', 'is', 'it', 'it-vh', 'ja', 'ka', 'kk', 'km', 'ko', 'ky', 'lb', 'lt', 'lv', 'mk', 'ml', 'ms', 'nb', 'nl', 'nl-be', 'nn', 'no', 'pl', 'pt', 'pt-br', 'rm', 'ro', 'ru', 'sk', 'sl', 'sq', 'sr', 'sr-sr', 'sv', 'ta', 'th', 'tj', 'tr', 'uk', 'vi', 'zh-cn', 'zh-hk', 'zh-tw');
		if(in_array($localize, $array)){
			$result 	= $localize;
		}
		return $result;
	}
	
	/* since bookmify v1.3.0 */
	public static function allAppointmentsToRemove($startDate,$endDate,$employeeID,$serviceID,$peopleCount,$bookingDuration){
		global $wpdb;
		// output variables
		$additionalArray 		= array();
		$customlist 			= array();
		$additionalTimeSlots 	= array();
		$appointmentArray 		= array();
		$startDate				= date("Y-m-d H:i:s", strtotime($startDate));
		$endDate				= date("Y-m-d H:i:s", strtotime($endDate));
		// here goes query
		$select	 				= "SELECT 
										a.start_date appStartDate,
										a.end_date appEndDate,
										a.service_id appServiceID,
										es.capacity_min serviceCapacityMin,
										es.capacity_max serviceCapacityMax,
										s.buffer_before serviceBufferBefore,
										s.buffer_after serviceBufferAfter,
										GROUP_CONCAT(ca.customer_id ORDER BY ca.id) customerIDs,
										GROUP_CONCAT(ca.number_of_people ORDER BY ca.id) customerPeopleCounts,
										GROUP_CONCAT(ca.status ORDER BY ca.id) customerStatuses

									FROM 	   	   {$wpdb->prefix}bmify_appointments a 
										INNER JOIN {$wpdb->prefix}bmify_customer_appointments ca 			ON ca.appointment_id = a.id 
										INNER JOIN {$wpdb->prefix}bmify_services s 							ON a.service_id = s.id 
										INNER JOIN {$wpdb->prefix}bmify_employee_services es 				ON a.service_id = es.service_id AND a.employee_id = es.employee_id

									WHERE a.employee_id=".$employeeID." AND a.start_date >= '".$startDate."' AND a.start_date <= '".$endDate."' AND a.status in ('pending', 'approved')";
		$Querify  		= new Querify( $select, 'appointment' );
		$appointments   = $Querify->getData( 99999999 );
		
		
		for( $i = 0; $i < count( $appointments->data ); $i++ ){
			$day 	= date('Y-m-d', strtotime($appointments->data[$i]->appStartDate));
			if(!isset($customlist[$day])){$customlist[$day] = array();}
			$customlist[$day][] = $appointments->data[$i];
		}
		foreach($customlist as $key => $appointmentss){
			$additionalTimeSlots 	= array();
			foreach($appointmentss as $appointment){
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
					$bufferBefore				= $appointment->serviceBufferBefore / 60;
					$bufferAfter				= $appointment->serviceBufferAfter / 60;
					$startDateInMinutes 		= HelperTime::timeToMinutes($appointment->appStartDate);
					$endDateInMinutes 			= HelperTime::timeToMinutes($appointment->appEndDate);
					$startAppointment 			= $startDateInMinutes - $bufferBefore;
					$endAppointment				= $endDateInMinutes + $bufferAfter;
					$appointmentArray[$key][] 	= array($startAppointment,$endAppointment);
					if($hasSlot == 1){
						if(($endDateInMinutes - $startDateInMinutes) >= ($bookingDuration / 60)){
							$additionalTimeSlots[] = $startDateInMinutes;
						}
					}
				}
			}
			$additionalArray[$key] 			= $additionalTimeSlots;
		}
		
		
		return array($appointmentArray,$additionalArray);
	}
	
	/* since bookmify v1.3.0 */
	public static function allEventsToRemoveGoogleCalendar($startDate,$endDate,$employeeID,$summaryDuration){
		global $wpdb;
		$googleData 	= HelperEmployees::getGoogleData($employeeID);
		$accessToken	= '';
		$calID          = '';
		$events			= array();
		if($googleData != NULL){
			$googleData 	= json_decode(stripslashes($googleData), true);
			$accessToken 	= $googleData['accessToken'];
			$calID 			= $googleData['calendarID'];

			$google 		= new GoogleCalendarProject();
			if($accessToken != ''){
				$events		= $google->getGoogleEventsFromToSimple($startDate,$endDate,$employeeID);
			}
		}
		return $events;
	}
}

