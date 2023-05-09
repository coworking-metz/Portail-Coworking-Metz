<?php

if (!defined('ABSPATH')) exit;

$siteId = daftplugInstantify::getSetting('ampClickyAnalyticsSiteId');

$fields = array(
	'vars' => array(
		'site_id' => $siteId,
	),
);

?>

<amp-analytics type="clicky">
	<script type="application/json">
		<?php echo json_encode($fields); ?>
	</script>
</amp-analytics>