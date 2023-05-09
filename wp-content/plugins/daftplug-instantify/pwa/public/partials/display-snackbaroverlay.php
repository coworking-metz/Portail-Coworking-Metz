<?php

if (!defined('ABSPATH')) exit;

$message = esc_html__(daftplugInstantify::getSetting('pwaOverlaysTypeSnackbarMessage'), $this->textDomain);
$backgroundColor = daftplugInstantify::getSetting('pwaOverlaysTypeSnackbarBackgroundColor');
$textColor = daftplugInstantify::getSetting('pwaOverlaysTypeSnackbarTextColor');
$buttonText = esc_html__('Install', $this->textDomain);

?>

<div class="daftplugPublicSnackbarOverlay" style="background: <?php echo $backgroundColor; ?>; color: <?php echo $textColor; ?>;">
    <div class="daftplugPublicSnackbarOverlay_message">
        <div class="daftplugPublicSnackbarOverlay_appname"><?php echo $buttonText; ?></div>
        <div class="daftplugPublicSnackbarOverlay_text"><?php echo $message; ?></div>
    </div>
    <div class="daftplugPublicSnackbarOverlay_button" style="background: <?php echo $textColor; ?>; color: <?php echo $backgroundColor; ?>;">
        <?php echo $buttonText; ?>
    </div>
</div> 