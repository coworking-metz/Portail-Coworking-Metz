<?php

// Obtenir le nombre de visites
function getNbVisites()
{
    return count(fetch_users_with_future_visite());
}

// Obtenir et stocker les utilisateurs avec des visites futures dans un transitoire
function fetch_users_with_future_visite()
{
    $args = array(
        'meta_key'     => 'visite',
        'meta_compare' => '>',
        'meta_value'   => current_time('mysql'),
        'meta_type'    => 'DATETIME',
    );

    $users_with_future_visite = get_users($args);

    return $users_with_future_visite;
}



add_action('init', function () {
    $args = array(
        'post_type' => 'viwec_template',
        'post_status' => 'publish',
        'posts_per_page' => -1,
    );
    $posts = get_posts($args);

    foreach (['email_alerte_cowo', 'email_confirmation_de_visite'] as $nom_champ) {
        add_filter('acf/load_field/name=' . $nom_champ, function ($field) use ($posts) {

            $field['choices'] = [''];
            if (!empty($posts)) {
                foreach ($posts as $post) {
                    $field['choices'][$post->ID] = '#' . $post->ID . ' - ' . $post->post_title;
                }
            }

            return $field;
        });
    }
});

add_action('admin_footer', function () {
    $screen = get_current_screen();
    if ($screen->base == 'toplevel_page_reglages-visites') {
?>
        <script type="text/javascript">
            document.addEventListener('DOMContentLoaded', function() {
                const selectFields = document.querySelectorAll('[data-name*="email_"][data-type="select"] select');
                selectFields.forEach(function(select) {
                    console.log(select);
                        const link = document.createElement('a');
                        link.target = '_blank';
                        link.innerText = 'Voir ce template';
                        select.parentNode.appendChild(link);

                        function updateLink() {
                            const value = this.value;
                            link.href = `post.php?post=${value}&action=edit&classic-editor`;
                        }

                        // Lien initial
                        updateLink.call(select);

                        select.addEventListener('change', function() {
                            // Mettre à jour le lien lors du changement de sélection
                            updateLink.call(this);
                        });
                });
            });
        </script>
<?php
    }
});


add_action('admin_bar_menu', function ($admin_bar) {
    if (is_admin()) {
        $screen = get_current_screen();
        if ($screen->base == 'toplevel_page_reglages-visites') {
            $admin_bar->add_menu(array(
                'id'    => 'voir',
                'title' => 'Voir la page de prise de rendez-vous',
                'href'  => 'https://rejoindre.coworking-metz.fr',
                'meta'  => array(
                    'target' => '_blank',
                    'title' => __('Voir la page'),
                ),
            ));
        }
    }
}, 100);

// Ajouter un lien dans le menu admin WP
add_action('admin_menu', function () {
    add_menu_page(
        'Visites',
        'Visites',
        'manage_options',
        'users.php?orderby=visite&order=desc',
        '',
        'dashicons-calendar',
        2
    );
});


// Ajouter un lien dans le menu admin WP
add_action('admin_menu', function () {
    global $menu;

    // Ajouter un nombre rouge
    foreach ($menu as $key => $value) {
        if ($menu[$key][0] == 'Visites') {

            $menu[$key][0] .= ' <span class="update-plugins count-1"><span class="update-count">' . getNbVisites() . '</span></span>';
            break;
        }
    }
}, 999);
