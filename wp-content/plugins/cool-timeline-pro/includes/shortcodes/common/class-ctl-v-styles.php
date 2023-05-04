<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if (!class_exists('CTL_V_Styles')) {

    class CTL_V_Styles {

        // vertical timeline stories custom color styles
        public static function clt_v_story_styles($post_id,$layout,$design,$timeline_skin){

            // Extra Settings
            $ctl_extra_settings = get_post_meta($post_id, 'extra_settings', true);
            $ctl_story_color = isset($ctl_extra_settings['ctl_story_color'])?$ctl_extra_settings['ctl_story_color']:'';
  
            $styles='';
            $skin_styles='';
            $desgin_styles='';
            $layout_styles='';
       if(isset($ctl_story_color) && !empty($ctl_story_color) && $ctl_story_color!="#")
       {
            switch($layout){
                case "one-side":
                    $layout_styles='
                    .cool-timeline.white-timeline.one-sided #story-'.$post_id.' .timeline-content .content-title:before,
                    .cool-timeline.white-timeline.one-sided #story-'.$post_id.' .timeline-content:before,
                    .cool-timeline.light-timeline.one-sided #story-'.$post_id.'.odd .timeline-content:before,
                    .cool-timeline.dark-timeline.one-sided #story-'.$post_id.' .timeline-content .content-title:before,
                    .cool-timeline.dark-timeline.one-sided #story-'.$post_id.' .timeline-content:before
                     { border-left-color: transparent; border-right-color: '.$ctl_story_color.'; }
                     .cool-timeline.white-timeline.one-sided #story-'.$post_id.'.odd  .timeline-content .content-title:before,
                     .cool-timeline.white-timeline.one-sided #story-'.$post_id.'.odd .timeline-content:before
                     {border-right-color:'.$ctl_story_color.';
                     border-left-color:transparent;
                     }
                     .main-design-3 .cool-timeline.white-timeline.one-sided #story-'.$post_id.'.odd .timeline-content
                     {
                        border-left:6px solid '.$ctl_story_color.';
                     }
                     ';
                    break;
                case "compact":
                    $layout_styles=' 
                    .cool-timeline.white-timeline.compact #story-'.$post_id. '.timeline-content .clt-compact-date
                     {color:'.$ctl_story_color.';}
                    .cool-timeline.white-timeline.compact .timeline-post.timeline-mansory.ctl-right#story-'.$post_id.' .timeline-content .content-title:after,
                    .cool-timeline.dark-timeline.compact .timeline-post.timeline-mansory.ctl-right#story-'.$post_id.' .timeline-content .content-title:after
                     { border-right-color:'.$ctl_story_color.';}
                    .cool-timeline.white-timeline.compact .timeline-post.timeline-mansory.ctl-left#story-'.$post_id.' .timeline-content .content-title:after,
                    .cool-timeline.dark-timeline.compact .timeline-post.timeline-mansory.ctl-left#story-'.$post_id.' .timeline-content .content-title:after
                     { border-left-color:'.$ctl_story_color.'; }
                     .cool-timeline.white-timeline.compact #story-'.$post_id.'.odd.ctl-right .timeline-content:before,
                     .cool-timeline.white-timeline.compact #story-'.$post_id.'.even.ctl-right .timeline-content:before,
                     .cool-timeline.dark-timeline.compact #story-'.$post_id.'.odd.ctl-right .timeline-content:before,
                     .cool-timeline.dark-timeline.compact #story-'.$post_id.'.even.ctl-right .timeline-content:before
                     { border-left-color: transparent; border-right-color: '.$ctl_story_color.'; }
                    .cool-timeline.white-timeline.compact #story-'.$post_id.'.odd.ctl-left .timeline-content:before,
                    .cool-timeline.white-timeline.compact #story-'.$post_id.'.even.ctl-left .timeline-content:before,
                    .cool-timeline.dark-timeline.compact #story-'.$post_id.'.odd.ctl-left .timeline-content:before,
                    .cool-timeline.dark-timeline.compact #story-'.$post_id.'.even.ctl-left .timeline-content:before
                    { border-right-color: transparent; border-left-color: '.$ctl_story_color.'; }
                    
                    .cool-timeline.white-timeline.compact .timeline-post.timeline-mansory.ctl-left#story-'.$post_id.' .timeline-content .content-title:after,
                    .cool-timeline.white-timeline.compact .timeline-post.timeline-mansory.ctl-right#story-'.$post_id.' .timeline-content .content-title:after,
                    .cool-timeline.dark-timeline.compact .timeline-post.timeline-mansory.ctl-left#story-'.$post_id.' .timeline-content .content-title:after,
                    .cool-timeline.dark-timeline.compact .timeline-post.timeline-mansory.ctl-right#story-'.$post_id.' .timeline-content .content-title:after,
                    .cool-timeline.white-timeline.compact #story-'.$post_id.'.odd.ctl-left .timeline-content:before,
                    .cool-timeline.white-timeline.compact #story-'.$post_id.'.even.ctl-left .timeline-content:before,
                    .cool-timeline.dark-timeline.compact #story-'.$post_id.'.odd.ctl-left .timeline-content:before,
                    .cool-timeline.dark-timeline.compact #story-'.$post_id.'.even.ctl-left .timeline-content:before,
                    {
                    border-left-color: transparent;
                    border-right-color: '.$ctl_story_color.';
                    }
                    @media (max-width: 860px) {
                  
                    .cool-timeline.white-timeline #story-'.$post_id.'.timeline-post.timeline-mansory.ctl-left.odd .timeline-content .content-title:after,
                    .cool-timeline.white-timeline #story-'.$post_id.'.timeline-post.timeline-mansory.ctl-left.even .timeline-content .content-title:after {
                        border-right-color:'.$ctl_story_color.';
                        border-left-color:transparent;
                    }
                    .cool-timeline.dark-timeline #story-'.$post_id.'.timeline-post.timeline-mansory.ctl-left.odd .timeline-content .content-title:after,
                    .cool-timeline.dark-timeline #story-'.$post_id.'.timeline-post.timeline-mansory.ctl-left.even .timeline-content .content-title:after {
                        border-right-color:'.$ctl_story_color.';
                        border-left-color:transparent;
                    }
                }
                    ';
                    break;
                default:
                    $layout_styles='';
                    break;
            }
           
           
            switch($design){
            case "design-6":
                $desgin_styles='
                .main-design-6 .cool-timeline.light-timeline #story-'.$post_id.'.timeline-post .timeline-content h2.content-title-2 a
                {color:'.$ctl_story_color.';}
                .main-design-6 .cool-timeline.white-timeline #story-'.$post_id.'.timeline-post .timeline-content h2.content-title-2 a
                {color:'.$ctl_story_color.';}
                .main-design-6 .cool-timeline.white-timeline #story-'.$post_id.'.timeline-post .timeline-icon.design-6-icon {padding-top:0px;color:#fff;}
                .main-design-6 .cool-timeline.dark-timeline.compact #story-'.$post_id.'.timeline-post .timeline-icon
                 { border-color:'.$ctl_story_color.';}
                
                ';
               
            break;
            case "design-5":
                $desgin_styles='
                .main-design-5 .cool-timeline.light-timeline #story-'.$post_id.'.timeline-post .timeline-content h2.content-title a,
                .main-design-5 .cool-timeline.white-timeline #story-'.$post_id.'.timeline-post .timeline-content h2.content-title a,
                .main-design-5 .cool-timeline.white-timeline #story-'.$post_id.'.timeline-post .timeline-content h2.content-title
                {color:'.$ctl_story_color.';}';
                break;
            case "design-4":
                $desgin_styles='
                .main-design-4 .cool-timeline.white-timeline #story-'.$post_id.' .timeline-content .content-title:before,
                .main-design-4 .cool-timeline.dark-timeline #story-'.$post_id.' .timeline-content .content-title:before
                { border-color:'.$ctl_story_color.';}';
                break;
            case "design-3":
              
               if($layout !='compact'){
                   $desgin_styles .=' .main-design-3 #story-'.$post_id.' .timeline-content.even
                    {
                        border-left: 6px solid '.$ctl_story_color.'; 
                    }'; 
               }

                $desgin_styles .='
                .main-design-3 #story-'.$post_id.' .timeline-content,
                .main-design-3 .cool-timeline.light-timeline #story-'.$post_id.'.even .timeline-content:before
                {border-right-color:'.$ctl_story_color.';}
                .main-design-3 .cool-timeline.light-timeline #story-'.$post_id.'.odd .timeline-content:before
                {border-left-color:'.$ctl_story_color.'; }		
                .main-design-3 .cool-timeline.light-timeline.compact #story-'.$post_id.'.odd.ctl-right .timeline-content:before,
    
                .main-design-3 .cool-timeline.light-timeline.compact #story-'.$post_id.'.even.ctl-right .timeline-content:before
                    { border-left-color: transparent;
                         border-right-color: '.$ctl_story_color.'; }
              
                 .main-design-3 #story-'.$post_id.'.ctl-right .timeline-content
                 {
                     border-left: 6px solid '.$ctl_story_color.'; 
                 } 
                .main-design-3 .cool-timeline.light-timeline.compact #story-'.$post_id.'.odd.ctl-left .timeline-content:before,
                .main-design-3 .cool-timeline.light-timeline.compact #story-'.$post_id.'.even.ctl-left .timeline-content:before
                { border-right-color: transparent; border-left-color: '.$ctl_story_color.'; }
                
                @media (max-width: 860px) {
                    .main-design-3 .cool-timeline.light-timeline #story-'.$post_id.'.even .timeline-content:before,
                    .main-design-3 .cool-timeline.light-timeline #story-'.$post_id.'.odd .timeline-content:before,
                    .main-design-3 .cool-timeline.light-timeline.compact #story-'.$post_id.'.odd.ctl-left .timeline-content:before
                        {
                        border-left-color: transparent;
                        border-right-color: '.$ctl_story_color.';
                        }

                    .main-design-3 #story-'.$post_id.' .timeline-content
                    {border-right:transparent;
   					border-left-color: '.$ctl_story_color.';
                    }
                    .main-design-3 #story-'.$post_id.' .timeline-content.even
                    {
                        border-left: 6px solid '.$ctl_story_color.'; 
                    }  
                }
                ';
                break;
                default:
                    $desgin_styles='';
                    break;
            }
         
            switch($timeline_skin){
                case "light":
                    $skin_styles='
                    .cool-timeline.light-timeline #story-'.$post_id. '.timeline-content h2.content-title-2 a,
                    .cool-timeline.light-timeline #story-'.$post_id. '.timeline-content h2.content-title-2
                    { color:'.$ctl_story_color.';}
                    ';
                    break;
                case "dark":
                    $skin_styles='.cool-timeline.dark-timeline #story-'.$post_id.' .icon-dot-full,
                    .cool-timeline.dark-timeline #story-'.$post_id.'.timeline-post .timeline-icon,
                    .cool-timeline.dark-timeline #story-'.$post_id.'.timeline-post .timeline-content .content-details a.ctl_read_more
                    {background:'.$ctl_story_color.';}
                    .cool-timeline.dark-timeline #story-'.$post_id.' .timeline-meta .meta-details
                        {color:'.$ctl_story_color.';}
                    .cool-timeline.dark-timeline #story-'.$post_id.'.timeline-post .timeline-content,
                    .cool-timeline.dark-timeline #story-'.$post_id.'.timeline-post .timeline-content img
                        {background: '.$ctl_story_color.';box-shadow: none;border-color: '.$ctl_story_color.';}
                    .cool-timeline.dark-timeline #story-'.$post_id.'.even .timeline-content:before,
                    .cool-timeline.dark-timeline #story-'.$post_id.'.even .timeline-content .content-title:before
                         {border-right-color:'.$ctl_story_color.';}
                    .cool-timeline.dark-timeline #story-'.$post_id.'.odd .timeline-content:before, .cool-timeline.dark-timeline #story-'.$post_id.'.odd .timeline-content .content-title:before
                        {border-left-color:'.$ctl_story_color.'; }
                        @media (max-width: 860px) {
                            .cool-timeline.dark-timeline #story-'.$post_id.'.odd .timeline-content .content-title:before, .cool-timeline.dark-timeline #story-'.$post_id.'.even .timeline-content .content-title:before,
                             .cool-timeline.dark-timeline #story-'.$post_id.'.odd .timeline-content:before, .cool-timeline.dark-timeline #story-'.$post_id.'.even .timeline-content:before 
                             {
                                border-left-color: transparent;
                                border-right-color: '.$ctl_story_color.';
                                }
                            }
                             ';
                    break;
                default:
                    $skin_styles='
                    .cool-timeline.white-timeline #story-'.$post_id.' .icon-dot-full,
                    .cool-timeline.white-timeline #story-'.$post_id.' .timeline-content .content-title, 
                    .cool-timeline.white-timeline.compact .timeline-post.icons_yes#story-'.$post_id.' 
                    .timeline-icon, 
                    .cool-timeline.white-timeline #story-'.$post_id.'.timeline-post .timeline-icon
                    {background:'.$ctl_story_color.';}
                
                    .cool-timeline.white-timeline #story-'.$post_id.' .timeline-meta .meta-details,
                    .cool-timeline.white-timeline #story-'.$post_id. '.timeline-content h2.content-title-2 a,
                    .cool-timeline.white-timeline #story-'.$post_id. '.timeline-content h2.content-title-2
                    {color:'.$ctl_story_color.';}
                    
                    .cool-timeline.white-timeline #story-'.$post_id.'.even  .timeline-content .content-title:before , .cool-timeline.white-timeline #story-'.$post_id.'.even .timeline-content:before
                    {border-right-color:'.$ctl_story_color.';}
                    .cool-timeline.white-timeline #story-'.$post_id.'.odd  .timeline-content .content-title:before,
                    .cool-timeline.white-timeline #story-'.$post_id.'.odd .timeline-content:before
                    {border-left-color:'.$ctl_story_color.'; }

                   
                    @media (max-width: 860px) {    
                        .cool-timeline.white-timeline #story-'.$post_id.'.odd .timeline-content .content-title:before, .cool-timeline.white-timeline #story-'.$post_id.'.even .timeline-content .content-title:before,
                        .cool-timeline.white-timeline #story-'.$post_id.'.odd .timeline-content:before, .cool-timeline.white-timeline #story-'.$post_id.'.even .timeline-content:before
                         {
                           border-left-color: transparent;
                           border-right-color: '.$ctl_story_color.';
                           }
                    }  
                    ';
                    break;
            }

             
                $styles.=$desgin_styles;
                $styles.=$skin_styles;
                $styles.=$layout_styles;
             return $styles;
           // return self::clt_minify_css($styles); 
          }       
    }



       // global style for vertical timeline  

        public static function  ctl_vertical_layout_styles( $var_name )
        {
      
            require('style-settings-vars.php');
              $styles='';
              $styles_hori='';
            /*  $styles.='
              /*-----Custom CSS-------*/
             // '.$custom_styles;
           
            /*
            Dynamic styles starts from here 
            */

 $styles.='.cool_timeline.cool-timeline-wrapper {
  background:'.$bg_color.';}';

 $styles.='.cool_timeline .timeline-main-title {
    '.$ctl_main_title_typo_all.'
    color:'.$main_title_color.';
}';


 $styles.='.cool-timeline.compact .timeline-post .timeline-content h2.compact-content-title,
 .cool-timeline.compact .timeline-post .timeline-content h2.content-title,
 .cool-timeline .timeline-post .timeline-content h2.content-title,
 .cool-timeline .timeline-post .timeline-content h2.content-title-2 ,
 .cool-timeline .timeline-post .timeline-content h2.content-title-simple
{
    '.$ctl_post_title_typo_all.';
}
.cool-timeline.white-timeline  .timeline-post .timeline-content .content-title a {
    color:#fff;
    '.$ctl_post_title_typo_all.';
}
.cool-timeline .timeline-post .timeline-content .content-details,
.cool-timeline .timeline-post .timeline-content .content-details p{
    '.$ctl_post_content_typo_all.';
}';

 $styles.='
