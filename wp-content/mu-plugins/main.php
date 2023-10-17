<?php
/*
Plugin Name: MU Plugin Coworking
Description: DIfférents outils et extensions pour le site
Author: GF
Version: 1.0
*/


define('ONE_MINUTE', 60);
define('FIVE_MINUTES', 5 * ONE_MINUTE);
define('ONE_HOUR', ONE_MINUTE * 60);
define('HALF_HOUR', ONE_HOUR / 2);
define('ONE_DAY', ONE_HOUR * 24);
define('ONE_WEEK', ONE_DAY * 7);
define('ONE_MONTH', ONE_DAY * 31);


include plugin_dir_path(__FILE__) . 'coworking-app/app.php';

include plugin_dir_path(__FILE__) . 'colonnes/colonnes.php';
include plugin_dir_path(__FILE__) . 'mon-compte/mon-compte.php';
include plugin_dir_path(__FILE__) . 'polaroid/polaroid.php';
include plugin_dir_path(__FILE__) . 'notifications/notifications.php';

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


function insert_attachment_from_file($file_path, $data = [], $meta = [])
{

    if (!$file_path) {
        return false;
    }

    $hash = md5_file($file_path);

    if ($attach = get_attachement_by_hash($hash)) {
        $attach_id = $attach->ID;
    }
    if (!$attach) {
        $image_info = getimagesize($file_path);
        $image_extensions = array(
            IMAGETYPE_GIF => 'gif',
            IMAGETYPE_JPEG => 'jpg',
            IMAGETYPE_PNG => 'png',
            IMAGETYPE_WEBP => 'webp',
        );

        $ext = $image_extensions[$image_info[2]] ?? false;
        if (!$ext) {
            return;
        }
        $upload = wp_upload_bits(basename($file_path).'.'.$ext, null, file_get_contents($file_path));
        if (!empty($upload['error'])) {
            return false;
        }

        $file_path = $upload['file'];


        $file_name = basename($file_path);
        $file_type = wp_check_filetype($file_name, null);
        $attachment_title = sanitize_file_name(pathinfo($file_name, PATHINFO_FILENAME));
        $wp_upload_dir = wp_upload_dir();

        $post_info = array(
            'guid'           => $wp_upload_dir['url'] . '/' . $file_name,
            'post_mime_type' => $file_type['type'],
            'post_title'     => $attachment_title,
            'post_content'   => '',
            'post_status'    => 'inherit',
        );

        // Create the attachment
        $attach_id = wp_insert_attachment($post_info, $file_path);


        // Include image.php
        require_once(ABSPATH . 'wp-admin/includes/image.php');

        // Define attachment metadata
        $attach_data = wp_generate_attachment_metadata($attach_id, $file_path);

        // Assign metadata to attachment
        wp_update_attachment_metadata($attach_id,  $attach_data);
    }
    if (count($data)) {
        $data['ID'] = $attach_id;
        if (isset($data['alt'])) {
            $alt = $data['alt'];
            unset($data['alt']);
            update_post_meta($attach_id, '_wp_attachment_image_alt', $alt);
        }
        wp_update_post($data);
    }

    update_field('hash', $hash, $attach_id);

    foreach ($meta as $k => $v) {
        update_field($k, $v, $attach_id);
    }
    return $attach_id;
}

/**
 * Get an attachement who was previously imported by insert_attachment_from_url from an external url
 *
 * @param  mixed $url
 * @return mixed
 */
function get_attachement_by_hash($hash)
{
    $attach = get_post_by_meta('hash', $hash, 'attachment');

    return $attach;
}


/**
 * Get Post object by post_meta query
 *
 * @use         $post = get_post_by_meta( array( meta_key = 'page_name', 'meta_value = 'contact' ) )
 * @since       1.0.4
 * @return      Object      WP post object
 */
function get_post_by_meta($key, $value = null, $type = 'post', $status = false)
{

    $posts = get_posts_by_meta($key, $value, $type, 1, $status);

    if (!$posts || is_wp_error($posts)) return false;

    return $posts[0];
}


function get_posts_by_meta($key, $value = null, $type = 'post', $limit = -1, $status = false, $exclude = [])
{

    $args = array(
        'meta_query'        => array(
            array(
                'key'       => $key
            )
        ),
        'post_type'         => $type,
        'posts_per_page'    => $limit,
    );
    if ($exclude) {
        $args['post_status'] = $status;
    }
    if ($status) {
        $args['post_status'] = $status;
    }
    if (!is_null($value)) {
        $args['meta_query'][0]['value'] = $value;
    } else {
        $args['meta_query'][0]['value'] = [''];
        $args['meta_query'][0]['compare'] = 'NOT IN';
    }
    // run query ##
    $posts = get_posts($args);

    // check results ##
    if (!$posts || is_wp_error($posts)) return false;

    return $posts;
}


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

    $temp_file = polaroid_tmpfile();  // This will give a unique file name in the temp directory with a ".tmp" extension
    $temp_file_path = $temp_file . '.' . $image_extensions[$image_info[2]];

    copy($file_path, $temp_file_path);


    $file_name = basename($temp_file_path);

    $upload = wp_upload_bits($file_name, null, file_get_contents($temp_file_path));

    // Remove the temporary file
    @unlink($temp_file_path);
    if (!empty($upload['error'])) {
        return false;
    }

    $file_type = wp_check_filetype($file_name, null);
    $attachment_title = sanitize_file_name(pathinfo($file_name, PATHINFO_FILENAME));
    $wp_upload_dir = wp_upload_dir();

    $post_info = array(
        'guid'           => $wp_upload_dir['url'] . '/' . $file_name,
        'post_mime_type' => $file_type['type'],
        'post_title'     => $attachment_title,
        'post_content'   => '',
        'post_status'    => 'inherit',
    );

    // Create the attachment
    $attach_id = wp_insert_attachment($post_info, $file_path);

    // Save the hash to the attachment's custom fields
    add_post_meta($attach_id, 'hash', $file_hash, true);

    // Return the attachment ID
    return $attach_id;
}


function imagecreatefromfile($filepath)
{
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
