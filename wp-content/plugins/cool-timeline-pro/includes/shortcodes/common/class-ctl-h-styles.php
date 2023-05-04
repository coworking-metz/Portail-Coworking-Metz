<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if (!class_exists('CTL_H_Styles')) {

    class CTL_H_Styles {

        // horizontal timeline stories custom color styles

        public static function ctl_h_story_styles($post_id ,$layout,$design,$timeline_skin){
            // Extra Settings
            $ctl_extra_settings = get_post_meta($post_id, 'extra_settings', true);
            $ctl_story_color = isset($ctl_extra_settings['ctl_story_color'])?$ctl_extra_settings['ctl_story_color']:'';

              $styles='';
              $skin_styles='';
              $desgin_styles='';
              $layout_styles='';
            if(isset($ctl_story_color) && !empty($ctl_story_color)&& $ctl_story_color!="#")
            {
                    $ctl_options_arr= get_option('cool_timeline_settings');   
                    $line_color= isset($ctl_options_arr['line_color'])? $ctl_options_arr['line_color'] : '#000';
                    switch($design){
                    case "design-7":
                        $desgin_styles='.cool-timeline-horizontal.ht-design-7 ul.slick-slider #story-id-'.$post_id.' a
                        {
                            color:'.$ctl_story_color.' !important;
                        }
                        .cool-timeline-horizontal.ht-design-7 ul.slick-slider #story-id-'.$post_id.' .ctl-main-story-date:after
                        {
                            background: -moz-linear-gradient(top,'.$ctl_story_color.' 0,rgba(229,229,229,0) 100%) !important;
                            background: -webkit-linear-gradient(top,'.$ctl_story_color.' 0,rgba(229,229,229,0) 100%) !important;
                            background: linear-gradient(to bottom,'.$ctl_story_color.' 0,rgba(229,229,229,0) 100%) !important;
                        }
                        .cool-timeline-horizontal.ht-design-7 ul.slick-slider #story-id-'.$post_id.' .ctl-main-story-date:before
                            {
                                background: '.$ctl_story_color.' !Important;
                                border-color: '.$ctl_story_color.' !Important;
                            }';
                        break;
                        case "design-6":
                      $desgin_styles='.cool-timeline-horizontal.white-timeline ul.ctl_h_nav .slick-list li#story-id-'.$post_id.' .ctl-story-time .ctl-tooltips span:after
                            {
                                border-top-color:'.$ctl_story_color.';
                            }
                            .cool-timeline-horizontal.dark-timeline ul.ctl_h_nav .slick-list li#story-id-'.$post_id.' .ctl-story-time .ctl-tooltips span:after
                            {
                                border-top-color:'.$ctl_story_color.';
                            }
                            ';
                        break;
                        case "design-4":
                            $desgin_styles='.cool-timeline-horizontal.white-timeline ul.ctl_h_nav .slick-list li#story-id-'.$post_id.' .ctl-story-time .ctl-tooltips span:after
                                  {
                                      border-top-color:'.$ctl_story_color.';
                                  }
                                  .cool-timeline-horizontal.dark-timeline ul.ctl_h_nav .slick-list li#story-id-'.$post_id.' .ctl-story-time .ctl-tooltips span:after
                                  {
                                      border-top-color:'.$ctl_story_color.';
                                  }
                                  ';
                              break;
                    case "design-3":
                        $desgin_styles='
                        .cool-timeline-horizontal.white-timeline.ht-design-3 .clt_carousel_slider ul.slick-slider .slick-list #story-id-'.$post_id.'.slick-slide:after,
                        .cool-timeline-horizontal.dark-timeline.ht-design-3 .clt_carousel_slider ul.slick-slider .slick-list #story-id-'.$post_id.'.slick-slide:after
                        {
                        border-color:'.$ctl_story_color.';
                        }
                        .cool-timeline-horizontal.white-timeline.ht-design-3 #story-id-'.$post_id.'-content.slick-slide .timeline-post h2.content-title-simple,
                        .cool-timeline-horizontal.white-timeline.ht-design-3 #story-id-'.$post_id.'-content.slick-slide .timeline-post h2.content-title-simple a,
                        {
                            color:'.$ctl_story_color.' !important;
                        }
                         .cool-timeline-horizontal.white-timeline.ht-design-3 #story-id-'.$post_id.'-content.slick-slide .timeline-post
                        {
                        border-top-color:'.$ctl_story_color.';
                        }
                        .cool-timeline-horizontal.white-timeline ul.ctl_h_nav .slick-list li#story-id-'.$post_id.' .ctl-story-time .ctl-tooltips span:after
                        {
                            border-top-color:'.$ctl_story_color.';
                        }
                        .cool-timeline-horizontal.dark-timeline ul.ctl_h_nav .slick-list li#story-id-'.$post_id.' .ctl-story-time .ctl-tooltips span:after
                        {
                            border-top-color:'.$ctl_story_color.';
                        }
                        ';
                        break;
                        case "design-4":
                            $desgin_styles=' 
                            .cool-timeline-horizontal.white-timeline.ht-design-4 #story-id-'.$post_id.'-content.slick-slide .timeline-post h2.content-title-simple,
                            .cool-timeline-horizontal.white-timeline.ht-design-4 #story-id-'.$post_id.'-content.slick-slide .timeline-post h2.content-title-simple a
                            {
                                color:'.$ctl_story_color.' !important;
                            }
                             ';
                        break;
                    default:
                        $desgin_styles='';
                        break;
                    }
                
                    switch($timeline_skin){
                        case "light":
                            $skin_styles='';
                        
                            break;
                        case "dark":
                            $skin_styles=' .cool-timeline-horizontal.dark-timeline #story-id-'.$post_id.'-content .timeline-post
                            {
                            background:'.$ctl_story_color.';border:0;box-shadow:none;
                            }
                            .cool-timeline-horizontal.dark-timeline #story-id-'.$post_id.'.slick-current:after,
                            .cool-timeline-horizontal.dark-timeline .clt_carousel_slider ul.slick-slider .slick-list #story-id-'.$post_id.'.slick-slide:after
                            {
                                border-bottom-color:'.$ctl_story_color.';
                            }
                            .cool-timeline-horizontal.dark-timeline #story-id-'.$post_id.' span.icon-placeholder,
                            .cool-timeline-horizontal.dark-timeline #story-id-'.$post_id.' .ctl-story-time:after,
                            .cool-timeline-horizontal.dark-timeline #story-id-'.$post_id.' span.ctl-story-time .ctl-tooltips span
                            {
                                background:'.$ctl_story_color.';
                            }
                            .cool-timeline-horizontal.dark-timeline #story-id-'.$post_id.' span.ctl-story-time	
                            {
                                color:'.$ctl_story_color.' !important;
                            }
                            .cool-timeline-horizontal.dark-timeline #story-id-'.$post_id.' .ctl-tooltips span:after,
                            {
                            border-top-color:'.$ctl_story_color.';
                            }';

                            break;
                        default:
                            $skin_styles='
                            .cool-timeline-horizontal.white-timeline #story-id-'.$post_id.'-content .timeline-post .content-title,
                            cool-timeline-horizontal.white-timeline #story-id-'.$post_id.' .ctl-tooltips span
                            {
                            background:'.$ctl_story_color.';
                            }
                            .cool-timeline-horizontal.white-timeline #story-id-'.$post_id.' .ctl-tooltips span:after, 
                            {
                             border-top-color:'.$ctl_story_color.';
                            }
                            .cool-timeline-horizontal.white-timeline .clt_carousel_slider ul.slick-slider .slick-list #story-id-'.$post_id.'.slick-slide:after,
                            .cool-timeline-horizontal.white-timeline #story-id-'.$post_id.'.slick-current:after
                            {
                            border-bottom-color:'.$ctl_story_color.';
                            }
                            .cool-timeline-horizontal.white-timeline #story-id-'.$post_id.' span.icon-placeholder,
                            .cool-timeline-horizontal.white-timeline #story-id-'.$post_id.' .ctl-story-time:after,
                            .cool-timeline-horizontal.white-timeline #story-id-'.$post_id.' span.ctl-story-time .ctl-tooltips span
                            {
                            background:'.$ctl_story_color.';
                            }
                            .cool-timeline-horizontal.white-timeline #story-id-'.$post_id.' span.ctl-story-time
                            {
                                color:'.$ctl_story_color.' !important;
                            } ';
                            break;
                    }
                    
          
           }
           $styles.=$desgin_styles;
           $styles.=$skin_styles;
           return self::clt_minify_css($styles);
        }   

    public static function ctl_horizontal_layout_styles(){
            require('style-settings-vars.php');
            $styles_hori='';       

      $styles_hori.='
 /* Horizontal Styles */
 
     .cool-timeline-horizontal.white-timeline.ht-design-5 .clt_carousel_slider ul.slick-slider .slick-list li:nth-child(2n):not(.slick-current) span.icon-placeholder, 
     .cool-timeline-horizontal.white-timeline.ht-design-5 .clt_carousel_slider ul.slick-slider .slick-list li:nth-child(2n+1):not(.slick-current) span.icon-placeholder, 
 
     .cool-timeline-horizontal.white-timeline.ht-design-5 .clt_carousel_slider ul.slick-slider .slick-list li:nth-child(2n):not(.slick-current) .ctl-story-time:after,
     .cool-timeline-horizontal.white-timeline.ht-design-5 .clt_carousel_slider ul.slick-slider .slick-list li:nth-child(2n+1):not(.slick-current) .ctl-story-time:after,
     
     .cool-timeline-horizontal.white-timeline.ht-design-5 .clt_carousel_slider ul.ctl_h_nav:before,
     .cool-timeline-horizontal.white-timeline.ht-design-6 .clt_carousel_slider ul.ctl_h_nav:before,

     .cool-timeline-horizontal.white-timeline.ht-design-7 ul.ctl_minimal_cont.slick-slider:before
     {
     background:'.$line_color.' !Important;
     }
     
     .cool-timeline-horizontal.white-timeline.ht-design-5 .clt_carousel_slider ul.slick-slider .slick-list li:nth-child(2n).slick-current span.icon-placeholder, 
     .cool-timeline-horizontal.white-timeline.ht-design-5 .clt_carousel_slider ul.slick-slider .slick-list li:nth-child(2n+1).slick-current span.icon-placeholder, 
 
     .cool-timeline-horizontal.white-timeline.ht-design-5 .clt_carousel_slider ul.slick-slider .slick-list li:nth-child(2n).slick-current .ctl-story-time:after,
     .cool-timeline-horizontal.white-timeline.ht-design-5 .clt_carousel_slider ul.slick-slider .slick-list li:nth-child(2n+1).slick-current .ctl-story-time:after,
     
     .cool-timeline-horizontal.dark-timeline.ht-design-5 .clt_carousel_slider ul.slick-slider .slick-list li:nth-child(2n).slick-current span.icon-placeholder, 
     .cool-timeline-horizontal.dark-timeline.ht-design-5 .clt_carousel_slider ul.slick-slider .slick-list li:nth-child(2n+1).slick-current span.icon-placeholder, 
 
     .cool-timeline-horizontal.dark-timeline.ht-design-5 .clt_carousel_slider ul.slick-slider .slick-list li:nth-child(2n).slick-current .ctl-story-time:after,
     .cool-timeline-horizontal.dark-timeline.ht-design-5 .clt_carousel_slider ul.slick-slider .slick-list li:nth-child(2n+1).slick-current .ctl-story-time:after,
 
     .cool-timeline-horizontal.dark-timeline.ht-design-5 .clt_caru_slider ul.slick-slider .slick-list li.slick-slide .timeline-post	
     {
     background:'.$first_post_color.' !Important;
     }
     
     .cool-timeline-horizontal.white-timeline.ht-design-5 .clt_carousel_slider ul.slick-slider .slick-list li:not(.slick-current) span.ctl-story-time,
     .cool-timeline-horizontal.white-timeline.ht-design-7 .ctl-slick-prev,
     .cool-timeline-horizontal.white-timeline.ht-design-7 .ctl-slick-next
     {
     color:'.$line_color.' !Important;
     }	
     
     .cool-timeline-horizontal.white-timeline.ht-design-5 .clt_carousel_slider ul.slick-slider .slick-list li.slick-current::before
     {
         background-image: -webkit-linear-gradient(left, '.$first_post_color.' 50%, '.$line_color.' 50%)!important;
     }
     .cool-timeline-horizontal.white-timeline.ht-design-5 .clt_carousel_slider ul.slick-slider .slick-list li:first-child.slick-current::before
     {
         background-image: -webkit-linear-gradient(left, '.$line_color.' 50%, '.$line_color.' 50%)!important;
     }
     .cool-timeline-horizontal.white-timeline.ht-design-5 .clt_carousel_slider ul.slick-slider .slick-list li.pi::before
     {
         background-image: -webkit-linear-gradient(left, '.$first_post_color.' 50%, '.$first_post_color.' 50%)!important;
     }
     .cool-timeline-horizontal.white-timeline.ht-design-5 .clt_carousel_slider ul.slick-slider .slick-list li:first-child.pi::before
     {
         background-image: -webkit-linear-gradient(left, '.$line_color.' 50%, '.$first_post_color.' 50%)!important;
     }
     .cool-timeline-horizontal.white-timeline.ht-design-5 .clt_carousel_slider ul.slick-slider .slick-list li.slick-current span.ctl-story-time
     {
         color: '.$first_post_color.' !important;
     }
     .cool-timeline-horizontal.dark-timeline.ht-design-5 .clt_carousel_slider ul.slick-slider .slick-list li.slick-current::before
     {
         background-image: -webkit-linear-gradient(left, '.$first_post_color.' 50%, #000 50%)!important;
     }
     .cool-timeline-horizontal.dark-timeline.ht-design-5 .clt_carousel_slider ul.slick-slider .slick-list li:first-child.slick-current::before
     {
         background-image: -webkit-linear-gradient(left, #000 50%, #000 50%)!important;
     }
     .cool-timeline-horizontal.dark-timeline.ht-design-5 .clt_carousel_slider ul.slick-slider .slick-list li.pi::before
     {
         background-image: -webkit-linear-gradient(left, '.$first_post_color.' 50%, '.$first_post_color.' 50%)!important;
     }
     .cool-timeline-horizontal.dark-timeline.ht-design-5 .clt_carousel_slider ul.slick-slider .slick-list li:first-child.pi::before
     {
         background-image: -webkit-linear-gradient(left, #000 50%, '.$first_post_color.' 50%)!important;
     }
     .cool-timeline-horizontal.dark-timeline.ht-design-5 .clt_carousel_slider ul.slick-slider .slick-list li.slick-current span.ctl-story-time
     
     {
         color: '.$first_post_color.' !important;
     }


     .cool-timeline-horizontal.white-timeline.ht-design-7 ul.slick-slider li a
     {
         color: '.$first_post_color.';
     }
     .cool-timeline-horizontal.white-timeline.ht-design-7 ul.slick-slider li:nth-child(2n+1) a
     {
         color: '.$second_post_color.';
     }
     .cool-timeline-horizontal.white-timeline.ht-design-7 ul.slick-slider li .ctl-main-story-date:before
     {
         background: '.$first_post_color.';
         border-color: '.$first_post_color.';
     }
     .cool-timeline-horizontal.white-timeline.ht-design-7 ul.slick-slider li:nth-child(2n+1) .ctl-main-story-date:before
     {
         background: '.$second_post_color.';
         border-color: '.$second_post_color.';
     }
     .cool-timeline-horizontal.white-timeline.ht-design-7 .ctl-main-story-date:after
     {
        background: linear-gradient(to bottom,'.$first_post_color.' 0,rgba(229,229,229,0) 100%);
     }
     .cool-timeline-horizontal.white-timeline.ht-design-7 li:nth-child(2n+1) .ctl-main-story-date:after
     {
        background: linear-gradient(to bottom,'.$second_post_color.' 0,rgba(229,229,229,0) 100%);
     }
     .cool-timeline-horizontal.ht-design-7 li .ctl-main-story-title,
     .ctl-popup-content h2.popup-content-title
     {
        font-size:calc('.$post_title_s.' - 3px)!important;
        '.$ctl_post_title_typo_all.';
     }
     .cool-timeline-horizontal.ht-design-7 li .ctl-main-story-date,
     .ctl-popup-content .popup-posted-date,
     .ctl-popup-content .popup-sublabels
     {
        '.$ctl_date_typo_all.';
        color:'.$ctl_date_color.';
     }
     .ctl-popup-content .content-details p {
        '.$ctl_post_content_typo_all.';
     }

     
      .cool-timeline-horizontal.ht-design-5 .clt_caru_slider ul.slick-slider .slick-list li.slick-slide .timeline-post h2.content-title-simple,
      .cool-timeline-horizontal.ht-design-6 .clt_caru_slider ul.slick-slider .slick-list li.slick-slide .timeline-post h2.content-title-simple
      {
        
        '.$ctl_post_title_typo_all.';
         margin: 5px 0;
         padding: 0;
      }
      .cool-timeline-horizontal.white-timeline.ht-design-5 .clt_caru_slider ul.slick-slider .slick-list li.slick-slide .timeline-post h2.content-title-simple a,
      .cool-timeline-horizontal.white-timeline.ht-design-6 .clt_caru_slider ul.slick-slider .slick-list li.slick-slide .timeline-post h2.content-title-simple a
      {
         color: '.$content_color.'; 
         filter: brightness(1.05);
         -webkit-filter: brightness(1.05);
      }
     
 
 .cool-timeline-horizontal.white-timeline .clt_caru_slider ul.slick-slider .slick-list li.slick-slide .timeline-post a.ctl_read_more {
     color: '.$content_color.';
     border: 1px solid '.$content_color.';
     filter: brightness(1.05);
     -webkit-filter: brightness(1.05);
 }
 .cool-timeline-horizontal .clt_caru_slider ul.slick-slider .slick-list li.slick-slide .timeline-post a.ctl_read_more:hover {
     filter: brightness(1.2);
     -webkit-filter: brightness(1.2);
 }
 
 .cool-timeline-horizontal .clt_caru_slider ul.slick-slider .slick-list li:nth-child(2n).slick-slide .timeline-post .content-title {
     background:'.$first_post_color.';
     color:'.$title_color.';
 }
 
 .cool-timeline-horizontal .clt_caru_slider ul.slick-slider .slick-list li:nth-child(2n+1).slick-slide .timeline-post .content-title {
     background:'.$second_post_color.';
     color:'.$title_color.';
 }
 .cool-timeline-horizontal.white-timeline .clt_caru_slider ul.slick-slider .slick-list li.slick-slide .timeline-post h2.content-title a,
 .cool-timeline-horizontal  .clt_carousel_slider ul.slick-slider .slick-list li.ht-dates-design-2 span.icon-placeholder,
 .cool-timeline-horizontal  .clt_carousel_slider ul.slick-slider .slick-list li.ht-dates-default span.icon-placeholder,
 .cool-timeline-horizontal .clt_caru_slider ul.slick-slider .slick-list li.slick-slide .timeline-post .content-title a {
     color:'.$title_color.';
 }
 
 .cool-timeline-horizontal .clt_carousel_slider ul.slick-slider .slick-list li:nth-child(2n).slick-slide.slick-current:after {
     border-bottom-color:'.$first_post_color.';
 }
 
 .cool-timeline-horizontal .clt_carousel_slider ul.slick-slider .slick-list li:nth-child(2n+1).slick-slide.slick-current:after {
     border-bottom-color:'.$second_post_color.';
 }
 
 .cool-timeline-horizontal.ht-design-3 .clt_caru_slider ul.slick-slider .slick-list li:nth-child(2n).slick-slide .timeline-post {
     border-top: 4px solid '.$first_post_color.';
 }
 
 .cool-timeline-horizontal.ht-design-3 .clt_caru_slider ul.slick-slider .slick-list li:nth-child(2n+1).slick-slide .timeline-post {
     border-top: 4px solid '.$second_post_color.';
 }
 
 .cool-timeline-horizontal.ht-design-2 .clt_carousel_slider ul.slick-slider .slick-list li:nth-child(2n).slick-slide:after {
     border-bottom-color: '.$first_post_color.';
 }
 .cool-timeline-horizontal.white-timeline.ht-design-3 .clt_carousel_slider ul.slick-slider .slick-list li:nth-child(2n).slick-slide:after {
     border: 2px solid '.$first_post_color.';
     border-bottom-color: '.$first_post_color.';
 }
 
 .cool-timeline-horizontal.ht-design-2 .clt_carousel_slider ul.slick-slider .slick-list li:nth-child(2n+1).slick-slide:after {
     border-bottom-color:'.$second_post_color.';
 }
 .cool-timeline-horizontal.white-timeline.ht-design-3 .clt_carousel_slider ul.slick-slider .slick-list li:nth-child(2n+1).slick-slide:after {
     border: 2px solid '.$second_post_color.';
     border-bottom-color: '.$second_post_color.';
 }
 
 .cool-timeline-horizontal .clt_carousel_slider ul.slick-slider .slick-list li:nth-child(2n) span.icon-placeholder, .cool-timeline-horizontal .clt_carousel_slider ul.slick-slider .slick-list li:nth-child(2n) .ctl-story-time:after, .cool-timeline-horizontal .wrp-desgin-4 ul.slick-slider .slick-list li:nth-child(2n) span.icon-placeholder, .cool-timeline-horizontal .wrp-desgin-4 ul.ctl_h_nav  .slick-list li:nth-child(2n) .ctl-story-time:after {
     background: '.$first_post_color.';
 }
 
 .cool-timeline-horizontal .clt_carousel_slider ul.slick-slider .slick-list li:nth-child(2n+1) span.icon-placeholder, .cool-timeline-horizontal .clt_carousel_slider ul.slick-slider .slick-list li:nth-child(2n+1) .ctl-story-time:after, .cool-timeline-horizontal .wrp-desgin-4 ul.slick-slider .slick-list li:nth-child(2n+1) span.icon-placeholder, .cool-timeline-horizontal .wrp-desgin-4 ul.ctl_h_nav  .slick-list li:nth-child(2n+1) .ctl-story-time:after {
     background:'.$second_post_color.';
 }
 
 .cool-timeline-horizontal .clt_carousel_slider ul.slick-slider .slick-list li span.ctl-story-time, .cool-timeline-horizontal .wrp-desgin-4 ul.slick-slider .slick-list li span.ctl-story-time {
     color:'.$ctl_date_color.'!important;
     '.$ctl_date_typo_all.';
 }
 
 .cool-timeline-horizontal .clt_carousel_slider ul.slick-slider .slick-list li:nth-child(2n) span.ctl-story-time, .cool-timeline-horizontal.ht-design-3 .clt_caru_slider ul.slick-slider .slick-list li:nth-child(2n).slick-slide .timeline-post h2.content-title-simple, .cool-timeline-horizontal.ht-design-4 .clt_caru_slider ul.slick-slider .slick-list li:nth-child(2n).slick-slide .timeline-post h2.content-title-simple, .cool-timeline-horizontal .wrp-desgin-4 ul.slick-slider .slick-list li:nth-child(2n) span.ctl-story-time, .cool-timeline-horizontal.ht-design-3 .clt_caru_slider ul.slick-slider .slick-list li:nth-child(2n).slick-slide .timeline-post h2.content-title-simple a, .cool-timeline-horizontal.ht-design-4 .clt_caru_slider ul.slick-slider .slick-list li:nth-child(2n).slick-slide .timeline-post h2.content-title-simple a {
     color:'.$first_post_color.';
 }
 
 .cool-timeline-horizontal .clt_carousel_slider ul.slick-slider .slick-list li:nth-child(2n+1) span.ctl-story-time, .cool-timeline-horizontal.ht-design-3 .clt_caru_slider ul.slick-slider .slick-list li:nth-child(2n+1).slick-slide .timeline-post h2.content-title-simple, .cool-timeline-horizontal.ht-design-4 .clt_caru_slider ul.slick-slider .slick-list li:nth-child(2n+1).slick-slide .timeline-post h2.content-title-simple, .cool-timeline-horizontal .wrp-desgin-4 ul.slick-slider .slick-list li:nth-child(2n+1) span.ctl-story-time, .cool-timeline-horizontal.ht-design-3 .clt_caru_slider ul.slick-slider .slick-list li:nth-child(2n+1).slick-slide .timeline-post h2.content-title-simple a, .cool-timeline-horizontal.ht-design-4 .clt_caru_slider ul.slick-slider .slick-list li:nth-child(2n+1).slick-slide .timeline-post h2.content-title-simple a {
     color:'.$second_post_color.';
 }
 ';
  $styles_hori.='.cool-timeline-horizontal.white-timeline .clt_caru_slider ul.slick-slider .slick-list li.slick-slide .timeline-post h2.content-title, .cool-timeline-horizontal.dark-timeline .clt_caru_slider ul.slick-slider .slick-list li.slick-slide .timeline-post h2.content-title, .cool-timeline-horizontal.light-timeline .clt_caru_slider ul.slick-slider .slick-list li.slick-slide .timeline-post h2.content-title, .cool-timeline-horizontal.ht-design-3 .clt_caru_slider ul.slick-slider .slick-list li.slick-slide .timeline-post h2.content-title-simple, .cool-timeline-horizontal.ht-design-4 .clt_caru_slider ul.slick-slider .slick-list li.slick-slide .timeline-post h2.content-title-simple,  .cool-timeline-horizontal.dark-timeline .clt_caru_slider ul.slick-slider .slick-list li.slick-slide .timeline-post h2.content-title a, .cool-timeline-horizontal.light-timeline .clt_caru_slider ul.slick-slider .slick-list li.slick-slide .timeline-post h2.content-title a, .cool-timeline-horizontal.ht-design-3 .clt_caru_slider ul.slick-slider .slick-list li.slick-slide .timeline-post h2.content-title-simple a, .cool-timeline-horizontal.ht-design-4 .clt_caru_slider ul.slick-slider .slick-list li.slick-slide .timeline-post h2.content-title-simple a
  {   
    '.$ctl_post_title_typo_all.';
     color:#fff;
 }
 .cool-timeline-horizontal.white-timeline .clt_caru_slider ul.slick-slider .slick-list li.slick-slide .timeline-post .content-details, .cool-timeline-horizontal.dark-timeline .clt_caru_slider ul.slick-slider .slick-list li.slick-slide .timeline-post .content-details, .cool-timeline-horizontal.light-timeline .clt_caru_slider ul.slick-slider .slick-list li.slick-slide .timeline-post .content-details {
    '.$ctl_post_content_typo_all.';
 }
 
 .cool-timeline-horizontal.white-timeline .clt_caru_slider ul.slick-slider .slick-list li.slick-slide .timeline-post {
     color:'.$content_color.';
     background:'.$content_bg_color.';
 }
 .cool-timeline-horizontal.white-timeline .clt_caru_slider ul.slick-slider .slick-list li.slick-slide .timeline-post a {
     color:'.$content_color.';
     filter: brightness(1.05);
     -webkit-filter: brightness(1.05);
 }
 .cool-timeline-horizontal.white-timeline .clt_caru_slider ul.slick-slider .slick-list li.slick-slide .timeline-post a:hover {
     color:'.$content_color.';
     filter: brightness(1.2);
     -webkit-filter: brightness(1.2);
 }';
 
 
    
  $styles_hori.='.cool-timeline-horizontal.white-timeline .clt_caru_slider ul.slick-slider .slick-list li.slick-slide .timeline-post .ctl_info {
     color:'.$content_color.';
 }';
  
 
  $styles_hori.='.cool-timeline-horizontal .clt_carousel_slider ul.slick-slider button.slick-prev:before, .cool-timeline-horizontal .clt_carousel_slider ul.slick-slider button.slick-next:before, .cool-timeline-horizontal .wrp-desgin-4 ul.slick-slider button.slick-prev:before, .cool-timeline-horizontal .wrp-desgin-4 ul.slick-slider button.slick-next:before {
     color:'.$line_color.';
 }
 
 .cool-timeline-horizontal.light-timeline .clt_carousel_slider ul.slick-slider button.slick-prev:before, .cool-timeline-horizontal.light-timeline .clt_carousel_slider ul.slick-slider button.slick-next:before, .cool-timeline-horizontal.light-timeline .wrp-desgin-4 ul.slick-slider button.slick-prev:before, .cool-timeline-horizontal.light-timeline .wrp-desgin-4 ul.slick-slider button.slick-next:before {
     color:#666;
 }
 
 .cool-timeline-horizontal.dark-timeline .clt_carousel_slider ul.slick-slider button.slick-prev:before, .cool-timeline-horizontal.dark-timeline .clt_carousel_slider ul.slick-slider button.slick-next:before, .cool-timeline-horizontal.dark-timeline .wrp-desgin-4 ul.slick-slider button.slick-prev:before, .cool-timeline-horizontal.dark-timeline .wrp-desgin-4 ul.slick-slider button.slick-next:before {
     color:#222;
 }';
 
 
 
  $styles_hori.='
 .cool-timeline-horizontal.white-timeline ul.ctl_h_nav  .slick-list li:nth-child(2n) .ctl-story-time .ctl-tooltips span{
  background: '.$first_post_color.';
 }
 .cool-timeline-horizontal.white-timeline ul.ctl_h_nav  .slick-list li:nth-child(2n+1) .ctl-story-time .ctl-tooltips span {
  background: '.$second_post_color.';
 }
 .cool-timeline-horizontal.white-timeline ul.ctl_h_nav  .slick-list li:nth-child(2n) .ctl-story-time .ctl-tooltips span:after
 {
     border-top-color: '.$first_post_color.';
 }
 .cool-timeline-horizontal.white-timeline ul.ctl_h_nav  .slick-list li:nth-child(2n+1) .ctl-story-time .ctl-tooltips span:after
 {
     border-top-color:'.$second_post_color.';
     }
 ';
 
  $styles_hori.='.cool-timeline-horizontal.white-timeline.ht-design-4 .clt_caru_slider ul.slick-slider .slick-list li.slick-slide .timeline-post {
     background: -moz-linear-gradient(90deg, rgba(255, 255, 255, 0) 0%, '.$content_bg_color.' 100%);
     background: -webkit-gradient(linear, left top, left bottom, color-stop(0%, '.$content_bg_color.'), color-stop(100%, rgba(255, 255, 255, 0)));
     background: -webkit-linear-gradient(90deg, rgba(255, 255, 255, 0) 0%, '.$content_bg_color.' 100%);
     background: -o-linear-gradient(90deg, rgba(255, 255, 255, 0) 0%, '.$content_bg_color.' 100%);
     background: -ms-linear-gradient(90deg, rgba(255, 255, 255, 0) 0%, '.$content_bg_color.' 100%);
     background: linear-gradient(0deg, rgba(255, 255, 255, 0) 0%, '.$content_bg_color.' 100%);}';
 
     $styles_hori.='.cool-timeline-horizontal .clt_carousel_slider ul.slick-slider .slick-list li:before, .cool-timeline-horizontal .wrp-desgin-4 ul.ctl_h_nav  .slick-list li:before  {
     background-color:'.$line_color.';
     background-image: -webkit-linear-gradient(top, '.$line_color.' 0%, '.$line_color.' 8%, '.$line_color.' 92%, '.$line_color.' 100%);
     background-image: -moz-linear-gradient(top, '.$line_color.' 0%, '.$line_color.' 8%,'.$line_color.' 92%, '.$line_color.' 100%);
     background-image: -ms-linear-gradient(top, '.$line_color.' 0%, '.$line_color.' 8%, '.$line_color.' 92%, '.$line_color.' 100%);}';
   
     $custom_css= preg_replace('/\\\\/', '', $custom_styles);
     $final_css=self::clt_minify_css($styles_hori);

     wp_add_inline_style( 'ctl-styles-horizontal',$custom_css.' '.$final_css);

        }

        public static function clt_minify_css($css){
         $buffer = $css;
          // Remove comments
          $buffer = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $buffer);
          // Remove space after colons
         $buffer = str_replace(': ', ':', $buffer);
          // Remove whitespace
        $buffer = str_replace(array("\r\n", "\r", "\n", "\t"), '', $buffer);
        $buffer = preg_replace(" {2,}", ' ',$buffer);
          // Write everything out
        return $buffer;
		}



    }

}