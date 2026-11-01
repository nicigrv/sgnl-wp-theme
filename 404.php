<?php
/**
 * Nicht gefunden.
 *
 * @package sgnl-neptun
 */

get_header();
?>
<main id="content" class="site-main site-main--list">
	<div class="list-wrap">
		<div class="empty-state">
			<h2><?php esc_html_e( 'Diese Seite gibt es nicht (mehr).', 'sgnl-neptun' ); ?></h2>
			<p><?php esc_html_e( 'Vielleicht wurde sie verschoben. Über die Suche oder das Menü findest du weiter.', 'sgnl-neptun' ); ?></p>
			<?php get_search_form(); ?>
			<p><a class="text-link" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Zur Startseite', 'sgnl-neptun' ); ?></a></p>
		</div>
	</div>
</main>
<?php
get_footer();
