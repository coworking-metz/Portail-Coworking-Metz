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

?>

<div class="daftplugPublicIosOverlay">
    <div class="daftplugPublicIosOverlay_background"></div>
    <div class="daftplugPublicIosOverlay_content">
        <div class="daftplugPublicIosOverlay_header">
            <div class="daftplugPublicIosOverlay_title"><?php printf(__('Add %s to Home Screen', $this->textDomain), $appName); ?></div>
            <div class="daftplugPublicIosOverlay_close"><?php esc_html_e('Close', $this->textDomain); ?></div>
        </div>
        <div class="daftplugPublicIosOverlay_main">
            <p class="daftplugPublicIosOverlay_message"><?php printf(__('For an optimized experience on mobile, add %s shortcut to your mobile device\'s home screen', $this->textDomain), $appName); ?></p>
            <div class="daftplugPublicIosOverlayStep -one">
                <img class="daftplugPublicIosOverlayStep_icon" src="<?php echo plugins_url('pwa/public/assets/img/icon-iosstep1.png', $this->pluginFile); ?>"/>
                <div class="daftplugPublicIosOverlayStep_desc"><?php esc_html_e('1) Press the share button on your browser\'s menu bar', $this->textDomain); ?></div>
            </div>
            <div class="daftplugPublicIosOverlayStep -two">
                <img class="daftplugPublicIosOverlayStep_icon" src="<?php echo plugins_url('pwa/public/assets/img/icon-iosstep2.png', $this->pluginFile); ?>"/>
                <div class="daftplugPublicIosOverlayStep_desc"><?php esc_html_e('2) Press \'Add to Home Screen\'.', $this->textDomain); ?></div>
            </div>
        </div>
    </div>
</div>