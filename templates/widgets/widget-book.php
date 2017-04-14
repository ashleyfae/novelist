<?php
/**
 * Template for displaying the Novelist Book Widget.
 *
 * Widget settings are contained within the $instance array.
 *
 * @package   novelist
 * @copyright Copyright (c) 2016, Nose Graze Ltd.
 * @license   GPL2+
 */

/*
 * Display 'before' text.
 */
if ( $instance['text_before'] ) {
	echo wpautop( $instance['text_before'] );
}

// If there's no book selected, bail.
if ( empty( $instance['book'] ) ) {
	return;
}

/*
 * Display the book cover with a link to the single book page.
 */
$book_cover_id = get_post_meta( $instance['book'], 'novelist_cover', true );
if ( ! empty( $book_cover_id ) ) {
	$size = $instance['book_cover_size'];
	echo '<a href="' . esc_url( get_permalink( $instance['book'] ) ) . '">' . wp_get_attachment_image( $book_cover_id, $this->get_chosen_size( $instance ), false, array( 'class' => sanitize_html_class( $instance['book_cover_align'] ) ) ) . '</a>';
}

/*
 * Display the synopsis.
 */
if ( ! empty( $instance['show_synopsis'] ) ) {
	echo apply_filters( 'novelist/widget/book/before-synopsis-tag', '<blockquote class="novelist-book-widget-synopsis">' );
	echo get_post_meta( $instance['book'], 'novelist_synopsis', true );
	echo apply_filters( 'novelist/widget/book/after-synopsis-tag', '</blockquote>' );
}

/*
 * Display a link to the book page.
 */
if ( ! empty( $instance['show_link'] ) ) {
	$link_text = ! empty( $instance['link_text'] ) ? $instance['link_text'] : __( 'More Information', 'novelist' );
	echo apply_filters( 'novelist/widget/book/link-to-book', '<p class="novelist-book-widget-link-to-book"><a href="' . esc_url( get_permalink( $instance['book'] ) ) . '" class="novelist-button">' . $link_text . '</a></p>' );
}

/*
 * Display 'after' text.
 */
if ( $instance['text_after'] ) {
	echo wpautop( $instance['text_after'] );
}