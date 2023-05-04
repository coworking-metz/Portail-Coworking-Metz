<?php

if (!defined('ABSPATH')) exit;

$color = daftplugInstantify::getSetting('pwaThemeColor');

?>

<div class="daftplugPublicScrollProgressBar">
	<div class="daftplugPublicScrollProgressBar_fill" style="background: <?php echo $color; ?>;"></div>
</div>