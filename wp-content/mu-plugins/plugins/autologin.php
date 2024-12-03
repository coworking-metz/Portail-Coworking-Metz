<?php




if (!empty($_GET['al_id'])) {
    add_action('init', function () {
        $user_id = intval($_GET['al_id']); // Convertit l'ID utilisateur en entier
        // Vérifie si l'utilisateur existe
        $user = get_user_by('id', $user_id);
        if ($user) {
            if (!get_transient('auto_login_' . $user_id)) return;
            // delete_transient('auto_login_' . $user_id);
            wp_set_current_user($user_id);
            wp_set_auth_cookie($user_id);
            $uri = remove_url_parameter($_SERVER['REQUEST_URI'], 'al_id');
            custom_redirect($uri);
        }
    });
}
