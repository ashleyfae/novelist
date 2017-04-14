<?php
/**
 * Template Functions
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
 * Returns the path to the Novelist templates directory
 *
 * @since 1.0.0
 * @return string
 */
function novelist_get_templates_dir() {
	return NOVELIST_PLUGIN_DIR . 'templates';
}

/**
 * Returns the URL to the Novelist templates directory
 *
 * @since 1.0.0
 * @return string
 */
function novelist_get_templates_url() {
	return NOVELIST_PLUGIN_URL . 'templates';
}

/**
 * Retrieves a template part
 *
 * Taken from bbPress
 *
 * @param string $slug
 * @param string $name Optional. Default null
 * @param bool   $load
 *
 * @uses  novelist_locate_template()
 * @uses  load_template()
 * @uses  get_template_part()
 *
 * @since 1.0.0
 * @return string
 */
function novelist_get_template_part( $slug, $name = null, $load = true ) {
	// Execute code for this part
	do_action( 'get_template_part_' . $slug, $slug, $name );

	// Setup possible parts
	$templates = array();
	if ( isset( $name ) ) {
		$templates[] = $slug . '-' . $name . '.php';
	}
	$templates[] = $slug . '.php';

	// Allow template parts to be filtered
	$templates = apply_filters( 'novelist/get-template-part/', $templates, $slug, $name );

	// Return the part that is found
	return novelist_locate_template( $templates, $load, false );
}

/**
 * Retrieve the name of the highest priority template file that exists.
 *
 * Searches in the STYLESHEETPATH before TEMPLATEPATH so that themes which
 * inherit from a parent theme can just overload one file. If the template is
 * not found in either of those, it looks in the theme-compat folder last.
 *
 * Taken from bbPress
 *
 * @param string|array $template_names Template file(s) to search for, in order.
 * @param bool         $load           If true the template file will be loaded if it is found.
 * @param bool         $require_once   Whether to require_once or require. Default true.
 *                                     Has no effect if $load is false.
 *
 * @since 1.0.0
 * @return string The template filename if one is located.
 */
function novelist_locate_template( $template_names, $load = false, $require_once = true ) {
	// No file found yet
	$located = false;

	// Try to find a template file
	foreach ( (array) $template_names as $template_name ) {

		// Continue if template is empty
		if ( empty( $template_name ) ) {
			continue;
		}

		// Trim off any slashes from the template name
		$template_name = ltrim( $template_name, '/' );

		// try locating this template file by looping through the template paths
		foreach ( novelist_get_theme_template_paths() as $template_path ) {
			if ( file_exists( $template_path . $template_name ) ) {
				$located = $template_path . $template_name;
				break;
			}
		}

		if ( $located ) {
			break;
		}

	}

	if ( ( true == $load ) && ! empty( $located ) ) {
		load_template( $located, $require_once );
	}

	return $located;
}

/**
 * Returns a list of paths to check for template locations
 *
 * @since 1.0.0
 * @return array
 */
function novelist_get_theme_template_paths() {
	$template_dir = novelist_get_theme_template_dir_name();

	$file_paths = array(
		1   => trailingslashit( get_stylesheet_directory() ) . $template_dir,
		10  => trailingslashit( get_template_directory() ) . $template_dir,
		100 => novelist_get_templates_dir()
	);

	$file_paths = apply_filters( 'novelist/template-paths', $file_paths );

	// sort the file paths based on priority
	ksort( $file_paths, SORT_NUMERIC );

	return array_map( 'trailingslashit', $file_paths );
}

/**
 * Returns the template directory name.
 *
 * Themes can filter this by using the novelist_templates_dir filter.
 *
 * @since 1.0.0
 * @return string
 */
function novelist_get_theme_template_dir_name() {
	return trailingslashit( apply_filters( 'novelist/templates-dir', 'novelist_templates' ) );
}

/**
 * After Book Content
 *
 * Executes an action after the post content. This allows the book information
 * to be automatically added.
 *
 * @see   novelist_add_book_information_content()
 *
 * @param string   $content Unfiltered post content
 *
 * @global WP_Post $post
 *
 * @since 1.0.0
 * @return string Filtered post content with book info added
 */
