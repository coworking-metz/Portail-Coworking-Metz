<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$clt_vars=array();
$ctl_options_arr= get_option('cool_timeline_settings');

$disable_months= isset($ctl_options_arr['story_date_settings']['disable_months']) ? $ctl_options_arr['story_date_settings']['disable_months'] : "no";

/*
 * Style options
 */

$background_type= isset($ctl_options_arr['timeline_background']) ? $ctl_options_arr['timeline_background'] : '0';
   $bg_color='';
  
if ($background_type == '1') {
   $bg_color=isset($ctl_options_arr['timeline_bg_color']) ? $ctl_options_arr['timeline_bg_color'] : 'none';
}

$first_post_color= isset($ctl_options_arr['first_post'])?$ctl_options_arr['first_post'] : "#02c5be";

$second_post_color= isset($ctl_options_arr['second_post'])?$ctl_options_arr['second_post'] : "#f12945";


$content_bg_color= isset($ctl_options_arr['content_bg_color'])?$ctl_options_arr['content_bg_color'] : '#f9f9f9';

$content_color= isset($ctl_options_arr['content_color'])?$ctl_options_arr['content_color'] : '#666666';

$title_color= isset($ctl_options_arr['title_color'])?$ctl_options_arr['title_color'] : '#fff';

$circle_border_color= isset($ctl_options_arr['circle_border_color'])?$ctl_options_arr['circle_border_color'] : '#333333';

$main_title_color= isset($ctl_options_arr['main_title_color'])?$ctl_options_arr['main_title_color'] : '#000';


/*
 * Typography options
 */

$ctl_main_title_typo =isset($ctl_options_arr['main_title_typo'])?$ctl_options_arr['main_title_typo']:"";  
$ctl_post_title_typo =isset($ctl_options_arr['post_title_typo'])?$ctl_options_arr['post_title_typo']:"";
$ctl_post_content_typo =isset($ctl_options_arr['post_content_typo'])?$ctl_options_arr['post_content_typo']:"";
$ctl_date_typo = isset($ctl_options_arr['ctl_date_typo'])?$ctl_options_arr['ctl_date_typo']:"";

$ctl_main_title_typo_all =isset($ctl_options_arr['main_title_typo'])?ctl_pro_get_typeo_output($ctl_options_arr['main_title_typo']):"";  
$ctl_post_title_typo_all =isset($ctl_options_arr['post_title_typo'])?ctl_pro_get_typeo_output($ctl_options_arr['post_title_typo']):"";
$ctl_post_content_typo_all =isset($ctl_options_arr['post_content_typo'])?ctl_pro_get_typeo_output($ctl_options_arr['post_content_typo']):"";
$ctl_date_typo_all = isset($ctl_options_arr['ctl_date_typo'])?ctl_pro_get_typeo_output($ctl_options_arr['ctl_date_typo']):"";

$custom_date_color= isset($ctl_options_arr['custom_date_color'])?$ctl_options_arr['custom_date_color']:'';

$post_title_s= isset($ctl_post_title_typo['font-size']) ? $ctl_post_title_typo['font-size'].'px' : '20px';

$post_content_f= isset($ctl_post_content_typo['font-family']) ? $ctl_post_content_typo['font-family'] : 'inherit';

$ctl_date_f='';$ctl_date_w=''; 

$ctl_date_f= isset($ctl_date_typo['font-family']) ? $ctl_date_typo['font-family'] : 'inherit';
$ctl_date_w= isset($ctl_date_typo['font-weight']) ? $ctl_date_typo['font-weight'] : 'inherit';
   
$ctl_date_color='';
if ($custom_date_color == "yes") {
   $ctl_date_color= isset($ctl_options_arr['ctl_date_color'])?$ctl_options_arr['ctl_date_color'] : '#fff';
}

$line_color= isset($ctl_options_arr['line_color'])? $ctl_options_arr['line_color'] : '#000';

$custom_styles=isset($ctl_options_arr['custom_styles']) ? $ctl_options_arr['custom_styles'] : '';