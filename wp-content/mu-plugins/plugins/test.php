<?php

if (isset($_GET['test'])) {
    $_GET['debug'] = true;
    add_action('init', function () {


		me(polaroid_clear_cache(225));
    });
}
