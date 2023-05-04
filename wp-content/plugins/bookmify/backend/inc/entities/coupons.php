<?php
namespace Bookmify;

use Bookmify\Helper;
use Bookmify\HelperAdmin;
use Bookmify\HelperCoupons;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


class Coupons{

	const PAGE_ID = 'bookmify_coupons';
	
	private $per_page;
	/**
	 * @since 1.0.0
	 * @access public
	*/
	public function __construct() {
		$this->assignValToVar();
		add_action( 'admin_menu', [ $this, 'registerAdminMenu' ], 20 );
		
		
		
		
		add_action( 'wp_ajax_ajaxQueryEditCoupon', [$this, 'ajaxQueryEditCoupon'] );
		add_action( 'wp_ajax_ajaxQueryDeleteCoupon', [$this, 'ajaxQueryDeleteCoupon'] );
		add_action( 'wp_ajax_ajaxQueryInsertOrUpdateCoupon', [$this, 'ajaxQueryInsertOrUpdateCoupon'] );
		
	}
	
	public function assignValToVar(){
		$this->per_page = get_option('bookmify_be_coupons_pp', 10);
	}
	
	/**
	 * @since 1.0.0
	 * @access public
	*/
	public function registerAdminMenu() {

		add_submenu_page(
			BOOKMIFY_MENU,
			esc_html__( 'Coupons', 'bookmify' ),
			esc_html__( 'Coupons', 'bookmify' ),
			'bookmify_be_read_coupons',
			self::PAGE_ID,
			[ $this, 'displayCouponsPage' ]
		);
	}
	
	public function displayCouponsPage() {
		global $wpdb;

		echo HelperAdmin::bookmifyAdminContentStart();
		?>
		
		<div class="bookmify_be_content_wrap bookmify_be_coupons_page">
			<div class="bookmify_be_page_title">
				<h3><?php echo esc_html($this->get_page_title()); ?><span class="count"><?php echo Helper::bookmifyItemsCount('coupons');?></span></h3>
				<div class="bookmify_be_add_new_item bookmify_be_add_new_coupon">
					<a href="#" class="bookmify_button add_new bookmify_add_new_button">
						<span class="text"><?php esc_html_e('Add New Coupon','bookmify');?></span>
						<span class="plus">
							<span class="icon"></span>
							<span class="bookmify_be_loader small">
								<span class="loader_process">
									<span class="ball"></span>
									<span class="ball"></span>
									<span class="ball"></span>
								</span>
							</span>
						</span>
					</a>
				</div>
			</div>
			<div class="bookmify_be_page_content">

				<div class="bookmify_be_coupons">
					
					<!-- Coupon Header -->
					<div class="bookmify_be_coupons_header">
						<div class="bookmify_be_coupons_header_in">
							<span class="list_title"><?php esc_html_e('Title', 'bookmify');?></span>
							<span class="list_code"><?php esc_html_e('Code', 'bookmify');?></span>
							<span class="list_discount"><?php esc_html_e('Discount', 'bookmify');?></span>
							<span class="list_deduction"><?php esc_html_e('Deduction', 'bookmify');?></span>
							<span class="list_limit"><?php esc_html_e('Used / Limit', 'bookmify');?></span>
							<span class="list_sdate"><?php esc_html_e('Start Date', 'bookmify');?></span>
							<span class="list_edate"><?php esc_html_e('End Date', 'bookmify');?></span>
							<span class="list_status"><?php esc_html_e('Status', 'bookmify');?></span>
						</div>
					</div>
					<!-- /Coupon Header -->
					
					<!-- Coupon List -->
					<div class="bookmify_be_coupons_list">
						<?php echo wp_kses_post($this->couponsList()); ?>
					</div>
					<!-- /Coupon Header -->
					
				</div>
				
			</div>
			<?php echo Helper::clonableFormCoupon(); ?>
		</div>
		<?php echo HelperAdmin::bookmifyAdminContentEnd();
		
	}
	

