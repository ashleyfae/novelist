<?php
/**
 * Displays the book cover image.
 *
 * @package   novelist
 * @copyright Copyright (c) 2016, Nose Graze Ltd.
 * @license   GPL2+
 */

$cover_id = get_post_meta( get_the_ID(), 'novelist_cover', true );

// If there's no image uploaded, try the default.
if ( empty( $cover_id ) || ! is_numeric( $cover_id ) ) {
	$cover_id = novelist_get_option( 'default_cover_image', false );
	if ( empty( $cover_id ) ) {
		return;
	}
}

$cover_size = apply_filters( 'novelist/shortcode/book/cover-image-size', array( 315, 375 ) );
?>
<div class="novelist-book-cover-image">
	<a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>">
		<?php echo wp_get_attachment_image( $cover_id, $cover_size ); ?>
	</a>
</div>
