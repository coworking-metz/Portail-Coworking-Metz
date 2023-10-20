<?php

function add_custom_shortcodes_to_template($shortcodes)
{
    foreach (['viwec_register_preview_shortcode', 'viwec_register_replace_shortcode'] as $filter) {
        add_filter($filter, function ($codes, $object, $args) use ($shortcodes) {

            return $shortcodes;
        }, 10, 3);
    }
}



function charger_template_mail($template_id, $codes = [])
{
    add_custom_shortcodes_to_template($codes);

    $template = new VIWEC_Render_Email_Template(['template_id' => $template_id]);
    ob_start();
    $template->get_content();
    $message = ob_get_contents();
    ob_end_clean();
    return ['message'=>$message,'subject'=>$template->get_subject()];
}
