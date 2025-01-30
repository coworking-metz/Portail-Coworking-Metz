<?php
/**
 * The template for displaying pages
 *
 * @package Ave theme
 */

// Redirect non-logged-in users to the login page
if ( ! is_user_logged_in() ) {
    wp_redirect( site_url( '/mon-compte/?redirect_to=' ) . urlencode( $_SERVER['REQUEST_URI'] ) );
    exit;
}

get_header();

while ( have_posts() ) : the_post();

    liquid_get_content_template();

endwhile;

get_footer();
