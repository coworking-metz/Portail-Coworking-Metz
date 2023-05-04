<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if( ! function_exists( 'get_fa' ) ) {

	function get_fa( $format = false, $post_id = null ) {
		if ( ! $post_id ) {
			global $post;
			if ( ! is_object( $post ) ) {
				return;
			}
			$post_id = $post->ID;
		}
		$icon='';
		$icon = get_post_meta( $post_id, 'fa_field_icon', true );
		if ( ! $icon ) {
			return;
		}
		if ( $format ) {
			if(strpos($icon, '-o') !==false){
				$icon="fa ".$icon;
			 }else if(strpos($icon, 'fas')!==false || strpos($icon, 'fab') !==false) {
                  $icon=$icon;
             }else{
				$icon="fa ".$icon;
			 }
			$output = '<i class="' . $icon . '"></i>';
		} else {
			$output = $icon;
		}
		return $output;
	}

}



// genrate categories based filter html
  function ctl_categories_filters($taxo,$select_cat,$type,$layout,$filter_categories)
  {
            $filters_html='';$default_cat='';$selected='';
           if(isset($select_cat) && $select_cat!==''){
                 $selected=$select_cat;
                }
            if($type=="story-tm"){
                $taxonomy=$taxo?$taxo:'ctl-stories';
                $action="st_cat_filters";
               $default_cat='';
            }else{
            $taxonomy=$taxo?$taxo:'category';
             $action="ct_cat_filters";
            }
            $taxonomy=apply_filters('ctl-taxonomy-filter',$taxonomy);

             if(version_compare(get_bloginfo('version'),'4.5.0', '>=') ){
              $terms = get_terms(array(
               'taxonomy' =>$taxonomy,
              'hide_empty' =>true,
               ));
              }else{
              $terms = get_terms($taxonomy, array('hide_empty' =>true,
                  ) );
                }

              $terms=apply_filters('ctl-category-filter',$terms);
             $dynamic_cats='';
             $totalposts=0;
             $categories_arr=[];
        if (!empty($terms) && !is_wp_error($terms))
        {
            foreach ($terms as $term) 
            {
                $term_slug=$term->slug;
                if($term_slug){
                    $active='';       
                        if($term_slug=="uncategorized"){
                            continue;  
                        }
                        $totalposts+=$term->count;
                            $categories_arr[$term_slug]['name']=$term->name;
                            $categories_arr[$term_slug]['count']=$term->count; 
                        } 
                        if($term_slug==$selected){
                            $categories_arr[$term_slug]['active']='active-category'; 
                        }
                }    
         }   
              
    
                $filters_html.='<div class="cat-filter-wrp"><ul>';
                if($type!="story-tm"){
                $filters_html.='<li><a data-post-count="'.$totalposts.'" 
                data-type="'.$layout.'"  href="#" class="ct-cat-filters" 
                data-tm-type="'.$type.'" data-action="'.$action.'"
                 data-term-slug="all">'.__('All','cool-timeline').'</a></li>';
                }
            
            if(is_array($filter_categories)){
                $filter_categories[]=$selected;
                $filter_categories= array_unique($filter_categories);
                 foreach($filter_categories as $cat_slug){
                    if(isset($categories_arr[$cat_slug])){
                  $cat_info=  $categories_arr[$cat_slug];

                  if(isset($cat_info['active'])){
                    $active=$cat_info['active'];
                    }else{
                        $active='';  
                    }
                    $filters_html.=sprintf( '<li><a data-type="'.$layout.'" 
                        data-post-count="%1$s" data-tm-type="'.$type.'"
                        href="#" data-action="'.$action.'" class="ct-cat-filters '.$active.'" data-term-slug="%2$s">%3$s</a></li>',
                        esc_attr($cat_info['count']),
                        esc_attr($cat_slug),
                        esc_html($cat_info['name'])
                        );
                    }
                }
            }else{

                if(is_array($categories_arr)){
                    foreach($categories_arr  as $cat_slug=>$cat_info){
                        if(isset($cat_info['active'])){
                            $active=$cat_info['active'];
                        }else{
                            $active='';  
                        }
                      $filters_html.=sprintf( '<li><a data-type="'.$layout.'" 
                        data-post-count="%1$s" data-tm-type="'.$type.'"
                        href="#" data-action="'.$action.'" class="ct-cat-filters '.$active.'" data-term-slug="%2$s">%3$s</a></li>',
                        esc_attr($cat_info['count']),
                        esc_attr($cat_slug),
                        esc_html($cat_info['name'])
                        );
                    }
                }
            }    
                $filters_html.='</ul></div>';
                return $filters_html; 

    //}       
}

