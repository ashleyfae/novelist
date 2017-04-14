<?php
/**
 * Filters applied to the book values.
 *
 * @package   novelist
 * @copyright Copyright (c) 2016, Nose Graze Ltd.
 * @license   GPL2+
 */

/**
 * Title: Add Schema
 *
 * Adds schema.org markup around the book title.
 *
 * @param string        $value          Final formatted value
 * @param string        $key            The key that is being filtered
 * @param array         $all_fields     All available book fields
 * @param array         $enabled_fields Only the fields that are enabled
 * @param Novelist_Book $book           Book object
 *
 * @since 1.0.0
 * @return string
 */
function novelist_add_schema_title( $value, $key, $all_fields, $enabled_fields, $book ) {
	if ( empty( $value ) ) {
		return $value;
	}

	return '<span itemprop="name">' . $value . '</span>';
}

add_filter( 'novelist/book/pre-render/title', 'novelist_add_schema_title', 10, 5 );

/**
 * Pages: Add Schema
 *
 * Adds schema.org markup around the number of pages.
 *
 * @param string        $value          Final formatted value
 * @param string        $key            The key that is being filtered
 * @param array         $all_fields     All available book fields
 * @param array         $enabled_fields Only the fields that are enabled
 * @param Novelist_Book $book           Book object
 *
 * @since 1.0.0
 * @return string
 */
function novelist_add_schema_pages( $value, $key, $all_fields, $enabled_fields, $book ) {
	if ( empty( $value ) ) {
		return $value;
	}

	return '<span itemprop="numberOfPages">' . $value . '</span>';
}

add_filter( 'novelist/book/pre-render/pages', 'novelist_add_schema_pages', 10, 5 );

/**
 * Add Embed
 *
 * Adds embed functionality to the field.
 *
 * @param string        $value          Final formatted value
 * @param string        $key            The key that is being filtered
 * @param array         $all_fields     All available book fields
 * @param array         $enabled_fields Only the fields that are enabled
 * @param Novelist_Book $book           Book object
 *
 * @since 1.0.0
 * @return string
 */
function novelist_add_embed_to_field( $value, $key = '', $all_fields = array(), $enabled_fields = array(), $book = false ) {
	global $wp_embed;

	return $wp_embed->autoembed( $value );
}

add_filter( 'novelist/book/render/extra_text', 'novelist_add_embed_to_field', 10, 5 );

/**
 * Auto add paragraph tags
 *
 * @param string        $value          Final formatted value
 * @param string        $key            The key that is being filtered
 * @param array         $all_fields     All available book fields
 * @param array         $enabled_fields Only the fields that are enabled
 * @param Novelist_Book $book           Book object
 *
 * @since 1.0.0
 * @return string
 */
function novelist_wpautop_field( $value, $key, $all_fields, $enabled_fields, $book ) {
	return wpautop( $value );
}

add_filter( 'novelist/book/render/extra_text', 'novelist_wpautop_field', 10, 5 );
add_filter( 'novelist/book/render/synopsis', 'novelist_wpautop_field', 10, 5 );
add_filter( 'novelist/book/render/excerpt', 'novelist_wpautop_field', 10, 5 );