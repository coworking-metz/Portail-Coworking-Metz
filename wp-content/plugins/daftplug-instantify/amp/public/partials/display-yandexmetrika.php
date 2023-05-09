<?php

if (!defined('ABSPATH')) exit;

$counterId = daftplugInstantify::getSetting('ampYandexMetrikaCounterId');

$fields = array(
	'vars' => array(
		'counterId' => $counterId,
	),
	'triggers' => array(
		'notBounce' => array(
			'on' => 'timer',
			'timerSpec' => array(
				'immediate' => 'false',
				'interval' => '15',
				'maxTimerLength' => '16',
			),
		),
	),
	'request' => 'notBounce',
);

?>

<amp-analytics type="metrika">
	<script type="application/json">
		<?php echo json_encode($fields); ?>
	</script>
</amp-analytics>