//custom excerpt content for content timeline
function ctl_get_content_excerpt($ctl_options_arr,$g_post_content){
    $read_m_btn='';
    $r_more=isset($ctl_options_arr['story_content_settings']['display_readmore'])?$ctl_options_arr['story_content_settings']['display_readmore']:"yes";
     if ($r_more == 'yes') {
        $target = isset($ctl_options_arr['story_content_settings']['story_link_target'])?$ctl_options_arr['story_content_settings']['story_link_target']:'_self';
         if(!empty($ctl_options_arr['story_content_settings']['read_more_lbl']))
            {
            $rmore_lbl=__($ctl_options_arr['story_content_settings']['read_more_lbl'],'cool-timeline');
             } else{
             $rmore_lbl=__('Read More', 'cool-timeline');
             }  
            $read_m_btn= '&hellip;<a class="read_more ctl_read_more" target="'.$target.'" href="' . get_permalink(get_the_ID()) . '">' .$rmore_lbl. '</a>';
           }
    $format = get_post_format() ? : 'standard';  
    
        if ($format=="standard") {
        $limit = isset($ctl_options_arr['story_content_settings']['content_length']) ? $ctl_options_arr['story_content_settings']['content_length'] :50;
            // wpautop() auto-wraps text in paragraphs
            $excerpt= wpautop( 
                // wp_trim_words() gets the first X words from a text string
                wp_trim_words(
                    get_the_excerpt(), // We'll use the post's content as our text string
                    $limit, // We want the first 55 words
                    $read_m_btn // This is what comes after the first 55 words
                ));

            $post_content =$excerpt;  
    }else{
     
        $post_content = apply_filters('the_content', $g_post_content);
    }
    return $post_content;
}

    /*
        Date format settings for stories
     */
function ctl_date_formats($date_format,$ctl_options_arr){
    if(!empty($date_format)){
              if($date_format=="default"){
                  $date_formats =isset($ctl_options_arr['story_date_settings']['ctl_date_formats']) ? $ctl_options_arr['story_date_settings']['ctl_date_formats'] : "M d";
                }else if($date_format=="custom"){ 
                 if (isset($ctl_options_arr['story_date_settings']['custom_date_style']) && $ctl_options_arr['story_date_settings']['custom_date_style']=="yes") {
                        $date_formats =$ctl_options_arr['story_date_settings']['custom_date_formats'];
                    }else{
                       $date_formats = "M d";
                    }
                }
                else{
                     $df=$date_format;
                     $date_formats =__("$df",'cool_timeline');     
                     }  
            }else{
            $defaut_df = isset($ctl_options_arr['story_date_settings']['ctl_date_formats']) ? $ctl_options_arr['story_date_settings']['ctl_date_formats'] : "M d";
            $date_formats =__("$defaut_df",'cool_timeline'); 
            }
            return $date_formats;
}
    
 // getting story date 
