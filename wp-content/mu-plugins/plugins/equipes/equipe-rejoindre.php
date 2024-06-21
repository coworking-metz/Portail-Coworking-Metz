<?php

if (isset($_GET['equipe-rejoindre'])) {
    add_action('init', function () {
        if (!is_user_logged_in()) {
            wp_redirect('/mon-compte/?redirect=' . urlencode($_SERVER['REQUEST_URI']));
            exit;
        }
        $uid = get_current_user_id();
        $hash = $_GET['equipe-rejoindre'] ?? '';
        $invitation = getInvitationParHash($hash, $uid);
        if (!$invitation) {
            wp_redirect('/mon-compte/equipe/?status=invite-error');
            exit;
        }
        $equipe = $invitation['equipe'];
        $membres = get_field('membres', $equipe->ID);
        $ids = array_column(array_column($membres, 'membre'), 'ID');
        if(in_array($uid, $ids)) {
            wp_redirect('/mon-compte/equipe/?status=invite-already');
            exit;
        }
        $membres[] = ['membre' => $invitation['user']->ID, 'role' => 'member'];
        update_field('membres', $membres, $equipe->ID);
        equipe_end_invitation($equipe, $hash);
        wp_redirect('/mon-compte/equipe/?status=invite-accepted');
        exit;
});
}
