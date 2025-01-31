<?php

if (isset($_GET['test'])) {
    $_GET['debug'] = true;
    add_action('init', function () {


		me(fetch_nomades_for_today());
    });
}
