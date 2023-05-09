<?php

if (!defined('ABSPATH')) exit;

$trackingId = daftplugInstantify::getSetting('ampGoogleAnalyticsTrackingId');

$fields = array(
	'vars' => array(
		'gtag_id' => $trackingId,
		'config' => array(
			$trackingId => array(
				'groups' => 'default',
			), 
		),
		'triggers' => array(
			'trackPageview' => array(
				'on' => 'visible',
				'request' => 'pageview'
			),
		)
	),
);

if (daftplugInstantify::getSetting('ampGoogleAnalyticsAmpLinker') == 'on') {
	$fields['vars']['linkers'] = array(
		'enabled' => true
	);
}

?>

<amp-analytics data-block-on-consent type="googleanalytics" data-credentials="include">
	<script type="application/json">
		<?php echo json_encode($fields); ?>
	</script>
</amp-analytics>