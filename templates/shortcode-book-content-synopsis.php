<?php
/**
 * Full synopsis of the book.
 *
 * @package   novelist
 * @copyright Copyright (c) 2016, Nose Graze Ltd.
 * @license   GPL2+
 */

$synopsis = get_post_meta( get_the_ID(), 'novelist_synopsis', true ); ?>

<?php if ( $synopsis ) : ?>
	<div itemprop="description" class="novelist-book-synopsis">
		<?php echo apply_filters( 'novelist/shortcode/book/synopsis', $synopsis, get_the_ID(), $excerpt_length ); ?>
	</div>
<?php endif; ?>