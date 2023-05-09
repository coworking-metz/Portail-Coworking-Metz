<?php

if (!defined('ABSPATH')) exit;

if (is_singular()) {
	global $post;
	$postPwaIcon = get_post_meta($post->ID, 'pwaIcon', true);
	$appIcon = (!empty($postPwaIcon)) ? wp_get_attachment_image_src($postPwaIcon, array(150, 150))[0] : wp_get_attachment_image_src(daftplugInstantify::getSetting('pwaIcon'), array(150, 150))[0];
} else {
	$appIcon = wp_get_attachment_image_src(daftplugInstantify::getSetting('pwaIcon'), array(150, 150))[0];
}

$message = esc_html__(daftplugInstantify::getSetting('pwaOverlaysTypeCheckoutMessage'), $this->textDomain);
$backgroundColor = daftplugInstantify::getSetting('pwaOverlaysTypeCheckoutBackgroundColor');
$textColor = daftplugInstantify::getSetting('pwaOverlaysTypeCheckoutTextColor');
$notNow = esc_html__('Not now', $this->textDomain);
$install = esc_html__('Install', $this->textDomain);

?>

<div class="daftplugPublic" data-daftplug-plugin="<?php echo $this->optionName; ?>">
    <div class="daftplugPublicCheckoutOverlay" style="background: <?php echo $backgroundColor; ?>; color: <?php echo $textColor; ?>;">
        <div class="daftplugPublicCheckoutOverlay_content">
            <img class="daftplugPublicCheckoutOverlay_icon" src="<?php echo $appIcon; ?>">
            <span class="daftplugPublicCheckoutOverlay_msg"><?php echo $message; ?></span>
        </div>
        <div class="daftplugPublicCheckoutOverlay_buttons">
            <div class="daftplugPublicCheckoutOverlay_dismiss" style="color: <?php echo $textColor; ?>;"><?php echo $notNow; ?></div>
            <div class="daftplugPublicCheckoutOverlay_install" style="background: <?php echo $textColor; ?>; color: <?php echo $backgroundColor; ?>;"><?php echo $install; ?></div>
        </div>
    </div>
</div>