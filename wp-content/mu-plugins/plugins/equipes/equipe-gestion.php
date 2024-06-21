<?php

if (isset($_POST['equipe-creer'])) {
    add_action('init', function () {
        $uid = get_current_user_id();
        $erreur = false;
        if (getMonEquipe($uid)) $erreur = true;


        if (!$erreur) {
            $name = $_POST['equipe-creer'];
            $eid = wp_insert_post(['post_title' => $name, 'post_type' => 'equipe', 'post_status' => 'publish']);
            equipe_rejoindre($eid, $uid, 'admin');
            wp_redirect_notification('/mon-compte/equipe', ['titre' => 'Votre équipe a été crée', 'texte' => 'Vous pouvez maintenant inviter des membres dans votre équipe pour pouvoir gérer leur compte coworker.']);
        }

        wp_redirect('/mon-compte/equipe');
        exit;
    });
}
if (isset($_POST['equipe-inviter'])) {
    add_action('init', function () {
        $equipe = getEquipePourGestionnaire(get_current_user_id());
        if (!$equipe) {
            wp_redirect('/');
            exit;
        }
        $gestionnaire = wp_get_current_user();
        $email = $_POST['equipe-inviter'];

        $user = get_user_by('email', $email);

        if (!$user || !in_array('customer', $user->roles)) {
            wp_redirect('/mon-compte/equipe/?erreur=user-not-found&equipe-inviter=' . urlencode($email));
            exit;
        }
        $invitation = equipe_invitation($equipe, $user);
        $codes = [
            ['{gestionnaire}' => $gestionnaire->display_name],
            ['{email_gestionnaire}' => $gestionnaire->user_email],
            ['{nom_equipe}' => $equipe['post_title']],
            ['{url_rejoindre_equipe}' => site_url('/mon-compte/equipe/?equipe-rejoindre=' . $invitation['hash'])]
        ];
        $mail = charger_template_mail('brevo-16', $codes);

        $to  = $user->user_email;
        $headers = array('Content-Type: text/html; charset=UTF-8');
        $headers[] = 'Bcc: contact@coworking-metz.fr';
        if (wp_mail($to, $mail['subject'], $mail['message'], $headers)) {
            wp_redirect('/mon-compte/equipe/?status=invite-sent&equipe-inviter=' . urlencode($email));
            exit;
        }
        wp_redirect('/mon-compte/equipe/?erreur=inknown-error&equipe-inviter=' . urlencode($email));
        exit;
    });
}
