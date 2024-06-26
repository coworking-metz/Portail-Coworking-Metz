<?php
/**
 * The template for displaying the product availability
 *
 * @author  themeComplete
 * @package WooCommerce Extra Product Options/Templates
 * @version 5.0
 */

defined( 'ABSPATH' ) || exit;

if ( ! isset( $current_product ) || ! $current_product ) {
    return;
}

$availability = [];

if ( ! $current_product->is_in_stock() ) {
    $availability[ 'class' ] = 'out-of-stock';
} elseif ( $current_product->managing_stock() && $current_product->is_on_backorder( $quantity_min ) && $current_product->backorders_require_notification() ) {
    $availability[ 'class' ] = 'available-on-backorder';
} else {
    if ( ! $current_product->has_enough_stock( $quantity_min ) ) {
        $availability[ 'class' ] = 'out-of-stock';
    } else {
        $availability[ 'class' ] = 'in-stock';
    }
}

$translations = wc_get_product_stock_status_options();

if ( ! $current_product->is_in_stock() ) {

	$availability[ 'availability' ] = $translations[ 'outofstock' ];

} elseif ( $current_product->managing_stock() && $current_product->is_on_backorder( $quantity_min ) ) {

	if ( $current_product->backorders_require_notification() ) {

		$availability[ 'availability' ] = wc_format_stock_for_display( $current_product );

	} else {
		$availability[ 'availability' ] = $translations[ 'instock' ];
	}

} elseif ( $current_product->managing_stock() ) {

	$availability[ 'availability' ] = wc_format_stock_for_display( $current_product );

} else {

	$availability[ 'availability' ] = '';

}

if ( ! empty( $availability[ 'availability' ] ) ) {
	ob_start();

	wc_get_template( 'single-product/stock.php', array(
		'product'      => $current_product,
		'class'        => $availability[ 'class' ],
		'availability' => $availability[ 'availability' ],
	) );

	$availability_html = ob_get_clean();

} else {
	$availability_html = '';
}

echo wp_kses_post( $availability_html );