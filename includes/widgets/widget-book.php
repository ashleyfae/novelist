<?php

/**
 * Widget for showcasing one of your books.
 *
 * @package   novelist
 * @copyright Copyright (c) 2016, Nose Graze Ltd.
 * @license   GPL2+
 */
class Novelist_Book_Widget extends WP_Widget {

	/**
	 * Novelist_Book_Widget constructor.
	 *
	 * Register the widget with WordPress.
	 *
	 * @access public
	 * @since  1.0.0
	 * @return void
	 */
	public function __construct() {

		parent::__construct(
			'novelist_book_widget',
			__( 'Novelist Book', 'novelist' ),
			array( 'description' => __( 'Showcase one of your books.', 'novelist' ) )
		);

	}

	/**
	 * Front-end display of the widget.
	 *
	 * @see    WP_Widget::widget()
	 *
	 * @param array $args     Widget arguments
	 * @param array $instance Saved widget settings
	 *
	 * @access public
	 * @since  1.0.0
	 * @return void
	 */
	public function widget( $args, $instance ) {

		echo $args['before_widget'];

		if ( ! empty( $instance['title'] ) ) {
			echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'];
		}

		do_action( 'novelist/widget/book/before-widget', $args, $instance );

		$template = novelist_get_template_part( 'widgets/widget', 'book', false );

		if ( $template ) {
			include $template;
		}

		do_action( 'novelist/widget/book/after-widget', $args, $instance );

		echo $args['after_widget'];

	}

