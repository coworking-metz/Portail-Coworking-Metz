<?php
namespace Bookmify;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Settings_Dayoff_Query{
	
	
	public static function dayoff_list(){
		global $wpdb;
		
		$query 		= "SELECT * FROM {$wpdb->prefix}bmify_dayoff WHERE employee_id IS NULL";
		$result 	= '';
		
		$page       = 1;
		
		$Querify  = new Querify( $query, 'dayoff' );
		$results  = $Querify->getData( 10, $page );
		
		$myres = '<ul class="dayoff_list">';
		
		for( $i = 0; $i < count( $results->data ); $i++ ){
			$edit_delete_panel = '';
			$every_year = 'no';
			$checked = '';
			$title = '';
			$date = '';
			if($results->data[$i]->every_year == 1){
				$every_year = 'yes';
			}
			if($every_year === 'yes'){
				$checked = 'checked';
			}
			$edit_delete_panel = '<div class="buttons_holder">
									<div class="btn_item btn_edit">
										<a href="#" class="bookmify_be_edit">
											<img class="bookmify_be_svg edit" src="'.BOOKMIFY_ASSETS_URL.'img/edit.svg" alt="" />
											<img class="bookmify_be_svg checked" src="'.BOOKMIFY_ASSETS_URL.'img/checked.svg" alt="" />
										</a>
									</div>
									<div class="btn_item">
										<a href="#" class="bookmify_be_delete" data-entity-id="'.$results->data[$i]->id.'">
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
			$update_block		= '<div class="bookmify_day_off_edit_dd">
										<form autocomplete="off">
											<div class="do_item">
												<label for="mdp-do-month-'.$results->data[$i]->id.'">'.esc_html__('Date', 'bookmify').'<span>*</span></label>
												<input data-selected-day="'.$results->data[$i]->date.'" class="mdp-do-hidden" id="mdp-do-month-'.$results->data[$i]->id.'" type="text" name="offday_days" placeholder="'.esc_attr__('yy-mm-dd', 'bookmify').'" />
												<input class="offday_hidden_day" type="hidden" name="offday_hidden_day" id="offday_hidden_day_'.$results->data[$i]->id.'" />
											</div>
											<div class="do_item">
												<label for="offday_name-'.$results->data[$i]->id.'">'.esc_html__('Title', 'bookmify').'<span>*</span></label>
												<input id="offday_name-'.$results->data[$i]->id.'" type="text" name="offday_name" placeholder="'.esc_attr__('Enter Off Day Title...', 'bookmify').'" value="'.$results->data[$i]->title.'" />
											</div>
											<div class="do_dd_footer">
												<div class="left_part">
													<label class="switch">
														<input type="checkbox" id="repeat-'.$results->data[$i]->id.'" value="1" name="offday_repeat" '.$checked.' />
														<span class="slider round"></span>
													</label>
													<label class="repeater" for="repeat-'.$results->data[$i]->id.'">'.esc_html__('Repeat Every Year', 'bookmify').'</label>
												</div>
												<div class="right_part">
													<a class="add" href="#">'.esc_html__('Save', 'bookmify').'</a>
												</div>
											</div>
										</form>
									</div>';
			// date format URI: https://codex.wordpress.org/Formatting_Date_and_Time
			
			$date 	= '<span class="list_date">'.date_i18n(get_option('bookmify_be_date_format', 'd F, Y'), strtotime($results->data[$i]->date)).'</span>';
			$title 	= '<span class="list_title">'.$results->data[$i]->title.'</span>';
			$myres .= '<li class="bookmify_be_list_item" data-entity-id="'.$results->data[$i]->id.'">
							<div class="bookmify_be_list_item_in">
								<div class="bookmify_be_list_item_header">
									<div class="header_in item" data-yearly="'.$every_year.'">
										<div class="header_info">
											<span class="f_year"></span>'.$date.$title.'
										</div>
										'.$edit_delete_panel.'
									</div>
								</div>
								'.$update_block.'
							</div>
						</li>';
			
		}
		
		
		$myres .= '</ul>';
		
		$myres .= $Querify->getPagination( 1, 'bookmify_be_pagination dayoff');
		return $myres;
	}
	
	
	
	
	public static function bookmify_be_ajax_pagination_dayoff(){
		global $wpdb;
		
		$isAjaxCall = false;
		$page = 1;
		$buffy = '';
		
		$query = "SELECT * FROM {$wpdb->prefix}bmify_dayoff WHERE employee_id IS NULL";
		
		if (!empty($_POST['bookmify_page'])) {
			$isAjaxCall = true;
			$page       = $_POST['bookmify_page'];
		}
		
		
		$Querify  	= new Querify( $query, 'dayoff' );
		$results    = $Querify->getData( 10, $page );
		
		
		$myres = '<ul class="dayoff_list">';
		
		for( $i = 0; $i < count( $results->data ); $i++ ){
			$edit_delete_panel = '';
			$every_year = 'no';
			$checked = '';
			$title = '';
			$date = '';
			if($results->data[$i]->every_year == 1){
				$every_year = 'yes';
			}
			if($every_year === 'yes'){
				$checked = 'checked';
			}
			$edit_delete_panel = '<div class="edit_del_panel">
									<a href="#" class="bookmify_be_edit"><img class="bookmify_be_svg" src="'.BOOKMIFY_ASSETS_URL.'img/edit.svg" alt="" /></a>
									<a href="#" class="bookmify_be_delete" data-entity-id="'.$results->data[$i]->id.'"><img class="bookmify_be_svg" src="'.BOOKMIFY_ASSETS_URL.'img/delete.svg" alt="" /></a>
								  </div>';
			$update_block		= '<div class="bookmify_day_off_edit_dd">
										<form autocomplete="off">
											<div class="do_item">
												<label for="mdp-do-month-'.$results->data[$i]->id.'">'.esc_html__('Date', 'bookmify').'<span>*</span></label>
												<input data-selected-day="'.$results->data[$i]->date.'" class="mdp-do-hidden" id="mdp-do-month-'.$results->data[$i]->id.'" type="text" name="offday_days" placeholder="'.esc_attr__('yy-mm-dd', 'bookmify').'" />
												<input class="offday_hidden_day" type="hidden" name="offday_hidden_day" id="offday_hidden_day_'.$results->data[$i]->id.'" />
											</div>
											<div class="do_item">
												<label for="offday_name-'.$results->data[$i]->id.'">'.esc_html__('Title', 'bookmify').'<span>*</span></label>
												<input id="offday_name-'.$results->data[$i]->id.'" type="text" name="offday_name" placeholder="'.esc_attr__('Enter Off Day Title...', 'bookmify').'" value="'.$results->data[$i]->title.'" />
											</div>
											<div class="do_dd_footer">
												<div class="left_part">
													<label class="switch">
														<input type="checkbox" id="repeat-'.$results->data[$i]->id.'" value="1" name="offday_repeat" '.$checked.' />
														<span class="slider round"></span>
													</label>
													<label class="repeater" for="repeat-'.$results->data[$i]->id.'">'.esc_html__('Repeat Every Year', 'bookmify').'</label>
												</div>
												<div class="right_part">
													<a class="add" href="#">'.esc_html__('Save', 'bookmify').'</a>
												</div>
											</div>
											<span class="closer"></span>
											<input name="offday_id" class="offday_id" id="offday_id_'.$results->data[$i]->id.'" value="'.$results->data[$i]->id.'" type="hidden" />
										</form>
									</div>';
			// date format URI: https://codex.wordpress.org/Formatting_Date_and_Time
			
			$date 	= '<span class="list_date">'.date_i18n(get_option('bookmify_be_date_format', 'd F, Y'), strtotime($results->data[$i]->date)).'</span>';
			$title 	= '<span class="list_title">'.$results->data[$i]->title.'</span>';
			$myres .= '<li><div class="item" data-yearly="'.$every_year.'"><span class="f_year"></span>'.$date.$title.$edit_delete_panel.'</div>'.$update_block.'</li>';
		}
		
		$myres .= '</ul>';
		
		$myres .= $Querify->getPagination( 1, 'bookmify_be_pagination dayoff');
		
		
		if ( $myres != NULL ) {
			$buffy .= $myres; 
		}
		
		
		// remove whitespaces form the ajax HTML
		$search = array(
			'/\>[^\S ]+/s',  // strip whitespaces after tags, except space
			'/[^\S ]+\</s',  // strip whitespaces before tags, except space
			'/(\s)+/s'       // shorten multiple whitespace sequences
		);		$replace = array(
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
	
	public static function working_hours_list($day = "monday", $employee_id = ""){
		global $wpdb;
		switch($day){
			case 'monday': 		$index = 1; break;
			case 'tuesday': 	$index = 2; break;
			case 'wednesday': 	$index = 3; break;
			case 'thursday': 	$index = 4; break;
			case 'friday': 		$index = 5; break;
			case 'saturday':	$index = 6; break;
			case 'sunday': 		$index = 7; break;
		}
		if($employee_id == ""){
			$index			= esc_sql($index);
			$select 		= "SELECT * FROM {$wpdb->prefix}bmify_business_hours_breaks WHERE day_index=".$index;
		}else{
			$index			= esc_sql($index);
			$employee_id	= esc_sql($employee_id);
			$select 		= "SELECT * FROM {$wpdb->prefix}bmify_employee_business_hours_breaks WHERE day_index=".$index." AND employee_id=".$employee_id;
		}
			
		$breaks 			= $wpdb->get_results( $select, OBJECT  );
		$html 				= '';
		$i 					= 0;
		$bs 				= 'break_start';
		$be 				= 'break_end';
		$timeFormat			= get_option('bookmify_be_time_format', 'h:i a');
		foreach($breaks as $break){
			$startClass 	= 'break_time '.$day.'_start_'.$i;
			$endClass 		= 'break_time '.$day.'_end_'.$i;
			$startName 		= $day.'_'.$bs;
			$endName 		= $day.'_'.$be;
			$startTime 		= date_i18n($timeFormat,strtotime($break->start_time));
			$endTime 		= date_i18n($timeFormat,strtotime($break->end_time));
			if($timeFormat != 'H:i'){
				if((date('H', strtotime($endTime)) + date('i', strtotime($endTime))) == 0){
					$endTime.= '.';
				}
			}else{
				if((date('H', strtotime($endTime)) + date('i', strtotime($endTime))) == 0){$endTime	= '24:00';}
			}
				
			$html 		   .= '<div class="item"><div class="'.$bs.'" data-value="'.$startTime.'"><input class="'.$startClass.'" readonly type="text" name="'.$startName.'" value="'.$startTime.'"></div><div class="'.$be.'" data-value="'.$endTime.'"><input class="'.$endClass.'" readonly type="text" name="'.$endName.'" value="'.$endTime.'"></div><div class="break_del"><span></span></div></div>';
			$i++;
		}
		return $html;
	}
	
	
	
	public function __construct(){
		add_action( 'wp_ajax_bookmify_be_ajax_pagination_dayoff', [$this, 'bookmify_be_ajax_pagination_dayoff'] );
	}
	
}
