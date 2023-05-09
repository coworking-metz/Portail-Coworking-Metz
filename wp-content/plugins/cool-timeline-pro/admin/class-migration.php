<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
class CTL_migrations
{

    /**
     * Constructor.
     */
    public function __construct()
    {        
        // migrate old version stories       
        add_action('admin_init',array($this,'ctl_postmeta_migration'));//Post meta migration > 3.5.3
        add_action('admin_init',array($this,'ctl_settings_migration'));
    }


    // migrate stories from cool timeline free version
    function ctl_postmeta_migration($version){
        
        //return if pro migration done
        if(get_option('ctl-pro-postmeta-migration') || get_option('ctl-postmeta-migration') ){
            return;
        }
        //old free to new pro
        $free_version = (get_option('cool-free-timeline-v')?get_option('cool-free-timeline-v'):2.1);
        if(version_compare($free_version,'2.1', '>') && !get_option('ctl-postmeta-migration') ){
            return;	
        }
        //direct new pro verion
        $pro_version=get_option("cool-timelne-pro-v");
       if(version_compare($pro_version,'4.0', '>') && !get_option('ctl-postmeta-migration') ){
           return;	
          }

        $args = array( 
            'post_type'   => 'cool_timeline',
             'post_status'=>array('publish','future'),
             'numberposts' => -1 );
           $posts = get_posts( $args );

         //Story Type
         $story_type_key = array(
            'story_based_on',           
            'ctl_story_date',        
            'ctl_story_timestamp',
            'ctl_story_lbl',
            'ctl_story_lbl_2',
            'ctl_story_order'         
        ); 

        //Story Media
        $story_media_key = array(
            'story_format',           
            'ctl_video',        
            'img_cont_size',
            're_'
        );

        $story_icon_key = array(
            'fa_field_icon',           
            'use_img_icon',        
            'story_img_icon'
        );
        
        $story_extra_key = array(
            'story_custom_link',           
            'ctl_story_color' 
        );
       
        if(isset($posts)&& is_array($posts) && !empty($posts))
        {
           foreach ( $posts as $post )
            {   

                $img_as_icon =  get_post_meta($post->ID,'use_img_icon',true); 
                $story_img_icon = get_post_meta($post->ID,'story_img_icon',true);              
                foreach ( $story_icon_key as $item )
                { 
                    $item_value =  get_post_meta($post->ID,$item,true);                            
                    if($img_as_icon == 'on' && !empty($story_img_icon)){                                              
                        $array_icon_type['story_icon_type'] = 'custom_image';                        
                        if(!empty($story_img_icon) ){
                            $thumbnail_img = wp_get_attachment_image_src($story_img_icon['id'],'thumbnail');
                            $array_icon_type['story_img_icon'] = $story_img_icon;
                            $array_icon_type['story_img_icon'] += array('thumbnail' =>$thumbnail_img[0],'width'=>'843','height'=>'450');                             
                        }
                    }
                    else{                        
                        $array_icon_type['story_icon_type'] = 'fontawesome';                           
                        $array_icon_type[$item] = $item_value;
                    }
                }

                foreach ( $story_type_key as $item )
                { 
                    $item_value =  get_post_meta($post->ID,$item,true);
                    $array_story_type[$item] = $item_value;
                }

                $slideshow_ids = [];
                foreach ( $story_media_key as $item )
                { 
                    $item_value =  get_post_meta($post->ID,$item,true);
                    if($item == 're_'){
                        if(gettype($item_value) == 'array'){
                            $total_slides = count($item_value);
                            for($i=0;$i<$total_slides;$i++){
                                array_push($slideshow_ids,$item_value[$i]['ctl_slide']['id']);                               
                            }  
                            if(!empty($slideshow_ids)){
                                $slideshow_images = implode(',',$slideshow_ids);
                                $array_story_media['ctl_slide'] = $slideshow_images;
                            }
                        }else{
                            $array_story_media['ctl_slide'] = '';  
                        } 
                                              
                    }else{
                        $array_story_media[$item] = $item_value;
                    }             
                }   
              

                foreach ( $story_extra_key as $item )
                { 
                    $item_value =  get_post_meta($post->ID,$item,true);
                    if($item == 'story_custom_link'){
                        $array_extra_key[$item]['url'] = $item_value;
                    } 
                    else{
                        $array_extra_key[$item] = $item_value;
                    }
                } 
                  
                update_post_meta($post->ID, 'story_type', $array_story_type);
                update_post_meta($post->ID, 'story_media',$array_story_media);
                update_post_meta($post->ID, 'story_icon',$array_icon_type);
                update_post_meta($post->ID, 'extra_settings',$array_extra_key); 
                update_option("ctl-pro-postmeta-migration","done"); 
                update_option("ctl-postmeta-migration","done"); 
            }
        }

    }  
    
