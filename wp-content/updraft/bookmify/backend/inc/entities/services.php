<?php
namespace Bookmify;

use Bookmify\Helper;
use Bookmify\HelperTime;
use Bookmify\HelperAdmin;
use Bookmify\HelperServices;
use Bookmify\Categories;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


class Services{

	const PAGE_ID = 'bookmify_services';
	
	private $per_page;
	
	public function __construct() {
		$this->assignValToVar();
		
		add_action( 'admin_menu', [ $this, 'register_admin_menu' ], 20 );
		add_action( 'wp_ajax_ajaxQueryInsertOrUpdateService', [$this, 'ajaxQueryInsertOrUpdateService'] );
		add_action( 'wp_ajax_ajaxQueryDeleteService', [$this, 'ajaxQueryDeleteService'] );
		add_action( 'wp_ajax_ajaxQueryDeleteExtraService', [$this, 'ajaxQueryDeleteExtraService'] );
		add_action( 'wp_ajax_ajaxQueryEditService', [$this, 'ajaxQueryEditService'] );
		add_action( 'wp_ajax_ajaxFilterServiceList', [$this, 'ajaxFilterServiceList'] );
		
		
//		add_action( 'wp_ajax_getCustomersList', [$this, 'getCustomersList'] );
	}

	public function assignValToVar(){
		$this->per_page = get_option('bookmify_be_services_pp', 10);
	}
	
	
	/**
	 * @since 1.0.0
	 * @access public
	*/
	public function register_admin_menu() {

		add_submenu_page(
			BOOKMIFY_MENU,
			esc_html__( 'Services', 'bookmify' ),
			esc_html__( 'Services', 'bookmify' ),
			'bookmify_be_read_services',
			self::PAGE_ID,
			[ $this, 'display_services_page' ]
		);
	}
	
	public function display_services_page() {
		global $wpdb;
		$categoryList = Categories::category_list();
		
		echo HelperAdmin::bookmifyAdminContentStart();
		?>
		
		<div class="bookmify_be_content_wrap bookmify_be_service_page">
			<div class="bookmify_be_page_title">
				<h3><?php echo esc_html($this->get_page_title()); ?><span class="count"><?php echo Helper::bookmifyItemsCount('services');?></span></h3>
				<div class="bookmify_be_add_new_item bookmify_be_add_new_service">
					<a href="#" class="bookmify_button add_new bookmify_add_new_button">
						<span class="text"><?php esc_html_e('Add New Service','bookmify');?></span>
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
				<div class="bookmify_be_service_content">
				
					<!-- CATEGORY LIST -->
					<div class="cat_list_wrap">
						<div class="title"><a class="active" href="#"><?php esc_html_e('All Services','bookmify'); ?></a></div>
						<div class="cat_list">
							<?php echo wp_kses_post($categoryList); ?>
						</div>

						<div class="add_button">
							<a href="#" class="bookmify_button add_new bookmify_add_new_button">
								<span class="text"><?php esc_html_e('Add New Category','bookmify');?></span>
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
							<form id="add_new_category" action="/">
								<input type="hidden" name="cat_name" value="<?php esc_attr_e('New Category', 'bookmify');?>" />
							</form>
						</div>
					</div>
					<!-- CATEGORY LIST -->

					<!-- SERVICE LIST -->
					<div class="bookmify_be_services">
						<?php echo HelperServices::allNanoInOne(); ?>
						<div class="bookmify_be_service_header">
							<div class="bookmify_be_service_header_in">
								<span class="list_title"><?php esc_html_e('Services','bookmify');?></span>
								<span class="list_price"><?php esc_html_e('Price','bookmify');?></span>
								<span class="list_duration"><?php esc_html_e('Duration','bookmify');?></span>
							</div>
						</div>
						<div class="bookmify_be_service_list">
							<?php echo $this->serviceList(); ?>
						</div>
					</div>
					<!-- SERVICE LIST -->
				</div>
			</div>
			
			<?php echo HelperServices::clonableForm(); ?>
			<?php echo HelperServices::clonableFormToAddExtra(); ?>
		</div>
		<?php echo HelperAdmin::bookmifyAdminContentEnd();
		
	}
	
	/**
	 * @since 1.0.0
	 * @access public
	*/
	public function serviceList(){
		global $wpdb;
		
		// SELECT
		$query 			= "SELECT * FROM {$wpdb->prefix}bmify_services";
		$Querify  		= new Querify( $query, 'service' );
		$services		= $Querify->getData( $this->per_page);

		$html = '<ul class="bookmify_be_list service_list">';
		if(count($services->data) == 0){
			$html .= Helper::bookmifyBeNoItems();
		}
		for( $i = 0; $i < count( $services->data ); $i++ ){
			$ID						= $services->data[$i]->id;
			$attachmentID			= $services->data[$i]->attachment_id;
			$visibility				= $services->data[$i]->visibility;
			$price					= $services->data[$i]->price;
			$duration				= $services->data[$i]->duration;
			$color					= $services->data[$i]->color;
			$title					= $services->data[$i]->title;
			$title 					= Helper::titleDecryption($title);
			$attachmentURL	 		= Helper::bookmifyGetImageByID($attachmentID);
			$selected	 			= bookmify_be_checked($visibility, "public");
			if($attachmentURL != ''){$opened = 'has_image';}else{$opened = '';}
			$price 					= Helper::bookmifyPriceCorrection($price);
			$duration 				= Helper::bookmifyNumberToDuration($duration);
			
			$html .=   '<li data-service-id="'.$ID.'" class="bookmify_be_service_item bookmify_be_list_item">
						<div class="bookmify_be_list_item_in">
							<div class="bookmify_service_heading bookmify_be_list_item_header">
								<div class="heading_in header_in">
									<div class="img_and_color_holder">
										<div class="img_holder" style="background-color:'.$color.'; background-image:url('.$attachmentURL.')"></div>
										<div class="color_info '.$opened.'" style="background-color:'.$color.'"></div>
									</div>
									<div class="service_info">
										<span class="service_title">
											<span class="s_top">'.$title.'</span>
											<span class="s_bottom">'.$price.' / '.$duration.'</span>
										</span>
										<span class="service_price">'.$price.'</span>
										<span class="service_duration">'.$duration.'</span>
									</div>
									<div class="buttons_holder">
										<div class="btn_item btn_duplicate">
											<a href="#" class="bookmify_be_duplicate">
												<img class="bookmify_be_svg duplicate" src="'.BOOKMIFY_ASSETS_URL.'img/copy-content.svg" alt="" />
												<span class="bookmify_be_loader small">
													<span class="loader_process">
														<span class="ball"></span>
														<span class="ball"></span>
														<span class="ball"></span>
													</span>
												</span>
											</a>
										</div>
										<div class="btn_item btn_edit">
											<a href="#" class="bookmify_be_edit">
												<img class="bookmify_be_svg edit" src="'.BOOKMIFY_ASSETS_URL.'img/edit.svg" alt="" />
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
											<a href="#" class="bookmify_be_delete" data-entity-id="'.$ID.'">
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
						</li>';
		}
			
		$html .= '</ul>';
		
		$html .= $Querify->getPagination( 1, 'bookmify_be_pagination service');
		
		return $html;
	}
	
