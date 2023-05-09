<?php
namespace Bookmify;

use Bookmify\Helper;
use Bookmify\HelperAdmin;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


class Taxes{

	const PAGE_ID = 'bookmify_taxes';
	
	private $per_page;
	/**
	 * @since 1.0.0
	 * @access public
	*/
	public function __construct() {
		$this->assignValToVar();
		add_action( 'admin_menu', [ $this, 'registerAdminMenu' ], 20 );
		
		
		
		
		add_action( 'wp_ajax_ajaxQueryEditTax', [$this, 'ajaxQueryEditTax'] );
		add_action( 'wp_ajax_ajaxQueryDeleteTax', [$this, 'ajaxQueryDeleteTax'] );
		add_action( 'wp_ajax_ajaxQueryInsertOrUpdateTax', [$this, 'ajaxQueryInsertOrUpdateTax'] );
		
	}
	
	public function assignValToVar(){
		$this->per_page = get_option('bookmify_be_taxes_pp', 10);
	}
	
	/**
	 * @since 1.0.0
	 * @access public
	*/
	public function registerAdminMenu() {

		add_submenu_page(
			BOOKMIFY_MENU,
			esc_html__( 'Taxes', 'bookmify' ),
			esc_html__( 'Taxes', 'bookmify' ),
			'bookmify_be_read_taxes',
			self::PAGE_ID,
			[ $this, 'displayTaxsPage' ]
		);
	}
	
	public function displayTaxsPage() {
		global $wpdb;

		echo HelperAdmin::bookmifyAdminContentStart();
		?>
		
		<div class="bookmify_be_content_wrap bookmify_be_taxes_page">
			<div class="bookmify_be_page_title">
				<h3><?php echo esc_html($this->get_page_title()); ?><span class="count"><?php echo Helper::bookmifyItemsCount('taxes');?></span></h3>
				<div class="bookmify_be_add_new_item bookmify_be_add_new_tax">
					<a href="#" class="bookmify_button add_new bookmify_add_new_button">
						<span class="text"><?php esc_html_e('Add New Tax','bookmify');?></span>
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

				<div class="bookmify_be_taxes">
					
					<!-- Tax Header -->
					<div class="bookmify_be_taxes_header">
						<div class="bookmify_be_taxes_header_in">
							<span class="list_name"><?php esc_html_e('Title', 'bookmify');?></span>
							<span class="list_rate"><?php esc_html_e('Rate', 'bookmify');?></span>
						</div>
					</div>
					<!-- /Tax Header -->
					
					<!-- Tax List -->
					<div class="bookmify_be_taxes_list">
						<?php echo $this->taxesList(); ?>
					</div>
					<!-- /Tax Header -->
					
				</div>
				
			</div>
			<?php echo Helper::clonableFormTax(); ?>
		</div>
		<?php echo HelperAdmin::bookmifyAdminContentEnd();
		
	}
	

