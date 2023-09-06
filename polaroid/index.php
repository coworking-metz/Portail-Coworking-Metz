<?php

$uid = $_GET['uid'] ?? false;
$dynamique = isset($_GET['dynamique']);



if ($uid) {
    $target = './gen/' . $uid . '.jpg';

    if (!$dynamique && file_exists($target)) {
        $expires = 86400; // 60 seconds * 60 minutes * 24 hours = 1 day
        header('Cache-Control: max-age=' . $expires . ', must-revalidate');
        header('Pragma: cache');
        header('Expires: ' . gmdate('D, d M Y H:i:s', time() + $expires) . ' GMT');

        header('Content-Type: image/jpeg');
        readfile($target);
        exit;
    };
}


define('WP_USE_THEMES', false); // We don't want to use themes.
require('../wp-load.php');





// print_r($_GET);
$polaroid = $_GET['polaroid'] ?? false;
if ($polaroid) {
    $photo = polaroid_tmpphoto();
    $write = false;
} else {
    $polaroid = polaroid_get($uid);
    if ($image = get_user_meta($uid, 'url_image_trombinoscope', true)) {
        $url = wp_get_attachment_url($image);
        header('Location:' . $url);
        exit;
    }
    $photo = $polaroid['photo'];
    $write = true;
}
list($width, $height) = getimagesize('pola-vide.png');

$img = imagecreatetruecolor($width, $height);

$tmp = imagecreatefromfile($photo);
list($tmpWidth, $tmpHeight) = getimagesize($photo);

$bande = $height * 5 / 100;
// Calculate the new dimensions
$maxWidth = $width - ($bande * 2);
$aspectRatio = $tmpWidth / $tmpHeight;
$newWidth = $maxWidth;
$newHeight = $maxWidth / $aspectRatio;

if ($newHeight > $height) {
    $newHeight = $height;
    $newWidth = $height * $aspectRatio;
}

imagecopyresampled($img, $tmp, $bande, $bande + 2, 0, 0, $newWidth, $newHeight, $tmpWidth, $tmpHeight);
imagedestroy($tmp);

// 4. Open the 'pola-vide.png' file and place it on top of everything in $img
$overlay = imagecreatefrompng('pola-vide.png');
imagecopy($img, $overlay, 0, 0, 0, 0, $width, $height);
imagedestroy($overlay);


// Text to be added
$text = $polaroid['nom'];
$fontFile = './EvelethClean.ttf'; // This is the path to your font file
$fontSize = 20; // This is the font size, adjust as needed
$fontColor = imagecolorallocate($img, 0, 0, 0); // Black color for the font

// Get bounding box of the text
$textBox = imagettfbbox($fontSize, 0, $fontFile, $text);
$textWidth = $textBox[2] - $textBox[0];
$textHeight = $textBox[1] - $textBox[7];
// Calculate coordinates
$x = ($width / 2) - ($textWidth / 2);
$y = ($height * 0.9) - ($textHeight / 2);

// Add the text to the image
imagettftext($img, $fontSize, 0, $x, $y, $fontColor, $fontFile, $text);

if ($polaroid['description'] && $polaroid['complement']) {
    $fontSize = 13;
    $line = 0.94;
} else {
    $fontSize = 18;
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
    $y = ($height * ($line + 0.04)) - ($textHeight / 2);


    imagettftext($img, $fontSize, 0, $x, $y, $fontColor, $fontFile, $text);
}

if ($write) {
    imagejpeg($img, $target, 90);
}

// 5. Output the image as jpeg
header('Content-Type: image/jpeg');
imagejpeg($img, null, 90);

imagedestroy($img);
