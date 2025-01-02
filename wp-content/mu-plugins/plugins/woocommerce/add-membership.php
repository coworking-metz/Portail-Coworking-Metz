<?php

// Hook into WooCommerce to automatically add a specific product to the cart.
add_action('wp_loaded', function () {
	// Check if WooCommerce cart is available and skip admin pages.
    if (!class_exists('WooCommerce') || is_admin()) {
        return;
    }

	$uri = $_SERVER['REQUEST_URI']??'';
	if(!strstr($uri, 'panier') && !strstr($uri, 'boutique')) return;

	$user_id = get_current_user_id();
	if(!$user_id) return;
	
	if(has_valid_membership($user_id)) return; 

    // Check if the cart already contains the product.
    $product_id = get_latest_adhesion_product_id();

    if (!$product_id) return;

	if(WC()->cart->find_product_in_cart(WC()->cart->generate_cart_id($product_id))) return;

    // Add the product to the cart.
    WC()->cart->add_to_cart($product_id);
});

/**
 * Retrieve the latest published product with the meta key 'productType' set to 'adhesion'.
 *
 * @return int|null The product ID or null if no product is found.
 */
function get_latest_adhesion_product_id()
{
    $query = new WP_Query([
        'post_type'      => 'product',
        'posts_per_page' => 1,
        'post_status'    => 'publish',
        'meta_query'     => [
            [
                'key'   => 'productType',
                'value' => 'adhesion',
            ],
        ],
        'orderby'        => 'date',
        'order'          => 'DESC',
    ]);

    if ($query->have_posts()) {
        $query->the_post();
        $product_id = get_the_ID();
        wp_reset_postdata();
        return $product_id;
    }

    wp_reset_postdata();
    return null;
}