function ctl_get_story_date($post_id,$date_formats) {
 $ctl_story_type = get_post_meta($post_id, 'story_type', true);
 $ctl_story_date = isset($ctl_story_type['ctl_story_date'])?$ctl_story_type['ctl_story_date']:'';

  if ($ctl_story_date) {
        if (strtotime($ctl_story_date)!==false) {
            $posted_date = date_i18n(__("$date_formats", 'cool-timeline'), strtotime("$ctl_story_date"));
        } else {
            $ctl_story_date = trim( str_ireplace(array('am','pm'),'',$ctl_story_date) );

            $dateobj = DateTime::createFromFormat('m/d/Y H:i',$ctl_story_date ,new DateTimeZone(wp_get_timezone_string()));
        //   $posted_date = date_i18n(__("$date_formats", 'cool-timeline'), $dateobj->getTimestamp()); 
            if($dateobj){
             $posted_date = $dateobj->format(__("$date_formats", 'cool-timeline'));
            }
            }
          return  $posted_date;
       }
}


// create default number based pagination
function ctl_pro_pagination($numpages = '', $pagerange = '', $paged='') {
 if (empty($pagerange)) {
    $pagerange = 2;
    }

 if ( get_query_var('paged') ) { 
            $paged = get_query_var('paged'); 
            } elseif ( get_query_var('page') ) { 
            $paged = get_query_var('page'); 
            } else { 
            $paged = 1; 
            }
     if ($numpages == '') {

        global $wp_query;

        $numpages = $wp_query->max_num_pages;

        if(!$numpages) {
             $numpages = 1;
            }
        }

     $big = 999999999; 
     $of_lbl = __( ' of ', 'cool-timeline' ); 
    $page_lbl = __( ' Page ', 'cool-timeline' ); 
 $pagination_args = array(
    'base' => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
     'format' => '?paged=%#%',
    'total'           => $numpages,
     'current'         => $paged,
     'show_all'        => False,
    'end_size'        => 1,
    'mid_size'        => 1,
    'prev_next'       => True,
    'prev_text'       => __('&laquo;'),
    'next_text'       => __('&raquo;'),
    'type'            => 'plain',
     'add_args'        => false,
    'add_fragment'    => '' );
 $paginate_links = paginate_links($pagination_args);
 $ctl_pagi='';
    if ($paginate_links) {
        $ctl_pagi .= "<nav class='custom-pagination'>";
     $ctl_pagi .= "<span class='page-numbers page-num'> ".$page_lbl . $paged . $of_lbl . $numpages . "</span> ";
        $ctl_pagi .= $paginate_links;
         $ctl_pagi .= "</nav>";
        return $ctl_pagi;
 }

}

// get post type from url
function ctl_get_ctp() {
    global $post, $typenow, $current_screen;
 if ( $post && $post->post_type )
        return $post->post_type;
  elseif( $typenow )
        return $typenow;
 elseif( $current_screen && $current_screen->post_type )
        return $current_screen->post_type;
 elseif( isset( $_REQUEST['post_type'] ) )
        return sanitize_key( $_REQUEST['post_type'] );
  return null;
}

// grab taxonmy list
if ( ! function_exists( 'ctl_entry_taxonomies' ) ) :
  
    function ctl_entry_taxonomies() {
        $categories_list = get_the_category_list( _x( ', ', 'Used between list items, there is a space after the comma.', 'cool-timeline' ) );
        $cat_meta='';
        if ( $categories_list) {
            $cat_meta .= sprintf( '<i class="fa fa-folder-open" aria-hidden="true"></i><span class="cat-links"><span class="screen-reader-text">%1$s </span>%2$s</span>',
                _x( 'Categories', 'Used before category names.', 'cool-timeline' ),
                $categories_list
            );
        }
        return $cat_meta;
    }
endif;

// grab post types
function ctl_post_tags() {
    $tags_list = get_the_tag_list( '', _x( ', ', 'Used between list items, there is a space after the comma.', 'cool-timeline' ) );
    if ( $tags_list ) {
        return sprintf( '<span class="tags-links"><i class="fa fa-bookmark"></i><span class="screen-reader-text">%1$s </span>%2$s</span>',
            _x( 'Tags', 'Used before tag names.', 'cool-timeline' ),
            $tags_list
        );
    }
}
// check video type 
function videoType($url) {
    if (strpos($url, 'youtube') > 0) {
        return 'youtube';
    } elseif (strpos($url, 'vimeo') > 0) {
        return 'vimeo';
    } else {
        return 'unknown';
    }
} 

