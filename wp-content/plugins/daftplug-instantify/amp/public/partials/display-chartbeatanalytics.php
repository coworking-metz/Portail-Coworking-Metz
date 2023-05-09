<?php

if (!defined('ABSPATH')) exit;

$accountId = daftplugInstantify::getSetting('ampChartbeatAnalyticsAccountId');

$fields = array(
	'vars' => array(
		'uid' => $accountId,
		'domain' => parse_url(trailingslashit(home_url('/', 'https')), PHP_URL_HOST),
		'title' => get_the_title(),
		'authors' => get_the_author_meta('display_name'),
	),
);

?>

<amp-analytics type="chartbeat">
	<script type="application/json">
		<?php echo json_encode($fields); ?>
	</script>
</amp-analytics>