	/*
	 * @since 1.3.6
	 * @access public
	*/
	public function couponsList(){
		global $wpdb;
		HelperCoupons::clearExpiredCoupons();
		$query 			= "SELECT * FROM {$wpdb->prefix}bmify_coupons ORDER BY title,id";
		$results 		= $wpdb->get_results( $query);
		

		$html 			= '<div class="bookmify_be_list coupon_list">';
		if(count($results) == 0){
			$html .= Helper::bookmifyBeNoItems();
		}
		foreach($results as $result){
			$stat				= $result->status;
			$couponID			= $result->id;
			$couponTitle		= $result->title;
			$couponCode			= $result->code;
			$couponDiscount		= $result->discount;
			$discountActive	 	= 'active';
			$deductionActive	= 'active';
			if($couponDiscount == 0){
				$couponDiscount = '-';
				$discountActive = 'disabled';
			}else{
				$couponDiscount .= '%';
				$deductionActive = 'disabled';
			}
			$couponDeduction	= Helper::bookmifyPriceCorrection($result->deduction,'coupons');
			$couponULimit		= $result->usage_limit;
			$couponUsed			= HelperCoupons::getUsedCouponsCount($couponID);
			$couponStartDate	= $result->date_limit_start;
			$couponEndDate		= $result->date_limit_end;
			if($couponStartDate == '' || $couponStartDate == NULL || $couponStartDate == '0000-00-00 00:00:00'){
				$couponStartDate	= esc_html__('No limit', 'bookmify');
				$couponEndDate		= esc_html__('No limit', 'bookmify');
			}else{
				$couponStartDate	= date_i18n(get_option('bookmify_be_date_format', 'd F, Y'), strtotime($couponStartDate));
				$couponEndDate		= date_i18n(get_option('bookmify_be_date_format', 'd F, Y'), strtotime($couponEndDate));
			}
			switch($stat){
				case 'active': 	$status = esc_html__('open', 'bookmify'); 	break;
				default: 		$status = esc_html__('closed', 'bookmify'); break;
			}
			$html .= '<div class="bookmify_be_list_item coupon_item" data-coupon-id="'.$couponID.'">
						<div class="bookmify_be_list_item_in">
							<div class="bookmify_be_list_item_header">
								<div class="header_in">
									<div class="info_holder">
										<div class="info_in">
											<span class="coupon_title">'.$couponTitle.'</span>
											<span class="coupon_code">'.$couponCode.'</span>
											<span class="coupon_discount '.$discountActive.'">'.$couponDiscount.'</span>
											<span class="coupon_deduction '.$deductionActive.'" data-active="">'.$couponDeduction.'</span>
											<span class="coupon_limit">'.$couponUsed.' / '.$couponULimit.'</span>
											<span class="coupon_sdate">'.$couponStartDate.'</span>
											<span class="coupon_edate">'.$couponEndDate.'</span>
											<span class="coupon_status"><span class="'.$stat.'"><span>'.$status.'</span></span></span>
										</div>
									</div>

									<div class="buttons_holder">
										<div class="btn_item btn_edit">
											<a href="#" class="bookmify_be_edit">
												<img class="bookmify_be_svg edit" src="'.BOOKMIFY_ASSETS_URL.'img/edit.svg" alt="" />
												<img class="bookmify_be_svg checked" src="'.BOOKMIFY_ASSETS_URL.'img/checked.svg" alt="" />
												<span class="bookmify_be_loader small">
													<span class="loader_process">
														<span class="ball"></span>
														<span class="ball"></span>
														<span class="ball"></span>
													</span>
												</span>
											</a>
										</div>
										<div class="btn_item">
											<a href="#" class="bookmify_be_delete" data-entity-id="'.$couponID.'">
												<img class="bookmify_be_svg" src="'.BOOKMIFY_ASSETS_URL.'img/delete.svg" alt="" />
												<span class="bookmify_be_loader small">
													<span class="loader_process">
														<span class="ball"></span>
														<span class="ball"></span>
														<span class="ball"></span>
													</span>
												</span>
											</a>
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
	/* since bookmify v1.3.6 */
	public function ajaxQueryInsertOrUpdateCoupon(){
		global $wpdb;
		$isAjaxCall 			= false;
		$error 					= '';
		
		// **************************************************************************************************************************
		// UPDATE 
		// **************************************************************************************************************************
		if ($_POST['couponID'] != '') {
			$couponID 				= (int)$_POST['couponID'];
			$couponTitle 			= esc_sql($_POST['couponTitle']);
			$couponCode 			= esc_sql($_POST['couponCode']);
			$couponDiscount 		= esc_sql($_POST['couponDiscount']);
			$couponDeduction 		= esc_sql($_POST['couponDeduction']);
			$couponLimit 			= esc_sql($_POST['couponLimit']);
			$couponDisDecProp 		= $_POST['couponDisDecProp'];
			$couponDateProp 		= $_POST['couponDateProp'];
			$dateRange 				= esc_sql($_POST['dateRange']);
			$couponInfo 			= esc_sql($_POST['couponInfo']);
				
			$query 					= "SELECT * FROM {$wpdb->prefix}bmify_coupons WHERE code='".$couponCode."' AND status='active' AND id<>".$couponID;
			$results 				= $wpdb->get_results( $query, OBJECT  );
			$resultsCount			= count($results);
			
			// coupon date limit
			$status					= 'active';
			if($couponDateProp == 'true'){
				$couponStartDate 	= $dateRange[0];
				$couponEndDate 		= $dateRange[1];
				$couponStartDate	= date("Y-m-d H:i:s", strtotime($couponStartDate));
				$couponEndDate		= date("Y-m-d H:i:s", strtotime($couponEndDate));
				if(date('Y-m-d H:i:s') > $couponEndDate){
					$status			= 'overdue';
				}
			}else{
				$couponStartDate 	= NULL;
				$couponEndDate 		= NULL;
			}
			// coupon discount OR deduction
			if($couponDisDecProp == 'true'){
				$couponDiscount		= 0;
			}else{
				$couponDeduction	= 0;
			}
			
			if($resultsCount == 0){
				add_filter( 'query', [$this,'bookmify_let_insert_null'] );
				$wpdb->query($wpdb->prepare( "UPDATE {$wpdb->prefix}bmify_coupons SET title=%s, code=%s, discount=%d, deduction=%f, usage_limit=%d, date_limit_start=%s, date_limit_end=%s, status=%s, info=%s WHERE id=%d", $couponTitle, $couponCode, $couponDiscount, $couponDeduction, $couponLimit, $couponStartDate, $couponEndDate, $status, $couponInfo, $couponID));
				remove_filter( 'query', [$this,'bookmify_let_insert_null'] );
			}else{
				$error 				= 'yes';
			}
		
		}else{
			$couponTitle 			= esc_sql($_POST['couponTitle']);
			$couponCode 			= esc_sql($_POST['couponCode']);
			$couponDiscount 		= esc_sql($_POST['couponDiscount']);
			$couponDeduction 		= esc_sql($_POST['couponDeduction']);
			$couponLimit 			= esc_sql($_POST['couponLimit']);
			$couponDisDecProp 		= $_POST['couponDisDecProp'];
			$couponDateProp 		= $_POST['couponDateProp'];
			$dateRange 				= esc_sql($_POST['dateRange']);
			$couponInfo 			= esc_sql($_POST['couponInfo']);
			
			$query 					= "SELECT * FROM {$wpdb->prefix}bmify_coupons WHERE code='".$couponCode."' AND status='active'";
			$results 				= $wpdb->get_results( $query, OBJECT );
			$resultsCount			= count($results);
			
			// coupon date limit
			$status					= 'active';
			if($couponDateProp == 'true'){
				$couponStartDate 	= $dateRange[0];
				$couponEndDate 		= $dateRange[1];
				$couponStartDate	= date("Y-m-d H:i:s", strtotime($couponStartDate));
				$couponEndDate		= date("Y-m-d H:i:s", strtotime($couponEndDate));
				if(date('Y-m-d H:i:s') > $couponEndDate){
					$status			= 'overdue';
				}
			}else{
				$couponStartDate 	= NULL;
				$couponEndDate 		= NULL;
			}
			// coupon discount OR deduction
			if($couponDisDecProp == 'true'){
				$couponDiscount		= 0;
			}else{
				$couponDeduction	= 0;
			}
			
			if($resultsCount == 0){
				add_filter( 'query', [$this,'bookmify_let_insert_null'] );
				$wpdb->query($wpdb->prepare( "INSERT INTO {$wpdb->prefix}bmify_coupons (title, code, discount, deduction, usage_limit, date_limit_start, date_limit_end, status, info) VALUES (%s, %s, %d, %f, %d, %s, %s, %s, %s)", $couponTitle, $couponCode, $couponDiscount, $couponDeduction, $couponLimit, $couponStartDate, $couponEndDate, $status, $couponInfo));
				remove_filter( 'query', [$this,'bookmify_let_insert_null'] );
			}else{
				$error 				= 'yes';
			}
		}
		
		
		$isAjaxCall = true;
		$html		= '';
		$query 		= "SELECT * FROM {$wpdb->prefix}bmify_coupons ORDER BY title,id";
		$results 	= $wpdb->get_results( $query);

		$html 		= '<div class="bookmify_be_list coupon_list">';
		if(count($results) == 0){
			$html .= Helper::bookmifyBeNoItems();
		}
		foreach($results as $result){
			$stat				= $result->status;
			$couponID			= $result->id;
			$couponTitle		= $result->title;
			$couponCode			= $result->code;
			$couponDiscount		= $result->discount;
			$discountActive	 	= 'active';
			$deductionActive	= 'active';
			if($couponDiscount == 0){
				$couponDiscount = '-';
				$discountActive = 'disabled';
			}else{
				$couponDiscount .= '%';
				$deductionActive = 'disabled';
			}
			$couponDeduction	= Helper::bookmifyPriceCorrection($result->deduction,'coupons');
			$couponULimit		= $result->usage_limit;
			$couponUsed			= HelperCoupons::getUsedCouponsCount($couponID);
			$couponStartDate	= $result->date_limit_start;
			$couponEndDate		= $result->date_limit_end;
			if($couponStartDate == '' || $couponStartDate == NULL || $couponStartDate == '0000-00-00 00:00:00'){
				$couponStartDate	= esc_html__('No limit', 'bookmify');
				$couponEndDate		= esc_html__('No limit', 'bookmify');
			}else{
				$couponStartDate	= date_i18n(get_option('bookmify_be_date_format', 'd F, Y'), strtotime($couponStartDate));
				$couponEndDate		= date_i18n(get_option('bookmify_be_date_format', 'd F, Y'), strtotime($couponEndDate));
			}
			switch($stat){
				case 'active': 	$status = esc_html__('open', 'bookmify'); 	break;
				default: 		$status = esc_html__('closed', 'bookmify'); break;
			}
			$html .= '<div class="bookmify_be_list_item coupon_item bookmify_be_animated" data-coupon-id="'.$couponID.'">
						<div class="bookmify_be_list_item_in">
							<div class="bookmify_be_list_item_header">
								<div class="header_in">
									<div class="info_holder">
										<div class="info_in">
											<span class="coupon_title">'.$couponTitle.'</span>
											<span class="coupon_code">'.$couponCode.'</span>
											<span class="coupon_discount '.$discountActive.'">'.$couponDiscount.'</span>
											<span class="coupon_deduction '.$deductionActive.'">'.$couponDeduction.'</span>
											<span class="coupon_limit">'.$couponUsed.' / '.$couponULimit.'</span>
											<span class="coupon_sdate">'.$couponStartDate.'</span>
											<span class="coupon_edate">'.$couponEndDate.'</span>
											<span class="coupon_status"><span class="'.$stat.'"><span>'.$status.'</span></span></span>
										</div>
									</div>

									<div class="buttons_holder">
										<div class="btn_item btn_edit">
											<a href="#" class="bookmify_be_edit">
												<img class="bookmify_be_svg edit" src="'.BOOKMIFY_ASSETS_URL.'img/edit.svg" alt="" />
												<img class="bookmify_be_svg checked" src="'.BOOKMIFY_ASSETS_URL.'img/checked.svg" alt="" />
												<span class="bookmify_be_loader small">
													<span class="loader_process">
														<span class="ball"></span>
														<span class="ball"></span>
														<span class="ball"></span>
													</span>
												</span>
											</a>
										</div>
										<div class="btn_item">
											<a href="#" class="bookmify_be_delete" data-entity-id="'.$couponID.'">
												<img class="bookmify_be_svg" src="'.BOOKMIFY_ASSETS_URL.'img/delete.svg" alt="" />
												<span class="bookmify_be_loader small">
													<span class="loader_process">
														<span class="ball"></span>
														<span class="ball"></span>
														<span class="ball"></span>
													</span>
												</span>
											</a>
										</div>
									</div>
								</div>
							</div>
								
						</div>
					</div>';

		}

		$html .= '</div>';
			
		$buffy = '';
		if ( $html != NULL ) {
			$buffy .= $html; 
		}

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
		$buffy = preg_replace($search, $replace, $buffy);

		$buffyArray = array(
			'bookmify_be_data' 		=> $buffy,
			'error' 				=> $error,
			'number'				=> Helper::bookmifyItemsCount('coupons'),
			'asd'					=> $couponDateProp,
			'dsa'					=> $couponStartDate,
			'xxx'					=> $couponEndDate,
			'results'				=> $resultsCount,
		);

		if ( true === $isAjaxCall ){die(json_encode($buffyArray));} 
		else{return json_encode($buffyArray);}
	}
	
	
	/**
	 * @since 1.3.6
	 * @access public
	*/
	public function bookmify_let_insert_null( $query )
	{
		return str_ireplace( "'NULL'", "NULL", $query ); 
	}

	
	
	
	/**
	 * @since 1.3.6
	 * @access public
	*/
	public function ajaxQueryDeleteCoupon(){
		global $wpdb;
		$id 			= '';
		$isAjaxCall 	= false;
		if(!empty($_POST['id'])){
			$isAjaxCall = true;
			$id 		= esc_sql($_POST['id']);
//			$wpdb->query($wpdb->prepare( "DELETE FROM {$wpdb->prefix}bmify_coupons_used WHERE coupon_id=%d", $id));
			$wpdb->query($wpdb->prepare( "DELETE FROM {$wpdb->prefix}bmify_coupons WHERE id=%d", $id));
			
			
			$query 			= "SELECT * FROM {$wpdb->prefix}bmify_coupons ORDER BY title,id";
		
			$results 		= $wpdb->get_results( $query);


			$html = '<div class="bookmify_be_list coupon_list">';
			if(count($results) == 0){
				$html .= Helper::bookmifyBeNoItems();
			}
			foreach($results as $result){
				$stat				= $result->status;
				$couponID			= $result->id;
				$couponTitle		= $result->title;
				$couponCode			= $result->code;
				$couponDiscount		= $result->discount;
				$discountActive	 	= 'active';
				$deductionActive	= 'active';
				if($couponDiscount == 0){
					$couponDiscount = '-';
					$discountActive = 'disabled';
				}else{
					$couponDiscount .= '%';
					$deductionActive = 'disabled';
				}
				$couponDeduction	= Helper::bookmifyPriceCorrection($result->deduction,'coupons');
				$couponULimit		= $result->usage_limit;
				$couponUsed			= HelperCoupons::getUsedCouponsCount($couponID);
				$couponStartDate	= $result->date_limit_start;
				$couponEndDate		= $result->date_limit_end;
				if($couponStartDate == '' || $couponStartDate == NULL || $couponStartDate == '0000-00-00 00:00:00'){
					$couponStartDate	= esc_html__('No limit', 'bookmify');
					$couponEndDate		= esc_html__('No limit', 'bookmify');
				}else{
					$couponStartDate	= date_i18n(get_option('bookmify_be_date_format', 'd F, Y'), strtotime($couponStartDate));
					$couponEndDate		= date_i18n(get_option('bookmify_be_date_format', 'd F, Y'), strtotime($couponEndDate));
				}
				switch($stat){
					case 'active': 	$status = esc_html__('open', 'bookmify'); 	break;
					default: 		$status = esc_html__('closed', 'bookmify'); break;
				}
				$html .= '<div class="bookmify_be_list_item coupon_item bookmify_be_animated" data-coupon-id="'.$couponID.'">
							<div class="bookmify_be_list_item_in">
								<div class="bookmify_be_list_item_header">
									<div class="header_in">
										<div class="info_holder">
											<div class="info_in">
												<span class="coupon_title">'.$couponTitle.'</span>
												<span class="coupon_code">'.$couponCode.'</span>
												<span class="coupon_discount '.$discountActive.'">'.$couponDiscount.'</span>
												<span class="coupon_deduction '.$deductionActive.'">'.$couponDeduction.'</span>
												<span class="coupon_limit">'.$couponUsed.' / '.$couponULimit.'</span>
												<span class="coupon_sdate">'.$couponStartDate.'</span>
												<span class="coupon_edate">'.$couponEndDate.'</span>
												<span class="coupon_status"><span class="'.$stat.'"><span>'.$status.'</span></span></span>
											</div>
										</div>

										<div class="buttons_holder">
											<div class="btn_item btn_edit">
												<a href="#" class="bookmify_be_edit">
													<img class="bookmify_be_svg edit" src="'.BOOKMIFY_ASSETS_URL.'img/edit.svg" alt="" />
													<img class="bookmify_be_svg checked" src="'.BOOKMIFY_ASSETS_URL.'img/checked.svg" alt="" />
													<span class="bookmify_be_loader small">
														<span class="loader_process">
															<span class="ball"></span>
															<span class="ball"></span>
															<span class="ball"></span>
														</span>
													</span>
												</a>
											</div>
											<div class="btn_item">
												<a href="#" class="bookmify_be_delete" data-entity-id="'.$couponID.'">
													<img class="bookmify_be_svg" src="'.BOOKMIFY_ASSETS_URL.'img/delete.svg" alt="" />
													<span class="bookmify_be_loader small">
														<span class="loader_process">
															<span class="ball"></span>
															<span class="ball"></span>
															<span class="ball"></span>
														</span>
													</span>
												</a>
											</div>
										</div>
									</div>
								</div>

							</div>
						</div>';

			}

			$html .= '</div>';
			
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
			$html = preg_replace($search, $replace, $html);
			
			$buffyArray = array(
				'number'				=> Helper::bookmifyItemsCount('coupons'),
				'bookmify_be_data'		=> $html,
			);

			if ( true === $isAjaxCall ){die(json_encode($buffyArray));} 
			else{return json_encode($buffyArray);}
		}
	}
	
	
	
	
	
	
	
	/* since bookmify v1.3.6 */
	
	public function ajaxQueryEditCoupon(){
		global $wpdb;
		$isAjaxCall 				= false;
		$html 						= '';
		
		if (!empty($_POST['bookmify_data'])) {
			$isAjaxCall 			= true;
			$id 					= $_POST['bookmify_data'];

			// SELECT
			$id 					= esc_sql($id);
			$query 					= "SELECT * FROM {$wpdb->prefix}bmify_coupons WHERE id=".$id;
			$coupons	 			= $wpdb->get_results( $query, OBJECT  );
			
			$cuurencyIcon			= Helper::bookmifyGetIconPrice();
			
			foreach($coupons as $coupon){
				$ID					= $coupon->id;
				$couponTitle		= $coupon->title;
				$couponCode			= $coupon->code;
				$couponDiscount		= (float)$coupon->discount;
				$couponDeduction	= (float)$coupon->deduction;
				$couponULimit		= (int)$coupon->usage_limit;
				$couponInfo			= $coupon->info;
				$couponDLS			= $coupon->date_limit_start;
				$couponDLE			= $coupon->date_limit_end;
				
				
				$cuurencyIcon		= Helper::bookmifyGetIconPrice();
				if($couponDeduction > 0){
					$saleTypeChecked	= 'checked="checked"';
					$discount			= 'disabled';
					$deduction			= 'enabled';
				}else{
					$saleTypeChecked	= '';
					$discount			= 'enabled';
					$deduction			= 'disabled';
				}
				if($couponDLS == '' || $couponDLS == NULL || $couponDLS == '0000-00-00 00:00:00'){
					$dateChecked		= '';
					$daterange			= 'disabled';
				}else{
					$dateChecked		= 'checked="checked"';
					$daterange			= 'enabled';
					$couponDLS			= date('Y-m-d', strtotime($couponDLS));
					$couponDLE			= date('Y-m-d', strtotime($couponDLE));
				}
				
				$html .= '<div class="bookmify_be_popup_form_wrap" data-entity-id="'.$ID.'">
							
							<div class="bookmify_be_popup_form_position_fixer">
								<div class="bookmify_be_popup_form_bg">
									<div class="bookmify_be_popup_form">

										<div class="bookmify_be_popup_form_header">
											<h3>'.esc_html__('Edit Coupon','bookmify').'</h3>
											<span class="closer"></span>
										</div>

										<div class="bookmify_be_popup_form_content">
											<div class="bookmify_be_popup_form_content_in">

												<div class="bookmify_be_popup_form_fields">

													<form autocomplete="off">
														
														<div class="input_wrap_row">
															<div class="input_wrap">
																<label><span class="title">'.esc_html__('Title','bookmify').'<span>*</span></span></label>
																<input class="coupon_title required_field" type="text" value="'.$couponTitle.'" />
															</div>
														</div>

														<div class="input_wrap_row">
															<div class="input_wrap">
																<label><span class="title">'.esc_html__('Code','bookmify').'<span>*</span></span></label>
																<input class="coupon_code required_field" type="text" value="'.$couponCode.'" />
															</div>
														</div>

														<div class="input_wrap_row">
															<div class="input_wrap">
																<label><span class="title">'.esc_html__('Limit Usage','bookmify').'</span></label>
																<input class="coupon_limit" type="number" value="'.$couponULimit.'" min="1" />
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
																	<input class="coupon_discount" type="number" value="'.$couponDiscount.'" min="0" max="100" />
																</div>
															</div>

															<div class="input_wrap_row input_deduction '.$deduction.'">
																<div class="input_wrap">
																	<label><span class="title">'.esc_html__('Deduction','bookmify').'<span class="title_icon">('.$cuurencyIcon.')</span></span></label>
																	<input class="coupon_deduction" type="number" value="'.$couponDeduction.'" min="0" />
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
																<input type="hidden" class="date_limit_start" value="'.$couponDLS.'" />
																<input type="hidden" class="date_limit_end" value="'.$couponDLE.'" />
															</div>
														</div>


														<div class="input_wrap_row">
															<div class="input_wrap">
																<label><span class="title">'.esc_html__('Info','bookmify').'</span></label>
																<textarea class="coupon_info" placeholder="'.esc_html__('Some info for internal usage', 'bookmify').'">'.$couponInfo.'</textarea>
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
			}
		}
		
		$buffy = '';
		if ( $html != NULL ) {
			$buffy .= $html; 
		}

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
		$buffy = preg_replace($search, $replace, $buffy);

		$buffyArray = array(
			'bookmify_be_data' 		=> $buffy,
			
		);

		if ( true === $isAjaxCall ){die(json_encode($buffyArray));} 
		else{return json_encode($buffyArray);}
	}
	
	/**
	 * @since 1.3.6
	 * @access protected
	*/
	protected function get_page_title() {
		return esc_html__( 'Coupons', 'bookmify' );
	}
}
	