	public function ajaxFilterServiceList(){
		global $wpdb;
		$isAjaxCall 	= false;
		$html 			= '';
		$page 			= 1;
		$filter 		= array();
		$id				= 'all';
		
		if (!empty($_POST['bookmify_page'])) {
			$isAjaxCall 	= true;
			$page 			= $_POST['bookmify_page'];
			$id			 	= $_POST['bookmify_id'];

			if($id != ''){ 
				$filter['id'] = $id;
			}
			// SELECT
			$query 			= "SELECT * FROM {$wpdb->prefix}bmify_services";
			$Querify  		= new Querify( $query, 'service' );
			$services		= $Querify->getData( $this->per_page, $page, $filter );


			$html = '<ul class="bookmify_be_list service_list">';
			if(count($services->data) == 0){
				$html .= Helper::bookmifyBeNoItems();
			}
			for( $i = 0; $i < count( $services->data ); $i++ ){
				$ID						= $services->data[$i]->id;
				$attachmentID			= $services->data[$i]->attachment_id;
				$visibility				= $services->data[$i]->visibility;
				$price					= $services->data[$i]->price;
				$duration				= $services->data[$i]->duration;
				$color					= $services->data[$i]->color;
				$title					= $services->data[$i]->title;
				$title 					= Helper::titleDecryption($title);
				$attachmentURL	 		= Helper::bookmifyGetImageByID($attachmentID);
				$selected	 			= bookmify_be_checked($visibility, "public");
				if($attachmentURL != ''){$opened = 'has_image';}else{$opened = '';}
				$price 					= Helper::bookmifyPriceCorrection($price);
				$duration 				= Helper::bookmifyNumberToDuration($duration);

				$html .=   '<li data-service-id="'.$ID.'" class="bookmify_be_service_item bookmify_be_list_item bookmify_be_animated">
							<div class="bookmify_be_list_item_in">
								<div class="bookmify_service_heading bookmify_be_list_item_header">
									<div class="heading_in header_in">
										<div class="img_and_color_holder">
											<div class="img_holder" style="background-color:'.$color.'; background-image:url('.$attachmentURL.')"></div>
											<div class="color_info '.$opened.'" style="background-color:'.$color.'"></div>
										</div>
										<div class="service_info">
											<span class="service_title">
												<span class="s_top">'.$title.'</span>
												<span class="s_bottom">'.$price.' / '.$duration.'</span>
											</span>
											<span class="service_price">'.$price.'</span>
											<span class="service_duration">'.$duration.'</span>
										</div>
										<div class="buttons_holder">
											<div class="btn_item btn_duplicate">
												<a href="#" class="bookmify_be_duplicate">
													<img class="bookmify_be_svg duplicate" src="'.BOOKMIFY_ASSETS_URL.'img/copy-content.svg" alt="" />
													<span class="bookmify_be_loader small">
														<span class="loader_process">
															<span class="ball"></span>
															<span class="ball"></span>
															<span class="ball"></span>
														</span>
													</span>
												</a>
											</div>
											<div class="btn_item btn_edit">
												<a href="#" class="bookmify_be_edit">
													<img class="bookmify_be_svg edit" src="'.BOOKMIFY_ASSETS_URL.'img/edit.svg" alt="" />
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
												<a href="#" class="bookmify_be_delete" data-entity-id="'.$ID.'">
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
							</li>';
			}
			$html .= '</ul>';
			$html .= $Querify->getPagination( 1, 'bookmify_be_pagination service');
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
			'bookmify_be_data' 		=> $buffy
		);

		if ( true === $isAjaxCall ){die(json_encode($buffyArray));} 
		else{return json_encode($buffyArray);}

	}
	
	
	
