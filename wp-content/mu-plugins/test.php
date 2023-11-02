<?php

if (isset($_GET['test'])) {
    $_GET['debug'] = true;
    add_action('init', function () {

        // brevo_start_unsubscribed();
        m(brevo_sync_to_wordpress_list());
        exit;
    });
}
