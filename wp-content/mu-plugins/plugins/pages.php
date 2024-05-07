<?php
add_filter('body_class', function($classes) {
    if (is_page()) {
        $slug = basename(get_permalink());
        $classes[] = 'page-' . $slug;
    }
    return $classes;
});
