<?php
namespace Bookmify;

use Bookmify\Helper;
use Bookmify\HelperAdmin;
use Bookmify\HelperSettings;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

abstract class Settings_Page {
	private $tabs;

	const PAGE_ID = '';

	/**
	 * @abstract
	 * @since 1.0.0
	 * @access protected
	*/
	//abstract protected function create_tabs();

	/**
	 * @abstract
	 * @since 1.0.0
	 * @access protected
	*/
	abstract protected function get_page_title();

	/**
	 * @static
	 * @since 1.0.0
	 * @access public
	*/
	public final static function get_url() {
		return admin_url( 'admin.php?page=' . static::PAGE_ID );
	}

	/**
	 * @since 1.0.0
	 * @access public
	*/
	public function __construct() {
		//add_action( 'admin_init', [ $this, 'register_settings_fields' ] );
	}

	
	public function hours_popup($day, $class= NULL,$start = NULL){
		$timeInterval 	= 30;
		$timeInterval	= get_option( 'bookmify_be_time_interval', '15' );
		$countInterval	= 1440 / $timeInterval;
		$result			= '<div class="nano scrollbar-inner '.$day.' '.$class.'" data-start="'.$start.'" data-class="'.$day.'"><div class="nano-content">';
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
		
		$result		   .= '</div></div>';
		return $result;
	}

	

