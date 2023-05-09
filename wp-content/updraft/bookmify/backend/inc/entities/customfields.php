<?php
namespace Bookmify;

use Bookmify\Helper;
use Bookmify\HelperAdmin;
use Bookmify\HelperCustomfields;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Customfields{
	
	const PAGE_ID = 'bookmify_customfields';
	protected $existing_customfields;
	
	/**
	 * @since 1.0.0
	 * @access public
	*/
	public function __construct() 
	{
		add_action( 'admin_menu', [ $this, 'registerAdminMenu' ], 20 );
		
		
		
		add_action( 'wp_ajax_ajaxQueryDeleteCF', [$this, 'ajaxQueryDeleteCF'] );
		add_action( 'wp_ajax_ajaxQueryEditCF', [$this, 'ajaxQueryEditCF'] );
		add_action( 'wp_ajax_ajaxQueryReorderCF', [$this, 'ajaxQueryReorderCF'] );
		add_action( 'wp_ajax_ajaxQueryInsertOrUpdateCF', [$this, 'ajaxQueryInsertOrUpdateCF'] );
	}
	
	/**
	 * @since 1.0.0
	 * @access public
	*/
	public function registerAdminMenu() 
	{
		add_submenu_page(
			BOOKMIFY_MENU,
			esc_html__( 'Customfields', 'bookmify' ),
			esc_html__( 'Customfields', 'bookmify' ),
			'bookmify_be_read_customfields',
			self::PAGE_ID,
			[ $this, 'displayCFPage' ]
		);
	}
	
	public function displayCFPage() 
	{
		global $wpdb;

		echo HelperAdmin::bookmifyAdminContentStart();
		?>
		
		<div class="bookmify_be_content_wrap bookmify_be_customfields_page">
			<div class="bookmify_be_page_title">
				<h3><?php echo esc_html($this->get_page_title()); ?><span class="count"><?php echo Helper::bookmifyItemsCount('customfields'); ?></span></h3>
			</div>
			<div class="bookmify_be_page_content">

				<div class="bookmify_be_customfields">
					
					<div class="bookmify_be_cf_type_buttons">
						<div class="cf_type_buttons_holder">
							<div class="cf_type_button"><a href="#" class="add checkbox" data-field-type="checkbox"><span></span> <?php esc_html_e( 'Checkbox', 'bookmify' ); ?></a></div>
							<div class="cf_type_button"><a href="#" class="add radiobuttons" data-field-type="radiobuttons"><span></span><?php esc_html_e( 'Radio Buttons', 'bookmify' ); ?></a></div>
							<div class="cf_type_button"><a href="#" class="add selectbox" data-field-type="selectbox"><span></span><?php esc_html_e( 'Select', 'bookmify' ); ?></a></div>
							<div class="cf_type_button"><a href="#" class="add text" data-field-type="text"><span></span><?php esc_html_e( 'Text Field', 'bookmify' ); ?></a></div>
							<div class="cf_type_button"><a href="#" class="add textarea" data-field-type="textarea"><span></span><?php esc_html_e( 'Textarea', 'bookmify' ); ?></a></div>
							<div class="cf_type_button"><a href="#" class="add textcontent
							" data-field-type="textcontent"><span></span><?php esc_html_e( 'Text Content', 'bookmify' ); ?></a></div>
						</div>
					</div>
					
					<div class="bookmify_be_customfields_list">
						<?php echo $this->customfieldsList(); ?>
						<?php echo HelperCustomfields::allNanoInOne(); ?>
					</div>
					
					
					
					<?php echo HelperCustomfields::clonableOption(); ?>
					
					
				</div>
				
			</div>
			<?php echo HelperCustomfields::clonableForm(); ?>
		</div>
		<?php echo HelperAdmin::bookmifyAdminContentEnd();
		
	}
	

	/**
	 * @since 1.0.0
	 * @access public
	*/
	public function customfieldsList()
	{
		global $wpdb;
		
		$query 	= "SELECT id,cf_type,cf_label FROM {$wpdb->prefix}bmify_customfields ORDER BY position, id";
		$cfs    = $wpdb->get_results( $query );
		
		$html 	= '';
		$html  .= '<div class="bookmify_be_list cfs_list">';
		if(count($cfs) == 0){
			$html .= Helper::bookmifyBeNoItems();
		}
		foreach($cfs as $cf){
			$cfID 		= $cf->id;
			$cfType 	= $cf->cf_type;
			$cfLabel 	= $cf->cf_label;
			switch($cfType){
				case 'checkbox': 		$cfTypetext = esc_html__('Checkbox', 'bookmify');  		break;
				case 'selectbox': 		$cfTypetext = esc_html__('Select', 'bookmify'); 		break;
				case 'radiobuttons': 	$cfTypetext = esc_html__('Radio Buttons', 'bookmify'); 	break;
				case 'text': 			$cfTypetext = esc_html__('Text', 'bookmify'); 			break;
				case 'textarea': 		$cfTypetext = esc_html__('Textarea', 'bookmify'); 		break;
				case 'textcontent': 	$cfTypetext = esc_html__('Text Content', 'bookmify'); 	break;
			}
			$html .= '<div class="bookmify_be_list_item cfs_item" data-cf-id="'.$cfID.'" data-cf-type="'.$cfType.'">
						<div class="bookmify_be_list_item_in">
							<div class="bookmify_be_list_item_header">
								<div class="header_in">
									<div class="info_holder">
										<div class="info_in">
											<span class="cf_label_holder">
												<span class="cf_type">'.$cfTypetext.'</span>
												<span class="cf_label">'.$cfLabel.'</span>
											</span>

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
										<div class="btn_item btn_delete">
											<a href="#" class="bookmify_be_delete" data-entity-id="'.$cfID.'">
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
										<div class="btn_item">
											<span class="bookmify_drag_handle">
												<span class="a"></span>
												<span class="b"></span>
												<span class="c"></span>
											</span>
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
	
	public function ajaxQueryEditCF(){
		global $wpdb;
		$isAjaxCall 	= false;
		$html 			= '';
		$cf_typetext 	= '';
		$values			= array();
		
		if (!empty($_POST['bookmify_data'])) {
			$isAjaxCall = true;
			$id 		= $_POST['bookmify_data'];

			// SELECT
			$id				= esc_sql($id);
			$query 			= "SELECT id,cf_type,cf_label,cf_value,cf_required FROM {$wpdb->prefix}bmify_customfields WHERE id=".$id;
			$cfs	 		= $wpdb->get_results( $query, OBJECT  );
			
			
			foreach($cfs as $cf){
				$cfID 		= $cf->id;
				$cfType 	= $cf->cf_type;
				$cfLabel 	= $cf->cf_label;
				$cfValue 	= $cf->cf_value;
				$cfRequired	= $cf->cf_required;
				$label 		= esc_html__('Label & Options', 'bookmify');
				
				switch($cfType){
					case 'checkbox': 		$editHeading =	esc_html__('Edit Checkbox', 'bookmify'); break;
					case 'selectbox': 		$editHeading =	esc_html__('Edit Select', 'bookmify'); 	break;
					case 'radiobuttons': 	$editHeading =	esc_html__('Edit Radio Buttons', 'bookmify'); break;
					case 'text': 			$editHeading =	esc_html__('Edit Text', 'bookmify'); 			$label = esc_html__('Label', 'bookmify'); break;
					case 'textarea': 		$editHeading =	esc_html__('Edit Textarea', 'bookmify'); 		$label = esc_html__('Label', 'bookmify'); break;
					case 'textcontent': 	$editHeading =	esc_html__('Edit Text Content', 'bookmify'); 	$label = esc_html__('Label', 'bookmify'); break;
				}

				$values = unserialize($cfValue);

				// FIELD OPTIONS
				$output = '';
				if(!empty($values)){
					foreach($values as $value){
						$output .= '<div class="bookmify_be_options_list_item option_wrap">
										<div class="label_wrap">
											<input type="text" value="'.$value['label'].'">
										</div>
										<div class="buttons_holder">
											<div class="btn_item">
												<a href="#" class="bookmify_be_delete" data-entity-id=""><img class="bookmify_be_svg" src="'.BOOKMIFY_ASSETS_URL.'img/delete.svg" alt="" /></a>
											</div>
											<div class="btn_item">
												<span class="bookmify_drag_handle">
													<span class="a"></span>
													<span class="b"></span>
													<span class="c"></span>
												</span>
											</div>
										</div>
									</div>';
					}
				}
				
				$html .= '<div class="bookmify_be_popup_form_wrap" data-entity-id="'.$cfID.'" data-entity-type="'.$cfType.'">
							
							<div class="bookmify_be_popup_form_position_fixer">
								<div class="bookmify_be_popup_form_bg">
									<div class="bookmify_be_popup_form">

										<div class="bookmify_be_popup_form_header">
											<h3>'.$editHeading.'</h3>
											<span class="closer"></span>
										</div>

										<div class="bookmify_be_popup_form_content">
											<div class="bookmify_be_popup_form_content_in">

												<div class="bookmify_be_popup_form_fields">

													<div class="cf_content_left">
														<div class="label"><label>'.$label.'</label></div>
														<div class="top">';
															if($cfType == 'textcontent'){
											$html .= '			<div class="label_wrap">
																	<textarea class="cf_label required_field">'.$cfLabel.'</textarea>
																</div>';					
															}else{
											$html .= '			<div class="label_wrap">
																	<input type="text" class="cf_label required_field" value="'.$cfLabel.'">
																</div>
																<div class="required_wrap">
																	<span class="bookmify_be_checkbox protip" data-pt-target="true" data-pt-title="'. esc_attr__('Required field', 'bookmify').'" data-pt-gravity="right 4 0">
																		<input class="req" type="checkbox" '.checked( $cfRequired, 1, false ).'>
																		<span class="checkmark">
																			<img class="bookmify_be_svg" src="'.BOOKMIFY_ASSETS_URL.'/img/checked.svg" alt="" />
																		</span>
																	</span>
																</div>
																';				
															}
											$html .= 	'</div>';

											switch($cfType){
												case "checkbox":
												case "selectbox":
												case "radiobuttons":
												$html .= '<div class="bottom">
															<div class="bookmify_be_options_list" data-cf-id="'.$cfID.'">
																'.$output.'
															</div>
															<div class="bookmify_be_add_new_text_button cf_add_option">
																<a href="#"><span></span>'.esc_html__('Add Option', 'bookmify').'</a>
															</div>
														</div>';
												break;
											}
										$html .= 	'</div>';	


										$attached_entity_ids 	= HelperCustomfields::cfServicesIDs($cfID);
										$attached_entity_names 	= HelperCustomfields::cfServicesNames($cfID);

										$attached_entity_placeholder = '';
										if($attached_entity_names == ''){
											$attached_entity_placeholder = esc_attr__('Attach Services','bookmify');
										}


										$html .= 	'<div class="cf_content_right">
														<div class="cf_services_holder">
															<div class="label"><label>'.esc_html__('Attached Services', 'bookmify').'</label></div>
															<div class="bookmify_be_custom_select">
																<input type="text" data-placeholder="'.esc_attr__('Attach Services','bookmify').'" placeholder="'.$attached_entity_placeholder.'" readonly />
																<input type="hidden" class="cf_services_ids" value="'.$attached_entity_ids.'">
																<span class="bot_btn">
																	<span></span>
																	<img class="bookmify_be_svg" src="'.BOOKMIFY_ASSETS_URL.'img/down.svg" alt="" />
																</span>
																<div class="bookmify_be_new_value">'.$attached_entity_names.'</div>
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
			'bookmify_be_id' 		=> $id
			
		);

		if ( true === $isAjaxCall ){die(json_encode($buffyArray));} 
		else{return json_encode($buffyArray);}
	}
	
	
	
	
	/**
	 * @since 1.0.0
	 * @access public
	*/
	public function ajaxQueryInsertOrUpdateCF(){
		global $wpdb;
		
		$html = $output = ''; 
		$params = array();
		$isAjaxCall = false;
		$arr = [];
		$cf_options = [];
		$type = '';
		$cf_label = $cf_req = $cf_id = $cf_services = '';
		
		if(!empty($_POST['bookmify_data'])){
			$isAjaxCall = true;

			$cf_arr = json_decode(stripslashes($_POST['bookmify_data']));
			
			foreach($cf_arr as $cf){
				
				$cf_id 		= $cf->id;
				$cf_label 	= $cf->label;
				if(isset($cf->req)){
					$cf_req 	= $cf->req;
				}
				
				if($cf_req){$cf_req=1;}else{$cf_req=0;}
				
				if(!empty($cf->options)){
					foreach($cf->options as $option){
						if($option != '' && $option != ' '){
							$cf_options[]['label'] = $option;
						}
					}
				}
					
				$type	= $cf->type;
				switch($cf->type){
					case 'checkbox':
					case 'selectbox':
					case 'radiobuttons': $cf_options = serialize($cf_options);
						break;
					default: $cf_options = '';
						break;
				}
				
				$cf_services = $cf->ser;
			}
		}

		$insertOrUpdate = '';
		if(!empty($_POST['insertOrUpdate'])){
			$insertOrUpdate = $_POST['insertOrUpdate'];
		}
		if($insertOrUpdate == 'update'){
			$cf_updated_at = date("Y-m-d H:i:s");
		
			$wpdb->query($wpdb->prepare( "UPDATE {$wpdb->prefix}bmify_customfields SET cf_label=%s, cf_required=%d, cf_value=%s, services_ids=%s, updated_at=%s WHERE id=%d", $cf_label, $cf_req, $cf_options, $cf_services, $cf_updated_at, $cf_id));
		}else if($insertOrUpdate == 'insert'){
			$cf_created_at 	= date("Y-m-d H:i:s");
			$key 			= $type.'_'.date("YmdHis");
			// INSERT (Best Practice)
			$wpdb->query($wpdb->prepare( "INSERT INTO {$wpdb->prefix}bmify_customfields ( cf_label, cf_key, cf_type, cf_required, cf_value, services_ids, created_at ) VALUES ( %s, %s, %s, %d, %s, %s, %s )", $cf_label, $key, $type, $cf_req, $cf_options, $cf_services, $cf_created_at));
		}

		
		
		
		$query 	= "SELECT id,cf_type,cf_label FROM {$wpdb->prefix}bmify_customfields ORDER BY position, id";
		$cfs    = $wpdb->get_results( $query );
		$html 	= '';
		$html  .= '<div class="bookmify_be_list cfs_list">';
		if(count($cfs) == 0){
			$html .= Helper::bookmifyBeNoItems();
		}
		foreach($cfs as $cf){
			$cfID 		= $cf->id;
			$cfType 	= $cf->cf_type;
			$cfLabel 	= $cf->cf_label;
			switch($cfType){
				case 'checkbox': 		$cfTypetext =	esc_html__('Checkbox', 'bookmify');  		break;
				case 'selectbox': 		$cfTypetext =	esc_html__('Select', 'bookmify'); 			break;
				case 'radiobuttons': 	$cfTypetext =	esc_html__('Radio Buttons', 'bookmify'); 	break;
				case 'text': 			$cfTypetext =	esc_html__('Text', 'bookmify'); 			break;
				case 'textarea': 		$cfTypetext =	esc_html__('Textarea', 'bookmify'); 		break;
				case 'textcontent': 	$cfTypetext =	esc_html__('Text Content', 'bookmify'); 	break;
			}
			$html .= '<div class="bookmify_be_list_item cfs_item bookmify_be_animated" data-cf-id="'.$cfID.'" data-cf-type="'.$cfType.'">
						<div class="bookmify_be_list_item_in">
							<div class="bookmify_be_list_item_header">
								<div class="header_in">
									<div class="info_holder">
										<div class="info_in">
											<span class="cf_label_holder">
												<span class="cf_type">'.$cfTypetext.'</span>
												<span class="cf_label">'.$cfLabel.'</span>
											</span>
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
										<div class="btn_item btn_delete">
											<a href="#" class="bookmify_be_delete" data-entity-id="'.$cfID.'">
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
										<div class="btn_item">
											<span class="bookmify_drag_handle">
												<span class="a"></span>
												<span class="b"></span>
												<span class="c"></span>
											</span>
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
			'number'				=> Helper::bookmifyItemsCount('customfields')
		);

		if ( true === $isAjaxCall ){die(json_encode($buffyArray));} 
		else{return json_encode($buffyArray);}
		
	}
	
	
	
	
	
	
	/**
	 * @since 1.0.0
	 * @access public
	*/
	public function ajaxQueryDeleteCF(){
		global $wpdb;
		$cf_id	 			= '';
		$isAjaxCall 		= false;
		if(!empty($_POST['bookmify_data'])){
			$isAjaxCall 	= true;
			$cf_id 			= $_POST['bookmify_data'];
			$wpdb->query($wpdb->prepare( "DELETE FROM {$wpdb->prefix}bmify_customfields WHERE id=%d", $cf_id));
		
			$buffyArray = array(
				'number'				=> Helper::bookmifyItemsCount('customfields')
			);

			if ( true === $isAjaxCall ){die(json_encode($buffyArray));} 
			else{return json_encode($buffyArray);}
		}
	}
	
	
	/**
	 * @since 1.0.0
	 * @access protected
	*/
	public function ajaxQueryReorderCF(){
		global $wpdb;
		
		if (!empty($_POST['bookmify_data'])) {
			$cfs_ids = $_POST['bookmify_data'];
			foreach($cfs_ids as $key=>$cf_id){
				$wpdb->query($wpdb->prepare( "UPDATE {$wpdb->prefix}bmify_customfields SET position=%d WHERE id=%d", $key, $cf_id));
			}
		}

	}
	
	
	
	
	/**
	 * @since 1.0.0
	 * @access protected
	*/
	protected function get_page_title() 
	{
		return esc_html__( 'Customfields', 'bookmify' );
	}
	
}