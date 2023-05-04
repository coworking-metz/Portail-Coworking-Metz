<?php
namespace Bookmify;

use Bookmify\HelperServices;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


class Categories{
	
	
	public function __construct(){
		add_action( 'wp_ajax_ajaxQueryInsertCategory', [$this, 'ajaxQueryInsertCategory'] );
		add_action( 'wp_ajax_ajaxQueryReorderCategory', [$this, 'ajaxQueryReorderCategory'] );
		add_action( 'wp_ajax_ajaxQueryUpdateCategory', [$this, 'ajaxQueryUpdateCategory'] );
		add_action( 'wp_ajax_ajaxQueryDeleteCategory', [$this, 'ajaxQueryDeleteCategory'] );
	}
	
	public static function query_select(){
		global $wpdb;
		
		$query = "SELECT id,title FROM {$wpdb->prefix}bmify_categories ORDER BY position, id";
		$result = $wpdb->get_results( $query, OBJECT  );
		
		return $result;
	}
	
	
	public function ajaxQueryUpdateCategory(){
		global $wpdb;
		$html 			= '';
		
		$params 		= array();
		
		$isAjaxCall 	= false;
		if (!empty($_POST['bookmify_data'])) {
			$isAjaxCall = true;
			parse_str($_POST['bookmify_data'], $params);
			$cat_name 	= $params['cat_name'];
			$cat_id	 	= $params['cat_id'];
			
			$wpdb->query($wpdb->prepare( "UPDATE {$wpdb->prefix}bmify_categories SET title=%s WHERE id=%d", $cat_name, $cat_id));
			

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
				'updatedNano'			=> HelperServices::categoryListNano(),
			);
			if ( true === $isAjaxCall ) {die(json_encode($buffyArray));} 
			else {return json_encode($buffyArray);}
		}
	}
	
	
	public function ajaxQueryDeleteCategory(){
		global $wpdb;
		$html 			= '';
		
		$isAjaxCall 	= false;
		if (!empty($_POST['bookmify_cat_id'])) {
			$isAjaxCall = true;
			$catID 		= $_POST['bookmify_cat_id'];
			
			$catID		= esc_sql($catID);
			$count 		= $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->prefix}bmify_services WHERE category_id=".$catID );
			
			if($count == 0){
				$wpdb->query($wpdb->prepare( "DELETE FROM {$wpdb->prefix}bmify_categories WHERE id=%d", $catID));
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
			$html = preg_replace($search, $replace, $html);


			$buffyArray = array(
				'updatedNano'			=> HelperServices::categoryListNano(),
				'count'					=> $count,
			);
			if ( true === $isAjaxCall ) {die(json_encode($buffyArray));} 
			else {return json_encode($buffyArray);}
		}
	}
	
	
	public function ajaxQueryReorderCategory(){
		global $wpdb;
		$html = '';
		
		$params = array();
		
		$isAjaxCall = false;
		if (!empty($_POST['bookmify_cat_ids'])) {
			$isAjaxCall = true;
			$cat_ids = $_POST['bookmify_cat_ids'];
			foreach($cat_ids as $key=>$cat_id){
				$wpdb->query($wpdb->prepare( "UPDATE {$wpdb->prefix}bmify_categories SET position=%d WHERE id=%d", $key, $cat_id));
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
			$html = preg_replace($search, $replace, $html);


			$buffyArray = array(
				'updatedNano'			=> HelperServices::categoryListNano(),
			);
			if ( true === $isAjaxCall ) {die(json_encode($buffyArray));} 
			else {return json_encode($buffyArray);}
			
		}
	}
	
	
	public static function category_list() {
		global $wpdb;
		$result = self::query_select();
		$list 	= '<ul>';
		foreach($result as $category){
			$categoryID 		= $category->id;
			$categoryTitle 		= $category->title;
			$list .=   '<li data-category-id="'.$categoryID.'">
							<div class="top_part">
								<div class="top_part_in">
									<div class="left_part">
										<form class="cat_update" autocomplete="off">
											<div class="cat_name" data-category-id="'.$categoryID.'">
												<span>'.$categoryTitle.'</span>
												<input type="text" name="cat_name" value="'.$categoryTitle.'" />
												<input type="hidden" name="cat_id" value="'.$categoryID.'" />
												<input type="hidden" name="cat_old_name" value="'.$categoryTitle.'" />
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
													<a href="#" class="bookmify_be_delete" data-entity-id="'.$categoryID.'">
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
										</form>
									</div>
								</div>
							</div>
							
						</li>';
		}
		
		$list .= '</ul>';
		
		return $list;
	}
	
	
	public function ajaxQueryInsertCategory(){
		global $wpdb;
		$html = '';
		
		$params = array();
		
		$isAjaxCall = false;

		if (!empty($_POST['bookmify_data'])) {
			$isAjaxCall = true;
			
			// we need to convert form data to array from string
 			parse_str($_POST['bookmify_data'], $params);
			$cat_name = $params['cat_name'];
			
			// INSERT (Best Practice)
			$wpdb->query($wpdb->prepare( "INSERT INTO {$wpdb->prefix}bmify_categories ( title ) VALUES ( %s )", $cat_name));

			// SELECT
			$query 		= "SELECT id,title FROM {$wpdb->prefix}bmify_categories ORDER BY id DESC LIMIT 1";
			$categories = $wpdb->get_results( $query, OBJECT  );
			foreach($categories as $category){
				$categoryID 		= $category->id;
				$categoryTitle 		= $category->title;
				$html .= '<li class="opened" data-category-id="'.$categoryID.'">
							<div class="top_part">
								<div class="top_part_in">
									<div class="left_part">
										<form class="cat_update" autocomplete="off">
											<div class="cat_name" data-category-id="'.$categoryID.'">
												<span>'.$categoryTitle.'</span>
												<input type="text" name="cat_name" value="'.$categoryTitle.'" />
												<input type="hidden" name="cat_id" value="'.$categoryID.'" />
												<input type="hidden" name="cat_old_name" value="'.$categoryTitle.'" />
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
													<a href="#" class="bookmify_be_delete" data-entity-id="'.$categoryID.'">
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
										</form>
									</div>
								</div>
							</div>
						</li>';
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
			$html = preg_replace($search, $replace, $html);


			$buffyArray = array(
				'html'					=> $html,
				'updatedNano'			=> HelperServices::categoryListNano(),
			);
			if ( true === $isAjaxCall ) {die(json_encode($buffyArray));} 
			else {return json_encode($buffyArray);}
		}

			

	}

} 