.cool-timeline .timeline-post .timeline-meta .meta-details, .cool-timeline.compact .timeline-post .timeline-content .clt-compact-date,
.main-design-6 .cool-timeline .timeline-post .timeline-content .story-date.clt-meta-date,
.main-design-6 .cool-timeline.compact .timeline-post .timeline-content .content-title.clt-meta-date,
.main-design-5 .cool-timeline .timeline-post .timeline-content .story-date.clt-meta-date,
.main-design-5 .cool-timeline.compact .timeline-post .timeline-content .content-title.clt-meta-date
 {
    '.$ctl_date_typo_all.';
}
.main-design-6 .cool-timeline.white-timeline .timeline-post .timeline-content .story-date.clt-meta-date,
.main-design-6 .cool-timeline.compact.white-timeline .timeline-post .timeline-content .content-title.clt-meta-date,
.main-design-5 .cool-timeline.white-timeline .timeline-post .timeline-content .story-date.clt-meta-date,
.main-design-5 .cool-timeline.compact.white-timeline .timeline-post .timeline-content .content-title.clt-meta-date
{ color:'.$ctl_date_color.'!important; }

.ctl-bullets-container li a, .section-bullets-bottom li a {
    font-family:'.$ctl_date_f.';
    font-weight:'.$ctl_date_w.';
}
.cool-timeline .timeline-year span {
    font-family:'.$ctl_date_f.';
}';




 $styles.='.cool-timeline.white-timeline .light-grey-post .timeline-content .content-title { color:#ffffff; }

