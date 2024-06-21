<?php

if (isset($_GET['equipe-retirer'])) {
    add_action('init', function () {
        if (!is_user_logged_in()) {
            wp_redirect('/mon-compte/?redirect=' . urlencode($_SERVER['REQUEST_URI']));
            exit;
        }
        $uid = get_current_user_id();
        $membreId = $_GET['equipe-retirer'] ?? '';
        $equipeMembre = getMonEquipe($membreId);
        $equipe = getMonEquipe($uid);

        if ($equipe['ID'] != $equipeMembre['ID']) $erreur = true;
        else if ($equipe['role'] != 'admin') $erreur = true;
        if ($erreur) {
            wp_redirect('/mon-compte/equipe/');
        } else {
            $membres = get_field('membres', $equipe['ID']);
            foreach ($membres as &$membre) {
                if ($membre['membre']['ID'] == $membreId)
                    $membre = false;
            }
            update_field('membres', array_filter($membres), $equipe['ID']);
            wp_redirect('/mon-compte/equipe/?status=member-removed');
        };
        exit;
    });
}

if (isset($_GET['equipe-quitter'])) {
    add_action('init', function () {
        if (equipe_admin_logged_in_as()) 
        wp_redirect_notification('/mon-compte/equipe/', ['temporaire' => true, 'titre' => 'Action impossible', 'texte' => 'Vous ne pouvez pas faire celà.']);
        
        if (!is_user_logged_in()) {
            wp_redirect('/mon-compte/?redirect=' . urlencode($_SERVER['REQUEST_URI']));
            exit;
        }
        $uid = get_current_user_id();
        $membreId = $_GET['equipe-quitter'] ?? '';

        $equipeMembre = getMonEquipe($membreId);
        $equipe = getMonEquipe($uid);

        if ($equipe['ID'] != $equipeMembre['ID']) $erreur = true;
        if ($uid != $membreId) $erreur = true;

        if ($erreur) {
            wp_redirect('/mon-compte/equipe/');
        } else {
            $membres = get_field('membres', $equipe['ID']);
            foreach ($membres as &$membre) {
                if ($membre['membre']['ID'] == $membreId)
                    $membre = false;
            }
            update_field('membres', array_filter($membres), $equipe['ID']);
            wp_redirect_notification('/mon-compte/equipe/', ['temporaire' => true, 'titre' => 'Équipe ' . $equipe['post_title'], 'texte' => 'Vous ne faites plus partie de cette équipe.']);
        };
        exit;
    });
}
