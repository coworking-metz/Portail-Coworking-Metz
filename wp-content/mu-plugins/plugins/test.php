<?php

if (isset($_GET['test'])) {
    $_GET['debug'] = true;
    add_action('init', function () {
        
        m(coworking_app_settings());
        exit;
    });
}