	/**
	 * @since 1.0.0
	 * @access public
	*/
	public function ajaxQueryInsertOrUpdateService(){
		global $wpdb;
		$params 			= array();
		$isAjaxCall 		= false;
		$oldFolk 			= array();
		$chosenFolk 		= array();
		
		if (!empty($_POST['bookmify_data'])) {
			$isAjaxCall = true;
			parse_str($_POST['bookmify_data'], $params);
			
			$service_title 					= $params['service_title'];
			$service_title					= Helper::titleEncryption($service_title);
			$service_price 					= $params['service_price'];
			$service_duration 				= $params['service_duration_sec'];
			$service_color 					= $params['service_color'];
			$service_pad_before 			= $params['service_bb_sec'];
			$service_pad_after 				= $params['service_ba_sec'];
			$service_info 					= $params['service_info'];
			$service_img_id 				= $params['service_img_id'];
			$service_category 				= $params['service_category_id'];
			$min_capacity	 				= $params['service_min_cap'];
			$max_capacity	 				= $params['service_max_cap'];
			$employees_ids					= '';
			if(isset($params['service_provider_ids'])){
				$employees_ids				= $params['service_provider_ids'];
			}
			$tax_ids						= '';
			if(isset($params['service_tax_ids'])){
				$tax_ids					= $params['service_tax_ids'];
			}
			$gallery_ids					= $params['gallery_ids'];
			$serviceID						= $_POST['serviceID'];
			
			
			if($max_capacity < $min_capacity){
				$max_capacity = $min_capacity;
			}
			
			$visibility 	= 'private';
			if(isset($params['service_visibility'])){
				$visibility = 'public';
			}
			if($serviceID == ''){
				// Проверка, если данная функция выполняет добавление нового сервиса!

				
				$wpdb->query($wpdb->prepare( "INSERT INTO {$wpdb->prefix}bmify_services (title,category_id,price,capacity_min,capacity_max,duration,color,attachment_id,buffer_before,buffer_after,info,gallery_ids,visibility) VALUES (%s,%d,%f,%d,%d,%d,%s,%d,%d,%d,%s,%s,%s)", $service_title,$service_category,$service_price,$min_capacity,$max_capacity,$service_duration,$service_color,$service_img_id,$service_pad_before,$service_pad_after,$service_info,$gallery_ids,$visibility));
				
				$query 		= "SELECT id FROM {$wpdb->prefix}bmify_services ORDER BY id DESC LIMIT 1;";
				$results 	= $wpdb->get_results( $query, OBJECT  );
				foreach($results as $result){
					$newServiceID		= $result->id;
				}
				$extraSer = json_decode(stripslashes($_POST['bookmify_data2']));
				foreach($extraSer as $extras){
					foreach($extras->allextra as $key => $extra){
						$extraAttID 	= $extra->att_id;
						$extraName 		= $extra->name;
						$extraPrice 	= $extra->price;
						$extraDuration 	= $extra->duration;
						$extraMaxCap 	= $extra->max_cap;
						$extraDesc 		= $extra->desc;

						$wpdb->query($wpdb->prepare( "INSERT INTO {$wpdb->prefix}bmify_extra_services (title, service_id, price, duration, attachment_id, capacity_max, info, position) VALUES (%s, %s, %f, %d, %s, %s, %s, %d)", $extraName, $newServiceID, $extraPrice, $extraDuration, $extraAttID, $extraMaxCap, $extraDesc, $key));
					}
				}
				if($employees_ids != ""){
					$employees_ids		= explode(",", $employees_ids);
					if(!empty($employees_ids)){
						foreach($employees_ids as $new_employee){
							$wpdb->query($wpdb->prepare( "INSERT INTO {$wpdb->prefix}bmify_employee_services ( service_id, employee_id, price, capacity_min, capacity_max ) VALUES ( %d, %d, %f, %d, %d )", $newServiceID, $new_employee, $service_price, $min_capacity, $max_capacity));
						}
					}
				}
				if($tax_ids != ""){
					$tax_ids			= explode(",", $tax_ids);
					if(!empty($tax_ids)){
						foreach($tax_ids as $tax_id){
							$wpdb->query($wpdb->prepare( "INSERT INTO {$wpdb->prefix}bmify_services_taxes ( tax_id, service_id ) VALUES ( %d, %d )", $tax_id, $newServiceID));
						}
					}
				}
			}else{
				$extraSer 	= json_decode(stripslashes($_POST['bookmify_data2']));
				$serviceID 	= esc_sql($serviceID);
				$query 		= "SELECT id FROM {$wpdb->prefix}bmify_extra_services WHERE service_id=".$serviceID;
				$group 		= $wpdb->get_results( $query, OBJECT  );

				foreach($group as $provider){
					$assignedExtraIDs[] = $provider->id;
				}
				foreach($extraSer as $extras){
					foreach($extras->allextra as $key => $extra){
						if(isset($extra->id)){$extraID = $extra->id;}else{$extraID = '';}
						$extraAttID 	= $extra->att_id;
						$extraName 		= $extra->name;
						$extraPrice 	= $extra->price;
						$extraDuration 	= $extra->duration;
						$extraMaxCap 	= $extra->max_cap;
						$extraDesc 		= $extra->desc;
						if($extraID != ''){
							if(in_array( $extraID, $assignedExtraIDs )){
								$wpdb->query($wpdb->prepare( "UPDATE {$wpdb->prefix}bmify_extra_services SET title=%s, service_id=%s, price=%f, duration=%d, attachment_id=%s, capacity_max=%s, info=%s, position=%d WHERE id=%d", $extraName, $serviceID, $extraPrice, $extraDuration, $extraAttID, $extraMaxCap, $extraDesc, $key, $extraID));
							}else{
								$wpdb->query($wpdb->prepare( "DELETE FROM {$wpdb->prefix}bmify_extra_services WHERE id=%d", $extraID));
							}
							
						}else{
							$wpdb->query($wpdb->prepare( "INSERT INTO {$wpdb->prefix}bmify_extra_services (title, service_id, price, duration, attachment_id, capacity_max, info, position) VALUES(%s, %s, %f, %d, %s, %s, %s, %d)", $extraName, $serviceID, $extraPrice, $extraDuration, $extraAttID, $extraMaxCap, $extraDesc, $key));
						}
						
					}
				}

				$employees_settings = $_POST['bookmify_employees_settings'];


				// before update, we have to set var to NULL if it equals zero
				if($service_category == 0){$service_category = 'NULL';}

				add_filter( 'query', [$this,'bookmify_let_insert_null'] );

				$wpdb->query($wpdb->prepare( "UPDATE {$wpdb->prefix}bmify_services SET title=%s, category_id=%s, price=%f, capacity_min=%d, capacity_max=%d, duration=%d, color=%s, attachment_id=%d, buffer_before=%d, buffer_after=%d, info=%s, visibility=%s, gallery_ids=%s WHERE id=%d", $service_title, $service_category, $service_price, $min_capacity, $max_capacity, $service_duration, $service_color, $service_img_id, $service_pad_before, $service_pad_after, $service_info, $visibility, $gallery_ids, $serviceID));

				remove_filter( 'query', [$this,'bookmify_let_insert_null'] );

				if($tax_ids != ''){
					$tax_ids			= explode(',', $tax_ids);
					// Get all existing service employees
					$serviceID 			= esc_sql($serviceID);
					$query 				= "SELECT tax_id FROM {$wpdb->prefix}bmify_services_taxes WHERE service_id=".$serviceID;
					$results 			= $wpdb->get_results( $query, OBJECT  );
					
					
					$oldFolk 			= array();
					$chosenFolk 		= array();
					foreach($results as $result){
						$oldFolk[] 		= $result->tax_id;
					}
					foreach($tax_ids as $tax_id){
						$chosenFolk[] 	= $tax_id;
					}

					$existing_taxes 	= array_values(array_intersect($oldFolk, $chosenFolk)); // existing tax
					$new_taxes 			= array_values(array_diff($chosenFolk,$oldFolk)); 		// new tax
					$released_taxes 	= array_values(array_diff($oldFolk,$chosenFolk)); 		// released tax



					// Insert new taxes
					if(!empty($new_taxes)){
						foreach($new_taxes as $new_tax){
							$wpdb->query($wpdb->prepare( "INSERT INTO {$wpdb->prefix}bmify_services_taxes ( service_id, tax_id ) VALUES ( %d, %d )", $serviceID, $new_tax));
						}
					}

					// Delete released taxes
					if(!empty($released_taxes)){
						foreach($released_taxes as $released_tax){
							$wpdb->query($wpdb->prepare( "DELETE FROM {$wpdb->prefix}bmify_services_taxes WHERE tax_id=%d AND service_id=%d", $released_tax, $serviceID));
						}
					}
				}else{
					$wpdb->query($wpdb->prepare( "DELETE FROM {$wpdb->prefix}bmify_services_taxes WHERE service_id=%d", $serviceID));
				}
				
				$oldFolk 				= array();
				$chosenFolk 			= array();
				
				
				if($employees_ids !== ''){
					$employees_ids		= explode(",", $employees_ids);
					// Get all existing service employees
					$serviceID 			= esc_sql($serviceID);
					$query 				= "SELECT employee_id FROM {$wpdb->prefix}bmify_employee_services WHERE service_id=".$serviceID;
					$results 			= $wpdb->get_results( $query, OBJECT  );


					foreach($results as $result){
						$oldFolk[] 		= $result->employee_id;
					}
					foreach($employees_ids as $employees_id){
						$chosenFolk[] 	= $employees_id;
					}

					$existing_employees = array_values(array_intersect($oldFolk, $chosenFolk)); // existing employees
					$new_employees 		= array_values(array_diff($chosenFolk,$oldFolk)); 		// new employees
					$released_employees = array_values(array_diff($oldFolk,$chosenFolk)); 		// released employees


					// Update existing employees
					if(!empty($existing_employees)){
						if($employees_settings == 'yes'){
							foreach($existing_employees as $existing_employee){
								$wpdb->query($wpdb->prepare( "UPDATE {$wpdb->prefix}bmify_employee_services SET price=%f WHERE employee_id=%d AND service_id=%d", $service_price, $existing_employee, $serviceID));
							}
						}
					}

					// Insert new employees
					if(!empty($new_employees)){
						foreach($new_employees as $new_employee){
							$wpdb->query($wpdb->prepare( "INSERT INTO {$wpdb->prefix}bmify_employee_services ( service_id, employee_id, price ) VALUES ( %d, %d, %f)", $serviceID, $new_employee, $service_price));
						}
					}

					// Delete released employees
					if(!empty($released_employees)){
						foreach($released_employees as $released_employee){
							$wpdb->query($wpdb->prepare( "DELETE FROM {$wpdb->prefix}bmify_employee_services WHERE employee_id=%d AND service_id=%d", $released_employee, $serviceID));
						}
					}
				}else{
					$wpdb->query($wpdb->prepare( "DELETE FROM {$wpdb->prefix}bmify_employee_services WHERE service_id=%d", $serviceID));
				}
			}
			
		
			$page 			= 1;
			$filter 		= array();
			$id				= 'all';
			
			if(isset($_POST['bookmify_page'])){
				$page 			= $_POST['bookmify_page'];
			}
			if(isset($_POST['bookmify_id'])){
				$id 			= $_POST['bookmify_id'];
			}

			if($id != ''){ 
				$filter['id'] = $id;
			}
			// SELECT
			$query 			= "SELECT * FROM {$wpdb->prefix}bmify_services";
			$Querify  		= new Querify( $query, 'service' );
			$services		= $Querify->getData( $this->per_page, $page, $filter );

			$html = '<ul class="bookmify_be_list service_list">';
			if(count($services->data) == 0){
				$html .= Helper::bookmifyBeNoItems();
			}
			for( $i = 0; $i < count( $services->data ); $i++ ){
				$ID						= $services->data[$i]->id;
				$attachmentID			= $services->data[$i]->attachment_id;
				$visibility				= $services->data[$i]->visibility;
				$price					= $services->data[$i]->price;
				$duration				= $services->data[$i]->duration;
				$color					= $services->data[$i]->color;
				$title					= $services->data[$i]->title;
				$title 					= Helper::titleDecryption($title);
				$attachmentURL	 		= Helper::bookmifyGetImageByID($attachmentID);
				$selected	 			= bookmify_be_checked($visibility, "public");
				if($attachmentURL != ''){$opened = 'has_image';}else{$opened = '';}
				$price 					= Helper::bookmifyPriceCorrection($price);
				$duration 				= Helper::bookmifyNumberToDuration($duration);

				$html .=   '<li data-service-id="'.$ID.'" class="bookmify_be_service_item bookmify_be_list_item  bookmify_be_animated">
							<div class="bookmify_be_list_item_in">
								<div class="bookmify_service_heading bookmify_be_list_item_header">
									<div class="heading_in header_in">
										<div class="img_and_color_holder">
											<div class="img_holder" style="background-color:'.$color.'; background-image:url('.$attachmentURL.')"></div>
											<div class="color_info '.$opened.'" style="background-color:'.$color.'"></div>
										</div>
										<div class="service_info">
											<span class="service_title">
												<span class="s_top">'.$title.'</span>
												<span class="s_bottom">'.$price.' / '.$duration.'</span>
											</span>
											<span class="service_price">'.$price.'</span>
											<span class="service_duration">'.$duration.'</span>
										</div>
										<div class="buttons_holder">
											<div class="btn_item btn_duplicate">
												<a href="#" class="bookmify_be_duplicate">
													<img class="bookmify_be_svg duplicate" src="'.BOOKMIFY_ASSETS_URL.'img/copy-content.svg" alt="" />
													<span class="bookmify_be_loader small">
														<span class="loader_process">
															<span class="ball"></span>
															<span class="ball"></span>
															<span class="ball"></span>
														</span>
													</span>
												</a>
											</div>
											<div class="btn_item btn_edit">
												<a href="#" class="bookmify_be_edit">
													<img class="bookmify_be_svg edit" src="'.BOOKMIFY_ASSETS_URL.'img/edit.svg" alt="" />
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
												<a href="#" class="bookmify_be_delete" data-entity-id="'.$ID.'">
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
							</li>';
			}

			$html .= '</ul>';

			$html .= $Querify->getPagination( 1, 'bookmify_be_pagination service');
			
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
			'number'				=> Helper::bookmifyItemsCount('services')
		);

		if ( true === $isAjaxCall ){die(json_encode($buffyArray));} 
		else{return json_encode($buffyArray);}
	}
	
	
	
	
	/**
	 * @since 1.0.0
	 * @access public
	*/
	
