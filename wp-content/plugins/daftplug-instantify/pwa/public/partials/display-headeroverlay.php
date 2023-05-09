<?php

if (!defined('ABSPATH')) exit;

if (is_singular()) {
	global $post;
	$postPwaName = get_post_meta($post->ID, 'pwaName', true);

	if (!empty($postPwaName)) {
		$appName = $postPwaName;
	} else {
		$appName = (daftplugInstantify::getSetting('pwaDynamicManifest') == 'on') ? get_the_title() : daftplugInstantify::getSetting('pwaName');
	}
} else {
	$appName = daftplugInstantify::getSetting('pwaName');
}

$message = esc_html__(daftplugInstantify::getSetting('pwaOverlaysTypeHeaderMessage'), $this->textDomain);
$backgroundColor = daftplugInstantify::getSetting('pwaOverlaysTypeHeaderBackgroundColor');
$textColor = daftplugInstantify::getSetting('pwaOverlaysTypeHeaderTextColor');
$buttonText = esc_html__('Install', $this->textDomain);

?>

<div class="daftplugPublicHeaderOverlay" style="background: <?php echo $backgroundColor; ?>; color: <?php echo $textColor; ?>;">
	<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="daftplugPublicHeaderOverlay_dismiss" style="stroke: <?php echo $textColor; ?>;">
        <g stroke-linecap="round" stroke-linejoin="round">
            <line x1="18" y1="6" x2="6" y2="18"></line>
            <line x1="6" y1="6" x2="18" y2="18"></line>
        </g>
    </svg>
    <div class="daftplugPublicHeaderOverlay_message">
        <div class="daftplugPublicHeaderOverlay_appname"><?php echo $appName; ?></div>
        <div class="daftplugPublicHeaderOverlay_text"><?php echo $message; ?></div>
    </div>
    <div class="daftplugPublicHeaderOverlay_button" style="background: <?php echo $textColor; ?>; color: <?php echo $backgroundColor; ?>;">
        <?php echo $buttonText; ?>
    </div>
</div> 