.cool-timeline.white-timeline .light-grey-post .timeline-content:after,
.cool-timeline.white-timeline .light-grey-post .timeline-content:before  { border-left-color:'.$content_bg_color.'; }
.cool-timeline.white-timeline .light-grey-post .even .timeline-content:after,
.cool-timeline.white-timeline .light-grey-post .even .timeline-content:before,
.cool-timeline.white-timeline.one-sided .light-grey-post .timeline-content:after,
.cool-timeline.white-timeline.one-sided .light-grey-post .timeline-content:before,
.cool-timeline.white-timeline.one-sided .light-grey-post .even .timeline-content:after,
.cool-timeline.white-timeline.one-sided .light-grey-post .even .timeline-content:before{
    border: 15px solid transparent;
 /*   border-right-color:'.$content_bg_color.'; */
}';



 $styles.='.cool-timeline.white-timeline .timeline-icon.icon-larger.iconbg-indigo{
    background:'.$circle_border_color.';
}
.cool-timeline.white-timeline:before,
.cool-timeline.white-timeline.one-sided:before  {
    background-color:'.$line_color.';
     background-image: -webkit-linear-gradient(top, '.$line_color.' 0%, '.$line_color.' 8%, '.$line_color.' 92%, '.$line_color.' 100%);
    background-image: -moz-linear-gradient(top, '.$line_color.' 0%, '.$line_color.' 8%, '.$line_color.' 92%, '.$line_color.' 100%);
    background-image: -ms-linear-gradient(top, '.$line_color.' 0%, '.$line_color.' 8%, '.$line_color.' 92%, '.$line_color.' 100%);
}';

 $styles.='
 .cool-timeline.white-timeline .timeline-year{
    background:'.$circle_border_color.';
}
.cool_timeline .cat-filter-wrp ul li a {border-color:'.$circle_border_color.';color:'.$circle_border_color.';font-family:'.$post_content_f.';}
.cool-timeline.white-timeline .timeline-year{
    -webkit-box-shadow: 0 0 0 4px white, inset 0 0 0 2px rgba(0, 0, 0, 0.05), 0 0 0 8px '.$line_color.';
    box-shadow: 0 0 0 4px white, inset 0 0 0 2px rgba(0, 0, 0, 0.05), 0 0 0 8px '.$line_color.';
}';


 $styles.='.cool-timeline.white-timeline .timeline-post .timeline-content .content-title,
 .cool-timeline.white-timeline .timeline-post .timeline-content .content-title a,
 .cool-timeline.white-timeline .timeline-post .timeline-content .content-title a,
 .main-design-5 .cool-timeline.white-timeline .timeline-post .timeline-content h2.content-title a,
 .main-design-6 .cool-timeline.white-timeline .timeline-post.even .timeline-content h2.content-title-simple a,
 .main-design-6 .cool-timeline.white-timeline .timeline-post.odd .timeline-content h2.content-title-simple a,
 .main-design-6 .cool-timeline.white-timeline .timeline-post .timeline-content h2.content-title-2 a,
 .main-design-6 .cool-timeline.white-timeline.compact .timeline-post .timeline-content h2.compact-content-title a,
 .main-design-5 .cool-timeline.white-timeline.compact .timeline-post .timeline-content h2.compact-content-title a
{
    color:'.$title_color.';
    text-decoration: none;
    box-shadow: none;
}
.main-design-6 .cool-timeline .timeline-post .timeline-icon.design-6-icon {color:'.$line_color.';}
.main-design-6 .cool-timeline.one-sided .timeline-post .timeline-icon.design-6-icon {background-color:'.$line_color.';color: #fff;}

.cool-timeline.white-timeline .timeline-post .timeline-content .content-title a:hover {
    color:'.$title_color.';
    filter: opacity(0.7);
    -webkit-filter: opacity(0.7);
}
.cool-timeline.white-timeline .timeline-post .timeline-content h2.content-title-simple,
.cool-timeline.white-timeline .timeline-post .timeline-content h2.content-title-simple a
{
    color:'.$content_color.';
    text-decoration: none;
    box-shadow: none;
    filter: brightness(1.05);
    -webkit-filter: brightness(1.05);
}
.cool-timeline.white-timeline .timeline-post .timeline-content h2.content-title-simple a:hover {
    color:'.$content_color.';
    filter: brightness(1.5);
    -webkit-filter: brightness(1.5);
}
.cool-timeline.white-timeline .timeline-post .timeline-content .content-details,  .section-bullets-bottom li.white-timeline a, .section-bullets-right li.white-timeline a, .section-bullets-left li.white-timeline a{
    color:'.$content_color.';
}
.cool-timeline.white-timeline .timeline-post .timeline-content .content-details a, .cool-timeline.white-timeline .timeline-post .post_meta_details a{
    color:'.$content_color.';
    filter: brightness(1.1);
    -webkit-filter: brightness(1.1);
}
.cool-timeline.white-timeline .timeline-post .timeline-content .content-details a:hover, .cool-timeline.white-timeline .timeline-post .post_meta_details a:hover{
    color:'.$content_color.';
    filter: brightness(1.25);
    -webkit-filter: brightness(1.25);
}
.cool-timeline.white-timeline .timeline-post .timeline-content, .section-bullets-bottom li.white-timeline, .section-bullets-right li.white-timeline, .section-bullets-left li.white-timeline{
    color:'.$content_color.';
    background:'.$content_bg_color.';
}
.ctl-footer-bullets-container li.white-timeline a:after, .section-bullets-right li.white-timeline a:after, .section-bullets-left li.white-timeline a:after {background:'.$content_color.';
filter: contrast(29%);
-webkit-filter: contrast(29%);
}
.ctl-footer-bullets-container li.white-timeline:before {border-bottom-color:'.$content_color.';
filter: contrast(29%);
-webkit-filter: contrast(29%);
}
.section-bullets-right li.white-timeline {border-left-color:'.$content_color.';}
.section-bullets-left li.white-timeline {border-right-color:'.$content_color.';}
.section-bullets-left li.white-timeline:before, .section-bullets-right li.white-timeline:before
{
background-image: inherit;
background-color: '.$content_color.';
filter: contrast(29%);
-webkit-filter: contrast(29%);
}
.section-bullets-bottom li.white-timeline {border-top-color:'.$content_color.';}
.cool-timeline.white-timeline .timeline-post .timeline-meta .meta-details, .cool-timeline.white-timeline.compact .timeline-post .timeline-content .clt-compact-date{
    color:'.$ctl_date_color.'!important;
}
';



 $styles.='.timeline-icon.icon-larger.iconbg-indigo.iconbg-turqoise.icon-color-white{
    -webkit-box-shadow: 0 0 0 4px white, inset 0 0 0 2px rgba(0, 0, 0, 0.05), 0 0 0 8px '.$line_color.';
    box-shadow: 0 0 0 4px white, inset 0 0 0 2px rgba(0, 0, 0, 0.05), 0 0 0 8px '.$line_color.';
}

