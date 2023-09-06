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

include plugin_dir_path(__FILE__) . 'polaroid/polaroid.php';

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

function upload_image_to_wp_media($file_path)
{
    // Check if the file exists
    if (!file_exists($file_path)) {
        return new WP_Error('no_file', 'File does not exist');
    }

    // Generate the hash of the file's content
    $file_hash = md5_file($file_path);

    // Check if the hash already exists in the attachments
    $existing_attachments = get_posts(array(
        'post_type' => 'attachment',
        'meta_query' => array(
            array(
                'key' => 'hash',
                'value' => $file_hash
            )
        )
    ));

    if ($existing_attachments && isset($existing_attachments[0])) {
        return $existing_attachments[0]->ID; // Return the existing attachment's ID
    }

    // Determine the type of the image
    $image_info = getimagesize($file_path);
    if (false === $image_info) {
        return new WP_Error('invalid_image', 'Unable to determine image type.');
    }

    // Map image type to appropriate file extension
    $image_extensions = array(
        IMAGETYPE_GIF => 'gif',
        IMAGETYPE_JPEG => 'jpg',
        IMAGETYPE_PNG => 'png',
        IMAGETYPE_WEBP => 'webp',
        // ... add other image types if needed
    );

    if (!isset($image_extensions[$image_info[2]])) {
        return new WP_Error('unsupported_image_type', 'The image type is not supported.');
    }

    // Create a temporary copy of the file with the correct extension
    $temp_dir = wp_tempnam();  // This will give a unique file name in the temp directory with a ".tmp" extension
    $temp_file_path = str_replace('.tmp', '', $temp_dir . '.' . $image_extensions[$image_info[2]]);
    copy($file_path, $temp_file_path);

    // Create an array of the uploaded file details
    $file_array = array(
        'name' => basename($temp_file_path),
        'tmp_name' => $temp_file_path
    );
    // Set the default error handler
    $overrides = array('test_form' => false);

    // Use the WordPress function to handle the upload
    $uploaded_file = media_handle_sideload($file_array, 0, '', $overrides);

    // Remove the temporary file
    @unlink($temp_file_path);

    // Check for upload errors
    if (is_wp_error($uploaded_file)) {
        return $uploaded_file;
    }

    // Save the hash to the attachment's custom fields
    add_post_meta($uploaded_file, 'hash', $file_hash, true);

    // Return the attachment ID
    return $uploaded_file;
}


function imagecreatefromfile($filepath) {
    // Check if the file exists
    if (!file_exists($filepath)) {
        return false;
    }

    // Determine the type of image
    $type = exif_imagetype($filepath);

    switch ($type) {
        case IMAGETYPE_JPEG:
            return imagecreatefromjpeg($filepath);
        case IMAGETYPE_PNG:
            return imagecreatefrompng($filepath);
        case IMAGETYPE_WEBP:
            return imagecreatefromwebp($filepath);
        // Add more cases as needed, like for GIF, BMP, etc.
        default:
            return false; // Or throw an exception, based on your needs.
    }
}