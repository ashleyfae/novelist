<?php
/**
 * Register and Display Book Meta Boxes
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
 * Register all the meta boxes for the book custom post type
 *
 * @since 1.0
 * @return void
 */
function novelist_add_book_meta_box() {
	$post_types = apply_filters( 'novelist/book-meta-box-post-types', array( 'book' ) );

	foreach ( $post_types as $post_type ) {
		add_meta_box( 'novelist_book_information', sprintf( __( '%s Information', 'novelist' ), novelist_get_label_singular() ), 'novelist_render_book_information_meta_box', $post_type, 'normal', 'high' );
		add_meta_box( 'novelist_book_series_number', __( 'Series Number', 'novelist' ), 'novelist_render_book_series_number_meta_box', $post_type, 'side', 'default' );
	}
}

add_action( 'add_meta_boxes', 'novelist_add_book_meta_box' );

/**
 * Render Book Information Meta Box
 *
 * @param WP_Post $post
 *
 * @since 1.0.0
 * @return void
 */
function novelist_render_book_information_meta_box( $post ) {
	do_action( 'novelist/meta-box/book-information', $post );

	wp_nonce_field( basename( __FILE__ ), 'novelist_book_meta_box_nonce' );
}

/**
 * Render Series Number Meta Box
 *
 * @param WP_Post $post
 *
 * @since 1.0.0
 * @return void
 */
function novelist_render_book_series_number_meta_box( $post ) {
	do_action( 'novelist/meta-box/series-number', $post );
}

/**
 * Save Meta Fields
 *
 * @param int     $post_id ID of the book being saved
 * @param WP_Post $post    Object of the book being saved
 *
 * @since 1.0.0
 * @return void
 */
function novelist_book_meta_box_save( $post_id, $post ) {

	/*
	 * Permission Check
	 */

	if ( ! isset( $_POST['novelist_book_meta_box_nonce'] ) || ! wp_verify_nonce( $_POST['novelist_book_meta_box_nonce'], basename( __FILE__ ) ) ) {
		return;
	}

	if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || ( defined( 'DOING_AJAX' ) && DOING_AJAX ) || isset( $_REQUEST['bulk_edit'] ) ) {
		return;
	}

	if ( isset( $post->post_type ) && 'revision' == $post->post_type ) {
		return;
	}

	if ( ! current_user_can( 'edit_book', $post_id ) ) {
		return;
	}

	/*
	 * Okay now we can save.
	 */

	$fields = array(
		'novelist_title',
		'novelist_cover',
		'novelist_series',
		'novelist_publisher',
		'novelist_pub_date',
		'novelist_contributors',
		'novelist_pages',
		'novelist_isbn',
		'novelist_asin',
		'novelist_synopsis',
		'novelist_goodreads',
		'novelist_purchase_links',
		'novelist_excerpt',
		'novelist_extra',
		'novelist_hide'
	);

	foreach ( apply_filters( 'novelist/book/meta-box/saved-fields', $fields ) as $field ) {
		if ( ! empty( $_POST[ $field ] ) ) {
			$new = apply_filters( 'novelist/book/meta-box/sanitize/' . $field, $_POST[ $field ] );
			update_post_meta( $post_id, $field, $new );

			// Add a timestamp for the pub date.
			if ( $field == 'novelist_pub_date' ) {
				$timestamp = strtotime( $new );

				update_post_meta( $post_id, 'novelist_pub_date_timestamp', $timestamp );
			}
		} else {
			delete_post_meta( $post_id, $field );
		}
	}

	do_action( 'novelist/meta-box/save-book', $post_id, $post );

}

add_action( 'save_post', 'novelist_book_meta_box_save', 10, 2 );

/**
 * Render Book Information Fields
 *
 * Plugins can insert their own HTML for custom fields by hooking
 * into this action:
 *  + novelist/meta-box/display-field-{key}
 *
 * @param WP_Post $post
 *
 * @since 1.0.0
 * @return void
 */
