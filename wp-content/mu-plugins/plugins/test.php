<?php

if (isset($_GET['test'])) {
    $_GET['debug'] = true;
    add_action('init', function () {

$nomade = '0';
me(empty($nomade));
    });
}
