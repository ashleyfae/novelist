<?php
/**
 * Thickbox Functions
 *
 * @package   novelist
 * @copyright Copyright (c) 2016, Nose Graze Ltd.
 * @license   GPL2+
 * @since     1.1.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Load Thickbox Assets
 *
 * @param string $hook
 *
 * @since 1.1.0
 * @return void
 */
function novelist_load_thickbox_assets( $hook ) {
	if ( ! apply_filters( 'novelist/thickbox/load-thickbox-assets', true ) ) {
		return;
	}

	if ( apply_filters( 'novelist/thickbox/disable-tinymce-button', false ) ) {
		return;
	}

	if ( $hook != 'post.php' && $hook != 'edit.php' ) {
		return;
	}

	wp_enqueue_script( 'thickbox' );
	wp_enqueue_style( 'thickbox' );
}

add_action( 'admin_enqueue_scripts', 'novelist_load_thickbox_assets' );

/**
 * Media Button
 *
 * Add an "Insert Book Grid" button above the TinyMCE Editor on add/edit screens.
 *
 * @global $pagenow
 * @global $typenow
 *
 * @since 1.1.0
 * @return void
 */
function novelist_media_button() {
	global $pagenow, $typenow;

	if ( apply_filters( 'novelist/thickbox/disable-tinymce-button', false ) ) {
		return;
	}

	// Don't show on book CPT.
	if ( $typenow == 'book' ) {
		return;
	}

	$allowed_pages = array(
		'post.php',
		'page.php',
		'post-new.php',
		'post-edit.php'
	);

	if ( ! in_array( $pagenow, apply_filters( 'novelist/thickbox/media-button-pages', $allowed_pages ) ) ) {
		return;
	}
	?>
	<a href="#TB_inline?width=640&inlineId=add-book-grid" class="thickbox button novelist-thickbox">
		<span class="wp-media-buttons-icon dashicons dashicons-book" id="novelist-media-button"></span>
		<?php printf(
			__( 'Add %s Grid', 'novelist' ),
			novelist_get_label_singular()
		); ?>
	</a>
	<?php
}

add_action( 'media_buttons', 'novelist_media_button', 11 );

/**
 * Render Thickbox Frame
 *
 * Adds HTML into the admin footer for rendering the thickbox frame that appears
 * after clicking on the button.
 *
 * @global $pagenow
 * @global $typenow
 *
 * @since 1.1.0
 * @return void
 */