.timeline-icon.icon-larger.iconbg-turqoise.icon-color-white{
    -webkit-box-shadow: 0 0 0 4px white, inset 0 0 0 2px rgba(0, 0, 0, 0.05), 0 0 0 8px '.$line_color.';
    box-shadow: 0 0 0 4px white, inset 0 0 0 2px rgba(0, 0, 0, 0.05), 0 0 0 8px '.$line_color.';
}';
 $styles.='.cool-timeline.white-timeline  .timeline-post.even .timeline-content .content-title {
    background:'.$first_post_color.';
}
.section-bullets-bottom li:nth-child(2n+1).white-timeline.active, .section-bullets-bottom li:nth-child(2n+1).white-timeline.active:after {border-top-color: '.$first_post_color.';}
.section-bullets-bottom li:nth-child(2n).white-timeline.active, .section-bullets-bottom li:nth-child(2n).white-timeline.active:after {border-top-color: '.$second_post_color.';}

.section-bullets-right li:nth-child(2n+1).white-timeline.active:after, .section-bullets-right li:nth-child(2n+1).white-timeline.active {border-left-color: '.$first_post_color.';}
.section-bullets-right li:nth-child(2n).white-timeline.active:after, .section-bullets-right li:nth-child(2n).white-timeline.active {border-left-color: '.$second_post_color.';}

