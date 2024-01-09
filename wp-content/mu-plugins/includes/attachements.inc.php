<?php

/**
 * Insère une pièce jointe à partir d'un fichier.
 *
 * @param string $file_path Chemin vers le fichier à insérer.
 * @param array  $data      Données supplémentaires pour le post de la pièce jointe.
 * @param array  $meta      Métadonnées ACF pour la pièce jointe.
 * @param int    $width     Largeur pour redimensionner l'image. Par défaut null.
 * 
 * @return int|bool L'ID de la pièce jointe ou false en cas d'échec.
 */
function insert_attachment_from_file($file_path, $data = [], $meta = [], $width = null)
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
        $upload = wp_upload_bits(basename($file_path) . '.' . $ext, null, file_get_contents($file_path));
        if (!empty($upload['error'])) {
            return false;
        }

        $file_path = $upload['file'];

        if ($width) {
            // Redimensionner l'image
            $image = wp_get_image_editor($file_path);
            if (!is_wp_error($image)) {
                $image->resize($width, null, false);
                $image->save($file_path);
            }
        }

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
 * Télécharge une image dans la bibliothèque de médias WordPress.
 *
 * @param string $file_path Chemin d'accès au fichier à télécharger.
 *
 * @return int|WP_Error L'ID de l'attachement ou un objet WP_Error en cas d'erreur.
 */

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