function novelist_admin_footer_for_thickbox() {
	global $pagenow, $typenow;

	if ( apply_filters( 'novelist/thickbox/disable-tinymce-button', false ) ) {
		return;
	}

	// Don't show on book CPT.
	if ( $typenow == 'book' ) {
		return;
	}

	$allowed_pages = array(
		'post.php',
		'page.php',
		'post-new.php',
		'post-edit.php'
	);

	if ( ! in_array( $pagenow, apply_filters( 'novelist/thickbox/media-button-pages', $allowed_pages ) ) ) {
		return;
	}
	?>
	<script type="text/javascript">
		function novelistBookGrid() {
			var atts = '';

			// @todo Series

			// @todo Genre

			// @todo Relation

			// Number of columns.
			atts += ' columns="' + jQuery('#novelist_number_columns').val() + '"';

			// Filter by series
			var series = jQuery('#novelist_series_filter').val();
			if (series != 'all') {
				atts += ' series="' + series + '"';
			}

			// Filter by genre
			var genre = jQuery('#novelist_genre_filter').val();
			if (genre != 'all') {
				atts += ' genre="' + genre + '"';
			}

			// Show title.
			if (jQuery('#novelist_show_title').val() == 'true') {
				atts += ' title="true"';
			}

			// Show series number.
			if (jQuery('#novelist_show_series_number').val() == 'true') {
				atts += ' series-number="true"';
			}

			// Show synopsis.
			switch (jQuery('#novelist_show_synopsis').val()) {
				case 'excerpt' :
					atts += ' excerpt="true"';
					break;

				case 'full' :
					atts += ' full-excerpt="true"';
					break;
			}

			// Show full content.
			if (jQuery('#novelist_show_full').val() == 'true') {
				atts += ' full-content="true"';
			}

			// Order by
			var orderby = jQuery('#novelist_orderby').val();
			if (orderby != 'menu_order') {
				atts += ' orderby="' + orderby + '"';
			}

			// Order
			if (jQuery('#novelist_order').val() != 'ASC') {
				atts += ' order="DESC"';
			}

			// Pagination
			if (jQuery('#novelist_pagination').val() != 'true') {
				atts += ' pagination="false"';
			}

			// Number of results
			var numberResults = jQuery('#novelist_number_results').val();
			if (numberResults != '12') {
				atts += ' number="' + numberResults + '"';
			}

			// Send the shortcode to the editor
			window.send_to_editor('[novelist-books' + atts + ']');
		}
	</script>

	<style>
		#add-book-grid-wrap .novelist-box-row {
			clear: both;
			overflow: hidden;
			margin: 1.5em 0;
		}

		#add-book-grid-wrap label {
			display: block;
			font-weight: 600;
		}

		@media (min-width: 768px) {
			#add-book-grid-wrap label {
				float: left;
				font-weight: 600;
				width: 40%;
			}

			#add-book-grid-wrap .novelist-box-row > div {
				float: right;
				width: 55%;
			}
		}
	</style>

	<div id="add-book-grid" style="display: none;">
		<div id="add-book-grid-wrap" class="wrap">
			<h3><?php _e( 'Add Book Grid', 'novelist' ); ?></h3>

			<!-- Number of Columns -->
			<div class="novelist-box-row">
				<label for="novelist_number_columns"><?php _e( 'Number of Columns', 'novelist' ); ?></label>
				<div>
					<select id="novelist_number_columns" name="novelist_number_columns">
						<?php foreach ( range( 1, 6 ) as $number ) : ?>
							<option value="<?php echo absint( $number ); ?>" <?php selected( 4, $number ); ?>><?php printf( _n( '%s Column', '%s Columns', $number, 'novelist' ), $number ); ?></option>
						<?php endforeach; ?>
					</select>
				</div>
			</div>

			<!-- Filter by Series -->
			<div class="novelist-box-row">
				<label for="novelist_series_filter"><?php _e( 'Filter by Series', 'novelist' ); ?></label>
				<div>
					<?php Novelist()->html->series_dropdown( array(
						'id'   => 'novelist_series_filter',
						'name' => 'novelist_series_filter'
					) ); ?>
				</div>
			</div>

			<!-- Filter by Genre -->
			<div class="novelist-box-row">
				<label for="novelist_genre_filter"><?php _e( 'Filter by Genre', 'novelist' ); ?></label>
				<div>
					<?php Novelist()->html->genre_dropdown( array(
						'id'   => 'novelist_genre_filter',
						'name' => 'novelist_genre_filter'
					) ); ?>
				</div>
			</div>

			<!-- Show Title -->
			<div class="novelist-box-row">
				<label for="novelist_show_title"><?php _e( 'Show the Book Title', 'novelist' ); ?></label>
				<div>
					<select id="novelist_show_title" name="novelist_show_title">
						<option value="true"><?php _e( 'Yes', 'novelist' ); ?></option>
						<option value="false" selected><?php _e( 'No', 'novelist' ); ?></option>
					</select>
				</div>
			</div>

			<!-- Show Series Number -->
			<div class="novelist-box-row">
				<label for="novelist_show_series_number"><?php _e( 'Show the Series Number', 'novelist' ); ?></label>
				<div>
					<select id="novelist_show_series_number" name="novelist_show_series_number">
						<option value="true"><?php _e( 'Yes', 'novelist' ); ?></option>
						<option value="false" selected><?php _e( 'No', 'novelist' ); ?></option>
					</select>
				</div>
			</div>

			<!-- Show Excerpt -->
			<div class="novelist-box-row">
				<label for="novelist_show_synopsis"><?php _e( 'Show Synopsis', 'novelist' ); ?></label>
				<div>
					<select id="novelist_show_synopsis" name="novelist_show_synopsis">
						<option value="excerpt"><?php _e( 'Yes - Excerpt', 'novelist' ); ?></option>
						<option value="full"><?php _e( 'Yes - Full Synopsis', 'novelist' ); ?></option>
						<option value="false" selected><?php _e( 'No', 'novelist' ); ?></option>
					</select>
				</div>
			</div>

			<!-- Show Full Content -->
			<div class="novelist-box-row">
				<label for="novelist_show_full"><?php _e( 'Show Full Content (includes cover, title, author, etc.)', 'novelist' ); ?></label>
				<div>
					<select id="novelist_show_full" name="novelist_show_full">
						<option value="true"><?php _e( 'Yes', 'novelist' ); ?></option>
						<option value="false" selected><?php _e( 'No', 'novelist' ); ?></option>
					</select>
				</div>
			</div>

			<!-- Order By -->
			<div class="novelist-box-row">
				<label for="novelist_orderby"><?php _e( 'Order By', 'novelist' ); ?></label>
				<div>
					<?php Novelist()->html->select( array(
						'id'       => 'novelist_orderby',
						'selected' => 'menu_order',
						'options'  => array(
							'menu_order'  => __( 'Menu Order', 'novelist' ),
							'title'       => __( 'Title', 'novelist' ),
							'date'        => __( 'Date', 'novelist' ),
							'publication' => __( 'Book Publication Date', 'novelist' ),
							'rand'        => __( 'Random', 'novelist' )
						)
					) ); ?>
					<div class="desc"><?php _e( '"Date" refers to the date the book was added into WordPress.', 'novelist' ); ?></div>
				</div>
			</div>

			<!-- Order -->
			<div class="novelist-box-row">
				<label for="novelist_order"><?php _e( 'Order', 'novelist' ); ?></label>
				<div>
					<?php Novelist()->html->select( array(
						'id'       => 'novelist_order',
						'selected' => 'ASC',
						'options'  => array(
							'ASC'  => __( 'Ascending', 'novelist' ),
							'DESC' => __( 'Descending', 'novelist' )
						)
					) ); ?>
				</div>
			</div>

			<!-- Pagination -->
			<div class="novelist-box-row">
				<label for="novelist_pagination"><?php _e( 'Split Results into Multiple Pages?', 'novelist' ); ?></label>
				<div>
					<?php Novelist()->html->select( array(
						'id'       => 'novelist_pagination',
						'selected' => 'true',
						'options'  => array(
							'true'  => __( 'Yes', 'novelist' ),
							'false' => __( 'No', 'novelist' )
						)
					) ); ?>
				</div>
			</div>

			<!-- Number -->
			<div class="novelist-box-row">
				<label for="novelist_number_results"><?php _e( 'Number of Results', 'novelist' ); ?></label>
				<div>
					<?php Novelist()->html->text( array(
						'id'    => 'novelist_number_results',
						'value' => '12',
						'type'  => 'number'
					) ); ?>
				</div>
			</div>

			<p class="submit">
				<input type="button" class="button-primary" value="<?php _e( 'Insert Grid', 'novelist' ); ?>" onclick="novelistBookGrid();">
				<a id="novelist-cancel-add-grid" class="button-secondary" onclick="tb_remove();"><?php _e( 'Cancel', 'novelist' ); ?></a>
			</p>
		</div>
	</div>
	<?php
}

add_action( 'admin_footer', 'novelist_admin_footer_for_thickbox' );