<?php
/**
 * Template for displaying the Novelist_Word_Count_Widget
 *
 * @package   novelist
 * @copyright Copyright (c) 2016, Nose Graze Ltd.
 * @license   GPL2+
 */

/*
 * Gather variables from the widget settings.
 */
$goal    = ! empty( $instance['goal'] ) ? absint( $instance['goal'] ) : 0;
$current = ! empty( $instance['current'] ) ? absint( $instance['current'] ) : 0;
$color   = ! empty( $instance['color'] ) ? $instance['color'] : '#333333';

/*
 * Build the label.
 * We need to replace the shortcodes with their values.
 */
$find    = array(
	'[goal]',
	'[current]'
);
$replace = array(
	number_format( $goal ),
	number_format( $current )
);
$label   = ! empty( $instance['label'] ) ? str_replace( $find, $replace, $instance['label'] ) : '';

// Display 'before' text.
if ( $instance['text_before'] ) {
	echo wpautop( $instance['text_before'] );
}

// Calculate the percentage complete.
$sanitized_goal = str_replace( ',', '', $goal );
$percentage     = ( $sanitized_goal > 0 ) ? round( ( str_replace( ',', '', $current ) / $sanitized_goal ) * 100 ) : 0;

/*
 * Display the progress bar.
 */
?>
	<div class="novelist-meter" style="border-color: <?php echo esc_attr( $color ); ?>">
		<span class="novelist-progress" style="width: <?php echo esc_attr( $percentage ); ?>%; background-color: <?php echo esc_attr( $color ); ?>"></span>
	</div>
<?php if ( ! empty( $label ) ) : ?>
	<div class="novelist-progress-label">
		<?php echo $label; ?>
	</div>
<?php endif;

// Display 'after' text.
if ( $instance['text_after'] ) {
	echo wpautop( $instance['text_after'] );
}