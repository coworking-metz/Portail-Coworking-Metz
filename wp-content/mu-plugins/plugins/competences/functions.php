<?php


function get_annuaire_users() {
    $args = [
        'meta_key'     => 'annuaire_optin',
        'meta_value'   => '1',
        'meta_compare' => '=',
        'orderby'      => ['last_name' => 'ASC', 'first_name' => 'ASC'],
        'fields'       => 'all',
        'number'       => -1,
    ];

    return get_users($args);
}
