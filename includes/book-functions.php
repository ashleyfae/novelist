<?php
/**
 * Functions related to books and their layouts.
 *
 * @package   novelist
 * @copyright Copyright (c) 2016, Nose Graze Ltd.
 * @license   GPL2+
 */

/**
 * Get Book Fields
 *
 * Returns an array of all the available book information fields,
 * their placeholder values, and their default labels.
 *
 * Other plugins can add their own fields using this filter:
 *  + novelist/book/available-fields
 *
 * @since 1.0.0
 * @return array
 */
function novelist_get_book_fields() {
	$fields = array(
		'cover'          => array(
			'name'        => __( 'Cover Image', 'novelist' ),
			'placeholder' => '[cover]',
			'label'       => '[cover]',
			'alignment'   => 'left' // left, center, right
		),
		'title'          => array(
			'name'        => __( 'Book Title', 'novelist' ),
			'placeholder' => '[title]',
			'label'       => sprintf( __( '<strong>Title:</strong> %s', 'novelist' ), '[title]' ),
			'linebreak'   => 'on'
		),
		'series'         => array(
			'name'        => __( 'Series Name', 'novelist' ),
			'placeholder' => '[series]',
			'label'       => sprintf( __( '<strong>Series:</strong> %s', 'novelist' ), '[series]' ),
			'linebreak'   => 'on'
		),
		'publisher'      => array(
			'name'        => __( 'Publisher', 'novelist' ),
			'placeholder' => '[publisher]',
			'label'       => sprintf( __( '<strong>Published by:</strong> %s', 'novelist' ), '[publisher]' ),
			'linebreak'   => 'on'
		),
		'pub_date'       => array(
			'name'        => __( 'Publication Date', 'novelist' ),
			'placeholder' => '[date]',
			'label'       => sprintf( __( '<strong>Release Date:</strong> %s', 'novelist' ), '[date]' ),
			'linebreak'   => 'on'
		),
		'contributors'   => array(
			'name'        => __( 'Contributors', 'novelist' ),
			'placeholder' => '[contributors]',
			'label'       => sprintf( __( '<strong>Contributors:</strong> %s', 'novelist' ), '[contributors]' ),
			'linebreak'   => 'on'
		),
		'genre'          => array(
			'name'        => __( 'Genre', 'novelist' ),
			'placeholder' => '[genre]',
			'label'       => sprintf( __( '<strong>Genre:</strong> %s', 'novelist' ), '[genre]' ),
			'linebreak'   => 'on'
		),
		'pages'          => array(
			'name'        => __( 'Pages', 'novelist' ),
			'placeholder' => '[pages]',
			'label'       => sprintf( __( '<strong>Pages:</strong> %s', 'novelist' ), '[pages]' ),
			'linebreak'   => 'on'
		),
		'isbn13'         => array(
			'name'        => __( 'ISBN13', 'novelist' ),
			'placeholder' => '[isbn13]',
			'label'       => sprintf( __( '<strong>ISBN13:</strong> %s', 'novelist' ), '[isbn13]' ),
			'linebreak'   => 'on'
		),
		'asin'           => array(
			'name'        => __( 'ASIN', 'novelist' ),
			'placeholder' => '[asin]',
			'label'       => sprintf( __( '<strong>ASIN:</strong> %s', 'novelist' ), '[asin]' ),
			'linebreak'   => 'on'
		),
		'synopsis'       => array(
			'name'        => __( 'Synopsis', 'novelist' ),
			'placeholder' => '[synopsis]',
			'label'       => '<blockquote class="novelist-synopsis">[synopsis]</blockquote>'
		),
		'goodreads_link' => array(
			'name'        => __( 'Goodreads Link', 'novelist' ),
			'placeholder' => '[goodreads]',
			'label'       => sprintf( __( '<a href="%s">Add on Goodreads</a>', 'novelist' ), '[goodreads]' ),
			'linebreak'   => 'on'
		),
		'purchase_links' => array(
			'name'        => __( 'Purchase Links', 'novelist' ),
			'placeholder' => '[purchaselinks]',
			'label'       => sprintf( __( '<strong>Buy the Book:</strong> %s', 'novelist' ), '[purchaselinks]' ),
			'linebreak'   => 'on'
		),
		'series_books'   => array(
			'name'        => __( 'Other Books in the Series', 'novelist' ),
			'placeholder' => '[seriesbooks]',
			'label'       => sprintf( __( '<br><strong>Also in this series:</strong> %s', 'novelist' ), '[seriesbooks]' )
		),
		'excerpt'        => array(
			'name'        => __( 'Excerpt', 'novelist' ),
			'placeholder' => '[excerpt]',
			'label'       => '[excerpt]'
		),
		'extra_text'     => array(
			'name'        => __( 'Extra Text', 'novelist' ),
			'placeholder' => '[extra]',
			'label'       => '[extra]'
		),
	);

	return apply_filters( 'novelist/book/available-fields', $fields );
}

