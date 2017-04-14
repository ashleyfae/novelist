<?php
/**
 * Functions for importing the demo book.
 *
 * @package   novelist
 * @copyright Copyright (c) 2016, Nose Graze Ltd.
 * @license   GPL2+
 */

function novelist_import_demo_book() {
	check_ajax_referer( 'novelist_import_demo_book', 'nonce' );

	if ( ! current_user_can( 'publish_books' ) ) {
		wp_send_json_error( sprintf( __( 'You don\'t have permission to add %s', 'novelist' ), novelist_get_label_plural( true ) ) );

		exit;
	}

	// Gather the book data.
	$book_data = array(
		'post_title'  => __( 'Under a Forever Sky', 'novelist' ),
		'post_status' => 'private',
		'post_type'   => 'book',
		'meta_input'  => array(
			'novelist_title'              => __( 'Under a Forever Sky', 'novelist' ),
			'novelist_series'             => '1',
			'novelist_publisher'          => __( 'Night Sky Publishing', 'novelist' ),
			'novelist_pub_date'           => __( 'February 18th 2016', 'novelist' ),
			'novelist_pub_date_timestamp' => strtotime( 'February 18th 2016' ),
			'novelist_pages'              => '316',
			'novelist_synopsis'           => __( 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.
			
			Aenean sagittis risus vel leo sodales, ut varius felis mattis. Interdum et malesuada fames ac ante ipsum primis in faucibus. Vivamus convallis tortor diam, ac ultricies elit sodales id. Nunc vel vulputate neque. Cras sed semper nunc. Duis eleifend auctor feugiat. Suspendisse maximus et sem et semper. Fusce venenatis massa in ultricies maximus. Fusce pulvinar nisl quis tincidunt finibus. Nam euismod ipsum felis, ac vehicula ex condimentum sed. In tincidunt sapien at tellus sagittis, a convallis nibh vulputate. Donec vitae ex nec mauris porttitor imperdiet a in metus. Aliquam erat volutpat. Vestibulum libero risus, fringilla sed nisi eu, pellentesque ullamcorper ex. Mauris vel dapibus arcu.

Proin nisl enim, cursus ac felis in, tempus sodales velit. Donec et dolor nibh. Donec mauris magna, tincidunt sit amet massa ut, accumsan pulvinar arcu.', 'novelist' ),
			'novelist_goodreads'          => 'https://www.goodreads.com',
			'novelist_extra'              => __( 'Phasellus a eros tempus, scelerisque mi a, consectetur velit. Donec arcu augue, finibus nec cursus eu, posuere eu nisl.
		
		Vivamus non dui ac enim aliquet dapibus. Nulla interdum auctor egestas. Duis interdum sapien id ipsum malesuada, eu porttitor felis rutrum. Curabitur laoreet hendrerit tristique. Pellentesque pretium, nulla eu finibus condimentum, nibh elit faucibus diam, nec volutpat arcu nunc eget ligula. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Quisque dictum justo erat, convallis pulvinar ex scelerisque at. Sed sit amet euismod nisl.', 'novelist' )
		)
	);

	/*
	 * Insert the book.
	 */
	$book_id = wp_insert_post( apply_filters( 'novelist/demo-book/post-data', $book_data ) );

	// Problem adding the book - bail.
	if ( empty( $book_id ) || is_wp_error( $book_id ) ) {
		wp_send_json_error( __( 'Error: There was a problem adding the demo book.', 'novelist' ) );
	}

	/*
	 * Update the purchase links.
	 */
	$saved_links = novelist_get_option( 'purchase_links', array() );
	$new_links   = array();
	if ( is_array( $saved_links ) ) {
		foreach ( $saved_links as $i => $link ) {
			$link_key = esc_attr( sanitize_title( $link['name'] ) );

			switch ( $link_key ) {
				case 'amazon' :
					$new_links[ $link_key ] = 'https://amazon.com';
					break;
				case 'barnes-noble' :
					$new_links[ $link_key ] = 'http://www.barnesandnoble.com/';
					break;
				default :
					$new_links[ $link_key ] = 'https://novelistplugin.com';
			}
		}

		update_post_meta( $book_id, 'novelist_purchase_links', apply_filters( 'novelist/demo-book/purchase-links', $new_links ) );
	}

	/*
	 * Insert the book series.
	 */
	$term_info = wp_insert_term( apply_filters( 'novelist/demo-book/series-name', __( 'Night Sky', 'novelist' ) ), 'novelist-series' );

	if ( is_array( $term_info ) && ! is_wp_error( $term_info ) ) {
		wp_set_post_terms( $book_id, $term_info['term_id'], 'novelist-series', false );
	}

	/*
	 * Insert the genre.
	 */
	$term_info = wp_insert_term( apply_filters( 'novelist/demo-book/genre-name', __( 'Fantasy', 'novelist' ) ), 'novelist-genre' );

	if ( is_array( $term_info ) && ! is_wp_error( $term_info ) ) {
		wp_set_post_terms( $book_id, $term_info['term_id'], 'novelist-genre', false );
	}

	/*
	 * Upload the book cover.
	 */
	$image_url = NOVELIST_PLUGIN_URL . 'assets/images/under-forever-sky.jpg';

	preg_match( '/[^\?]+\.(jpe?g|jpe|gif|png)\b/i', $image_url, $matches );

	if ( $matches ) {
		$file_array         = array();
		$file_array['name'] = basename( $matches[0] );

		// Download file to temp location.
		$file_array['tmp_name'] = download_url( $image_url );

		if ( ! is_wp_error( $file_array['tmp_name'] ) ) {
			$id = media_handle_sideload( $file_array, $book_id, __( 'Cover for Under a Forever Sky', 'novelist' ) );

			// If error storing permanently, unlink.
			if ( is_wp_error( $id ) ) {
				@unlink( $file_array['tmp_name'] );
			}

			// Attach the cover to the book.
			update_post_meta( $book_id, 'novelist_cover', absint( $id ) );
		}
	}

	/*
	 * Update option to designate that we've added the demo book.
	 */
	update_option( 'novelist_imported_demo_book', true );

	/*
	 * Action
	 */
	do_action( 'novelist/demo-book/import', $book_id );

	$response = sprintf(
		'<a href="%1$s" class="button button-primary" target="_blank">%2$s</a> <a href="%3$s" class="button button-secondary" target="_blank">%4$s</a>',
		esc_url( get_permalink( $book_id ) ),
		__( 'View Book', 'novelist' ),
		esc_url( get_edit_post_link( $book_id ) ),
		__( 'Edit Book', 'novelist' )
	);

	wp_send_json_success( $response );

	exit;
}

add_action( 'wp_ajax_novelist_import_demo_book', 'novelist_import_demo_book' );