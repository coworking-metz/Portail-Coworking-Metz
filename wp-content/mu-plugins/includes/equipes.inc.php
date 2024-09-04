<?php


function equipe_rejoindre($equipe, $memberId, $role = 'member')
{
    if (is_numeric($equipe)) {
        $eid = $equipe;
    } else {
        $equipe = (array)$equipe;
        $eid = $equipe['ID'];
    }
    $membres = get_field('membres', $eid);
    if ($membres) {
        $ids = array_column(array_column($membres, 'membre'), 'ID');
        if (in_array($memberId, $ids)) {
            return false;
        }
    }
    $membres[] = ['membre' => $memberId, 'role' => $role];
    update_field('membres', $membres, $eid);
}
function getMonEquipe($uid, $gestionnaire = true)
{
    $equipes = get_posts([
        'post_type' => 'equipe',
        'posts_per_page' => -1,  // Rechercher tous les posts
    ]);
    foreach ($equipes as $equipe) {
        $membres = get_field('membres', $equipe->ID);
        if (!$membres) continue;
        foreach ($membres as $membre) {

            if ($gestionnaire && $membre['role'] != 'admin') continue;
            if ($membre['membre']['ID'] == $uid) {
                $equipe = (array)$equipe;
                $equipe['membres'] = [];
                foreach ($membres as $m) {
                    $tmp = $m['membre'];
                    $tmp['balance'] = get_user_balance($m['membre']);
                    $tmp['equipe-role'] = $m['role'];
                    $tmp['photo'] = 'https://photos.coworking-metz.fr/photo/size/thumbnail/' . $tmp['ID'] . '.jpg';
                    // $tmp['polaroid'] = 'https://photos.coworking-metz.fr/polaroid/size/thumbnail/' . $tmp['ID'] . '.jpg';
                    $equipe['membres'][] = $tmp;
                }
                $invitations = equipe_invitations($equipe);
                foreach ($invitations as $invitation) {
                    $tmp = (array)$invitation['user']->data;
                    $tmp['equipe-role'] = 'waiting';
                    $options = get_field('polaroids', 'option');
                    $tmp['photo'] = $options['photo_par_defaut'];
                    $equipe['membres'][] = $tmp;
                }
                $equipe['role'] = $membre['role'];
                return $equipe;
            }
        }
    }
    if ($gestionnaire) {
        return getMonEquipe($uid, false);
    }
    return null;  // Retourner null si aucun post correspondant n'est trouvé
}

function getInvitationParHash($hash, $uid)
{
    $equipes = get_posts([
        'post_type' => 'equipe',
        'posts_per_page' => -1,  // Rechercher tous les posts
    ]);
    foreach ($equipes as $equipe) {
        $invitations = equipe_invitations($equipe);
        foreach ($invitations as $invitation) {
            if ($invitation['hash'] != $hash) continue;
            if ($invitation['user']->ID != $uid) continue;
            $invitation['equipe'] = $equipe;
            return $invitation;
        }
    }
}
function equipe_end_invitation($equipe, $hash)
{
    $equipe = (array) $equipe;
    $equipe_id = $equipe['ID'] ?? false;
    if (!$equipe_id) return;
    $invites = get_post_meta($equipe_id, 'invited_users', true);
    $invites = $invites ? unserialize($invites) : [];
    foreach ($invites as &$invite) {
        if ($invite['hash'] == $hash) {
            $invite = false;
        }
    }
    $invites = array_filter($invites);
    return update_post_meta($equipe_id, 'invited_users', serialize($invites));
}
function equipe_invitations($equipe)
{
    $equipe = (array) $equipe;
    $equipe_id = $equipe['ID'] ?? false;
    if (!$equipe_id) return;
    $invites = get_post_meta($equipe_id, 'invited_users', true);
    $invites = $invites ? unserialize($invites) : [];
    return $invites;
}

