<?php

function add_custom_shortcodes_to_template($shortcodes)
{
    foreach (['viwec_register_preview_shortcode', 'viwec_register_replace_shortcode'] as $filter) {
        add_filter($filter, function ($codes, $object, $args) use ($shortcodes) {

            return $shortcodes;
        }, 10, 3);
    }
}
