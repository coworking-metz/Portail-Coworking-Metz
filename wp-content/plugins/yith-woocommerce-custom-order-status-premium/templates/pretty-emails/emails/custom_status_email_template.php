<?php
/**
 * Customer Status email template for pretty email plugin
 *
 * @author        Yithemes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly ?>

<?php include MBWPE_TPL_PATH . '/settings.php'; ?>

<?php do_action( 'woocommerce_email_header', $email_heading, $email ); ?>

	<p><?php echo wp_kses_post( $custom_message ); ?></p>

<?php do_action( 'woocommerce_email_before_order_table', $order, $sent_to_admin, $plain_text ); ?>

	<h2 <?php echo esc_attr( $orderref ); ?>><?php printf( esc_html__( 'Order #%s', 'woocommerce' ), $order->get_order_number() ); ?></h2>

	<table cellspacing="0" cellpadding="6" style="border-collapse:collapse; width: 100%; border: 1px solid <?php echo esc_attr( $bordercolor ); ?>;" border="1" bordercolor="<?php echo esc_attr( $bordercolor ); ?>">
		<thead>
		<tr>
			<th scope="col" width="50%" style="<?php echo esc_attr( $missingstyle ); ?>text-align:center; border: 1px solid <?php echo esc_attr( $bordercolor ); ?>;"><?php esc_html_e( 'Product', 'woocommerce' ); ?></th>
			<th scope="col" width="25%" style="<?php echo esc_attr( $missingstyle ); ?>text-align:center; border: 1px solid <?php echo esc_attr( $bordercolor ); ?>;"><?php esc_html_e( 'Quantity', 'woocommerce' ); ?></th>
			<th scope="col" width="25%" style="<?php echo esc_attr( $missingstyle ); ?>text-align:center; border: 1px solid <?php echo esc_attr( $bordercolor ); ?>;"><?php esc_html_e( 'Price', 'woocommerce' ); ?></th>
		</tr>
		</thead>
		<tbody>
		<?php include MBWPE_TPL_PATH . '/tbody.php'; ?>
		</tbody>
		<?php include MBWPE_TPL_PATH . '/tfoot.php'; ?>
	</table>

<?php do_action( 'woocommerce_email_after_order_table', $order, $sent_to_admin, $plain_text ); ?>

<?php do_action( 'woocommerce_email_order_meta', $order, $sent_to_admin, $plain_text ); ?>

<?php do_action( 'woocommerce_email_customer_details', $order, $sent_to_admin, $plain_text ); ?>

<?php do_action( 'woocommerce_email_footer', $email ); ?>

<?php include MBWPE_TPL_PATH . '/treatments.php'; ?>