	/*
	 * @since 1.0.0
	 * @access public
	*/
	public function taxesList(){
		global $wpdb;
		
		$query 			= "SELECT * FROM {$wpdb->prefix}bmify_taxes ORDER BY title,id";
		
		$results 		= $wpdb->get_results( $query);
		

		$html = '<div class="bookmify_be_list tax_list">';
		if(count($results) == 0){
			$html .= Helper::bookmifyBeNoItems();
		}
		foreach($results as $result){
			$html .= '<div class="bookmify_be_list_item tax_item" data-tax-id="'.$result->id.'">
						<div class="bookmify_be_list_item_in">
							<div class="bookmify_be_list_item_header">
								<div class="header_in">
									<div class="info_holder">
										<div class="info_in">
											<span class="tax_name">'.$result->title.'</span>
											<span class="tax_rate">'.$result->rate.'</span>
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
									</div>
								</div>
							</div>
								
						</div>
					</div>';
			
		}
		
		$html .= '</div>';

		return $html;
	}
	public function ajaxQueryInsertOrUpdateTax(){
		global $wpdb;
		$isAjaxCall 			= false;
		
		// **************************************************************************************************************************
		// UPDATE 
		// **************************************************************************************************************************
		if ($_POST['taxID'] != '') {
			$taxID		= esc_sql($_POST['taxID']);
			$taxTitle 	= esc_sql($_POST['taxTitle']);
			$taxRate 	= esc_sql($_POST['taxRate']);
				
			$wpdb->query($wpdb->prepare( "UPDATE {$wpdb->prefix}bmify_taxes SET title=%s, rate=%f WHERE id=%d", $taxTitle, $taxRate, $taxID));

		
		
		}else{
			$taxTitle 	= esc_sql($_POST['taxTitle']);
			$taxRate 	= esc_sql($_POST['taxRate']);
				
			$wpdb->query($wpdb->prepare( "INSERT INTO {$wpdb->prefix}bmify_taxes (title, rate) VALUES (%s,%f)", $taxTitle, $taxRate));
			
			
		}
		$isAjaxCall = true;
		$query 		= "SELECT * FROM {$wpdb->prefix}bmify_taxes ORDER BY title,id";
		$results 	= $wpdb->get_results( $query);

		$html 		= '<div class="bookmify_be_list tax_list">';
		if(count($results) == 0){
			$html .= Helper::bookmifyBeNoItems();
		}
		foreach($results as $result){
			$html .= '<div class="bookmify_be_list_item tax_item bookmify_be_animated" data-tax-id="'.$result->id.'">
						<div class="bookmify_be_list_item_in">
							<div class="bookmify_be_list_item_header">
								<div class="header_in">
									<div class="info_holder">
										<div class="info_in">
											<span class="tax_name">'.$result->title.'</span>
											<span class="tax_rate">'.$result->rate.'</span>
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
			'number'				=> Helper::bookmifyItemsCount('taxes')
		);

		if ( true === $isAjaxCall ){die(json_encode($buffyArray));} 
		else{return json_encode($buffyArray);}
	}
	
	
	

	
	
	
	/**
	 * @since 1.0.0
	 * @access public
	*/
	public function ajaxQueryDeleteTax(){
		global $wpdb;
		$id 			= '';
		$isAjaxCall 	= false;
		if(!empty($_POST['bookmify_tax_id'])){
			$isAjaxCall = true;
			$id 		= $_POST['bookmify_tax_id'];
			$wpdb->query($wpdb->prepare( "DELETE FROM {$wpdb->prefix}bmify_services_taxes WHERE tax_id=%d", $id));
			$wpdb->query($wpdb->prepare( "DELETE FROM {$wpdb->prefix}bmify_taxes WHERE id=%d", $id));
			
			
			$buffyArray = array(
				'number'				=> Helper::bookmifyItemsCount('taxes'),
			);

			if ( true === $isAjaxCall ){die(json_encode($buffyArray));} 
			else{return json_encode($buffyArray);}
		}
	}
	
	
	
	
	
	
	
	
	
	public function ajaxQueryEditTax(){
		global $wpdb;
		$isAjaxCall = false;
		$html 		= '';
		
		if (!empty($_POST['bookmify_data'])) {
			$isAjaxCall 	= true;
			$id 			= $_POST['bookmify_data'];

			// SELECT
			$id 			= esc_sql($id);
			$query 			= "SELECT * FROM {$wpdb->prefix}bmify_taxes WHERE id=".$id;
			$taxes	 		= $wpdb->get_results( $query, OBJECT  );
			
			
			foreach($taxes as $tax){
				$ID			= $tax->id;
				$taxTitle	= $tax->title;
				$taxRate	= $tax->rate;
				
				$html .= '<div class="bookmify_be_popup_form_wrap" data-entity-id="'.$ID.'">
							
							<div class="bookmify_be_popup_form_position_fixer">
								<div class="bookmify_be_popup_form_bg">
									<div class="bookmify_be_popup_form">

										<div class="bookmify_be_popup_form_header">
											<h3>'.esc_html__('Edit Tax','bookmify').'</h3>
											<span class="closer"></span>
										</div>

										<div class="bookmify_be_popup_form_content">
											<div class="bookmify_be_popup_form_content_in">

												<div class="bookmify_be_popup_form_fields">

													<form autocomplete="off">
														<div class="input_wrap_row">
															<div class="input_wrap">
																<label><span class="title">'.esc_html__('Title','bookmify').'<span>*</span></span></label>
																<input class="tax_title required_field" type="text" value="'.$taxTitle.'" />
															</div>
														</div>

														<div class="input_wrap_row">
															<div class="input_wrap">
																<label><span class="title">'.esc_html__('Rate','bookmify').'<span>*</span></span></label>
																<input class="tax_rate required_field" type="number" value="'.$taxRate.'" />
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
	 * @since 1.0.0
	 * @access protected
	*/
	protected function get_page_title() {
		return esc_html__( 'Taxes', 'bookmify' );
	}
}
	

