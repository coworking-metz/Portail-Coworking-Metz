<?php

if (!defined('ABSPATH')) exit;

$statUrl = daftplugInstantify::getSetting('ampStatCounterUrl');

?>

<div id="statcounter">
	<amp-pixel src="<?php echo $statUrl; ?>" ></amp-pixel> 
</div>