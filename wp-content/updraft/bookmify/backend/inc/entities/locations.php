<?php
namespace Bookmify;

use Bookmify\Helper;
use Bookmify\HelperAdmin;
use Bookmify\HelperLocations;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


class Locations{

	const PAGE_ID = 'bookmify_locations';
	
	private $per_page;
	/**
	 * @since 1.0.0
	 * @access public
	*/
	public function __construct() {
		$this->assignValToVar();
		add_action( 'admin_menu', [ $this, 'registerAdminMenu' ], 20 );
		add_action( 'wp_ajax_locationsListAjax', [$this, 'locationsListAjax'] );
		
		
		
		
		add_action( 'wp_ajax_ajaxQueryEditLocation', [$this, 'ajaxQueryEditLocation'] );
		add_action( 'wp_ajax_ajaxQueryDeleteLocation', [$this, 'ajaxQueryDeleteLocation'] );
		add_action( 'wp_ajax_ajaxQueryInsertOrUpdateLocation', [$this, 'ajaxQueryInsertOrUpdateLocation'] );
		
	}
	
	public function assignValToVar(){
		$this->per_page = get_option('bookmify_be_locations_pp', 10);
	}
	
	/**
	 * @since 1.0.0
	 * @access public
	*/
	public function registerAdminMenu() {

		add_submenu_page(
			BOOKMIFY_MENU,
			esc_html__( 'Locations', 'bookmify' ),
			esc_html__( 'Locations', 'bookmify' ),
			'bookmify_be_read_locations',
			self::PAGE_ID,
			[ $this, 'displayLocationsPage' ]
		);
	}
	
	public function displayLocationsPage() {
		global $wpdb;

		echo HelperAdmin::bookmifyAdminContentStart();
		?>
		
		<div class="bookmify_be_content_wrap bookmify_be_locations_page">
			<div class="bookmify_be_page_title">
				<h3><?php echo esc_html($this->get_page_title()); ?><span class="count"><?php echo Helper::bookmifyItemsCount('locations');?></span></h3>
				<div class="bookmify_be_add_new_item bookmify_be_add_new_location">
					<a href="#" class="bookmify_button add_new bookmify_add_new_button">
						<span class="text"><?php esc_html_e('Add New Location','bookmify');?></span>
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

				<div class="bookmify_be_locations">
					
					<!-- Location Header -->
					<div class="bookmify_be_locations_header">
						<div class="bookmify_be_locations_header_in">
							<span class="list_name"><?php esc_html_e('Name', 'bookmify');?></span>
							<span class="list_location"><?php esc_html_e('Locations', 'bookmify');?></span>
						</div>
					</div>
					<!-- /Location Header -->
					
					<!-- Location List -->
					<div class="bookmify_be_locations_list">
						<?php echo HelperLocations::allNanoInOne(); ?>
						<?php echo $this->locationsList(); ?>
					</div>
					<!-- /Location Header -->
					
				</div>
				
			</div>
			<?php echo HelperLocations::clonableForm(); ?>
		</div>
		<?php echo HelperAdmin::bookmifyAdminContentEnd();
		
	}
	

