<?php
namespace Bookmify;



// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {exit; }


/**
 * Class Helper Calendar
 */
class HelperCalendar{
	
	
	
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
			$html  .= '<div data-id="'.$result->id.'">'.Helper::titleDecryption( $result->title ).'</div>';
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
		$query 		= "SELECT first_name, last_name, id FROM {$wpdb->prefix}bmify_employees ORDER BY first_name, last_name, id";
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
		
		$html = '<div class="bookmify_be_calendars_filter" data-filter-status="">
						<div class="bookmify_be_filter_wrap">
							<div class="bookmify_be_filter">
								<div class="bookmify_be_row">

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
}

