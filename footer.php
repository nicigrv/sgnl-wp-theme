<?php
/**
 * Footer.
 *
 * @package sgnl-neptun
 */
?>
<footer class="site-footer">
	<div class="lane-band" aria-hidden="true"></div>
	<div class="site-footer__inner">
		<div class="footer-brand">
			<p class="footer-brand__name"><?php echo esc_html( sgnl_opt( 'footer_brand' ) ); ?></p>
			<?php sgnl_social_links(); ?>
		</div>
		<?php foreach ( array( 'first_box_widgets', 'second_box_widgets', 'third_box_widgets' ) as $sidebar ) : ?>
			<?php if ( is_active_sidebar( $sidebar ) ) : ?>
				<div class="footer-col">
					<?php dynamic_sidebar( $sidebar ); ?>
				</div>
			<?php endif; ?>
		<?php endforeach; ?>
	</div>
	<div class="site-footer__bottom">
		<p class="copyright"><?php echo sgnl_copyright(); // phpcs:ignore ?></p>
		<?php do_action( 'sgnl_footer_bottom' ); ?>
	</div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
