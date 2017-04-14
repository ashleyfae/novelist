<?php
/**
 * Book excerpt
 *
 * @package   novelist
 * @copyright Copyright (c) 2016, Nose Graze Ltd.
 * @license   GPL2+
 */

$excerpt_length = apply_filters( 'novelist/shortcode/book/excerpt-length', 30 ); ?>

<?php if ( has_excerpt() ) : ?>
	<div itemprop="description" class="novelist-book-excerpt">
		<?php echo apply_filters( 'novelist/shortcode/book/excerpt', wp_trim_words( get_post_field( 'post_excerpt', get_the_ID() ), $excerpt_length ) ); ?>
	</div>
<?php elseif ( $excerpt = get_post_meta( get_the_ID(), 'novelist_synopsis', true ) ) : ?>
	<div itemprop="description" class="novelist-book-excerpt">
		<?php echo apply_filters( 'novelist/shortcode/book/excerpt', wp_trim_words( $excerpt, $excerpt_length ), get_the_ID(), $excerpt_length ); ?>
	</div>
<?php endif; ?>