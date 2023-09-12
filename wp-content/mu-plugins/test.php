<?php

if (isset($_GET['test'])) {
    add_action('init', function () {
        CF::purgeUrls(['/wp-content/uploads/2020/05/20200524_120331.jpg']);

        exit;
    });
}
