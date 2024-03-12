<?php

if (isset($_GET['test'])) {
    $_GET['debug'] = true;
    add_action('init', function () {


		m(get_users_with_photos());
        exit;
    });
}
