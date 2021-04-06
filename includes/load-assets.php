<?php
/**
 * Loads all assets on the front-end.
 *
 * @package   novelist
 * @copyright Copyright (c) 2016, Nose Graze Ltd.
 * @license   GPL2+
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register front-end stylesheet.
 *
 * @since 1.0.0
 * @return void
 */
function novelist_register_styles() {
	if ( novelist_get_option( 'disable_styles', false ) ) {
		return;
	}

	$file          = 'novelist-front-end.css';
	$templates_dir = novelist_get_theme_template_dir_name();

	$child_theme_style_sheet     = trailingslashit( get_stylesheet_directory() ) . $templates_dir . $file;
	$child_theme_style_sheet_2   = trailingslashit( get_stylesheet_directory() ) . $templates_dir . 'novelist-front-end.css';
	$parent_theme_style_sheet    = trailingslashit( get_template_directory() ) . $templates_dir . $file;
	$parent_theme_style_sheet_2  = trailingslashit( get_template_directory() ) . $templates_dir . 'novelist-front-end.css';
	$novelist_plugin_style_sheet = trailingslashit( novelist_get_templates_dir() ) . $file;

	// Look in the child theme directory first, followed by the parent theme, followed by the Novelist core templates directory
	// Also look for the min version first, followed by non minified version, even if SCRIPT_DEBUG is not enabled.
	// This allows users to copy just novelist-front-end.css to their theme
	if ( file_exists( $child_theme_style_sheet ) || ( ! empty( $suffix ) && ( $nonmin = file_exists( $child_theme_style_sheet_2 ) ) ) ) {
		if ( ! empty( $nonmin ) ) {
			$url = trailingslashit( get_stylesheet_directory_uri() ) . $templates_dir . 'novelist-front-end.css';
		} else {
			$url = trailingslashit( get_stylesheet_directory_uri() ) . $templates_dir . $file;
		}
	} elseif ( file_exists( $parent_theme_style_sheet ) || ( ! empty( $suffix ) && ( $nonmin = file_exists( $parent_theme_style_sheet_2 ) ) ) ) {
		if ( ! empty( $nonmin ) ) {
			$url = trailingslashit( get_template_directory_uri() ) . $templates_dir . 'novelist-front-end.css';
		} else {
			$url = trailingslashit( get_template_directory_uri() ) . $templates_dir . $file;
		}
	} elseif ( file_exists( $novelist_plugin_style_sheet ) || file_exists( $novelist_plugin_style_sheet ) ) {
		$url = trailingslashit( novelist_get_templates_url() ) . $file;
	}

	if ( empty( $url ) ) {
		return;
	}

	wp_register_style( 'novelist', $url, array(), NOVELIST_VERSION, 'all' );
	wp_enqueue_style( 'novelist' );

	// Add inline CSS
	wp_add_inline_style( 'novelist', novelist_generate_css() );
}

add_action( 'wp_enqueue_scripts', 'novelist_register_styles' );

/**
 * Generate CSS
 *
 * Generates some custom CSS based on the Novelist style settings.
 *
 * @since 1.0.0
 * @return string
 */
function novelist_generate_css() {
	$css = '';

	// Button BG
	$button_bg = novelist_get_option( 'button_bg', '#333333' );
	if ( $button_bg ) {
		$css .= '.novelist-button {
			background: ' . esc_attr( $button_bg ) . ';
		}
		
		.novelist-button:hover {
			background: ' . esc_attr( novelist_adjust_brightness( $button_bg, - 50 ) ) . ';
		}';
	}

	// Button Text Colour
	$button_text = novelist_get_option( 'button_text', '#ffffff' );
	if ( $button_text ) {
		$css .= '.novelist-button, .novelist-button:hover {
			color: ' . esc_attr( $button_text ) . ';
		}';
	}

	return apply_filters( 'novelist/inline-css', $css );
}

/**
 * Adjust Hex Brightness
 *
 * Enter a step number between -255 and 255. Negative means darker, positive
 * means lighter. This will return a new hex value that has been darkened
 * or brightened based on that value.
 *
 * @param string $hex
 * @param int    $steps
 *
 * @since 1.0.0
 * @return string
 */
function novelist_adjust_brightness( $hex, $steps ) {
	// Steps should be between -255 and 255. Negative = darker, positive = lighter
	$steps = max( - 255, min( 255, $steps ) );

	// Normalize into a six character long hex string
	$hex = str_replace( '#', '', $hex );
	if ( strlen( $hex ) == 3 ) {
		$hex = str_repeat( substr( $hex, 0, 1 ), 2 ) . str_repeat( substr( $hex, 1, 1 ), 2 ) . str_repeat( substr( $hex, 2, 1 ), 2 );
	}

	// Split into three parts: R, G and B
	$color_parts = str_split( $hex, 2 );
	$return      = '#';

	foreach ( $color_parts as $color ) {
		$color = hexdec( $color ); // Convert to decimal
		$color = max( 0, min( 255, $color + $steps ) ); // Adjust color
		$return .= str_pad( dechex( $color ), 2, '0', STR_PAD_LEFT ); // Make two char hex code
	}

	return $return;
}