	/**
	 * @since 1.0.0
	 * @access public
	*/
	public function display_settings_page() {
		
		
		echo HelperAdmin::bookmifyAdminContentStart();
		
		?>
		<div class="bookmify_be_content_wrap bookmify_be_options_wrap">
			<div class="bookmify_be_page_title"><h3><?php echo esc_html($this->get_page_title()); ?></h3></div>
			
			<div class="bookmify_be_page_content">
				<div class="bookmify_be_settings">
					
					<div class="bookmify_be_tabs_wrap">
					<ul id="bookmify_be_tabs_nav" class="bookmify_be_tabs_nav nav nav-tabs">
						<li class="active">
							<a id="bookmify-settings-tab-1" class="nav-tab" data-toggle="tab" href="#tab-1">
								<?php esc_html_e('General', 'bookmify');?>
								<span class="icon"><img class="bookmify_be_svg" src="<?php echo BOOKMIFY_ASSETS_URL;?>/img/layers.svg" alt="" /></span>
							</a>
						</li>
						<li>
							<a id="bookmify-settings-tab-2" class="nav-tab" data-toggle="tab" href="#tab-2">
								<?php esc_html_e('Company Info', 'bookmify');?>
								<span class="icon"><img class="bookmify_be_svg" src="<?php echo BOOKMIFY_ASSETS_URL;?>/img/building.svg" alt="" /></span>
							</a>
						</li>
						<li>
							<a id="bookmify-settings-tab-3" class="nav-tab" data-toggle="tab" href="#tab-3">
								<?php esc_html_e('Working Schedule', 'bookmify');?>
								<span class="icon"><img class="bookmify_be_svg" src="<?php echo BOOKMIFY_ASSETS_URL;?>/img/clock-calendar.svg" alt="" /></span>
							</a>
						</li>
						<li>
							<a id="bookmify-settings-tab-4" class="nav-tab" data-toggle="tab" href="#tab-4">
								<?php esc_html_e('Payments', 'bookmify');?>
								<span class="icon"><img class="bookmify_be_svg" src="<?php echo BOOKMIFY_ASSETS_URL;?>/img/credit-card.svg" alt="" /></span>
							</a>
						</li>
						<li>
							<a id="bookmify-settings-tab-5" class="nav-tab" data-toggle="tab" href="#tab-5">
								<?php esc_html_e('Google Calendar', 'bookmify');?>
								<span class="icon"><img class="bookmify_be_svg" src="<?php echo BOOKMIFY_ASSETS_URL;?>/img/google.svg" alt="" /></span>
							</a>
						</li>
						<li>
							<a id="bookmify-settings-tab-6" class="nav-tab" data-toggle="tab" href="#tab-6">
								<?php esc_html_e('Customers', 'bookmify');?>
								<span class="icon"><img class="bookmify_be_svg" src="<?php echo BOOKMIFY_ASSETS_URL;?>/img/users.svg" alt="" /></span>
							</a>
						</li>
						<li>
							<a id="bookmify-settings-tab-7" class="nav-tab" data-toggle="tab" href="#tab-7">
								<?php esc_html_e('Services', 'bookmify');?>
								<span class="icon"><img class="bookmify_be_svg" src="<?php echo BOOKMIFY_ASSETS_URL;?>/img/service.svg" alt="" /></span>
							</a>
						</li>
						<li>
							<a id="bookmify-settings-tab-8" class="nav-tab" data-toggle="tab" href="#tab-8">
								<?php esc_html_e('Notification', 'bookmify');?>
								<span class="icon"><img class="bookmify_be_svg" src="<?php echo BOOKMIFY_ASSETS_URL;?>/img/notification-bell.svg" alt="" /></span>
							</a>
						</li>
						<li>
							<a id="bookmify-settings-tab-11" class="nav-tab" data-toggle="tab" href="#tab-11">
								<?php esc_html_e('Calendar', 'bookmify');?>
								<span class="icon"><img class="bookmify_be_svg" src="<?php echo BOOKMIFY_ASSETS_URL;?>/img/calendar.svg" alt="" /></span>
							</a>
						</li>
<!--
						<li>
							<a id="bookmify-settings-tab-9" class="nav-tab" data-toggle="tab" href="#tab-9">
								<?php //esc_html_e('Activate Plugin', 'bookmify');?>
								<span class="icon"><img class="bookmify_be_svg" src="<?php //echo BOOKMIFY_ASSETS_URL;?>/img/key.svg" alt="" /></span>
							</a>
						</li>
-->
						<li>
							<a id="bookmify-settings-tab-10" class="nav-tab" data-toggle="tab" href="#tab-10">
								<?php esc_html_e('Customization', 'bookmify');?>
								<span class="icon"><img class="bookmify_be_svg" src="<?php echo BOOKMIFY_ASSETS_URL;?>/img/feature.svg" alt="" /></span>
							</a>
						</li>
					</ul>
					<div class="bookmify_be_tabs_content tab-content">

						<div id="tab-1" class="tab-pane fade bookmify-settings-general-page active">
							<!-- GENERAL settings -->
							<div class="bookmify_be_settings_general">
								<form method="post" action="" class="bookmify_general_options">
								<?php settings_fields('bookmify_be_options'); ?>
								<?php do_settings_sections('bookmify_be_options'); ?>
								<?php echo HelperSettings::bookmifySettingsGeneralTab(); ?>
								</form>
							</div>
							<!-- /GENERAL settings -->
						</div>
						
						
						<div id="tab-5" class="tab-pane fade bookmify-settings-google-calendar-page">
							<!-- GENERAL settings -->
							<div class="bookmify_be_settings_google_calendar">
								<form method="post" action="" class="bookmify_google_calendar_options">
									<?php settings_fields('bookmify_be_google_options');?>
									<?php do_settings_sections('bookmify_be_google_options');?>
									<div class="title_holder">
										<h3><?php esc_html_e('Google Calendar', 'bookmify'); ?></h3>
									</div>
									
									<div class="general_items">
										<div class="general_item_group">
											<!-- Client ID -->
											<?php 
												$gcAddPending			= '';
												$gcAddAttendees			= '';
												$gcSendInvitation		= '';
												if(get_option('bookmify_be_gc_add_pending', '') == 'on'){
													$gcAddPending 		= 'checked="checked"';
												}
												if(get_option('bookmify_be_gc_add_attendees', '') == 'on'){
													$gcAddAttendees 	= 'checked="checked"';
												}
												if(get_option('bookmify_be_gc_send_invitaion', '') == 'on'){
													$gcSendInvitation 	= 'checked="checked"';
												}
											?>
											<div class="general_item">
												<div class="item_title"><label for="gc_client_id" title="<?php esc_attr_e('Client ID', 'bookmify');?>"><?php esc_html_e('Client ID', 'bookmify'); ?></label></div>
												<div class="item_content"><input id="gc_client_id" name="bookmify_be_gc_client_id" type="text" value="<?php echo get_option('bookmify_be_gc_client_id', ''); ?>" /></div>
											</div>
											<!-- /Client ID -->

											<!-- Client Secret -->
											<div class="general_item">
												<div class="item_title"><label for="gc_client_secret" title="<?php esc_attr_e('Client Secret', 'bookmify');?>"><?php esc_html_e('Client Secret', 'bookmify'); ?></label></div>
												<div class="item_content"><input id="gc_client_secret" name="bookmify_be_gc_client_secret" type="text" value="<?php echo get_option('bookmify_be_gc_client_secret', ''); ?>" /></div>
											</div>
											<!-- /Client Secret -->
										</div>
										<div class="general_item_group">
											<!-- Redirect URL -->
											<div class="general_item">
												<div class="item_title"><label for="gc_redirect_url" title="<?php esc_attr_e('Redirect URL', 'bookmify');?>"><?php esc_html_e('Redirect URL', 'bookmify'); ?></label></div>
												<div class="item_content"><input onClick="this.select();" id="gc_redirect_url" readonly name="bookmify_be_gc_redirect_url" type="text" value="<?php echo esc_url(BOOKMIFY_SITE_URL.'/wp-admin/admin.php?page=bookmify_user_profile');?>" /></div>
											</div>
											<!-- /Redirect URL -->

											<!-- Maximum Number Of Events Returned -->
											<div class="general_item">
												<div class="item_title"><label for="gc_max_num_events" title="<?php esc_attr_e('Maximum Number Of Events Returned', 'bookmify');?>"><?php esc_html_e('Maximum Number Of Events Returned', 'bookmify'); ?></label></div>
												<div class="item_content"><input id="gc_max_num_events" name="bookmify_be_gc_max_num_events" type="number" value="<?php echo get_option('bookmify_be_gc_max_num_events', 40); ?>" /></div>
											</div>
											<!-- /Maximum Number Of Events Returned:  -->
										</div>
										<div class="general_item_group">
											<!-- Add Pending Appointments -->
											<div class="general_item">
												<div class="item_title"><label for="gc_add_pending_app" title="<?php esc_attr_e('Add Pending Appointments', 'bookmify');?>"><?php esc_html_e('Add Pending Appointments', 'bookmify'); ?></label></div>
												<div class="item_content">
													<label class="bookmify_be_switch">
														<input type="checkbox" id="gc_add_pending_app" name="bookmify_be_gc_add_pending" <?php echo esc_attr($gcAddPending);?> />
														<span class="slider round"></span>
													</label>
												</div>
											</div>
											<!-- /Add Pending Appointments -->

											<!-- Add Event\'s Attendees -->
											<div class="general_item">
												<div class="item_title"><label for="gc_add_event_attendees" title="<?php esc_attr_e('Add Event\'s Attendees', 'bookmify');?>"><?php esc_html_e('Add Event\'s Attendees', 'bookmify'); ?></label></div>
												<div class="item_content">
													<label class="bookmify_be_switch">
														<input type="checkbox" id="gc_add_event_attendees" name="bookmify_be_gc_add_attendees" <?php echo esc_attr($gcAddAttendees);?> />
														<span class="slider round"></span>
													</label>
												</div>
											</div>
											<!-- /Add Event\'s Attendees -->

											<!-- Send Event Invitation Email  -->
											<div class="general_item">
												<div class="item_title"><label for="gc_send_event_invitation" title="<?php esc_attr_e('Send Event Invitation Email', 'bookmify');?>"><?php esc_html_e('Send Event Invitation Email', 'bookmify'); ?></label></div>
												<div class="item_content">
													<label class="bookmify_be_switch">
														<input type="checkbox" id="gc_send_event_invitation" name="bookmify_be_gc_send_invitaion" <?php echo esc_attr($gcSendInvitation);?> />
														<span class="slider round"></span>
													</label>
												</div>
											</div>
											<!-- /Add Event\'s Attendees -->
										</div>
									</div>
									

									<div class="save_btn">
										<a class="bookmify_save_link" href="#">
											<span class="text"><?php esc_html_e('Save', 'bookmify');?></span>
											<span class="save_process">
												<span class="ball"></span>
												<span class="ball"></span>
												<span class="ball"></span>
											</span>
										</a>
									</div>

								</form>
							</div>
							<!-- /GENERAL settings -->
						</div>


						<div id="tab-3" class="tab-pane fade bookmify-settings-ws-page">
							<div class="working_schedule">
								<div class="days_off_content">
									<div class="title_holder">
										<h3><?php esc_html_e('Working Schedule', 'bookmify');?></h3>
									</div>
									<div class="bookmify_be_tabs_wrap_offday bookmify_be_tab_wrap">
										<div class="offday_tab_wrapper bookmify_be_link_tabs">
											<ul class="bookmify_be_tabs_nav_offday">
												<li class="active">
													<a class="bookmify_be_tab_link" href="#"><?php esc_html_e('Working Hours', 'bookmify');?></a>
												</li>
												<li>
													<a class="bookmify_be_tab_link" href="#"><?php esc_html_e('Days Off', 'bookmify');?></a>
												</li>
											</ul>
										</div>
										<div class="bookmify_be_tabs_content_offday bookmify_be_content_tabs">
											<div class="active bookmify_be_tab_pane">
												<?php													
													echo Settings_Page::hours_popup('monday', 'working_hours');
													echo Settings_Page::hours_popup('break');
												?>
												<form method="post" action="" class="bookmify_working_hours">

													<?php settings_fields('bookmify_be_whours');?>
													<?php do_settings_sections('bookmify_be_whours');?>
													<?php 
														$timeFormat = get_option('bookmify_be_time_format', 'h:i a');
														$moChecked 	= '';$tuChecked = '';$weChecked = '';$thChecked = '';
														$frChecked 	= '';$saChecked = '';$suChecked = '';
														$moSWH 		= get_option('bookmify_be_monday_start', '08:00');
														$moEWH 		= get_option('bookmify_be_monday_end', '18:00');
														$tuSWH 		= get_option('bookmify_be_tuesday_start', '08:00');
														$tuEWH 		= get_option('bookmify_be_tuesday_end', '18:00');
														$weSWH 		= get_option('bookmify_be_wednesday_start', '08:00');
														$weEWH 		= get_option('bookmify_be_wednesday_end', '18:00');
														$thSWH 		= get_option('bookmify_be_thursday_start', '08:00');
														$thEWH 		= get_option('bookmify_be_thursday_end', '18:00');
														$frSWH 		= get_option('bookmify_be_friday_start', '08:00');
														$frEWH 		= get_option('bookmify_be_friday_end', '18:00');
														$saSWH 		= get_option('bookmify_be_saturday_start', '08:00');
														$saEWH 		= get_option('bookmify_be_saturday_end', '18:00');
														$suSWH 		= get_option('bookmify_be_sunday_start', '08:00');
														$suEWH 		= get_option('bookmify_be_sunday_end', '18:00');
														if($moSWH !== ''){
															$moChecked 	= 'checked="checked"';
															$moSWH		= date_i18n($timeFormat,strtotime($moSWH));
															$moEWH		= date_i18n($timeFormat,strtotime($moEWH));
														}
														if($tuSWH !== ''){
															$tuChecked 	= 'checked="checked"';
															$tuSWH		= date_i18n($timeFormat,strtotime($tuSWH));
															$tuEWH		= date_i18n($timeFormat,strtotime($tuEWH));
														}
														if($weSWH !== ''){
															$weChecked 	= 'checked="checked"';
															$weSWH		= date_i18n($timeFormat,strtotime($weSWH));
															$weEWH		= date_i18n($timeFormat,strtotime($weEWH));
														}
														if($thSWH !== ''){
															$thChecked 	= 'checked="checked"';
															$thSWH		= date_i18n($timeFormat,strtotime($thSWH));
															$thEWH		= date_i18n($timeFormat,strtotime($thEWH));
														}
														if($frSWH !== ''){
															$frChecked 	= 'checked="checked"';
															$frSWH		= date_i18n($timeFormat,strtotime($frSWH));
															$frEWH		= date_i18n($timeFormat,strtotime($frEWH));
														}
														if($saSWH !== ''){
															$saChecked 	= 'checked="checked"';
															$saSWH		= date_i18n($timeFormat,strtotime($saSWH));
															$saEWH		= date_i18n($timeFormat,strtotime($saEWH));
														}
														if($suSWH !== ''){
															$suChecked 	= 'checked="checked"';
															$suSWH		= date_i18n($timeFormat,strtotime($suSWH));
															$suEWH		= date_i18n($timeFormat,strtotime($suEWH));
														}
														if($timeFormat != 'H:i'){
															if((date('H', strtotime($moEWH)) + date('i', strtotime($moEWH))) == 0){$moEWH .= '.';}
															if((date('H', strtotime($tuEWH)) + date('i', strtotime($tuEWH))) == 0){$tuEWH .= '.';}
															if((date('H', strtotime($weEWH)) + date('i', strtotime($weEWH))) == 0){$weEWH .= '.';}
															if((date('H', strtotime($thEWH)) + date('i', strtotime($thEWH))) == 0){$thEWH .= '.';}
															if((date('H', strtotime($frEWH)) + date('i', strtotime($frEWH))) == 0){$frEWH .= '.';}
															if((date('H', strtotime($saEWH)) + date('i', strtotime($saEWH))) == 0){$saEWH .= '.';}
															if((date('H', strtotime($suEWH)) + date('i', strtotime($suEWH))) == 0){$suEWH .= '.';}
														}else{
															if((date('H', strtotime($moEWH)) + date('i', strtotime($moEWH))) == 0){$moEWH  = '24:00';}
															if((date('H', strtotime($tuEWH)) + date('i', strtotime($tuEWH))) == 0){$tuEWH  = '24:00';}
															if((date('H', strtotime($weEWH)) + date('i', strtotime($weEWH))) == 0){$weEWH  = '24:00';}
															if((date('H', strtotime($thEWH)) + date('i', strtotime($thEWH))) == 0){$thEWH  = '24:00';}
															if((date('H', strtotime($frEWH)) + date('i', strtotime($frEWH))) == 0){$frEWH  = '24:00';}
															if((date('H', strtotime($saEWH)) + date('i', strtotime($saEWH))) == 0){$saEWH  = '24:00';}
															if((date('H', strtotime($suEWH)) + date('i', strtotime($suEWH))) == 0){$suEWH  = '24:00';}
														}
													?>
													<ul class="bookmify_be_working_hours_list setting_wh">
														<li class="monday">
															<div class="item hour_item">
																<div class="item_wh">
																	<div class="day">
																		<label>
																			<input name="monday_checked" type="checkbox" <?php echo esc_attr($moChecked);?>>
																			<span class="checkmark">
																				<img class="bookmify_be_svg" src="<?php echo BOOKMIFY_ASSETS_URL;?>/img/checked.svg" alt="" />
																			</span>
																		</label>
																		<span><?php esc_html_e('Monday', 'bookmify');?></span>
																	</div>
																	<div class="hours">
																		<div class="start"><input class="time" readonly autocomplete="off" name="bookmify_be_monday_start" type="text" value="<?php echo $moSWH; ?>" /></div>
																		<div class="end"><input class="time" readonly autocomplete="off" name="bookmify_be_monday_end" type="text" value="<?php echo $moEWH;?>" /></div>
																	</div>
																</div>
																<div class="item_bh">
																	<div class="day">
																		<span><?php esc_html_e('Breaks', 'bookmify');?></span>
																	</div>
																	<div class="breaks">
																		<div class="breaks_list">
																			<?php
																				$results = Settings_Dayoff_Query::working_hours_list('monday');
																				echo wp_kses_post($results);
																			?>
																		</div>
																		<div class="breaks_add">
																			<a href="#"><span></span><?php esc_html_e('Add New', 'bookmify');?></a>
																		</div>
																	</div>
																</div>
																<div class="apply">
																	<span class="protip" data-pt-title="<?php esc_attr_e('Apply to all days', 'bookmify');?>" data-pt-gravity="left -4 0">
																		<img class="bookmify_be_svg" src="<?php echo BOOKMIFY_ASSETS_URL;?>/img/check.svg" alt="" />
																	</span>
																</div>
															</div>
														</li>
														<li class="tuesday">
															<div class="item hour_item">
																<div class="item_wh">
																	<div class="day">
																		<label>
																			<input name="tuesday_checked" type="checkbox" <?php echo esc_attr($tuChecked);?>>
																			<span class="checkmark">
																				<img class="bookmify_be_svg" src="<?php echo BOOKMIFY_ASSETS_URL;?>/img/checked.svg" alt="" />
																			</span>
																		</label>
																		<span><?php esc_html_e('Tuesday', 'bookmify');?></span>
																	</div>
																	<div class="hours">
																		<div class="start"><input class="time" readonly autocomplete="off" name="bookmify_be_tuesday_start" type="text" value="<?php echo $tuSWH; ?>" /></div>
																		<div class="end"><input class="time" readonly autocomplete="off" name="bookmify_be_tuesday_end" type="text" value="<?php echo $tuEWH; ?>" /></div>
																	</div>
																</div>
																<div class="item_bh">
																	<div class="day">
																		<span><?php esc_html_e('Breaks', 'bookmify');?></span>
																	</div>
																	<div class="breaks">
																		<div class="breaks_list">
																			<?php
																				$results = Settings_Dayoff_Query::working_hours_list('tuesday');
																				echo wp_kses_post($results);
																			?>
																		</div>
																		<div class="breaks_add">
																			<a href="#"><span></span><?php esc_html_e('Add New', 'bookmify');?></a>
																		</div>
																	</div>
																</div>
																<div class="apply">
																	<span class="protip" data-pt-title="<?php esc_attr_e('Apply to all days', 'bookmify');?>" data-pt-gravity="left -4 0">
																		<img class="bookmify_be_svg" src="<?php echo BOOKMIFY_ASSETS_URL;?>/img/check.svg" alt="" />
																	</span>
																</div>
															</div>
														</li>
														<li class="wednesday">
															<div class="item hour_item">
																<div class="item_wh">
																	<div class="day">
																		<label>
																			<input name="wednesday_checked" type="checkbox" <?php echo esc_attr($weChecked);?>>
																			<span class="checkmark">
																				<img class="bookmify_be_svg" src="<?php echo BOOKMIFY_ASSETS_URL;?>/img/checked.svg" alt="" />
																			</span>
																		</label>
																		<span><?php esc_html_e('Wednesday', 'bookmify');?></span>
																	</div>
																	<div class="hours">
																		<div class="start"><input class="time" readonly autocomplete="off" name="bookmify_be_wednesday_start" type="text" value="<?php echo $weSWH; ?>" /></div>
																		<div class="end"><input class="time" readonly autocomplete="off" name="bookmify_be_wednesday_end" type="text" value="<?php echo $weEWH; ?>" /></div>
																	</div>
																</div>
																<div class="item_bh">
																	<div class="day">
																		<span><?php esc_html_e('Breaks', 'bookmify');?></span>
																	</div>
																	<div class="breaks">
																		<div class="breaks_list">
																			<?php
																				$results = Settings_Dayoff_Query::working_hours_list('wednesday');
																				echo wp_kses_post($results);
																			?>
																		</div>
																		<div class="breaks_add">
																			<a href="#"><span></span><?php esc_html_e('Add New', 'bookmify');?></a>
																		</div>
																	</div>
																</div>
																<div class="apply">
																	<span class="protip" data-pt-title="<?php esc_attr_e('Apply to all days', 'bookmify');?>" data-pt-gravity="left -4 0">
																		<img class="bookmify_be_svg" src="<?php echo BOOKMIFY_ASSETS_URL;?>/img/check.svg" alt="" />
																	</span>
																</div>
															</div>
														</li>
														<li class="thursday">
															<div class="item hour_item">
																<div class="item_wh">
																	<div class="day">
																		<label>
																			<input name="thursday_checked" type="checkbox" <?php echo esc_attr($thChecked);?>>
																			<span class="checkmark">
																				<img class="bookmify_be_svg" src="<?php echo BOOKMIFY_ASSETS_URL;?>/img/checked.svg" alt="" />
																			</span>
																		</label>
																		<span><?php esc_html_e('Thursday', 'bookmify');?></span>
																	</div>
																	<div class="hours">
																		<div class="start"><input class="time" readonly autocomplete="off" name="bookmify_be_thursday_start" type="text" value="<?php echo $thSWH; ?>" /></div>
																		<div class="end"><input class="time" readonly autocomplete="off" name="bookmify_be_thursday_end" type="text" value="<?php echo $thEWH; ?>" /></div>
																	</div>
																</div>
																<div class="item_bh">
																	<div class="day">
																		<span><?php esc_html_e('Breaks', 'bookmify');?></span>
																	</div>
																	<div class="breaks">
																		<div class="breaks_list">
																			<?php
																				$results = Settings_Dayoff_Query::working_hours_list('thursday');
																				echo wp_kses_post($results);
																			?>
																		</div>
																		<div class="breaks_add">
																			<a href="#"><span></span><?php esc_html_e('Add New', 'bookmify');?></a>
																		</div>
																	</div>
																</div>
																<div class="apply">
																	<span class="protip" data-pt-title="<?php esc_attr_e('Apply to all days', 'bookmify');?>" data-pt-gravity="left -4 0">
																		<img class="bookmify_be_svg" src="<?php echo BOOKMIFY_ASSETS_URL;?>/img/check.svg" alt="" />
																	</span>
																</div>
															</div>
														</li>
														<li class="friday">
															<div class="item hour_item">
																<div class="item_wh">
																	<div class="day">
																		<label>
																			<input name="friday_checked" type="checkbox" <?php echo esc_attr($frChecked);?>>
																			<span class="checkmark">
																				<img class="bookmify_be_svg" src="<?php echo BOOKMIFY_ASSETS_URL;?>/img/checked.svg" alt="" />
																			</span>
																		</label>
																		<span><?php esc_html_e('Friday', 'bookmify');?></span>
																	</div>
																	<div class="hours">
																		<div class="start"><input class="time" readonly autocomplete="off" name="bookmify_be_friday_start" type="text" value="<?php echo $frSWH; ?>" /></div>
																		<div class="end"><input class="time" readonly autocomplete="off" name="bookmify_be_friday_end" type="text" value="<?php echo $frEWH; ?>" /></div>
																	</div>
																</div>
																<div class="item_bh">
																	<div class="day">
																		<span><?php esc_html_e('Breaks', 'bookmify');?></span>
																	</div>
																	<div class="breaks">
																		<div class="breaks_list">
																			<?php
																				$results = Settings_Dayoff_Query::working_hours_list('friday');
																				echo wp_kses_post($results);
																			?>
																		</div>
																		<div class="breaks_add">
																			<a href="#"><span></span><?php esc_html_e('Add New', 'bookmify');?></a>
																		</div>
																	</div>
																</div>
																<div class="apply">
																	<span class="protip" data-pt-title="<?php esc_attr_e('Apply to all days', 'bookmify');?>" data-pt-gravity="left -4 0">
																		<img class="bookmify_be_svg" src="<?php echo BOOKMIFY_ASSETS_URL;?>/img/check.svg" alt="" />
																	</span>
																</div>
															</div>
														</li>
														<li class="saturday">
															<div class="item hour_item">
																<div class="item_wh">
																	<div class="day">
																		<label>
																			<input name="saturday_checked" type="checkbox" <?php echo esc_attr($saChecked);?>>
																			<span class="checkmark">
																				<img class="bookmify_be_svg" src="<?php echo BOOKMIFY_ASSETS_URL;?>/img/checked.svg" alt="" />
																			</span>
																		</label>
																		<span><?php esc_html_e('Saturday', 'bookmify');?></span>
																	</div>
																	<div class="hours">
																		<div class="start"><input class="time" readonly autocomplete="off" name="bookmify_be_saturday_start" type="text" value="<?php echo $saSWH; ?>" /></div>
																		<div class="end"><input class="time" readonly autocomplete="off" name="bookmify_be_saturday_end" type="text" value="<?php echo $saEWH; ?>" /></div>
																	</div>
																</div>
																<div class="item_bh">
																	<div class="day">
																		<span><?php esc_html_e('Breaks', 'bookmify');?></span>
																	</div>
																	<div class="breaks">
																		<div class="breaks_list">
																			<?php
																				$results = Settings_Dayoff_Query::working_hours_list('saturday');
																				echo wp_kses_post($results);
																			?>
																		</div>
																		<div class="breaks_add">
																			<a href="#"><span></span><?php esc_html_e('Add New', 'bookmify');?></a>
																		</div>
																	</div>
																</div>
																<div class="apply">
																	<span class="protip" data-pt-title="<?php esc_attr_e('Apply to all days', 'bookmify');?>" data-pt-gravity="left -4 0">
																		<img class="bookmify_be_svg" src="<?php echo BOOKMIFY_ASSETS_URL;?>/img/check.svg" alt="" />
																	</span>
																</div>
															</div>
														</li>
														<li class="sunday">
															<div class="item hour_item">
																<div class="item_wh">
																	<div class="day">
																		<label>
																			<input name="sunday_checked" type="checkbox" <?php echo esc_attr($suChecked);?>>
																			<span class="checkmark">
																				<img class="bookmify_be_svg" src="<?php echo BOOKMIFY_ASSETS_URL;?>/img/checked.svg" alt="" />
																			</span>
																		</label>
																		<span><?php esc_html_e('Sunday', 'bookmify');?></span>
																	</div>
																	<div class="hours">
																		<div class="start"><input class="time" readonly autocomplete="off" name="bookmify_be_sunday_start" type="text" value="<?php echo $suSWH; ?>" /></div>
																		<div class="end"><input class="time" readonly autocomplete="off" name="bookmify_be_sunday_end" type="text" value="<?php echo $suEWH; ?>" /></div>
																	</div>
																</div>
																<div class="item_bh">
																	<div class="day">
																		<span><?php esc_html_e('Breaks', 'bookmify');?></span>
																	</div>
																	<div class="breaks">
																		<div class="breaks_list">
																			<?php
																				$results = Settings_Dayoff_Query::working_hours_list('sunday');
																				echo wp_kses_post($results);
																			?>
																		</div>
																		<div class="breaks_add">
																			<a href="#"><span></span><?php esc_html_e('Add New', 'bookmify');?></a>
																		</div>
																	</div>
																</div>
																<div class="apply">
																	<span class="protip" data-pt-title="<?php esc_attr_e('Apply to all days', 'bookmify');?>" data-pt-gravity="left -4 0">
																		<img class="bookmify_be_svg" src="<?php echo BOOKMIFY_ASSETS_URL;?>/img/check.svg" alt="" />
																	</span>
																</div>
															</div>
														</li>
													</ul>

													<div class="save_working_hours">
														<a href="#">
															<span class="text"><?php esc_html_e('Save', 'bookmify'); ?></span>

															<span class="save_process">
																<span class="ball"></span>
																<span class="ball"></span>
																<span class="ball"></span>
															</span>
														</a>
													</div>
												</form>
											</div>
											<div class="bookmify_be_tab_pane bookmify_be_day_off_wrapper">
												<div class="bookmify_be_day_off_wrap">
													<div class="bookmify_day_off_add_section">
														<div class="bookmify_day_off_add_section_in">
															<form autocomplete="off">
																<div class="do_item">
																	<label for="mdp-do-month"><?php esc_html_e('Date', 'bookmify'); ?><span>*</span></label>
																	<input id="mdp-do-month" type="text" name="offday_days" placeholder="<?php esc_attr_e('yy-mm-dd, yy-mm-dd ...', 'bookmify');?>" />
																	<input type="hidden" name="offday_hidden_day" id="offday_hidden_day" />
																</div>
																<div class="do_item">
																	<label for="offday_name"><?php esc_html_e('Title', 'bookmify'); ?><span>*</span></label>
																	<input id="offday_name" type="text" name="offday_name" placeholder="<?php esc_attr_e('Enter Off Day Title...', 'bookmify');?>" />
																</div>
																<div class="do_dd_footer">
																	<div class="left_part">
																		<label class="switch">
																			<input type="checkbox" id="repeat" value="1" name="offday_repeat" />
																			<span class="slider round"></span>
																		</label>
																		<label class="repeater" for="repeat"><?php esc_html_e('Repeat Every Year', 'bookmify');?></label>
																	</div>
																	<div class="right_part">
																		<a class="add" href="#">
																			<span class="text"><?php esc_html_e('Save', 'bookmify'); ?></span>

																			<span class="save_process">
																				<span class="ball"></span>
																				<span class="ball"></span>
																				<span class="ball"></span>
																			</span>
																		</a>
																		<a class="cancel" href="#"><?php esc_html_e('Clear', 'bookmify'); ?></a>
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
																		<?php esc_html_e('Date', 'bookmify'); ?>
																	</span>
																	<span class="list_title">
																		<?php esc_html_e('Title', 'bookmify'); ?>
																	</span>
																</div>
															</div>
															<div class="do_list_content">
																<?php 
																	$results = Settings_Dayoff_Query::dayoff_list();
																	echo wp_kses_post($results);
																?>
															</div>
															<div class="do_list_footer">
																<span>
																	<span class="f_year"></span>
																	<span class="f_text">
																		<?php esc_html_e('Repeat Every Year', 'bookmify'); ?>
																	</span>
																</span>
															</div>

														</div>
													</div>
												</div>
											</div>
										</div>


									</div>
								</div>
							</div>
						</div>


						<div id="tab-6" class="tab-pane fade bookmify-settings-customers-page">
							<!-- CUSTOMER settings -->
							<div class="bookmify_be_settings_customer">
								<form method="post" action="" class="bookmify_settings_form bookmify_customer_options">
									<?php settings_fields('bookmify_be_customer_options');?>
									<?php do_settings_sections('bookmify_be_customer_options');?>
									<div class="title_holder">
										<h3><?php esc_html_e('Customers', 'bookmify'); ?></h3>
									</div>
									
									
									<div class="general_item">
										<div class="item_title">
											<label for="customers_pp" title="<?php esc_attr_e('Customers Per Page', 'bookmify');?>"><?php esc_html_e('Customers Per Page', 'bookmify'); ?></label>
										</div>
										<div class="item_content">
											<select id="customers_pp" class="bookmify_be_customers_pp" name="bookmify_be_customers_pp">
												<?php
												$html = '';
												$numbers = array('all' => esc_html__('Show All', 'bookmify'));
												for($i=1; $i <= 500;){$numbers[$i] = $i; $i++;}

												foreach($numbers as $key => $number){
													$html .= '<option value="'.$key.'" '.bookmify_be_selected(get_option( 'bookmify_be_customers_pp', 10 ), $key).'>'.$number.'</option>';
												}
												echo wp_kses_post($html);
												?>
											</select>
										</div>
									</div>



									<div class="save_btn">
										<a class="bookmify_save_link" href="#">
											<span class="text"><?php esc_html_e('Save', 'bookmify');?></span>
											<span class="save_process">
												<span class="ball"></span>
												<span class="ball"></span>
												<span class="ball"></span>
											</span>
										</a>
									</div>

								</form>
							</div>
							<!-- /CUSTOMER settings -->
						</div>
						
						
						<div id="tab-4" class="tab-pane fade bookmify-settings-payments-page">
							<!-- PAYMENTS settings -->
							<div class="bookmify_be_settings_payments">
								<form method="post" action="" class="bookmify_settings_form bookmify_payment_options">
									<?php settings_fields('bookmify_be_payment_options');?>
									<?php do_settings_sections('bookmify_be_payment_options');?>
									<div class="title_holder">
										<h3><?php esc_html_e('Payments', 'bookmify'); ?></h3>
									</div>
									
									<div class="general_items">
										
										<!-- Payment Page Options -->
										<div class="general_item_group">
											<div class="general_item">
												<div class="item_title"><label title="<?php esc_attr_e('Payments Per Page', 'bookmify');?>"><?php esc_html_e('Payments Per Page', 'bookmify'); ?></label></div>
												<div class="item_content">
													<select name="bookmify_be_payments_pp">
														<?php
														$html = '';
														$numbers = array('all' => esc_html__('Show All', 'bookmify'));
														for($i=1; $i <= 500;){$numbers[$i] = $i; $i++;}

														foreach($numbers as $key => $number){
															$html .= '<option value="'.$key.'" '.bookmify_be_selected(get_option( 'bookmify_be_payments_pp', 10 ), $key).'>'.$number.'</option>';
														}
														echo wp_kses_post($html);
														?>
													</select>
												</div>
											</div>

											<div class="general_item">
												<div class="item_title"><label title="<?php esc_attr_e('Payment Filter Date Range', 'bookmify');?>"><?php esc_html_e('Payment Filter Date Range', 'bookmify'); ?></label></div>
												<div class="item_content">
													<select name="bookmify_be_payments_daterange">
														<?php
														$html = '';
														$numbers = array();
														for($i=1; $i <= 90;){$numbers[$i] = $i; $i++;}

														foreach($numbers as $key => $number){
															$html .= '<option value="'.$key.'" '.bookmify_be_selected(get_option( 'bookmify_be_payments_daterange', 30 ), $key).'>'.$number.'</option>';
														}
														echo wp_kses_post($html);
														?>
													</select>
												</div>
											</div>
											<?php 
												$localPaymentChecked = '';
												if(get_option('bookmify_be_local_payment', 'on') == 'on'){
													$localPaymentChecked = 'checked="checked"';
												}
											?>
											<div class="general_item local_payment_switch <?php echo esc_attr($localPaymentChecked);?>">
												<div class="item_title"><label for="local_payment_switch" title="<?php esc_attr_e('Local Payment', 'bookmify');?>"><?php esc_html_e('Local Payment', 'bookmify'); ?></label></div>
												<div class="item_content">
													<label class="bookmify_be_switch">
														<input type="checkbox" id="local_payment_switch" name="bookmify_be_local_payment" <?php echo esc_attr($localPaymentChecked);?>  />
														<span class="slider round"></span>
													</label>
												</div>
											</div>
											
											<?php 
												if(Helper::enabledPaymentMethods() == 1){
													$paymentSectionSwitcher = 'enabled';
												}else{
													$paymentSectionSwitcher = 'disabled';
												}
											?>
											<div class="general_item payment_section_action <?php echo esc_attr($paymentSectionSwitcher);?>">
												<div class="item_title">
													<label for="payment_section" title="<?php esc_attr_e('Payment Section Action', 'bookmify');?>"><?php esc_html_e('Payment Section Action', 'bookmify'); ?></label>
												</div>
												<div class="item_content">
													<?php 
														$paymentSections = Helper::bookmifyPaymentSectionAction();
													?>
													<select id="payment_section" class="bookmify_be_payment_section" name="bookmify_be_payment_section">
														<?php
														$html = '';
														foreach($paymentSections as $format => $paymentSection){
															$html .= '<option value="'.$format.'" '.bookmify_be_selected(get_option( 'bookmify_be_payment_section', 'default' ), $format).'>'.$paymentSection['ct'].'</option>';
														}
														echo wp_kses_post($html);
														?>
													</select>
												</div>
											</div>
											
										</div>
										<!-- /Payment Page Options -->
										
										
										<!-- Currency Options -->
										<div class="general_item_group">
											<div class="general_item">
												<div class="item_title">
													<label for="currency_format" title="<?php esc_attr_e('Currency Format', 'bookmify');?>"><?php esc_html_e('Currency Format', 'bookmify'); ?></label>
												</div>
												<div class="item_content">
													<?php 
														$currencies = Helper::bookmifyCurrencies();
													?>
													<select id="currency_format" class="bookmify_be_currencies" name="bookmify_be_currency_format">
														<?php
														$html = '';
														foreach($currencies as $format => $currency){
															$html .= '<option value="'.$format.'" '.bookmify_be_selected(get_option( 'bookmify_be_currency_format', 'USD' ), $format).'>'.$currency['ct'].' '.$currency['sb'].'</option>';
														}
														echo wp_kses_post($html);
														?>
													</select>
												</div>
											</div>

											<div class="general_item">
												<div class="item_title">
													<label for="currency_position" title="<?php esc_attr_e('Currency Position', 'bookmify');?>"><?php esc_html_e('Currency Position', 'bookmify'); ?></label>
												</div>
												<div class="item_content">
													<select id="currency_position" class="bookmify_be_currency_position" name="bookmify_be_currency_position">
														<?php
														$html = '';
														$positions = array(
															'left' 		=> esc_html__('Left', 'bookmify'),
															'lspace' 	=> esc_html__('Left-Space', 'bookmify'),
															'right' 	=> esc_html__('Right', 'bookmify'),
															'rspace' 	=> esc_html__('Right-Space', 'bookmify'),
														);

														foreach($positions as $key => $position){
															$html .= '<option value="'.$key.'" '.bookmify_be_selected(get_option( 'bookmify_be_currency_position', 'lspace' ), $key).'>'.$position.'</option>';
														}
														echo wp_kses_post($html);
														?>
													</select>
												</div>
											</div>

											<div class="general_item">
												<div class="item_title">
													<label for="price_format" title="<?php esc_attr_e('Price Format', 'bookmify');?>"><?php esc_html_e('Price Format', 'bookmify'); ?></label>
												</div>
												<div class="item_content">
													<select id="price_format" class="bookmify_be_price_format" name="bookmify_be_price_format">
														<?php
														$html = '';
														$formats = array(
															'cd' 	=> esc_html__('Comma-Dot', 'bookmify'),
															'dc' 	=> esc_html__('Dot-Comma', 'bookmify'),
															'sd' 	=> esc_html__('Space-Dot', 'bookmify'),
															'sc' 	=> esc_html__('Space-Comma', 'bookmify'),
														);

														foreach($formats as $key => $format){
															$html .= '<option value="'.$key.'" '.bookmify_be_selected(get_option( 'bookmify_be_price_format', 'cd' ), $key).'>'.$format.'</option>';
														}
														echo wp_kses_post($html);
														?>
													</select>
												</div>
											</div>
											
											<div class="general_item">
												<div class="item_title"><label for="price_decimal" title="<?php esc_attr_e('Price Decimal', 'bookmify');?>"><?php esc_html_e('Price Decimal', 'bookmify'); ?></label></div>
												<div class="item_content">
													<input id="price_decimal" type="number" name="bookmify_be_price_decimal" value="<?php echo get_option( 'bookmify_be_price_decimal', 2 ); ?>">
												</div>
											</div>
											
										</div>
										<!-- /Currency Options -->
										
										<!-- Paypal Options -->
										<div class="general_item_group payment_item_group">
											<?php 
												$payaplTest				= 'disabled';
												$payaplLive				= 'enabled';
												$paypalSandBoxMode		= '';
												if(get_option('bookmify_be_paypal_sandbox_mode', 'on') == 'on'){
													$paypalSandBoxMode 	= 'checked="checked"';
													$payaplTest			= 'enabled';
													$payaplLive			= 'disabled';
												}
												
												$paypalChecked			= '';
												$paypalSwitch			= 'disabled';
												if(get_option('bookmify_be_paypal_switch', '') == 'on'){
													$paypalChecked	 	= 'checked="checked"';
													$paypalSwitch		= 'enabled';
												}else{
													$payaplTest			= 'disabled';
													$payaplLive			= 'disabled';
												}
											?>
											<span class="paypal_logo">
												<img src="<?php echo BOOKMIFY_ASSETS_URL;?>/img/paypal-png.png" alt="" />
											</span>
											<div class="general_item">
												<div class="item_title"><label for="paypal_switch" title="<?php esc_attr_e('Paypal', 'bookmify');?>"><?php esc_html_e('Paypal', 'bookmify'); ?></label></div>
												<div class="item_content">
													<label class="bookmify_be_switch">
														<input type="checkbox" id="paypal_switch" name="bookmify_be_paypal_switch" <?php echo esc_attr($paypalChecked);?>  />
														<span class="slider round"></span>
													</label>
												</div>
											</div>
											<div class="general_item paypal_sandbox_switch <?php echo esc_attr($paypalSwitch);?>">
												<div class="item_title"><label for="paypal_sandbox_mode" title="<?php esc_attr_e('Paypal Sandbox Mode', 'bookmify');?>"><?php esc_html_e('Paypal Sandbox Mode', 'bookmify'); ?></label></div>
												<div class="item_content">
													<label class="bookmify_be_switch">
														<input type="checkbox" id="paypal_sandbox_mode" name="bookmify_be_paypal_sandbox_mode" <?php echo esc_attr($paypalSandBoxMode);?>  />
														<span class="slider round"></span>
													</label>
												</div>
											</div>

											<div class="general_item paypal_sandbox <?php echo esc_attr($payaplTest);?>">
												<div class="item_title">
													<label for="paypal_client_id" title="<?php esc_attr_e('Sandbox Paypal Client ID', 'bookmify');?>"><?php esc_html_e('Sandbox Paypal Client ID', 'bookmify');?></label>
												</div>
												<div class="item_content">
													<input id="paypal_client_id" name="bookmify_be_paypal_client_id" type="text" value="<?php echo get_option('bookmify_be_paypal_client_id', ''); ?>" />
												</div>
											</div>

											<div class="general_item paypal_sandbox <?php echo esc_attr($payaplTest);?>">
												<div class="item_title">
													<label for="paypal_client_secret" title="<?php esc_attr_e('Sandbox Paypal Client Secret', 'bookmify');?>"><?php esc_html_e('Sandbox Paypal Client Secret', 'bookmify');?></label>
												</div>
												<div class="item_content">
													<input id="paypal_client_secret" name="bookmify_be_paypal_client_secret" type="text" value="<?php echo get_option('bookmify_be_paypal_client_secret', ''); ?>" />
												</div>
											</div>

											<div class="general_item paypal_live <?php echo esc_attr($payaplLive);?>">
												<div class="item_title">
													<label for="paypal_client_id_live" title="<?php esc_attr_e('Live Paypal Client ID', 'bookmify');?>"><?php esc_html_e('Live Paypal Client ID', 'bookmify');?></label>
												</div>
												<div class="item_content">
													<input id="paypal_client_id_live" name="bookmify_be_paypal_client_id_live" type="text" value="<?php echo get_option('bookmify_be_paypal_client_id_live', ''); ?>" />
												</div>
											</div>

											<div class="general_item paypal_live <?php echo esc_attr($payaplLive);?>">
												<div class="item_title">
													<label for="paypal_client_secret_live" title="<?php esc_attr_e('Live Paypal Client Secret', 'bookmify');?>"><?php esc_html_e('Live Paypal Client Secret', 'bookmify');?></label>
												</div>
												<div class="item_content">
													<input id="paypal_client_secret_live" name="bookmify_be_paypal_client_secret_live" type="text" value="<?php echo get_option('bookmify_be_paypal_client_secret_live', ''); ?>" />
												</div>
											</div>
										</div>
										<!-- /Paypal Options -->
										
										
										<!-- Stripe Options -->
										<div class="general_item_group payment_item_group">
											<span class="stripe_logo">
												<img src="<?php echo BOOKMIFY_ASSETS_URL;?>/img/stripe.jpg" alt="" />
											</span>
											
											<?php 
												$stripeTest				= 'disabled';
												$stripeLive				= 'enabled';
												$stripeTestMode			= '';
												if(get_option('bookmify_be_stripe_test_mode', 'on') == 'on'){
													$stripeTestMode 	= 'checked="checked"';
													$stripeTest			= 'enabled';
													$stripeLive			= 'disabled';
												}
												
												$stripeChecked			= '';
												$stripeSwitch			= 'disabled';
												if(get_option('bookmify_be_stripe_switch', '') == 'on'){
													$stripeChecked	 	= 'checked="checked"';
													$stripeSwitch		= 'enabled';
												}else{
													$stripeTest			= 'disabled';
													$stripeLive			= 'disabled';
												}
											?>
											<div class="general_item">
												<div class="item_title"><label for="stripe_switch" title="<?php esc_attr_e('Stripe', 'bookmify');?>"><?php esc_html_e('Stripe', 'bookmify'); ?></label></div>
												<div class="item_content">
													<label class="bookmify_be_switch">
														<input type="checkbox" id="stripe_switch" name="bookmify_be_stripe_switch" <?php echo esc_attr($stripeChecked);?>  />
														<span class="slider round"></span>
													</label>
												</div>
											</div>
											<div class="general_item stripe_test_switch <?php echo esc_attr($stripeSwitch);?>">
												<div class="item_title"><label for="stripe_test_mode" title="<?php esc_attr_e('Stripe Test Mode', 'bookmify');?>"><?php esc_html_e('Stripe Test Mode', 'bookmify'); ?></label></div>
												<div class="item_content">
													<label class="bookmify_be_switch">
														<input type="checkbox" id="stripe_test_mode" name="bookmify_be_stripe_test_mode" <?php echo esc_attr($stripeTestMode);?>  />
														<span class="slider round"></span>
													</label>
												</div>
											</div>

											<div class="general_item stripe_test <?php echo esc_attr($stripeTest);?>">
												<div class="item_title">
													<label for="strpe_test_publishable_key" title="<?php esc_attr_e('Test Stripe Publishable Key', 'bookmify');?>"><?php esc_html_e('Test Stripe Publishable Key', 'bookmify');?></label>
												</div>
												<div class="item_content">
													<input id="strpe_test_publishable_key" name="bookmify_be_stripe_test_publishable_key" type="text" value="<?php echo get_option('bookmify_be_stripe_test_publishable_key', ''); ?>" />
												</div>
											</div>

											<div class="general_item stripe_test <?php echo esc_attr($stripeTest);?>">
												<div class="item_title">
													<label for="stripe_test_secret_key" title="<?php esc_attr_e('Test Stripe Secret Key', 'bookmify');?>"><?php esc_html_e('Test Stripe Secret Key', 'bookmify');?></label>
												</div>
												<div class="item_content">
													<input id="stripe_test_secret_key" name="bookmify_be_stripe_test_secret_key" type="text" value="<?php echo get_option('bookmify_be_stripe_test_secret_key', ''); ?>" />
												</div>
											</div>

											<div class="general_item stripe_live <?php echo esc_attr($stripeLive);?>">
												<div class="item_title">
													<label for="stripe_publishable_key" title="<?php esc_attr_e('Live Stripe Publishable Key', 'bookmify');?>"><?php esc_html_e('Live Stripe Publishable Key', 'bookmify');?></label>
												</div>
												<div class="item_content">
													<input id="stripe_publishable_key" name="bookmify_be_stripe_publishable_key" type="text" value="<?php echo get_option('bookmify_be_stripe_publishable_key', ''); ?>" />
												</div>
											</div>

											<div class="general_item stripe_live <?php echo esc_attr($stripeLive);?>">
												<div class="item_title">
													<label for="stripe_secret_key" title="<?php esc_attr_e('Live Stripe Secret Key', 'bookmify');?>"><?php esc_html_e('Live Stripe Secret Key', 'bookmify');?></label>
												</div>
												<div class="item_content">
													<input id="stripe_secret_key" name="bookmify_be_stripe_secret_key" type="text" value="<?php echo get_option('bookmify_be_stripe_secret_key', ''); ?>" />
												</div>
											</div>
											
										</div>
										<!-- /Stripe Options -->
										
										
									</div>
									
									<div class="save_btn">
										<a class="bookmify_save_link" href="#">
											<span class="text"><?php esc_html_e('Save', 'bookmify');?></span>
											<span class="save_process">
												<span class="ball"></span>
												<span class="ball"></span>
												<span class="ball"></span>
											</span>
										</a>
									</div>

								</form>
							</div>
							<!-- /PAYMENTS settings -->
						</div>
						
						<div id="tab-2" class="tab-pane fade bookmify-settings-company-info-page">
							<!-- Company Info Settings -->
							<div class="bookmify_be_settings_company_info">
								<form method="post" action="" class="bookmify_settings_form bookmify_company_info_options">
								<?php settings_fields('bookmify_be_company_info_options'); ?>
								<?php do_settings_sections('bookmify_be_company_info_options'); ?>
								<?php echo HelperSettings::bookmifySettingsCompanyInfoTab(); ?>
								</form>
							</div>
							<!-- /Company Info Settings -->
						</div>
						

						<div id="tab-7" class="tab-pane fade bookmify-settings-services-page">
							<!-- GENERAL settings -->
							<div class="bookmify_be_settings_service">
								<form method="post" action="" class="bookmify_settings_form bookmify_service_options">
									<?php settings_fields('bookmify_be_service_options');?>
									<?php do_settings_sections('bookmify_be_service_options');?>
									<div class="title_holder">
										<h3><?php esc_html_e('Services', 'bookmify'); ?></h3>
									</div>


									<div class="general_item">
										<div class="item_title"><label title="<?php esc_attr_e('Services Per Page', 'bookmify');?>"><?php esc_html_e('Services Per Page', 'bookmify'); ?></label></div>
										<div class="item_content">
											<select id="spp" class="bookmify_be_spp" name="bookmify_be_services_pp">
												<?php
												$html = '';
												$numbers = array('all' => esc_html__('Show All', 'bookmify'));
												for($i=1; $i <= 500;){$numbers[$i] = $i; $i++;}

												foreach($numbers as $key => $number){
													$html .= '<option value="'.$key.'" '.bookmify_be_selected(get_option( 'bookmify_be_services_pp', 10 ), $key).'>'.$number.'</option>';
												}
												echo wp_kses_post($html);
												?>
											</select>
										</div>
									</div>



									<div class="save_btn">
										<a class="bookmify_save_link" href="#">
											<span class="text"><?php esc_html_e('Save', 'bookmify');?></span>
											<span class="save_process">
												<span class="ball"></span>
												<span class="ball"></span>
												<span class="ball"></span>
											</span>
										</a>
									</div>

								</form>
							</div>
							<!-- /GENERAL settings -->
						</div>
						
						
						<div id="tab-8" class="tab-pane fade bookmify-settings-notification-page">
							<!-- NOTIFICATION settings -->
							<div class="bookmify_be_settings_notification">
								<form method="post" action="" class="bookmify_settings_form bookmify_be_notification_options">
								<?php settings_fields('bookmify_be_notification_options'); ?>
								<?php do_settings_sections('bookmify_be_notification_options'); ?>
								<?php echo HelperSettings::bookmifySettingsNotificationTab(); ?>
								</form>
							</div>
							<!-- /NOTIFICATION settings -->
						</div>
						
						<div id="tab-11" class="tab-pane fade bookmify-settings-calendar-page">
							<!-- NOTIFICATION settings -->
							<div class="bookmify_be_settings_calendar">
								<form method="post" action="" class="bookmify_settings_form bookmify_be_calendar_options">
								<?php settings_fields('bookmify_be_calendar_options'); ?>
								<?php do_settings_sections('bookmify_be_calendar_options'); ?>
								<?php echo HelperSettings::bookmifySettingsCalendarTab(); ?>
								</form>
							</div>
							<!-- /NOTIFICATION settings -->
						</div>
						
						<div id="tab-10" class="tab-pane fade bookmify-settings-frontend-page">
							<!-- FRONTEND settings -->
							<div class="bookmify_be_settings_frontend">
								<form method="post" action="" class="bookmify_settings_form bookmify_be_frontend_options">
								<?php settings_fields('bookmify_be_frontend_options'); ?>
								<?php do_settings_sections('bookmify_be_frontend_options'); ?>
								<?php echo HelperSettings::bookmifySettingsFrontEndTab(); ?>
								</form>
							</div>
							<!-- /FRONTEND settings -->
						</div>



					</div>

				</div>
				</div>
			</div>
			
		</div>
		<?php
		
		echo HelperAdmin::bookmifyAdminContentEnd();
	}

	
	
}