/**
 * Invite a user to an equipe and store the invitation in post meta.
 *
 * @param mixed  $equipe Post ID of the equipe.
 * @param mixed $user User ID of the invited user.
 */
function equipe_invitation($equipe, $user)
{
    $equipe = (array) $equipe;
    $equipe_id = $equipe['ID'] ?? false;
    if (!$equipe_id) return;
    $invites = equipe_invitations($equipe);
    foreach ($invites as $invite) {
        if ($invite['user']->ID == $user->ID) return $invite;
    }
    $invite = ['user' => $user, 'hash' => sha1(microtime() . rand())];
    $invites[] = $invite;
    update_post_meta($equipe_id, 'invited_users', serialize($invites));
    return $invite;
}

/**
 * Récupère l'équipe associée à un gestionnaire spécifique par son ID utilisateur.
 *
 * @param int $uid ID de l'utilisateur.
 * @return WP_Post|null Le post de l'équipe correspondante ou null si non trouvé.
 */
function getEquipePourGestionnaire($uid)
{
    $equipes = get_posts([
        'post_type' => 'equipe',
        'posts_per_page' => -1,  // Rechercher tous les posts
    ]);
    foreach ($equipes as $equipe) {
        $membres = get_field('membres', $equipe->ID);
        foreach ($membres as $membre) {
            if ($membre['role'] != 'admin') continue;
            if ($membre['membre']['ID'] == $uid) {
                $equipe = (array)$equipe;
                $equipe['membres'] = [];
                foreach ($membres as $membre) {
                    $tmp = $membre['membre'];
                    $tmp['equipe-role'] = $membre['role'];
                    $tmp['photo'] = 'https://photos.coworking-metz.fr/photo/size/thumbnail/' . $tmp['ID'] . '.jpg';
                    // $tmp['polaroid'] = 'https://photos.coworking-metz.fr/polaroid/size/thumbnail/' . $tmp['ID'] . '.jpg';
                    $equipe['membres'][] = $tmp;
                }
                $invitations = equipe_invitations($equipe);
                foreach ($invitations as $invitation) {
                    $tmp = (array)$invitation['user']->data;
                    $tmp['equipe-role'] = 'waiting';
                    $options = get_field('polaroids', 'option');
                    $tmp['photo'] = $options['photo_par_defaut'];
                    $equipe['membres'][] = $tmp;
                }
                return $equipe;
            }
        }
    }
    return null;  // Retourner null si aucun post correspondant n'est trouvé
}
function isInEquipe($user, $equipe)
{
    $uid = $user->ID ?? $user;

    return $uid && in_array($uid, array_column($equipe['membres'], 'ID'));
}
function equipe_role($role)
{
    if ($role == 'waiting') return 'Invitation envoyée';
    if ($role == 'admin') return 'Gestionnaire';
    return 'Membre';
}


function equipe_get_erreur($id_erreur)
{
    if ($id_erreur == 'user-not-found') {
        return 'Impossible de trouver un coworker actif avec cette adresse e-mail.';
    }
}


function equipe_set_admin_cookie($user)
{
    $expiration = time() + 14 * DAY_IN_SECONDS;
    $cookie = (array)$user->data;
    unset($cookie['user_pass']);
    return setcookie('wp-equipe-admin-user', serialize($cookie), $expiration, '/');
}

function equipe_log_in_has($user_id)
{
    $user = get_userdata($user_id)->user_login;
    wp_set_current_user($user_id);
    wp_set_auth_cookie($user_id);
    // do_action('wp_login', $user);
    return true;
}


function equipe_admin_logged_in_as()
{
    $uid = get_current_user_id();
    if (!$uid) return;

    $data = $_COOKIE['wp-equipe-admin-user'] ?? false;

    if (!$data) return;

    $data = unserialize(stripslashes($data));

    if ($data['ID'] == $uid) {
        setcookie('wp-equipe-admin-user');
        return;
    }
    return $data;
}
