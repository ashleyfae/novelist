<?php
/**
 * Dashboard Columns
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
 * Book Columns
 *
 * Defines the custom columns for the "All Books" page.
 *
 * @param array $book_columns
 *
 * @since 1.0.0
 * @return array Updated array of columns
 */
function novelist_book_columns( $book_columns ) {
	$series_labels = novelist_get_taxonomy_labels( 'novelist-series' );
	$genre_labels  = novelist_get_taxonomy_labels( 'novelist-genre' );

	$book_columns = array(
		'cb'                       => '<input type="checkbox"/>',
		'title'                    => __( 'Title', 'novelist' ),
		'taxonomy-novelist-series' => $series_labels['name'],
		'taxonomy-novelist-genre'  => $genre_labels['name'],
		'cover'                    => __( 'Book Cover', 'novelist' )
	);

	return apply_filters( 'novelist/cpt/book-columns', $book_columns );
}

add_filter( 'manage_edit-book_columns', 'novelist_book_columns' );

/**
 * Adds a "Book ID" label on hover actions.
 *
 * @param array   $actions
 * @param WP_Post $post
 *
 * @since 1.1.11
 * @return mixed
 */
function novelist_book_list_table_actions( $actions, $post ) {
	if ( 'book' !== $post->post_type ) {
		return $actions;
	}

	$actions['novelist-book-id'] = sprintf( esc_html__( 'Book ID: %d', 'novelist' ), esc_html( $post->ID ) );

	return $actions;
}
add_filter( 'page_row_actions', 'novelist_book_list_table_actions', 10, 2 );

/**
 * Render Book Columns
 *
 * @param string $column_name Name of the current column
 * @param int    $post_id     ID of the post
 *
 * @since 1.0.0
 * @return void
 */
function novelist_render_book_columns( $column_name, $post_id ) {

	if ( get_post_type( $post_id ) != 'book' ) {
		return;
	}

	$book = new Novelist_Book( $post_id );

	switch ( $column_name ) {

		case 'cover' :
			$cover_ID = $book->cover_ID;
			if ( ! empty( $cover_ID ) ) {
				echo wp_get_attachment_image( intval( $cover_ID ), 'thumbnail' );
			}
			break;

	}

}

add_action( 'manage_book_posts_custom_column', 'novelist_render_book_columns', 10, 2 );
