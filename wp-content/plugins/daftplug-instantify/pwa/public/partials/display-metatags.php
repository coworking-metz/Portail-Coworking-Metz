<?php

if (!defined('ABSPATH')) exit;

if (is_singular()) {
	global $post;
    $postPwaName = get_post_meta($post->ID, 'pwaName', true);
    $postPwaShortName = get_post_meta($post->ID, 'pwaShortName', true);
	$postPwaIcon = get_post_meta($post->ID, 'pwaIcon', true);

	if (!empty($postPwaName)) {
		$appName = $postPwaName;
	} else {
		$appName = (daftplugInstantify::getSetting('pwaDynamicManifest') == 'on') ? get_the_title() : daftplugInstantify::getSetting('pwaName');
	}

    if (!empty($postPwaShortName)) {
		$appShortName = $postPwaShortName;
	} else {
        if (daftplugInstantify::getSetting('pwaDynamicManifest') == 'on') {
            $appShortName = (strlen(get_the_title()) > 12) ? substr(get_the_title(), 0, 9).'...' : get_the_title();
        } else {
            $appShortName = daftplugInstantify::getSetting('pwaShortName');
        }
	}

	$appIcon = (!empty($postPwaIcon)) ? preg_replace('/(\.[^.]+)$/', sprintf('%s$1', '-192x192'), wp_get_attachment_image_src($postPwaIcon, 'full')[0] ?? '') : preg_replace('/(\.[^.]+)$/', sprintf('%s$1', '-192x192'), wp_get_attachment_image_src(daftplugInstantify::getSetting('pwaIcon'), 'full')[0] ?? '');
} else {
    $appName = daftplugInstantify::getSetting('pwaName');
    $appShortName = daftplugInstantify::getSetting('pwaShortName');
	$appIcon = preg_replace('/(\.[^.]+)$/', sprintf('%s$1', '-192x192'), wp_get_attachment_image_src(daftplugInstantify::getSetting('pwaIcon'), 'full')[0] ?? '');
}

?>

<link rel="manifest" crossorigin="use-credentials" href="<?php echo $this->getManifestUrl(false); ?>">
<meta name="theme-color" content="<?php echo daftplugInstantify::getSetting('pwaThemeColor'); ?>">
<meta name="mobile-web-app-capable" content="yes">
<meta name="application-name" content="<?php echo $appName; ?>">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-touch-fullscreen" content="yes">
<meta name="apple-mobile-web-app-title" content="<?php echo $appShortName; ?>">
<meta name="apple-mobile-web-app-status-bar-style" content="<?php echo daftplugInstantify::getSetting('pwaIosStatusBarStyle'); ?>">
<?php
if ((daftplugInstantify::getSetting('pwaRelatedApplication1') == 'on') && (daftplugInstantify::getSetting('pwaRelatedApplication1Platform') == 'itunes')) {
    echo '<meta name="apple-itunes-app" content="app-id='.daftplugInstantify::getSetting("pwaRelatedApplication1Id").', app-argument='.daftplugInstantify::getSetting("pwaRelatedApplication1Url").'">';
} elseif ((daftplugInstantify::getSetting('pwaRelatedApplication2') == 'on') && (daftplugInstantify::getSetting('pwaRelatedApplication2Platform') == 'itunes')) {
    echo '<meta name="apple-itunes-app" content="app-id='.daftplugInstantify::getSetting("pwaRelatedApplication2Id").', app-argument='.daftplugInstantify::getSetting("pwaRelatedApplication2Url").'">';
} elseif ((daftplugInstantify::getSetting('pwaRelatedApplication3') == 'on') && (daftplugInstantify::getSetting('pwaRelatedApplication3Platform') == 'itunes')) {
    echo '<meta name="apple-itunes-app" content="app-id='.daftplugInstantify::getSetting("pwaRelatedApplication3Id").', app-argument='.daftplugInstantify::getSetting("pwaRelatedApplication3Url").'">';
}
?>
<link rel="apple-touch-icon" href="<?php echo $appIcon; ?>">
<?php

if (file_exists($this->pluginUploadDir . 'img-pwa-apple-launch.png')) {
    $devices = array(
        'iPhone 12 Pro Max, 13 Pro Pro Max' => array(
            'device-width'               => '428px',
            'device-height'              => '926px',
            '-webkit-device-pixel-ratio' => '3',
            'launch-width'               => '1284',
            'launch-height'              => '2778',
        ),
        
        'iPhone 12, 12 Pro, 13, 13 Pro' => array(
            'device-width'               => '390px',
            'device-height'              => '844px',
            '-webkit-device-pixel-ratio' => '3',
            'launch-width'               => '1170',
            'launch-height'              => '2532',
        ),

        'iPhone 11 Pro Max' => array(
            'device-width'               => '414px',
            'device-height'              => '896px',
            '-webkit-device-pixel-ratio' => '3',
            'launch-width'               => '1242',
            'launch-height'              => '2688',
        ),

        'iPhone 11' => array(
            'device-width'               => '414px',
            'device-height'              => '896px',
            '-webkit-device-pixel-ratio' => '2',
            'launch-width'               => '828',
            'launch-height'              => '1792',
        ),

        'iPhone X, iPhone 11 Pro' => array(
            'device-width'               => '375px',
            'device-height'              => '812px',
            '-webkit-device-pixel-ratio' => '3',
            'launch-width'               => '1125',
            'launch-height'              => '2436',
        ),
        
        'iPhone 8, 7, 6, 6s' => array(
            'device-width'               => '375px',
            'device-height'              => '667px',
            '-webkit-device-pixel-ratio' => '2',
            'launch-width'               => '750',
            'launch-height'              => '1334',
        ),
        
        'iPhone 8 Plus, 7 Plus, 6s Plus, 6 Plus' => array(
            'device-width'               => '414px',
            'device-height'              => '736px',
            '-webkit-device-pixel-ratio' => '3',
            'launch-width'               => '1242',
            'launch-height'              => '2208',
        ),

        'iPhone 5' => array(
            'device-width'               => '320px',
            'device-height'              => '568px',
            '-webkit-device-pixel-ratio' => '2',
            'launch-width'               => '640',
            'launch-height'              => '1136',
        ),

        'iPad Mini, Air' => array(
            'device-width'               => '768px',
            'device-height'              => '1024px',
            '-webkit-device-pixel-ratio' => '2',
            'launch-width'               => '1536',
            'launch-height'              => '2048',
        ),
        
        'iPad Pro 10.5' => array(
            'device-width'               => '834px',
            'device-height'              => '1112px',
            '-webkit-device-pixel-ratio' => '2',
            'launch-width'               => '1668',
            'launch-height'              => '2224',
        ),

        'iPad Pro 12.9' => array(
            'device-width'               => '1024px',
            'device-height'              => '1366px',
            '-webkit-device-pixel-ratio' => '2',
            'launch-width'               => '2048',
            'launch-height'              => '2732',
        ),
    );

    foreach ($devices as $device) {
        echo '<link rel="apple-touch-startup-image" media="(device-width: '.$device['device-width'].') and (device-height: '.$device['device-height'].') and (-webkit-device-pixel-ratio: '.$device['-webkit-device-pixel-ratio'].')" href="'.$this->pluginUploadUrl.'img-pwa-apple-launch-'.$device['launch-width'].'x'.$device['launch-height'].'.png'.'">';
    }
}
?>