<?php


if (isset($_GET['template_preview'])) {
    /**
     * Afficher un apercu d'un template d'email donné
     * cf https://www.coworking-metz.fr/wp-admin/edit.php?post_type=viwec_template
     */

    add_action('init', function () {

        $template_id = $_GET['template_preview'];

        $mail = charger_template_mail($template_id);

        echo '<br><code>Subject: '.$mail['subject'].'</code><hr>';
        echo $mail['message'];
        
        exit;
    });
}
