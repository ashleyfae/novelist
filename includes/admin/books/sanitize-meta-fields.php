<?php
/**
 * Sanitize Meta Fields
 *
 * Functions for sanitizing the meta field inputs on save.
 *
 * @package   novelist
 * @copyright Copyright (c) 2016, Nose Graze Ltd.
 * @license   GPL2+
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/*
 * Sanitize text fields
 */
add_filter( 'novelist/book/meta-box/sanitize/novelist_title', 'sanitize_text_field' );
add_filter( 'novelist/book/meta-box/sanitize/novelist_series', 'sanitize_text_field' );
add_filter( 'novelist/book/meta-box/sanitize/novelist_publisher', 'sanitize_text_field' );
add_filter( 'novelist/book/meta-box/sanitize/novelist_pub_date', 'sanitize_text_field' );
add_filter( 'novelist/book/meta-box/sanitize/novelist_contributors', 'sanitize_text_field' );
add_filter( 'novelist/book/meta-box/sanitize/novelist_pages', 'sanitize_text_field' );
add_filter( 'novelist/book/meta-box/sanitize/novelist_isbn', 'sanitize_text_field' );
add_filter( 'novelist/book/meta-box/sanitize/novelist_asin', 'sanitize_text_field' );
add_filter( 'novelist/book/meta-box/sanitize/novelist_goodreads', 'sanitize_text_field' );

/*
 * Sanitize Numbers
 */
add_filter( 'novelist/book/meta-box/sanitize/novelist_pages', 'intval' );
add_filter( 'novelist/book/meta-box/sanitize/novelist_cover', 'intval' );

/*
 * Sanitize URLs
 */
add_filter( 'novelist/book/meta-box/sanitize/novelist_goodreads', 'esc_url_raw' );

/*
 * Sanitize large textareas
 */
function novelist_wp_kses_post( $input ) {
	$allowed_html = wp_kses_allowed_html( 'post' );

	// iframe
	$allowed_html['iframe'] = array(
		'src'             => array(),
		'height'          => array(),
		'width'           => array(),
		'frameborder'     => array(),
		'allowfullscreen' => array(),
	);

	// form fields - input
	$allowed_html['input'] = array(
		'class' => array(),
		'id'    => array(),
		'name'  => array(),
		'value' => array(),
		'type'  => array(),
	);

	// select
	$allowed_html['select'] = array(
		'class' => array(),
		'id'    => array(),
		'name'  => array(),
		'value' => array(),
		'type'  => array(),
	);

	// select options
	$allowed_html['option'] = array(
		'selected' => array(),
	);

	// style
	$allowed_html['style'] = array(
		'types' => array(),
	);

	return wp_kses( $input, apply_filters( 'novelist/wp-kses-allowed-html', $allowed_html ) );
}

add_filter( 'novelist/book/meta-box/sanitize/novelist_synopsis', 'novelist_wp_kses_post' );
add_filter( 'novelist/book/meta-box/sanitize/novelist_excerpt', 'novelist_wp_kses_post' );
add_filter( 'novelist/book/meta-box/sanitize/novelist_extra', 'novelist_wp_kses_post' );

/**
 * Sanitize Purchase Links
 *
 * @param array $links_array
 *
 * @since 1.0.0
 * @return array|string
 */
function novelist_sanitize_purchase_links( $links_array ) {
	return is_array( $links_array ) ? array_map( 'sanitize_text_field', $links_array ) : sanitize_text_field( $links_array );
}

add_filter( 'novelist/book/meta-box/sanitize/novelist_purchase_links', 'novelist_sanitize_purchase_links' );

/**
 * Sanitize Checkbox
 *
 * @param $value
 *
 * @since 1.0.0
 * @return bool
 */
function novelist_sanitize_checkbox( $value ) {
	return ( $value ) ? true : false;
}

add_filter( 'novelist/book/meta-box/sanitize/novelist_hide', 'novelist_sanitize_checkbox' );