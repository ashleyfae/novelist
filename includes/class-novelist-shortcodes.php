<?php

/**
 * Registers all Novelist shortcodes with WordPress.
 *
 * @deprecated 2.0
 *
 * @package   novelist
 * @copyright Copyright (c) 2021, Ashley Gibson
 * @license   GPL2+
 */
class Novelist_Shortcodes {

	/**
	 * Books Grid
	 *
	 * Displays a grid of books.
     *
     * @deprecated 2.0
	 *
	 * @param array $atts    Shortcode attributes
	 * @param null  $content Content
	 *
	 * @since 1.0.0
	 * @return string
	 */
	public function books( $atts, $content = null ) {
        _deprecated_function(__CLASS__.'::'.__METHOD__, '2.0', \Novelist\Shortcodes\BookGridShortcode::class);

        return novelist(\Novelist\Shortcodes\BookGridShortcode::class)->make($atts, $content);
	}

	/**
	 * Series Grid
	 *
	 * Displays a grid of books, grouped by series. This shortcode actually calls the
	 * `[novelist-books]` shortcode and uses most of the same attributes.
	 *
	 * Attributes unique to this shortcode are:
	 *      `series-name`
	 *      `series-exclude`
	 *      `standalones`
     *
     * @deprecated 2.0
	 *
	 * @param array $atts    Shortcode attributes
	 * @param null  $content Content
	 *
	 * @since 1.0.4
	 * @return string
	 */
	public function series_grid( $atts, $content = null ) {
        _deprecated_function(__CLASS__.'::'.__METHOD__, '2.0', \Novelist\Shortcodes\SeriesGridShortcode::class);

        return novelist(\Novelist\Shortcodes\SeriesGridShortcode::class)->make($atts, $content);
	}

	/**
	 * Show Book
	 *
	 * Displays the fully rendered book information for a single book.
     *
     * @deprecated 2.0
	 *
	 * @param array $atts    Shortcode attributes
	 * @param null  $content Content
	 *
	 * @access public
	 * @since  1.0.0
	 * @return string
	 */
	public function show_book( $atts, $content = null ) {
        _deprecated_function(__CLASS__.'::'.__METHOD__, '2.0', \Novelist\Shortcodes\ShowBookShortcode::class);

        return novelist(\Novelist\Shortcodes\ShowBookShortcode::class)->make($atts, $content);
	}

}
