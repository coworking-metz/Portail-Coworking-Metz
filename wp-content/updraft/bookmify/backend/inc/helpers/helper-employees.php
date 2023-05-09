<?php
namespace Bookmify;

use Bookmify\Helper;
use Bookmify\HelperEmployees;


// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {exit; }


/**
 * Class Helper Employees
 */
class HelperEmployees
{
	/*
	 * @since 1.0.0
	 * @access private
	*/
	public static function clonableForm(){
		
		$html = '<div class="bookmify_be_popup_form_wrap">
					'.self::allNanoInOne().'
					<div class="bookmify_be_popup_form_position_fixer">
						<div class="bookmify_be_popup_form_bg">
							<div class="bookmify_be_popup_form">

								<div class="bookmify_be_popup_form_header">
									<h3>'.esc_html__('New Employee','bookmify').'</h3>
									<span class="closer"></span>
								</div>

								<div class="bookmify_be_popup_form_content">
									<div class="bookmify_be_popup_form_content_in">

										<div class="bookmify_be_popup_form_fields">


											<div class="bookmify_be_employeestabs_wrap bookmify_be_tab_wrap">
												<div class="bookmify_be_link_tabs">
													<ul class="bookmify_be_employeestabs_nav">
														<li class="active"><a class="bookmify_be_tab_link" href="#">'.esc_html__('Details','bookmify').'</a></li>
														<li><a class="bookmify_be_tab_link" href="#">'.esc_html__('Services','bookmify').'</a></li>
														<li><a class="bookmify_be_tab_link" href="#">'.esc_html__('Working Hours','bookmify').'</a></li>
														<li><a class="bookmify_be_tab_link" href="#">'.esc_html__('Days Off','bookmify').'</a></li>
													</ul>
												</div>
												<div class="bookmify_be_employeestabs_content bookmify_be_content_tabs">
													<div class="bookmify_be_tab_pane active employee_tab">'.self::getDetailsEmployeeTab().'</div>
													<div class="bookmify_be_tab_pane employee_tab">'.self::getServicesEmployeeTab().'</div>
													<div class="bookmify_be_tab_pane bookmify_be_wh_wrapper employee_tab">'.self::getWorkingHoursEmployeeTab().'</div>
													<div class="bookmify_be_tab_pane bookmify_be_day_off_wrapper emloyee_tab">'.self::getDaysOffEmployeeTab().'</div>
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
		
		return $html;
	}
		
