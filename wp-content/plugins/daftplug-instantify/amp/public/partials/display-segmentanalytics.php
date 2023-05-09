<?php

if (!defined('ABSPATH')) exit;

$writeKey = daftplugInstantify::getSetting('ampSegmentAnalyticsWriteKey');

$fields = array(
	'vars' => array(
		'writeKey' => $writeKey,
		'name' => get_the_title()
	),
);

?>

<amp-analytics type="segment">
	<script type="application/json">
		<?php echo json_encode($fields) ?>
	</script>
</amp-analytics>