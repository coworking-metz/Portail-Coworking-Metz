<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Control core classes for avoid errors
if( class_exists( 'CTL' ) ) {

    //
    // Set a unique slug-like ID
    $prefix = 'cool_timeline_settings';
  
    //
    // Create options
    CTL::createOptions( $prefix, array(    
      'framework_title' =>__('Cool Timeline Pro Settings','cool-timeline1'),
      'menu_title' => __('Timeline Settings','cool-timeline1'),
      'menu_slug'  => 'cool_timeline_settings',
      'menu_type' =>'submenu',
      'menu_parent' => 'cool-plugins-timeline-addon',
      'nav'=>'inline', 
      'menu_icon'=>CTP_PLUGIN_URL.'assets/images/cool-timeline-icon.svg',    
      'menu_position' => 6,
      'show_reset_section'=>false,
      'show_reset_all'=>false,	
      'show_bar_menu'=>false		
    ) );
  
    //
    // Create a section
    CTL::createSection( $prefix, array(
      'title'  => __('General Settings','cool-timeline1'),
      'fields' => array(
  
       

        // Create a Fieldset
        array(
          'id'     => 'timeline_header',
          'type'   => 'fieldset',
          'title'  => 'Content Before Timeline',
          'fields' => array(
            array(
              'id'         => 'display_title',
              'type'       => 'radio',
              'title'      => __('Timeline Title ?','cool-timeline1'),
              'options'    => array(
                'yes' => __('Yes','cool-timeline1'),
                'no' => __('No','cool-timeline1'),
              ),
              'desc'  => __('Timeline Heading:- Title above the Timeline','cool-timeline1'),
              'inline'    => true,
              'default'    => 'yes',                   
            ),
    
            array(
              'id'    => 'title_text',
              'type'  => 'text',
              'title' => __('Timeline Title(Default)','cool-timeline1'),
              'default'=>__('Cool Timeline', 'cool-timeline1'),
              'dependency' => array( 'display_title', '==', 'yes' )                 
            ),
           
            array(
              'id'    => 'user_avatar',          
              'title' => __('Timeline Image', 'cool-timeline1'), 
              'type'  => 'media',          
              'library' => 'image',
              'url' => true,
              'preview' => true,
              'desc'  => __('Image above the Timeline','cool-timeline1'),
            ),              
          ),
        ), //End Fieldset

        // Create a Fieldset
        array(
          'id'     => 'navigation_settings',
          'type'   => 'fieldset',
          'title'  => 'Year Navigation',
          'fields' => array(
            array(
              'id'         => 'enable_navigation',
              'type'       => 'radio',
              'title'      => __('Enable Scrolling  Navigation ?','cool-timeline1'),
              'options'    => array(
                'yes' => __('Yes','cool-timeline1'),
                'no' => __('No','cool-timeline1'),
              ),
              'inline'    => true,
              'default'    => 'yes'        
            ),
    
            array(
              'id'         => 'navigation_position',
              'type'       => 'radio',
              'title'      => __('Scrolling Navigation Position?','cool-timeline1'),
              'options'    => array(
                'left' => __('Left Side', 'cool-timeline1'),
                'right' => __('Right Side', 'cool-timeline1'),
                'bottom' => __('Bottom Fixed', 'cool-timeline1'),
              ),
              'inline'    => true,
              'default'    => 'right',
              'dependency' => array( 'enable_navigation', '==', 'yes' ),        
            ),    
            
          ),
        ), //End Fieldset

        // Create a Fieldset
        array(
          'id'     => 'pagination_settings',
          'type'   => 'fieldset',
          'title'  => 'Pagination',
          'fields' => array(
            array(
              'id'         => 'enable_pagination',
              'type'       => 'radio',
              'title'      => __('Enable Pagination?','cool-timeline1'),
              'options'    => array(
                'yes' => __('Yes','cool-timeline1'),
                'no' => __('No','cool-timeline1'),
              ),
              'inline'    => true,
              'default'    => 'yes',
              'desc' => __('Pagination settings added in shortcode Generator in version 2.4', 'cool-timeline')        
            )
          ),
        ), //End Fieldset

        

        // Create a Fieldset
        array(
          'id'     => 'blog_post_settings',
          'type'   => 'fieldset',
          'title'  => 'Content Timeline(Blog Posts)',
          'fields' => array(
            array(
              'id'         => 'post_meta',
              'type'       => 'radio',
              'title'      => __('Display Post Meta (Categries,Tags)?','cool-timeline1'),
              'options'    => array(
                'yes' => __('Yes','cool-timeline1'),
                'no' => __('No','cool-timeline1'),
              ),
              'inline'    => true,
              'default'    => 'yes'        
            ), 
          ),
        ), //End Fieldset

        

      )
    ) );
  
    
    // Create a section
    CTL::createSection( $prefix, array(
      'title'  => __('Style Settings','cool-timeline1'),
      'fields' => array(
  
        array(
          'id'    => 'first_story_position',
          'type'  => 'button_set',
          'title' => __('Vertical Timeline Stories Starts From','cool-timeline1'),
          'desc' => __('Not for Compact and Horizontal layout','cool-timeline1'),
          'options'    => array(
            'left'  => __('Left','cool-timeline1'),
            'right' => __('Right','cool-timeline1'),
          ),
          'default' => 'right',
        ),

        array(
          'id'    => 'timeline_background',
          'type'  => 'switcher',
          'title' => __('Timeline Background','cool-timeline1'),
          'text_on'  => __('Yes','cool-timeline1'),
          'text_off' => __('No','cool-timeline1'),
          'text_width' => 100,
          'default'    => false,
        ),

        array(
          'id'      => 'timeline_bg_color',
          'type'    => 'color',
          'title'   => __('Timeline Background Color','cool-timeline1'),
          'default' => '#ffbc00',
          'dependency' => array( 'timeline_background', '==', 'true' )
        ),

        array(
          'id'      => 'content_bg_color',
          'type'    => 'color',
          'title'   => __('Story Background Color','cool-timeline1'),
          'default' => '#ffffff'
        ),

        array(
          'id'      => 'content_color',
          'type'    => 'color',
          'title'   => __('Content Font Color','cool-timeline1'),
          'default' => '#666666'
        ),

        array(
          'id'      => 'title_color',
          'type'    => 'color',
          'title'   => __('Story Title Color','cool-timeline1'),
          'default' => '#ffffff'
        ),
        
        array(
          'id'      => 'circle_border_color',
          'type'    => 'color',
          'title'   => __('Circle Color','cool-timeline1'),
          'default' => '#38aab7'
        ),

        array(
          'id'      => 'line_color',
          'type'    => 'color',
          'title'   => __('Line Color','cool-timeline1'),
          'default' => '#025149'
        ),

        array(
          'id'      => 'first_post',
          'type'    => 'color',
          'title'   => __('First Color','cool-timeline1'),
          'default' => '#02C5BE'
        ),

        array(
          'id'      => 'second_post',
          'type'    => 'color',
          'title'   => __('Second Color','cool-timeline1'),
          'default' => '#F12945'
        ),      
        
        array(
          'id'         => 'custom_date_color',
          'type'       => 'radio',
          'title'      => __('Enable custom date color','cool-timeline1'),
          'options'    => array(
            'yes' => __('Yes','cool-timeline1'),
            'no' => __('No(Default style)','cool-timeline1'),
          ),
          'inline'    => true,
          'default'    => 'no'        
        ),

        array(
          'id'      => 'ctl_date_color',
          'type'    => 'color',
          'title'   => __('Stories date color','cool-timeline1'),
          'default' => '#000000',
          'dependency' => array( 'custom_date_color', '==', 'yes' )
        ),

  
      )
    ) );
  
    // Create a section
    CTL::createSection( $prefix, array(
      'title'  => __('Typography Setings','cool-timeline1'),
      'fields' => array(       
  
        array(
          'id'          => 'title_tag',
          'type'        => 'select',
          'title'       => __('Timeline Title Heading Tag','cool-timeline1'),
         // 'placeholder' => __('Select an option','cool-timeline1'),
          'options'     => array(
              'h1' => __('H1','cool-timeline1'),
        			'h2' => __('H2','cool-timeline1'),
        			'h3' => __('H3','cool-timeline1'),
        			'h4' => __('H4','cool-timeline1'),
        			'h5' => __('H5','cool-timeline1'),
        			'h6' => __('H6','cool-timeline1'),
          ),
          'default'     => 'h2'
        ),
        
        array(
          'id'      => 'main_title_typo',
          'type'    => 'typography',
          'title'   => __('Timeline Title','cool-timeline1'),
          'default' => array(
            'font-family' => 'Maven Pro',
            'font-size'   => '22',
            'line-height' => '',
            'unit'        => 'px',
            'type'        => 'google',
            'text-align'  => 'center',    
            'font-weight' => '700',   
          ),
          'color' => false,
        ),
    
        array(
          'id'      => 'ctl_date_typo',
          'type'    => 'typography',
          'title'   => __('Story Date','cool-timeline1'),
          'default' => array(
            'font-family' => 'Maven Pro',
            'font-size'   => '21',
            'line-height' => '',
            'unit'        => 'px',
            'type'        => 'google',
            'text-align'  => 'center',
            'font-weight' => '700',
          ),
          'text_align'=> false,         
          'color' => false,
        ),

        array(
          'id'      => 'post_title_typo',
          'type'    => 'typography',
          'title'   => __('Story Title','cool-timeline1'),
          'default' => array(
            'font-family' => 'Maven Pro',
            'font-size'   => '20',
            'line-height' => '',
            'unit'        => 'px',
            'type'        => 'google',
            'font-weight' => '700',
          ),
          'color' => false,
        ),

        // A textarea field      

        array(
          'id'      => 'post_content_typo',
          'type'    => 'typography',
          'title'   => __('Story Content','cool-timeline1'),
          'default' => array(
            'font-family' => 'Maven Pro',
            'font-size'   => '16',
            'line-height' => '',
            'unit'        => 'px',
            'type'        => 'google',
          ),
          'color' => false,
        ),         
  
      )
    ) );


    // Create a section
    CTL::createSection( $prefix, array(
      'title'  => __('Stories Settings','cool-timeline1'),
      'fields' => array(
        array(
          'id'    => 'post_type_slug',
          'type'  => 'text',
          'title' => __('Custom slug of timeline stories','cool-timeline1'),
          'default'=>'', 
          'desc' => __('Remember to save the permalink again in settings -> Permalinks.','cool-timeline1'),                   
        ),
        
        

        array(
          'id'     => 'story_date_settings',
          'type'   => 'fieldset',
          'title'  => 'Story Date',
          'fields' => array(

              array(
                
                'id'    => 'year_label_visibility',
                'type'  => 'switcher',
                'title' => __('Year Label','cool-timeline1'),
                'text_on'  => __('Show','cool-timeline1'),
                'text_off' => __('Hide','cool-timeline1'),
                'text_width' => 100,
                'default'    => true,
                'desc'  => __('Only for Vertical and One sided layout','cool-timeline1'),
              ),

              array(
                'id'         => 'disable_months',
                'type'       => 'radio',
                'title'      => __('Hide Stories Dates?','cool-timeline1'),
                'options'    => array(
                  'yes' => __('Yes','cool-timeline1'),
                  'no' => __('No','cool-timeline1'),
                ),
                'inline'    => true,
                'default'    => 'no'        
              ),      
              array(
                'id'         => 'ctl_date_formats',
                'type'       => 'radio',
                'title'      => __('Stories Date Format','cool-timeline1'),
                'options'    => array(
                    'M d' => __('M d', 'cool-timeline'),
                    'F j, Y' => __('F j, Y', 'cool-timeline'), 
                    'Y-m-d' => __('Y-m-d', 'cool-timeline'),
                    'm/d/Y' => __('m/d/Y', 'cool-timeline'), 
                    'd/m/Y' => __('d/m/Y', 'cool-timeline'),
                    'F j' => __('F j', 'cool-timeline'), 
                    'F j Y g:i A' => __('F j Y g:i A', 'cool-timeline'), 
                    'Y' => __('Y', 'cool-timeline')
                ),
                'inline'    => false,
                'default'    => 'M d',
                'dependency' => array( 'disable_months', '==', 'no' )                 
              ),    
              array(
                'id'         => 'custom_date_style',
                'type'       => 'radio',
                'title'      => __('Enable custom date Format','cool-timeline1'),
                'options'    => array(
                  'yes' => __('Yes','cool-timeline1'),
                  'no' => __('No(Default style)','cool-timeline1'),
                ),
                'inline'    => true,
                'default'    => 'no'        
              ),      
              array(
                'id'    => 'custom_date_formats',
                'type'  => 'text',
                'title' => __('Custom date format','cool-timeline1'),
                'default' => '', 
                'desc' => __('Stories date formats   e.g  D,M,Y <a  target="_blank" href="http://php.net/manual/en/function.date.php">Click here to view more</a>', 'cool-timeline1'),                 
                'dependency' => array( 'custom_date_style', '==', 'yes' )
              ),
            ),
        ), //End Fieldset

         // Create a Fieldset
        array(
          'id'     => 'story_content_settings',
          'type'   => 'fieldset',
          'title'  => 'Story Content',
          'fields' => array(
              array(
                'id'    => 'content_length',
                'type'  => 'text',
                'title' => __('Content Length','cool-timeline1'),
                'default'=>'50',
                'desc'  => __('Please enter no of words','cool-timeline1'),
              ),
              array(
                'id'         => 'display_readmore',
                'type'       => 'radio',
                'title'      => __('Display read more?','cool-timeline1'),
                'options'    => array(
                  'yes' => __('Yes','cool-timeline1'),
                  'no' => __('No','cool-timeline1'),
                ),
                'desc'=>  __('It will also disable link from story title.','cool-timeline1'),
                'inline'    => true,
                'default'    => 'yes'        
              ),      
              array(
                'id'    => 'read_more_lbl',
                'type'  => 'text',
                'title' => __('Stories Read more Text','cool-timeline1'),
                'default'=>'', 
                'dependency' => array( 'display_readmore', '==', 'yes' )         
              ),      
              array(
                'id'         => 'story_link_target',
                'type'       => 'radio',
                'title'      => __('Open read more link in?','cool-timeline1'),
                'options'    => array( 
                  '_self' => __('Same Tab','cool-timeline1'),
                  '_blank' => __('New Tab','cool-timeline1'),
                ),
                'inline'    => true,
                'default'    => '_self',
                'dependency' => array( 'display_readmore', '==', 'yes' )          
              ),

              array(
                'id'    => 'default_icon',
                'type'  => 'icon',
                'title' => __('Stories Default Icon','cool-timeline1'),
                'std' => '',
              //  'desc' => __('Please add stories default  icon class from here <a target="_blank" href="http://fontawesome.io/icons">Font Awesome</a>', 'cool-timeline')         
              ),

            ),
          ), //End Fieldset

        // Create a Fieldset
        array(
          'id'     => 'story_media_settings',
          'type'   => 'fieldset',
          'title'  => 'Story Media',
          'fields' => array(

            // Create a Fieldset
            array(
              'id'         => 'stories_images',
              'type'       => 'radio',
              'title'      => __('Stories Images?','cool-timeline1'),
              'options'    => array(
                  'popup' => 'In Popup(CT Lightbox)',
                  'theme-popup' => 'In Popup(Theme Lightbox)',
                  'single' => 'Story detail link',
                  'disable_links'=>'Disable links'
              ),
              'inline'    => true,
              'default'    => 'popup',
              'desc' => __('*Choose theme lightbox if your theme supports an image lightbox.', 'cool-timeline')   
            ),
    
            array(
              'id'         => 'ctl_slideshow',
              'type'       => 'radio',
              'title'      => __('Stories Slideshow ?','cool-timeline1'),
              'options'    => array(
                  true => 'Enable',
                  false => 'Disable'
              ),
              'inline'    => true,
              'default'    => 'true',
              'desc' => __('*Choose theme lightbox if your theme supports an image lightbox.', 'cool-timeline')   
            ),
    
            array(
              'id'    => 'animation_speed',
              'type'  => 'text',
              'title' => __('Slide Show Speed(For Image Slideshow in Vertical Timeline) ?','cool-timeline1'),
              'default' => '5000', 
              'desc' => __('Enter the speed in milliseconds 1000 = 1 second', 'cool-timeline'),
              'dependency' => array( 'ctl_slideshow', '==', 'true' )                 
            ),
    
          ),
        ), //End Fieldset

      )
    ));
    //End Section
 

     // Create a section
    CTL::createSection( $prefix, array(
      'title'  => __('Extra Settings','cool-timeline1'),
      'fields' => array(
        
        array(
          'id'         => 'disable_FA',
          'type'       => 'radio',
          'title'      => __('Disable Font Awesome CSS?','cool-timeline1'),
          'options'    => array(
            'yes' => __('Yes','cool-timeline1'),
            'no' => __('No','cool-timeline1'),
          ),
          'inline'    => true,
          'default'    => 'no',
          'desc' => __('Remove Font Awesome icons CSS from all pages','cool-timeline')                   
        ),

        array(
          'id'         => 'disable_GF',
          'type'       => 'radio',
          'title'      => __('Disable Google Font?','cool-timeline1'),
          'options'    => array(
            'yes' => __('Yes','cool-timeline1'),
            'no' => __('No','cool-timeline1'),
          ),
          'inline'    => true,
          'default'    => 'no',
          'desc' => __('Remove google fonts CSS from all pages','cool-timeline')                   
        ),

        array(
          'id'       => 'custom_styles',
          'type'     => 'code_editor',
          'title'    => __('Custom Styles','cool-timeline1'),
          'settings' => array(
            'theme'  => 'mbo',
            'mode'   => 'css',
          ),         
        ),

      )

    ));//End Section



    // Create a section
    CTL::createSection( $prefix, array(
      'title'  => __('Timeline Shortcodes','cool-timeline1'),
      'fields' => array(
  
        // A Subheading
        array(
          'type'    => 'subheading',
          'content' => __('Timeline Shortcodes.','cool-timeline1'),
        ),

        array(
          'id'=>'timeline_display',
          'type'=>'content',
          'content'=> '<h3>'.__('Default timeline.','cool-timeline').'</h3><code><strong>[cool-timeline layout="default" designs="default" skin="default" category="{add here category-slug}" show-posts="10" order="DESC" icons="NO" animations="bounceInUp" date-format="default" story-content="short" based="default" compact-ele-pos="main-date" pagination="default" filters="no"] </strong> </code></br></br>          
                      <h3>'.__('Shortcode for multiple timeline (category based timeline)','cool-timeline').'</h3><code><strong>[cool-timeline layout="default" designs="default" skin="default" category="{add here category-slug}" show-posts="10" order="DESC" icons="NO" animations="bounceInUp" date-format="default" story-content="short" based="default" compact-ele-pos="main-date" pagination="default" filters="no"] </strong> </code></br></br>
                      <h3>'.__('Horizontal Timeline.','cool-timeline').'</h3><code><strong>[cool-timeline layout="horizontal" category="{add here category-slug}" skin="default" designs="default" show-posts="20" order="DESC" items="" icons="NO" story-content="short" date-format="default" based="default" autoplay="false" start-on="0"] </strong> </code></br></br>
                      <h3>'.__('Vertical Content Timeline(any post type).','cool-timeline').'</h3><code><strong>[cool-content-timeline post-type="post" post-category="" tags="" story-content="short" taxonomy="category" layout="default" designs="default" skin="default" show-posts="10" order="DESC" icons="NO" animations="bounceInUp" date-format="default" pagination="default" filters="no"] </strong> </code></br></br>          
                      <h3>'.__('Horizontal Content Timeline(any post type).','cool-timeline').'</h3><code><strong>[cool-content-timeline post-type="post" post-category="" tags="" autoplay="false" story-content="short" taxonomy="category" layout="horizontal" designs="default" skin="default" show-posts="10" order="DESC" start-on="0" icons="NO" items="" date-format="default"] </strong> </code></br></br>',          
          ),

          array(
            'type'    => 'subheading',
            'content' => __('Timeline Shortcode Generator (For Classic Editor).','cool-timeline1'),
          ),

          array(
            'id'=>'ctl-classic-shortcode',
            'type'=>'content',
            'content'=> '<img src="'.CTP_PLUGIN_URL.'assets/images/classic-shortcode-button.gif" style="width:100%">',          
          ),

          array(
            'type'    => 'subheading',
            'content' => __('Timeline Shortcode Generator (For Gutenberg Block).','cool-timeline1'),
          ),

          array(
            'id'=>'ctl-shortcode-button',
            'type'=>'content',
            'content'=> '<img src="'.CTP_PLUGIN_URL.'assets/images/shortcode-generator.gif" style="width:100%">',          
          ),

          
      )
    ) );
    //End Section

  }