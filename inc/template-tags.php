<?php
/**
 * Template-Hilfsfunktionen.
 *
 * @package sgnl-neptun
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Großer Kopfbereich nur auf der Startseite.
 *
 * @return bool
 */
function sgnl_is_front_hero() {
	return is_front_page() && ! is_paged();
}

/**
 * Überschrift für den Kopfbereich der Unterseiten.
 *
 * @return string
 */
function sgnl_page_heading() {
	if ( is_home() && ! is_front_page() ) {
		$id = (int) get_option( 'page_for_posts' );
		return $id ? get_the_title( $id ) : __( 'News', 'sgnl-neptun' );
	}
	if ( is_singular() ) {
		return single_post_title( '', false );
	}
	if ( is_search() ) {
		/* translators: %s: Suchbegriff */
		return sprintf( __( 'Suche: %s', 'sgnl-neptun' ), get_search_query() );
	}
	if ( is_404() ) {
		return __( 'Seite nicht gefunden', 'sgnl-neptun' );
	}
	if ( is_category() || is_tag() || is_tax() ) {
		return single_term_title( '', false );
	}
	if ( is_archive() ) {
		return wp_strip_all_tags( get_the_archive_title() );
	}
	return get_bloginfo( 'name' );
}

/**
 * Übergeordnete Seite als kleiner Rück-Link (nur bei Unterseiten).
 */
function sgnl_parent_link() {
	if ( is_page() ) {
		$parent = wp_get_post_parent_id( get_queried_object_id() );
		if ( $parent ) {
			printf(
				'<a class="page-hero__parent" href="%s">%s</a>',
				esc_url( get_permalink( $parent ) ),
				esc_html( get_the_title( $parent ) )
			);
		}
	} elseif ( is_singular( 'post' ) ) {
		$cats = get_the_category( get_queried_object_id() );
		if ( $cats ) {
			printf(
				'<a class="page-hero__parent" href="%s">%s</a>',
				esc_url( get_category_link( $cats[0] ) ),
				esc_html( $cats[0]->name )
			);
		}
	}
}

/**
 * Slideshow-Bilder für den Kopfbereich.
 *
 * @param bool $front Startseite?
 */
function sgnl_header_media( $front ) {
	$ids = sgnl_header_image_ids( $front );
	if ( ! $ids ) {
		return;
	}
	echo '<div class="hero-media" data-slideshow="' . ( count( $ids ) > 1 ? '1' : '0' ) . '" aria-hidden="true">';
	foreach ( $ids as $i => $id ) {
		echo wp_get_attachment_image( $id, 'full', false, array(
			'class'         => 'hero-media__img' . ( 0 === $i ? ' is-active' : '' ),
			'alt'           => '',
			'loading'       => 0 === $i ? 'eager' : 'lazy',
			'fetchpriority' => 0 === $i ? 'high' : 'low',
			'sizes'         => '100vw',
			'decoding'      => 'async',
		) );
	}
	echo '</div>';
}

/**
 * Wellen-Trenner unter dem Kopfbereich.
 */
function sgnl_wave() {
	echo '<svg class="hero-wave" viewBox="0 0 1440 90" preserveAspectRatio="none" aria-hidden="true" focusable="false"><path d="M0 60c120-26 240-38 360-30s240 40 360 40 240-36 360-44 240 10 360 26v38H0z"/></svg>';
}

/**
 * Datum + Kategorie eines Beitrags.
 */
function sgnl_post_meta() {
	$cats = get_the_category();
	echo '<div class="post-meta">';
	if ( $cats ) {
		printf( '<a class="pill" href="%s">%s</a>', esc_url( get_category_link( $cats[0] ) ), esc_html( $cats[0]->name ) );
	}
	printf(
		'<time datetime="%s">%s</time>',
		esc_attr( get_the_date( 'c' ) ),
		esc_html( get_the_date() )
	);
	echo '</div>';
}

/**
 * Footer-Copyright.
 *
 * @return string
 */
function sgnl_copyright() {
	$text = (string) sgnl_opt( 'footer_copyright' );
	$text = str_replace(
		array( '{year}', '{blogname}' ),
		array( date_i18n( 'Y' ), get_bloginfo( 'name' ) ),
		$text
	);
	return wp_kses_post( $text );
}

/**
 * Social-Links (Footer).
 */
function sgnl_social_links() {
	$out = '';
	for ( $i = 1; $i <= 3; $i++ ) {
		$url = trim( (string) sgnl_opt( 'social_' . $i ) );
		if ( ! $url ) {
			continue;
		}
		$icon  = sgnl_social_icon_name( $url );
		$label = 'mail' === $icon ? __( 'E-Mail schreiben', 'sgnl-neptun' ) : ( 'instagram' === $icon ? __( 'SG Neptun auf Instagram', 'sgnl-neptun' ) : $url );
		$ext   = 0 === strpos( $url, 'http' ) ? ' target="_blank" rel="noopener"' : '';
		$out  .= sprintf(
			'<a class="social-link" href="%s"%s aria-label="%s">%s</a>',
			esc_url( $url, array( 'http', 'https', 'mailto', 'tel' ) ),
			$ext,
			esc_attr( $label ),
			sgnl_icon( $icon, 22 )
		);
	}
	if ( $out ) {
		echo '<div class="social-links">' . $out . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput
	}
}

/**
 * Beitragskarte (Blog-Übersicht).
 */
function sgnl_post_card() {
	?>
	<article id="post-<?php the_ID(); ?>" <?php post_class( 'post-card' ); ?>>
		<a class="post-card__media" href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true">
			<?php
			if ( has_post_thumbnail() ) {
				the_post_thumbnail( 'medium_large', array( 'alt' => '', 'loading' => 'lazy' ) );
			} else {
				echo '<span class="post-card__placeholder"></span>';
			}
			?>
		</a>
		<div class="post-card__body">
			<?php sgnl_post_meta(); ?>
			<h2 class="post-card__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
			<p class="post-card__excerpt"><?php echo esc_html( wp_trim_words( get_the_excerpt(), 24, '…' ) ); ?></p>
			<a class="post-card__more" href="<?php the_permalink(); ?>"><?php esc_html_e( 'Weiterlesen', 'sgnl-neptun' ); ?><span class="screen-reader-text">: <?php the_title(); ?></span></a>
		</div>
	</article>
	<?php
}

/**
 * Blätter-Navigation.
 */
function sgnl_pagination() {
	the_posts_pagination( array(
		'mid_size'           => 1,
		'prev_text'          => __( 'Zurück', 'sgnl-neptun' ),
		'next_text'          => __( 'Weiter', 'sgnl-neptun' ),
		'before_page_number' => '<span class="screen-reader-text">' . __( 'Seite', 'sgnl-neptun' ) . ' </span>',
	) );
}