/** 
* @param string $url The URL
* @return string the video id extracted from url
*/

function getVimeoVideoIdFromUrl($url = '') {
   $regs = array();
   $id = '';
   if (preg_match('%^https?:\/\/(?:www\.|player\.)?vimeo.com\/(?:channels\/(?:\w+\/)?|groups\/([^\/]*)\/videos\/|album\/(\d+)\/video\/|video\/|)(\d+)(?:$|\/|\?)(?:[?]?.*)$%im', $url, $regs)) {
       $id = $regs[3];
   }
   return $id;
}

// generate video html
function clt_story_video($post_id){
  $ctl_story_media = get_post_meta($post_id, 'story_media', true);
  $ctl_video = isset($ctl_story_media['ctl_video'])?$ctl_story_media['ctl_video']:'';

  if ($ctl_video) {
    if(videoType($ctl_video)=="youtube"){
            preg_match('/[\\?\\&]v=([^\\?\\&]+)/', $ctl_video, $matches);
        if (isset($matches[1])) {
                $id = $matches[1];
            return '<div class="full-width">
            <iframe width="100%" 
            src="https://www.youtube.com/embed/' . $id . '" 
            frameborder="0" allowfullscreen></iframe></div>';
            }  
    }elseif(videoType($ctl_video)=="vimeo"){
        $video_id=getVimeoVideoIdFromUrl($ctl_video);
        return '<div class="full-width">
        <div style="padding:42.5% 0 0 0;position:relative;">
            <iframe src="https://player.vimeo.com/video/'.$video_id.'?color=0041ff&title=0&byline=0&portrait=0&badge=0"
                style="position:absolute;top:0;left:0;width:100%;height:100%;" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen>
                </iframe>
            </div>
            <script src="https://player.vimeo.com/api/player.js"></script>
        </div>';
    }else{
        return '<div class="full-width">Not Correct URL</div>'; 
    }
  }
}

