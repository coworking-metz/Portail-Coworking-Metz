<?php
namespace Bookmify;

use Bookmify\Helper;
use Bookmify\HelperAdmin;
use Bookmify\HelperShortcodes;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


class BackendShortcodes{

	const PAGE_ID = 'bookmify_shortcodes';
	
	private $per_page;
	/**
	 * @since 1.0.0
	 * @access public
	*/
	public function __construct() {
		add_action( 'admin_menu', [ $this, 'registerAdminMenu' ], 20 );
		
		
		
		
		add_action( 'wp_ajax_ajaxQueryEditShortcode', [$this, 'ajaxQueryEditShortcode'] );
		add_action( 'wp_ajax_ajaxQueryDeleteShortcode', [$this, 'ajaxQueryDeleteShortcode'] );
		add_action( 'wp_ajax_ajaxQueryInsertOrUpdateShortcode', [$this, 'ajaxQueryInsertOrUpdateShortcode'] );
		
		
		add_action( 'wp_ajax_ajaxPreviewServiceList', [$this, 'ajaxPreviewServiceList'] );
		
	}

	
	/**
	 * @since 1.0.0
	 * @access public
	*/
	public function registerAdminMenu() {

		add_submenu_page(
			BOOKMIFY_MENU,
			esc_html__( 'Shortcodes', 'bookmify' ),
			esc_html__( 'Shortcodes', 'bookmify' ),
			'bookmify_be_read_shortcodes',
			self::PAGE_ID,
			[ $this, 'displayShortcodesPage' ]
		);
	}
	
	public function displayShortcodesPage() {
		global $wpdb;

		echo HelperAdmin::bookmifyAdminContentStart();
		?>
		
		<div class="bookmify_be_content_wrap bookmify_be_shortcodes_page">
			<div class="bookmify_be_page_title">
				<h3><?php echo esc_html($this->get_page_title()); ?><span class="count"><?php echo Helper::bookmifyItemsCount('shortcodes');?></span></h3>
				<div class="bookmify_be_add_new_item bookmify_be_add_new_shortcode">
					<a href="#" class="bookmify_button add_new bookmify_add_new_button">
						<span class="text"><?php esc_html_e('Add New Shortcode','bookmify');?></span>
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

				<div class="bookmify_be_shortcodes">
					
					<div class="bookmify_be_hidden_info">
						<span class="bookmify_be_loader">
							<span class="loader_process">
								<span class="ball"></span>
								<span class="ball"></span>
								<span class="ball"></span>
							</span>
						</span>
					</div>
					
					<!-- Shortcode Header -->
					<div class="bookmify_be_shortcodes_header">
						<div class="bookmify_be_shortcodes_header_in">
							<span class="list_name"><?php esc_html_e('Title', 'bookmify');?></span>
							<span class="list_rate"><?php esc_html_e('Shortcode', 'bookmify');?></span>
						</div>
					</div>
					<!-- /Shortcode Header -->
					
					<!-- Shortcode List -->
					<div class="bookmify_be_shortcodes_list">
						<?php echo $this->shortcodesList(); ?>
					</div>
					<!-- /Shortcode Header -->
					
				</div>
				
			</div>
			<?php echo HelperShortcodes::clonableFormShortcode(); ?>
		</div>
		<?php echo HelperAdmin::bookmifyAdminContentEnd();
		
	}
	