/**
 * Book Cover Alignment Options
 *
 * Returns an array of book cover alignment options.
 *
 * @since 1.0.0
 * @return array
 */
function novelist_book_alignment_options() {
	$options = array(
		'left'   => __( 'Left', 'novelist' ),
		'center' => __( 'Centered', 'novelist' ),
		'right'  => __( 'Right', 'novelist' )
	);

	return apply_filters( 'novelist/book/cover-alignment-options', $options );
}

/**
 * Get Default Book Layout Keys
 *
 * Returns an array of which settings we want to appear in the book layout
 * by default.
 *
 * This is only used when the layout hasn't been customized or when the 'Book Layout'
 * tab is reset to the default.
 *
 * @since 1.0.0
 * @return array
 */
function novelist_get_default_book_layout_keys() {
	$default_keys = array(
		'cover',
		'title',
		'series',
		'publisher',
		'pub_date',
		'genre',
		'pages',
		'synopsis',
		'goodreads_link',
		'purchase_links',
		'extra_text'
	);

	return apply_filters( 'novelist/settings/default-layout-keys', $default_keys );
}

/**
 * Get Default Book Field Values
 *
 * Returns the array of default fields. These are the ones used if no settings have
 * been changed. They're loaded on initial install or when the 'Book Layout' tab
 * is reset to the default.
 *
 * @uses  novelist_get_default_book_layout_keys()
 *
 * @param array|null $all_fields
 *
 * @since 1.0.0
 * @return array
 */
function novelist_get_default_book_field_values( $all_fields = null ) {
	if ( ! is_array( $all_fields ) ) {
		$all_fields = novelist_get_book_fields();
	}
	$default_keys   = novelist_get_default_book_layout_keys();
	$default_values = array();

	if ( ! is_array( $default_keys ) ) {
		return array();
	}

	foreach ( $default_keys as $key ) {
		if ( ! array_key_exists( $key, $all_fields ) ) {
			continue;
		}

		$key_value = $all_fields[ $key ];

		if ( array_key_exists( 'placeholder', $key_value ) ) {
			unset( $key_value['placeholder'] );
		}

		$default_values[ $key ] = $key_value;
	}

	return $default_values;
}

/**
 * Get Formatted Series
 *
 * Returns a formatted series name, combining the name of the series with
 * the position of the book in it.
 *
 * @param int  $book_id       ID of the book to get the series of.
 * @param bool $linked        Whether or not to display the results as links.
 * @param int  $series_number Number of the book in the series. If ommitted, we'll find this out based on $book_id.
 *
 * @since 1.0.0
 * @return bool|string False if there is no series
 */
function novelist_get_formatted_series( $book_id = 0, $linked = true, $series_number = null ) {
	if ( $series_number === null ) {
		$series_number = get_post_meta( $book_id, 'novelist_series', true );
	}

	// If we don't have a series number, just return the list of taxonomy terms.
	if ( empty( $series_number ) ) {
		return novelist_get_taxonomy_term_list($book_id, 'novelist-series');
	}

	$series_terms = wp_get_post_terms( $book_id, 'novelist-series' );

	if ( is_wp_error( $series_terms ) ) {
		return false;
	}

	$series_number_array = explode( ', ', $series_number );
	$final_list          = array();

    $taxonomy = get_taxonomy('novelist-series');
    if ($taxonomy instanceof WP_Taxonomy && ! $taxonomy->public) {
        $linked = false;
    }

	foreach ( $series_terms as $key => $term ) {
		$this_series_number = array_key_exists( $key, $series_number_array ) ? $series_number_array[ $key ] : $series_number_array[0];
		$series_name        = sprintf( '%1$s #%2$s', esc_html( $term->name ), '<span itemprop="position">' . $this_series_number . '</span>' );
		$term_link          = get_term_link( $term );
		$final_list[]       = $linked ? '<a href="' . esc_url( apply_filters( 'novelist/book/series-link', $term_link, $term, $book_id ) ) . '">' . $series_name . '</a>' : $series_name;
	}

	return implode( apply_filters( 'novelist/book/series-separator', ', ' ), $final_list );
}

