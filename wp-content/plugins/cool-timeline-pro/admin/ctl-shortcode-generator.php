<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 *
 * This file is responsible for creating all admin settings in Timeline Builder (post)
 */
if (!defined("ABSPATH")) {
    exit('Can not load script outside of WordPress Enviornment!');
}

if (!class_exists('CTL_shortcode_generator')) {
    class CTL_shortcode_generator
    {

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
         * The Constructor
         */
        public function __construct()
        {
            // register actions
            $this->CTL_shortcode_generator();
            add_action('admin_print_styles', array($this, 'ctl_custom_shortcode_style'));

        }

        public function ctl_custom_shortcode_style()
        {
            echo '<style>
           
            span.dashicon.dashicons.dashicons-ctl-custom-icon:before {
        content:"";
        background: url(' . CTP_PLUGIN_URL . 'assets/images/timeline-icon2-32x32.png);
        background-size: contain;
        background-repeat: no-repeat;
        height: 20px;
        display: block;

        }
           #wp-content-wrap a[data-modal-id="ctl_timeline_shortcode"]:before {
        content: "";
        background: url(' . CTP_PLUGIN_URL . 'assets/images/timeline-icon-222.png);
        background-size: contain;
        background-repeat: no-repeat;
        height: 17px;
        display: inline-block;
        margin: 0px 1px -3px 0;
        width: 20px;
        }
        #wp-content-wrap a[data-modal-id="ctl_timeline_shortcode"] {
         //   background: #000;
         //   border-color: #000;        
        }
             
        #ctl-modal-ctl_timeline_shortcode .ctl-modal-inner {
            height: 500px !important;
            // overflow: auto;          
        }
        #ctl-modal-ctl_timeline_shortcode .ctl-modal-content {            
            // overflow: hidden !important; 
            height:400px !important;        
            // min-height: -webkit-fill-available;
        } 
                     
        
        </style>';
        }

        public function CTL_shortcode_generator()
        {
            $id = isset($GLOBALS['_GET']['post'])?$GLOBALS['_GET']['post']:'';
            $post_type = isset($GLOBALS['_GET']['post_type'])?$GLOBALS['_GET']['post_type']:get_post_type($id);
            if($post_type!=='page' && $post_type!=='post' && $post_type!='') { 
                return;
            }
            if (class_exists('CTL')) {
                // Set a unique slug-like ID
                $prefix = 'ctl_timeline_shortcode';              
                // Get All Post Types as List
                // Create a shortcoder
                CTL::createShortcoder($prefix, array(
                    'button_title' => 'Add Timeline',
                    'insert_title' => 'Insert shortcode',                    
                    'gutenberg' => array(
                        'title' => 'Cool Timeline Shortcode Generator',
                        'icon' => 'ctl-custom-icon',
                        'description' => 'Cool Timeline Shortcode Generator Block.',
                        'category' => 'widgets',
                        'keywords' => array('shortcode', 'ctl','timeline','cooltimeline'),
                    ),
                ));
               
                //
                // A basic shortcode
          
                CTL::createSection($prefix, array(
                    'title' => 'Story Timeline',
                    'view' => 'normal', // View model of the shortcode. `normal` `contents` `group` `repeater`
                    'shortcode' => 'cool-timeline', // Set a unique slug-like name of shortcode.
                    'fields' => array(
                        array(
                            'id' => 'based',
                            'type' => 'select',
                            'title' => 'Timeline Based On',
                            'default' =>'default',                            
                            'options' => array(
                                'default' => 'Default(Date Based)',
                                'custom' => 'Custom Order',                                
                            ),
                            'desc'=>'Show either date or custom label/text along with timeline stories.',
                            'attributes' => array(
                                'style' => 'width: 50%;',
                            ),
                        ),
                        array(
                            'id' => 'category',
                            'type' => 'select',
                            'title' => 'Category',
                            'placeholder' => 'Select a Category',                                                                                        
                            'settings'=>array(
                                'width'=>'50%'
                            ),
                            'options'=>'ctl_select_category',
                            'attributes' => array(
                                'style' => 'width: 50%;',
                            ),
                            
                        ),
                        array(
                            'id' => 'layout',
                            'type' => 'select',
                            'title' => 'Select Layout',
                            'placeholder' => 'Select Layout',                                                     
                            'default' => 'default',
                            'desc'=>'Select your timeline Layout',                            
                            'options' => array(
                                'default' => 'Vertical',
                                'horizontal' => 'Horizontal',
                                'one-side' => 'One Side Layout',                               
                                'compact' => 'Compact Layout',
                            ),
                             'attributes' => array(
                                'style' => 'width: 50%;',
                            ),
                        ),
                        array(
                            'id' => 'items',
                            'type' => 'select',
                            'title' => 'Slide To Show?',                                   
                            'default' => '', 
                            'placeholder' => 'Select number of items', 
                            'dependency' => array( 'layout', '==', 'horizontal' ),
                            'options' => array(
                                '1' => '1',
                                '2' => '2',
                                '3' => '3',
                                '4' => '4',    
                                                                  
                            ),
                            'desc'=>'Number of slide to show', 
                             'attributes' => array(
                                'style' => 'width: 50%;',
                            ),
                        ),
                        array(
                            'id' => 'autoplay',
                            'type' => 'select',
                            'title' => 'Autoplay Settings?',                                   
                            'default' => 'false',                             
                            'dependency' => array( 'layout', '==', 'horizontal' ),
                            'options' => array(
                                'true' => 'True',
                                'false' => 'False',
                            ),
                            'desc'=>'Number of slide to show', 
                             'attributes' => array(
                                'style' => 'width: 50%;',
                            ),
                        ),
                         array(
                            'id' => 'start-on',
                            'type' => 'text',
                            'title' => 'Timeline Starting from Story e.g(2)',                                                       
                            'dependency' => array( 'layout', '==', 'horizontal' ), 
                            'default' => '0',           
                            'attributes' => array(
                                'style' => 'width: 50%;',
                            ),
                        ),                       
                         array(
                            'id' => 'autoplay-speed',
                            'type' => 'text',
                            'title' => 'Slideshow Speed ?',                           
                            'dependency' => array( 'layout', '==', 'horizontal' ),   
                            'default' => '3000',        
                            'attributes' => array(
                                'style' => 'width: 50%;',
                            ),
                        ),
                         array(
                            'id' => 'compact-ele-pos',
                            'type' => 'select',
                            'title' => 'Compact Layout Date&Title positon',                                   
                            'default' => 'main-date',                             
                            'dependency' => array( 'layout', '==', 'compact' ),
                            'options' => array(
                                'main-date' => __( "On top date/label below title",'cool-timeline' ) ,
                                'main-title' => __( "On top title below date/label",'cool-timeline') 
                                  
                                                                  
                            ),
                        ),
                        array(
                            'id' => 'designs',
                            'type' => 'select',
                            'title' => 'Select Design',
                            'placeholder' => 'Select Design',                                                     
                            'default' => 'default',
                            'desc'=>'Select your timeline Layout',                            
                            'options' => array(
                                'default' => 'Default',
                                'design-2' => 'Flat Design',
                                'design-3' => 'Classic Design',                               
                                'design-4' => 'Elegant Design',
                                'design-5' => 'Clean Design',
                                'design-6' => 'Modern Design',
                                'design-7' => 'Minimal Design',
                            ),
                             'attributes' => array(
                                'style' => 'width: 50%;',
                            ),
                        ),
                         array(
                            'id' => 'skin',
                            'type' => 'select',
                            'title' => 'Select Skin',                                   
                            'default' => 'default', 
                            'dependency' => array( 'layout', '!=', 'horizontal' ),
                            'options' => array(
                                'default' => 'Default',
                                'dark' => 'Dark',
                                'light' => 'Light',                               
                            ),
                            'desc'=>'Create Light, Dark or Colorful Timeline', 
                             'attributes' => array(
                                'style' => 'width: 50%;',
                            ),
                        ),

                        array(
                            'id' => 'date-format',
                            'type' => 'select',
                            'title' => 'Select Date Formats', 
                            'default' => 'F j',                           
                             'options' => array(                                
                                'F j' => date_i18n( 'F j' ),
                                'F j Y' => date_i18n( 'F j Y' ),
                                'Y-m-d' => date_i18n( 'Y-m-d' ),
                                'm/d/Y' => date_i18n( 'm/d/Y' ),
                                'd/m/Y' => date_i18n( 'd/m/Y' ),                                
                                'F j Y g:i A' => date_i18n( 'F j Y g:i A' ),
                                'Y' => date_i18n( 'Y' ),
                                'custom' => __( 'Custom', 'cool-timeline' ),
                            ),
                            'desc'=>'Timeline Stories dates formats', 
                            'attributes' => array(
                                'style' => 'width: 50%;',
                            ),
                        ),

                         array(
                            'id' => 'pagination',
                            'type' => 'select',
                            'title' => 'Pagination Settings',
                            'default' =>'default',                            
                            'options' => array(
                                'default' => 'Default',
                                'ajax_load_more' => 'Ajax Load More',                                
                            ),
                            'desc'=>'',
                            'attributes' => array(
                                'style' => 'width: 50%;',
                            ),
                        ),
                          array(
                            'id' => 'filters',
                            'type' => 'select',
                            'title' => 'Category Filters Settings',
                            'default' =>'NO',                            
                            'options' => array(
                                'no' => 'NO',
                                'yes' => 'YES',                                
                            ),
                            'desc'=>'',
                            'attributes' => array(
                                'style' => 'width: 50%;',
                            ),
                        ),
                        array(
                            'id' => 'filter-categories',
                            'type' => 'select',
                            'title' => 'Add categories slug for filters',                           
                            'placeholder' => 'Select  Category Slug',
                            'dependency' => array( 'filters', '==', 'yes' ),
                            'chosen'      => true,
                            'multiple'    => true,
                            'desc'=>'You can select multiple slug like (stories,our-history)',                                                            
                            'settings'=>array(
                                'width'=>'50%'
                            ),
                            'options'=>'ctl_select_category_slug'
                        ),
                         array(
                            'id' => 'icons',
                            'type' => 'select',
                            'title' => 'Icons',
                            'default' =>'NO',                            
                            'options' => array(
                                'NO' => 'NO',
                                'YES' => 'YES',                                
                            ),
                            'desc'=>'Display Icons In Timeline Stories. By default Is Dot',
                            'attributes' => array(
                                'style' => 'width: 50%;',
                            ),
                        ),

                       
                        array(
                            'id' => 'animations',
                            'type' => 'select',
                            'title' => 'Animation',
                            'default' =>'none',
                            'options' => array(
                                'none' =>'none',
                                'fade' =>'fade',
                                'zoom-in' =>'zoom-in',
                                'flip-right' =>'flip-right',
                                'zoom-out' =>'zoom-out',
                                'fade-up' =>'fade-up',
                                'fade-down' =>'fade-down',
                                'fade-left' =>'fade-left',
                                'fade-right' =>'fade-right',
                                'fade-up-right' =>'fade-up-right',
                                'fade-up-left' =>'fade-up-left',
                                'fade-down-right' =>'fade-down-right',
                                'fade-down-left' =>'fade-down-left',
                                'flip-up' =>'flip-up',
                                'flip-down' =>'flip-down',
                                'flip-left' =>'flip-left',
                                'slide-up' =>'slide-up',
                                'slide-left' =>'slide-left',
                                'slide-right' =>'slide-right',
                                'zoom-in-up' =>'zoom-in-up',
                                'zoom-in-down' =>'zoom-in-down',
                                'slide-down' =>'slide-down',
                                'zoom-in-left' =>'zoom-in-left',
                                'zoom-in-right' =>'zoom-in-right',
                                'zoom-out-up' =>'zoom-out-up',
                                'zoom-out-down' =>'zoom-out-down',
                                'zoom-out-left' =>'zoom-out-left',
                                'zoom-out-right' =>'zoom-out-right',

                            ),
                            'dependency' => array( 'layout', '!=', 'horizontal' ),
                            'attributes' => array(
                                'style' => 'width: 50%;',
                            ),
                        ),
                          array(
                            'id' => 'show-posts',
                            'type' => 'text',
                            'title' => 'Display Per Page?',                           
                            'default' => '10',              
                             'desc'=>'You Can Show Pagination After These Posts In Vertical Timeline.',            
                            'attributes' => array(
                                'style' => 'width: 50%;',
                            ),
                        ),
                       
                        array(
                            'id' => 'story-content',
                            'type' => 'select',
                            'title' => 'Stories Description?',
                            'default' =>'short',
                            'options' => array(
                                'short' => 'Summary',
                                'full' => 'Full Text',                                
                            ),
                           'desc'=>'<span>Summary:- Short description<br>Full:- All content with formated text.</span>',
                            'attributes' => array(
                                'style' => 'width: 50%;',
                            ),

                        ),         
                         array(
                            'id' => 'order',
                            'type' => 'select',
                            'title' => 'Stories Order?',
                            'default' =>'DESC',                            
                            'options' => array(
                                'DESC' => 'DESC',
                                'ASC' => 'ASC',
                            ),
                            'attributes' => array(
                                'style' => 'width: 50%;',
                            ),
                             'desc'=>'<span>Timeline Stories order like:- DESC(2017-1900) , ASC(1900-2017)</span>',
                        ),             
                        

                    )
                    ));
                    //conten timeline shortcode



                     CTL::createSection($prefix, array(
                    'title' => 'Post Timeline',
                    'view' => 'normal', // View model of the shortcode. `normal` `contents` `group` `repeater`
                    'shortcode' => 'cool-content-timeline', // Set a unique slug-like name of shortcode.
                    'fields' => array(
                        
                        
                              
                        array(
                            'id' => 'layout',
                            'type' => 'select',
                            'title' => 'Select Layout',
                            'placeholder' => 'Select Layout',                                                     
                            'default' => 'default',
                            'desc'=>'Select your timeline Layout',                            
                            'options' => array(
                                'default' => 'Vertical',
                                'horizontal' => 'Horizontal',
                                'one-side' => 'One Side Layout',                               
                                'compact' => 'Compact Layout',
                            ),
                             'attributes' => array(
                                'style' => 'width: 50%;',
                            ),
                        ),
                        array(
                            'id' => 'items',
                            'type' => 'select',
                            'title' => 'Slide To Show?',                                   
                            'default' => '', 
                            'placeholder' => 'Select number of items', 
                            'dependency' => array( 'layout', '==', 'horizontal' ),
                            'options' => array(
                                '1' => '1',
                                '2' => '2',
                                '3' => '3',
                                '4' => '4',    
                                                                  
                            ),
                            'desc'=>'Number of slide to show', 
                             'attributes' => array(
                                'style' => 'width: 50%;',
                            ),
                        ),
                        array(
                            'id' => 'autoplay',
                            'type' => 'select',
                            'title' => 'Autoplay Settings?',                                   
                            'default' => 'false',                             
                            'dependency' => array( 'layout', '==', 'horizontal' ),
                            'options' => array(
                                'true' => 'True',
                                'false' => 'False',
                                  
                                                                  
                            ),
                            'desc'=>'Number of slide to show', 
                             'attributes' => array(
                                'style' => 'width: 50%;',
                            ),
                        ),
                        array(
                            'id' => 'autoplay-speed',
                            'type' => 'text',
                            'title' => 'Slideshow Speed ?',                           
                            'dependency' => array( 'layout', '==', 'horizontal' ),   
                            'default' => '3000',        
                            'attributes' => array(
                                'style' => 'width: 50%;',
                            ),
                        ),
                         array(
                            'id' => 'start-on',
                            'type' => 'text',
                            'title' => 'Timeline Starting from Story e.g(2)',                                                       
                            'dependency' => array( 'layout', '==', 'horizontal' ),           
                            'attributes' => array(
                                'style' => 'width: 50%;',
                            ),
                        ),
                        
                        //  array(
                        //     'id' => 'compact-ele-pos',
                        //     'type' => 'select',
                        //     'title' => 'Compact Layout Date&Title positon',                                   
                        //     'default' => 'main-date',                             
                        //     'dependency' => array( 'layout', '==', 'compact' ),
                        //     'options' => array(
                        //         'main-date' => __( "On top date/label below title",'cool-timeline' ) ,
                        //         'main-title' => __( "On top title below date/label",'cool-timeline') 
                                  
                                                                  
                        //     ),
                        // ),
                        array(
                            'id' => 'designs',
                            'type' => 'select',
                            'title' => 'Select Design',
                            'placeholder' => 'Select Design',                                                     
                            'default' => 'default',
                            'desc'=>'Select your timeline Layout',                            
                            'options' => array(
                                'default' => 'Default',
                                'design-2' => 'Flat Design',
                                'design-3' => 'Classic Design',                               
                                'design-4' => 'Elegant Design',
                                'design-5' => 'Clean Design',
                                'design-6' => 'Modern Design',                                
                            ),
                             'attributes' => array(
                                'style' => 'width: 50%;',
                            ),
                        ),
                         array(
                            'id' => 'skin',
                            'type' => 'select',
                            'title' => 'Select Skin',                                   
                            'default' => 'default', 
                            'dependency' => array( 'layout', '!=', 'horizontal' ),
                            'options' => array(
                                'default' => 'Default',
                                'dark' => 'Dark',
                                'light' => 'Light',                               
                            ),
                            'desc'=>'Create Light, Dark or Colorful Timeline', 
                             'attributes' => array(
                                'style' => 'width: 50%;',
                            ),
                        ),
                        array(
                            'id' => 'post-type',
                            'type' => 'select',
                            'title' => 'Select Post Type',                            
                            'default' => 'post',        
                            'attributes' => array(
                                'style' => 'width: 50%;',
                            ),
                            'options'     => 'post_types',
                        
                            'desc'=>'Dont Change This If You Are Creating Blog Posts Timeline or Define Content Type Of Your Timeline Like:- Posts',
                        ),

                        array(
                            'id' => 'taxonomy',
                            'type' => 'select',
                            'title' => 'Taxonomy Name',                            
                            'default' => 'category',        
                            'attributes' => array(
                                'style' => 'width: 50%;',
                            ),
                            'options'     => 'ctl_get_all_texonomy',
                            'desc'=>'Dont Change This If You Are Creating Blog Posts Timeline or Define Content Type Taxonomy.',
                        ),
                        array(
                            'id' => 'post-category',
                            'type' => 'select',
                            'title' => 'Specific category(s) (Add category(s) slug - comma separated)',                            
                            'default' => '',        
                            'placeholder' =>'Select Categorys' ,                            
                            'chosen'      => true,
                            'multiple'    => true,
                            'options'     => 'get_all_cate',
                            'settings'=>array(
                                'width' => '50%',
                            ),
                            
                            'desc'=>'Show Category Specific Blog Posts. Like For cooltimeline.com/category/fb-history/ it will be fb-history',
                        ),
                        array(
                            'id' => 'tags',
                            'type' => 'select',
                            'title' => 'Specific tags(add tags slug)',
                            'placeholder' =>'Select tags' ,                          
                            'default' => '',        
                            'chosen'      => true,
                            'multiple'    => true,
                            'settings'=>array(
                                'width' => '50%',
                            ),
                            'options'     => 'tags',
                            'desc'=>'Show Tag Specific Blog Posts. Like For cooltimeline.com/tag/fb-history/ it will be fb-history.',
                        ),
                        array(
                            'id' => 'date-format',
                            'type' => 'select',
                            'title' => 'Select Date Formats', 
                            'default' => 'F j',                           
                             'options' => array(
                                'F j' => date_i18n( 'F j' ),
                                'F j Y' => date_i18n( 'F j Y' ),
                                'Y-m-d' => date_i18n( 'Y-m-d' ),
                                'm/d/Y' => date_i18n( 'm/d/Y' ),
                                'd/m/Y' => date_i18n( 'd/m/Y' ),                                
                                'F j Y g:i A' => date_i18n( 'F j Y g:i A' ),
                                'Y' => date_i18n( 'Y' ),
                                'custom' => __( 'Custom', 'cool-timeline' ),
                            ),
                            'desc'=>'Timeline Stories dates formats', 
                            'attributes' => array(
                                'style' => 'width: 50%;',
                            ),
                        ),
                         array(
                            'id' => 'based',
                            'type' => 'select',
                            'title' => 'Timeline Based On',
                            'default' =>'default',                            
                            'options' => array(
                                'default' => 'Default(Date Based)',
                                'custom' => 'Custom Order',                                
                            ),
                            'desc'=>'Show either date or custom label/text along with timeline stories.',
                            'attributes' => array(
                                'style' => 'width: 50%;',
                            ),
                        ),
                         array(
                            'id' => 'pagination',
                            'type' => 'select',
                            'title' => 'Pagination Settings',
                            'default' =>'default',                            
                            'options' => array(
                                'default' => 'Default',
                                'ajax_load_more' => 'Ajax Load More',                                
                            ),
                            'desc'=>'',
                            'attributes' => array(
                                'style' => 'width: 50%;',
                            ),
                        ),
                          array(
                            'id' => 'filters',
                            'type' => 'select',
                            'title' => 'Category Filters Settings',
                            'default' =>'NO',                            
                            'options' => array(
                                'no' => 'NO',
                                'yes' => 'YES',                                
                            ),
                            'desc'=>'',
                            'attributes' => array(
                                'style' => 'width: 50%;',
                            ),
                        ),
                        array(
                            'id' => 'filter-categories',
                            'type' => 'select',
                            'title' => 'Add categories slug for filters',                           
                             'default' =>'',             
                             'dependency' => array( 'filters', '==', 'yes' ),                                              
                            'placeholder' =>'Select Categorys' ,                            
                            'chosen'      => true,
                            'multiple'    => true,
                            'options'     => 'ctl_get_post_cat',
                            'settings'=>array(
                                'width' => '50%',
                            ),
                            'desc'=>'eg(stories,our-history)',
                        ),
                         array(
                            'id' => 'icons',
                            'type' => 'select',
                            'title' => 'Icons',
                            'default' =>'NO',                            
                            'options' => array(
                                'NO' => 'NO',
                                'YES' => 'YES',                                
                            ),
                            'desc'=>'Display Icons In Timeline Stories. By default Is Dot',
                            'attributes' => array(
                                'style' => 'width: 50%;',
                            ),
                        ),

                       
                        array(
                            'id' => 'animation',
                            'type' => 'select',
                            'title' => 'Animation',
                            'default' =>'none',
                            'options' => array(
                                'none' =>'none',
                                'fade' =>'fade',
                                'zoom-in' =>'zoom-in',
                                'flip-right' =>'flip-right',
                                'zoom-out' =>'zoom-out',
                                'fade-up' =>'fade-up',
                                'fade-down' =>'fade-down',
                                'fade-left' =>'fade-left',
                                'fade-right' =>'fade-right',
                                'fade-up-right' =>'fade-up-right',
                                'fade-up-left' =>'fade-up-left',
                                'fade-down-right' =>'fade-down-right',
                                'fade-down-left' =>'fade-down-left',
                                'flip-up' =>'flip-up',
                                'flip-down' =>'flip-down',
                                'flip-left' =>'flip-left',
                                'slide-up' =>'slide-up',
                                'slide-left' =>'slide-left',
                                'slide-right' =>'slide-right',
                                'zoom-in-up' =>'zoom-in-up',
                                'zoom-in-down' =>'zoom-in-down',
                                'slide-down' =>'slide-down',
                                'zoom-in-left' =>'zoom-in-left',
                                'zoom-in-right' =>'zoom-in-right',
                                'zoom-out-up' =>'zoom-out-up',
                                'zoom-out-down' =>'zoom-out-down',
                                'zoom-out-left' =>'zoom-out-left',
                                'zoom-out-right' =>'zoom-out-right',

                            ),
                            'dependency' => array( 'layout', '!=', 'horizontal' ),
                            'attributes' => array(
                                'style' => 'width: 50%;',
                            ),
                        ),
                          array(
                            'id' => 'show-posts',
                            'type' => 'text',
                            'title' => 'Display Per Page?',                           
                            'default' => '10',              
                             'desc'=>'You Can Show Pagination After These Posts In Vertical Timeline.',            
                            'attributes' => array(
                                'style' => 'width: 50%;',
                            ),
                        ),
                       
                        array(
                            'id' => 'story-content',
                            'type' => 'select',
                            'title' => 'Stories Description?',
                            'default' =>'short',
                            'options' => array(
                                'short' => 'Summary',
                                'full' => 'Full Text',                                
                            ),
                           'desc'=>'<span>Summary:- Short description<br>Full:- All content with formated text.</span>',
                            'attributes' => array(
                                'style' => 'width: 50%;',
                            ),

                        ),         
                         array(
                            'id' => 'order',
                            'type' => 'select',
                            'title' => 'Stories Order?',
                            'default' =>'DESC',                            
                            'options' => array(
                                'DESC' => 'DESC',
                                'ASC' => 'ASC',
                            ),
                            'attributes' => array(
                                'style' => 'width: 50%;',
                            ),
                             'desc'=>'<span>Timeline Stories order like:- DESC(2017-1900) , ASC(1900-2017)</span>',
                        ),             
                        

                    )
                    ));
              

            }
        

             /**
     * Fetch all timeline items for shortcode builder options
     * 
     * @return array $ids An array of timeline item's ID & title 
     */    

     function get_all_tagss(){
                                    $args = array(
                        'public' => true,
                    );
                    $posts = [];
                    $i=1;
                    foreach (get_taxonomies($args, 'names') as $post_type) {
                        
                        $posts[$post_type] = get_tags(array(
                            'taxonomy' => $post_type,
                            'hide_empty' => false,
                        ));
                           
                    }

                    $ctl_categories = [];
                    if (!empty($posts) || !is_wp_error($posts)) {
                        foreach ($posts as $key => $term) {
                         //   $ctl_categories[$i]=!empty($term[0]->taxonomy)?$term[0]->taxonomy .' (Category)':"";
                            foreach ($term as $keys => $value) {
                                $ctl_categories[$value->slug] = $value->name;

                            }
                        //    $i++;

                        }
                        

                    }

                return $ctl_categories;

            }
            function get_all_cate(){
                                    $args = array(
                        'public' => true,
                    );
                    $posts = [];
                    $i=1;
                    foreach (get_taxonomies($args, 'names') as $post_type) {
                        
                        $posts[$post_type] = get_terms(array(
                            'taxonomy' => $post_type,
                            'hide_empty' => false,
                        ));
                           
                    }

                    $ctl_categories = [];
                    if (!empty($posts) || !is_wp_error($posts)) {
                        foreach ($posts as $key => $term) {
                         //   $ctl_categories[$i]=!empty($term[0]->taxonomy)?$term[0]->taxonomy .' (Category)':"";
                            foreach ($term as $keys => $value) {
                                $ctl_categories[$value->slug] = $value->name;

                            }
                        //    $i++;

                        }
                        

                    }

                return $ctl_categories;

            }
     function ctl_get_all_texonomy(){
         $args = array(
        'public' => true,    
        );
        $posts=[];
        foreach ( get_taxonomies( $args, 'names' ) as $post_type ) {
            if ($post_type == "post_tag" || $post_type == "post_format") {

            }
            else{

             $posts[$post_type] = $post_type;
            }

         }
         return $posts;
     }
    
     function ctl_get_all_post(){
         $args = array(
        'public' => true,    
        );
        $posts=[];
        foreach ( get_post_types( $args, 'names' ) as $post_type ) {
        $posts[$post_type] = $post_type;

         }
         return $posts;
     }
     
    function ctl_select_category(){

        $terms = get_terms(array(
        'taxonomy' => 'ctl-stories',
        'hide_empty' => false,
        ));
        $ctl_categories=array();
		$ctl_categories[''] = __('All Categories','cool-timeline');

        if (!empty($terms) || !is_wp_error($terms)) {
            foreach ($terms as $term) {
                $ctl_categories[$term->slug] =$term->name ;
            }
        }

        
        return $ctl_categories;

    }
        function ctl_select_category_slug(){

        $terms = get_terms(array(
        'taxonomy' => 'ctl-stories',
        'hide_empty' => false,
        ));
        $ctl_categories=array();		

        if (!empty($terms) || !is_wp_error($terms)) {
            foreach ($terms as $term) {
                $ctl_categories[$term->slug] =$term->name ;
            }
        }

        
        return $ctl_categories;

    }

    function ctl_get_post_cat(){

        $terms = get_terms(array(
        'taxonomy' => 'category',
        'hide_empty' => false,
        ));
        $ctl_categories=array();		

        if (!empty($terms) || !is_wp_error($terms)) {
            foreach ($terms as $term) {
                $ctl_categories[$term->slug] =$term->name ;
            }
        }

        
        return $ctl_categories;

    }
    function ctl_get_post_tags(){

        $tags = get_tags(array(
            'taxonomy' => 'category',
            'hide_empty' => false,
        ));
      $tag_list=[];
        foreach ($tags as $tag) {
            $tag_list[$tag->name]=$tag->name;
           
        }


        
        return $tag_list;

    }
    
        }

    }

}

new CTL_shortcode_generator();
