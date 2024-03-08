<?php
require('./lib/utils.php');
define('WP_USE_THEMES', false); // We don't want to use themes.
require('../wp-load.php');


$skip = isset($_GET['skip']);
$uid = $_GET['uid'] ?? false;
$small = $_GET['small'] ?? false;
$width = $_GET['width'] ?? false;


$filename = [$uid, 'raw'];
if ($small) {
    $filename[] = 'small';
} else {
    $filename[] = $width;
}

$filename = implode('-', array_filter($filename)) . '.jpg';
$path = __DIR__ . '/tmp/' . $filename;

if (!$skip) {
    outputImageIfExists($path);
}


$polaroid = polaroid_get($uid);
$photo = $polaroid['photo'] ?? false;


if ($photo) {
    CF::cacheHeaders();
    outputImageWithHeaders($photo, $small ? 150 : $width, $path);
}