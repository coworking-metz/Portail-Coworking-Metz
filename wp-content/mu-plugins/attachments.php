<?php


add_filter('wp_handle_upload_prefilter', function ($file) {

    // if (current_user_can('upload-all-image-types')) return $file;

    if ($file['type'] == 'image/png') {
        if (pngTojpeg($file['tmp_name'], $file['tmp_name'])) {
            $file['type'] = 'image/jpeg';
            $file['name'] = str_ireplace('.png', '.jpg', $file['name']);
            $file['size'] = filesize($file['tmp_name']);
        }
    }
    return $file;
});
