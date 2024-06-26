<?php
/**
 * Admin new order email (plain text)
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/emails/plain/admin-new-order.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you (the theme developer).
 * will need to copy the new files to your theme to maintain compatibility. We try to do this.
 * as little as possible, but it does happen. When this occurs the version of the template file will.
 * be bumped and the readme will list any important changes.
 *
 * @see            http://docs.woothemes.com/document/template-structure/
 * @author         WooThemes
 * @package        WooCommerce/Templates/Emails/Plain
 * @version        2.5.0
 */

if ( !defined( 'ABSPATH' ) ) {
    exit;
}

echo "=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n";
echo esc_html( wp_strip_all_tags( $email_heading ) );
echo "\n=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n\n";

echo esc_html( wp_strip_all_tags( wptexturize( $custom_message ) ) ) . "\n\n";

if ( $display_order_info ) {

	echo "----------------------------------------\n\n";

    /**
     * @hooked WC_Emails::order_details() Shows the order details table.
     * @since  2.5.0
     */
    do_action( 'woocommerce_email_order_details', $order, $sent_to_admin, $plain_text, $email );

	echo "\n----------------------------------------\n\n";

    /**
     * @hooked WC_Emails::order_meta() Shows order meta data.
     */
    do_action( 'woocommerce_email_order_meta', $order, $sent_to_admin, $plain_text, $email );

    /**
     * @hooked WC_Emails::customer_details() Shows customer details
     * @hooked WC_Emails::email_address() Shows email address
     */
    do_action( 'woocommerce_email_customer_details', $order, $sent_to_admin, $plain_text, $email );

}
echo "\n----------------------------------------\n\n";

echo wp_kses_post( apply_filters( 'woocommerce_email_footer_text', get_option( 'woocommerce_email_footer_text' ) ) );
