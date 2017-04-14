<?php

/**
 * HTML elements
 *
 * A helper class for outputting common HTML elements.
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
 * Class Novelist_HTML
 *
 * @since 1.1.0
 */
class Novelist_HTML {

	/**
	 * Series Dropdown
	 *
	 * @param array $args Arguments to override the defaults.
	 *
	 * @access public
	 * @since  1.1.0
	 * @return void
	 */
	public function series_dropdown( $args = array() ) {

		$defaults = array(
			'name'            => 'novelist_select_series',
			'id'              => 'novelist_select_series',
			'class'           => '',
			'multiple'        => false,
			'selected'        => 0,
			'number'          => 30,
			'standalones'     => __( 'Standalones', 'novelist' ),
			'show_option_all' => __( 'All Series', 'novelist' ),
			'term_args'       => array(
				'taxonomy' => 'novelist-series'
			)
		);

		$args    = wp_parse_args( $args, $defaults );
		$options = array();

		$series = get_terms( $args['term_args'] );

		if ( $series ) {
			foreach ( $series as $term ) {
				$options[ $term->term_id ] = $term->name;
			}
		}

		if ( $args['standalones'] ) {
			$options['none'] = $args['standalones'];
		}

		$this->select( array(
			'name'             => $args['name'],
			'selected'         => $args['selected'],
			'id'               => $args['id'],
			'class'            => $args['class'],
			'options'          => $options,
			'multiple'         => $args['multiple'],
			'show_option_all'  => $args['show_option_all'],
			'show_option_none' => false
		) );

	}

	/**
	 * Genre Dropdown
	 *
	 * @param array $args Arguments to override the defaults.
	 *
	 * @access public
	 * @since  1.1.0
	 * @return void
	 */
	public function genre_dropdown( $args = array() ) {

		$defaults = array(
			'name'            => 'novelist_genre_dropdown',
			'id'              => 'novelist_genre_dropdown',
			'class'           => '',
			'multiple'        => false,
			'selected'        => 0,
			'number'          => 30,
			'show_option_all' => __( 'All Genres', 'novelist' ),
			'term_args'       => array(
				'taxonomy' => 'novelist-genre'
			)
		);

		$args    = wp_parse_args( $args, $defaults );
		$options = array();

		$series = get_terms( $args['term_args'] );

		if ( $series ) {
			foreach ( $series as $term ) {
				$options[ $term->term_id ] = $term->name;
			}
		}

		$this->select( array(
			'name'             => $args['name'],
			'selected'         => $args['selected'],
			'id'               => $args['id'],
			'class'            => $args['class'],
			'options'          => $options,
			'multiple'         => $args['multiple'],
			'show_option_all'  => $args['show_option_all'],
			'show_option_none' => false
		) );

	}

	/**
	 * Render Text Field
	 *
	 * @param array $args Arguments to override the defaults.
	 *
	 * @access public
	 * @since  1.1.0
	 * @return void
	 */
	public function text( $args = array() ) {

		$defaults = array(
			'id'          => '',
			'name'        => '',
			'type'        => 'text',
			'value'       => '',
			'placeholder' => '',
			'class'       => '',
			'disabled'    => false
		);

		$args = wp_parse_args( $args, $defaults );

		$class    = implode( ' ', array_map( 'sanitize_html_class', explode( ' ', $args['class'] ) ) );
		$disabled = '';
		if ( $args['disabled'] ) {
			$disabled = ' disabled="disabled"';
		}
		?>
		<input type="<?php echo esc_attr( $args['type'] ); ?>" id="<?php echo esc_attr( $args['id'] ); ?>" class="<?php echo esc_attr( $class ); ?>" name="<?php echo esc_attr( $args['name'] ); ?>" value="<?php echo esc_attr( $args['value'] ); ?>" placeholder="<?php echo esc_attr( $args['placeholder'] ); ?>"<?php echo $disabled; ?>>
		<?php

	}

	/**
	 * Select Dropdown
	 *
	 * @param array $args Arguments to override the defaults.
	 *
	 * @access public
	 * @since  1.1.0
	 * @return void
	 */
	public function select( $args = array() ) {

		$defaults = array(
			'options'          => array(),
			'name'             => '',
			'class'            => '',
			'id'               => '',
			'selected'         => 0,
			'placeholder'      => '',
			'multiple'         => false,
			'show_option_all'  => '',
			'show_option_none' => ''
		);

		$args = wp_parse_args( $args, $defaults );

		if ( $args['multiple'] ) {
			$multiple = ' MULTIPLE';
		} else {
			$multiple = '';
		}

		$class = implode( ' ', array_map( 'sanitize_html_class', explode( ' ', $args['class'] ) ) );
		?>
		<select id="<?php echo esc_attr( $args['id'] ); ?>" class="<?php echo esc_attr( $class ); ?>" name="<?php echo esc_attr( $args['name'] ); ?>"<?php echo $multiple; ?>>

			<?php if ( $args['show_option_all'] ) :
				if ( $args['multiple'] ) {
					$selected = selected( true, in_array( 0, $args['selected'] ), false );
				} else {
					$selected = selected( $args['selected'], 0, false );
				}
				?>
				<option value="all"<?php echo $selected; ?>><?php echo esc_html( $args['show_option_all'] ); ?></option>
			<?php endif; ?>

			<?php
			if ( ! empty( $args['options'] ) ) {

				if ( $args['show_option_none'] ) {
					if ( $args['multiple'] ) {
						$selected = selected( true, in_array( - 1, $args['selected'] ), false );
					} else {
						$selected = selected( $args['selected'], - 1, false );
					}
					?>
					<option value="-1"<?php echo $selected; ?>><?php echo esc_html( $args['show_option_none'] ); ?></option>
					<?php
				}

				foreach ( $args['options'] as $key => $option ) {

					if ( $args['multiple'] && is_array( $args['selected'] ) ) {
						$selected = selected( true, in_array( $key, $args['selected'], true ), false );
					} else {
						$selected = selected( $args['selected'], $key, false );
					}

					?>
					<option value="<?php echo esc_attr( $key ); ?>"<?php echo $selected; ?>><?php echo esc_html( $option ); ?></option>
					<?php

				}

			}
			?>

		</select>
		<?php

	}

}