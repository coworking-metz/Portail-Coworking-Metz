<?php

if (isset($_GET['test'])) {
     $_GET['debug'] = true;
     add_action('init', function () {

		me(api_coworker_presences());
		exit;
     });
 }