// create slider from stories images
function clt_story_slideshow($post_id,$timeline_type,$ctl_options_arr,$active_design){

    //Story Media
    $ctl_story_media = get_post_meta($post_id, 'story_media', true);
    $ctl_slides = array();
    if($ctl_story_media['ctl_slide'] != ''){
        $ctl_slides = explode(',', $ctl_story_media['ctl_slide']);
    }
       
    $slides_html='';$story_slides='';
    $stories_images_link =isset($ctl_options_arr['story_media_settings']['stories_images'])?$ctl_options_arr['story_media_settings']['stories_images']:'popup';
    $ctl_slideshow = isset($ctl_options_arr['story_media_settings']['ctl_slideshow']) ? $ctl_options_arr['story_media_settings']['ctl_slideshow'] : true;
    $animation_speed = isset($ctl_options_arr['story_media_settings']['animation_speed']) ? $ctl_options_arr['story_media_settings']['animation_speed'] : 7000;    
    
    // Extra Settings
    $ctl_extra_settings = get_post_meta($post_id, 'extra_settings', true);
    $custom_link = isset($ctl_extra_settings['story_custom_link']['url'])?$ctl_extra_settings['story_custom_link']['url']:'';        
    

        if (array_filter($ctl_slides)) {
        foreach ($ctl_slides as $key => $att_index) {
            $story_full_img = wp_get_attachment_image_src($att_index, 'full');
          
            if($active_design=="design-7"){
                $slides = wp_get_attachment_image_src($att_index, 'full');
            }else{
                $slides = wp_get_attachment_image_src($att_index, 'large');
            }
         
            $s_img_cls='';

            if (is_array($slides) && $slides[0]) {
                if($active_design=="design-7"){
                    $story_slides .= '<li><img class="'.$s_img_cls.'" src="' . $slides[0]. '" class="no-lazyload skip-lazy"></li>';
                }else{
                    if($stories_images_link == "popup") {
                        $story_slides .= '<li>
                        <a  class="ctl_prettyPhoto" rel="ctl_prettyPhoto[pp_gallery-'.$post_id.']" href="'.$story_full_img[0].'" class="no-lazyload">
                        <img class="'.$s_img_cls.'" src="' . $slides[0] . '" class="no-lazyload skip-lazy"></a></li>';
                    }
                    else if($stories_images_link == "single"){
                        if(isset($custom_link)&& !empty($custom_link)){
                            $story_slides .= '<li><a target="_blank" href="'.$custom_link.'"><img class="'.$s_img_cls.'" src="' . $slides[0]. '" class="no-lazyload skip-lazy"></a></li>';
                        }
                        else{
                            $story_slides .= '<li><a target="_blank" href="'.get_the_permalink($post_id).'"><img class="'.$s_img_cls.'" src="' . $slides[0]. '" class="no-lazyload skip-lazy"></a></li>';
                        }
                    }
                    else{
                        $story_slides .= '<li><img class="'.$s_img_cls.'" src="' . $slides[0]. '" class="no-lazyload skip-lazy"></li>';
                    }
                }      
            }
          }   
       }

      if($active_design=="design-7"){
         $slides_html .= '<div class="full-width  ctl_slideshow">';
         $slides_html .= '<div 
         class="ctl_popup_slick">
         <ul data-animationSpeed="' . $animation_speed . '"  data-slideshow="' . $ctl_slideshow . '"  class="slides">';
         $slides_html .= $story_slides . '</ul></div></div>';

       }else if($timeline_type=="horizontal" && $active_design!="design-7"){
        $slides_html .= '<div class="full-width  ctl_slideshow">';
         $slides_html .= '<ul data-animationSpeed="' . $animation_speed . '"  data-slideshow="' . $ctl_slideshow . '"  class="slides">';
         $slides_html .= $story_slides . '</ul></div>';
       }
        else{
         $slides_html .= '<div class="full-width  ctl_slideshow">';
         $slides_html .= '<div 
          class="ctl_flexslider"><ul class="slides"
          data-animationSpeed="' . $animation_speed . '"  data-slideshow="' . $ctl_slideshow . '" 
          >';
         $slides_html .= $story_slides . '</ul></div></div>';
              
       }   
       return  $slides_html;
}

