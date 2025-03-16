<?php


// Exclut des URLs spécifiques du sitemap XML de WordPress
add_filter('wp_sitemaps_posts_query_args', function ($args, $post_type) {
    if ('page' === $post_type) {
        $excluded_slugs = ['boutique', 'panier', 'commande', 'mon-compte', 'election-ca'];
        $excluded_pages = get_posts([
            'post_type' => 'page',
            'post_name__in' => $excluded_slugs,
            'fields' => 'ids',
            'numberposts' => -1,
        ]);
        if (!empty($excluded_pages)) {
            $args['post__not_in'] = array_merge($args['post__not_in'] ?? [], $excluded_pages);
        }
    }
    return $args;
}, 10, 2);


// Exclut des produits "cachés" du sitemap XML de WordPress
add_filter('wp_sitemaps_posts_query_args', function ($args, $post_type) {
    if ('product' === $post_type) {
        $hidden_products = get_posts([
            'post_type'   => 'product',
            'numberposts' => -1,
            'tax_query'   => [
                [
                    'taxonomy' => 'product_visibility',
                    'field'    => 'name',
                    'terms'    => ['exclude-from-catalog', 'exclude-from-catalog'],
                    'operator' => 'IN',
                ],
            ],
            'fields' => 'ids',
        ]);

        if (!empty($hidden_products)) {
            $args['post__not_in'] = array_merge($args['post__not_in'] ?? [], $hidden_products);
        }
    }
    return $args;
}, 10, 2);


// Exclut complètement les utilisateurs (auteurs) du sitemap XML WordPress
add_filter('wp_sitemaps_add_provider', function ($provider, $name) {
    if ('users' === $name) {
        return false;
    }
    return $provider;
}, 10, 2);
