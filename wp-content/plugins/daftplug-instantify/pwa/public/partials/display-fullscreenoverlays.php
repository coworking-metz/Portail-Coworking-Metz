<?php

if (!defined('ABSPATH')) exit;

if (is_singular()) {
	global $post;
	$postPwaName = get_post_meta($post->ID, 'pwaName', true);
	$postPwaIcon = get_post_meta($post->ID, 'pwaIcon', true);

	if (!empty($postPwaName)) {
		$appName = $postPwaName;
	} else {
		$appName = (daftplugInstantify::getSetting('pwaDynamicManifest') == 'on') ? get_the_title() : daftplugInstantify::getSetting('pwaName');
	}

	$appIcon = (!empty($postPwaIcon)) ? wp_get_attachment_image_src($postPwaIcon, array(150, 150))[0] : wp_get_attachment_image_src(daftplugInstantify::getSetting('pwaIcon'), array(150, 150))[0];
} else {
	$appName = daftplugInstantify::getSetting('pwaName');
	$appIcon = wp_get_attachment_image_src(daftplugInstantify::getSetting('pwaIcon'), array(150, 150))[0];
}

$cib = esc_html__('Continue in browser', $this->textDomain);
$tit = esc_html__('To install tap', $this->textDomain);
$ac = esc_html__('and choose', $this->textDomain);
$aths = esc_html__('Add to Home Screen', $this->textDomain);

if ((in_array('chrome', (array)daftplugInstantify::getSetting('pwaOverlaysBrowsers')) && daftplugInstantify::isPlatform('chrome')) || (in_array('edge', (array)daftplugInstantify::getSetting('pwaOverlaysBrowsers')) && daftplugInstantify::isPlatform('edge'))) {
	?>
	<div class="daftplugPublicFullscreenOverlay -chrome">
	    <div class="daftplugPublicFullscreenOverlay_close"><?php echo $cib; ?></div>
	    <div class="daftplugPublicFullscreenOverlay_logo" style="background-image:url(<?php echo $appIcon; ?>)"><?php echo $appName; ?></div>
	    <div class="daftplugPublicFullscreenOverlay_text"><?php echo $tit.' '.$aths; ?></div>
	    <div class="daftplugPublicFullscreenOverlay_icon -pointer"></div>
	    <div class="daftplugPublicFullscreenOverlay_button"><?php echo $aths; ?></div>
	</div>
	<?php 
} elseif (in_array('safari', (array)daftplugInstantify::getSetting('pwaOverlaysBrowsers')) && daftplugInstantify::isPlatform('safari')) {
	?>
	<div class="daftplugPublicFullscreenOverlay -safari">
        <div class="daftplugPublicFullscreenOverlay_close"><?php echo $cib; ?></div>
        <div class="daftplugPublicFullscreenOverlay_logo" style="background-image:url(<?php echo $appIcon; ?>)"><?php echo $appName; ?></div>
        <div class="daftplugPublicFullscreenOverlay_text">
        	<?php echo $tit; ?>
        	<div class="daftplugPublicFullscreenOverlay_icon -home"></div>
        	<?php echo $ac; ?><br>
        	<?php echo $aths; ?>   
        </div>
        <div class="daftplugPublicFullscreenOverlay_icon -pointer"></div>
    </div>
	<?php
} elseif (in_array('firefox', (array)daftplugInstantify::getSetting('pwaOverlaysBrowsers')) && daftplugInstantify::isPlatform('firefox')) {
	?>
    <div class="daftplugPublicFullscreenOverlay -firefox">
        <div class="daftplugPublicFullscreenOverlay_logo" style="background-image:url(<?php echo $appIcon; ?>)"><?php echo $appName; ?></div>
        <div class="daftplugPublicFullscreenOverlay_text">
        	<?php echo $tit; ?>
        	<div class="daftplugPublicFullscreenOverlay_icon -home"></div>
        	<?php echo $ac; ?><br>
        	<?php echo $aths; ?>
        </div>
		<div class="daftplugPublicFullscreenOverlay_close"><?php echo $cib; ?></div>
		<div class="daftplugPublicFullscreenOverlay_icon -pointer"></div>
    </div>
	<?php
} elseif (in_array('opera', (array)daftplugInstantify::getSetting('pwaOverlaysBrowsers')) && daftplugInstantify::isPlatform('opera')) {
	?>
    <div class="daftplugPublicFullscreenOverlay -opera">
        <div class="daftplugPublicFullscreenOverlay_icon -pointer"></div>
        <div class="daftplugPublicFullscreenOverlay_logo" style="background-image:url(<?php echo $appIcon; ?>)"><?php echo $appName; ?></div>
        <div class="daftplugPublicFullscreenOverlay_text">
        	<?php echo $tit; ?>
        	<div class="daftplugPublicFullscreenOverlay_icon -home"></div>
        	<?php echo $ac; ?><br>
        	<?php echo $aths; ?>
        </div>
        <div class="daftplugPublicFullscreenOverlay_close"><?php echo $cib; ?></div>
    </div>
	<?php
}

?>