// grab story featured images and set custom size
function clt_story_featured_img($post_id,$ctl_options_arr){
   
    //Story Media
    $ctl_story_media = get_post_meta($post_id, 'story_media', true);
    $img_cont_size = isset($ctl_story_media['img_cont_size'])?$ctl_story_media['img_cont_size']:'';

    $ctl_extra_settings = get_post_meta($post_id, 'extra_settings', true);
    $custom_link = isset($ctl_extra_settings['story_custom_link'])?$ctl_extra_settings['story_custom_link']:'';        
 
    $img_html='';
    $stories_images_link =isset($ctl_options_arr['story_media_settings']['stories_images'])? $ctl_options_arr['story_media_settings']['stories_images']:'popup';
    $imgAlt = get_post_meta(get_post_thumbnail_id($post_id),'_wp_attachment_image_alt', true);
    $alt_text=$imgAlt?$imgAlt:get_the_title($post_id);
    

  if ($stories_images_link =="popup") {
      $img_f_url = wp_get_attachment_url(get_post_thumbnail_id($post_id));
      $story_img_link = '<a title="' . esc_attr(get_the_title($post_id)). '"  href="' . $img_f_url . '" class="ctl_prettyPhoto">';
    } else if ($stories_images_link == "single") {
        
       if(isset($custom_link['url']) && !empty($custom_link['url'])){
            $target = !empty($custom_link['target']) ? $custom_link['target'] :'_self';
            $story_img_link = '<a target="'.$target.'" title="' . esc_attr(get_the_title($post_id)) . '"  href="' . $custom_link['url']. '" class="single-page-link">';
        }else{
           $target = isset($ctl_options_arr['story_content_settings']['story_link_target'])?$ctl_options_arr['story_content_settings']['story_link_target']:'_self';
           $story_img_link = '<a target="'.$target.'" title="' . esc_attr(get_the_title($post_id)) . '"  href="' . get_the_permalink($post_id) . '" class="single-page-link">';
        
        }
    } else if ($stories_images_link == "disable_links") {
         $story_img_link = '';
         $s_l_close='';
     }
	 else if($stories_images_link =="theme-popup") {
		     $img_f_url = wp_get_attachment_url(get_post_thumbnail_id($post_id));
			$story_img_link = '<a title="' . esc_attr(get_the_title($post_id)). '"  href="' . $img_f_url . '">';
	  }
	 else {
            $s_l_close='';
             $story_img_link = '<a target="'.$target.'" title="' . esc_attr(get_the_title($post_id)) . '"  href="' . get_the_permalink($post_id) . '" class="">';
          }

    if ($img_cont_size == "small") {
    
        if ( has_post_thumbnail($post_id) ) {
         $img_html .= '<div class="pull-left">';
        $img_html.=$story_img_link;
        $img_html.=get_the_post_thumbnail( $post_id,apply_filters('cool_timeline_story_img_size','medium'), array( 'class' => 'story-img left_small','alt'=>$alt_text) );
         $img_html.='</a>';
          $img_html.='</div>';
         }   
     } else {
         if ( has_post_thumbnail($post_id) ) {
        $img_html .= '<div class="full-width">';
        $img_html.=$story_img_link;
        $img_html.=get_the_post_thumbnail( $post_id,apply_filters('cool_timeline_story_img_size','large'),array( 'class' => 'story-img','alt'=>$alt_text) );
          $img_html.='</a>';
          $img_html.='</div>';
         }
     }      
      return  $img_html;
}

 function ctl_minimal_featured_img($post_id,$img_cont_size){
    $img_html='';
    $imgAlt = get_post_meta(get_post_thumbnail_id($post_id),'_wp_attachment_image_alt', true);
    $alt_text=$imgAlt?$imgAlt:get_the_title($post_id);
    if ($img_cont_size == "small") {
        if ( has_post_thumbnail($post_id) ) {
         $img_html .= '<div class="pull-left">';
         $img_html.=get_the_post_thumbnail( $post_id, 'medium', array( 'class' => 'story-img left_small','alt'=>$alt_text) );
          $img_html.='</div>';
         }   
     } else {
         if ( has_post_thumbnail($post_id) ) {
         $img_html .= '<div class="full-width">';
         $img_html.=get_the_post_thumbnail( $post_id, 'large', array( 'class' => 'story-img','alt'=>$alt_text) );
          $img_html.='</div>';
         }
     }  
     return $img_html;    
 }
