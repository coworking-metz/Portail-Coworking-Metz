<?php

if (!defined('ABSPATH')) exit;

$account = daftplugInstantify::getSetting('ampAlexaMetricsAccount');
$domain = daftplugInstantify::getSetting('ampAlexaMetricsDomain');

$fields = array(
	'vars' => array(
		'atrk_acct' => $account,
		'domain' => $domain,
	),
);

?>

<amp-analytics type="alexametrics">
	<script type="application/json">
		<?php echo json_encode($fields, JSON_UNESCAPED_SLASHES); ?>
	</script>
</amp-analytics>