function novelist_after_book_content( $content ) {
	global $post;

	if ( ! is_a( $post, 'WP_Post' ) || ! is_main_query() || is_admin() || post_password_required() || $post->post_type != 'book' ) {
		return $content;
	}

	ob_start();
	do_action( 'novelist/book/after-content', $post->ID, $content );
	$content = ob_get_clean() . $content;

	return $content;
}

add_filter( 'the_content', 'novelist_after_book_content' );

/**
 * After Book Excerpt
 *
 * Replaces the excerpt with our excerpt template file. Includes the book cover
 * and synopsis.
 *
 * @see novelist_add_book_excerpt()
 *
 * @param string $excerpt
 *
 * @return string
 */
function novelist_after_book_excerpt( $excerpt ) {
	global $post;

	if ( ! is_a( $post, 'WP_Post' ) || ! is_main_query() || is_admin() || post_password_required() || $post->post_type != 'book' ) {
		return $excerpt;
	}

	ob_start();
	do_action( 'novelist/book/excerpt', $post->ID, $excerpt );
	$excerpt = ob_get_clean();

	return $excerpt;
}

add_filter( 'the_excerpt', 'novelist_after_book_excerpt' );

/**
 * Add Book Information
 *
 * Loads the template containing all the book information.
 *
 * @param int    $post_id ID of the current post
 * @param string $content Post content
 *
 * @since 1.0.0
 * @return void
 */
function novelist_add_book_information_content( $post_id, $content ) {
	novelist_get_template_part( 'book', 'content' );
}

add_action( 'novelist/book/after-content', 'novelist_add_book_information_content', 10, 2 );

/**
 * Add Excerpt
 *
 * Loads the template containing all the excerpt details.
 *
 * @param int    $post_id ID of the current post
 * @param string $excerpt Post excerpt
 *
 * @since 1.0.0
 * @return void
 */
function novelist_add_book_excerpt( $post_id, $excerpt ) {
	novelist_get_template_part( 'book', 'excerpt' );
}

add_action( 'novelist/book/excerpt', 'novelist_add_book_excerpt', 10, 2 );

/**
 * Modify Book Query
 *
 * Modifies the WP_Query to set the page order to "menu order".
 * Adds 'meta_query' parameters to remove any books marked as hidden.
 *
 * @param WP_Query $query
 *
 * @since 1.0.0
 * @return void
 */
function novelist_modify_book_query( $query ) {
	if ( ! $query->is_main_query() || is_admin() ) {
		return;
	}

	if ( is_post_type_archive( 'book' ) || is_tax( 'novelist-genre' ) ) {

		// Adjust the order.
		$query->set( 'orderby', 'menu_order' );
		$query->set( 'order', 'ASC' );

		// Hide books marked as hidden.
		$meta_query = array(
			array(
				'key'     => 'novelist_hide',
				'compare' => 'NOT EXISTS'
			)
		);
		$query->set( 'meta_query', $meta_query );

	} elseif ( is_tax( 'novelist-series' ) ) {

		// Adjust the order.
		$query->set( 'orderby', 'meta_value_num' );
		$query->set( 'order', 'ASC' );
		$query->set( 'meta_key', 'novelist_series' );

	}

	do_action( 'novelist/pre-get-posts', $query );
}

add_action( 'pre_get_posts', 'novelist_modify_book_query' );

/**
 * Body Classes
 *
 * Adds Novelist class names to the `<body>` tag if viewing a Novelist archive/singular page.
 *
 * @param $classes
 *
 * @since 1.0.5
 * @return array
 */
function novelist_body_classes( $classes ) {
	if ( is_post_type_archive( 'book' ) || is_tax( array( 'novelist-genre', 'novelist-series' ) ) ) {
		$classes[] = 'novelist-book-archive';
		$classes[] = 'novelist-page';
	} elseif ( is_singular( 'book' ) ) {
		$classes[] = 'novelist-page';
	}

	return $classes;
}

add_filter( 'body_class', 'novelist_body_classes' );