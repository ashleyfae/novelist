<?php

/**
 * Displays a list of all books in a given series.
 *
 * @package   novelist
 * @copyright Copyright (c) 2016, Nose Graze Ltd.
 * @license   GPL2+
 */
class Novelist_Books_By_Series extends WP_Widget {

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
			'novelist_books_by_series_widget',
			__( 'Novelist Books by Series', 'novelist' ),
			array( 'description' => __( 'List all of the books in a series.', 'novelist' ) )
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

		do_action( 'novelist/widget/books-by-series/before-widget', $args, $instance );

		if ( ! array_key_exists( 'series', $instance ) || $instance['series'] === '' ) {
			$instance['series'] = 0;
		}

		if ( $instance['text_before'] ) {
			echo wpautop( $instance['text_before'] );
		}

		if ( $instance['series'] || $instance['series'] === 0 ) {

			// Build the query args.
			$query_args = array(
				'post_type'      => 'book',
				'posts_per_page' => - 1,
				'nopaging'       => true,
				'orderby'        => 'meta_value_num',
				'meta_key'       => 'novelist_series',
				'order'          => 'ASC'
			);

			if ( is_numeric( $instance['series'] ) && $instance['series'] > 0 ) {
				$query_args['tax_query'] = array(
					array(
						'taxonomy' => 'novelist-series',
						'field'    => 'id',
						'terms'    => array( $instance['series'] )
					)
				);
			} elseif ( (int) $instance['series'] === - 1 ) {
				$all_series = get_terms( 'novelist-series', array( 'fields' => 'ids' ) );

				$query_args['tax_query'] = array(
					array(
						'taxonomy' => 'novelist-series',
						'field'    => 'id',
						'terms'    => $all_series,
						'operator' => 'NOT IN'
					)
				);
			}

			// Query for books.
			$books = new WP_Query( apply_filters( 'novelist/widget/books-by-series/query-args', $query_args ) );

			if ( $books->have_posts() ) {

				?>
				<ul class="novelist-books-series-list">

					<?php while ( $books->have_posts() ) : $books->the_post();

						$book_obj    = new Novelist_Book( get_the_ID() );
						$book_title  = $book_obj->get_title();
						$series_name = novelist_get_formatted_series( get_the_ID() );

						// Append the series name.
						if ( $series_name ) {
							$book_title = sprintf( '%s (%s)', $book_title, $series_name );
						}

						$final_html = '<li><a href="' . esc_url( get_permalink() ) . '">' . $book_title . '</a></li>';

						echo apply_filters( 'novelist/widget/books-by-series/book-entry', $final_html, $book_obj, $book_title, $series_name );

					endwhile;
					wp_reset_postdata();
					?>

				</ul>
				<?php

			}

		}

		if ( $instance['text_after'] ) {
			echo wpautop( $instance['text_after'] );
		}

		do_action( 'novelist/widget/books-by-series/after-widget', $args, $instance );

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

		$title       = ! empty( $instance['title'] ) ? $instance['title'] : '';
		$series      = ! empty( $instance['series'] ) ? (int) $instance['series'] : '';
		$text_before = ! empty( $instance['text_before'] ) ? $instance['text_before'] : '';
		$text_after  = ! empty( $instance['text_after'] ) ? $instance['text_after'] : '';
		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'novelist' ); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'series' ); ?>"><?php _e( 'Select a Series:', 'novelist' ); ?></label>
			<?php
			$args = array(
				'show_option_all'  => __( 'All Series', 'novelist' ),
				'show_option_none' => __( 'Standalones', 'novelist' ),
				'orderby'          => 'NAME',
				'hide_empty'       => false,
				'name'             => $this->get_field_name( 'series' ),
				'id'               => $this->get_field_id( 'series' ),
				'class'            => 'widefat',
				'selected'         => $series,
				'taxonomy'         => 'novelist-series',
			);
			wp_dropdown_categories( $args );
			?>
		</p>

		<hr>

		<p><strong><?php _e( 'Extra Text', 'novelist' ); ?></strong></p>

		<p>
			<label for="<?php echo $this->get_field_id( 'text_before' ); ?>"><?php _e( 'Text before the list:', 'novelist' ); ?></label>
			<textarea class="widefat" id="<?php echo $this->get_field_id( 'text_before' ); ?>" name="<?php echo $this->get_field_name( 'text_before' ); ?>" rows="10"><?php echo esc_textarea( $text_before ); ?></textarea>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'text_after' ); ?>"><?php _e( 'Text after the list:', 'novelist' ); ?></label>
			<textarea class="widefat" id="<?php echo $this->get_field_id( 'text_after' ); ?>" name="<?php echo $this->get_field_name( 'text_after' ); ?>" rows="10"><?php echo esc_textarea( $text_after ); ?></textarea>
		</p>
		<?php

		do_action( 'novelist/widget/books-by-series/after-form', $instance, $this );

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

		$instance                = array();
		$instance['title']       = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
		$instance['series']      = ( ! empty( $new_instance['series'] ) ) ? intval( $new_instance['series'] ) : '';
		$instance['text_before'] = ( ! empty( $new_instance['text_before'] ) ) ? wp_kses_post( $new_instance['text_before'] ) : '';
		$instance['text_after']  = ( ! empty( $new_instance['text_after'] ) ) ? wp_kses_post( $new_instance['text_after'] ) : '';

		do_action( 'novelist/widget/books-by-series/update', $instance, $new_instance, $old_instance );

		return $instance;

	}

}

add_action( 'widgets_init', function () {
	register_widget( 'Novelist_Books_By_Series' );
} );