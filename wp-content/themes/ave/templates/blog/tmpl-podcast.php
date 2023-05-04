<?php
$format = get_post_format();
$read_time = liquid_helper()->get_option( 'liquid-read-min-label' );
if( 'video' === $format ) {
?>
<div class="post-video">
	<?php $this->entry_thumbnail('liquid-grid' ) ?>
	<?php $this->entry_tags(); ?>
</div>
<?php
} else if( 'link' !== $format ) {
	$this->entry_thumbnail( 'liquid-grid' );
}
?>

<div class="liquid-blog-item-inner pb-4">
	<a href="<?php the_permalink()?>" class="liquid-overlay-link"><?php the_title() ?></a>
	<header class="liquid-lp-header px-4 mb-4 mb-md-5 pt-1">
		<div class="liquid-lp-details">
		<?php $this->entry_tags(); ?>
		</div>
		<?php $this->entry_title( 'font-weight-semibold h3 size-sm' ); ?>
	</header>
	
	<footer class="liquid-lp-footer px-4 pt-md-2">
		<div class="liquid-lp-details">
			<?php $this->entry_tags(); ?>
			<?php if ( $read_time ): ?>
			| <span class="liquid-lp-meta"><?php echo esc_html($read_time); ?></span>
			<?php endif; ?>
		</div>
	</footer>
</div>