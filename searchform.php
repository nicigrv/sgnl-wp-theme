<?php
/**
 * Suchformular.
 *
 * @package sgnl-neptun
 */
$sgnl_sid = wp_unique_id( 'search-' );
?>
<form role="search" method="get" class="search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<label class="screen-reader-text" for="<?php echo esc_attr( $sgnl_sid ); ?>"><?php esc_html_e( 'Suche nach', 'sgnl-neptun' ); ?></label>
	<input type="search" id="<?php echo esc_attr( $sgnl_sid ); ?>" class="search-field" placeholder="<?php esc_attr_e( 'Suchen …', 'sgnl-neptun' ); ?>" value="<?php echo get_search_query(); ?>" name="s">
	<button type="submit" class="search-submit"><?php echo sgnl_icon( 'search', 18 ); // phpcs:ignore ?><span class="screen-reader-text"><?php esc_html_e( 'Suchen', 'sgnl-neptun' ); ?></span></button>
</form>
