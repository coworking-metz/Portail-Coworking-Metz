<?php


if (isset($_GET['template_preview'])) {
    /**
     * Afficher un apercu d'un template d'email donné
     * cf https://www.coworking-metz.fr/wp-admin/edit.php?post_type=viwec_template
     */

    add_action('init', function () {

        $template_id = $_GET['template_preview'];

        $user_id = $_GET['user_id'] ?? false;

        $codes = [];
        if ($user_id) {
            $visite = get_user_meta($user_id, 'visite', true);
            $data = get_userdata($user_id);
            $codes = [
                ['{date_visite}' => date_francais($visite, true)],
                ['{url_visite_ics}' => site_url() . '/api-json-wp/cowo/v1/visite-ics?user_id=' . $user_id],
                ['{app_login_link}' => app_login_link($user_id)],
                ['{user_name}' => $data->display_name],
                ['{_user_email}' => $data->user_email],
                ['{date_visite}' => date_francais($visite, true)],
                ['{url_commandes_user}' => admin_url('edit.php?s&post_status=all&post_type=shop_order&_customer_user=' . $user_id)],
                ['{url_fiche_user}' => admin_url('user-edit.php?user_id=' . $user_id)],
                ['{url_finaliser_compte_coworker_user}' => admin_url('user-edit.php?finaliser=true&user_id=' . $user_id)],

            ];
        }


        $mail = charger_template_mail($template_id, $codes);

        echo '<br><code>Subject: ' . $mail['subject'] . '</code><hr>';
        echo $mail['message'];

        exit;
    });
}
