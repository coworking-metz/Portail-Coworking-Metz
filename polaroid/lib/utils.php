<?php




/**
 * Outputs an image with the correct headers.
 * If the image is a PNG, it converts it to JPG before outputting.
 * 
 * @param string $imagePath Path to the image file.
 */
function outputImageWithHeaders($imagePath) {
    if (!file_exists($imagePath)) {
        header('HTTP/1.0 404 Not Found');
        echo 'File not found.';
        return;
    }

    $fileInfo = pathinfo($imagePath);
    $extension = strtolower($fileInfo['extension']);

    if ($extension === 'png') {
        $image = imagecreatefrompng($imagePath);
        header('Content-Type: image/jpeg');
        imagejpeg($image);
        imagedestroy($image);
    } else {
        $mimeType = mime_content_type($imagePath);
        header("Content-Type: $mimeType");
        readfile($imagePath);
    }
    exit;
}