function novelist_render_book_information_fields( $post ) {
	$all_fields     = novelist_get_book_fields();
	$enabled_fields = novelist_get_option( 'book_layout', novelist_get_default_book_field_values( $all_fields ) );

	// Always include the ISBN and ASIN
	if ( ! array_key_exists( 'isbn13', $enabled_fields ) ) {
		$enabled_fields['isbn13'] = $all_fields['isbn13'];
	}
	if ( ! array_key_exists( 'asin', $enabled_fields ) ) {
		$enabled_fields['asin'] = $all_fields['asin'];
	}

	$book = new Novelist_Book( $post->ID );

	if ( ! empty( $post->ID ) ) {
		?>
		<div class="novelist-box-row">
			<label for="novelist_book_id"><?php esc_html_e( 'Book ID', 'novelist' ); ?></label>
			<div class="novelist-input-wrapper">
				<?php echo esc_html( $post->ID ); ?>
			</div>
		</div>
		<?php
	}

	// Loop through each field and display it.
	foreach ( $enabled_fields as $key => $settings ) {

		switch ( $key ) {

			case 'cover' :
				$cover_ID = $book->cover_ID;
				?>
				<div class="novelist-box-row">
					<label for="novelist_cover"><?php _e( 'Cover Image', 'novelist' ); ?></label>
					<div class="novelist-input-wrapper">
						<?php
						if ( ! empty( $cover_ID ) ) {
							$attr = array(
								'id'    => 'novelist_cover_image',
								'style' => ''
							);

							echo wp_get_attachment_image( intval( $cover_ID ), 'medium', false, $attr );
						} else {
							?>
							<img id="novelist_cover_image" src="" style="display: none;">
							<?php
						}
						?>

						<div class="novelist-cover-image-fields">
							<input type="button" value="<?php _e( 'Upload Image', 'novelist' ); ?>" class="button novelist_upload_image_button" id="novelist_cover_upload" onclick="return novelist_open_uploader('novelist_cover');">
							<input type="button" value="<?php _e( 'Remove Image', 'novelist' ); ?>" class="button novelist_image_remove_button" id="novelist_cover_remove" onclick="return novelist_clear_uploader('novelist_cover');" style="<?php echo empty( $cover_ID ) ? 'display: none;' : ''; ?>">
						</div>

						<input type="hidden" name="novelist_cover" id="novelist_cover" value="<?php echo esc_attr( $cover_ID ); ?>">
					</div>
				</div>
				<?php
				break;

			case 'title' :
				?>
				<div class="novelist-box-row">
					<label for="novelist_title"><?php _e( 'Book Title', 'novelist' ); ?></label>
					<div class="novelist-input-wrapper">
						<input type="text" id="novelist_title" name="novelist_title" value="<?php echo esc_attr( $book->get_title() ); ?>">
					</div>
				</div>
				<?php
				break;

			case 'series' :
				// tk
				break;

			case 'publisher' :
				?>
				<div class="novelist-box-row">
					<label for="novelist_publisher"><?php _e( 'Publisher', 'novelist' ); ?></label>
					<div class="novelist-input-wrapper">
						<input type="text" id="novelist_publisher" name="novelist_publisher" value="<?php echo esc_attr( $book->publisher ); ?>">
					</div>
				</div>
				<?php
				break;

			case 'pub_date' :
				?>
				<div class="novelist-box-row">
					<label for="novelist_pub_date"><?php _e( 'Publication Date', 'novelist' ); ?></label>
					<div class="novelist-input-wrapper">
						<input type="text" id="novelist_pub_date" name="novelist_pub_date" value="<?php echo esc_attr( $book->pub_date ); ?>">
					</div>
				</div>
				<?php
				break;

			/*
			 * Contributors
			 */
			case 'contributors' :
				?>
				<div class="novelist-box-row">
					<label for="novelist_contributors"><?php _e( 'Contributors', 'novelist' ); ?></label>
					<div class="novelist-input-wrapper">
						<input type="text" id="novelist_contributors" name="novelist_contributors" value="<?php echo esc_attr( $book->contributors ); ?>">
					</div>
				</div>
				<?php
				break;

			case 'genre' :
				// tk
				break;

			case 'pages' :
				?>
				<div class="novelist-box-row">
					<label for="novelist_pages"><?php _e( 'Number of Pages', 'novelist' ); ?></label>
					<div class="novelist-input-wrapper">
						<input type="number" id="novelist_pages" name="novelist_pages" value="<?php echo esc_attr( $book->pages ); ?>">
					</div>
				</div>
				<?php
				break;

			case 'isbn13' :
				?>
				<div class="novelist-box-row">
					<label for="novelist_isbn"><?php _e( 'ISBN13', 'novelist' ); ?></label>
					<div class="novelist-input-wrapper">
						<input type="text" id="novelist_isbn" name="novelist_isbn" value="<?php echo esc_attr( $book->isbn13 ); ?>">
					</div>
				</div>
				<?php
				break;

			case 'asin' :
				?>
				<div class="novelist-box-row">
					<label for="novelist_asin"><?php _e( 'ASIN', 'novelist' ); ?></label>
					<div class="novelist-input-wrapper">
						<input type="text" id="novelist_asin" name="novelist_asin" value="<?php echo esc_attr( $book->asin ); ?>">
					</div>
				</div>
				<?php
				break;

			case 'synopsis' :
				$tinymce_args = array(
					'media_buttons' => false,
					'textarea_name' => 'novelist_synopsis',
					'teeny'         => true
				);
				?>
				<div class="novelist-box-row">
					<label for="novelist_synopsis"><?php _e( 'Synopsis', 'novelist' ); ?></label>
					<div class="novelist-input-wrapper">
						<?php wp_editor( $book->synopsis, 'novelist_synopsis', apply_filters( 'novelist/book/meta-box/synopsis-tinymce-args', $tinymce_args ) ); ?>
					</div>
				</div>
				<?php
				break;

			case 'goodreads_link' :
				?>
				<div class="novelist-box-row">
					<label for="novelist_goodreads"><?php _e( 'Goodreads URL', 'novelist' ); ?></label>
					<div class="novelist-input-wrapper">
						<input type="url" id="novelist_goodreads" name="novelist_goodreads" value="<?php echo esc_url( $book->goodreads_link ); ?>" placeholder="http://">
					</div>
				</div>
				<?php
				break;

			case 'purchase_links' :
				$links = novelist_get_option( 'purchase_links', array() );
				if ( is_array( $links ) ) {
					foreach ( $links as $i => $link ) {
						$link_key = array_key_exists( 'id', $link ) ? $link['id'] : esc_attr( sanitize_title( $link['name'] ) );
						$value    = array_key_exists( $link_key, $book->purchase_links ) ? $book->purchase_links[ $link_key ] : '';
						?>
						<div class="novelist-box-row">
							<label for="novelist_buy_link_<?php echo esc_attr( $link_key ); ?>"><?php printf( __( '%s URL', 'novelist' ), esc_html( $link['name'] ) ); ?></label>
							<div class="novelist-input-wrapper">
								<input type="url" id="novelist_buy_link_<?php echo esc_attr( $link_key ); ?>" name="novelist_purchase_links[<?php echo esc_attr( $link_key ); ?>]" value="<?php echo esc_url( $value ); ?>" placeholder="http://">
							</div>
						</div>
						<?php
					}
				}
				break;

			/*
			 * Excerpt
			 */
			case 'excerpt' :
				$tinymce_args = array(
					'media_buttons' => false,
					'textarea_name' => 'novelist_excerpt',
					'teeny'         => true
				);
				?>
				<div class="novelist-box-row">
					<label for="novelist_extra"><?php _e( 'Excerpt', 'novelist' ); ?></label>
					<div class="novelist-input-wrapper">
						<?php wp_editor( $book->excerpt, 'novelist_excerpt', apply_filters( 'novelist/book/meta-box/excerpt-tinymce-args', $tinymce_args ) ); ?>
					</div>
				</div>
				<?php
				break;

			/*
			 * Extra Text
			 */
			case 'extra_text' :
				$tinymce_args = array(
					'media_buttons' => true,
					'textarea_name' => 'novelist_extra',
					'teeny'         => true
				);
				?>
				<div class="novelist-box-row">
					<label for="novelist_extra"><?php _e( 'Extra Text', 'novelist' ); ?></label>
					<div class="novelist-input-wrapper">
						<?php wp_editor( $book->extra_text, 'novelist_extra', apply_filters( 'novelist/book/meta-box/extra-text-tinymce-args', $tinymce_args ) ); ?>
					</div>
				</div>
				<?php
				break;

		}

		// Allow plugins to hook in here.
		do_action( 'novelist/meta-box/display-field-' . $key, $book, $post, $settings );

	}
}