	public static function getWorkingHoursEmployeeTab($ID = ''){
		global $wpdb;
		$html = '';
		
		$mo_wh_s = $mo_wh_e = $tu_wh_s = $tu_wh_e = $we_wh_s = $we_wh_e = $th_wh_e = $th_wh_s = $fr_wh_e = $fr_wh_s = $sa_wh_e = $sa_wh_s = $su_wh_e = $su_wh_s = '';
		if($ID != ''){
			$mo_wh_s = Helper::bookmifyWorkingHoursOfEmployee($ID,1,'start_time');
			$mo_wh_e = Helper::bookmifyWorkingHoursOfEmployee($ID,1,'end_time');
			$tu_wh_s = Helper::bookmifyWorkingHoursOfEmployee($ID,2,'start_time');
			$tu_wh_e = Helper::bookmifyWorkingHoursOfEmployee($ID,2,'end_time');
			$we_wh_s = Helper::bookmifyWorkingHoursOfEmployee($ID,3,'start_time');
			$we_wh_e = Helper::bookmifyWorkingHoursOfEmployee($ID,3,'end_time');
			$th_wh_s = Helper::bookmifyWorkingHoursOfEmployee($ID,4,'start_time');
			$th_wh_e = Helper::bookmifyWorkingHoursOfEmployee($ID,4,'end_time');
			$fr_wh_s = Helper::bookmifyWorkingHoursOfEmployee($ID,5,'start_time');
			$fr_wh_e = Helper::bookmifyWorkingHoursOfEmployee($ID,5,'end_time');
			$sa_wh_s = Helper::bookmifyWorkingHoursOfEmployee($ID,6,'start_time');
			$sa_wh_e = Helper::bookmifyWorkingHoursOfEmployee($ID,6,'end_time');
			$su_wh_s = Helper::bookmifyWorkingHoursOfEmployee($ID,7,'start_time');
			$su_wh_e = Helper::bookmifyWorkingHoursOfEmployee($ID,7,'end_time');
		}
		
		$moC1=$moC2=$moC1=$tuC1=$tuC2=$weC1=$weC2=$thC1=$thC2=$frC1=$frC2=$saC1=$saC1=$saC2=$suC1=$suC2='';
		
		// monday
		if($mo_wh_s == ''){$mo_wh_s = get_option('bookmify_be_monday_start', '08:00');if($mo_wh_s == ''){$mo_wh_s = '08:00';}}else{$moC1='data-checked="checked"';$moC2='checked';}
		if($mo_wh_e == ''){$mo_wh_e = get_option('bookmify_be_monday_end', '18:00');if($mo_wh_e == ''){$mo_wh_e = '18:00';}}
		// tuesday
		if($tu_wh_s == ''){$tu_wh_s = get_option('bookmify_be_tuesday_start', '08:00');if($tu_wh_s == ''){$tu_wh_s = '08:00';}}else{$tuC1='data-checked="checked"';$tuC2='checked';}
		if($tu_wh_e == ''){$tu_wh_e = get_option('bookmify_be_tuesday_end', '18:00');if($tu_wh_e == ''){$tu_wh_e = '18:00';}}
		// wednesday
		if($we_wh_s == ''){$we_wh_s = get_option('bookmify_be_wednesday_start', '08:00');if($we_wh_s == ''){$we_wh_s = '08:00';}}else{$weC1='data-checked="checked"';$weC2='checked';}
		if($we_wh_e == ''){$we_wh_e = get_option('bookmify_be_wednesday_end', '18:00');if($we_wh_e == ''){$we_wh_e = '18:00';}}
		// thursday
		if($th_wh_s == ''){$th_wh_s = get_option('bookmify_be_thursday_start', '08:00');if($th_wh_s == ''){$th_wh_s = '08:00';}}else{$thC1='data-checked="checked"';$thC2='checked';}
		if($th_wh_e == ''){$th_wh_e = get_option('bookmify_be_thursday_end', '18:00');if($th_wh_e == ''){$th_wh_e = '18:00';}}
		// friday
		if($fr_wh_s == ''){$fr_wh_s = get_option('bookmify_be_friday_start', '08:00');if($fr_wh_s == ''){$fr_wh_s = '08:00';}}else{$frC1='data-checked="checked"';$frC2='checked';}
		if($fr_wh_e == ''){$fr_wh_e = get_option('bookmify_be_friday_end', '18:00');if($fr_wh_e == ''){$fr_wh_e = '18:00';}}
		// saturday
		if($sa_wh_s == ''){$sa_wh_s = get_option('bookmify_be_saturday_start', '08:00');if($sa_wh_s == ''){$sa_wh_s = '08:00';}}else{$saC1='data-checked="checked"';$saC2='checked';}
		if($sa_wh_e == ''){$sa_wh_e = get_option('bookmify_be_saturday_end', '18:00');if($sa_wh_e == ''){$sa_wh_e = '18:00';}}
		// sunday
		if($su_wh_s == ''){$su_wh_s = get_option('bookmify_be_sunday_start', '08:00');if($su_wh_s == ''){$su_wh_s = '08:00';}}else{$suC1='data-checked="checked"';$suC2='checked';}
		if($su_wh_e == ''){$su_wh_e = get_option('bookmify_be_sunday_end', '18:00');if($su_wh_e == ''){$su_wh_e = '18:00';}}
		
		if($ID == ''){
			if(get_option('bookmify_be_monday_start', '08:00') != ''){$moC1='data-checked="checked"';$moC2='checked';}
			if(get_option('bookmify_be_tuesday_start', '08:00') != ''){$tuC1='data-checked="checked"';$tuC2='checked';}
			if(get_option('bookmify_be_wednesday_start', '08:00') != ''){$weC1='data-checked="checked"';$weC2='checked';}
			if(get_option('bookmify_be_thursday_start', '08:00') != ''){$thC1='data-checked="checked"';$thC2='checked';}
			if(get_option('bookmify_be_friday_start', '08:00') != ''){$frC1='data-checked="checked"';$frC2='checked';}
			if(get_option('bookmify_be_saturday_start', '08:00') != ''){$saC1='data-checked="checked"';$saC2='checked';}
			if(get_option('bookmify_be_sunday_start', '08:00') != ''){$suC1='data-checked="checked"';$suC2='checked';}
		}
		$timeFormat = get_option('bookmify_be_time_format', 'h:i a');
		$mo_wh_s 	= date_i18n($timeFormat,strtotime($mo_wh_s));
		$mo_wh_e 	= date_i18n($timeFormat,strtotime($mo_wh_e));
		$tu_wh_s 	= date_i18n($timeFormat,strtotime($tu_wh_s));
		$tu_wh_e 	= date_i18n($timeFormat,strtotime($tu_wh_e));
		$we_wh_s 	= date_i18n($timeFormat,strtotime($we_wh_s));
		$we_wh_e 	= date_i18n($timeFormat,strtotime($we_wh_e));
		$th_wh_s 	= date_i18n($timeFormat,strtotime($th_wh_s));
		$th_wh_e 	= date_i18n($timeFormat,strtotime($th_wh_e));
		$fr_wh_s 	= date_i18n($timeFormat,strtotime($fr_wh_s));
		$fr_wh_e 	= date_i18n($timeFormat,strtotime($fr_wh_e));
		$sa_wh_s 	= date_i18n($timeFormat,strtotime($sa_wh_s));
		$sa_wh_e 	= date_i18n($timeFormat,strtotime($sa_wh_e));
		$su_wh_s 	= date_i18n($timeFormat,strtotime($su_wh_s));
		$su_wh_e 	= date_i18n($timeFormat,strtotime($su_wh_e));
		
		if($timeFormat != 'H:i'){
			if((date('H', strtotime($mo_wh_e)) + date('i', strtotime($mo_wh_e))) == 0){$mo_wh_e .= '.';}
			if((date('H', strtotime($tu_wh_e)) + date('i', strtotime($tu_wh_e))) == 0){$tu_wh_e .= '.';}
			if((date('H', strtotime($we_wh_e)) + date('i', strtotime($we_wh_e))) == 0){$we_wh_e .= '.';}
			if((date('H', strtotime($th_wh_e)) + date('i', strtotime($th_wh_e))) == 0){$th_wh_e .= '.';}
			if((date('H', strtotime($fr_wh_e)) + date('i', strtotime($fr_wh_e))) == 0){$fr_wh_e .= '.';}
			if((date('H', strtotime($sa_wh_e)) + date('i', strtotime($sa_wh_e))) == 0){$sa_wh_e .= '.';}
			if((date('H', strtotime($su_wh_e)) + date('i', strtotime($su_wh_e))) == 0){$su_wh_e .= '.';}
		}else{
			if((date('H', strtotime($mo_wh_e)) + date('i', strtotime($mo_wh_e))) == 0){$mo_wh_e  = '24:00';}
			if((date('H', strtotime($tu_wh_e)) + date('i', strtotime($tu_wh_e))) == 0){$tu_wh_e  = '24:00';}
			if((date('H', strtotime($we_wh_e)) + date('i', strtotime($we_wh_e))) == 0){$we_wh_e  = '24:00';}
			if((date('H', strtotime($th_wh_e)) + date('i', strtotime($th_wh_e))) == 0){$th_wh_e  = '24:00';}
			if((date('H', strtotime($fr_wh_e)) + date('i', strtotime($fr_wh_e))) == 0){$fr_wh_e  = '24:00';}
			if((date('H', strtotime($sa_wh_e)) + date('i', strtotime($sa_wh_e))) == 0){$sa_wh_e  = '24:00';}
			if((date('H', strtotime($su_wh_e)) + date('i', strtotime($su_wh_e))) == 0){$su_wh_e  = '24:00';}
		}
			
		
		$html .= '<form method="post" action="" class="bookmify_working_hours">
					<ul class="bookmify_be_working_hours_list employee_wh">
						<li class="monday" '.$moC1.'>
							<div class="item hour_item">
								<div class="item_wh">
									<div class="day">
										<label>
											<input name="monday_checked" type="checkbox" '.$moC2.'>
											<span class="checkmark">
												<img class="bookmify_be_svg" src="'.BOOKMIFY_ASSETS_URL.'/img/checked.svg" alt="" />
											</span>
										</label>
										<span>'.esc_html__('Monday', 'bookmify').'</span>
									</div>
									<div class="hours">
										<div class="start"><input class="time" autocomplete="off" name="bookmify_be_monday_start" type="text" value="'.$mo_wh_s.'" readonly /></div>
										<div class="end"><input class="time" autocomplete="off" name="bookmify_be_monday_end" type="text" value="'.$mo_wh_e.'" readonly /></div>
									</div>
								</div>
								<div class="item_bh">
									<div class="day">
										<span>'.esc_html__('Breaks', 'bookmify').'</span>
									</div>
									<div class="breaks">
										<div class="breaks_list">
											'.Settings_Dayoff_Query::working_hours_list('monday', $ID).'
										</div>
										<div class="breaks_add">
											<a href="#"><span></span>'.esc_html__('Add New', 'bookmify').'</a>
										</div>
									</div>
								</div>
								<div class="apply">
									<span class="protip" data-pt-target="true" data-pt-title="'.esc_attr__('Apply to all days', 'bookmify').'" data-pt-gravity="left -4 0">
										<img class="bookmify_be_svg" src="'.BOOKMIFY_ASSETS_URL.'/img/check.svg" alt="" />
									</span>
								</div>
							</div>
						</li>
						<li class="tuesday" '.$tuC1.'>
							<div class="item hour_item">
								<div class="item_wh">
									<div class="day">
										<label>
											<input name="tuesday_checked" type="checkbox" '.$tuC2.'>
											<span class="checkmark">
												<img class="bookmify_be_svg" src="'.BOOKMIFY_ASSETS_URL.'/img/checked.svg" alt="" />
											</span>
										</label>
										<span>'.esc_html__('Tuesday', 'bookmify').'</span>
									</div>
									<div class="hours">
										<div class="start"><input class="time" autocomplete="off" name="bookmify_be_tuesday_start" type="text" value="'.$tu_wh_s.'" readonly /></div>
										<div class="end"><input class="time" autocomplete="off" name="bookmify_be_tuesday_end" type="text" value="'.$tu_wh_e.'" readonly /></div>
									</div>
								</div>
								<div class="item_bh">
									<div class="day">
										<span>'.esc_html__('Breaks', 'bookmify').'</span>
									</div>
									<div class="breaks">
										<div class="breaks_list">
											'.Settings_Dayoff_Query::working_hours_list('tuesday', $ID).'
										</div>
										<div class="breaks_add">
											<a href="#"><span></span>'.esc_html__('Add New', 'bookmify').'</a>
										</div>
									</div>
								</div>
								<div class="apply">
									<span class="protip" data-pt-target="true" data-pt-title="'.esc_attr__('Apply to all days', 'bookmify').'" data-pt-gravity="left -4 0">
										<img class="bookmify_be_svg" src="'.BOOKMIFY_ASSETS_URL.'/img/check.svg" alt="" />
									</span>
								</div>
							</div>
						</li>
						<li class="wednesday" '.$weC1.'>
							<div class="item hour_item">
								<div class="item_wh">
									<div class="day">
										<label>
											<input name="wednesday_checked" type="checkbox" '.$weC2.'>
											<span class="checkmark">
												<img class="bookmify_be_svg" src="'.BOOKMIFY_ASSETS_URL.'/img/checked.svg" alt="" />
											</span>
										</label>
										<span>'.esc_html__('Wednesday', 'bookmify').'</span>
									</div>
									<div class="hours">
										<div class="start"><input class="time" autocomplete="off" name="bookmify_be_wednesday_start" type="text" value="'.$we_wh_s.'" readonly /></div>
										<div class="end"><input class="time" autocomplete="off" name="bookmify_be_wednesday_end" type="text" value="'.$we_wh_e.'" readonly /></div>
									</div>
								</div>
								<div class="item_bh">
									<div class="day">
										<span>'.esc_html__('Breaks', 'bookmify').'</span>
									</div>
									<div class="breaks">
										<div class="breaks_list">
											'.Settings_Dayoff_Query::working_hours_list('wednesday', $ID).'
										</div>
										<div class="breaks_add">
											<a href="#"><span></span>'.esc_html__('Add New', 'bookmify').'</a>
										</div>
									</div>
								</div>
								<div class="apply">
									<span class="protip" data-pt-target="true" data-pt-title="'.esc_attr__('Apply to all days', 'bookmify').'" data-pt-gravity="left -4 0">
										<img class="bookmify_be_svg" src="'.BOOKMIFY_ASSETS_URL.'/img/check.svg" alt="" />
									</span>
								</div>
							</div>
						</li>
						<li class="thursday" '.$thC1.'>
							<div class="item hour_item">
								<div class="item_wh">
									<div class="day">
										<label>
											<input name="thursday_checked" type="checkbox" '.$thC2.'>
											<span class="checkmark">
												<img class="bookmify_be_svg" src="'.BOOKMIFY_ASSETS_URL.'/img/checked.svg" alt="" />
											</span>
										</label>
										<span>'.esc_html__('Thursday', 'bookmify').'</span>
									</div>
									<div class="hours">
										<div class="start"><input class="time" autocomplete="off" name="bookmify_be_thursday_start" type="text" value="'.$th_wh_s.'" readonly /></div>
										<div class="end"><input class="time" autocomplete="off" name="bookmify_be_thursday_end" type="text" value="'.$th_wh_e.'" readonly /></div>
									</div>
								</div>
								<div class="item_bh">
									<div class="day">
										<span>'.esc_html__('Breaks', 'bookmify').'</span>
									</div>
									<div class="breaks">
										<div class="breaks_list">
											'.Settings_Dayoff_Query::working_hours_list('thursday', $ID).'
										</div>
										<div class="breaks_add">
											<a href="#"><span></span>'.esc_html__('Add New', 'bookmify').'</a>
										</div>
									</div>
								</div>
								<div class="apply">
									<span class="protip" data-pt-target="true" data-pt-title="'.esc_attr__('Apply to all days', 'bookmify').'" data-pt-gravity="left -4 0">
										<img class="bookmify_be_svg" src="'.BOOKMIFY_ASSETS_URL.'/img/check.svg" alt="" />
									</span>
								</div>
							</div>
						</li>
						<li class="friday" '.$frC1.'>
							<div class="item hour_item">
								<div class="item_wh">
									<div class="day">
										<label>
											<input name="friday_checked" type="checkbox" '.$frC2.'>
											<span class="checkmark">
												<img class="bookmify_be_svg" src="'.BOOKMIFY_ASSETS_URL.'/img/checked.svg" alt="" />
											</span>
										</label>
										<span>'.esc_html__('Friday', 'bookmify').'</span>
									</div>
									<div class="hours">
										<div class="start"><input class="time" autocomplete="off" name="bookmify_be_friday_start" type="text" value="'.$fr_wh_s.'" readonly /></div>
										<div class="end"><input class="time" autocomplete="off" name="bookmify_be_friday_end" type="text" value="'.$fr_wh_e.'" readonly /></div>
									</div>
								</div>
								<div class="item_bh">
									<div class="day">
										<span>'.esc_html__('Breaks', 'bookmify').'</span>
									</div>
									<div class="breaks">
										<div class="breaks_list">
											'.Settings_Dayoff_Query::working_hours_list('friday', $ID).'
										</div>
										<div class="breaks_add">
											<a href="#"><span></span>'.esc_html__('Add New', 'bookmify').'</a>
										</div>
									</div>
								</div>
								<div class="apply">
									<span class="protip" data-pt-target="true" data-pt-title="'.esc_attr__('Apply to all days', 'bookmify').'" data-pt-gravity="left -4 0">
										<img class="bookmify_be_svg" src="'.BOOKMIFY_ASSETS_URL.'/img/check.svg" alt="" />
									</span>
								</div>
							</div>
						</li>
						<li class="saturday" '.$saC1.'>
							<div class="item hour_item">
								<div class="item_wh">
									<div class="day">
										<label>
											<input name="saturday_checked" type="checkbox" '.$saC2.'>
											<span class="checkmark">
												<img class="bookmify_be_svg" src="'.BOOKMIFY_ASSETS_URL.'/img/checked.svg" alt="" />
											</span>
										</label>
										<span>'.esc_html__('Saturday', 'bookmify').'</span>
									</div>
									<div class="hours">
										<div class="start"><input class="time" autocomplete="off" name="bookmify_be_saturday_start" type="text" value="'.$sa_wh_s.'" readonly /></div>
										<div class="end"><input class="time" autocomplete="off" name="bookmify_be_saturday_end" type="text" value="'.$sa_wh_e.'" readonly /></div>
									</div>
								</div>
								<div class="item_bh">
									<div class="day">
										<span>'.esc_html__('Breaks', 'bookmify').'</span>
									</div>
									<div class="breaks">
										<div class="breaks_list">
											'.Settings_Dayoff_Query::working_hours_list('saturday', $ID).'
										</div>
										<div class="breaks_add">
											<a href="#"><span></span>'.esc_html__('Add New', 'bookmify').'</a>
										</div>
									</div>
								</div>
								<div class="apply">
									<span class="protip" data-pt-target="true" data-pt-title="'.esc_attr__('Apply to all days', 'bookmify').'" data-pt-gravity="left -4 0">
										<img class="bookmify_be_svg" src="'.BOOKMIFY_ASSETS_URL.'/img/check.svg" alt="" />
									</span>
								</div>
							</div>
						</li>
						<li class="sunday" '.$suC1.'>
							<div class="item hour_item">
								<div class="item_wh">
									<div class="day">
										<label>
											<input name="sunday_checked" type="checkbox" '.$suC2.'>
											<span class="checkmark">
												<img class="bookmify_be_svg" src="'.BOOKMIFY_ASSETS_URL.'/img/checked.svg" alt="" />
											</span>
										</label>
										<span>'.esc_html__('Sunday', 'bookmify').'</span>
									</div>
									<div class="hours">
										<div class="start"><input class="time" autocomplete="off" name="bookmify_be_sunday_start" type="text" value="'.$su_wh_s.'" /></div>
										<div class="end"><input class="time" autocomplete="off" name="bookmify_be_sunday_end" type="text" value="'.$su_wh_e.'" /></div>
									</div>
								</div>
								<div class="item_bh">
									<div class="day">
										<span>'.esc_html__('Breaks', 'bookmify').'</span>
									</div>
									<div class="breaks">
										<div class="breaks_list">
											'.Settings_Dayoff_Query::working_hours_list('sunday', $ID).'
										</div>
										<div class="breaks_add">
											<a href="#"><span></span>'.esc_html__('Add New', 'bookmify').'</a>
										</div>
									</div>
								</div>
								<div class="apply">
									<span class="protip" data-pt-target="true" data-pt-title="'.esc_attr__('Apply to all days', 'bookmify').'" data-pt-gravity="left -4 0">
										<img class="bookmify_be_svg" src="'.BOOKMIFY_ASSETS_URL.'/img/check.svg" alt="" />
									</span>
								</div>
							</div>
						</li>
					</ul>

				</form>';
		return $html;
	}
	public static function getDaysOffEmployeeTab($ID = ''){
		global $wpdb;
		$html = '<div class="bookmify_be_day_off_wrap">
					<div class="bookmify_day_off_add_section">
						<div class="bookmify_day_off_add_section_in">
							<form autocomplete="off">
								<div class="do_item">
									<label>'.esc_html__('Date', 'bookmify').'
									<span>*</span></label>
									<input data-id="'.$ID.'" type="text" name="offday_days" placeholder="'.esc_attr__('yy-mm-dd, yy-mm-dd ...', 'bookmify').'" />
									<input type="hidden" name="offday_hidden_day" id="offday_hidden_day_'.$ID.'" />
								</div>
								<div class="do_item">
									<label for="offday_name">'.esc_html__('Title', 'bookmify').'
									<span>*</span></label>
									<input id="offday_name" type="text" name="offday_name" placeholder="'.esc_attr__('Enter Off Day Title...', 'bookmify').'" />
								</div>
								<div class="do_dd_footer">
									<div class="left_part">
										<label class="switch">
											<input type="checkbox" id="repeat" value="1" name="offday_repeat" />
											<span class="slider round"></span>
										</label>
										<label class="repeater" for="repeat">'.esc_html__('Repeat Every Year', 'bookmify').'</label>
									</div>
									<div class="right_part">
										<a class="add" href="#">
											<span class="text">'.esc_html__('Add', 'bookmify').'</span>

											<span class="save_process">
												<span class="ball"></span>
												<span class="ball"></span>
												<span class="ball"></span>
											</span>
										</a>
										<a class="cancel" href="#">'.esc_html__('Clear', 'bookmify').'</a>
									</div>
								</div>
							</form>
						</div>
					</div>
					<div class="do_list">
						<div class="do_list_in">
							<div class="do_list_topbar">
								<div class="do_list_topbar_in">
									<span class="list_date">
										'.esc_html__('Date', 'bookmify').'
									</span>
									<span class="list_title">
										'.esc_html__('Title', 'bookmify').'
									</span>
								</div>
							</div>
							<div class="do_list_content">
								<div class="bookmify_be_tab_wrap">
									<div class="bookmify_be_link_tabs">
										<ul>
											<li class="active"><a href="#" class="bookmify_be_tab_link">'.esc_html__('Private Days Off', 'bookmify').'</a></li>
											<li><a href="#" class="bookmify_be_tab_link">'.esc_html__('Global Days Off', 'bookmify').'</a></li>
										</ul>
									</div>
									<div class="bookmify_be_content_tabs">
										<div class="bookmify_be_tab_pane active">
											'.self::employeeDaysOffList($ID).'
										</div>
										<div class="bookmify_be_tab_pane">
											'.self::employeeDaysOffList('all').'
										</div>
									</div>

								</div>
							</div>
							<div class="do_list_footer">
								<span>
									<span class="f_year"></span>
									<span class="f_text">
										'.esc_html__('Repeat Every Year', 'bookmify').'
									</span>
								</span>
								<span>
									<span class="f_once f_year"></span>
									<span class="f_text">
										'.esc_html__('Once Only', 'bookmify').'
									</span>
								</span>
							</div>
						</div>
					</div>
				</div>';
		return $html;
	}
	public static function getServicesEmployeeTab($ID = ''){
		global $wpdb;
		$html = '<div class="bookmify_be_employee_edite_services">
					'.self::getEmployeeServicesList($ID).'
				</div>';
		return $html;
	}
	public static function getDetailsEmployeeTab($ID = ''){
		global $wpdb;
		$html				= '';
		$googleClientID 	= get_option( 'bookmify_be_gc_client_id', '' );
		$googleClientSecret = get_option( 'bookmify_be_gc_client_secret', '' );
		$googleContent 		= 'enable';
		if($googleClientID == '' || $googleClientSecret == ''){
			$googleContent 	= 'disable';
		}
		if($ID == ''){
			$googleWrap		= '';
			if($googleContent == 'enable'){
				$googleTop      = '<span>'.esc_html__('Google Profile', 'bookmify').'</span>';
				$googleBottom 	= '<span>'.esc_html__('Inactive', 'bookmify').'</span>';
				$googleWrap		= 	'<div class="bookmify_be_emmp_google_cal">
											<div class="google_cal_in">
												<span class="g_top">'.$googleTop.'</span>
												<span class="g_bottom">'.$googleBottom.'</span>
											</div>
											<div class="google_cal_icon">
												<span><img class="bookmify_be_svg" src="'.BOOKMIFY_ASSETS_URL.'img/google_dash.svg" alt="" /></span>
											</div>
										</div>';
			}
				
			$html = '<div class="bookmify_be_employee_edit_detail">
						<div class="left_part">
							<div class="input_img">
								<input type="hidden" class="bookmify_be_img_id" name="employee_img_id" value="" />
								<div class="bookmify_thumb_wrap">
									<div class="bookmify_thumb_edit">
										<span class="edit"><img class="bookmify_be_svg" src="'.BOOKMIFY_ASSETS_URL.'img/image.svg" alt="" /></span>
									</div>
									<div class="bookmify_thumb_remove"><a href="#" class="bookmify_be_delete" data-entity-id=""><img class="bookmify_be_svg" src="'.BOOKMIFY_ASSETS_URL.'img/delete.svg" alt="" /></a></div>
								</div>
							</div>
							<div class="visibility">
								<label class="switch">
									<input type="checkbox" id="visible_" value="1" name="employee_visibility" checked />
									<span class="slider round"></span>
								</label>
								<label class="repeater" for="visible_" checked="checked	">'.esc_html__('Visible to Public','bookmify').'</label>
							</div>
							'.$googleWrap.'
						</div>
						<div class="right_part">

							<div class="first_last_name">
								<div class="first_name">
									<label>
										<span class="title">'.esc_html__('First Name','bookmify').'<span>*</span></span>
									</label>
									<input class="required_field" type="text" name="first_name" value="" />
								</div>
								<div class="last_name">
									<label>
										<span class="title">'.esc_html__('Last Name','bookmify').'<span>*</span></span>
									</label>
									<input class="required_field" type="text" name="last_name" value="" />
								</div>
							</div>
							
							
							
							<div class="email_location">
								<div class="email_wrap">
									<label>
										<span class="title">'.esc_html__('Email','bookmify').'<span>*</span></span>
									</label>
									<input class="required_field employee_email" type="text" name="email" value="" />
								</div>
								<div class="location_wrap">
									<label>
										<span class="title">'.esc_html__('Location','bookmify').'</span>
									</label>
									<input type="text" name="location" placeholder="'.esc_attr__('Select from Existing Locations','bookmify').'" value="" readonly />
									<input type="hidden" name="location_id" value="">
									<span class="bot_btn"><span></span><img class="bookmify_be_svg" src="'.BOOKMIFY_ASSETS_URL.'img/down.svg" alt="" /></span>
								</div>
							</div>

							<div class="phone_wpuser">
								<div class="phone_wrap">
									<label>
										<span class="title">'.esc_html__('Phone','bookmify').'</span>
									</label>
									<input type="tel" name="phone" value="" />
									<span class="bot__btn"><img class="bookmify_be_svg" src="'.BOOKMIFY_ASSETS_URL.'img/down.svg" alt="" /></span>
								</div>
								<div class="wp_user_wrap">
									<label>
										<span class="title">'.esc_html__('WordPress User','bookmify').'</span>
									</label>
									<input type="text" name="wp_user" placeholder="'.esc_attr__('Select from WP users','bookmify').'" readonly value="">
									<input type="hidden" name="wp_user_id" value="">
									<span class="bot_btn"><span></span><img class="bookmify_be_svg" src="'.BOOKMIFY_ASSETS_URL.'img/down.svg" alt="" /></span>
								</div>
							</div>
							<div class="info_holder">
								<label>'.esc_html__('Info','bookmify').'</label>
								<textarea name="employee_info" placeholder="'.esc_attr__('Some info for internal usage','bookmify').'"></textarea>
							</div>

						</div>
					</div>';
		}else{
			$ID 			= esc_sql($ID);
			$query 			= "SELECT * FROM {$wpdb->prefix}bmify_employees WHERE id=".$ID;
			$employees	 	= $wpdb->get_results( $query, OBJECT  );
			foreach($employees as $employee){
				$ID						= $employee->id;
				$wpUserID				= $employee->wp_user_id;
				$attachmentID			= $employee->attachment_id;
				$visibility				= $employee->visibility;
				$firstName				= $employee->first_name;
				$lastName				= $employee->last_name;
				$email					= $employee->email;
				$phone					= $employee->phone;
				$info					= $employee->info;
				$attachmentURLLarge		= Helper::bookmifyGetImageByID($attachmentID, 'large');
				$attachmentURL	 		= Helper::bookmifyGetImageByID($attachmentID);
				if($attachmentURL != ''){$opened = 'has_image';}else{$opened = '';}
				$selected	 			= bookmify_be_checked($visibility, "public");
				if(Helper::bookmifyDoesWPUserExist($wpUserID)){
					$wpUserName			= Helper::bookmifyWPUserNamebyID($wpUserID);
				}else{
					$wpUserID 			= '';
					$wpUserName 		= '';
					$wpdb->query($wpdb->prepare( "UPDATE {$wpdb->prefix}bmify_employees SET wp_user_id=%s WHERE id=%d", '', $ID));
				}
				$locationID 			= Helper::getLocationDataByEmployeeID($ID, "id");
				$locationName 			= Helper::getLocationDataByEmployeeID($ID);
				

				$googleWrap				= '';
				if($googleContent == 'enable'){
					
					$googleData 			= HelperEmployees::getGoogleData($ID);

					if($googleData != NULL){
						$googleData 		= json_decode(stripslashes($googleData), true);
						$googleTop 			= '<a href="mailto:'.$googleData['calendarID'].'" title="'.esc_attr__('Send mail to this email', 'bookmify').'">'.$googleData['calendarID'].'</a>';
						$googleBottom 		= '<span>'.esc_html__('Active', 'bookmify').'</span>';
					}else{
						$googleTop      	= '<span>'.esc_html__('Google Profile', 'bookmify').'</span>';
						$googleBottom 		= '<span>'.esc_html__('Inactive', 'bookmify').'</span>';
					}
					$googleWrap		= 	'<div class="bookmify_be_emmp_google_cal">
											<div class="google_cal_in">
												<span class="g_top">'.$googleTop.'</span>
												<span class="g_bottom">'.$googleBottom.'</span>
											</div>
											<div class="google_cal_icon">
												<span><img class="bookmify_be_svg" src="'.BOOKMIFY_ASSETS_URL.'img/google_dash.svg" alt="" /></span>
											</div>
										</div>';
				}
				
				$html 			= '<div class="bookmify_be_employee_edit_detail">
									<div class="left_part">
										<div class="input_img">
											<input type="hidden" class="bookmify_be_img_id" name="employee_img_id" value="'.$attachmentID.'" />
											<div class="bookmify_thumb_wrap '.$opened.'" style="background-image:url('.$attachmentURLLarge.')">
												<div class="bookmify_thumb_edit">
													<span class="edit"><img class="bookmify_be_svg" src="'.BOOKMIFY_ASSETS_URL.'img/image.svg" alt="" /></span>
												</div>
												<div class="bookmify_thumb_remove '.$opened.'"><a href="#" class="bookmify_be_delete" data-entity-id="'.$ID.'"><img class="bookmify_be_svg" src="'.BOOKMIFY_ASSETS_URL.'img/delete.svg" alt="" /></a></div>
											</div>
										</div>
										<div class="visibility">
											<label class="switch">
												<input type="checkbox" id="visible_'.$ID.'" value="1" name="employee_visibility" '.$selected.' />
												<span class="slider round"></span>
											</label>
											<label class="repeater" for="visible_'.$ID.'">'.esc_html__('Visible to Public','bookmify').'</label>
										</div>
										'.$googleWrap.'
									</div>
									<div class="right_part">

										<div class="first_last_name">
											<div class="first_name">
												<label>
													<span class="title">'.esc_html__('First Name','bookmify').'<span>*</span></span>
												</label>
												<input class="required_field" type="text" name="first_name" value="'.$firstName.'" />
											</div>
											<div class="last_name">
												<label>
													<span class="title">'.esc_html__('Last Name','bookmify').'<span>*</span></span>
												</label>
												<input class="required_field" type="text" name="last_name" value="'.$lastName.'" />
											</div>
										</div>

										<div class="email_location">
											<div class="email_wrap">
												<label>
													<span class="title">'.esc_html__('Email','bookmify').'<span>*</span></span>
												</label>
												<input class="required_field employee_email" type="text" name="email" value="'.$email.'" />
											</div>
											<div class="location_wrap">
												<label>
													<span class="title">'.esc_html__('Location','bookmify').'</span>
												</label>
												<input type="text" name="location" placeholder="'.esc_attr__('Select from Existing Locations','bookmify').'" value="'.$locationName.'" readonly />
												<input type="hidden" name="location_id" value="'.$locationID.'">
												<span class="bot_btn"><span></span><img class="bookmify_be_svg" src="'.BOOKMIFY_ASSETS_URL.'img/down.svg" alt="" /></span>
											</div>
										</div>

										<div class="phone_wpuser">
											<div class="phone_wrap">
												<label>
													<span class="title">'.esc_html__('Phone','bookmify').'</span>
												</label>
												<input type="tel" name="phone" value="'.$phone.'" />
												<span class="bot__btn"><img class="bookmify_be_svg" src="'.BOOKMIFY_ASSETS_URL.'img/down.svg" alt="" /></span>
											</div>
											<div class="wp_user_wrap">
												<label>
													<span class="title">'.esc_html__('WordPress User','bookmify').'</span>
												</label>
												<input type="text" name="wp_user" placeholder="'.esc_attr__('Select from WP users','bookmify').'" readonly value="'.$wpUserName.'">
												<input type="hidden" name="wp_user_id" value="'.$wpUserID.'">
												<span class="bot_btn"><span></span><img class="bookmify_be_svg" src="'.BOOKMIFY_ASSETS_URL.'img/down.svg" alt="" /></span>
											</div>
										</div>
										
										
										
										<div class="info_holder">
											<label>'.esc_html__('Info','bookmify').'</label>
											<textarea name="employee_info" placeholder="'.esc_attr__('Some info for internal usage','bookmify').'">'.$info.'</textarea>
										</div>

									</div>
								</div>';
			}
		}
			
		return $html;
	}
	/**
     * Get Service Col.
	 * @since 1.0.0
     */
    public static function getLocationIDsByEmployeeID( $id = NULL )
    {
        global $wpdb;
		$result = '';
		
		if($id == NULL || $id == ''){
			
		}else{
			$id 		= esc_sql($id);
			$query 		= "SELECT location_id FROM {$wpdb->prefix}bmify_employee_locations WHERE employee_id=".$id;
			$results 	= $wpdb->get_results( $query);
			foreach($results as $service){
				$result .= $service->location_id;
				$result .= ',';
			}
			$result = rtrim($result,",");
		}
		return $result;
    }
	/**
	 * @since 1.0.0
	 * @access public
	*/
	public static function getLocationNewValue($employee_id){
		global $wpdb;
		$output = '';

		$assigned_locations_ids = array();
		$checked = '';
		
		$query = "SELECT * FROM {$wpdb->prefix}bmify_locations ORDER BY title, id";
		$locations = $wpdb->get_results( $query, OBJECT  );
		
		$employee_id = esc_sql($employee_id);
		$query = "SELECT * FROM {$wpdb->prefix}bmify_employee_locations WHERE employee_id=".$employee_id;
		$group = $wpdb->get_results( $query, OBJECT  );
		
		foreach($group as $provider){
			$assigned_locations_ids[] = $provider->location_id;
		}
		$count = '';
		$key = 0;
		$myKey = 0;
		$ofKey = 0;
		
		// experimental types: +3 or 3/4
		$type = 'of'; // plus
		foreach($locations as $location){
			$ofKey++;
			if(in_array( $location->id, $assigned_locations_ids )){$checked = 'checked';}
			
			if($checked == 'checked'){
				$key++;
				if($key == 1){
					$output .= $location->title.' '.$location->address;
				}else{
					$myKey++;
				}
			}
			$checked = '';
		}
		if($output == ''){
			$output = '';
		}else{
			if($myKey > 0){
				if($type == 'of'){
					$output .= '<span>'.($myKey+1).' / '.($ofKey).'</span>';
				}else{
					$output .= '<span>+'.($myKey).'</span>';
				}
				
			}
		}
			
		return $output;
	}
	/**
	 * @since 1.0.0
	 * @access public
	*/
	public static function getEmployeeServicesList($employee_id){
		global $wpdb;
		$assigned_service_ids = array();
		if($employee_id == ''){
			$query 		= "SELECT * FROM {$wpdb->prefix}bmify_categories ORDER BY position, id";
			$categories = $wpdb->get_results( $query, OBJECT  );
			$output 	= '<ul class="service_list">';
		
			foreach($categories as $category){
				$serviceHTML 	= '';
				$categoryID		= $category->id;
				$categoryID 	= esc_sql($categoryID);
				$query 			= "SELECT * FROM {$wpdb->prefix}bmify_services WHERE category_id=".$categoryID." ORDER BY title, id";
				$services 		= $wpdb->get_results( $query, OBJECT  );
				foreach($services as $service){
					$disabled = 'disabled';
					$serviceTitle = Helper::titleDecryption($service->title);
					$randon_number = rand(10,9999);

					$uid = 'bookmify_be_employees_service_'.$service->id.'_'.$randon_number;

					$serviceHTML .= '<li>
										<div class="service_wrapper">
											<div class="price_input_wrap service_title">
												<span class="bookmify_be_checkbox">
													<input type="checkbox" name="service_ids[]" class="bookmify_be_check_item" value="'.$service->id.'" data-title="'.$serviceTitle.'" id="'.$uid.'">
													<span class="checkmark">
														<img class="bookmify_be_svg" src="'.BOOKMIFY_ASSETS_URL.'/img/checked.svg" alt="" />
													</span>
												</span>
												<label for="'.$uid.'"><span>'.$serviceTitle.'</span></label>
											</div>
											<div class="price_wrap price_capacity">
												<input type="number" class="bookmify_be_employees_service_price" name="service_prices[]" value="'.$service->price.'" '.$disabled.'>
												<input type="number" min="1" class="bookmify_be_employees_service_cmin" name="service_cmins[]" value="'.$service->capacity_min.'" '.$disabled.'>
												<input type="number" min="1" class="bookmify_be_employees_service_cmax" name="service_cmaxs[]" value="'.$service->capacity_max.'" '.$disabled.'>
												<input type="number" min="1" max="100" class="bookmify_be_employees_service_deposit" name="service_cdeposit[]" value="100" '.$disabled.'>
											</div>
										</div>

									</li>';

				}
				if (!empty($services)){
					$output .= '<li class="category_item">
									<div class="category_item_in">
										<div class="category_heading">
											<span class="bookmify_be_checkbox">
												<input class="select_all" type="checkbox">
												<span class="checkmark">
													<img class="bookmify_be_svg" src="'.BOOKMIFY_ASSETS_URL.'/img/checked.svg" alt="" />
												</span>
											</span>
											<span class="cat_title">'.$category->title.'</span>
											<span class="cat_price">'.esc_html__('Price', 'bookmify').'</span>
											<span class="cat_capacity">'.esc_html__('Capacity', 'bookmify').'</span>
											<span class="cat_deposit">'.esc_html__('Deposit', 'bookmify').'</span>
										</div>
										<ul class="services_list_cat">';
				}
				$output .= $serviceHTML;
				if (!empty($services)){
					$output .= '</ul></div></li>';
				}
			}


			$output .= '</ul>';
		}else{
			$query 			= "SELECT * FROM {$wpdb->prefix}bmify_categories ORDER BY position, id";
			$categories 	= $wpdb->get_results( $query, OBJECT  );

			$employee_id 	= esc_sql($employee_id);
			$query 			= "SELECT service_id FROM {$wpdb->prefix}bmify_employee_services WHERE employee_id=".$employee_id;
			$group 			= $wpdb->get_results( $query, OBJECT  );

			foreach($group as $service){
				$assigned_service_ids[] = $service->service_id;
			}

			$output = '<ul class="service_list">';

			foreach($categories as $category){
				$serviceHTML = '';
				$i = 0;
				$k = 0;
				$categoryID = $category->id;
				$categoryID = esc_sql($categoryID);
				$query = "SELECT * FROM {$wpdb->prefix}bmify_services WHERE category_id=".$categoryID." ORDER BY title, id";
				$services = $wpdb->get_results( $query, OBJECT  );
				foreach($services as $service){
					$i++;
					$serviceTitle = Helper::titleDecryption($service->title);
					$disabled = 'disabled';
					$checked = '';
					$employees_price = '';
					$active = '';

					// check capacity min and max
					/**********************************************************************************************************/
					$employees_cmin = '';
					$employees_cmax = '';
					
					$serviceID 		= $service->id;
					$serviceID 		= esc_sql($serviceID);
					$employee_id 	= esc_sql($employee_id);
					$query 			= "SELECT capacity_min,capacity_max,price,deposit FROM {$wpdb->prefix}bmify_employee_services WHERE employee_id=".$employee_id." AND service_id=".$serviceID;
					$cap_mins 		= $wpdb->get_results( $query, OBJECT  );
					$deposit		= 100;
					foreach($cap_mins as $cap_min){
						$employees_cmin 	= $cap_min->capacity_min;
						$employees_cmax 	= $cap_min->capacity_max;
						$employees_price 	= $cap_min->price;
						$deposit 			= (int) $cap_min->deposit;
					}

					if($employees_cmin != ''){$last_cmin = $employees_cmin;}else{$last_cmin = $service->capacity_min;}

					if($employees_cmax != ''){$last_cmax = $employees_cmax;}else{$last_cmax = $service->capacity_max;}

					/**********************************************************************************************************/

					if($employees_price != ''){$last_price = $employees_price;}else{$last_price = $service->price;}

					/**********************************************************************************************************/

					if(in_array( $serviceID, $assigned_service_ids )){
						$checked 	= 'checked="checked"';
						$disabled 	= '';
						$active 	= 'active';
						$k++;
					}

					/**********************************************************************************************************/

					$randon_number = rand(10,9999);

					$uid = 'bookmify_be_employees_service_'.$employee_id.'_'.$serviceID.'_'.$randon_number;

					$serviceHTML .= '<li class="'.$active.'">
									<div class="service_wrapper">
										<div class="price_input_wrap service_title">
											<span class="bookmify_be_checkbox">
												<input type="checkbox" name="service_ids[]" class="bookmify_be_check_item" value="'.$service->id.'" '.$checked.' data-title="'.$serviceTitle.'" id="'.$uid.'">
												<span class="checkmark">
													<img class="bookmify_be_svg" src="'.BOOKMIFY_ASSETS_URL.'/img/checked.svg" alt="" />
												</span>
											</span>
											<label for="'.$uid.'"><span>'.$serviceTitle.'</span></label>
										</div>
										<div class="price_wrap price_capacity">
											<input type="number" class="bookmify_be_employees_service_price" name="service_prices[]" value="'.$last_price.'" '.$disabled.'>
											<input type="number" min="1" class="bookmify_be_employees_service_cmin" name="service_cmins[]" value="'.$last_cmin.'" '.$disabled.'>
											<input type="number" min="1" class="bookmify_be_employees_service_cmax" name="service_cmaxs[]" value="'.$last_cmax.'" '.$disabled.'>
											<input type="number" min="1" max="100" class="bookmify_be_employees_service_deposit" name="service_cdeposit[]" value="'.$deposit.'" '.$disabled.'>
										</div>
									</div>


								</li>';

				}
				if($i == $k){
					$checked2 = 'checked="checked"';
				}else{
					$checked2 = '';
				}
				if (!empty($services)){
					$output .= '<li class="category_item">
									<div class="category_item_in">
										<div class="category_heading">
											<span class="bookmify_be_checkbox">
												<input class="select_all" type="checkbox" '.$checked2.'>
												<span class="checkmark">
													<img class="bookmify_be_svg" src="'.BOOKMIFY_ASSETS_URL.'/img/checked.svg" alt="" />
												</span>
											</span>
											<span class="cat_title">'.$category->title.'</span>
											<span class="cat_price">'.esc_html__('Price', 'bookmify').'</span>
											<span class="cat_capacity">'.esc_html__('Capacity', 'bookmify').'</span>
											<span class="cat_deposit">'.esc_html__('Deposit', 'bookmify').'</span>
										</div>
										<ul class="services_list_cat">';
				}
				$output .= $serviceHTML;
				if (!empty($services)){
					$output .= '</ul></div></li>';
				}
			}


			$output .= '</ul>';
		}
			
		return $output;
	}
	
