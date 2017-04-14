<?php
/**
 * shortcode-book-content-series-number.php
 *
 * @package   novelist
 * @copyright Copyright (c) 2016, Nose Graze Ltd.
 * @license   GPL2+
 */

$series_number = get_post_meta( get_the_ID(), 'novelist_series', true );

if ( empty( $series_number ) ) {
	return;
}

?>
<p class="novelist-book-series-number"><?php printf( __( 'Book %s', 'novelist' ), esc_html( $series_number ) ); ?></p>
