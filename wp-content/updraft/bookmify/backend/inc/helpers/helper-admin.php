<?php
namespace Bookmify;

use Bookmify\Helper;


// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {exit; }


/**
 * Class Helper Admin
 */
class HelperAdmin
{
	public static function topbarNotificationPopup(){
		$html = '<div class="bookmify_be_notification_popup">
					<div class="not_wrap">
						<div class="not_inner">
							<div class="not_header">
								<h3>'.esc_html__('Notifications', 'bookmify').'</h3>
							</div>
							<div class="not_list">
								<ul>
									<li class="new">
										<div class="item">
											<span class="text">'.esc_html__('New add-on for Bookmify', 'bookmify').'</span>
											<span class="date">Dec 11, 2019</span>
										</div>
									</li>
									<li>
										<div class="item">
											<span class="text">'.esc_html__('Released New Version (1.0.1)', 'bookmify').'</span>
											<span class="date">Nov 04, 2019</span>
										</div>
									</li>
									<li>
										<div class="item">
											<span class="text">'.esc_html__('Fixed Some Issues', 'bookmify').'</span>
											<span class="date">Oct 23, 2019</span>
										</div>
									</li>
									<li>
										<div class="item">
											<span class="text">'.esc_html__('New add-on for Bookmify', 'bookmify').'</span>
											<span class="date">Oct 17, 2019</span>
										</div>
									</li>
								</ul>
							</div>
							<div class="not_footer">
								<a href="#"></a>
								<span>'.esc_html__('Show All Notifications', 'bookmify').'</span>
							</div>
						</div>
					</div>
				</div>';
		
		
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
		
		return $html;
	}
	public static function topbarShortcodePopup(){
		global $wpdb;
		$query 			= "SELECT shortcode,title FROM {$wpdb->prefix}bmify_shortcodes ORDER BY title,id LIMIT 4";
		$results 		= $wpdb->get_results( $query);
		
		$html 			= '<div class="bookmify_be_list shortcode_list">';
		
		$array 			= HelperShortcodes::bookmifyMainShortcodes();
		
		array_unshift($results,$array);
		$list			= '';
		foreach($results as $result){
			$list 		.= '<li>
								<div class="item" title="'.$result->title.'">
									<span class="text">'.$result->shortcode.'</span>
								</div>
							</li>';
		}
		$html = '<div class="bookmify_be_shortcode_popup">
					<div class="short_wrap">
						<div class="short_inner">
							<div class="short_header">
								<h3>'.esc_html__('Shortcodes', 'bookmify').'</h3>
							</div>
							<div class="short_list">
								<ul>
									'.$list.'
								</ul>
							</div>
							<div class="not_footer">
								<a href="'.BOOKMIFY_SITE_URL.'/wp-admin/admin.php?page=bookmify_shortcodes"></a>
								<span>'.esc_html__('All Shortcodes / Add New', 'bookmify').'</span>
							</div>
						</div>
					</div>
				</div>';
		
		
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
		
		return $html;
	}
	public static function topbarHelpCenterPopup(){
		$html = '<div class="bookmify_be_help_popup">
					<div class="help_wrap">
						<div class="help_inner">
							<div class="help_header">
								<h3>'.esc_html__('Help Center', 'bookmify').'</h3>
							</div>
							<div class="help_list">
								<ul>
									<li>
										<a href="https://bookmify.frenify.net/1/how-to-install/" target="_blank">
											<span class="icon">
												<img class="bookmify_be_svg" src="'.BOOKMIFY_ASSETS_URL.'img/document.svg" alt="" />
											</span>
											<span class="text">'.esc_html__('Documentation', 'bookmify').'</span>
										</a>
									</li>
									<li>
										<a href="https://bookmify.frenify.net/1/faq/" target="_blank">
											<span class="icon">
												<img class="bookmify_be_svg" src="'.BOOKMIFY_ASSETS_URL.'img/question.svg" alt="" />
											</span>
											<span class="text">'.esc_html__('FAQ', 'bookmify').'</span>
										</a>
									</li>
									<li>
										<a href="https://codecanyon.net/user/frenify#contact" target="_blank">
											<span class="icon">
												<img class="bookmify_be_svg" src="'.BOOKMIFY_ASSETS_URL.'img/email.svg" alt="" />
											</span>
											<span class="text">'.esc_html__('Contact Us', 'bookmify').'</span>
										</a>
									</li>
									<li>
										<a href="https://codecanyon.net/downloads" target="_blank">
											<span class="icon">
												<img class="bookmify_be_svg" src="'.BOOKMIFY_ASSETS_URL.'img/love.svg" alt="" />
											</span>
											<span class="text">'.esc_html__('Feedback', 'bookmify').'</span>
										</a>
									</li>
								</ul>
							</div>
						</div>
					</div>
				</div>';
		
		
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
		
		return $html;
	}
	
