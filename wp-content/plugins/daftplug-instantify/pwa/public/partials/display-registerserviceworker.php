<?php

if (!defined('ABSPATH')) exit;

$homeUrlParts = parse_url(trailingslashit(home_url('/', 'https')));
$path = '/';

if (array_key_exists('path', $homeUrlParts)) {
    $path = $homeUrlParts['path'];
}

if (daftplugInstantify::isAmpPage()) {
	?>
	<amp-install-serviceworker
	    src="<?php echo $this->getServiceWorkerUrl(false); ?>"
	    data-scope="<?php echo $path; ?>"
	    layout="nodisplay">
	</amp-install-serviceworker>
	<?php
} else {
	?>
	<script type="text/javascript" id="serviceworker">
	    if (navigator.serviceWorker) {
	        window.addEventListener('load', function() {
	            navigator.serviceWorker.register(
	                <?php echo $this->getServiceWorkerUrl(); ?>, {scope: "<?php echo str_replace('/', '\/', $path); ?>"}
	            );
	        });
	    }
	</script>
	<?php
}

?>