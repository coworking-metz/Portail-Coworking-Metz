<?php

if (!defined('ABSPATH')) exit;

$histatsId = daftplugInstantify::getSetting('ampHistatsAnalyticsId');

$url = add_query_arg(esc_attr($histatsId), '', '//sstatic1.histats.com/0.gif');
$url = add_query_arg('101', '', $url);

?>

<div id="histats">
	<amp-pixel src="<?php echo esc_url_raw($url); ?>" ></amp-pixel> 
</div>