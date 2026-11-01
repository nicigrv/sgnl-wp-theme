<?php
/**
 * Customizer-Einstellungen (Design → Anpassen → "SG Neptun").
 * Standardwerte werden aus den alten Mesmerize-Einstellungen übernommen,
 * damit nach dem Theme-Wechsel alles wie vorher aussieht.
 *
 * @package sgnl-neptun
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Standardwerte – teilweise aus den alten Theme-Einstellungen abgeleitet.
 *
 * @return array
 */
function sgnl_defaults() {
	static $defaults = null;
	if ( null !== $defaults ) {
		return $defaults;
	}

	$legacy_buttons = get_theme_mod( 'header_content_buttons', array() );
	$btn            = is_array( $legacy_buttons ) && ! empty( $legacy_buttons[0] ) ? $legacy_buttons[0] : array();

	$defaults = array(
		'hero_title'        => get_theme_mod( 'header_title', get_bloginfo( 'name' ) ),
		'hero_text'         => '',
		'hero_btn1_label'   => isset( $btn['label'] ) ? $btn['label'] : '',
		'hero_btn1_url'     => isset( $btn['url'] ) ? $btn['url'] : '',
		'hero_btn2_label'   => '',
		'hero_btn2_url'     => '',
		'header_cta_label'  => '',
		'header_cta_url'    => '',
		'overlay_opacity'   => 60,
		'footer_copyright'  => '&copy; {year} {blogname}.',
		'footer_brand'      => get_bloginfo( 'name' ),
	);

	// Slideshow-Bilder (Startseite: 6 Plätze, Unterseiten: 3 Plätze).
	$front = sgnl_legacy_slideshow_ids( 'header_slideshow' );
	$inner = sgnl_legacy_slideshow_ids( 'inner_header_slideshow' );
	for ( $i = 1; $i <= 6; $i++ ) {
		$defaults[ 'hero_image_' . $i ] = isset( $front[ $i - 1 ] ) ? $front[ $i - 1 ] : '';
	}
	for ( $i = 1; $i <= 3; $i++ ) {
		$defaults[ 'inner_image_' . $i ] = isset( $inner[ $i - 1 ] ) ? $inner[ $i - 1 ] : '';
	}

	// Social-Links aus dem alten Footer.
	$n = 1;
	for ( $i = 0; $i <= 4 && $n <= 3; $i++ ) {
		$enabled = get_theme_mod( "footer_content_social_icon_{$i}_enabled", false );
		$link    = get_theme_mod( "footer_content_social_icon_{$i}_link", '' );
		if ( $enabled && $link && '#' !== $link ) {
			$defaults[ 'social_' . $n ] = $link;
			$n++;
		}
	}
	for ( ; $n <= 3; $n++ ) {
		$defaults[ 'social_' . $n ] = '';
	}
	// Bekannte Werte als Rückfall, falls die alten Einstellungen fehlen.
	if ( '' === $defaults['social_1'] && '' === $defaults['social_2'] ) {
		$defaults['social_1'] = 'https://www.instagram.com/sgneptun_lampertheim';
		$defaults['social_2'] = 'mailto:info@sgnl.de';
	}

	return $defaults;
}

/**
 * IDs aus der alten Mesmerize-Slideshow-Einstellung.
 *
 * @param string $mod Name der Einstellung.
 * @return array
 */
function sgnl_legacy_slideshow_ids( $mod ) {
	$raw = get_theme_mod( $mod, array() );
	$ids = array();
	if ( is_array( $raw ) ) {
		foreach ( $raw as $item ) {
			$val = is_array( $item ) && isset( $item['url'] ) ? $item['url'] : $item;
			if ( is_numeric( $val ) ) {
				$ids[] = (int) $val;
			} elseif ( is_string( $val ) && $val ) {
				$id = attachment_url_to_postid( $val );
				if ( $id ) {
					$ids[] = $id;
				}
			}
		}
	}
	return $ids;
}

/**
 * Einstellung lesen.
 *
 * @param string $key Schlüssel ohne Präfix.
 * @return mixed
 */
function sgnl_opt( $key ) {
	$d = sgnl_defaults();
	return get_theme_mod( 'sgnl_' . $key, isset( $d[ $key ] ) ? $d[ $key ] : '' );
}

/**
 * Bild-IDs für den Header.
 *
 * @param bool $front Startseite?
 * @return int[]
 */
function sgnl_header_image_ids( $front ) {
	$ids = array();
	$max = $front ? 6 : 3;
	for ( $i = 1; $i <= $max; $i++ ) {
		$id = (int) sgnl_opt( ( $front ? 'hero_image_' : 'inner_image_' ) . $i );
		if ( $id && wp_attachment_is_image( $id ) ) {
			$ids[] = $id;
		}
	}
	if ( ! $ids && ! $front ) {
		return sgnl_header_image_ids( true );
	}
	return $ids;
}

