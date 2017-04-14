<?php
/**
 * Functions for registering post types and taxonomies.
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
 * Registers the Book post type.
 *
 * @since 1.0.0
 * @return void
 */
function novelist_setup_post_types() {

	$archives = defined( 'NOVELIST_DISABLE_ARCHIVE' ) && NOVELIST_DISABLE_ARCHIVE ? false : true;
	$slug     = defined( 'NOVELIST_SLUG' ) ? NOVELIST_SLUG : 'books';
	$rewrite  = defined( 'NOVELIST_DISABLE_REWRITE' ) && NOVELIST_DISABLE_REWRITE ? false : array(
		'slug'       => $slug,
		'with_front' => false
	);

	// Set up the labels.
	$book_labels = apply_filters( 'novelist/cpt/book-labels', array(
		'name'                  => _x( '%2$s', 'book post type name', 'novelist' ),
		'singular_name'         => _x( '%1$s', 'singular book post type name', 'novelist' ),
		'add_new'               => __( 'Add New', 'novelist' ),
		'add_new_item'          => __( 'Add New %1$s', 'novelist' ),
		'edit_item'             => __( 'Edit %1$s', 'novelist' ),
		'new_item'              => __( 'New %1$s', 'novelist' ),
		'all_items'             => __( 'All %2$s', 'novelist' ),
		'view_item'             => __( 'View %1$s', 'novelist' ),
		'search_items'          => __( 'Search %2$s', 'novelist' ),
		'not_found'             => __( 'No %2$s found', 'novelist' ),
		'not_found_in_trash'    => __( 'No %2$s found in Trash', 'novelist' ),
		'parent_item_colon'     => '',
		'menu_name'             => _x( '%2$s', 'book post type menu name', 'novelist' ),
		'featured_image'        => __( '%1$s Cover Image', 'novelist' ),
		'set_featured_image'    => __( 'Set %1$s Cover Image', 'novelist' ),
		'remove_featured_image' => __( 'Remove %1$s Cover Image', 'novelist' ),
		'use_featured_image'    => __( 'Use as %1$s Cover Image', 'novelist' ),
	) );

	foreach ( $book_labels as $key => $value ) {
		$book_labels[ $key ] = sprintf( $value, novelist_get_label_singular(), novelist_get_label_plural() );
	}

	$book_args = array(
		'labels'             => $book_labels,
		'public'             => true,
		'publicly_queryable' => true,
		'show_ui'            => true,
		'show_in_menu'       => true,
		'query_var'          => true,
		'rewrite'            => $rewrite,
		'capability_type'    => 'book',
		'map_meta_cap'       => true,
		'menu_icon'          => 'dashicons-book',
		'has_archive'        => $archives,
		'hierarchical'       => true,
		'supports'           => apply_filters( 'novelist/cpt/book-supports', array( 'title', 'page-attributes' ) ),
	);

	register_post_type( 'book', apply_filters( 'novelist/cpt/book-args', $book_args ) );

}

add_action( 'init', 'novelist_setup_post_types', 1 );

/**
 * Get Default Labels
 *
 * @since 1.0.0
 * @return array $defaults Default labels
 */
function novelist_get_default_labels() {
	$defaults = array(
		'singular' => __( 'Book', 'novelist' ),
		'plural'   => __( 'Books', 'novelist' )
	);

	return apply_filters( 'novelist/cpt/book-default-labels', $defaults );
}

/**
 * Get Singular Label
 *
 * @param bool $lowercase Whether or not the result should be in lowercase.
 *
 * @since 1.0.0
 * @return string
 */
function novelist_get_label_singular( $lowercase = false ) {
	$defaults = novelist_get_default_labels();

	return ( $lowercase ) ? strtolower( $defaults['singular'] ) : $defaults['singular'];
}

/**
 * Get Plural Label
 *
 * @since 1.0.0
 * @return string
 */
function novelist_get_label_plural( $lowercase = false ) {
	$defaults = novelist_get_default_labels();

	return ( $lowercase ) ? strtolower( $defaults['plural'] ) : $defaults['plural'];
}

/**
 * Change default "Enter title here" input
 *
 * @param string $title Default title placeholder text
 *
 * @since 1.0.0
 * @return string $title New placeholder text
 */
function novelist_change_default_title( $title ) {
	$screen = get_current_screen();
	if ( 'book' == $screen->post_type ) {
		$label = novelist_get_label_singular( true );
		$title = sprintf( __( 'Enter %s title here', 'novelist' ), $label );
	}

	return $title;
}

add_filter( 'enter_title_here', 'novelist_change_default_title' );

/**
 * Register Taxonomies
 *
 * @since 1.0.0
 * @return void
 */
