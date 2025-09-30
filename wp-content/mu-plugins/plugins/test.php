<?php

if (isset($_GET['test'])) {
    $_GET['debug'] = true;
    add_action('init', function () {


		me(get_users_with_contribution_cafe_active(365));
    });
}
