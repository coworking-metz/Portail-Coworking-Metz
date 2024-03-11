<?php

if (isset($_GET['test'])) {
    $_GET['debug'] = true;
    add_action('init', function () {


		m(site_url(),CoworkingMetz\CloudFlare::doPurgeUrls(site_url()));
        exit;
    });
}