function novelist_setup_taxonomies() {

	$slug = defined( 'NOVELIST_SLUG' ) ? NOVELIST_SLUG : 'books';

	/** Genres */
	$genre_labels = array(
		'name'              => sprintf( _x( '%s Genres', 'taxonomy general name', 'novelist' ), novelist_get_label_singular() ),
		'singular_name'     => sprintf( _x( '%s Genre', 'taxonomy singular name', 'novelist' ), novelist_get_label_singular() ),
		'search_items'      => sprintf( __( 'Search %s Genres', 'novelist' ), novelist_get_label_singular() ),
		'all_items'         => sprintf( __( 'All %s Genres', 'novelist' ), novelist_get_label_singular() ),
		'parent_item'       => sprintf( __( 'Parent %s Genre', 'novelist' ), novelist_get_label_singular() ),
		'parent_item_colon' => sprintf( __( 'Parent %s Genre:', 'novelist' ), novelist_get_label_singular() ),
		'edit_item'         => sprintf( __( 'Edit %s Genre', 'novelist' ), novelist_get_label_singular() ),
		'update_item'       => sprintf( __( 'Update %s Genre', 'novelist' ), novelist_get_label_singular() ),
		'add_new_item'      => sprintf( __( 'Add New %s Genre', 'novelist' ), novelist_get_label_singular() ),
		'new_item_name'     => sprintf( __( 'New %s Genre Name', 'novelist' ), novelist_get_label_singular() ),
		'menu_name'         => __( 'Genres', 'novelist' ),
	);
	$genre_args   = apply_filters( 'novelist/taxonomy/genre-args', array(
			'hierarchical'      => true,
			'labels'            => apply_filters( 'novelist/taxonomy/genre-default-labels', $genre_labels ),
			'show_ui'           => true,
			'show_in_nav_menus' => true,
			'query_var'         => 'novelist-genre',
			'rewrite'           => array( 'slug' => $slug . '/genre', 'with_front' => false, 'hierarchical' => true ),
			'capabilities'      => array(
				'manage_terms' => 'manage_book_terms',
				'edit_terms'   => 'edit_book_terms',
				'assign_terms' => 'assign_book_terms',
				'delete_terms' => 'delete_book_terms'
			)
		)
	);
	register_taxonomy( 'novelist-genre', array( 'book' ), $genre_args );
	register_taxonomy_for_object_type( 'novelist-genre', 'book' );

	/** Series */
	$series_labels = array(
		'name'              => sprintf( _x( '%s Series', 'taxonomy general name', 'novelist' ), novelist_get_label_singular() ),
		'singular_name'     => sprintf( _x( '%s Series', 'taxonomy singular name', 'novelist' ), novelist_get_label_singular() ),
		'search_items'      => sprintf( __( 'Search %s Series', 'novelist' ), novelist_get_label_singular() ),
		'all_items'         => sprintf( __( 'All %s Series', 'novelist' ), novelist_get_label_singular() ),
		'parent_item'       => sprintf( __( 'Parent %s Series', 'novelist' ), novelist_get_label_singular() ),
		'parent_item_colon' => sprintf( __( 'Parent %s Series:', 'novelist' ), novelist_get_label_singular() ),
		'edit_item'         => sprintf( __( 'Edit %s Series', 'novelist' ), novelist_get_label_singular() ),
		'update_item'       => sprintf( __( 'Update %s Series', 'novelist' ), novelist_get_label_singular() ),
		'add_new_item'      => sprintf( __( 'Add New %s Series', 'novelist' ), novelist_get_label_singular() ),
		'new_item_name'     => sprintf( __( 'New %s Series Name', 'novelist' ), novelist_get_label_singular() ),
		'menu_name'         => __( 'Series', 'novelist' ),
	);
	$series_args   = apply_filters( 'novelist/taxonomy/series-args', array(
			'hierarchical'      => true,
			'labels'            => apply_filters( 'novelist/taxonomy/series-default-labels', $series_labels ),
			'show_ui'           => true,
			'show_admin_column' => true,
			'show_in_nav_menus' => true,
			'query_var'         => 'novelist-series',
			'rewrite'           => array( 'slug' => $slug . '/series', 'with_front' => false, 'hierarchical' => true ),
			'capabilities'      => array(
				'manage_terms' => 'manage_book_terms',
				'edit_terms'   => 'edit_book_terms',
				'assign_terms' => 'assign_book_terms',
				'delete_terms' => 'delete_book_terms'
			)
		)
	);
	register_taxonomy( 'novelist-series', array( 'book' ), $series_args );
	register_taxonomy_for_object_type( 'novelist-series', 'book' );

}

add_action( 'init', 'novelist_setup_taxonomies', 0 );

/**
 * Get Taxonomy Labels
 *
 * Get the singular and plural labels for the book taxonomies.
 *
 * @param string $taxonomy
 *
 * @since 1.0.0
 * @return array|bool False on failure
 */
function novelist_get_taxonomy_labels( $taxonomy = 'novelist-series' ) {

	$allowed_taxonomies = apply_filters( 'novelist/taxonomy/allowed-taxonomies', array(
		'novelist-genre',
		'novelist-series'
	) );

	if ( ! in_array( $taxonomy, $allowed_taxonomies ) ) {
		return false;
	}

	$labels   = array();
	$taxonomy = get_taxonomy( $taxonomy );

	if ( false !== $taxonomy ) {
		$singular = $taxonomy->labels->singular_name;
		$name     = $taxonomy->labels->name;
		$labels   = array(
			'name'          => $name,
			'singular_name' => $singular,
		);
	}

	return apply_filters( 'novelist/taxonomy/get-taxonomy-labels', $labels, $taxonomy );

}