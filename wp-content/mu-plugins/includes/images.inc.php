<?php



/**
 * Converts a PNG image to a JPEG image
 *
 * @param string $from The path to the PNG image to convert
 * @param string|bool $to The path to save the JPEG image to
 * @param int $quality The quality of the JPEG image
 *
 * @return string|bool The path to the JPEG image if successful, otherwise false
 */
function pngToJpeg($from, $to = false, $quality = 80)
{

    if (!$to) {
        $to = str_replace('.png', '.jpg', $from);
    }
    $image = imagecreatefromfile($from);
    if (!$image) return false;

    $ret = imagejpeg($image, $to, $quality);
    imagedestroy($image);

    return $ret ? $to : false;
}