<?php
/*
Plugin Name: MU Plugin Coworking
Description: DIfférents outils et extensions pour le site
Author: GF
Version: 1.0
*/

include plugin_dir_path(__FILE__) . 'coworking-app/app.php';
include plugin_dir_path(__FILE__) . 'coworking-app/app-auth.php';
include plugin_dir_path(__FILE__) . 'coworking-app/app-droits.php';
include plugin_dir_path(__FILE__) . 'coworking-app/app-session.php';

add_action('init', function () {
    if (!function_exists('acf_get_pro_field_types')) {
        function acf_get_pro_field_types()
        {
            return array(
                'clone'            => array(
                    'name'          => 'clone',
                    'label'         => _x('Clone', 'noun', 'acf'),
                    'doc_url'       => acf_add_url_utm_tags('https://www.advancedcustomfields.com/resources/clone/', 'docs', 'field-type-selection'),
                    'preview_image' => acf_get_url() . '/assets/images/field-type-previews/field-preview-clone.png',
                    'description'   => __('This allows you to select and display existing fields. It does not duplicate any fields in the database, but loads and displays the selected fields at run-time. The Clone field can either replace itself with the selected fields or display the selected fields as a group of subfields.', 'acf'),
                    'tutorial_url'  => acf_add_url_utm_tags('https://www.advancedcustomfields.com/resources/how-to-use-the-clone-field/', 'docs', 'field-type-selection'),
                    'category'      => 'layout',
                    'pro'           => true,
                ),
                'flexible_content' => array(
                    'name'          => 'flexible_content',
                    'label'         => __('Flexible Content', 'acf'),
                    'doc_url'       => acf_add_url_utm_tags('https://www.advancedcustomfields.com/resources/flexible-content/', 'docs', 'field-type-selection'),
                    'preview_image' => acf_get_url() . '/assets/images/field-type-previews/field-preview-flexible-content.png',
                    'description'   => __('This provides a simple, structured, layout-based editor. The Flexible Content field allows you to define, create and manage content with total control by using layouts and subfields to design the available blocks.', 'acf'),
                    'tutorial_url'  => acf_add_url_utm_tags('https://www.advancedcustomfields.com/resources/building-layouts-with-the-flexible-content-field-in-a-theme/', 'docs', 'field-type-selection'),
                    'category'      => 'layout',
                    'pro'           => true,
                ),
                'gallery'          => array(
                    'name'          => 'gallery',
                    'label'         => __('Gallery', 'acf'),
                    'doc_url'       => acf_add_url_utm_tags('https://www.advancedcustomfields.com/resources/gallery/', 'docs', 'field-type-selection'),
                    'preview_image' => acf_get_url() . '/assets/images/field-type-previews/field-preview-gallery.png',
                    'description'   => __('This provides an interactive interface for managing a collection of attachments. Most settings are similar to the Image field type. Additional settings allow you to specify where new attachments are added in the gallery and the minimum/maximum number of attachments allowed.', 'acf'),
                    'tutorial_url'  => acf_add_url_utm_tags('https://www.advancedcustomfields.com/resources/how-to-use-the-gallery-field/', 'docs', 'field-type-selection'),
                    'category'      => 'content',
                    'pro'           => true,
                ),
                'repeater'         => array(
                    'name'          => 'repeater',
                    'label'         => __('Repeater', 'acf'),
                    'doc_url'       => acf_add_url_utm_tags('https://www.advancedcustomfields.com/resources/repeater/', 'docs', 'field-type-selection'),
                    'preview_image' => acf_get_url() . '/assets/images/field-type-previews/field-preview-repeater.png',
                    'description'   => __('This provides a solution for repeating content such as slides, team members, and call-to-action tiles, by acting as a parent to a set of subfields which can be repeated again and again.', 'acf'),
                    'tutorial_url'  => acf_add_url_utm_tags('https://www.advancedcustomfields.com/resources/repeater/how-to-use-the-repeater-field/', 'docs', 'field-type-selection'),
                    'category'      => 'layout',
                    'pro'           => true,
                ),
            );
        }
    }
});