	/**
	 * Back-end widget form.
	 *
	 * @see    WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 *
	 * @access public
	 * @since  1.0.0
	 * @return void
	 */
	public function form( $instance ) {

		$title             = ! empty( $instance['title'] ) ? $instance['title'] : '';
		$book_id           = ! empty( $instance['book'] ) ? $instance['book'] : '';
		$show_book_cover   = ! empty( $instance['show_book_cover'] ) ? $instance['show_book_cover'] : false;
		$book_cover_size   = ! empty( $instance['book_cover_size'] ) ? $instance['book_cover_size'] : 'medium';
		$book_cover_align  = ! empty( $instance['book_cover_align'] ) ? $instance['book_cover_align'] : 'center';
		$book_cover_width  = ! empty( $instance['book_cover_width'] ) ? intval( $instance['book_cover_width'] ) : '';
		$book_cover_height = ! empty( $instance['book_cover_height'] ) ? intval( $instance['book_cover_height'] ) : '';
		$show_synopsis     = ! empty( $instance['show_synopsis'] ) ? $instance['show_synopsis'] : false;
		$show_link         = ! empty( $instance['show_link'] ) ? $instance['show_link'] : false;
		$link_text         = ! empty( $instance['link_text'] ) ? $instance['link_text'] : '';
		$text_before       = ! empty( $instance['text_before'] ) ? $instance['text_before'] : '';
		$text_after        = ! empty( $instance['text_after'] ) ? $instance['text_after'] : '';

		$args       = array(
			'orderby'                => 'title',
			'order'                  => 'ASC',
			'post_type'              => 'book',
			'post_status'            => 'any',
			'posts_per_page'         => 500,
			'no_found_rows'          => true,
			'update_post_meta_cache' => false,
			'update_post_term_cache' => false
		);
		$book_query = new WP_Query( apply_filters( 'novelist/widget/book/book-query-args', $args ) );
		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'novelist' ); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'book' ); ?>"><?php _e( 'Book:', 'novelist' ); ?></label>
			<?php if ( $book_query->have_posts() ) : ?>
				<select class="widefat" id="<?php echo $this->get_field_id( 'book' ); ?>" name="<?php echo $this->get_field_name( 'book' ); ?>">
					<option value="" <?php selected( $book_id, '' ); ?>><?php _e( '- Select a Book -', 'novelist' ); ?></option>
					<?php while ( $book_query->have_posts() ) : $book_query->the_post(); ?>
						<option value="<?php echo esc_attr( get_the_ID() ); ?>" <?php selected( $book_id, get_the_ID() ); ?>><?php echo esc_html( get_the_title() ); ?></option>
					<?php endwhile; ?>
				</select>
			<?php else : ?>
				<br><?php printf( __( 'It looks like you don\'t have any %1$s yet! Why not <a href="%2$s">add one?</a>', 'novelist' ), novelist_get_label_plural(), esc_url( admin_url( '/post-new.php?post_type=book' ) ) ); ?>
			<?php endif; ?>

			<?php wp_reset_postdata(); ?>
		</p>

		<hr>

		<p><strong><?php _e( 'Book Cover Settings', 'novelist' ); ?></strong></p>

		<p class="novelist-row">
			<input id="<?php echo $this->get_field_id( 'show_book_cover' ); ?>" class="novelist-checkbox-change" data-target=".novelist-show-book-cover-contents" name="<?php echo $this->get_field_name( 'show_book_cover' ); ?>" type="checkbox" value="on" <?php checked( $show_book_cover, 'on' ); ?>>
			<label for="<?php echo $this->get_field_id( 'show_book_cover' ); ?>"><?php _e( 'Show book cover', 'novelist' ); ?></label>
		</p>

		<div class="novelist-show-book-cover-contents">
			<table>
				<tr class="novelist-row">
					<td>
						<label for="<?php echo $this->get_field_id( 'book_cover_size' ); ?>"><?php _e( 'Size:', 'novelist' ); ?></label>
						<select class="widefat novelist-checkbox-change" data-target=".novelist-custom-size-content" data-target-value="custom" id="<?php echo $this->get_field_id( 'book_cover_size' ); ?>" name="<?php echo $this->get_field_name( 'book_cover_size' ); ?>">
							<?php foreach ( novelist_get_image_sizes() as $key => $value ) : ?>
								<option value="<?php echo esc_attr( $key ); ?>" <?php selected( $book_cover_size, esc_attr( $key ) ); ?>><?php echo esc_html( $key ); ?></option>
							<?php endforeach; ?>
						</select>
					</td>
					<td>
						<label for="<?php echo $this->get_field_id( 'book_cover_align' ); ?>"><?php _e( 'Alignment:', 'novelist' ); ?></label>
						<select class="widefat" id="<?php echo $this->get_field_id( 'book_cover_align' ); ?>" name="<?php echo $this->get_field_name( 'book_cover_align' ); ?>">
							<?php foreach ( $this->get_alignments() as $key => $value ) : ?>
								<option value="<?php echo esc_attr( $key ); ?>" <?php selected( $book_cover_align, esc_attr( $key ) ); ?>><?php echo esc_html( $value ); ?></option>
							<?php endforeach; ?>
						</select>
					</td>
				</tr>
				<tr class="novelist-custom-size-content">
					<td>
						<label for="<?php echo $this->get_field_id( 'book_cover_width' ); ?>"><?php _e( 'Width:', 'novelist' ); ?></label>
						<input class="widefat" id="<?php echo $this->get_field_id( 'book_cover_width' ); ?>" name="<?php echo $this->get_field_name( 'book_cover_width' ); ?>" type="number" value="<?php echo esc_attr( $book_cover_width ); ?>">
					</td>
					<td>
						<label for="<?php echo $this->get_field_id( 'book_cover_height' ); ?>"><?php _e( 'Height:', 'novelist' ); ?></label>
						<input class="widefat" id="<?php echo $this->get_field_id( 'book_cover_height' ); ?>" name="<?php echo $this->get_field_name( 'book_cover_height' ); ?>" type="number" value="<?php echo esc_attr( $book_cover_height ); ?>">
					</td>
				</tr>
			</table>
		</div>

		<hr>

		<p>
			<input id="<?php echo $this->get_field_id( 'show_synopsis' ); ?>" name="<?php echo $this->get_field_name( 'show_synopsis' ); ?>" type="checkbox" value="on" <?php checked( $show_synopsis, 'on' ); ?>>
			<label for="<?php echo $this->get_field_id( 'show_synopsis' ); ?>"><?php _e( 'Show synopsis', 'novelist' ); ?></label>
		</p>

		<p class="novelist-row">
			<input id="<?php echo $this->get_field_id( 'show_link' ); ?>" class="novelist-checkbox-change" data-target=".novelist-show-link-contents" name="<?php echo $this->get_field_name( 'show_link' ); ?>" type="checkbox" value="on" <?php checked( $show_link, 'on' ); ?>>
			<label for="<?php echo $this->get_field_id( 'show_link' ); ?>"><?php _e( 'Show link to book page', 'novelist' ); ?></label>
		</p>

		<div class="novelist-show-link-contents">
			<p>
				<label for="<?php echo $this->get_field_id( 'link_text' ); ?>"><?php _e( 'Link Text:', 'novelist' ); ?></label>
				<input class="widefat" id="<?php echo $this->get_field_id( 'link_text' ); ?>" name="<?php echo $this->get_field_name( 'link_text' ); ?>" type="text" value="<?php echo esc_attr( $link_text ); ?>">
			</p>
		</div>

		<hr>

		<p><strong><?php _e( 'Extra Text', 'novelist' ); ?></strong></p>

		<p>
			<label for="<?php echo $this->get_field_id( 'text_before' ); ?>"><?php _e( 'Text before the book:', 'novelist' ); ?></label>
			<textarea class="widefat" id="<?php echo $this->get_field_id( 'text_before' ); ?>" name="<?php echo $this->get_field_name( 'text_before' ); ?>" rows="10"><?php echo esc_textarea( $text_before ); ?></textarea>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'text_after' ); ?>"><?php _e( 'Text after the book:', 'novelist' ); ?></label>
			<textarea class="widefat" id="<?php echo $this->get_field_id( 'text_after' ); ?>" name="<?php echo $this->get_field_name( 'text_after' ); ?>" rows="10"><?php echo esc_textarea( $text_after ); ?></textarea>
		</p>
		<?php

		do_action( 'novelist/widget/book/after-form', $instance, $this );

	}

	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see    WP_Widget::update()
	 *
	 * @param array $new_instance Values just submitted to be saved.
	 * @param array $old_instance Previously saved values from the database.
	 *
	 * @access public
	 * @since  1.0.0
	 * @return array
	 */
	public function update( $new_instance, $old_instance ) {

		$instance                      = array();
		$instance['title']             = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
		$instance['book']              = ( ! empty( $new_instance['book'] ) && is_numeric( $new_instance['book'] ) && get_post_type( $new_instance['book'] ) == 'book' ) ? intval( $new_instance['book'] ) : '';
		$instance['show_book_cover']   = ( ! empty( $new_instance['show_book_cover'] ) ) ? 'on' : false;
		$instance['book_cover_size']   = ( ! empty( $new_instance['book_cover_size'] ) && array_key_exists( $new_instance['book_cover_size'], novelist_get_image_sizes() ) ) ? strip_tags( $new_instance['book_cover_size'] ) : 'medium';
		$instance['book_cover_align']  = ( ! empty( $new_instance['book_cover_align'] ) && array_key_exists( $new_instance['book_cover_align'], $this->get_alignments() ) ) ? strip_tags( $new_instance['book_cover_align'] ) : 'center';
		$instance['book_cover_width']  = ( ! empty( $new_instance['book_cover_width'] ) && is_numeric( $new_instance['book_cover_width'] ) ) ? intval( $new_instance['book_cover_width'] ) : '';
		$instance['book_cover_height'] = ( ! empty( $new_instance['book_cover_height'] ) && is_numeric( $new_instance['book_cover_height'] ) ) ? intval( $new_instance['book_cover_height'] ) : '';
		$instance['show_synopsis']     = ( ! empty( $new_instance['show_synopsis'] ) ) ? 'on' : false;
		$instance['show_link']         = ( ! empty( $new_instance['show_link'] ) ) ? 'on' : false;
		$instance['link_text']         = ( ! empty( $new_instance['link_text'] ) ) ? wp_kses( $new_instance['link_text'], $this->get_allowed_html() ) : '';
		$instance['text_before']       = ( ! empty( $new_instance['text_before'] ) ) ? wp_kses_post( $new_instance['text_before'] ) : '';
		$instance['text_after']        = ( ! empty( $new_instance['text_after'] ) ) ? wp_kses_post( $new_instance['text_after'] ) : '';

		do_action( 'novelist/widget/book/update', $instance, $new_instance, $old_instance );

		return $instance;

	}

	/**
	 * Get Image Sizes
	 * 
	 * @deprecated
	 *
	 * @access public
	 * @since  1.0.0
	 * @return array
	 */
	public function get_image_sizes() {

		global $_wp_additional_image_sizes;

		$sizes = array();

		foreach ( get_intermediate_image_sizes() as $_size ) {
			if ( in_array( $_size, array( 'thumbnail', 'medium', 'medium_large', 'large' ) ) ) {
				$sizes[ $_size ]['width']  = get_option( "{$_size}_size_w" );
				$sizes[ $_size ]['height'] = get_option( "{$_size}_size_h" );
				$sizes[ $_size ]['crop']   = (bool) get_option( "{$_size}_crop" );
			} elseif ( isset( $_wp_additional_image_sizes[ $_size ] ) ) {
				$sizes[ $_size ] = array(
					'width'  => $_wp_additional_image_sizes[ $_size ]['width'],
					'height' => $_wp_additional_image_sizes[ $_size ]['height'],
					'crop'   => $_wp_additional_image_sizes[ $_size ]['crop'],
				);
			}
		}

		$sizes['full']   = array();
		$sizes['custom'] = array();

		return apply_filters( 'novelist/widget/book/image-sizes', $sizes );

	}

	/**
	 * Get Image Alignments
	 *
	 * @access public
	 * @since  1.0.0
	 * @return array
	 */
	public function get_alignments() {

		$alignments = array(
			'alignleft'   => __( 'Left', 'novelist' ),
			'aligncenter' => __( 'Center', 'novelist' ),
			'alignright'  => __( 'Right', 'novelist' )
		);

		return apply_filters( 'novelist/widget/book/image-alignments', $alignments );

	}

	/**
	 * Get Allowed HTML
	 *
	 * (For the link text)
	 *
	 * @access public
	 * @since  1.0.0
	 * @return array
	 */
	public function get_allowed_html() {

		$allowed_html = array(
			'span' => array()
		);

		return apply_filters( 'novelist/widget/book/allowed-link-text-html', $allowed_html );

	}

	/**
	 * Get Chosen Image Size
	 *
	 * Returns the chosen image size, to be used in wp_get_attachment_image()
	 * Will either be a string value of one of the pre-defined sizes, or
	 * an array of width and height values (in that order).
	 *
	 * @param array $instance
	 *
	 * @access public
	 * @since  1.0.0
	 * @return array|string
	 */
	public function get_chosen_size( $instance ) {
		$size = $instance['book_cover_size'];

		if ( $size != 'custom' ) {
			return strip_tags( $size );
		}

		$width  = $instance['book_cover_width'];
		$height = $instance['book_cover_height'];

		return array( $width, $height );
	}

}

add_action( 'widgets_init', function () {
	register_widget( 'Novelist_Book_Widget' );
} );