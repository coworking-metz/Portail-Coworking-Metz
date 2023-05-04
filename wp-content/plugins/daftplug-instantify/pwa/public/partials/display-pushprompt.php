<?php

if (!defined('ABSPATH')) exit;

$appIcon = wp_get_attachment_image_src(daftplugInstantify::getSetting('pwaIcon'), array(150, 150))[0];
$message = esc_html__(daftplugInstantify::getSetting('pwaPushPromptMessage'), self::$textDomain);
$textColor = daftplugInstantify::getSetting('pwaPushPromptTextColor');
$backgroundColor = daftplugInstantify::getSetting('pwaPushPromptBgColor');
$dismiss = esc_html__('Dismiss', self::$textDomain);
$allow = esc_html__('Allow Notifications', self::$textDomain);

?>

<div class="daftplugPublicPushPrompt" style="background: <?php echo $backgroundColor; ?>;">
    <div class="daftplugPublicPushPrompt_content">
        <img class="daftplugPublicPushPrompt_icon" alt="<?php echo daftplugInstantify::getSetting('pwaName'); ?>" src="<?php echo $appIcon; ?>">
        <span class="daftplugPublicPushPrompt_msg" style="color: <?php echo $textColor; ?>;"><?php echo $message; ?></span>
    </div>
    <div class="daftplugPublicPushPrompt_buttons">
        <div class="daftplugPublicPushPrompt_dismiss" style="color: <?php echo $textColor; ?>; opacity: 0.65;"><?php echo $dismiss; ?></div>
        <div class="daftplugPublicPushPrompt_allow" style="background: <?php echo $textColor; ?>; color: <?php echo $backgroundColor; ?>;"><?php echo $allow; ?></div>
    </div>
</div>