.section-bullets-left li:nth-child(2n+1).white-timeline.active:after, .section-bullets-left li:nth-child(2n+1).white-timeline.active {border-right-color: '.$first_post_color.';}
.section-bullets-left li:nth-child(2n).white-timeline.active:after, .section-bullets-left li:nth-child(2n).white-timeline.active {border-right-color: '.$second_post_color.';}

.cool-timeline .timeline-post.even .timeline-content h2.content-title-2,
.cool-timeline .timeline-post.even .timeline-content h2.content-title-2 a,
.cool-timeline.white-timeline .timeline-post.even .timeline-content h2.content-title-simple,
.cool-timeline.white-timeline .timeline-post.even .timeline-content h2.content-title-simple a
{
    color:'.$first_post_color.';
}';

 $styles.='.cool-timeline.white-timeline .timeline-post.even .timeline-content .content-title:before, .main-design-3 .cool-timeline.light-timeline .timeline-post.even .timeline-content:before, .main-design-3 .cool-timeline.dark-timeline .timeline-post.even .timeline-content:before, .main-design-3 .cool-timeline.white-timeline .timeline-post.even .timeline-content:before{
    border-right-color:'.$first_post_color.';
}
.main-design-3 .cool-timeline.white-timeline.compact .timeline-post.timeline-mansory.ctl-left .timeline-content, .main-design-3 .cool-timeline.dark-timeline.compact .timeline-post.timeline-mansory.ctl-left .timeline-content {
	border-right:6px solid '.$first_post_color.';
	border-left:0;
}
.main-design-3 .cool-timeline.white-timeline.compact .timeline-post.timeline-mansory.ctl-right .timeline-content, .main-design-3 .cool-timeline.dark-timeline.compact .timeline-post.timeline-mansory.ctl-right .timeline-content {
	border-left:6px solid '.$second_post_color.';
	border-right:0;
}
.main-design-3 .cool-timeline.white-timeline.compact .timeline-post.timeline-mansory.ctl-right .timeline-content:before, .main-design-3 .cool-timeline.dark-timeline.compact .timeline-post.timeline-mansory.ctl-right .timeline-content:before {
    border-right-color:'.$second_post_color.';
	border-left-color:transparent;
}
.main-design-3 .cool-timeline.white-timeline.compact .timeline-post.timeline-mansory.ctl-left .timeline-content:before, .main-design-3 .cool-timeline.dark-timeline.compact .timeline-post.timeline-mansory.ctl-left .timeline-content:before {
    border-left-color:'.$first_post_color.';
	border-right-color:transparent;
}
.cool-timeline.white-timeline.compact .timeline-post.timeline-mansory.ctl-left .timeline-content .content-title:after
{
	border-left-color:'.$first_post_color.';
}
.cool-timeline.white-timeline.compact .timeline-post.timeline-mansory.ctl-right .timeline-content .content-title:after {
    border-right-color:'.$second_post_color.';
}
.cool-timeline.white-timeline  .timeline-post.even .icon-dot-full, .cool-timeline.one-sided.white-timeline .timeline-post.even .icon-dot-full, .cool-timeline.white-timeline.compact .timeline-post.ctl-left .icon-dot-full, .cool-timeline.white-timeline.compact  .timeline-post.ctl-left .timeline-content .content-title, .cool-timeline.white-timeline.compact .timeline-post.icons_yes.ctl-left .timeline-icon, .main-design-3 .cool-timeline.dark-timeline.compact .timeline-post.timeline-mansory.ctl-left .timeline-content, .main-design-3 .cool-timeline.dark-timeline.compact .timeline-post.timeline-mansory.ctl-left .timeline-icon{
    background:'.$first_post_color.';
}
.cool-timeline.white-timeline.compact .cooltimeline_cont  .center-line { background: '.$line_color.'; }
.cool-timeline.white-timeline.compact .timeline-post.timeline-mansory.icons_yes  .iconbg-turqoise, .cool-timeline.white-timeline.compact .cooltimeline_cont .center-line:before, .cool-timeline.white-timeline.compact .cooltimeline_cont .center-line:after {
	border-color: '.$line_color.';
}
.cool-timeline.white-timeline  .timeline-post.even .icon-color-white, .cool-timeline.one-sided.white-timeline .timeline-post.even .icon-color-white, .main-design-3 .cool-timeline.dark-timeline .timeline-post.even .timeline-content, .main-design-3 .cool-timeline.dark-timeline .timeline-post.even .timeline-icon{
    background:'.$first_post_color.';
}';



 $styles.='.cool-timeline.white-timeline  .timeline-post.odd .timeline-content .content-title, .cool-timeline.white-timeline.compact .timeline-post.ctl-right .icon-dot-full, .cool-timeline.white-timeline.compact  .timeline-post.ctl-right .timeline-content .content-title, .cool-timeline.white-timeline.compact .timeline-post.icons_yes.ctl-right .timeline-icon, .main-design-3 .cool-timeline.dark-timeline .timeline-post.odd .timeline-content, .main-design-3 .cool-timeline.dark-timeline .timeline-post.odd .timeline-icon, .main-design-3 .cool-timeline.dark-timeline.compact .timeline-post.timeline-mansory.ctl-right .timeline-content, .main-design-3 .cool-timeline.dark-timeline.compact .timeline-post.timeline-mansory.ctl-right .timeline-icon {
    background:'.$second_post_color.';
}

.cool-timeline .timeline-post.odd .timeline-content h2.content-title-2,
.cool-timeline .timeline-post.odd .timeline-content h2.content-title-2 a,
.cool-timeline.white-timeline .timeline-post .timeline-content h2.content-title-simple,
.cool-timeline.white-timeline .timeline-post .timeline-content h2.content-title-simple a
{
    color:'.$second_post_color.';
}

