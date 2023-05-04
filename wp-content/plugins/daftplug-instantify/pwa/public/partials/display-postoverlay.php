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

$backgroundColor = daftplugInstantify::getSetting('pwaInstallButtonBackgroundColor');
$textColor = daftplugInstantify::getSetting('pwaInstallButtonTextColor');
$header = esc_html__('See this post in...', $this->textDomain);
$open = esc_html__('Open', $this->textDomain);
$continue = esc_html__('Continue', $this->textDomain);

switch (true) {
    case daftplugInstantify::isPlatform('chrome'):
        $browserName = 'Chrome';
        $browserIcon = plugins_url('pwa/public/assets/img/icon-chrome.png', $this->pluginFile);
        break;
    case daftplugInstantify::isPlatform('firefox'):
        $browserName = 'Firefox';
        $browserIcon = plugins_url('pwa/public/assets/img/icon-firefox.png', $this->pluginFile);
        break;
    case daftplugInstantify::isPlatform('safari'):
        $browserName = 'Safari';
        $browserIcon = plugins_url('pwa/public/assets/img/icon-safari.png', $this->pluginFile);
        break;
    case daftplugInstantify::isPlatform('opera'):
        $browserName = 'Opera';
        $browserIcon = plugins_url('pwa/public/assets/img/icon-opera.png', $this->pluginFile);
        break;
    case daftplugInstantify::isPlatform('edge'):
        $browserName = 'Edge';
        $browserIcon = plugins_url('pwa/public/assets/img/icon-edge.png', $this->pluginFile);
        break;
    default:
        $browserName = 'Chrome';
        $browserIcon = plugins_url('pwa/public/assets/img/icon-chrome.png', $this->pluginFile);
}

?>

<div class="daftplugPublicPostOverlay">
    <div class="daftplugPublicPostOverlay_background"></div>
    <div class="daftplugPublicPostOverlay_content">
        <div class="daftplugPublicPostOverlay_header"><?php echo $header; ?></div>
        <div class="daftplugPublicPostOverlay_actions">
            <div class="daftplugPublicPostOverlayAction -app">
                <img class="daftplugPublicPostOverlayAction_logo" src="<?php echo $appIcon; ?>" alt="<?php echo $appName; ?>">
                <span class="daftplugPublicPostOverlayAction_name"><?php echo $appName; ?></span>
                <button class="daftplugPublicPostOverlayAction_button -install" style="background: <?php echo $backgroundColor; ?>; color: <?php echo $textColor; ?>;"><?php echo $open; ?></button>
            </div>
            <div class="daftplugPublicPostOverlayAction -browser">
                <img class="daftplugPublicPostOverlayAction_logo" src="<?php echo $browserIcon; ?>" alt="<?php echo $browserName; ?>">
                <span class="daftplugPublicPostOverlayAction_name"><?php echo $browserName; ?></span>
                <button class="daftplugPublicPostOverlayAction_button -dismiss"><?php echo $continue; ?></button>
            </div>
        </div>
    </div>
</div>