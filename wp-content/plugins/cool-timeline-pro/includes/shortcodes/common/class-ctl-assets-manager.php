<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if (!class_exists('CTL_Assets_Manager')) {

    class CTL_Assets_Manager {

    /**
     * The unique instance of the plugin.
     *
     */
    private static $instance;

    /**
     * Gets an instance of our plugin.
     *
     */
    public static function get_instance()
    {
        if (null === self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }
     /**
   * Registers our plugin with WordPress.
   */
  	public static function registers()
  	{
        add_action('wp_enqueue_scripts', array(self::$instance, 'ctl_common_assets'));

        require CTP_PLUGIN_DIR . 'includes/shortcodes/common/class-ctl-h-styles.php';
        require CTP_PLUGIN_DIR . 'includes/shortcodes/common/class-ctl-v-styles.php';
        add_action('wp_enqueue_scripts', array('CTL_V_Styles','ctl_vertical_layout_styles'));
        add_action('wp_enqueue_scripts', array('CTL_H_Styles','ctl_horizontal_layout_styles'));
        add_action('wp_head', array('CTL_V_Styles', 'ctl_navigation_styles'));
   
    }

     // load all font familys
     public static function ctl_google_fonts(){
                
            $ctl_options_arr = get_option('cool_timeline_settings');             
            $selected_fonts = array();

            if(isset($ctl_options_arr['post_content_typo']['font-family']))
            {
                $post_content_typo=$ctl_options_arr['post_content_typo'];
               if(isset($post_content_typo['type'])
                && $post_content_typo['type']=='google' )
                {
                    $selected_fonts[]=$post_content_typo['font-family'];   
                }
              
            }
          
            if(isset($ctl_options_arr['post_title_typo']['font-family']))
            {
                $post_title_typo=$ctl_options_arr['post_title_typo'];
               if(isset($post_title_typo['type'])
                && $post_title_typo['type']=='google' )
                {
                    $selected_fonts[]=$post_title_typo['font-family'];   
                }
            }
            
            if(isset($ctl_options_arr['main_title_typo']['font-family']))
            {
                $main_title_typo=$ctl_options_arr['main_title_typo'];
               if(isset($main_title_typo['type'])
                && $main_title_typo['type']=='google' )
                {
                    $selected_fonts[]=$main_title_typo['font-family'];   
                }
            }
            if(isset($ctl_options_arr['ctl_date_typo']['font-family']))
            {
                $ctl_date_typo=$ctl_options_arr['ctl_date_typo'];
               if(isset($ctl_date_typo['type'])
                && $ctl_date_typo['type']=='google' )
                {
                    $selected_fonts[]=$ctl_date_typo['font-family'];   
                }
            }
            /*
            * google fonts
            */
            // Remove any duplicates in the list
            $selected_fonts = array_unique($selected_fonts);
         
            // If it is a Google font, go ahead and call the function to enqueue it
            $gfont_arr=array();

        if(is_array($selected_fonts)){
        foreach ($selected_fonts as $font) {
                if ($font && $font != 'inherit') {
                    if ($font == 'Raleway'){
                        $font = 'Raleway:100';
					}
                    $font = str_replace(" ", "+", $font);
                    $gfont_arr[]=$font;
                }
            }
           
        if(is_array($gfont_arr)&& !empty($gfont_arr)){
            $allfonts=implode("|",$gfont_arr);  
               wp_register_style("ctl-gfonts", "https://fonts.googleapis.com/css?family=$allfonts", false, CTLPV, 'all');
            }
        }
            wp_register_style("ctl_default_fonts", "https://fonts.googleapis.com/css?family=Open+Sans:400,300,300italic,400italic,600,600italic,700,700italic,800", false, CTLPV, 'all');

        }   


            
    // register common assets for all type of timelines
   public static function ctl_common_assets(){

        wp_register_script('ctl_prettyPhoto', CTP_PLUGIN_URL . 'assets/js/jquery.prettyPhoto.js', array('jquery'), CTLPV, true);
        wp_register_script('ctl_scripts', CTP_PLUGIN_URL . 'assets/js/ctl_scripts.min.js', array('jquery'), CTLPV, true);
        wp_register_script('section-scroll-js', CTP_PLUGIN_URL . 'assets/js/jquery.section-scroll.min.js', array('jquery'), CTLPV, true);
        // load more js
        wp_register_script('ctl-ajax-load-more', CTP_PLUGIN_URL . 'assets/js/load-more.min.js', array('jquery'), CTLPV, true);


        wp_register_style('ctl_pp_css', CTP_PLUGIN_URL . 'assets/css/prettyPhoto.css', null, CTLPV, 'all');
        wp_register_style('ctl_styles', CTP_PLUGIN_URL . 'assets/css/ctl_styles.min.css', null, CTLPV, 'all');
        wp_register_style('section-scroll', CTP_PLUGIN_URL . 'assets/css/section-scroll.min.css', null, CTLPV, 'all');
    
        wp_register_style('aos-css',CTP_PLUGIN_URL. 'assets/css/aos.css', null, CTLPV, 'all');
    
        wp_register_script('aos-js', CTP_PLUGIN_URL . 'assets/js/aos.js', array('jquery'), CTLPV, true);
        wp_register_script('ctl-imagesloaded', CTP_PLUGIN_URL . 'assets/js/imagesloaded.pkgd.min.js', array('jquery'), CTLPV);
        wp_register_script('ctl-masonry', CTP_PLUGIN_URL . 'assets/js/masonry.pkgd.min.js', array('jquery'), CTLPV);
        wp_register_script('ctl-compact-js', CTP_PLUGIN_URL . 'assets/js/ctl_compact_scripts.min.js', array('jquery','ctl-masonry'), CTLPV);

            /*
            * Horizontal timeline
            */
        // register scripts
        wp_register_script('ctl_horizontal_scripts', CTP_PLUGIN_URL . 'assets/js/ctl_horizontal_scripts.min.js', array('jquery'), CTLPV, true);
        wp_register_script('ctl_horizontal_flat', CTP_PLUGIN_URL . 'assets/js/ctl_horizontal_scripts_flat.min.js', array('jquery'), CTLPV, true);
        wp_register_script('ctl_horizontal_classic', CTP_PLUGIN_URL . 'assets/js/ctl_horizontal_scripts_classic.min.js', array('jquery'), CTLPV, true);
        wp_register_script('ctl_horizontal_elegent', CTP_PLUGIN_URL . 'assets/js/ctl_horizontal_scripts_elegent.min.js', array('jquery'), CTLPV, true);
        wp_register_script('ctl_horizontal_minimal', CTP_PLUGIN_URL . 'assets/js/ctl_horizontal_scripts_minimal.min.js', array('jquery'), CTLPV, true);
        wp_register_script('ctl_horizontal_clean', CTP_PLUGIN_URL . 'assets/js/ctl_horizontal_scripts_clean.min.js', array('jquery'), CTLPV, true);
        
        // register styles
        wp_register_script('ctl-slick-js',CTP_PLUGIN_URL . 'assets/js/slick.min.js', array('jquery'), CTLPV, true);
        wp_register_style('ctl-styles-horizontal', CTP_PLUGIN_URL . 'assets/css/ctl-styles-horizontal.min.css', null, CTLPV, 'all');
        wp_register_style('ctl-styles-slick', CTP_PLUGIN_URL . 'assets/css/slick.css', null, CTLPV, 'all');
        // compact styles
        wp_register_style('ctl-compact-tm', CTP_PLUGIN_URL . 'assets/css/ctl-compact-tm.min.css',array('ctl_styles'), CTLPV, 'all');
        wp_register_style('rtl-styles', CTP_PLUGIN_URL . 'assets/css/rtl-styles.css', null, CTLPV, 'all');
        self::$instance->ctl_google_fonts();

        wp_register_style('ctl-font-awesome','https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css', null, CTLPV, 'all');
            
    }

        
    // load all common assets
    function ctl_load_global_assets(){
        
        self::$instance->ctl_common_assets(); // register styles function, fixed issue with Semplice theme.
        //  Enqueue common required assets
        wp_enqueue_script('ctl-slick-js');
        wp_enqueue_style('ctl-styles-slick');

        $ctl_options_arr = get_option('cool_timeline_settings');    
        $disable_gf = isset($ctl_options_arr['disable_GF'])?$ctl_options_arr['disable_GF']:'no';
        if( $disable_gf!="yes"){
            wp_enqueue_style('ctl-gfonts');
            wp_enqueue_style('ctl_default_fonts');
        }
    

    wp_enqueue_script('ctl_prettyPhoto');
    wp_enqueue_style('ctl_pp_css');
    wp_enqueue_style('ctl-font-awesome');
    wp_enqueue_style('ctl-font-shims','https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/v4-shims.min.css'); 

    $disable_fa = isset($ctl_options_arr['disable_FA'])?$ctl_options_arr['disable_FA']:'no';
    if($disable_fa=="yes"){
        wp_dequeue_style('ctl-font-awesome');
        wp_dequeue_style('ctl-font-shims');
    }
    if(is_rtl()){
        wp_enqueue_style('rtl-styles');
        }
    }
    /*loading assets based upon shortcode type*/
function clt_conditional_assets($active_design,$ctl_animation,$layout='default',$type=''){
    // load assets for horizontal layout 
    if($type=="horizontal"){
        // Enqueue required assets for horizontal timeline
            wp_enqueue_style('ctl-styles-horizontal');
            if($active_design=="design-2"){
            wp_enqueue_script('ctl_horizontal_flat');
            }else if($active_design=="design-3"){
            wp_enqueue_script('ctl_horizontal_classic');
            }else if($active_design=="design-4"){
            wp_enqueue_script('ctl_horizontal_elegent');
            }else if($active_design=="design-5" || $active_design=="design-6"){
            wp_enqueue_script('ctl_horizontal_clean');
            }
            else{
            
            wp_enqueue_script('ctl_horizontal_scripts');
            }
        
    }
    // load styles if vertical layout
    if(in_array($layout,array('default','compact','one-side')))
    {
        wp_enqueue_style('ctl_styles');  
        // Enqueue required assets for vertical timeline
        wp_enqueue_style('section-scroll');
        wp_enqueue_script('section-scroll-js');
        if($ctl_animation != 'none'){
            wp_enqueue_script('aos-js');
            wp_enqueue_style('aos-css');
        }
    

        wp_enqueue_script('ctl_scripts');  
        $ltr=is_rtl()?'false':'true';
    // originLeft:".$ltr."
        if($layout=="compact"){
            wp_enqueue_style('ctl-compact-tm');
            if (! wp_script_is('ctl-masonry','enqueued' )) { 
                wp_enqueue_script('ctl-imagesloaded');
                wp_enqueue_script('ctl-masonry');
                wp_enqueue_script('ctl-compact-js');
            }
        }
        wp_enqueue_script('ctl-ajax-load-more');
        
    }elseif($layout=="horizontal"){
                    // Enqueue required assets for horizontal timeline
                    wp_enqueue_style('ctl-styles-horizontal');
            
                    if($active_design=="design-2"){
                        wp_enqueue_script('ctl_horizontal_flat');
                    }else if($active_design=="design-3"){
                        wp_enqueue_script('ctl_horizontal_classic');
                    }else if($active_design=="design-4"){
                        wp_enqueue_script('ctl_horizontal_elegent');
                    }else if($active_design=="design-5" || $active_design=="design-6"){
                        wp_enqueue_script('ctl_horizontal_clean');
                    }else if($active_design=="design-7"){
                        wp_enqueue_script('ctl_horizontal_minimal');
                    }else{
                        wp_enqueue_script('ctl_horizontal_scripts');
                    }
            }
    }

                
   }
}