<?php

if (!defined('ABSPATH')) exit;

$pryd = esc_html__('Please rotate your device', $this->textDomain);

?>

<div class="daftplugPublicRotateNotice">
    <div class="daftplugPublicRotateNotice_phone"></div>
    <div class="daftplugPublicRotateNotice_text"><?php echo $pryd; ?></div>
</div>