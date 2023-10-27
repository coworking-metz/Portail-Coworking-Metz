<?php

if (isset($_GET['test'])) {
    $_GET['debug'] = true;
    add_action('init', function () {

        me(recapJoursDeVisites());
        $uid = get_current_user_id();
        update_user_meta($uid, '_first_login_date', false);
me($uid);
        // $template = new VIWEC_Render_Email_Template(['template_id' => 28637]);

        // print_r($template->get_content());

        exit;
    });
}
