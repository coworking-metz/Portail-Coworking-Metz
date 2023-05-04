<?php
! defined( 'ABSPATH' ) && exit; // Exit if accessed directly

extract( $args );
$is_multiple = isset( $multiple ) && $multiple;
$multiple    = ( $is_multiple ) ? ' multiple' : '';
$class       = isset( $class ) ? $class : '';

if ( $is_multiple ) {
	$value = ! empty( $value ) && is_array( $value ) ? $value : array();
}
?>
<div id="<?php echo esc_attr( $id ); ?>-container" <?php if ( isset( $deps ) ): ?>data-field="<?php echo esc_attr( $id ); ?>" data-dep="<?php echo esc_attr( $deps['ids'] ); ?>" data-value="<?php echo esc_attr( $deps['values'] ); ?>" <?php endif ?>>
	<label for="<?php echo esc_attr( $id ); ?>"><?php echo esc_html( $label ); ?></label>
	<div class="yith-wccos-select_wrapper">
		<select<?php echo esc_attr( $multiple ); ?> id="<?php echo esc_attr( $id ); ?>" class="<?php echo esc_attr( $class ); ?>" name="<?php echo esc_attr( $name ); ?><?php if ( $is_multiple )
			echo "[]" ?>" <?php if ( isset( $std ) ) : ?>data-std="<?php echo esc_attr( $is_multiple ? implode( ' ,', $std ) : $std ); ?>"<?php endif ?>>
			<?php foreach ( $options as $key => $item ) : ?>
				<option value="<?php echo esc_attr( $key ); ?>" <?php if ( $is_multiple ): selected( true, in_array( $key, $value ) );
				else: selected( $key, $value ); endif; ?> ><?php echo esc_html( $item ); ?></option>
			<?php endforeach; ?>
		</select>
	</div>

	<span class="desc inline"><?php echo esc_html( $desc ); ?></span>
</div>