<?php


add_filter('acf/load_field/name=polaroid_nom', function ($field) {
    $user = wp_get_current_user();
    if ($user) {
        $field['default_value'] = $user->display_name;
    }
    return $field;
});