.main-design-4 .cool-timeline.white-timeline.compact .timeline-post.timeline-mansory.ctl-right .timeline-content .content-title:before {
    border: 2px solid '.$second_post_color.';
}
.main-design-4 .cool-timeline.white-timeline.compact .timeline-post.timeline-mansory.ctl-left .timeline-content .content-title:before {
    border: 2px solid '.$first_post_color.';
}
.main-design-3 .cool-timeline.white-timeline.compact .timeline-post.ctl-left .timeline-content h2.content-title-2, .cool-timeline.white-timeline.compact .timeline-post.ctl-left .timeline-content h2.content-title-2 a, .cool-timeline.white-timeline.compact .timeline-post.ctl-left .timeline-content .clt-compact-date, .main-design-3 .cool-timeline.dark-timeline .timeline-post.even .timeline-meta .meta-details {
color:'.$first_post_color.';
}
.main-design-3 .cool-timeline.white-timeline.compact .timeline-post.ctl-right .timeline-content h2.content-title-2, .cool-timeline.white-timeline.compact .timeline-post.ctl-right .timeline-content h2.content-title-2 a, .cool-timeline.white-timeline.compact .timeline-post.ctl-right .timeline-content .clt-compact-date, .main-design-3 .cool-timeline.dark-timeline .timeline-post.odd .timeline-meta .meta-details {
color:'.$second_post_color.';
}
.main-design-3 .cool-timeline.white-timeline .timeline-post.even .timeline-content, 
.main-design-3 .cool-timeline.light-timeline .timeline-post.even .timeline-content, .main-design-3 .cool-timeline.dark-timeline .timeline-post.even .timeline-content {
    border-left: 6px solid '.$first_post_color.';
}
.main-design-3 .cool-timeline.white-timeline .timeline-post.odd .timeline-content, 
.main-design-3 .cool-timeline.light-timeline .timeline-post.odd .timeline-content, .main-design-3 .cool-timeline.dark-timeline .timeline-post.odd .timeline-content
 { border-right: 6px solid '.$second_post_color.';
}
.main-design-3 .cool-timeline.white-timeline .timeline-post.even .timeline-content .content-title, .main-design-3 .cool-timeline.white-timeline .timeline-post.odd .timeline-content .content-title {
    background: none !important;
}
.main-design-3 .cool-timeline.white-timeline .timeline-post.even .timeline-content h2.content-title, .main-design-3 .cool-timeline.white-timeline .timeline-post.even .timeline-content h2.content-title a
{
    color:'.$first_post_color.';
}
.main-design-3 .cool-timeline.white-timeline .timeline-post.odd .timeline-content h2.content-title, .main-design-3 .cool-timeline.white-timeline .timeline-post.odd .timeline-content h2.content-title a
{
    color:'.$second_post_color.';
}
.main-design-3 .cool-timeline.white-timeline.one-sided .timeline-post.odd .timeline-content, .main-design-3 .cool-timeline.light-timeline.one-sided .timeline-post.odd .timeline-content, .main-design-3 .cool-timeline.dark-timeline.one-sided .timeline-post.odd .timeline-content {
    border-left: 6px solid '.$second_post_color.';
    border-right: 1px solid #ccc;
}';

 $styles.='.main-design-4 .cool-timeline .timeline-post.even .timeline-content .content-title:before {
    content: "";
    width: 27px;
    border: 2px solid #222;
    position: absolute;
    left: -25px;
    top: 27px;
    z-index:-1;
}
.main-design-4 .cool-timeline .timeline-post.odd .timeline-content .content-title:before {
    content: "";
    width: 27px;
    border: 2px solid #222;
    position: absolute;
    right: -25px;
    top: 27px;
    z-index:-1;
}
.main-design-4 .cool-timeline.white-timeline .timeline-post.even .timeline-content .content-title:before {
    border: 2px solid '.$first_post_color.';
}
.main-design-4 .cool-timeline.white-timeline .timeline-post.odd .timeline-content .content-title:before {
    border: 2px solid '.$second_post_color.';
}
.main-design-4 .cool-timeline.light-timeline .timeline-post .timeline-content .content-title:before, .main-design-4 .cool-timeline.light-timeline.one-sided .timeline-post .timeline-content .content-title:before {
    border: 2px solid #eaeaea;
}
.main-design-4 .cool-timeline.dark-timeline .timeline-post .timeline-content .content-title:before, .main-design-4 .cool-timeline.dark-timeline.one-sided .timeline-post .timeline-content .content-title:before {
    border: 2px solid #111;
}';

 $styles.='.cool-timeline.white-timeline  .timeline-post .icon-dot-full, .cool-timeline.one-sided.white-timeline .timeline-post .icon-dot-full{
    background:'.$second_post_color.';
}

.cool-timeline.white-timeline  .timeline-post .icon-color-white, .cool-timeline.one-sided.white-timeline .timeline-post .icon-color-white{
    background: '.$second_post_color.';
}
.cool-timeline.white-timeline .timeline-post.odd .timeline-content .content-title:before, .main-design-3 .cool-timeline.light-timeline .timeline-post.odd .timeline-content:before, .main-design-3 .cool-timeline.dark-timeline .timeline-post.odd .timeline-content:before, .main-design-3 .cool-timeline.white-timeline .timeline-post.odd .timeline-content:before {
    border-left-color:'.$second_post_color.';
}
.cool-timeline.white-timeline.one-sided .timeline-post.odd .timeline-content .content-title:before, .main-design-3 .cool-timeline.light-timeline.one-sided .timeline-post.odd .timeline-content:before, .main-design-3 .cool-timeline.dark-timeline.one-sided .timeline-post.odd .timeline-content:before, .main-design-3 .cool-timeline.white-timeline.one-sided .timeline-post.odd .timeline-content:before {
    border-right-color:'.$second_post_color.';
    border-left-color: transparent;
}
.main-design-3 .cool-timeline.white-timeline.one-sided .timeline-post.odd .timeline-content:before, .main-design-3 .cool-timeline.light-timeline.one-sided .timeline-post.odd .timeline-content:before, .main-design-3 .cool-timeline.dark-timeline.one-sided .timeline-post.odd .timeline-content:before {
    right:inherit;
    left:-30px;
}


.cool-timeline.white-timeline  .timeline-post.even .timeline-meta .meta-details{
    color:'.$first_post_color.';
}
.cool-timeline.white-timeline  .timeline-post.odd .timeline-meta .meta-details{
    color:'.$second_post_color.';
}

