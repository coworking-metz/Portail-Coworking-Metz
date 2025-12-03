<?php

if (isset($_GET['test'])) {
    $_GET['debug'] = true;
    add_action('init', function () {

me(avent_email_alerte(1251,"2025-12-03"));

    });
}
