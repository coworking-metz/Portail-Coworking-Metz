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
