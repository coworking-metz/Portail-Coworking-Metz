<?php 
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

    $ctl_options_arr = get_option('cool_timeline_settings');
   
    $default_icon = isset($ctl_options_arr['story_content_settings']['default_icon'])?$ctl_options_arr['story_content_settings']['default_icon']:'';
    $ctl_post_per_page = isset($ctl_options_arr['post_per_page'])?$ctl_options_arr['post_per_page']:0;
    $story_desc_type =isset($ctl_options_arr['desc_type'])?$ctl_options_arr['desc_type']:'';
    $ctl_content_length =isset($ctl_options_arr['story_content_settings']['content_length']);
    $ctl_posts_orders =isset( $ctl_options_arr['posts_orders']) ? $ctl_options_arr['posts_orders'] : "DESC";
    $disable_months = isset($ctl_options_arr['story_date_settings']['disable_months']) ? $ctl_options_arr['story_date_settings']['disable_months'] : "no";
    $enable_navigation = isset($ctl_options_arr['navigation_settings']['enable_navigation'] )? $ctl_options_arr['navigation_settings']['enable_navigation'] : 'yes';
    $navigation_position =isset( $ctl_options_arr['navigation_settings']['navigation_position']) ? $ctl_options_arr['navigation_settings']['navigation_position'] : 'right';

    $enable_pagination = isset($ctl_options_arr['pagination_settings']['enable_pagination']) ? $ctl_options_arr['pagination_settings']['enable_pagination'] : 'yes';

  
    $ctl_slideshow =  isset($ctl_options_arr['story_media_settings']['ctl_slideshow']) ? $ctl_options_arr['story_media_settings']['ctl_slideshow'] : true;
    $animation_speed = isset($ctl_options_arr['story_media_settings']['animation_speed'])? $ctl_options_arr['story_media_settings']['animation_speed'] : 7000;      
  
    $r_more=  isset($ctl_options_arr['story_content_settings']['display_readmore'])?$ctl_options_arr['story_content_settings']['display_readmore']:"yes";

    /*
    Content timeline only
    */

    //$ctl_posts_order='date';
    $post_meta =  isset($ctl_options_arr['blog_post_settings']['post_meta']) ? $ctl_options_arr['blog_post_settings']['post_meta'] : 'yes';

    $format = __('d/M/Y', 'cool-timeline');
    $year_position = 2;
     $ctl_post_per_page = $ctl_post_per_page ? $ctl_post_per_page : 10;
     $ctl_content_length ? $ctl_content_length : 100;
?>