	public static function employeeDaysOffList($employeeID){
		global $wpdb;
		if($employeeID == ''){
			$myres = '<div class="bookmify_private_dayoff_list"><ul class="dayoff_list"></ul></div>';
		}else{
			if($employeeID == 'all'){
				$query 	= "SELECT * FROM {$wpdb->prefix}bmify_dayoff WHERE employee_id IS NULL";
			}else{
				$employeeID	= esc_sql($employeeID);
				$query 	= "SELECT * FROM {$wpdb->prefix}bmify_dayoff WHERE employee_id=".$employeeID;
			}

			$myres 		= '<div class="bookmify_private_dayoff_list">';
			if($employeeID == 'all'){
				$myres = '';
			}
			$myres 	   .= '<ul class="dayoff_list">';

			$results 	= $wpdb->get_results( $query, OBJECT  );
			foreach($results as $result){
				$edit_delete_panel 	= '';
				$every_year 		= 'no';
				$checked 			= '';
				$title	 			= '';
				$date 				= '';
				if($result->every_year == 1){
					$every_year 	= 'yes';
				}
				if($every_year == 'yes'){
					$checked 		= 'checked';
				}
				$dayoffID			= $result->id;
				$dayoffDate			= $result->date;
				$dayoffTitle		= $result->title;
				$edit_delete_panel = '<div class="buttons_holder">
										<div class="btn_item btn_edit">
											<a href="#" class="bookmify_be_edit">
												<img class="bookmify_be_svg edit" src="'.BOOKMIFY_ASSETS_URL.'img/edit.svg" alt="" />
												<img class="bookmify_be_svg checked" src="'.BOOKMIFY_ASSETS_URL.'img/checked.svg" alt="" />
											</a>
										</div>
										<div class="btn_item">
											<a href="#" class="bookmify_be_delete" data-entity-id="'.$dayoffID.'">
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
													<label for="mdp-do-month-'.$dayoffID.'">'.esc_html__('Date', 'bookmify').'<span>*</span></label>
													<input data-selected-day="'.$dayoffDate.'" class="mdp-do-hidden required_field" id="mdp-do-month-'.$dayoffID.'" type="text" name="offday_days" placeholder="'.esc_attr__('yy-mm-dd', 'bookmify').'" />
													<input class="offday_hidden_day" type="hidden" name="offday_hidden_day" id="offday_hidden_day_'.$dayoffID.'" />
												</div>
												<div class="do_item">
													<label for="offday_name-'.$dayoffID.'">'.esc_html__('Title', 'bookmify').'<span>*</span></label>
													<input class="required_field" id="offday_name-'.$dayoffID.'" type="text" name="offday_name" placeholder="'.esc_attr__('Enter Off Day Title...', 'bookmify').'" value="'.$dayoffTitle.'" />
												</div>
												<div class="do_dd_footer">
													<div class="left_part">
														<label class="switch">
															<input type="checkbox" id="repeat-'.$dayoffID.'" value="1" name="offday_repeat" '.$checked.' />
															<span class="slider round"></span>
														</label>
														<label class="repeater" for="repeat-'.$dayoffID.'">'.esc_html__('Repeat Every Year', 'bookmify').'</label>
													</div>
												</div>
											</form>
										</div>';
				// date format URI: https://codex.wordpress.org/Formatting_Date_and_Time
				if($employeeID == 'all'){
					$edit_delete_panel 	= "";
					$update_block 		= "";
				}
				$date 	= '<span class="list_date">'.date_i18n(get_option('bookmify_be_date_format', 'd F, Y'), strtotime($dayoffDate)).'</span>';
				$title 	= '<span class="list_title">'.$dayoffTitle.'</span>';
				$myres .= '<li class="bookmify_be_list_item" data-entity-id="'.$dayoffID.'">
								<div class="bookmify_be_list_item_in">
									<div class="bookmify_be_list_item_header">
										<div class="header_in item" data-yearly="'.$every_year.'">
											<div class="header_info">
												<span class="f_year"></span>
												'.$date.$title.'
											</div>
											'.$edit_delete_panel.'
										</div>
									</div>
									'.$update_block.'
								</div>
							</li>';
			}



			if($employeeID == 'all'){
				$myres .= '</ul>';
			}else{
				$myres .= '</ul></div>';
			}
		}
			
		
		return $myres;
	}
	public static function allNanoInOne($ID = ''){
		global $wpdb;
		$html  = '<div class="bookmify_be_all_nano employee">';
		$html .= self::locationListNano();
		$html .= self::wpUserListNano($ID);
		$html .= self::workingHoursListNano('', 'employee_working_hours');
		$html .= self::workingHoursListNano('employee_break');
		$html .= '</div>';
		return $html;
	}
	/**
	 * @since 1.0.0
	 * @access public
	*/
	public static function locationListNano(){
		global $wpdb;
		
		$query 		= "SELECT * FROM {$wpdb->prefix}bmify_locations";
		$locations 	= $wpdb->get_results( $query, OBJECT  );
		$html 		= '<div class="nano employee_locations"><div class="nano-content">';
		foreach($locations as $location){
			$html  .= '<div data-id="'.$location->id.'">'.$location->title.'</div>';
		}
		$html 	   .= '</div></div>';
		return $html;
	}
	
