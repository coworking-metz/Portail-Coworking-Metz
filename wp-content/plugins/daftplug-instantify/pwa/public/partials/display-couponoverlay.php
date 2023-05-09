<?php

if (!defined('ABSPATH')) exit;

$message = esc_html__(daftplugInstantify::getSetting('pwaOverlaysTypeCouponMessage'), $this->textDomain);
$discountPercentage = daftplugInstantify::getSetting('pwaOverlaysTypeCouponPercentage');
$buttonText = esc_html__('Install Web App & Redeem Coupon', $this->textDomain);

?>

<div class="daftplugPublicCouponOverlay">
    <div class="daftplugPublicCouponOverlay_background"></div>
    <div class="daftplugPublicCouponOverlay_content">
        <div class="daftplugPublicCouponOverlay_close"></div>
        <img class="daftplugPublicCouponOverlay_icon" src="<?php echo plugins_url('pwa/public/assets/img/icon-discount.png', $this->pluginFile); ?>"/>
        <div class="daftplugPublicCouponOverlay_title"><?php printf(__('GET %s%% DISCOUNT', $this->textDomain), $discountPercentage); ?></div>
        <div class="daftplugPublicCouponOverlay_message"><?php echo $message; ?></div>
        <button class="daftplugPublicCouponOverlay_button -install"><?php echo $buttonText; ?></button>
    </div>
</div>