	public static function bookmifyHeader($bookmifyUser = ''){
		$currentUser 		= wp_get_current_user();
		$currentUserName 	= $currentUser->display_name;
		$html = '<div class="bookmify_be_header">
					<div class="header_wrap">
						<div class="logo_wrap">
							<img src="'.BOOKMIFY_ASSETS_URL.'img/main-logo.png" alt="" />
							<span>'.esc_html__('Online Bookings Management', 'bookmify').'</span>
						</div>';
		if($bookmifyUser == ''){
			$html .=		'<div class="notification_wrap">
								<div class="text_panel">
									<h3>'.esc_html__('Hello', 'bookmify').' <span>'.$currentUserName.'.</span></h3>
								</div>
								<div class="icons_panel">';
//						  $html .= '<div class="not_icon new">
//										<span>
//											<img class="bookmify_be_svg" src="'.BOOKMIFY_ASSETS_URL.'img/notification-bell.svg" alt="" />
//										</span>
//										'.self::topbarNotificationPopup().'
//									</div>';
						  $html .= '<div class="short_icon">
										<span>
											<img class="bookmify_be_svg" src="'.BOOKMIFY_ASSETS_URL.'img/code.svg" alt="" />
										</span>
										'.self::topbarShortcodePopup().'
									</div>
									<div class="help_icon">
										<span>
											<img class="bookmify_be_svg" src="'.BOOKMIFY_ASSETS_URL.'img/support.svg" alt="" />
										</span>
										'.self::topbarHelpCenterPopup().'
									</div>
								</div>
							</div>';
		}
			
		$html 		.= '
						
					</div>
				</div>';
		
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
		
		return $html;
	}
	public static function applyPopupForService(){
		$html = '<div id="bookmify_be_confirm_apply">
					<div class="confirm_inner">
						<div class="desc_holder">
							<p>'.esc_html__('You are about to change a service setting which is also configured separately for each employees employee. Do you want to update it in employees settings too?', 'bookmify').'</p>
						</div>
						<div class="links_holder">
							<a class="yes" href="#">'.esc_html__('Yes', 'bookmify').'</a>
							<a class="no" href="#">'.esc_html__('No, just change here', 'bookmify').'</a>
							<a class="cancel" href="#">'.esc_html__('Cancel', 'bookmify').'</a>
						</div>
					</div>
				</div>';
		
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
		
		return $html;
	}
	public static function areYouSurePopup(){		
		$html = '<div id="bookmify_be_confirm">
					<div class="confirm_inner">
						<div class="desc_holder">
							<p>'.esc_html__('Are you sure?', 'bookmify').'</p>
						</div>
						<div class="links_holder">
							<a class="yes" href="#">'.esc_html__('Yes', 'bookmify').'</a>
							<a class="no" href="#">'.esc_html__('Cancel', 'bookmify').'</a>
						</div>
					</div>
				</div>';
		
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
		
		return $html;
	}
	public static function bookmifyHiddenInfo(){		
		$html = '<div class="bookmify_be_price_hidden">
					<input class="currency_format" type="hidden" value="'.Helper::bookmifyGetIconPrice().'" />
					<input class="currency_position" type="hidden" value="'.get_option( 'bookmify_be_currency_position', 'left' ).'" />
					<input class="price_format" type="hidden" value="'.get_option( 'bookmify_be_price_format', 'cd' ).'" />
					<input class="price_decimal" type="hidden" value="'.get_option( 'bookmify_be_price_decimal', '2' ).'" />
				</div>

				<div class="bookmify_be_months_hidden">
					<input class="jan" type="hidden" value="'.date_i18n( 'F', mktime(0, 0, 0, 1), 1 ).'" />
					<input class="feb" type="hidden" value="'.date_i18n( 'F', mktime(0, 0, 0, 2), 1 ).'" />
					<input class="mar" type="hidden" value="'.date_i18n( 'F', mktime(0, 0, 0, 3), 1 ).'" />
					<input class="apr" type="hidden" value="'.date_i18n( 'F', mktime(0, 0, 0, 4), 1 ).'" />
					<input class="may" type="hidden" value="'.date_i18n( 'F', mktime(0, 0, 0, 5, 1) ).'" />
					<input class="jun" type="hidden" value="'.date_i18n( 'F', mktime(0, 0, 0, 6, 1) ).'" />
					<input class="jul" type="hidden" value="'.date_i18n( 'F', mktime(0, 0, 0, 7, 1) ).'" />
					<input class="aug" type="hidden" value="'.date_i18n( 'F', mktime(0, 0, 0, 8, 1) ).'" />
					<input class="sep" type="hidden" value="'.date_i18n( 'F', mktime(0, 0, 0, 9, 1) ).'" />
					<input class="oct" type="hidden" value="'.date_i18n( 'F', mktime(0, 0, 0, 10, 1) ).'" />
					<input class="nov" type="hidden" value="'.date_i18n( 'F', mktime(0, 0, 0, 11, 1) ).'" />
					<input class="dec" type="hidden" value="'.date_i18n( 'F', mktime(0, 0, 0, 12, 1) ).'" />
					<input class="def" type="hidden" value="'.get_option( 'bookmify_be_date_format', 'd F, Y' ) .'" />
				</div>
				<div class="bookmify_be_mintime_tobooking">
					<input value="'.get_option( 'bookmify_be_mintime_tobooking', 'disabled' ).'" type="hidden" />
				</div>
				<div class="bookmify_be_maxtime_tobooking">
					<input value="'.get_option( 'bookmify_be_maxtime_tobooking', 'disabled' ).'" type="hidden" />
				</div>';
		
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
		
		return $html;
	}
	public static function bookmifyAdminContentStart($bookmifyUser = ''){
		$html =	'<div class="bookmify_be_wrapper">
					'.self::bookmifyHiddenInfo().self::areYouSurePopup().self::applyPopupForService().'
					<div class="bookmify_be_wrapper_in">
						'.self::bookmifyHeader($bookmifyUser).'
						<div class="bookmify_be_content">';
							
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
		
		return $html;
	}
	public static function bookmifyAdminContentEnd(){
		$html  = '<div class="bookmify_be_footer"><a class="protip" data-pt-target="true" data-pt-title="'.esc_attr__('View Changelog', 'bookmify').'" data-pt-gravity="left -4 0" href="https://bookmify.frenify.net/1/changelog/" target="_blank">'.esc_html__('v', 'bookmify').BOOKMIFY_VERSION.'</a></div>';
		$html .= '</div></div></div>';				
		return $html;
	}
}