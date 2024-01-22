<?php

if (isset($_GET['test'])) {
    $_GET['debug'] = true;
    add_action('init', function () {

m(fetch_users_with_visite_today());
        exit;
    });
}
