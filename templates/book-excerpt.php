<?php
/**
 * Template for displaying the book excerpt. This is used on archive pages.
 *
 * @package   novelist
 * @copyright Copyright (c) 2016, Nose Graze Ltd.
 * @license   GPL2+
 */

$book  = new Novelist_Book( get_the_ID() );
$cover = $book->get_cover_image( array( 150, 225 ), 'alignleft' );

/*
 * Display the book cover with a link to the book.
 */
if ( $cover ) {
	echo '<a href="' . esc_url( get_permalink() ) . '">' . $cover . '</a>';
}

/*
 * Display the formatted synopsis.
 */
$book->render( 'synopsis' );