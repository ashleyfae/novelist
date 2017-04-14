<?php
/**
 * Display the full content of the book.
 * This template is nearly the same as 'book-content.php'
 *
 * @package   novelist
 * @copyright Copyright (c) 2016, Nose Graze Ltd.
 * @license   GPL2+
 */

$enabled_fields = novelist_get_option( 'book_layout', array() );
$book           = new Novelist_Book( get_the_ID(), $enabled_fields );

foreach ( $enabled_fields as $key => $value ) :
	$book->render( $key );
endforeach;