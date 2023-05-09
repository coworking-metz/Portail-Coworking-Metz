<?php

if (!defined('ABSPATH')) exit;

$message = esc_html__(daftplugInstantify::getSetting('ampCookieNoticeMessage'), $this->textDomain);
$buttonText = esc_html__(daftplugInstantify::getSetting('ampCookieNoticeButtonText'), $this->textDomain);

?>

<amp-user-notification layout="nodisplay" id="daftplugPublicCookieNotice" class="daftplugPublicCookieNotice">
   <p class="daftplugPublicCookieNotice_message"><?php echo $message; ?></p>
   <button class="daftplugPublicCookieNotice_button" on="tap:daftplugPublicCookieNotice.dismiss"><?php echo $buttonText; ?></button>
</amp-user-notification>