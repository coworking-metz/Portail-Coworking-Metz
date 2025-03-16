<?php



// Ajoute une meta robots noindex,nofollow sur des pages spécifiques
add_action('wp_head', function() {
    if (!is_page(['boutique', 'panier', 'commande', 'mon-compte', 'election-ca'])) {
        return;
    }
    echo '<meta name="robots" content="noindex,nofollow" />' . "\n";
});

// Ajoute noindex,nofollow pour les produits WooCommerce ayant la visibilité "Caché"
add_action('wp_head', function () {
    if (!is_singular('product')) return;

    global $post;

    $visibility = wp_get_post_terms($post->ID, 'product_visibility', ['fields' => 'slugs']);

    if (in_array('exclude-from-catalog', $visibility, true) && in_array('exclude-from-search', $visibility, true)) {
        echo '<meta name="robots" content="noindex,nofollow" />' . "\n";
    }
});

// Ajoute automatiquement noindex,nofollow sur toutes les pages publiques des utilisateurs
add_action('wp_head', function () {
    if (is_author()) {
        echo '<meta name="robots" content="noindex,nofollow" />' . "\n";
    }
});
