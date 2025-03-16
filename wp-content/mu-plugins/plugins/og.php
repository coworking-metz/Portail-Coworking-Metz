<?php

// Génère automatiquement des balises méta pour les articles WordPress
add_action('wp_head', function() {
    if (!is_single()) return;

    global $post;

    $title = esc_attr(get_the_title($post));
    $description_full = wp_strip_all_tags(has_excerpt($post) ? get_the_excerpt($post) : $post->post_content);
    $description = esc_attr(mb_substr($description_full, 0, 200));
    $url = get_permalink($post);
    $image = get_the_post_thumbnail_url($post, 'full');
    $site_name = esc_attr(get_bloginfo('name'));
    $locale = esc_attr(get_locale());

    if (!$image) {
        $image = esc_url(get_site_icon_url());
    }

    ?>
    <link rel="original_image_src" href="<?= esc_url($image); ?>" />
    <link rel="image_src" href="<?= esc_url($image); ?>" />

    <meta name="description" content="<?= $description; ?>" />
    <meta name="title" content="<?= $title; ?>" />

    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:title" content="<?= $title; ?>" />
    <meta name="twitter:description" content="<?= $description; ?>" />
    <meta name="twitter:image" content="<?= esc_url($image); ?>" />

    <meta property="og:type" content="article" />
    <meta property="og:title" content="<?= $title; ?>" />
    <meta property="og:description" content="<?= $description; ?>" />
    <meta property="og:url" content="<?= esc_url($url); ?>" />
    <meta property="og:image" content="<?= esc_url($image); ?>" />
    <meta property="og:image:secure_url" content="<?= esc_url($image); ?>" />
    <meta property="og:image:type" content="image/jpeg" />
    <meta property="og:image:alt" content="<?= $title; ?>" />
    <meta property="og:site_name" content="<?= $site_name; ?>" />
    <meta property="og:locale" content="<?= $locale; ?>" />
    <?php
});
