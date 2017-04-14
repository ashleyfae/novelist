<?php
/**
 * Button to view more information about the book.
 *
 * You can use the filter 'novelist/shortcode/book/button-text' to change the button text.
 *
 * @package   novelist
 * @copyright Copyright (c) 2016, Nose Graze Ltd.
 * @license   GPL2+
 */
?>
<div class="novelist-book-details-button">
	<a href="<?php echo esc_url( get_permalink() ); ?>" class="novelist-button"><?php echo apply_filters( 'novelist/shortcode/book/button-text', __( 'More Details &raquo;', 'novelist' ) ); ?></a>
</div>