add_action( 'novelist/meta-box/book-information', 'novelist_render_book_information_fields', 10 );

/**
 * Render Extra Book Fields
 *
 * Includes extra fields that are not necessarily related to the
 * book information.
 *
 * @param WP_Post $post
 *
 * @since 1.0.0
 * @return void
 */
function novelist_render_extra_book_fields( $post ) {

	$hide = get_post_meta( $post->ID, 'novelist_hide', true );
	$hide = ( $hide == 'on' ) ? true : $hide;
	?>
	<div class="novelist-box-row">
		<label for="novelist_hide"><?php _e( 'Hide from Archives', 'novelist' ); ?></label>
		<div class="novelist-input-wrapper">
			<input type="checkbox" id="novelist_hide" name="novelist_hide" value="1" <?php checked( $hide, true ); ?>> <?php _e( 'Yes', 'novelist' ); ?>
		</div>
	</div>
	<?php

}

add_action( 'novelist/meta-box/book-information', 'novelist_render_extra_book_fields', 20 );


/**
 * Render Series Number Field
 *
 * @param WP_Post $post
 *
 * @since 1.0.0
 * @return void
 */
function novelist_render_series_number_field( $post ) {
	$book = new Novelist_Book( $post->ID );
	?>
	<div class="novelist-series-number-wrap">
		<label for="novelist_series"><?php _e( 'Number in the Series', 'novelist' ); ?></label>
		<input type="text" id="novelist_series" name="novelist_series" value="<?php echo esc_attr( $book->series_number ); ?>">
	</div>
	<?php
}

add_action( 'novelist/meta-box/series-number', 'novelist_render_series_number_field' );
