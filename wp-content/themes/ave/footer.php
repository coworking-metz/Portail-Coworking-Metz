<?php
/**
 * The template for displaying the footer
 *
 * Contains the closing of the main containers
 *
 * @package Ave theme
 */
?>

			<?php liquid_action( 'after_content' ); ?>
		</main><!-- #content -->
		<?php
		liquid_action( 'before_footer' );
		liquid_action( 'footer' );
		liquid_action( 'after_footer' );
		?>

	</div><!-- .site-container -->
	<div class="hide tickets-status"><p><em>1</em></p></div>
	<?php liquid_action( 'after' ) ?>

	<?php wp_footer(); ?>
</body>
</html>