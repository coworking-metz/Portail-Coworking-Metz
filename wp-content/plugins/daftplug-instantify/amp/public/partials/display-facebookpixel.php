<?php

if (!defined('ABSPATH')) exit;

$pixelId = daftplugInstantify::getSetting('ampFacebookPixelId');

?>

<amp-pixel src="https://www.facebook.com/tr?id=<?php echo esc_attr($pixelId); ?>&ev=PageView&noscript=1"></amp-pixel>