	/*
	 * @since 1.0.0
	 * @access public
	*/
	public function shortcodesList(){
		global $wpdb;
		
		$query 			= "SELECT * FROM {$wpdb->prefix}bmify_shortcodes ORDER BY title,id";
		
		$results 		= $wpdb->get_results( $query);
		

		$html = '<div class="bookmify_be_list shortcode_list">';
		
		$array = HelperShortcodes::bookmifyMainShortcodes();
		
		array_unshift($results,$array);
		
		foreach($results as $result){
			$html .= '<div class="bookmify_be_list_item shortcode_item" data-shortcode-id="'.$result->id.'">
						<div class="bookmify_be_list_item_in">
							<div class="bookmify_be_list_item_header">
								<div class="header_in">
									<div class="info_holder">
										<div class="info_in">
											<span class="shortcode_t">'.$result->title.'</span>
											<span class="shortcode_sh">'.$result->shortcode.'</span>
										</div>
									</div>';
			if($result->id != 0){
				$html				.= '
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
											<a href="#" class="bookmify_be_delete" data-entity-id="'.$result->id.'">
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
									</div>';
			}
				$html				.= '
								</div>
							</div>
								
						</div>
					</div>';
			
		}
		
		$html .= '</div>';

		return $html;
	}
	public function ajaxQueryInsertOrUpdateShortcode(){
		global $wpdb;
		$isAjaxCall 				= false;
			
		$serviceIDs					= array();
		$categoryIDs				= array();
		$employeeIDs				= array();
		$locationIDs				= array();
		$sortBy						= '';
		// filters
		if(!empty($_POST['serviceIDs'])){
			$serviceIDs 		= $_POST['serviceIDs'];
		}
		if(!empty($_POST['categoryIDs'])){
			$categoryIDs 		= $_POST['categoryIDs'];
		}
		if(!empty($_POST['employeeIDs'])){
			$employeeIDs 		= $_POST['employeeIDs'];
		}
		if(!empty($_POST['locationIDs'])){
			$locationIDs 		= $_POST['locationIDs'];
		}
		if(!empty($_POST['sortBy'])){
			$sortBy 			= $_POST['sortBy'];
		}
		$serviceIDs		 		= implode(',', $serviceIDs);
		$categoryIDs			= implode(',', $categoryIDs);
		$employeeIDs			= implode(',', $employeeIDs);
		$locationIDs			= implode(',', $locationIDs);

		$sh		= '';
		if($serviceIDs != ''){
			$sh	.= "service_id='".$serviceIDs."'";
		}
		if($categoryIDs != ''){
			if($sh != ''){
				$sh	.= ' ';
			}
			$sh	.= "category_id='".$categoryIDs."'";
		}
		if($employeeIDs != ''){
			if($sh != ''){
				$sh	.= ' ';
			}
			$sh	.= "employee_id='".$employeeIDs."'";
		}
		if($locationIDs != ''){
			if($sh != ''){
				$sh	.= ' ';
			}
			$sh	.= "location_id='".$locationIDs."'";
		}
		if($sortBy != ''){
			if($sh != ''){
				$sh	.= ' ';
			}
			$sh	.= "order='".$sortBy."'";
		}
		$shortcodeSh = '[bookmify_app_alpha]';
		if($sh != ''){
			$shortcodeSh = "[bookmify_app_alpha ".$sh."]";
		}
		
		// **************************************************************************************************************************
		// UPDATE 
		// **************************************************************************************************************************
		if ($_POST['shortcodeID'] != '') {
			$shortcodeID		= esc_sql($_POST['shortcodeID']);
			$shortcodeTitle 	= esc_sql($_POST['shortcodeTitle']);
			$wpdb->query($wpdb->prepare( "UPDATE {$wpdb->prefix}bmify_shortcodes SET title=%s, shortcode=%s WHERE id=%d", $shortcodeTitle, $shortcodeSh, $shortcodeID));
		
		}else{
			$shortcodeTitle 	= esc_sql($_POST['shortcodeTitle']);
			$wpdb->query($wpdb->prepare( "INSERT INTO {$wpdb->prefix}bmify_shortcodes (title, shortcode) VALUES (%s,%s)", $shortcodeTitle, $shortcodeSh));
		}
		$isAjaxCall 	= true;
		$query 			= "SELECT * FROM {$wpdb->prefix}bmify_shortcodes ORDER BY title,id";
		
		$results 		= $wpdb->get_results( $query);
		

		$html = '<div class="bookmify_be_list shortcode_list">';
		
		$array = HelperShortcodes::bookmifyMainShortcodes();
		
		array_unshift($results,$array);
		
		foreach($results as $result){
			$html .= '<div class="bookmify_be_list_item shortcode_item" data-shortcode-id="'.$result->id.'">
						<div class="bookmify_be_list_item_in">
							<div class="bookmify_be_list_item_header">
								<div class="header_in">
									<div class="info_holder">
										<div class="info_in">
											<span class="shortcode_t">'.$result->title.'</span>
											<span class="shortcode_sh">'.$result->shortcode.'</span>
										</div>
									</div>';
			if($result->id != 0){
				$html				.= '
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
											<a href="#" class="bookmify_be_delete" data-entity-id="'.$result->id.'">
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
									</div>';
			}
				$html				.= '
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
			'number'				=> Helper::bookmifyItemsCount('shortcodes')
		);

		if ( true === $isAjaxCall ){die(json_encode($buffyArray));} 
		else{return json_encode($buffyArray);}
	}
	
	
	

	
	
	
	/**
	 * @since 1.0.0
	 * @access public
	*/
	public function ajaxQueryDeleteShortcode(){
		global $wpdb;
		$id 			= '';
		$isAjaxCall 	= false;
		if(!empty($_POST['bookmify_shortcode_id'])){
			$isAjaxCall = true;
			$id 		= $_POST['bookmify_shortcode_id'];
			$wpdb->query($wpdb->prepare( "DELETE FROM {$wpdb->prefix}bmify_shortcodes WHERE id=%d", $id));
			
			
			$buffyArray = array(
				'number'				=> Helper::bookmifyItemsCount('shortcodes'),
			);

			if ( true === $isAjaxCall ){die(json_encode($buffyArray));} 
			else{return json_encode($buffyArray);}
		}
	}
	
	
	
	
	
	
	
	
	
