<?php

/**
 * Widget for displaying word count progress.
 *
 * @package   novelist
 * @copyright Copyright (c) 2016, Nose Graze Ltd.
 * @license   GPL2+
 */
class Novelist_Word_Count_Widget extends WP_Widget {

	/**
	 * Novelist_Word_Count_Widget constructor.
	 *
	 * Register the widget with WordPress.
	 *
	 * @access public
	 * @since  1.0.0
	 * @return void
	 */
	public function __construct() {

		parent::__construct(
			'novelist_word_count_widget',
			__( 'Novelist Word Count', 'novelist' ),
			array( 'description' => __( 'Keep track of your progress towards a word count goal.', 'novelist' ) )
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

		do_action( 'novelist/widget/word-count/before-widget' );

		$template = novelist_get_template_part( 'widgets/widget', 'word-count', false );

		if ( $template ) {
			include $template;
		}

		do_action( 'novelist/widget/word-count/after-widget' );

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

		$instance = wp_parse_args( (array) $instance, array(
			'title'       => __( 'Word Count', 'novelist' ),
			'goal'        => '',
			'current'     => '',
			'color'       => '#333333',
			'label'       => '[current] / [goal]',
			'text_before' => '',
			'text_after'  => ''
		) );

		$title       = ! empty( $instance['title'] ) ? $instance['title'] : '';
		$goal        = ( ! empty( $instance['goal'] ) && is_numeric( $instance['goal'] ) ) ? $instance['goal'] : '';
		$current     = ( ! empty( $instance['current'] ) && is_numeric( $instance['current'] ) ) ? $instance['current'] : 0;
		$color       = ! empty( $instance['color'] ) ? $instance['color'] : '#333333';
		$label       = ! empty( $instance['label'] ) ? $instance['label'] : '';
		$text_before = ! empty( $instance['text_before'] ) ? $instance['text_before'] : '';
		$text_after  = ! empty( $instance['text_after'] ) ? $instance['text_after'] : '';

		do_action( 'novelist/widget/word-count/before-form' );

		?>
		<script type="text/javascript">
			//<![CDATA[
			jQuery(document).ready(function () {
				jQuery('.novelist-color-picker').wpColorPicker();
			});
			//]]>
		</script>

		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'novelist' ); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'goal' ); ?>"><?php _e( 'Word Count Goal:', 'novelist' ); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'goal' ); ?>" name="<?php echo $this->get_field_name( 'goal' ); ?>" type="number" value="<?php echo esc_attr( $goal ); ?>">
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'current' ); ?>"><?php _e( 'Current Word Count:', 'novelist' ); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'current' ); ?>" name="<?php echo $this->get_field_name( 'current' ); ?>" type="number" value="<?php echo esc_attr( $current ); ?>">
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'color' ); ?>"><?php _e( 'Bar Colour:', 'novelist' ); ?></label>
			<br>
			<input class="novelist-color-picker" id="<?php echo $this->get_field_id( 'color' ); ?>" name="<?php echo $this->get_field_name( 'color' ); ?>" type="text" value="<?php echo esc_attr( $color ); ?>" data-default-color="#333333">
		</p>

		<hr>

		<p>
			<label for="<?php echo $this->get_field_id( 'label' ); ?>"><?php _e( 'Label:', 'novelist' ); ?></label> <br>
			<?php _e('Use <mark>[current]</mark> as a placeholder for your current word count, and <mark>[goal]</mark> as a placeholder for your goal.', 'novelist'); ?> <br>
			<input class="widefat" id="<?php echo $this->get_field_id( 'label' ); ?>" name="<?php echo $this->get_field_name( 'label' ); ?>" type="text" value="<?php echo esc_attr( $label ); ?>">
		</p>

		<hr>

		<p><strong><?php _e( 'Add Text', 'novelist' ); ?></strong></p>

		<p>
			<label for="<?php echo $this->get_field_id( 'text_before' ); ?>"><?php _e( 'Text Before the Bar:', 'novelist' ); ?></label>
			<textarea class="widefat" id="<?php echo $this->get_field_id( 'text_before' ); ?>" name="<?php echo $this->get_field_name( 'text_before' ); ?>" rows="6"><?php echo esc_textarea( $text_before ); ?></textarea>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'text_after' ); ?>"><?php _e( 'Text After the Bar:', 'novelist' ); ?></label>
			<textarea class="widefat" id="<?php echo $this->get_field_id( 'text_after' ); ?>" name="<?php echo $this->get_field_name( 'text_after' ); ?>" rows="6"><?php echo esc_textarea( $text_after ); ?></textarea>
		</p>
		<?php

		do_action( 'novelist/widget/word-count/after-form' );

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
		$instance['goal']        = ( ! empty( $new_instance['goal'] ) && is_numeric( $new_instance['goal'] ) ) ? absint( $new_instance['goal'] ) : '';
		$instance['current']     = ( ! empty( $new_instance['current'] ) && is_numeric( $new_instance['current'] ) ) ? absint( $new_instance['current'] ) : '';
		$instance['color']       = ( ! empty( $new_instance['color'] ) ) ? $this->sanitize_hex_color( $new_instance['color'] ) : '#333333';
		$instance['label']       = ( ! empty( $new_instance['label'] ) ) ? strip_tags( $new_instance['label'] ) : '';
		$instance['text_before'] = ( ! empty( $new_instance['text_before'] ) ) ? wp_kses_post( $new_instance['text_before'] ) : '';
		$instance['text_after']  = ( ! empty( $new_instance['text_after'] ) ) ? wp_kses_post( $new_instance['text_after'] ) : '';

		do_action( 'novelist/widget/word-count/update', $instance, $new_instance, $old_instance );

		return $instance;

	}

	/**
	 * Sanitize Hex Color
	 *
	 * @param string $color
	 *
	 * @access public
	 * @since  1.0.0
	 * @return string
	 */
	public function sanitize_hex_color( $color ) {
		if ( ! empty( $color ) && preg_match( '|^#([A-Fa-f0-9]{3}){1,2}$|', $color ) ) {
			return $color;
		}

		return '';
	}

}

add_action( 'widgets_init', function () {
	register_widget( 'Novelist_Word_Count_Widget' );
} );