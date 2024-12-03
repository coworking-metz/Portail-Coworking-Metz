<?php


add_filter('wp_handle_upload_prefilter', function ($file) {


    if (current_user_can('administrator')) return $file;
    if (current_user_can('upload-all-image-types')) return $file;

    if ($file['type'] == 'image/png') {
        if (pngTojpeg($file['tmp_name'], $file['tmp_name'])) {
            $file['type'] = 'image/jpeg';
            $file['name'] = str_ireplace('.png', '.jpg', $file['name']);
            $file['size'] = filesize($file['tmp_name']);
        }
    }
    return $file;
});

if (isset($_GET['polaroid_bad'])) {
    add_action('pre_get_posts', function ($query) {
        // Check if we are in the admin area and the correct post type is queried
        if (is_admin() && $query->query['post_type'] == 'attachment') {
            $query->set('meta_key', 'polaroid_bad');
            $query->set('meta_value', '1');
        }
    });
}

add_filter('media_row_actions', function ($actions, $post) {
    // Vous pouvez ajouter des vérifications conditionnelles ici si nécessaire
    // Par exemple, pour n'afficher le lien que si la métadonnée spécifique n'est pas déjà définie
    $bad = get_post_meta($post->ID, 'polaroid_bad', true);
    if ($bad) {
        $url = admin_url('upload.php?action=set_polaroid_valid&amp;post=' . $post->ID);
        $actions['set_polaroid_valid'] = '<a href="' . wp_nonce_url($url, 'set_polaroid_valid_' . $post->ID) . '" aria-label="' . esc_attr__('Marquer cette photo comme valide', 'text-domain') . '">' . esc_html__('Accepter cette photo', 'text-domain') . '</a>';
    }
    if (isset($actions['copy'])) {
        unset($actions['copy']);
    }
    if (isset($actions['view'])) {
        unset($actions['view']);
    }
    if (isset($actions['edit'])) {
        unset($actions['edit']);
    }
    if (isset($actions['delete'])) {
        $actions['delete'] = str_replace('Supprimer définitivement', '🗑️', $actions['delete']);
    }
    if (isset($actions['download'])) {
        $actions['download'] = str_replace('Télécharger le fichier', '💾', $actions['download']);
    }
    return $actions;
}, 10, 2);


add_action('admin_init', function () {
    $action = $_REQUEST['action'] ?? '';
    if ('set_polaroid_valid' === $action) {
        $post_id = $_REQUEST['post'] ?? 0;
        check_admin_referer('set_polaroid_valid_' . $post_id); // Sécurité pour vérifier que la demande vient bien de WordPress

        // Vérifier que l'utilisateur a la capacité de modifier le post
        if (!current_user_can('edit_post', $post_id)) {
            wp_die(__('Vous n’avez pas les permissions nécessaires pour modifier ce post.', 'text-domain'));
        }
        // Mettre à jour la métadonnée
        delete_post_meta($post_id, 'polaroid_bad');
        update_field('polaroid_checked', true, $post_id);

        wp_redirect($_SERVER['HTTP_REFERER']);
        exit;
    }
});
