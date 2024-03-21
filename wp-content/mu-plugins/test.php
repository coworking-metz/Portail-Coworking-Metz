<?php

if (isset($_GET['test'])) {
    $_GET['debug'] = true;
    add_action('init', function () {

me(
    get_user_balance(465)
);
        
        exit;
    });
}