// fetch custom icon
function ctl_post_icon($post_id,$default_icon){
    $icon = ''; $extra_class='';
    if(isset($default_icon)&& !empty($default_icon)){
        if(strpos($default_icon, 'fab') ===false){
            $extra_class= 'fa';
        }
        $icon ='<i class="'.$extra_class.' '.$default_icon.'" ></i>';
    }else {
        $icon = '<i class="fa fa-clock-o"></i>';
    }
    if ( get_post_type($post_id) == 'cool_timeline' ) {

        //Story Icon
        $ctl_story_icon = get_post_meta($post_id, 'story_icon', true);
        $ctl_icon_type = isset($ctl_story_icon['story_icon_type'])?$ctl_story_icon['story_icon_type']:'fontawesome';
        
    	if($ctl_icon_type == 'fontawesome'){
     	    $ctl_fontawesome_icon = isset($ctl_story_icon['fa_field_icon'])?$ctl_story_icon['fa_field_icon']:'';
            $extra_class= '';          
            if($ctl_fontawesome_icon != ''){
                if(strpos($ctl_fontawesome_icon, 'fab') ===false){
                    $extra_class= 'fa';
                }               
                $icon ='<i class="'.$extra_class.' '.$ctl_fontawesome_icon.'" ></i>';
            }            
    	}
    	if($ctl_icon_type == 'custom_image'){
     	    $ctl_image_icon_url = isset($ctl_story_icon['story_img_icon']['url'])?$ctl_story_icon['story_img_icon']['url']:'';
            $ctl_image_icon_id = isset($ctl_story_icon['story_img_icon']['id'])?$ctl_story_icon['story_img_icon']['id']:'';
            if($ctl_image_icon_id!=''){
                return wp_get_attachment_image($ctl_image_icon_id,'thumbnail',true, array( "class" => "ctl-icon-img" ) ); 
            }
    	}
    }   
    return $icon;

}

// timeline main top title 
function ctl_main_title($ctl_options_arr, $ctl_title_text,$ttype){
    $main_title_html='';
    $ctl_title_tag = isset($ctl_options_arr['title_tag']) ? $ctl_options_arr['title_tag'] : 'H2';
     $title_visibilty =isset($ctl_options_arr['timeline_header']['display_title']) ? $ctl_options_arr['timeline_header']['display_title'] : "yes";
     if($ttype=="default_timeline"){
     if (isset($ctl_options_arr['timeline_header']['user_avatar']['id'])) {
                    $user_avatar = wp_get_attachment_image_src($ctl_options_arr['timeline_header']['user_avatar']['id'], 'ctl_avatar');
             if (isset($user_avatar[0]) && !empty($user_avatar[0])) {
                        $main_title_html.= '<div class="avatar_container row"><span title="' . $ctl_title_text . '"><img  class=" center-block img-responsive img-circle" alt="' . $ctl_title_text . '" src="' . $user_avatar[0] . '"></span></div> ';
                         }   
      }   
      } 
     if ($title_visibilty == "yes") {
                    $main_title_html.= sprintf('<%s class="timeline-main-title center-block">%s</%s>',$ctl_title_tag, $ctl_title_text, $ctl_title_tag);
                }                  
    return $main_title_html;
}

/**
 * Returns the timezone string for a site, even if it's set to a UTC offset
 *
 * Adapted from http://www.php.net/manual/en/function.timezone-name-from-abbr.php#89155
 *
 * @return string valid PHP timezone string
 */
function wp_get_timezone_string() {

  // if site timezone string exists, return it
  if ( $timezone = get_option( 'timezone_string' ) )
      return $timezone;

  // get UTC offset, if it isn't set then return UTC
  if ( 0 === ( $utc_offset = get_option( 'gmt_offset', 0 ) ) )
      return 'UTC';

  // adjust UTC offset from hours to seconds
  $utc_offset *= 3600;

  // attempt to guess the timezone string from the UTC offset
  if ( $timezone = timezone_name_from_abbr( '', $utc_offset, 0 ) ) {
      return $timezone;
  }

  // last try, guess timezone string manually
  $is_dst = date( 'I' );

  foreach ( timezone_abbreviations_list() as $abbr ) {
      foreach ( $abbr as $city ) {
          if ( $city['dst'] == $is_dst && $city['offset'] == $utc_offset )
              return $city['timezone_id'];
      }
  }
  
  // fallback to UTC
  return 'UTC';
}


/*
Create own custom timestamp for stories
*/
function ctl_generate_custom_timestamp($story_date){
     if(!empty($story_date)){
          $ctl_story_date=strtotime($story_date);
        if( $ctl_story_date!==false){
            $story_timestamp =date('YmdHi',$ctl_story_date);
          } else {
            $split_date=explode(" ",$story_date);
            if(is_array($split_date)&& count($split_date)>1){
            // grab story date
            $date_arr=explode("/",$split_date[0]);
            // convert into 24 format
            $time=$split_date[1].' '.$split_date[2];
            $converted_time= date("Hi",strtotime($time));
            if(is_array($date_arr)){
            // create custom timestamps
            $story_timestamp=$date_arr[2].$date_arr[0].$date_arr[1].$converted_time;
            }
         }
        }
        return $story_timestamp;  
     }
}