	public function ajaxQueryEditShortcode(){
		global $wpdb;
		$isAjaxCall = false;
		$html 		= '';
		
		if (!empty($_POST['bookmify_data'])) {
			$isAjaxCall 	= true;
			$id 			= $_POST['bookmify_data'];

			// SELECT
			$id 			= esc_sql($id);
			$query 			= "SELECT * FROM {$wpdb->prefix}bmify_shortcodes WHERE id=".$id;
			$shortcodes	 	= $wpdb->get_results( $query, OBJECT  );
			
			
			foreach($shortcodes as $shortcode){
				$ID				= $shortcode->id;
				$title			= $shortcode->title;
				$shortcodee		= $shortcode->shortcode;
				$array			= explode(' ', $shortcodee);
				$sIDs			= '';
				$eIDs			= '';
				$lIDs			= '';
				$cIDs			= '';
				$order			= '';
				$sortReady		= '';
				$orderNewValue	= '';
				if(!empty($array)){
					array_shift($array); // remove first element from array: [0] -> [bookmify_app_alpha
					for($i=0; $i<count($array); $i++){
						$el 		= $array[$i];
						$el2		= explode('=', $el);
						if(strstr($el,"service_id='")){
							$sIDs 	= $el2[1];
							$sIDs 	= ltrim($sIDs, "'");
							$sIDs 	= rtrim($sIDs, ']');
							$sIDs 	= rtrim($sIDs, "'");
						}else if(strstr($el,"employee_id='")){
							$eIDs 	= $el2[1];
							$eIDs 	= ltrim($eIDs, "'");
							$eIDs 	= rtrim($eIDs, ']');
							$eIDs 	= rtrim($eIDs, "'");
						}else if(strstr($el,"location_id='")){
							$lIDs 	= $el2[1];
							$lIDs 	= ltrim($lIDs, "'");
							$lIDs 	= rtrim($lIDs, ']');
							$lIDs 	= rtrim($lIDs, "'");
						}else if(strstr($el,"category_id='")){
							$cIDs 	= $el2[1];
							$cIDs 	= ltrim($cIDs, "'");
							$cIDs 	= rtrim($cIDs, ']');
							$cIDs 	= rtrim($cIDs, "'");
						}else if(strstr($el,"order='")){
							$order 	= $el2[1];
							$order 	= ltrim($order, "'");
							$order 	= rtrim($order, ']');
							$order 	= rtrim($order, "'");
						}
					}
				}
				$sPlaceholder		= esc_attr__('Select from Services','bookmify');
				$ePlaceholder		= esc_attr__('Select from Employees','bookmify');
				$cPlaceholder		= esc_attr__('Select from Categories','bookmify');
				$oPlaceholder		= esc_attr__('Select from Options','bookmify');
				$lPlaceholder		= esc_attr__('Select from Locations','bookmify');
				if($sIDs != ''){$sPlaceholder = '';}
				if($eIDs != ''){$ePlaceholder = '';}
				if($cIDs != ''){$cPlaceholder = '';}
				if($lIDs != ''){$lPlaceholder = '';}
				if($order != ''){$oPlaceholder = '';$sortReady = 'ready';}
				switch($order){
					case 'title_asc': 	$orderNewValue = esc_html__( 'Title Ascending', 'bookmify' ); 	break;
					case 'title_desc': 	$orderNewValue = esc_html__( 'Title Descending', 'bookmify' ); 	break;
					case 'price_asc': 	$orderNewValue = esc_html__( 'Price Ascending', 'bookmify' ); 	break;
					case 'price_desc': 	$orderNewValue = esc_html__( 'Price Descending', 'bookmify' ); 	break;
				}
				$html .= '<div class="bookmify_be_popup_form_wrap" data-entity-id="'.$ID.'" data-asd="'.$order.'">
							'.HelperShortcodes::frontendPreview().'
							<div class="bookmify_be_popup_form_position_fixer">
								<div class="bookmify_be_popup_form_bg">
									<div class="bookmify_be_popup_form">

										<div class="bookmify_be_popup_form_header">
											<h3>'.esc_html__('Edit Shortcode','bookmify').'</h3>
											<span class="closer"></span>
										</div>

										<div class="bookmify_be_popup_form_content">
											<div class="bookmify_be_popup_form_content_in">

												<div class="bookmify_be_popup_form_fields">

													<form autocomplete="off">
														<div class="input_wrap_row">
															<div class="input_wrap">
																<label><span class="title">'.esc_html__('Title','bookmify').'<span>*</span></span></label>
																<input class="sh_title required_field" type="text" value="'.$title.'" />
															</div>
														</div>

														<div class="input_wrap_row">
															<div class="input_wrap category">
																<label>
																	<span class="title">'.esc_html__('Category','bookmify').'</span>
																</label>
																<input type="text" name="category" placeholder="'.$cPlaceholder.'" readonly value="" data-placeholder="'.esc_attr__('Select from Categories','bookmify').'" />
																<input type="hidden" name="category_ids" value="'.$cIDs.'" />
																<span class="bot_btn"><span></span><img class="bookmify_be_svg" src="'.BOOKMIFY_ASSETS_URL.'img/down.svg" alt="" /></span>
																<div class="bookmify_be_new_value"></div>
																'.HelperShortcodes::categoryList($cIDs).'
															</div>
														</div>

														<div class="input_wrap_row">
															<div class="input_wrap service">
																<label>
																	<span class="title">'.esc_html__('Service','bookmify').'</span>
																</label>
																<input type="text" name="service" placeholder="'.$sPlaceholder.'" readonly value=""  data-placeholder="'.esc_attr__('Select from Services','bookmify').'" />
																<input type="hidden" name="service_ids" value="'.$sIDs.'">
																<span class="bot_btn"><span></span><img class="bookmify_be_svg" src="'.BOOKMIFY_ASSETS_URL.'img/down.svg" alt="" /></span>
																<div class="bookmify_be_new_value"></div>
																'.HelperShortcodes::serviceList($sIDs).'
															</div>
														</div>

														<div class="input_wrap_row">
															<div class="input_wrap employee">
																<label>
																	<span class="title">'.esc_html__('Employee','bookmify').'</span>
																</label>
																<input type="text" name="employee" placeholder="'.$ePlaceholder.'" readonly value=""  data-placeholder="'.esc_attr__('Select from Employees','bookmify').'" />
																<input type="hidden" name="employee_ids" value="'.$eIDs.'">
																<span class="bot_btn"><span></span><img class="bookmify_be_svg" src="'.BOOKMIFY_ASSETS_URL.'img/down.svg" alt="" /></span>
																<div class="bookmify_be_new_value"></div>
																'.HelperShortcodes::employeeList($eIDs).'
															</div>
														</div>

														<div class="input_wrap_row">
															<div class="input_wrap location">
																<label>
																	<span class="title">'.esc_html__('Location','bookmify').'</span>
																</label>
																<input type="text" name="location" placeholder="'.$lPlaceholder.'" readonly value=""  data-placeholder="'.esc_attr__('Select from Locations','bookmify').'" />
																<input type="hidden" name="location_ids" value="'.$lIDs.'">
																<span class="bot_btn"><span></span><img class="bookmify_be_svg" src="'.BOOKMIFY_ASSETS_URL.'img/down.svg" alt="" /></span>
																<div class="bookmify_be_new_value"></div>
																'.HelperShortcodes::locationList($lIDs).'
															</div>
														</div>

														<div class="input_wrap_row">
															<div class="input_wrap sort '.$sortReady.'">
																<label>
																	<span class="title">'.esc_html__('Sort By','bookmify').'</span>
																</label>
																<input type="text" name="sorting" placeholder="'.$oPlaceholder.'" readonly value=""  data-placeholder="'.esc_attr__('Select from Options','bookmify').'" />
																<input type="hidden" name="sorting_by" value="'.$order.'">
																<span class="bot_btn"><span></span><img class="bookmify_be_svg" src="'.BOOKMIFY_ASSETS_URL.'img/down.svg" alt="" /></span>
																<div class="bookmify_be_new_value">'.$orderNewValue.'</div>
																'.HelperShortcodes::sorting($order).'
															</div>
														</div>



													</form>

												</div>

											</div>
										</div>

										'.Helper::bookmifyPopupSaveSection('generate').'

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
	
	public static function ajaxPreviewServiceList(){
		global $wpdb;
		$isAjaxCall 				= false;
		$html 						= '';
		
		$serviceIDs					= array();
		$categoryIDs				= array();
		$employeeIDs				= array();
		$locationIDs				= array();
		$sortBy						= '';
		
		if (!empty($_POST['do'])) {
			$isAjaxCall 			= true;
			
			// filters
			if(!empty($_POST['serviceIDs'])){
				$serviceIDs 	= $_POST['serviceIDs'];
			}
			if(!empty($_POST['categoryIDs'])){
				$categoryIDs 		= $_POST['categoryIDs'];
			}
			if(!empty($_POST['employeeIDs'])){
				$employeeIDs 		= $_POST['employeeIDs'];
			}
			if(!empty($_POST['locationIDs'])){
				$locationIDs 		= $_POST['locationIDs'];
			}
			if(!empty($_POST['sortBy'])){
				$sortBy 			= $_POST['sortBy'];
			}
			
			$query 		 = "SELECT
							s.id id,
							s.attachment_id attachment_id,
							s.visibility visibility,
							s.price servicePrice,
							s.duration serviceDuration,
							s.title serviceTitle

						FROM 	   	   {$wpdb->prefix}bmify_services s 
							INNER 	JOIN {$wpdb->prefix}bmify_employee_services es 				ON es.service_id = s.id
							INNER 	JOIN {$wpdb->prefix}bmify_employees e 						ON es.employee_id = e.id
							LEFT 	JOIN {$wpdb->prefix}bmify_employee_locations el				ON el.employee_id = e.id
							WHERE s.visibility='public' AND e.visibility='public' AND";
			


			
			if(!empty($serviceIDs)){
				$serviceIDs = esc_sql($serviceIDs);
				$query .= " s.id IN (" . implode(",", array_map("intval", $serviceIDs)) . ") AND";
			}
			if(!empty($categoryIDs)){
				$categoryIDs = esc_sql($categoryIDs);
				$query .= " s.category_id IN (" . implode(",", array_map("intval", $categoryIDs)) . ") AND";
			}
			if(!empty($employeeIDs)){
				$employeeIDs = esc_sql($employeeIDs);
				$query .= " e.id IN (" . implode(",", array_map("intval", $employeeIDs)) . ") AND";
			}
			if(!empty($locationIDs)){
				$locationIDs = esc_sql($locationIDs);
				$query .= " el.location_id IN (" . implode(",", array_map("intval", $locationIDs)) . ") AND";
			}
			
			$query = rtrim($query, 'AND');
			
			$query .= " GROUP BY es.service_id";
			
			$sortBy = esc_sql($sortBy);
			switch($sortBy){
				default:
				case 'title_asc': 	$query .= " ORDER BY s.title ASC"; break;
				case 'title_desc': 	$query .= " ORDER BY s.title DESC"; break;
				case 'price_asc': 	$query .= " ORDER BY s.price ASC"; break;
				case 'price_desc':	$query .= " ORDER BY s.price DESC"; break;
			}
			
			$results	= $wpdb->get_results( $query, OBJECT  );
			
			
			
			$html = '<ul class="bookmify_fe_list service_list">';
		
			foreach( $results as $result ){
				$ID						= $result->id;
				$attachmentID			= $result->attachment_id;
				$price					= $result->servicePrice;
				$duration				= $result->serviceDuration;
				$title					= Helper::titleDecryption($result->serviceTitle);
				$attachmentURL	 		= Helper::bookmifyGetImageByID($attachmentID);
				if($attachmentURL != ''){$opened = 'has_image';}else{$opened = '';}
				$price 					= Helper::bookmifyPriceCorrection($price, 'frontend');
				$duration2 				= Helper::bookmifyNumberToDuration($duration);
				$html .=   '<li data-service-id="'.$ID.'" class="bookmify_fe_service_item bookmify_fe_list_item '.$opened.'">
								<div class="bookmify_fe_list_item_in">
									<div class="bookmify_service_heading bookmify_fe_list_item_header">
										<div class="heading_in header_in">
											<div class="img_and_color_holder">
												<div class="img_holder" style="background-image:url('.$attachmentURL.')"></div>
											</div>
											<div class="service_info">
												<div class="left_part">
													<span class="service_title">'.$title.'</span>
													<span class="service_duration">'.$duration2.'</span>
												</div>
												<div class="right_part">
													<span class="service_price"><span>'.$price.'</span></span>
													<span class="service_hover"><span>'.esc_html__('Book Now', 'bookmify').'</span></span>
												</div>
											</div>
										</div>
									</div>
								</div>
							</li>';

			}

			$html .= '</ul>';
			
			
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
			'query' 				=> $query,
		);

		if ( true === $isAjaxCall ){die(json_encode($buffyArray));}
		else{return json_encode($buffyArray);}
	}
	
	/**
	 * @since 1.0.0
	 * @access protected
	*/
	protected function get_page_title() {
		return esc_html__( 'Shortcodes', 'bookmify' );
	}
}
	

