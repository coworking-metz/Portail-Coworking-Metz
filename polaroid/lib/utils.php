<?php



/**
 * Outputs an image with the correct headers.
 * If the image is a PNG, it converts it to JPG before outputting.
 * Resizes the image to the given width while keeping the aspect ratio if width is set.
 * 
 * @param string $imagePath Path to the image file.
 * @param int|null $width Optional width to resize the image.
 */
function outputImageWithHeaders($imagePath, $width = null) {
    if (!file_exists($imagePath)) {
        header('HTTP/1.0 404 Not Found');
        echo 'File not found.';
        return;
    }

    $fileInfo = pathinfo($imagePath);
    $extension = strtolower($fileInfo['extension']);

    if ($extension === 'png') {
        $image = imagecreatefrompng($imagePath);
    } else {
        $image = imagecreatefromstring(file_get_contents($imagePath));
    }

    if ($width && imagesx($image) > $width) {
        $height = (int) (imagesy($image) * ($width / imagesx($image)));
        $resizedImage = imagescale($image, $width, $height);
        imagedestroy($image);
        $image = $resizedImage;
    }
    if ($extension === 'png') {
        header('Content-Type: image/jpeg');
        imagejpeg($image);
    } else {
        $mimeType = mime_content_type($imagePath);
        header("Content-Type: $mimeType");
        if ($mimeType === 'image/jpeg') {
            imagejpeg($image);
        } elseif ($mimeType === 'image/gif') {
            imagegif($image);
        } elseif ($mimeType === 'image/png') {
            imagepng($image);
        }
    }

    imagedestroy($image);
    exit;
}
