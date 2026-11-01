<?php
/**
 * SG Neptun Lampertheim – Theme-Funktionen
 *
 * Eigenständiges Theme (kein Parent, kein Page-Builder).
 * Vereinsfunktionen (Cookie-Banner, Hinweise, Weiterleitungen, iFrame-Block,
 * Umstellung) liegen im Plugin "SG Neptun Funktionen" (sgnl-core).
 *
 * @package sgnl-neptun
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'SGNL_THEME_VERSION', '2.0.3' );
define( 'SGNL_THEME_DIR', get_stylesheet_directory() );
define( 'SGNL_THEME_URI', get_stylesheet_directory_uri() );

/*
 * Selbstreparatur: Version 1 war ein Child-Theme von Mesmerize. Wird diese Version
 * über die alte hochgeladen, steht in der Datenbank noch "mesmerize" als Parent.
 * Dann wird das hier korrigiert, damit Mesmerize nicht mehr mitgeladen wird.
 */
if ( get_option( 'template' ) !== get_option( 'stylesheet' ) && get_option( 'stylesheet' ) === basename( __DIR__ ) ) {
	update_option( 'template', get_option( 'stylesheet' ) );
	if ( ! defined( 'WP_CLI' ) && ! wp_doing_ajax() && ! wp_doing_cron() && isset( $_SERVER['REQUEST_URI'] ) && 'GET' === ( $_SERVER['REQUEST_METHOD'] ?? '' ) ) {
		// Seite einmal neu laden, damit der Parent nicht mehr geladen wird.
		header( 'Location: ' . ( is_ssl() ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'], true, 302 );
		exit;
	}
}

require SGNL_THEME_DIR . '/inc/class-github-updater.php';
require SGNL_THEME_DIR . '/inc/icons.php';

// Updates direkt aus GitHub (Dashboard → Aktualisierungen).
new SGNL_Theme_GitHub_Updater( array(
	'type'  => 'theme',
	'repo'  => 'nicigrv/sgnl-wp-theme',
	'slug'  => basename( SGNL_THEME_DIR ),
	'asset' => 'sgnl-neptun.zip',
	'name'  => 'SG Neptun Lampertheim',
) );
require SGNL_THEME_DIR . '/inc/customizer.php';
require SGNL_THEME_DIR . '/inc/template-tags.php';

/* -------------------------------------------------------------------------
 * Grundeinrichtung
 * ---------------------------------------------------------------------- */
add_action( 'after_setup_theme', function () {
	load_theme_textdomain( 'sgnl-neptun', SGNL_THEME_DIR . '/languages' );

	add_theme_support( 'title-tag' );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'align-wide' );
	add_theme_support( 'editor-styles' );
	add_theme_support( 'html5', array( 'search-form', 'gallery', 'caption', 'style', 'script', 'navigation-widgets' ) );
	add_theme_support( 'custom-logo', array(
		'height'      => 120,
		'width'       => 440,
		'flex-height' => true,
		'flex-width'  => true,
	) );
	add_theme_support( 'customize-selective-refresh-widgets' );

	add_editor_style( array( 'assets/css/theme.css', 'assets/css/editor.css' ) );

	// Gleicher Menü-Ort wie im alten Theme (Mesmerize) -> Menüzuordnung bleibt erhalten.
	register_nav_menus( array(
		'primary' => __( 'Hauptmenü', 'sgnl-neptun' ),
	) );
} );

/* Widget-Bereiche: gleiche IDs wie im alten Theme -> Footer-Widgets bleiben erhalten */
add_action( 'widgets_init', function () {
	$common = array(
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget'  => '</div>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	);
	register_sidebar( array_merge( $common, array(
		'id'   => 'first_box_widgets',
		'name' => __( 'Footer – Spalte 1', 'sgnl-neptun' ),
	) ) );
	register_sidebar( array_merge( $common, array(
		'id'   => 'second_box_widgets',
		'name' => __( 'Footer – Spalte 2', 'sgnl-neptun' ),
	) ) );
	register_sidebar( array_merge( $common, array(
		'id'   => 'third_box_widgets',
		'name' => __( 'Footer – Spalte 3', 'sgnl-neptun' ),
	) ) );
	register_sidebar( array_merge( $common, array(
		'id'          => 'sidebar-1',
		'name'        => __( 'Blog-Seitenleiste', 'sgnl-neptun' ),
		'description' => __( 'Wird neben Beiträgen angezeigt, sobald hier ein Widget liegt.', 'sgnl-neptun' ),
	) ) );
} );

/* -------------------------------------------------------------------------
 * Styles & Skripte
 * ---------------------------------------------------------------------- */
function sgnl_asset_ver( $rel ) {
	$f = SGNL_THEME_DIR . '/' . $rel;
	return file_exists( $f ) ? (string) filemtime( $f ) : SGNL_THEME_VERSION;
}

add_action( 'wp_enqueue_scripts', function () {
	wp_enqueue_style( 'sgnl-theme', SGNL_THEME_URI . '/assets/css/theme.css', array(), sgnl_asset_ver( 'assets/css/theme.css' ) );
	wp_enqueue_script( 'sgnl-theme', SGNL_THEME_URI . '/assets/js/theme.js', array(), sgnl_asset_ver( 'assets/js/theme.js' ), array(
		'in_footer' => true,
		'strategy'  => 'defer',
	) );
	wp_localize_script( 'sgnl-theme', 'sgnlTheme', array(
		'menuOpen'  => __( 'Menü öffnen', 'sgnl-neptun' ),
		'menuClose' => __( 'Menü schließen', 'sgnl-neptun' ),
		'subOpen'   => __( 'Untermenü öffnen', 'sgnl-neptun' ),
	) );
} );

/* Schriften vorladen (liegen im Theme, kein Google-Request) */
add_action( 'wp_head', function () {
	$base = esc_url( SGNL_THEME_URI . '/assets/fonts/' );
	echo '<link rel="preload" href="' . $base . 'lexend-latin-wght-normal.woff2" as="font" type="font/woff2" crossorigin>' . "\n";
	echo '<meta name="theme-color" content="#0B2A5B">' . "\n";
}, 1 );

/* Emoji-Skripte von WordPress weglassen (Emojis funktionieren trotzdem nativ) */
add_action( 'init', function () {
	remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
	remove_action( 'wp_print_styles', 'print_emoji_styles' );
	remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
	remove_action( 'admin_print_styles', 'print_emoji_styles' );
} );

/* -------------------------------------------------------------------------
 * Block-Stile (im Editor unter "Stile" auswählbar)
 * ---------------------------------------------------------------------- */
add_action( 'init', function () {
	register_block_style( 'core/heading', array(
		'name'  => 'sgnl-lane',
		'label' => __( 'Mit Bahnleine', 'sgnl-neptun' ),
	) );
	register_block_style( 'core/group', array(
		'name'  => 'sgnl-card',
		'label' => __( 'Karte', 'sgnl-neptun' ),
	) );
	register_block_style( 'core/group', array(
		'name'  => 'sgnl-box',
		'label' => __( 'Info-Box (Download/Kontakt)', 'sgnl-neptun' ),
	) );
	register_block_style( 'core/image', array(
		'name'  => 'sgnl-photo',
		'label' => __( 'Foto mit Schatten', 'sgnl-neptun' ),
	) );
	register_block_style( 'core/image', array(
		'name'  => 'sgnl-portrait',
		'label' => __( 'Porträt (rund)', 'sgnl-neptun' ),
	) );
	register_block_style( 'core/table', array(
		'name'  => 'sgnl-table',
		'label' => __( 'Vereinstabelle', 'sgnl-neptun' ),
	) );
	foreach ( array(
		'sgnl-mail'     => __( 'Mit Mail-Symbol', 'sgnl-neptun' ),
		'sgnl-download' => __( 'Mit Download-Symbol', 'sgnl-neptun' ),
		'sgnl-link'     => __( 'Mit Link-Symbol', 'sgnl-neptun' ),
		'sgnl-light'    => __( 'Hell', 'sgnl-neptun' ),
	) as $name => $label ) {
		register_block_style( 'core/button', array( 'name' => $name, 'label' => $label ) );
	}
	register_block_style( 'core/paragraph', array(
		'name'  => 'sgnl-stat',
		'label' => __( 'Kennzahl (zählt hoch)', 'sgnl-neptun' ),
	) );
} );

/* Eigene Muster-Kategorie */
add_action( 'init', function () {
	register_block_pattern_category( 'sgnl', array( 'label' => __( 'SG Neptun', 'sgnl-neptun' ) ) );
} );

/* -------------------------------------------------------------------------
 * Kleinigkeiten
 * ---------------------------------------------------------------------- */
add_filter( 'excerpt_length', function () {
	return 24;
}, 20 );

add_filter( 'excerpt_more', function () {
	return '…';
} );

/* Body-Klassen für Layout */
add_filter( 'body_class', function ( $classes ) {
	$classes[] = sgnl_is_front_hero() ? 'has-front-hero' : 'has-page-hero';
	if ( is_singular() && has_blocks() ) {
		$classes[] = 'has-blocks';
	}
	return $classes;
} );

/* Leere Absätze aus alten Inhalten (z. B. "<p></p>") nicht als Abstand rendern */
add_filter( 'render_block_core/paragraph', function ( $html ) {
	if ( preg_match( '#^\s*<p[^>]*>\s*(&nbsp;|\s)*</p>\s*$#u', $html ) ) {
		return '';
	}
	return $html;
} );

/* Externe Links in Navigation sicher öffnen */
add_filter( 'nav_menu_link_attributes', function ( $atts ) {
	if ( ! empty( $atts['target'] ) && '_blank' === $atts['target'] ) {
		$atts['rel'] = 'noopener';
	}
	return $atts;
} );