	/*
	 * @since 1.0.0
	 * @access public
	*/
	public function locationsList(){
		global $wpdb;
		
		$query 			= "SELECT * FROM {$wpdb->prefix}bmify_locations";
		
		$Querify  		= new Querify( $query, 'location' );
		$locations      = $Querify->getData( 10 ); // locations per page
		

		$html = '<div class="bookmify_be_list location_list">';
		if(count($locations->data) == 0){
			$html .= Helper::bookmifyBeNoItems();
		}
		for($i = 0; $i < count( $locations->data ); $i++){
			
			$attachment_url = Helper::bookmifyGetImageByID($locations->data[$i]->attachment_id);
			
			// FIELD MAIN HTML
			$html .= '<div class="bookmify_be_list_item location_item" data-location-id="'.$locations->data[$i]->id.'">
						<div class="bookmify_be_list_item_in">
							<div class="bookmify_be_list_item_header">
								<div class="header_in">
									<div class="info_holder">
										<div class="info_in">
											<span class="location_img_holder" style="background-image:url('.$attachment_url.')"></span>
											<span class="location_name">
												<span class="l_name">'.$locations->data[$i]->title.'</span>
												<span class="l_address">'.$locations->data[$i]->address.'</span>
											</span>
											<span class="location_address">'.$locations->data[$i]->address.'</span>
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
											<a href="#" class="bookmify_be_delete" data-entity-id="'.$locations->data[$i]->id.'">
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
		
		$html .= $Querify->getPagination( 1, 'bookmify_be_pagination location');

		return $html;
	}
	public function ajaxQueryInsertOrUpdateLocation(){
		global $wpdb;
		$isAjaxCall 				= false;
		
		// **************************************************************************************************************************
		// UPDATE EXISTING EMPLOYEE
		// **************************************************************************************************************************
		if ($_POST['insertOrUpdate'] == 'update') {
			$l_id = $l_title = $l_address = $l_info = $l_img_id = $l_employees = '';
			$oldFolk = $chosenFolk = $employees_ids = [];

			if(!empty($_POST['bookmify_data'])){

				$locations 			= json_decode(stripslashes($_POST['bookmify_data']));

				foreach($locations as $location){

					$l_id 			= $location->id;
					$l_title 		= $location->title;
					$l_address 		= $location->address;
					$l_info 		= $location->info;
					$l_img_id 		= $location->imgID;
					$l_employees 	= $location->employeesIDs;
				}
			}

			// update location table
			$wpdb->query($wpdb->prepare( "UPDATE {$wpdb->prefix}bmify_locations SET title=%s, address=%s, info=%s, attachment_id=%d WHERE id=%d", $l_title, $l_address, $l_info, $l_img_id, $l_id));
			
			if($l_employees != ""){
				$employees_ids	= explode(",", $l_employees);
			}
			

			// Get all existing service employees
			$l_id  				= esc_sql($l_id);
			$query 				= "SELECT employee_id FROM {$wpdb->prefix}bmify_employee_locations WHERE location_id=".$l_id;
			$old_employees 		= $wpdb->get_results( $query, OBJECT  );
			
			
			foreach($old_employees as $old_employee){
				$oldFolk[] 		= $old_employee->employee_id;
			}
			foreach($employees_ids as $employee_id){
				$chosenFolk[] 	= $employee_id;
			}

			$existing_employees = array_values(array_intersect($oldFolk, $chosenFolk)); 	// existing employees
			$new_employees 		= array_values(array_diff($chosenFolk,$oldFolk)); 			// new employees
			$released_employees = array_values(array_diff($oldFolk,$chosenFolk)); 			// released employees

			// Insert new employees
			if(!empty($new_employees)){
				foreach($new_employees as $new_employee){
					$wpdb->query($wpdb->prepare( "INSERT INTO {$wpdb->prefix}bmify_employee_locations ( location_id, employee_id ) VALUES ( %d, %d)", $l_id, $new_employee));
				}
			}

			// Delete released employees
			if(!empty($released_employees)){
				foreach($released_employees as $released_employee){
					$wpdb->query($wpdb->prepare( "DELETE FROM {$wpdb->prefix}bmify_employee_locations WHERE employee_id=%d", $released_employee));
				}
			}
		
		}else{
		// **************************************************************************************************************************
		// INSERT NEW EMPLOYEE
		// **************************************************************************************************************************
			$locationID = $l_title = $l_address = $l_info = $l_img_id = '';
			$l_employees = '';
			$employeesIDs = [];
			if(!empty($_POST['bookmify_data'])){

				$locations = json_decode(stripslashes($_POST['bookmify_data']));

				foreach($locations as $location){
					$l_title 		= $location->title;
					$l_address 		= $location->address;
					$l_info 		= $location->info;
					$l_img_id 		= $location->imgID;
					$l_employees 	= $location->employeesIDs;
				}
			}

			
			// INSERT (Best Practice)
			$wpdb->query($wpdb->prepare( "INSERT INTO {$wpdb->prefix}bmify_locations ( title, address, info, attachment_id ) VALUES ( %s, %s, %s, %d )", $l_title, $l_address, $l_info, $l_img_id));
			
			$query 			= "SELECT id FROM {$wpdb->prefix}bmify_locations ORDER BY id DESC LIMIT 1";
			$results 		= $wpdb->get_results( $query, OBJECT  );
			foreach($results as $result){
				$locationID	= $result->id;
			}
			
			if($l_employees != ""){
				$employeesIDs	= explode(",", $l_employees);
			}
			
			if(!empty($employeesIDs)){
				foreach($employeesIDs as $employeeID){
					$wpdb->query($wpdb->prepare( "INSERT INTO {$wpdb->prefix}bmify_employee_locations ( location_id, employee_id ) VALUES ( %d, %d)", $locationID, $employeeID));
				}
			}
			
		}
		$page = 1;
		
		if (!empty($_POST['bookmify_page'])){
			$isAjaxCall 		= true;
			$page       		= $_POST['bookmify_page'];
		}
		
		if(!empty($_POST['do'])){
			// SELECT
			$isAjaxCall = true;
			$query 		= "SELECT * FROM {$wpdb->prefix}bmify_locations";

			$Querify  	= new Querify( $query, 'location' );
			$locations  = $Querify->getData( 10, $page );

			$html = '<div class="bookmify_be_list location_list">';
			if(count($locations->data) == 0){
				$html .= Helper::bookmifyBeNoItems();
			}
			for($i = 0; $i < count( $locations->data ); $i++){
			
				$attachment_url = Helper::bookmifyGetImageByID($locations->data[$i]->attachment_id);

				// FIELD MAIN HTML
				$html .= '<div class="bookmify_be_list_item location_item bookmify_be_animated" data-location-id="'.$locations->data[$i]->id.'">
							<div class="bookmify_be_list_item_in">
								<div class="bookmify_be_list_item_header">
									<div class="header_in">
										<div class="info_holder">
											<div class="info_in">
												<span class="location_img_holder" style="background-image:url('.$attachment_url.')"></span>
												<span class="location_name">
													<span class="l_name">'.$locations->data[$i]->title.'</span>
													<span class="l_address">'.$locations->data[$i]->address.'</span>
												</span>
												<span class="location_address">'.$locations->data[$i]->address.'</span>
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
												<a href="#" class="bookmify_be_delete" data-entity-id="'.$locations->data[$i]->id.'">
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

			$html .= $Querify->getPagination( 1, 'bookmify_be_pagination location');
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
			'number'				=> Helper::bookmifyItemsCount('locations')
		);

		if ( true === $isAjaxCall ){die(json_encode($buffyArray));} 
		else{return json_encode($buffyArray);}
	}
	
	
	
	/*
	 * @since 1.0.0
	 * @access public
	*/
	public function locationsListAjax(){
		global $wpdb;
		
		$isAjaxCall = false;
		$page = 1;
		
		if (!empty($_POST['bookmify_page'])){
			$isAjaxCall 		= true;
			$page       		= $_POST['bookmify_page'];
		}
		
		$query = "SELECT * FROM {$wpdb->prefix}bmify_locations";
		
		$Querify  		= new Querify( $query, 'location' );
		$locations      = $Querify->getData( 10, $page );
		

		$html = '<div class="bookmify_be_list location_list">';
		if(count($locations->data) == 0){
			$html .= Helper::bookmifyBeNoItems();
		}
		for($i = 0; $i < count( $locations->data ); $i++){
			$attachment_url = Helper::bookmifyGetImageByID($locations->data[$i]->attachment_id);
			
			// FIELD MAIN HTML
			$html .= '<div class="bookmify_be_list_item bookmify_be_animated location_item" data-location-id="'.$locations->data[$i]->id.'">
						<div class="bookmify_be_list_item_in">
							<div class="bookmify_be_list_item_header">
								<div class="header_in">
									<div class="info_holder">
										<div class="info_in">
											<span class="location_img_holder" style="background-image:url('.$attachment_url.')"></span>
											<span class="location_name">
												<span class="l_name">'.$locations->data[$i]->title.'</span>
												<span class="l_address">'.$locations->data[$i]->address.'</span>
											</span>
											<span class="location_address">'.$locations->data[$i]->address.'</span>
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
											<a href="#" class="bookmify_be_delete" data-entity-id="'.$locations->data[$i]->id.'">
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
		
		$html .= $Querify->getPagination( 1, 'bookmify_be_pagination location');
		
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
	public function ajaxQueryDeleteLocation(){
		global $wpdb;
		$id 			= '';
		$isAjaxCall 	= false;
		$html			= '';
		if(!empty($_POST['bookmify_data'])){
			$isAjaxCall = true;
			$id 		= $_POST['bookmify_data'];
			
			$wpdb->query($wpdb->prepare( "DELETE FROM {$wpdb->prefix}bmify_employee_locations WHERE location_id=%d", $id));
			$wpdb->query($wpdb->prepare( "DELETE FROM {$wpdb->prefix}bmify_locations WHERE id=%d", $id));
			
			
			$page = 1;
		
			if (!empty($_POST['bookmify_page'])){
				$isAjaxCall 		= true;
				$page       		= $_POST['bookmify_page'];
			}

			// SELECT
			$isAjaxCall = true;
			$query 		= "SELECT * FROM {$wpdb->prefix}bmify_locations";

			$Querify  	= new Querify( $query, 'location' );
			$locations  = $Querify->getData( 10, $page );

			$html = '<div class="bookmify_be_list location_list">';
			if(count($locations->data) == 0){
				$html .= Helper::bookmifyBeNoItems();
			}
			for($i = 0; $i < count( $locations->data ); $i++){

				$attachment_url = Helper::bookmifyGetImageByID($locations->data[$i]->attachment_id);

				// FIELD MAIN HTML
				$html .= '<div class="bookmify_be_list_item location_item bookmify_be_animated" data-location-id="'.$locations->data[$i]->id.'">
							<div class="bookmify_be_list_item_in">
								<div class="bookmify_be_list_item_header">
									<div class="header_in">
										<div class="info_holder">
											<div class="info_in">
												<span class="location_img_holder" style="background-image:url('.$attachment_url.')"></span>
												<span class="location_name">
													<span class="l_name">'.$locations->data[$i]->title.'</span>
													<span class="l_address">'.$locations->data[$i]->address.'</span>
												</span>
												<span class="location_address">'.$locations->data[$i]->address.'</span>
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
												<a href="#" class="bookmify_be_delete" data-entity-id="'.$locations->data[$i]->id.'">
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


				$html .= '</div>';

				$html .= $Querify->getPagination( 1, 'bookmify_be_pagination location');
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
				'number'				=> Helper::bookmifyItemsCount('locations')
			);

			if ( true === $isAjaxCall ){die(json_encode($buffyArray));} 
			else{return json_encode($buffyArray);}
		}
	}
	
	
	
	
	
	
	
	
	
	public function ajaxQueryEditLocation(){
		global $wpdb;
		$isAjaxCall = false;
		$html 		= '';
		
		if (!empty($_POST['bookmify_data'])) {
			$isAjaxCall = true;
			$id 		= $_POST['bookmify_data'];

			// SELECT
			$id 			= esc_sql($id);
			$query 			= "SELECT * FROM {$wpdb->prefix}bmify_locations WHERE id=".$id;
			$locations	 	= $wpdb->get_results( $query, OBJECT  );
			
			
			foreach($locations as $location){
				$ID			= $location->id;
				
				$opened 	= '';
				$attachment_url 		= Helper::bookmifyGetImageByID($location->attachment_id);
				$attachment_url_large 	= Helper::bookmifyGetImageByID($location->attachment_id, 'large');
				if($attachment_url != ''){$opened = 'has_image';}
				
				$html .= '<div class="bookmify_be_popup_form_wrap" data-entity-id="'.$ID.'">
							
							<div class="bookmify_be_popup_form_position_fixer">
								<div class="bookmify_be_popup_form_bg">
									<div class="bookmify_be_popup_form">

										<div class="bookmify_be_popup_form_header">
											<h3>'.esc_html__('Edit Location','bookmify').'</h3>
											<span class="closer"></span>
										</div>

										<div class="bookmify_be_popup_form_content">
											<div class="bookmify_be_popup_form_content_in">

												<div class="bookmify_be_popup_form_fields">

													
													<div class="bookmify_be_form_wrap">
														<div class="left_part">
															<div class="input_wrap input_img">
																<input type="hidden" class="bookmify_be_img_id" name="service_img_id" value="'.$location->attachment_id.'" />
																<div class="bookmify_thumb_wrap '.$opened.'" style="background-image:url('.$attachment_url_large.')">
																	<div class="bookmify_thumb_edit">
																		<span class="edit"><img class="bookmify_be_svg" src="'.BOOKMIFY_ASSETS_URL.'img/image.svg" alt="" /></span>
																	</div>
																	<div class="bookmify_thumb_remove '.$opened.'"><a href="#" class="bookmify_be_delete" data-entity-id="'.$ID.'"><img class="bookmify_be_svg" src="'.BOOKMIFY_ASSETS_URL.'img/delete.svg" alt="" /></a></div>
																</div>
															</div>
														</div>

														<div class="right_part">
															<div class="input_wrap_row">
																<div class="input_wrap name_holder">
																	<label><span class="title">'.esc_html__('Name','bookmify').'<span>*</span></span></label>
																	<input type="text" class="location_name required_field" placeholder="'.esc_attr__('Location Name','bookmify').'" value="'.$location->title.'" />
																</div>
																<div class="input_wrap address_holder">
																	<label><span class="title">'.esc_html__('Location','bookmify').'<span>*</span></span></label>
																	<input placeholder="'.esc_attr__('Location Address','bookmify').'" type="text" class="location_address required_field" value="'.$location->address.'" />
																</div>
															</div>';


												$attached_entity_ids 	= HelperLocations::locationEmployeesIds($ID);
												$attached_entity_names 	= HelperLocations::locationEmployeesNames($ID);

												$attached_entity_placeholder = '';
												if($attached_entity_names == ''){
													$attached_entity_placeholder = esc_attr__('Select from Employees','bookmify');
												}


												$html .=    '<div class="input_wrap_row">
																<div class="input_wrap location_employees_holder">
																	<label><span class="title">'.esc_html__('Employees','bookmify').'</span></label>
																	<div class="bookmify_be_custom_select">
																		<input type="text" data-placeholder="'.esc_attr__('Select from Employees','bookmify').'" placeholder="'.$attached_entity_placeholder.'" readonly />
																		<input type="hidden" class="location_employees_ids" value="'.$attached_entity_ids.'">
																		<span class="bot_btn">
																			<span></span>
																			<img class="bookmify_be_svg" src="'.BOOKMIFY_ASSETS_URL.'img/down.svg" alt="" />
																		</span>
																		<div class="bookmify_be_new_value">'.$attached_entity_names.'</div>
																	</div>
																</div>
															</div>

															<div class="input_wrap_row">
																<div class="input_wrap info_holder">
																	<label><span class="title">'.esc_html__('Description','bookmify').'</span></label>
																	<textarea class="location_info" placeholder="'.esc_attr__('Some info','bookmify').'">'.$location->info.'</textarea>
																</div>
															</div>

															
														

														</div>
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
			
		);

		if ( true === $isAjaxCall ){die(json_encode($buffyArray));} 
		else{return json_encode($buffyArray);}
	}
	
	/**
	 * @since 1.0.0
	 * @access protected
	*/
	protected function get_page_title() {
		return esc_html__( 'Locations', 'bookmify' );
	}
}
	

