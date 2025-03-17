<?php

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
    
    foreach (['email_recap_visite', 'email_rappel_visite', 'email_alerte_avent','email_alerte_cowo','email_alerte_cowo_nomade', 'email_confirmation_de_visite', 'email_confirmation_nomade', 'email_finalisation_compte'] as $nom_champ) {
        add_filter('acf/load_field/name=' . $nom_champ, function ($field) use ($posts) {
            $templates = brevo_getTemplates();
            
            $field['choices'] = [''];
            foreach ($templates as $template) {
                $field['choices']['brevo-'.$template['id']] = 'Brevo - #' . str_pad($template['id'],3,0,STR_PAD_LEFT) . ' - ' . $template['name'];
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
