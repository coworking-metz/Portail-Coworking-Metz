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
                ['{_user_id}' => $data->ID],
                ['{date_visite}' => date_francais($visite, true)],
                ['{url_visite_ics}' => 'https://www.coworking-metz.fr/api-json-wp/cowo/v1/visite-ics?user_id=' . $user_id],
                ['{app_login_link}' => app_login_link($user_id)],
                ['{user_name}' => $data->display_name],
                ['{_user_email}' => $data->user_email],
                ['{activite}' => get_visiteur_activite($data->ID)],
                ['{date_visite}' => date_francais($visite, true)],
                ['{url_commandes_user}' => admin_url('edit.php?s&post_status=all&post_type=shop_order&_customer_user=' . $user_id)],
                ['{url_fiche_user}' => admin_url('user-edit.php?user_id=' . $user_id)],
                ['{_admin_url}' => admin_url()],
                ['{url_finaliser_compte_coworker_user}' => admin_url('user-edit.php?finaliser=true&user_id=' . $user_id)],

            ];
        }


        $mail = charger_template_mail($template_id, $codes);

        echo '<br><code>Subject: ' . $mail['subject'] . '</code><hr>';
        $args = array(
            'orderby' => 'registered',
            'order' => 'DESC'
        );
        $allusers = get_users($args);
        ?>
        <script>
            function reloadPage() {
                const url = new URL(window.location.href);
                url.searchParams.delete('user_id'); // Remove the user_id if it exists
                
                const newUserId = document.getElementById('user_id').value;
                url.searchParams.set('user_id', newUserId); // Set the new user_id
                console.log(url)
                document.location.href = url;
            }
        </script>
<?php

        echo '<select id="user_id" onchange="reloadPage()"><option value="" selected disabled>Voir pour le compte:</option>';
        foreach ($allusers as $user) {
            echo '<option value="' . $user->ID . '"' . ($user->ID == $user_id ? 'selected' : '') . '>' . $user->display_name . ' #'.$user->ID.' (' . $user->user_email . ')</option>';
        }
        echo '</select><hr>';

        echo $mail['message'];

        exit;
    });
}
