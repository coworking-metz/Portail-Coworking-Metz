<?php

if (isset($_GET['test'])) {
    $_GET['debug'] = true;
    add_action('init', function () {


        var_dump(envoyerMailAlerte(879));

        // $template = new VIWEC_Render_Email_Template(['template_id' => 28637]);

        // print_r($template->get_content());

        exit;
    });
}
