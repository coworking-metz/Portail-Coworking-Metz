<?php

/**
 * Remplir les champs de choix des templates dans la page des reglages des visites avec la liste des templates d'emails
 */
/*add_action('init', function () {

    $templates = brevo_getTemplates();
    
    foreach (['email_recap_visite', 'email_alerte_avent','email_alerte_cowo', 'email_confirmation_de_visite', 'email_finalisation_compte'] as $nom_champ) {
        add_filter('acf/load_field/name=' . $nom_champ, function ($field) use ($templates) {

            $field['choices'] = [''];
                foreach ($templates as $template) {
                    $field['choices'][$template['id']] = '#' . $template['id'] . ' - ' . $template['name'];
                }

            return $field;
        });
    }
});
*/

/**
 * Remplir les champs de choix des templates dans la page des reglages des visites avec la liste des templates d'emails
 */
add_action('init', function () {
    $args = array(
        'post_type' => 'viwec_template',
        'post_status' => 'publish',
        'posts_per_page' => -1,
    );
    $posts = get_posts($args);
    $templates = brevo_getTemplates();

    foreach (['email_recap_visite', 'email_alerte_avent','email_alerte_cowo', 'email_confirmation_de_visite', 'email_finalisation_compte'] as $nom_champ) {
        add_filter('acf/load_field/name=' . $nom_champ, function ($field) use ($posts, $templates) {

            $field['choices'] = [''];
            foreach ($templates as $template) {
                $field['choices']['brevo-'.$template['id']] = 'Brevo - #' . $template['id'] . ' - ' . $template['name'];
            }

            if (!empty($posts)) {
                foreach ($posts as $post) {
                    $field['choices'][$post->ID] = 'Wordpress - #' . $post->ID . ' - ' . $post->post_title;
                }
            }

            return $field;
        });
    }
});