.cool-timeline .timeline-post .timeline-content .content-title span{
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.cool-timeline .timeline-post .timeline-content .content-details { margin: 0; }
.cool-timeline .timeline-post .timeline-content .content-title {
    min-height: 50px;
    line-height:normal;
}

.ctl-popup-content h2.popup-content-title
{
font-size:calc('.$post_title_s.' - 3px)!important;
'.$ctl_post_title_typo_all.';
}
.ctl-popup-content .popup-posted-date,
.ctl-popup-content .popup-sublabels,
.main-design-7.compact-wrapper .minimal-date.clt-meta-date
{
    '.$ctl_date_typo_all.';
    color:'.$ctl_date_color.';
}
.ctl-popup-content .content-details p {
    '.$ctl_post_content_typo_all.';
}
';



 $styles.='.cool_timeline .avatar_container img.center-block.img-responsive.img-circle{
border:4px solid '.$line_color.';
}
img.center-block.img-responsive.img-circle{
    width:200px;
    height:200px;
}
.cool-timeline.white-timeline.one-sided .timeline-year:before {
    content: "";
    width: 42px;
    background: '.$line_color.';
    border: 2px solid '.$line_color.';
    position: absolute;
    left: -50px;
    top: 48.5%;
   }
.main-design-2 .cool-timeline.one-sided .timeline-year:before, .main-design-3 .cool-timeline.one-sided .timeline-year:before, .main-design-4 .cool-timeline.one-sided .timeline-year:before {
    width: 25px;
    left: -32px;
   }';

$styles.='/* @responsive styling
----------------------------------------------- */
@media (max-width: 860px) {
	.main-design-6 .cool-timeline .timeline-post .timeline-icon.design-6-icon {background:'.$line_color.';color:#fff;}
	.main-design-4 .cool-timeline.white-timeline.compact .timeline-post.timeline-mansory.ctl-left.even .timeline-content .content-title:before {
    border: 2px solid '.$first_post_color.';
	}
	.main-design-4 .cool-timeline.white-timeline.compact .timeline-post.timeline-mansory.ctl-left.odd .timeline-content .content-title:before {
    border: 2px solid '.$second_post_color.';
	}
	.main-design-3 .cool-timeline.white-timeline.compact .timeline-post.ctl-left.even .timeline-content h2.content-title-2, .cool-timeline.white-timeline.compact .timeline-post.ctl-left.even .timeline-content h2.content-title-2 a, .cool-timeline.white-timeline.compact .timeline-post.ctl-left.even .timeline-content .clt-compact-date {
	color:'.$first_post_color.';
	}
	.main-design-3 .cool-timeline.white-timeline.compact .timeline-post.ctl-left.odd .timeline-content h2.content-title-2, .cool-timeline.white-timeline.compact .timeline-post.ctl-left.odd .timeline-content h2.content-title-2 a, .cool-timeline.white-timeline.compact .timeline-post.ctl-left.odd .timeline-content .clt-compact-date {
	color:'.$second_post_color.';
	}
	.main-design-3 .cool-timeline.white-timeline.compact .timeline-post.timeline-mansory.ctl-left.even .timeline-content, .main-design-3 .cool-timeline.dark-timeline.compact .timeline-post.timeline-mansory.ctl-left.even .timeline-content {
	border-left:6px solid '.$first_post_color.';
	border-right:0;
	}
	.main-design-3 .cool-timeline.white-timeline.compact .timeline-post.timeline-mansory.ctl-left.odd .timeline-content, .main-design-3 .cool-timeline.dark-timeline.compact .timeline-post.timeline-mansory.ctl-left.odd .timeline-content {
	border-left:6px solid '.$second_post_color.';
	border-right:0;
	}
	.main-design-3 .cool-timeline.white-timeline.compact .timeline-post.timeline-mansory.ctl-left.odd .timeline-content:before, .main-design-3 .cool-timeline.dark-timeline.compact .timeline-post.timeline-mansory.ctl-left.odd .timeline-content:before {
    border-right-color:'.$second_post_color.';
	border-left-color:transparent;
	}
	.main-design-3 .cool-timeline.white-timeline.compact .timeline-post.timeline-mansory.ctl-left.even .timeline-content:before, .main-design-3 .cool-timeline.dark-timeline.compact .timeline-post.timeline-mansory.ctl-left.even .timeline-content:before {
    border-right-color:'.$first_post_color.';
	border-left-color:transparent;
	}
	.cool-timeline.white-timeline.compact .timeline-post.ctl-left.even .timeline-content .content-title, .cool-timeline.white-timeline.compact .timeline-post.ctl-left.even .icon-dot-full, .cool-timeline.white-timeline.compact .timeline-post.icons_yes.ctl-left.even .timeline-icon, .main-design-3 .cool-timeline.dark-timeline.compact .timeline-post.timeline-mansory.ctl-left.even .timeline-content, .main-design-3 .cool-timeline.dark-timeline.compact .timeline-post.timeline-mansory.ctl-left.even .timeline-icon {
		background:'.$first_post_color.';
	}
	.cool-timeline.white-timeline.compact .timeline-post.ctl-left.odd .timeline-content .content-title, .cool-timeline.white-timeline.compact .timeline-post.ctl-left.odd .icon-dot-full, .cool-timeline.white-timeline.compact .timeline-post.icons_yes.ctl-left.odd .timeline-icon, .main-design-3 .cool-timeline.dark-timeline.compact .timeline-post.timeline-mansory.ctl-left.odd .timeline-content, .main-design-3 .cool-timeline.dark-timeline.compact .timeline-post.timeline-mansory.ctl-left.odd .timeline-icon {
		background:'.$second_post_color.';
	}
	.cool-timeline.white-timeline.compact .timeline-post.timeline-mansory.ctl-left.odd .timeline-content .content-title:after {
		border-right-color:'.$second_post_color.';
	}
	.cool-timeline.white-timeline.compact .timeline-post.timeline-mansory.ctl-left.even .timeline-content .content-title:after {
		border-right-color:'.$first_post_color.';
	}
	
    .cool-timeline .light-grey-post .timeline-content:after,
    .cool-timeline .light-grey-post .timeline-content:before,
    .cool-timeline .light-grey-post .even .timeline-content:after,
    .cool-timeline .light-grey-post .even .timeline-content:before,
.cool-timeline.white-timeline.compact .timeline-post.timeline-mansory.ctl-left .timeline-content .content-title:after	{
        border-right-color:'.$first_post_color.';
        border-left-color:transparent;
    }
.cool-timeline .custom-pagination {
    margin-left: 30px;
}
.cool-timeline.one-sided .timeline-post .icon-dot-full {
    margin: 27px 0 0 -7px;
}

.main-design-3 .cool-timeline.white-timeline .timeline-post.odd .timeline-content, .main-design-3 .cool-timeline.light-timeline .timeline-post.odd .timeline-content, .main-design-3 .cool-timeline.dark-timeline .timeline-post.odd .timeline-content {
    border-left: 6px solid '.$second_post_color.';
    border-right: 1px solid #ccc;
}
.main-design-3 .cool-timeline .timeline-post.odd .timeline-content:before {
    right:inherit;
    left:-30px;
}';

$styles.='.main-design-3 .cool-timeline.white-timeline .timeline-post.odd .timeline-content:before, .main-design-3 .cool-timeline.light-timeline .timeline-post.odd .timeline-content:before, .main-design-3 .cool-timeline.dark-timeline .timeline-post.odd .timeline-content:before {
    border-right-color: '.$second_post_color.';
    border-left-color: transparent;
}

.main-design-4 .cool-timeline .timeline-post.even .timeline-content .content-title:before, .main-design-4 .cool-timeline .timeline-post.odd .timeline-content .content-title:before { top:33px; }

.main-design-4 .cool-timeline.light-timeline.one-sided .timeline-post.even .timeline-content .content-title:before, .main-design-4 .cool-timeline.light-timeline.one-sided .timeline-post.odd .timeline-content .content-title:before, .main-design-4 .cool-timeline.dark-timeline.one-sided .timeline-post.even .timeline-content .content-title:before, .main-design-4 .cool-timeline.dark-timeline.one-sided .timeline-post.odd .timeline-content .content-title:before { top:27px; }

    .cool-timeline.white-timeline .timeline-post.odd .timeline-content .content-title:before{
        border-right-color:'.$second_post_color.';
        border-left-color:transparent;
    }
   .cool-timeline .timeline-post.odd .timeline-content .content-title:before{
        border-left-color:transparent;
    }
    .cool-timeline.light-timeline .timeline-post.odd .timeline-content .content-title:before
   {
       border-right-color: #eaeaea;
       left: -25px;
       right: inherit;
   }
   .cool-timeline.dark-timeline .timeline-post.odd .timeline-content .content-title:before
   {
       border-right-color: #000;
   }';

 $styles.='.cool-timeline-horizontal.white-timeline.ht-design-4 .clt_caru_slider ul.slick-slider .slick-list li.slick-slide .timeline-post {
    background: -moz-linear-gradient(90deg, rgba(255, 255, 255, 0) 0%, '.$content_bg_color.' 100%);
    background: -webkit-gradient(linear, left top, left bottom, color-stop(0%, '.$content_bg_color.'), color-stop(100%, rgba(255, 255, 255, 0)));
    background: -webkit-linear-gradient(90deg, rgba(255, 255, 255, 0) 0%, '.$content_bg_color.' 100%);
    background: -o-linear-gradient(90deg, rgba(255, 255, 255, 0) 0%, '.$content_bg_color.' 100%);
    background: -ms-linear-gradient(90deg, rgba(255, 255, 255, 0) 0%, '.$content_bg_color.' 100%);
    background: linear-gradient(0deg, rgba(255, 255, 255, 0) 0%, '.$content_bg_color.' 100%);';

    $styles.='.cool-timeline-horizontal .clt_carousel_slider ul.slick-slider .slick-list li:before, .cool-timeline-horizontal .wrp-desgin-4 ul.ctl_h_nav  .slick-list li:before  {
    background-color:'.$line_color.';
    background-image: -webkit-linear-gradient(top, '.$line_color.' 0%, '.$line_color.' 8%, '.$line_color.' 92%, '.$line_color.' 100%);
    background-image: -moz-linear-gradient(top, '.$line_color.' 0%, '.$line_color.' 8%,'.$line_color.' 92%, '.$line_color.' 100%);
    background-image: -ms-linear-gradient(top, '.$line_color.' 0%, '.$line_color.' 8%, '.$line_color.' 92%, '.$line_color.' 100%);}';




 $styles.='.cool-timeline.light-timeline .timeline-year:before {
    content: "";
    width: 42px;
    background: #eaeaea;
    border: 2px solid #eaeaea;
    position: absolute;
    left: -50px;
    top: 48.5%;
   }
   .cool-timeline.light-timeline .timeline-year, .cool-timeline.dark-timeline .timeline-year, .cool-timeline.white-timeline .timeline-year
   {
    left:100px;
   }
   .cool-timeline.light-timeline.one-sided .timeline-year, .cool-timeline.light-timeline.one-sided .timeline-year, .cool-timeline.one-sided .timeline-year
   {
    left:85px;
   }

   .cool-timeline.dark-timeline .timeline-year:before {
    content: "";
    width: 42px;
    background: #222;
    border: 2px solid #000;
    position: absolute;
    left: -50px;
    top: 48.5%;
   }
   .cool-timeline.white-timeline .timeline-year:before {
    content: "";
    width: 42px;
    border: 2px solid '.$line_color.';
    position: absolute;
    left: -50px;
    top: 48.5%;
   }