add_action( 'customize_register', function ( WP_Customize_Manager $wpc ) {
	$d = sgnl_defaults();

	$wpc->add_panel( 'sgnl', array(
		'title'    => __( 'SG Neptun', 'sgnl-neptun' ),
		'priority' => 25,
	) );

	/* --- Startseite --- */
	$wpc->add_section( 'sgnl_hero', array(
		'title' => __( 'Startseite: Kopfbereich', 'sgnl-neptun' ),
		'panel' => 'sgnl',
	) );
	$text_fields = array(
		'hero_title'      => __( 'Überschrift', 'sgnl-neptun' ),
		'hero_text'       => __( 'Text unter der Überschrift (optional)', 'sgnl-neptun' ),
		'hero_btn1_label' => __( 'Button 1: Text', 'sgnl-neptun' ),
		'hero_btn1_url'   => __( 'Button 1: Link', 'sgnl-neptun' ),
		'hero_btn2_label' => __( 'Button 2: Text (optional)', 'sgnl-neptun' ),
		'hero_btn2_url'   => __( 'Button 2: Link', 'sgnl-neptun' ),
	);
	foreach ( $text_fields as $key => $label ) {
		$wpc->add_setting( 'sgnl_' . $key, array(
			'default'           => $d[ $key ],
			'sanitize_callback' => false !== strpos( $key, 'url' ) ? 'esc_url_raw' : 'sanitize_text_field',
		) );
		$wpc->add_control( 'sgnl_' . $key, array(
			'label'   => $label,
			'section' => 'sgnl_hero',
			'type'    => 'hero_text' === $key ? 'textarea' : ( false !== strpos( $key, 'url' ) ? 'url' : 'text' ),
		) );
	}
	for ( $i = 1; $i <= 6; $i++ ) {
		$wpc->add_setting( 'sgnl_hero_image_' . $i, array(
			'default'           => $d[ 'hero_image_' . $i ],
			'sanitize_callback' => 'absint',
		) );
		$wpc->add_control( new WP_Customize_Media_Control( $wpc, 'sgnl_hero_image_' . $i, array(
			/* translators: %d: Nummer */
			'label'     => sprintf( __( 'Slideshow-Bild %d', 'sgnl-neptun' ), $i ),
			'section'   => 'sgnl_hero',
			'mime_type' => 'image',
		) ) );
	}

	/* --- Unterseiten --- */
	$wpc->add_section( 'sgnl_inner', array(
		'title' => __( 'Unterseiten: Kopfbild', 'sgnl-neptun' ),
		'panel' => 'sgnl',
	) );
	for ( $i = 1; $i <= 3; $i++ ) {
		$wpc->add_setting( 'sgnl_inner_image_' . $i, array(
			'default'           => $d[ 'inner_image_' . $i ],
			'sanitize_callback' => 'absint',
		) );
		$wpc->add_control( new WP_Customize_Media_Control( $wpc, 'sgnl_inner_image_' . $i, array(
			/* translators: %d: Nummer */
			'label'     => sprintf( __( 'Bild %d', 'sgnl-neptun' ), $i ),
			'section'   => 'sgnl_inner',
			'mime_type' => 'image',
		) ) );
	}
	$wpc->add_setting( 'sgnl_overlay_opacity', array(
		'default'           => $d['overlay_opacity'],
		'sanitize_callback' => 'absint',
	) );
	$wpc->add_control( 'sgnl_overlay_opacity', array(
		'label'       => __( 'Abdunklung der Kopfbilder (%)', 'sgnl-neptun' ),
		'section'     => 'sgnl_inner',
		'type'        => 'range',
		'input_attrs' => array( 'min' => 20, 'max' => 90, 'step' => 5 ),
	) );

	/* --- Kopfzeile & Footer --- */
	$wpc->add_section( 'sgnl_footer', array(
		'title' => __( 'Kopfzeile & Footer', 'sgnl-neptun' ),
		'panel' => 'sgnl',
	) );
	$footer_fields = array(
		'header_cta_label' => array( __( 'Button oben rechts: Text (leer = ausblenden)', 'sgnl-neptun' ), 'text' ),
		'header_cta_url'   => array( __( 'Button oben rechts: Link', 'sgnl-neptun' ), 'url' ),
		'footer_brand'     => array( __( 'Footer: Vereinsname', 'sgnl-neptun' ), 'text' ),
		'footer_copyright' => array( __( 'Footer: Copyright ({year} und {blogname} werden ersetzt)', 'sgnl-neptun' ), 'text' ),
		'social_1'         => array( __( 'Social-Link 1 (z. B. Instagram)', 'sgnl-neptun' ), 'text' ),
		'social_2'         => array( __( 'Social-Link 2 (z. B. mailto:info@sgnl.de)', 'sgnl-neptun' ), 'text' ),
		'social_3'         => array( __( 'Social-Link 3', 'sgnl-neptun' ), 'text' ),
	);
	foreach ( $footer_fields as $key => $conf ) {
		$wpc->add_setting( 'sgnl_' . $key, array(
			'default'           => $d[ $key ],
			'sanitize_callback' => 'url' === $conf[1] ? 'esc_url_raw' : ( 0 === strpos( $key, 'social_' ) ? 'sgnl_sanitize_link' : 'wp_kses_post' ),
		) );
		$wpc->add_control( 'sgnl_' . $key, array(
			'label'   => $conf[0],
			'section' => 'sgnl_footer',
			'type'    => 'url' === $conf[1] ? 'url' : 'text',
		) );
	}
} );

/**
 * Links inkl. mailto:/tel: erlauben.
 *
 * @param string $v Wert.
 * @return string
 */
function sgnl_sanitize_link( $v ) {
	return esc_url_raw( trim( $v ), array( 'http', 'https', 'mailto', 'tel' ) );
}
