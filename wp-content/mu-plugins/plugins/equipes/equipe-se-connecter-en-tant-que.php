<?php


if (isset($_GET['revenir-equipe-admin-user'])) {
    add_action('init', function () {
        $user = equipe_admin_logged_in_as();

        if (!$user) $error = true;

        $equipe = getEquipePourGestionnaire($user['ID']);
        $memberId = get_current_user_id();
        if (!isInEquipe($memberId, $equipe)) $error = true;

        if (!$error) {
            if (!equipe_log_in_has($user['ID'])) $error = true;
            wp_redirect_notification('/mon-compte/equipe', ['temporaire' => true, 'titre' => 'Vous êtes de retour', 'texte' => 'Vous êtes à nouveau connecté avec votre compte personnel <strong>' . $user['display_name'].'</strong>']);
        }
        if ($error) {
            wp_redirect('/mon-compte/equipe');
            exit;
        }
    });
}
if ($_GET['se-connecter-en-tant-que'] ?? false) {
    add_action('init', function () {
        $error = false;
        $user = wp_get_current_user();

        if (!$user)
            $error = true;

        $uid = $user->ID;
        $equipe = getMonEquipe($uid);
        if (!$equipe)
            $error = true;


        $memberId = $_GET['se-connecter-en-tant-que'];
        $member = get_userdata($memberId);
        if (!$member) $error = true;
        if (!isInEquipe($memberId, $equipe)) $error = true;



        if (!$error) {
            if (!equipe_set_admin_cookie($user)) $error = true;

            if (!equipe_log_in_has($memberId)) $error = true;

            wp_redirect_notification('/la-boutique', ['temporaire' => true, 'titre' => 'Connexion effectuée', 'texte' => 'Vous êtes connecté en tant que <strong>' . $member->display_name . '</strong>']);
        }

        if ($error) {
            wp_redirect('/mon-compte/equipe');
            exit;
        }

        m($user);
        me($equipe);
    });
}

add_action('init', function () {

    if (equipe_admin_logged_in_as()) {

        add_action('wp_footer', function () {
            $admin = equipe_admin_logged_in_as();
            if (!$admin) return;

            $user = wp_get_current_user();
            if (!$user) return;

            echo '<div style="z-index:1000;position:fixed;bottom:0;left:0;right:0;padding:.5em;background-color: darkgreen; color:white;text-align: center;">Votre compte d\'administrateur d\'équipe <strong>' . $admin['display_name'] . '</strong> est connecté en tant que <strong>' . $user->display_name . '</strong><br>Vous pouvez passer commande pour ce membre sur la boutique ou bien <a href="?revenir-equipe-admin-user"><u>revenir à votre compte</u></a>.</div>';
        });
    }
});