/**
 * Gets a comma-separated list of taxonomy terms for the given book.
 * This factors in whether or not the taxonomy is public. If it's not public, the terms will not be linked to archive
 * pages. If it is public, then they will be.
 *
 * @param int $book_id
 * @param string $taxonomy_slug
 *
 * @return string|false
 */
function novelist_get_taxonomy_term_list($book_id, $taxonomy_slug) {
    $taxonomy = get_taxonomy($taxonomy_slug);

    if ($taxonomy instanceof WP_Taxonomy && ! $taxonomy->public) {
        $terms = get_the_terms($book_id, $taxonomy_slug);
        if (empty($terms) || is_wp_error($terms)) {
            return false;
        }

        return implode(', ', wp_list_pluck($terms, 'name'));
    } else {
        return get_the_term_list($book_id, $taxonomy_slug, '', ', ', '');
    }
}

/**
 * Get Books
 *
 * Returns an array of all Novelist books in an id => name format.
 * These results are cached in a transient to make sure everything
 * loads nice and fast! The transient is cleared when a book is
 * added or updated.
 *
 * @see   novelist_clear_books_transient()
 *
 * @since 1.0.0
 * @return array
 */
function novelist_get_books() {

	// If we have these settings cached, let's return them.
	if ( false !== ( $book_array = get_transient( 'novelist_books_array' ) ) ) {
		return $book_array;
	}

	$book_array = array();
	$query_args = array(
		'order'                  => 'ASC',
		'orderby'                => 'title',
		'post_type'              => 'book',
		'post_status'            => 'any',
		'posts_per_page'         => 200,
		'no_found_rows'          => true,
		'update_post_meta_cache' => false,
		'update_post_term_cache' => false
	);

	$book_query = new WP_Query( apply_filters( 'novelist/get-books-args', $query_args ) );

	if ( ! $book_query->have_posts() ) {
		wp_reset_postdata();

		return $book_array;
	}

	while ( $book_query->have_posts() ) : $book_query->the_post();

		$book_array[ get_the_ID() ] = get_the_title();

	endwhile;

	wp_reset_postdata();

	set_transient( 'novelist_books_array', $book_array );

	return $book_array;

}

/**
 * Get Latest Book
 *
 * Returns the WP_Post object for the most recent book entered into the system. This is judged
 * based on post publication date.
 *
 * @param string $status Post status to filter by. Default is 'publish'.
 *
 * @since 1.1.0
 * @return WP_Post|false WP_Post object if a book is found, otherwise false.
 */
function novelist_get_latest_book( $status = 'publish' ) {
	$query_args = array(
		'order'                  => 'DESC',
		'orderby'                => 'date',
		'post_type'              => 'book',
		'post_status'            => $status,
		'posts_per_page'         => 1,
		'no_found_rows'          => true,
		'update_post_meta_cache' => false,
		'update_post_term_cache' => false
	);

	$book_query = new WP_Query( apply_filters( 'novelist/get-latest-book-args', $query_args ) );

	if ( ! $book_query->have_posts() ) {
		return false;
	}

	if ( is_array( $book_query->posts ) && $book_query->posts[0] ) {
		return $book_query->posts[0];
	}

	return false;
}

/**
 * Get Latest Book ID
 *
 * Returns the ID number of the most recent book entered into the system. This is judged
 * based on post publication date.
 *
 * @uses  novelist_get_latest_book()
 *
 * @param string $status Post status to filter by. Default is 'publish'.
 *
 * @since 1.1.0
 * @return bool|int Post ID on success, false if no book is found.
 */