	public static function wpUserListNano($ID = ''){
		global $wpdb;
		$andQuery 	= "";
		$ID			= esc_sql($ID);
		if($ID != ''){
			$andQuery = " WHERE id<>".$ID;
		}
		$query 		= "SELECT wp_user_id FROM {$wpdb->prefix}bmify_employees".$andQuery;
		$results 	= $wpdb->get_results( $query, OBJECT  );
		$excludeArr = array();
		foreach($results as $result){
			$excludeArr[] = $result->wp_user_id;
		}
		$args = array(
			'role__in'     	=> array('bookmify-employee'),
			'exclude'     	=> $excludeArr,
		 ); 
		$users 		 = get_users($args);
		$html 		 = 	'<div class="nano scrollbar-inner wp_users">';
		$html 		.= 		'<div class="nano-content">';
		$html 		.= 			'<div data-id="n">'.esc_html__('Create New','bookmify').'</div>';
		foreach ( $users as $user ) {
			$html  .= 			'<div data-id="'.$user->ID.'">'.esc_html( $user->display_name ).'</div>';
		}
		$html 	   .= 		'</div>';
		$html 	   .= 	'</div>';
		return $html;
	}
	public static function workingHoursListNano($day, $class = NULL, $start = NULL, $id = NULL){
		$timeInterval	= get_option( 'bookmify_be_time_interval', '15' );
		$countInterval	= 1440 / $timeInterval;
		$result			= '<div class="nano scrollbar-inner '.$day.' '.$class.'" data-id="'.$id.'" data-start="'.$start.'" data-class="'.$day.'"><div class="nano-content">';
		$startTime 		= strtotime('00:00');
		$timeFormat		= get_option('bookmify_be_time_format', 'h:i a');
		
		for($i = 0; $i < $countInterval; $i++){
			$formatHi 		 = date("H:i", strtotime('+'.$i*$timeInterval.' minutes', $startTime));
			$formatDefault	 = date_i18n($timeFormat,strtotime('+'.$i*$timeInterval.' minutes', $startTime));
			$result 		.= '<div data-id="'.$formatHi.'" data-id2="'.$formatDefault.'">'.$formatDefault.'</div>';
		}
		if($timeFormat == 'H:i'){
			$last  		= '24:00';
		}else{
			$last		= date_i18n($timeFormat,strtotime('24:00')).'.';
		}
		$result .= '<div data-id="24:00" data-id2="'.$last.'">'.$last.'</div>';
		$result .= '</div></div>';
		return $result;
	}
	
	public static function servicesListAsFilter(){
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
	
	public static function updateGoogleData($employeeID, $googleData){
		global $wpdb;
		
		$wpdb->query($wpdb->prepare( "UPDATE {$wpdb->prefix}bmify_employees SET google_data=%s WHERE id=%d", $googleData, $employeeID));
	}
	
	public static function getGoogleData($employeeID){
		global $wpdb;
		$googleData 	= '';
		$employeeID		= esc_sql($employeeID);
		$query 			= "SELECT google_data FROM {$wpdb->prefix}bmify_employees WHERE id=".$employeeID;
		$results		= $wpdb->get_results( $query, OBJECT );
		if(!empty($results)){
			$googleData 	= $results[0]->google_data;
		}
		if($googleData == NULL || $googleData == ''){
			$googleData = '';
		}
		return $googleData;
	}
	
}