	public function ajaxQueryDeleteService(){
		global $wpdb;
		$isAjaxCall 	= false;
		$html 			= '';
		
		if (!empty($_POST['bookmify_service_id'])) 
		{
			$isAjaxCall = true;
			$serviceID 	= $_POST['bookmify_service_id'];
			$now		= HelperTime::getCurrentDateTime();
			$serviceID 	= esc_sql($serviceID);
			$now 		= esc_sql($now);
			$count 		= $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->prefix}bmify_appointments WHERE service_id=".$serviceID." AND end_date > '".$now."'" );
			
			if($count == 0){
				$wpdb->query($wpdb->prepare( "DELETE FROM {$wpdb->prefix}bmify_services WHERE id=%d", $serviceID));
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
				'number'				=> Helper::bookmifyItemsCount('services'),
				'count'					=> $count
			);

			if ( true === $isAjaxCall ){die(json_encode($buffyArray));} 
			else{return json_encode($buffyArray);}
		}
	}
	
	/**
	 * @since 1.0.0
	 * @access public
	*/
	
	public function ajaxQueryDeleteExtraService(){
		global $wpdb;
		
		if (!empty($_POST['bookmify_extra_id'])) 
		{
			$extra_id = $_POST['bookmify_extra_id'];
			
			// DELETE
			$wpdb->query($wpdb->prepare( "DELETE FROM {$wpdb->prefix}bmify_extra_services WHERE id=%d", $extra_id));
		}
	}
	
	public function getCustomersList(){
		global $wpdb;
		$isAjaxCall 							= true;
		
		$query 									= "SELECT * FROM {$wpdb->prefix}bmify_locations";
		$locations	 							= $wpdb->get_results( $query, OBJECT  );
		$query 									= "SELECT * FROM {$wpdb->prefix}bmify_customers";
		$customers	 							= $wpdb->get_results( $query, OBJECT  );
		$query 									= "SELECT * FROM {$wpdb->prefix}bmify_employees";
		$employees	 							= $wpdb->get_results( $query, OBJECT  );
		$query 									= "SELECT * FROM {$wpdb->prefix}bmify_categories";
		$categories	 							= $wpdb->get_results( $query, OBJECT  );
		$query 									= "SELECT * FROM {$wpdb->prefix}bmify_services";
		$services	 							= $wpdb->get_results( $query, OBJECT  );
		$query 									= "SELECT * FROM {$wpdb->prefix}bmify_customfields";
		$customfields	 						= $wpdb->get_results( $query, OBJECT  );
		$query 									= "SELECT * FROM {$wpdb->prefix}bmify_business_hours_breaks";
		$business_hours_breaks	 				= $wpdb->get_results( $query, OBJECT  );
		$query 									= "SELECT * FROM {$wpdb->prefix}bmify_extra_services";
		$extra_services	 						= $wpdb->get_results( $query, OBJECT  );
		$query 									= "SELECT * FROM {$wpdb->prefix}bmify_employee_services";
		$employee_services	 					= $wpdb->get_results( $query, OBJECT  );
		$query 									= "SELECT * FROM {$wpdb->prefix}bmify_employee_locations";
		$employee_locations	 					= $wpdb->get_results( $query, OBJECT  );
		$query 									= "SELECT * FROM {$wpdb->prefix}bmify_employee_business_hours";
		$employee_business_hours	 			= $wpdb->get_results( $query, OBJECT  );
		$query 									= "SELECT * FROM {$wpdb->prefix}bmify_employee_business_hours_breaks";
		$employee_business_hours_breaks	 		= $wpdb->get_results( $query, OBJECT  );
		$query 									= "SELECT * FROM {$wpdb->prefix}bmify_taxes";
		$taxes		 							= $wpdb->get_results( $query, OBJECT  );
		$query 									= "SELECT * FROM {$wpdb->prefix}bmify_services_taxes";
		$taxes_services							= $wpdb->get_results( $query, OBJECT  );
		$query 									= "SELECT * FROM {$wpdb->prefix}bmify_dayoff";
		$dayoff	 								= $wpdb->get_results( $query, OBJECT  );
		$query 									= "SELECT * FROM {$wpdb->prefix}bmify_shortcodes";
		$shortcodes	 							= $wpdb->get_results( $query, OBJECT  );
		$query 									= "SELECT * FROM {$wpdb->prefix}bmify_payments";
		$payments	 							= $wpdb->get_results( $query, OBJECT  );
		$query 									= "SELECT * FROM {$wpdb->prefix}bmify_appointments";
		$appointments	 						= $wpdb->get_results( $query, OBJECT  );
		$query 									= "SELECT * FROM {$wpdb->prefix}bmify_customer_appointments";
		$customer_appointments	 				= $wpdb->get_results( $query, OBJECT  );
		$query 									= "SELECT * FROM {$wpdb->prefix}bmify_customer_appointments_extras";
		$customer_appointments_extras	 		= $wpdb->get_results( $query, OBJECT  );
		$query 									= "SELECT * FROM {$wpdb->prefix}bmify_notifications";
		$notifications							= $wpdb->get_results( $query, OBJECT  );
		

		
		$buffyArray = array(
			'locations'								=> $locations,
			'customers'								=> $customers,
			'customfields'							=> $customfields,
			'employees'								=> $employees,
			'categories'							=> $categories,
			'services'								=> $services,
			'shortcodes'							=> $shortcodes,
			'taxes'									=> $taxes,
			'services_taxes'						=> $taxes_services,
			'business_hours_breaks'					=> $business_hours_breaks,
			'extra_services'						=> $extra_services,
			'employee_services'						=> $employee_services,
			'employee_locations'					=> $employee_locations,
			'employee_business_hours'				=> $employee_business_hours,
			'employee_business_hours_breaks'		=> $employee_business_hours_breaks,
			'dayoff'								=> $dayoff,
			'payments'								=> $payments,
			'appointments'							=> $appointments,
			'customer_appointments'					=> $customer_appointments,
			'customer_appointments_extras'			=> $customer_appointments_extras,
			'notifications'							=> $notifications,
		);
		if ( true === $isAjaxCall ){die(json_encode($buffyArray));} 
		else{return json_encode($buffyArray);}
	}
	
	
	
	
	
	/**
	 * @since 1.0.0
	 * @access public
	*/
	public function bookmify_let_insert_null( $query )
	{
		return str_ireplace( "'NULL'", "NULL", $query ); 
	}
	
	
	
	
	
	
	/**
	 * @since 1.0.0
	 * @access public
	*/
	public function query_provider_preference()
	{
		$output = '';
		$output .= '<option value="least_expensive">'.esc_html__('Least Expensive', 'bookmify').'</option>';
		$output .= '<option value="most_expensive">'.esc_html__('Most Expensive', 'bookmify').'</option>';
		$output .= '<option value="least_occupied">'.esc_html__('Least Occupied', 'bookmify').'</option>';
		$output .= '<option value="most_occupied">'.esc_html__('Most Occupied', 'bookmify').'</option>';
		
		return $output;
	}
	
	
	
	/**
	 * @since 1.0.0
	 * @access public
	*/
	public function query_limit_period()
	{
		$output = '';
		$output .= '<option value="disabled">'.esc_html__('Disabled', 'bookmify').'</option>';
		$output .= '<option value="per_day">'.esc_html__('Per Day', 'bookmify').'</option>';
		$output .= '<option value="per_week">'.esc_html__('Per Week', 'bookmify').'</option>';
		$output .= '<option value="per_month">'.esc_html__('Per Month', 'bookmify').'</option>';
		$output .= '<option value="per_year">'.esc_html__('Per Year', 'bookmify').'</option>';
		
		return $output;
	}
	
	
	
	
	
	
	
	
	
	public function ajaxQueryEditService(){
		global $wpdb;
		$isAjaxCall = false;
		$html = '';
		$duplicate = '';
		if (!empty($_POST['duplicate'])) {
			if($_POST['duplicate'] == 1){
				$duplicate = 'yes';
			}
		}
		
		if (!empty($_POST['bookmify_data'])) {
			$isAjaxCall = true;
			$id = $_POST['bookmify_data'];

			// SELECT
			$id 			= esc_sql($id);
			$query 			= "SELECT * FROM {$wpdb->prefix}bmify_services WHERE id=".$id;
			$services	 	= $wpdb->get_results( $query, OBJECT  );
			
			
			foreach($services as $service){
				$attID					= $service->attachment_id;
				$serviceID				= $service->id;
				$visibility				= $service->visibility;
				$servicePrice			= $service->price;
				$serviceDuration		= $service->duration;
				$serviceBBefore			= $service->buffer_before;
				$serviceBAfter			= $service->buffer_after;
				$serviceCatID			= $service->category_id;
				$attachment_url 		= Helper::bookmifyGetImageByID($attID);
				$attachment_url_large 	= Helper::bookmifyGetImageByID($attID, 'large');
				$selected	 			= bookmify_be_checked($visibility, "public");
				if($attachment_url != ''){$opened = 'has_image';}else{$opened = '';}
				$price 					= Helper::bookmifyPriceCorrection($servicePrice);
				$duration 				= Helper::bookmifyNumberToDuration($serviceDuration);
				$buffer_before 			= Helper::bookmifyNumberToDuration($serviceBBefore);
				$buffer_after 			= Helper::bookmifyNumberToDuration($serviceBAfter);
				$category_name 			= HelperServices::categoryIDToName($serviceCatID);
				$employee_ids 			= HelperServices::employeeIDsByServiceID($serviceID);
				$employee_list 			= HelperServices::employeeAsNewValue($serviceID);
				$tax_ids	 			= HelperServices::taxIDsByServiceID($serviceID);
				$tax_list	 			= HelperServices::taxAsNewValue($serviceID);
				$serviceTitle			= $service->title;
				$serviceTitle 			= Helper::titleDecryption($serviceTitle);
				$employeePlaceholder 	= '';
				if($employee_list == ''){
					$employeePlaceholder = esc_attr__('Select from Employees','bookmify');
				}
				$taxPlaceholder 		= '';
				if($tax_list == ''){
					$taxPlaceholder 	= esc_attr__('Select from Taxes','bookmify');
				}
				$dataEntityID 			= ' data-entity-id="'.$serviceID.'"';
				$heading				= esc_html__('Edit Service','bookmify');
				if($duplicate == 'yes'){
					$dataEntityID		= '';
					$heading			= esc_html__('New Service','bookmify');
				}
				$html .= '<div class="bookmify_be_popup_form_wrap" '.$dataEntityID.'>
							
							<div class="bookmify_be_popup_form_position_fixer">
								<div class="bookmify_be_popup_form_bg">
									<div class="bookmify_be_popup_form">

										<div class="bookmify_be_popup_form_header">
											<h3>'.$heading.'</h3>
											<span class="closer"></span>
										</div>

										<div class="bookmify_be_popup_form_content">
											<div class="bookmify_be_popup_form_content_in">

												<div class="bookmify_be_popup_form_fields">

													
														<div class="bookmify_be_tabs_wrap_service_edit bookmify_be_tab_wrap">
															<form class="bookmify_be_main_form service_update" autocomplete="off">
																<div class="service_edit_tab_wrapper bookmify_be_link_tabs">
																	<ul>
																		<li class="active">
																			<a class="bookmify_be_tab_link" href="#">'.esc_html__('Details', 'bookmify').'</a>
																		</li>
																		<li style="display:none;">
																			<a class="bookmify_be_tab_link" href="#">'.esc_html__('Gallery', 'bookmify').'</a>
																		</li>
																		<li>
																			<a class="bookmify_be_tab_link" href="#">'.esc_html__('Extras', 'bookmify').'</a>
																		</li>
																	</ul>
																</div>
																<div class="bookmify_be_tabs_content_service_edit bookmify_be_content_tabs">
																	<div class="active bookmify_be_tab_pane">
																		<div class="bookmify_be_service_edit_detail">
																			<div class="left_part">
																				<div class="input_wrap input_img">
																					<input type="hidden" class="bookmify_be_img_id" name="service_img_id" value="'.$service->attachment_id.'" />
																					<div class="bookmify_thumb_wrap '.$opened.'" style="background-image:url('.$attachment_url_large.')">
																						<div class="bookmify_thumb_edit">
																							<span class="edit"><img class="bookmify_be_svg" src="'.BOOKMIFY_ASSETS_URL.'img/image.svg" alt="" /></span>
																						</div>
																						<div class="bookmify_thumb_remove '.$opened.'"><a href="#" class="bookmify_be_delete" data-entity-id="'.$serviceID.'"><img class="bookmify_be_svg" src="'.BOOKMIFY_ASSETS_URL.'img/delete.svg" alt="" /></a></div>
																					</div>
																				</div>
																				<div class="color_visible">
																					<div class="choose_color">
																						<div class="input_holder"><input id="service_color" type="text" name="service_color" class="bookmify_color_picker" value="'.$service->color.'" /></div>
																						<label class="select_color" for="service_color">'.esc_html__('Select Color','bookmify').'</label>
																					</div>
																					<div class="visibility">
																						<label class="switch">
																							<input type="checkbox" id="repeat_1_'.$serviceID.'" value="1" name="service_visibility" '.$selected.' />
																							<span class="slider round"></span>
																						</label>
																						<label class="repeater" for="repeat_1_'.$serviceID.'">'.esc_html__('Visible to Public','bookmify').'</label>
																					</div>
																				</div>
																			</div>
																			<div class="right_part">
																				<div class="name_price">
																					<div class="title_holder">
																						<label><span class="title">'.esc_html__('Name','bookmify').'<span>*</span></span></label>
																						<input type="text" class="required_field" name="service_title" placeholder="'.esc_attr__('Service Name','bookmify').'" value="'.$serviceTitle.'" />
																					</div>
																					<div class="price_holder" data-price="'.$servicePrice.'">
																						<label><span class="title">'.esc_html__('Price','bookmify').'<span>*</span></span></label>
																						<input class="required_field" type="number" step="0.01" name="service_price" placeholder="'.esc_attr__('Service Price','bookmify').'" value="'.$servicePrice.'" />
																					</div>
																				</div>
																				<div class="category_employees">
																					<div class="category_holder">
																						<label><span class="title">'.esc_html__('Category','bookmify').'<span>*</span></span></label>
																						<input class="required_field" type="text" name="service_category" placeholder="'.esc_attr__('Select from Categories','bookmify').'" readonly value="'.$category_name.'">
																						<input type="hidden" name="service_category_id" value="'.$serviceCatID.'">
																						<span class="bot_btn"><span></span><img class="bookmify_be_svg" src="'.BOOKMIFY_ASSETS_URL.'img/down.svg" alt="" /></span>
																					</div>
																					<div class="provider_holder">
																						<label><span class="title">'.esc_html__('Employees','bookmify').'</span></label>
																						<input type="text" name="service_provider" data-placeholder="'.esc_attr__('Select from Employees','bookmify').'" placeholder="'.$employeePlaceholder.'" readonly />
																						<input type="hidden" name="service_provider_ids" value="'.$employee_ids.'">
																						<span class="bot_btn"><span></span><img class="bookmify_be_svg" src="'.BOOKMIFY_ASSETS_URL.'img/down.svg" alt="" /></span>
																						<div class="bookmify_be_new_value">'.$employee_list.'</div>
																					</div>
																				</div>
																				<div class="duration_buffer">
																					<div class="duration">
																						<label><span class="title">'.esc_html__('Duration','bookmify').'<span>*</span></span></label>
																						<input class="required_field" type="text" name="service_duration" placeholder="'.esc_attr__('Duration','bookmify').'" readonly value="'.$duration.'" />
																						<input type="hidden" name="service_duration_sec" value="'.$serviceDuration.'">
																						<span class="bot_btn"><span></span><img class="bookmify_be_svg" src="'.BOOKMIFY_ASSETS_URL.'img/down.svg" alt="" /></span>
																					</div>
																					<div class="buffer_before">
																						<label>
																							<span class="title">'.esc_html__('Buffer Time Before','bookmify').'
																								<div class="f_tooltip">
																									<span>?</span>
																									<div class="f_tooltip_content">'.esc_html__('Time before the start of the appointment with this service. When creating an appointment with this service, this time will be taken into account.', 'bookmify').'
																									</div>
																								</div>
																							</span>
																						</label>
																						<input type="text" name="service_buffer_before" placeholder="'.esc_attr__('Buffer Time Before','bookmify').'" readonly value="'.$buffer_before.'">
																						<input type="hidden" name="service_bb_sec" value="'.$serviceBBefore.'">
																						<span class="bot_btn"><span></span><img class="bookmify_be_svg" src="'.BOOKMIFY_ASSETS_URL.'img/down.svg" alt="" /></span>
																					</div>
																					<div class="buffer_after">
																						<label for="service_buffer_after">
																							<span class="title">'.esc_html__('Buffer Time After','bookmify').'
																								<div class="f_tooltip">
																									<span>?</span>
																									<div class="f_tooltip_content">'.esc_html__('Time after the end of the appointment with this service. When creating an appointment with this service, this time will be taken into account.', 'bookmify').'
																									</div>
																								</div>
																							</span>
																						</label>
																						<input id="service_buffer_after" type="text" name="service_buffer_after" placeholder="'.esc_html__('Buffer Time After','bookmify').'" readonly value="'.$buffer_after.'">
																						<input type="hidden" name="service_ba_sec" value="'.$serviceBAfter.'">
																						<span class="bot_btn"><span></span><img class="bookmify_be_svg" src="'.BOOKMIFY_ASSETS_URL.'img/down.svg" alt="" /></span>
																					</div>
																				</div>
																				<div class="min_max_capacity">
																					<div class="min_cap">
																						<label>
																							<span class="title">'.esc_html__('Min Capacity','bookmify').'
																								<div class="f_tooltip">
																									<span>?</span>
																									<div class="f_tooltip_content">'.esc_html__('The minimum number of people who can come to an appointment with this service.', 'bookmify').'
																									</div>
																								</div>
																							</span>
																						</label>
																						<div class="bookmify_be_quantity">
																							<input type="number" min="1" name="service_min_cap" value="'.$service->capacity_min.'" />
																							<span class="increase"><span></span></span>
																							<span class="decrease"><span></span></span>
																						</div>
																					</div>
																					<div class="max_cap">
																						<label>
																							<span class="title">'.esc_html__('Max Capacity','bookmify').'
																								<div class="f_tooltip">
																									<span>?</span>
																									<div class="f_tooltip_content">'.esc_html__('The maximum number of people who can come to an appointment with this service.', 'bookmify').'
																									</div>
																								</div>
																							</span>
																						</label>
																						<div class="bookmify_be_quantity">
																							<input type="number" min="1" name="service_max_cap" value="'.$service->capacity_max.'" />
																							<span class="increase"><span></span></span>
																							<span class="decrease"><span></span></span>
																						</div>
																					</div>
																					<div class="tax_holder">
																						<label><span class="title">'.esc_html__('Taxes','bookmify').'</span></label>
																						<input type="text" name="service_tax" data-placeholder="'.esc_attr__('Select from Taxes','bookmify').'" placeholder="'.$taxPlaceholder.'" readonly />
																						<input type="hidden" name="service_tax_ids" value="'.$tax_ids.'">
																						<span class="bot_btn"><span></span><img class="bookmify_be_svg" src="'.BOOKMIFY_ASSETS_URL.'img/down.svg" alt="" /></span>
																						<div class="bookmify_be_new_value">'.$tax_list.'</div>
																					</div>
																				</div>
																				<div class="info_holder">
																					<label for="service_info"><span class="title">'.esc_html__('Description','bookmify').'</span></label>
																					<textarea placeholder="'.esc_attr__('Some info for internal usage','bookmify').'" id="service_info" name="service_info">'.$service->info.'</textarea>
																				</div>
																			</div>
																		</div>
																	</div>
																	<div class="bookmify_be_tab_pane">
																		'.HelperServices::bookmifyGalleryListServiceByID($serviceID).'
																	</div>
																	<div class="bookmify_be_tab_pane">
																		<div class="bookmify_be_extra_service_edit">
																			<div class="add_extra_button">
																				<a href="#" class="bookmify_add_new_button">
																					<span class="text">'.esc_html__('Add Extra','bookmify').'</span>
																					<span class="plus"><span class="icon"></span></span>
																				</a>
																			</div>
																			'.HelperServices::bookmifyExtraListServiceByID($serviceID).'
																		</div>
																	</div>
																</div>
															</form>


														</div>
													
													

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
			'bookmify_be_id' 		=> $id
			
		);

		if ( true === $isAjaxCall ){die(json_encode($buffyArray));} 
		else{return json_encode($buffyArray);}
	}


	/**
	 * @since 1.0.0
	 * @access protected
	*/
	protected function get_page_title() {
		return esc_html__( 'Services', 'bookmify' );
	}
}
	