function novelist_get_latest_book_id( $status = 'publish' ) {
	$latest_book = novelist_get_latest_book( $status );

	if ( ! is_a( $latest_book, 'WP_Post' ) ) {
		return false;
	}

	return $latest_book->ID;
}

/**
 * Clear 'novelist_books_array' Transient
 *
 * The transient gets deleted when a 'book' CPT is saved.
 *
 * @see   novelist_get_books()
 *
 * @param int     $post_id ID of the post
 * @param WP_Post $post    Post object
 *
 * @since 1.0.0
 * @return void
 */
function novelist_clear_books_transient( $post_id, $post ) {
	delete_transient( 'novelist_books_array' );
}

add_action( 'novelist/meta-box/save-book', 'novelist_clear_books_transient', 10, 2 );

/**
 * Get Cover Link Choices
 *
 * Returns an array of options for where to link the book cover.
 * Includes all purchase links, Goodreads link, and "none".
 *
 * @since 1.0.0
 * @return array
 */
function novelist_get_cover_link_choices() {
	$link_choices = array(
		'none'      => __( 'None', 'novelist' ),
		'goodreads' => __( 'Goodreads', 'novelist' )
	);

	$purchase_links = novelist_get_option( 'purchase_links' );

	if ( is_array( $purchase_links ) && count( $purchase_links ) ) {
		foreach ( $purchase_links as $link_details ) {
			$name = $link_details['name'];

			if ( empty( $name ) ) {
				continue;
			}

			$link_key = array_key_exists( 'id', $link_details ) ? $link_details['id'] : esc_attr( sanitize_title( $name ) );

			$link_choices[ $link_key ] = $name;
		}
	}

	return apply_filters( 'novelist/get-cover-link-choices', $link_choices );
}

/**
 * Get Image Sizes
 *
 * Returns an array of all available image media sizes.
 *
 * @param bool $include_custom Whether or not to include 'custom as an option.
 *
 * @since 1.0.2
 * @return array
 */
function novelist_get_image_sizes( $include_custom = false ) {
	global $_wp_additional_image_sizes;

	$sizes = array();

	foreach ( get_intermediate_image_sizes() as $_size ) {
		if ( in_array( $_size, array( 'thumbnail', 'medium', 'medium_large', 'large' ) ) ) {
			$sizes[ $_size ] = $_size;
		} elseif ( isset( $_wp_additional_image_sizes[ $_size ] ) ) {
			$sizes[ $_size ] = $_size;
		}
		$sizes[ $_size ] = $_size;
	}

	$sizes['full'] = _x( 'full', 'image size', 'novelist' );

	if ( $include_custom ) {
		$sizes['custom'] = _x( 'custom', 'image size', 'novelist' );
	}

	return apply_filters( 'novelist/widget/book/image-sizes', $sizes );
}

/**
 * Get Default Cover Image URL
 *
 * @param string $size Desired image size.
 *
 * @since 1.1.0
 * @return string|false Image URL or false if there isn't one.
 */
function novelist_get_default_cover_url( $size = 'full' ) {
	$default_cover = novelist_get_option( 'default_cover_image', false );

	if ( empty( $default_cover ) ) {
		return false;
	}

	$url = wp_get_attachment_image_url( $default_cover, $size );

	return apply_filters( 'novelist/get-default-cover-url', $url, $default_cover, $size );
}

/**
 * Get Default Cover Image
 *
 * @param string $size  Desired image size to display.
 * @param string $class Class name(s) to add to the `<img>` tag.
 *
 * @since 1.1.0
 * @return string|false False if no image is set.
 */
function novelist_get_default_cover( $size = 'full', $class = '' ) {
	$default_cover = novelist_get_option( 'default_cover_image', false );

	if ( empty( $default_cover ) ) {
		return false;
	}

	$image = wp_get_attachment_image( intval( $default_cover ), $size, false, array( 'class' => 'novelist-cover-image ' . $class ) );

	return apply_filters( 'novelist/get-default-cover', $image, $default_cover, $size, $class );
}