.main-design-2 .cool-timeline .timeline-year:before, .main-design-3 .cool-timeline .timeline-year:before, .main-design-4 .cool-timeline .timeline-year:before {
    width: 25px;
    left: -32px;
}';
$styles.=' .cool-timeline .light-grey-post .timeline-content:after,
    .cool-timeline .light-grey-post .timeline-content:before,
    .cool-timeline .light-grey-post .odd .timeline-content:after,
    .cool-timeline .light-grey-post .odd .timeline-content:before {
        border-right-color:'.$second_post_color.';
        border-left-color:transparent;
    }
} ';
 $styles.='';
 
 $custom_css= preg_replace('/\\\\/', '', $custom_styles);
 $final_css=self::clt_minify_css($styles);
 wp_add_inline_style( 'ctl_styles',$custom_css.' '.$final_css);
      

        }


      
		
     public static function ctl_navigation_styles() {
            $ctl_options_arr = get_option('cool_timeline_settings');           
            $navigation_position = isset($ctl_options_arr['navigation_settings']['navigation_position']) ? $ctl_options_arr['navigation_settings']['navigation_position'] : 'right';
            $output = '<style type="text/css">
                    .ctl-bullets-container {
                display: block;
                position: fixed;
                ' . $navigation_position . ': 0;
                height: 100%;
                z-index: 1049;
                font-weight: normal;
                height: 70vh;
                overflow-x: hidden;
                overflow-y: auto;
                margin: 15vh auto;
            }</style>';

            echo $output;
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