<?php
/**
 * Template for displaying the book information on the single book page.
 *
 * @package   novelist
 * @copyright Copyright (c) 2016, Nose Graze Ltd.
 * @license   GPL2+
 */

$enabled_fields = novelist_get_option( 'book_layout', novelist_get_default_book_field_values() );
$book           = new Novelist_Book( get_the_ID() );
?>
<div itemscope itemtype="http://schema.org/Book">
	<?php foreach ( $enabled_fields as $key => $value ) :
		$book->render( $key, $enabled_fields );
	endforeach; ?>
</div>