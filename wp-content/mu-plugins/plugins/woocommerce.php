<?php
define('WOOCOMMERCE_CLOSED', false);
define('WOOCOMMERCE_CLOSED_ALLOW_ADMINS', true);
define('WOOCOMMERCE_CLOSED_MESSAGE', 'La boutique est fermée temporairement pour maintenance. Merci de revenir dans quelques instants.');


if (WOOCOMMERCE_CLOSED) {
    // Disable purchasing capabilities
    add_filter('woocommerce_is_purchasable', function ($purchasable, $product) {
        if (WOOCOMMERCE_CLOSED_ALLOW_ADMINS && current_user_can('administrator')) return $purchasable;
        return false; // Makes all products unpurchasable
    }, 10, 2);

    add_filter('woocommerce_add_to_cart_validation', function ($valid, $product_id, $quantity) {
        if (WOOCOMMERCE_CLOSED_ALLOW_ADMINS && current_user_can('administrator')) return $valid;
        wc_add_notice(__('Sorry, our shop is currently closed.', 'woocommerce'), 'error');
        return false; // Prevents adding products to the cart
    }, 10, 3);

    // Display a shop closure notice
    add_action('woocommerce_before_main_content', function () {
        if (WOOCOMMERCE_CLOSED_ALLOW_ADMINS && current_user_can('administrator')) {
            echo '<div style="margin-block:.5em;background-color: #ff2f00; color:white;padding: 10px; text-align: center;">La boutique est fermée au membres pour maintenance. Les administrateurs ont accès à la boutique.</div>';
        } else {
            echo '<div style="margin-block:.5em;background-color: #ff2f00; color:white;padding: 10px; text-align: center;">' . esc_html__(WOOCOMMERCE_CLOSED_MESSAGE, 'woocommerce') . '</div>';
        }
    });
}


add_action( 'wp', function () {
    remove_theme_support( 'wc-product-gallery-zoom' );
}, 100 );

add_filter('woocommerce_post_class', function ($classes) {
    if ('product' == get_post_type()) {

        $pid = get_the_ID();
        $terms = wp_get_post_terms($pid, 'product_cat');
        $classes = array_merge($classes, array_map(function ($slug) {
            return 'cat-' . $slug;
        }, array_column($terms, 'slug')));
    }
    return $classes;
}, 21, 3); //woocommerce use priority 20, so if you want to do something after they finish be more lazy


add_filter('woocommerce_available_payment_gateways', function ($available_gateways) {
    if (is_admin() && !defined('DOING_AJAX')) {
        return $available_gateways;
    }

    $user = wp_get_current_user();

    if (current_user_can('administrator')) return $available_gateways;

    if (get_field('payer_en_virement', $user)) return $available_gateways;

    unset($available_gateways['bacs']); // 'bacs' is the id for bank transfer

    return $available_gateways;
});

add_filter('woocommerce_webhook_payload', function ($payload, $resource, $resource_id, $id) {
    $order = wc_get_order($resource_id);

    if ($order && isset($payload['line_items'])) {
        // Loop through each item in the order
        foreach ($payload['line_items'] as &$item) {
            $item['productType'] = convertProductType(get_field('productType', $item['product_id']));
        }
    }
    return $payload;
}, 10, 4);



// Génère automatiquement des balises méta pour les produits WooCommerce
add_action('wp_head', function() {
    if (!is_product()) return;

    global $post;
    $product = wc_get_product($post->ID);

    if (!$product) return;

    $title = esc_attr($product->get_name());
	$description_full = wp_strip_all_tags($product->get_short_description() ?: $product->get_description());
    $description = esc_attr(mb_substr($description_full, 0, 200));
    $url = get_permalink($product->get_id());
    $image = wp_get_attachment_url($product->get_image_id());
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

    <meta property="og:type" content="product" />
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