    function ctl_settings_migration()
	{
        if(!get_option('cool_timeline_options')){
            return;
        }  

		$old_settings =	get_option('cool_timeline_options');	   
	    $new_settings = $this->ctl_recursive_change_key($old_settings, array('face' => 'font-family','size'=>'font-size','weight'=>'font-weight','src'=>'url'));
	 
		update_option('cool_timeline_settings', $new_settings);
		update_option('ctl_settings_migration_status','done');
		delete_option('cool_timeline_options');
	}

    function recursive_change_key($arr, $set) {
		if (is_array($arr) && is_array($set)) {
			$newArr = array();
			foreach ($arr as $k => $v) {
				$key = array_key_exists( $k, $set) ? $set[$k] : $k;
				$newArr[$key] = is_array($v) ? $this->recursive_change_key($v, $set) : $v;
                if($key == 'font-size'){
                    				
					$newArr[$key] = str_replace("px","",$v);	
                    				
				}	
			}
		     
			return $newArr;
		}
		return $arr;	
	}
    function ctl_recursive_change_key($arr, $set) {
		if (is_array($arr) && is_array($set)) {
			$newArr = array();
            $timeline_header = array(); $navigation_settings = array(); $pagination_settings = array();
            $blog_post_settings = array(); $story_date_settings = array(); $story_content_settings = array();
            $story_media_settings = array();           
          
            $timeline_header_key = array('display_title','title_text','user_avatar');     
            $navigation_settings_key = array('enable_navigation','navigation_position');   
            $pagination_settings_key = array('enable_pagination'); 
            $blog_post_settings_key = array('post_meta');  
            $story_date_settings_key = array('year_label_visibility','disable_months','ctl_date_formats','custom_date_style','custom_date_formats');
            $story_content_settings_key = array('content_length','display_readmore','read_more_lbl','story_link_target','default_icon');
            $story_media_settings_key = array('stories_images','ctl_slideshow','animation_speed');
            $arr = $this->recursive_change_key($arr, $set);
            foreach($arr as $key=>$value){
                if(in_array($key,$timeline_header_key)){  

                   
                    if($key == 'user_avatar'){  
                        if(!empty($value)){
                            $value = $this->recursive_change_key($value, array('src'=>'url'));
                            $thumbnail_img = wp_get_attachment_image_src($value['id'],'thumbnail');
                            $value += array('thumbnail' =>$thumbnail_img[0],'width'=>'843','height'=>'450');                       
                            $timeline_header += array($key =>$value);
                        }                      
                        
                    }else{
                        $timeline_header += array($key =>$value);
                    }   
                    
                }elseif(in_array($key,$navigation_settings_key)){  
                    $navigation_settings += array($key =>$value);  
                }    
                elseif(in_array($key,$pagination_settings_key)){     
                    $pagination_settings += array($key =>$value);  
                }                   
                elseif(in_array($key,$blog_post_settings_key)){ 
                    $blog_post_settings += array($key =>$value); 
                }  
                elseif(in_array($key,$story_date_settings_key)){ 
                    $story_date_settings += array($key =>$value);   
                } 
                elseif(in_array($key,$story_content_settings_key)){  
                    $story_content_settings += array($key =>$value);  
                }
                elseif(in_array($key,$story_media_settings_key)){
                    $story_media_settings += array($key =>$value);  
                }elseif($key == 'main_title_typo'){
                    $title_alignment = isset($arr['title_alignment'])?$arr['title_alignment']:'center';
                    $value +=array('text-align' =>$title_alignment,'type' =>'google'); 
                    $newArr['main_title_typo'] = $value;
                }elseif($key == 'post_title_text_style'){
					$newArr['post_title_typo']['text-transform'] = $value;
				}elseif($key == 'background'){				    
					if(isset($value['enabled'])){					
						$newArr['timeline_background']='1';						
						$newArr['timeline_bg_color']=$value['bg_color'];						
					}
					else{						
						$newArr['timeline_background'] ='0';
					}						
				}elseif($key == 'post_title_typo'){
                    $value +=array('type' =>'google');
                    $newArr['post_title_typo'] = $value;
                }elseif($key == 'ctl_date_typo'){
                    $value +=array('type' =>'google');
                    $newArr['ctl_date_typo'] = $value;
                }elseif($key == 'post_content_typo'){
                    $value +=array('type' =>'google');
                    $newArr['post_content_typo'] = $value;
                }
                else{
                    $newArr[$key] = $value;
                }
                
            }
            
            $newArr['timeline_header'] = $timeline_header; 
            $newArr['navigation_settings'] = $navigation_settings; 
            $newArr['pagination_settings'] = $pagination_settings; 
            $newArr['blog_post_settings'] = $blog_post_settings; 
            $newArr['story_date_settings'] = $story_date_settings; 
            $newArr['story_content_settings'] = $story_content_settings; 
            $newArr['story_media_settings'] = $story_media_settings;   
			return $newArr;
		}
		return $arr;	
	}
 
	

}
new CTL_migrations();