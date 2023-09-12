<?php


$quality = 60;
$hd = $_GET['hd'] ?? false;
$uid = $_GET['uid'] ?? false;
$dynamique = isset($_GET['dynamique']);


// if ($uid) {
//     $target = __DIR__ . '/' . $uid . ($hd ? '-hd' : '') . '.jpg';
//     if (!$dynamique && file_exists($target)) {
//         polaroid_output($target);
//     };
// }


define('WP_USE_THEMES', false); // We don't want to use themes.
require('../wp-load.php');




// print_r($_GET);
$polaroid = $_GET['polaroid'] ?? false;
if ($polaroid) {
    $photo = polaroid_tmpphoto();
} else {
    $polaroid = polaroid_get($uid);
    if ($image = get_user_meta($uid, 'url_image_trombinoscope', true)) {
        $url = wp_get_attachment_url($image);
        if ($url) {
            polaroid_output(urlToPath($url));
        }
    }
    $photo = $polaroid['photo'];
}

list($width, $height) = getimagesize('./images/pola-vide.png');
$img = imagecreatetruecolor($width, $height);

$tmp = imagecreatefromfile($photo);
list($tmpWidth, $tmpHeight) = getimagesize($photo);


$mode = ($tmpWidth - $tmpHeight) > 100 ? 'landscape' : 'portrait';
$aspectRatio = $tmpWidth / $tmpHeight;
$bande = $height * 5.3 / 100;

if ($mode == 'landscape') {
    $maxHeight = $height * 75 / 100;
    $newHeight = $maxHeight;
    $newWidth = $maxHeight * $aspectRatio;
} else {
    // Calculate the new dimensions
    $maxWidth = $width - ($bande * 2);
    $newWidth = $maxWidth;
    $newHeight = $maxWidth / $aspectRatio;
}
if ($newHeight > $height) {
    $newHeight = $height;
    $newWidth = $height * $aspectRatio;
}

imagecopyresampled($img, $tmp, $bande, $bande + 2, 0, 0, $newWidth, $newHeight, $tmpWidth, $tmpHeight);
imagedestroy($tmp);

// 4. Open the './images/pola-vide.png' file and place it on top of everything in $img
$overlay = imagecreatefrompng('./images/pola-vide.png');
imagecopy($img, $overlay, 0, 0, 0, 0, $width, $height);
imagedestroy($overlay);


// Text to be added
$text = $polaroid['nom'];
$fontFile = './EvelethClean.ttf'; // This is the path to your font file
$fontSize = 40; // This is the font size, adjust as needed
$fontColor = imagecolorallocate($img, 0, 0, 0); // Black color for the font

// Get bounding box of the text
$textBox = imagettfbbox($fontSize, 0, $fontFile, $text);
$textWidth = $textBox[2] - $textBox[0];
$textHeight = $textBox[1] - $textBox[7];
// Calculate coordinates
$x = ($width / 2) - ($textWidth / 2);
$y = ($height * 0.89) - ($textHeight / 2);

// Add the text to the image
imagettftext($img, $fontSize, 0, $x, $y, $fontColor, $fontFile, $text);

if ($polaroid['description'] && $polaroid['complement']) {
    $fontSize = 35;
    $line = 0.94;
} else {
    $fontSize = 40;
    $line = 0.96;
}
$fontFile = './EvelethCleanThin.ttf';

if ($text = $polaroid['description']) {

    // Get bounding box of the text
    $textBox = imagettfbbox($fontSize, 0, $fontFile, $text);
    $textWidth = $textBox[2] - $textBox[0];
    $textHeight = $textBox[1] - $textBox[7];
    // Calculate coordinates
    $x = ($width / 2) - ($textWidth / 2);
    $y = ($height * $line) - ($textHeight / 2);


    imagettftext($img, $fontSize, 0, $x, $y, $fontColor, $fontFile, $text);
}

if ($text = $polaroid['complement']) {

    // Get bounding box of the text
    $textBox = imagettfbbox($fontSize, 0, $fontFile, $text);
    $textWidth = $textBox[2] - $textBox[0];
    $textHeight = $textBox[1] - $textBox[7];
    // Calculate coordinates
    $x = ($width / 2) - ($textWidth / 2);
    $y = ($height * ($line + 0.035)) - ($textHeight / 2);


    imagettftext($img, $fontSize, 0, $x, $y, $fontColor, $fontFile, $text);
}

if (!$hd) {

    // Get the current width and height of the image
    $originalWidth = imagesx($img);
    $originalHeight = imagesy($img);

    // Calculate the new height while maintaining the aspect ratio
    $newWidth = 400; // target width
    $aspectRatio = $originalWidth / $originalHeight;
    $newHeight = $newWidth / $aspectRatio;

    // Create a new blank image with the calculated width and height
    $newImage = imagecreatetruecolor($newWidth, $newHeight);

    // Resample the original image onto the new image
    imagecopyresampled($newImage, $img, 0, 0, 0, 0, $newWidth, $newHeight, $originalWidth, $originalHeight);

    // Output or save the new ima
    $img = $newImage;
}

// 5. Output the image as jpeg
header('Content-Type: image/jpeg');
imagejpeg($img, null, $quality);

imagedestroy($img);