// migrate stories from old PRO version
function ctl_migrate_pro_old_stories(){
     $args = array( 
        'post_type'   => 'cool_timeline',
         'post_status'=>array('publish','future'),
         'numberposts' => -1 );
       $posts = get_posts( $args );
    if(isset($posts)&& is_array($posts) && !empty($posts))
    {
       foreach ( $posts as $post )
       {
        $ctl_story_date= get_post_meta($post->ID, 'ctl_story_date',true);
        if (!empty($ctl_story_date) ) {
        $story_timestamp= ctl_generate_custom_timestamp($ctl_story_date);
        update_post_meta($post->ID,'ctl_story_timestamp',$story_timestamp );
            }
        }
     }
}

// migrate stories from cool timeline free version
function ctl_migrate_from_free($version){
    $args = array( 
        'post_type'   => 'cool_timeline',
         'post_status'=>array('publish','future'),
         'numberposts' => -1 );
       $posts = get_posts( $args );
   
    if(isset($posts)&& is_array($posts) && !empty($posts))
    {
       foreach ( $posts as $post )
        {
              
                if($version=="old"){
                    $published_date= get_the_date('m/d/Y h:i a', $post->ID );
                  if($published_date){
                     update_post_meta($post->ID, 'ctl_story_date', $published_date);
                     $story_timestamp= ctl_generate_custom_timestamp($published_date);
                     update_post_meta($post->ID,'ctl_story_timestamp',$story_timestamp );
                    }
                }

                if(!get_post_meta($post->ID,'story_based_on',true)){
                    update_post_meta($post->ID, 'story_based_on','default');
               }
               $post_categories = get_the_terms( $post->ID, 'ctl-stories' );
               if(empty($post_categories) || is_wp_error( $post_categories ) ){
                    $term_taxonomy_ids = wp_set_object_terms($post->ID,'timeline-stories','ctl-stories', true );
               }
        }
     }

}

function ctl_set_default_value( $value, $default){

    if( isset($value) && !empty($value) ){
        return $value;
    }

    return $default;

}



function ctl_pro_get_typeo_output($settings) {
    $output    = '';
    $important ='';
    $font_family   = ( ! empty($settings['font-family'] ) ) ? $settings['font-family'] : '';
    $backup_family = ( ! empty( $settings['backup-font-family'] ) ) ? ', '. $settings['backup-font-family'] : '';
    if ( $font_family ) {
      $output .= 'font-family:'. $font_family .''. $backup_family . $important .';';
    }
    // Common font properties
    $properties = array(
      'color',
      'font-weight',
      'font-style',
      'font-variant',
      'text-align',
      'text-transform',
      'text-decoration',
    );

    foreach ( $properties as $property ) {
      if ( isset($settings[$property] ) && $settings[$property] !== '' ) {
        $output .= $property .':'.$settings[$property] . $important .';';
      }
    }

    $properties = array(
      'font-size',
      'line-height',
      'letter-spacing',
      'word-spacing',
    );

    
    $unit = ( ! empty($settings['unit'] ) ) ? $settings['unit'] : 'px';
   
    $line_height_unit = ( ! empty( $settings['line_height_unit'] ) ) ? $settings['line_height_unit'] : 'px';
   
    foreach ( $properties as $property ) {
      if ( isset($settings[$property] ) && $settings[$property] !== '' ) {
        $unit = ( $property === 'line-height' ) ? $line_height_unit : $unit;
        $output .= $property .':'. $settings[$property] . $unit . $important .';';
      }
    }
    return $output;
}