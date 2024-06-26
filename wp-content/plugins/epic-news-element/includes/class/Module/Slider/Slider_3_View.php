<?php
/**
 * @author : Jegtheme
 */
namespace EPIC\Module\Slider;

Class Slider_3_View extends SliderViewAbstract
{
    public function content($results)
    {
        $content = '';
        foreach($results as $key => $post)
        {
            $primary_category = $this->get_primary_category($post->ID);
            $post_thumbnail_id = get_post_thumbnail_id( $post->ID );
            $image = apply_filters('epic_single_image_owl', $post_thumbnail_id, 'epic-360x504');

            $content .=
                "<div " . epic_post_class("jeg_slide_item", $post->ID) . ">
                    " . epic_edit_post( $post->ID ) . "
                    <a href=\"" . get_the_permalink($post) . "\">
                        {$image}
                    </a>
                    <div class=\"jeg_slide_caption\">
                        <div class=\"jeg_caption_container\">
                            <div class=\"jeg_post_category\">
                                {$primary_category}
                            </div>
                            <h2 class=\"jeg_post_title\">
                                <a href=\"" . get_the_permalink($post) . "\">" . get_the_title($post) . "</a>
                            </h2>
                            <p class=\"jeg_post_excerpt\"> {$this->get_excerpt($post)} </p>
                            {$this->render_meta($post)}
                        </div>
                    </div>
                </div>";
        }

        return $content;
    }

    public function render_element($result, $attr)
    {
        if(!empty($result))
        {
            $content        = $this->content($result);
            $column_class   = $this->get_module_column_class($attr);
            $autoplay_delay = isset( $attr['autoplay_delay']['size'] ) ? $attr['autoplay_delay']['size'] : $attr['autoplay_delay'];
            $number_item    = isset( $attr['number_item']['size'] ) ? $attr['number_item']['size'] : $attr['number_item'];

	        $space       = "margin-right: 5px;";
	        $placeholder = '<div class=\'thumbnail-inner\' style="' . $space . 'flex: 0 1 30px;"><div class=\'thumbnail-container\'></div></div>';
	        for ( $i = 1; $i <= $number_item; $i ++ ) {
		        $placeholder .= "<div class='thumbnail-inner' style='$space'><div class='thumbnail-container size-1400'></div></div>";
	        }
	        $placeholder .= '<div class=\'thumbnail-inner\' style="flex: 0 1 30px;"><div class=\'thumbnail-container\'></div></div>';

            $output =
                "<div {$this->element_id($attr)} class=\"jeg_slider_wrapper jeg_slider_type_3_wrapper {$column_class} {$this->unique_id} {$this->get_vc_class_name()} {$attr['el_class']}\">
                    <div class='jeg_slider_placeholder'>
						<div class='thumbnail-wrapper'>
							{$placeholder}
						</div>
					</div>
                    <div class=\"jeg_slider_type_3 jeg_slider slider-carousel\" data-items=\"{$number_item}\" data-autoplay=\"{$attr['enable_autoplay']}\" data-delay=\"{$autoplay_delay}\">
                        {$content}
                    </div>
                </div>";
            return $output;
        } else {
            return $this->empty_content();
        }
    }

    public function render_meta($post)
    {
        $output = '';

        if( epic_get_option('show_block_meta', true) && epic_get_option('show_block_meta_date', true) )
        {
            $time     = $this->format_date($post);
            $output =
                "<div class=\"jeg_post_meta\">
                    <span class=\"jeg_meta_date\"><i class=\"fa fa-clock-o\"></i> {$time}</span>
                </div>";
        }

        return $output;
    }
}
