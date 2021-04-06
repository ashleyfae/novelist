<?php

/**
 * Registers all Novelist shortcodes with WordPress.
 *
 * @package   novelist
 * @copyright Copyright (c) 2016, Nose Graze Ltd.
 * @license   GPL2+
 */
class Novelist_Shortcodes {

	/**
	 * Novelist_Shortcodes constructor.
	 *
	 * @access public
	 * @since  1.0.0
	 * @return void
	 */
	public function __construct() {

		add_shortcode( 'novelist-books', array( $this, 'books' ) );
		add_shortcode( 'books-in', array( $this, 'books' ) ); //@deprecated
		add_shortcode( 'novelist-series-grid', array( $this, 'series_grid' ) );
		add_shortcode( 'show-book', array( $this, 'show_book' ) );

	}

	/**
	 * Books Grid
	 *
	 * Displays a grid of books.
	 *
	 * @param array $atts    Shortcode attributes
	 * @param null  $content Content
	 *
	 * @since 1.0.0
	 * @return string
	 */
	public function books( $atts, $content = null ) {

		$atts = shortcode_atts( array(
			'series'        => '',
			// Comma-separated list of series IDs or names to display results from. Or "none" to only get standalones.
			'genre'         => '',
			// Comma-separated list of genre IDs or names to display results from.
			'relation'      => 'OR',
			// Relation between taxonomy term queries.
			'columns'       => 4,
			// Number of columns in each row.
			'title'         => 'false',
			// Whether or not to display the title.
			'series-number' => 'false',
			// Whether or not to display the number in the series.
			'excerpt'       => 'false',
			// Excerpt from the synopsis.
			'full-content'  => 'false',
			// Fully formatted book content.
			'full-synopsis' => 'false',
			// Fully synopsis
			'button'        => 'true',
			// Button to single book page. Text in here becomes the button text.
			'covers'        => 'true',
			// Whether or not to display book cover.
			'covers-align'  => 'center',
			// Cover alignment.
			'orderby'       => 'menu_order',
			// How to order the results. Options: `name`, `title`, `random`, `publication`, `date`, `menu_order`, `series_number`
			'order'         => 'ASC',
			// Order of the results.
			'pagination'    => 'true',
			// Whether or not to include pagination.
			'number'        => '12',
			// Number of results per page.
			'offset'        => 0,
			// Number of books to skip
			'display'       => '',
			'ids'           => ''
			// Specific book IDs
		), $atts, 'novelist-books' );

		if ( ! empty( $atts['ids'] ) ) {

			$book_ids = explode( ',', $atts['ids'] );
			$book_ids = array_map( 'absint', $book_ids );

			$args = array(
				'post_type'      => 'book',
				'posts_per_page' => - 1,
				'post__in'       => $book_ids
			);

		} else {

			/*
			 * Start building our query args.
			 */
			$args = array(
				'post_type'  => 'book',
				'orderby'    => $atts['orderby'],
				'order'      => $atts['order'],
				'meta_query' => array(
					array(
						'key'     => 'novelist_hide',
						'compare' => 'NOT EXISTS'
					)
				)
			);

			/*
			 * Add in offset args.
			 */
			if ( $atts['offset'] > 0 ) {
				$args['offset'] = absint( $atts['offset'] );
			}

			/*
			 * Catch out invalid orderby dates.
			 */
			switch ( $atts['orderby'] ) {

				case 'name' :
					// nothing here intentionally
					// use below value

				case 'title' :
					$args['orderby'] = 'title';
					break;

				case 'rand' :
					// nothing here intentionally
					// use below value

				case 'random' :
					$args['orderby'] = 'rand';
					break;

				case 'publication' :
					$args['orderby']  = 'meta_value_num';
					$args['meta_key'] = 'novelist_pub_date_timestamp';
					break;

				case 'series_number' :
					$args['orderby']  = 'meta_value_num';
					$args['meta_key'] = 'novelist_series';
					break;

				case 'date' :
					$args['orderby'] = 'date';
					break;

				default :
					$args['orderby'] = 'menu_order title';
					break;

			}

			/*
			 * Add taxonomy relation.
			 */
			if ( $atts['series'] || $atts['genre'] ) {
				$args['tax_query'] = array(
					'relation' => $atts['relation']
				);
			}

			/*
			 * Filter by series.
			 */
			if ( $atts['series'] ) {
				if ( $atts['series'] == 'none' ) {
					$get_terms_args = array(
						'taxonomy'   => 'novelist-series',
						'hide_empty' => false,
						'fields'     => 'ids'
					);
					$all_series     = get_terms( $get_terms_args );

					$args['tax_query'][] = array(
						'taxonomy' => 'novelist-series',
						'field'    => 'term_id',
						'terms'    => $all_series,
						'operator' => 'NOT IN'
					);

					// We need to unset this to prevent funky errors.
					if ( array_key_exists( 'meta_key', $args ) && $args['meta_key'] == 'novelist_series' ) {
						unset( $args['meta_key'] );
						$args['orderby'] = 'title';
					}

				} else {

					$series_names = explode( ',', $atts['series'] );

					foreach ( $series_names as $name ) {
						if ( is_numeric( $name ) ) {
							$term_id = $name;
						} else {
							$term = get_term_by( 'name', trim( $name ), 'novelist-series' );

							if ( ! $term ) {
								continue;
							}

							$term_id = $term->term_id;
						}

						$args['tax_query'][] = array(
							'taxonomy' => 'novelist-series',
							'field'    => 'term_id',
							'terms'    => $term_id
						);
					}

				}
			}

			/*
			 * Filter by genre.
			 */
			if ( $atts['genre'] ) {
				$genre_names = explode( ',', $atts['genre'] );

				foreach ( $genre_names as $name ) {
					if ( is_numeric( $name ) ) {
						$term_id = $name;
					} else {
						$term = get_term_by( 'name', trim( $name ), 'novelist-genre' );

						if ( ! $term ) {
							continue;
						}

						$term_id = $term->term_id;
					}

					$args['tax_query'][] = array(
						'taxonomy' => 'novelist-genre',
						'field'    => 'term_id',
						'terms'    => $term_id
					);
				}
			}

		}

		/*
		 * Add in pagination args.
		 */
		if ( filter_var( $atts['pagination'], FILTER_VALIDATE_BOOLEAN ) || ( ! filter_var( $atts['pagination'], FILTER_VALIDATE_BOOLEAN ) && $atts['number'] > 0 ) ) {
			$args['posts_per_page'] = (int) $atts['number'];
			if ( $args['posts_per_page'] < 0 ) {
				$args['posts_per_page'] = abs( $args['posts_per_page'] );
			}
		} else {
			$args['nopaging']      = true;
			$args['no_found_rows'] = true;
		}

		/*
		 * Add pagination.
		 */
		if ( get_query_var( 'paged' ) ) {
			$args['paged'] = get_query_var( 'paged' );
		} elseif ( get_query_var( 'page' ) ) {
			$args['paged'] = get_query_var( 'page' );
		} else {
			$args['paged'] = 1;
		}

		/*
		 * Let other plugins manipulate these arguments.
		 */
		$args = apply_filters( 'novelist/shortcode/book/query-args', $args, $atts );

		$books = new WP_Query( $args );

		if ( $books->have_posts() ) {

			$class = 'novelist-book-columns-' . intval( $atts['columns'] );
			ob_start(); ?>
			<div class="novelist-book-list <?php echo apply_filters( 'novelist/shortcode/book/wrapper-class', $class, $atts ); ?>">
				<?php while ( $books->have_posts() ) : $books->the_post(); ?>
					<div id="novelist-book-<?php the_ID(); ?>" class="novelist-book novelist-covers-align-<?php echo esc_attr( sanitize_html_class( $atts['covers-align'] ) ); ?>" itemscope itemtype="http://schema.org/Book">
						<?php
						do_action( 'novelist/shortcode/book/content-before', get_the_ID(), $atts );

						/*
						 * Display the cover image.
						 */
						if ( $atts['covers'] != 'false' && $atts['full-content'] == 'false' ) {
							novelist_get_template_part( 'shortcode', 'book-content-image' );
							do_action( 'novelist/shortcode/book/after-cover', get_the_ID(), $atts );
						}

						/*
						 * Display the book title.
						 */
						if ( $atts['title'] != 'false' ) {
							novelist_get_template_part( 'shortcode', 'book-content-title' );
							do_action( 'novelist/shortcode/book/after-title', get_the_ID(), $atts );
						}

						/*
						 * Display the series number.
						 */
						if ( $atts['series-number'] != 'false' ) {
							novelist_get_template_part( 'shortcode', 'book-content-series-number' );
							do_action( 'novelist/shortcode/book/after-series-number', get_the_ID(), $atts );
						}

						/*
						 * Display the full content or excerpt.
						 */
						if ( $atts['excerpt'] != 'false' ) {
							novelist_get_template_part( 'shortcode', 'book-content-excerpt' );
							do_action( 'novelist/shortcode/book/after-excerpt', get_the_ID(), $atts );
						} elseif ( $atts['full-synopsis'] != 'false' ) {
							novelist_get_template_part( 'shortcode', 'book-content-synopsis' );
							do_action( 'novelist/shortcode/book/after-synopsis', get_the_ID(), $atts );
						} elseif ( $atts['full-content'] != 'false' ) {
							novelist_get_template_part( 'shortcode', 'book-content-full' );
							do_action( 'novelist/shortcode/book/after-full-content', get_the_ID(), $atts );
						}

						/*
						 * Display the 'more info' button.
						 */
						if ( $atts['button'] == 'true' ) {
							novelist_get_template_part( 'shortcode', 'book-content-button' );
							do_action( 'novelist/shortcode/book/after-button', get_the_ID(), $atts );
						}

						/*
						 * Extra stuff.
						 */
						if ( $atts['display'] ) {
							$extra_content = explode( ',', $atts['display'] );

							if ( count( $extra_content ) ) {
								foreach ( $extra_content as $content ) {
									do_action( 'novelist/shortcode/book/layout-display', trim( $content ), get_the_ID(), $atts );
								}
							}
						}

						do_action( 'novelist/shortcode/book/content-after', get_the_ID(), $atts );
						?>
					</div>
				<?php endwhile; ?>
			</div>

			<?php
			if ( filter_var( $atts['pagination'], FILTER_VALIDATE_BOOLEAN ) ) {
				if ( is_single() ) {
					$pagination = paginate_links( apply_filters( 'novelist/shortcode/book/pagination-args', array(
						'base'    => get_permalink() . '%#%',
						'format'  => '?paged=%#%',
						'current' => max( 1, $args['paged'] ),
						'total'   => $books->max_num_pages
					), $atts, $books, $args ) );
				} else {
					$big          = 999999;
					$search_for   = array( $big, '#038;' );
					$replace_with = array( '%#%', '&' );
					$pagination   = paginate_links( apply_filters( 'novelist/shortcode/book/pagination-args', array(
						'base'    => str_replace( $search_for, $replace_with, get_pagenum_link( $big ) ),
						'format'  => '?paged=%#%',
						'current' => max( 1, $args['paged'] ),
						'total'   => $books->max_num_pages
					), $atts, $books, $args ) );
				}

				if ( ! empty( $pagination ) ) {
					?>
					<div id="novelist-book-pagination" class="pagination">
						<?php echo $pagination; ?>
					</div>
					<?php
				}
			}

			wp_reset_postdata();

			$display = ob_get_clean();

		} else {
			$display = sprintf( _x( 'No %s found', 'book post type name', 'novelist' ), novelist_get_label_plural() );
			$display = '';
		}

		/*
		 * Return display, but let plugins manipulate it.
		 */

		return apply_filters( 'novelist/shortcode/book/display', $display, $atts, $args, $books );

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
	 * @param array $atts    Shortcode attributes
	 * @param null  $content Content
	 *
	 * @since 1.0.4
	 * @return string
	 */
	public function series_grid( $atts, $content = null ) {

		$atts = shortcode_atts( array(
			'series-name'   => 'true',
			// Whether or not to show the name of the series before the grid.
			'exclude'       => '',
			// Comma-separated list of series IDs or names to exclude.
			'standalones'   => 'true',
			// Whether or not to show standalones at the end.
			'columns'       => 4,
			// Number of columns in each row.
			'title'         => 'false',
			// Whether or not to display the title.
			'series-number' => 'false',
			// Whether or not to display the number in the series.
			'excerpt'       => 'false',
			// Excerpt from the synopsis.
			'full-content'  => 'false',
			// Fully formatted book content.
			'full-synopsis' => 'false',
			// Fully synopsis
			'button'        => 'true',
			// Button to single book page. Text in here becomes the button text.
			'covers'        => 'true',
			// Whether or not to display book cover.
			'covers-align'  => 'center',
			// Cover alignment.
			'orderby'       => 'series_number',
			// How to order the results. Options: `name`, `title`, `random`, `publication`, `date`, `menu_order`, `series_number`
			'order'         => 'ASC',
			// Order of the results.
			'offset'        => 0
			// Number of books to skip
		), $atts, 'novelist-series-grid' );

		// No pagination because WTF.
		$atts['pagination'] = 'false';
		$atts['number']     = - 1;

		$series_args  = array(
			'taxonomy' => 'novelist-series',
		);
		$excluded_ids = array();

		$excluded_series = explode( ',', $atts['exclude'] );

		foreach ( $excluded_series as $name ) {
			if ( is_numeric( $name ) ) {
				$excluded_ids[] = $name;
			} else {
				$term = get_term_by( 'name', trim( $name ), 'novelist-series' );

				if ( ! $term ) {
					continue;
				}

				$excluded_ids[] = $term->term_id;
			}
		}

		if ( count( $excluded_ids ) ) {
			$series_args['exclude'] = $excluded_ids;
		}

		$all_series = get_terms( apply_filters( 'novelist/shortcode/series-grid/get-terms-args', $series_args ) );
		$output     = '';

		/*
		 * Display each series.
		 */
		if ( $all_series && is_array( $all_series ) ) {

			foreach ( $all_series as $series ) {

				// Manually add some attributes.
				$this_series_atts           = $atts;
				$this_series_atts['series'] = $series->term_id;

				$output .= '<div class="novelist-series-grid">';

				// Display the series name
				if ( filter_var( $atts['series-name'], FILTER_VALIDATE_BOOLEAN ) ) {
					$output .= apply_filters( 'novelist/shortcode/series-grid/series-name', '<h2 id="series-' . esc_attr( $series->slug ) . '">' . esc_html( $series->name ) . '</h2>' );
				}

				// Main grid shortcode.
				$output .= $this->books( $this_series_atts );

				$output .= '</div>';

			}

		}

		/*
		 * Display standalones.
		 */
		if ( filter_var( $atts['standalones'], FILTER_VALIDATE_BOOLEAN ) ) {
			$this_series_atts           = $atts;
			$this_series_atts['series'] = 'none';
			$standalone_grid            = $this->books( $this_series_atts );

			if ( ! empty( $standalone_grid ) ) {
				$output .= '<div class="novelist-series-grid">';

				// Display the series name
				if ( filter_var( $atts['series-name'], FILTER_VALIDATE_BOOLEAN ) ) {
					$output .= apply_filters( 'novelist/shortcode/series-grid/standalone-series-name', '<h2>' . __( 'Standalones', 'novelist' ) . '</h2>' );
				}

				// Main grid shortcode.
				$output .= $standalone_grid;

				$output .= '</div>';
			}
		}

		return $output;

	}

	/**
	 * Show Book
	 *
	 * Displays the fully rendered book information for a single book.
	 *
	 * @param array $atts    Shortcode attributes
	 * @param null  $content Content
	 *
	 * @access public
	 * @since  1.0.0
	 * @return string
	 */
	public function show_book( $atts, $content = null ) {

		$atts = shortcode_atts( array(
			'title' => null, // Title of the book
			'id'    => null, // ID of the book
		), $atts, 'show-book' );

		$args = array(
			'post_type'      => 'book',
			'posts_per_page' => 1,
		);

		if ( ! empty( $atts['id'] ) ) {
			$args['p'] = intval( $atts['id'] );
		} elseif ( ! empty( $atts['title'] ) ) {
			$args['s'] = strip_tags( $atts['title'] );
		}

		$book_query = new WP_Query( apply_filters( 'novelist/shortcode/show-book/query-args', $args ) );

		// No results - bail.
		if ( ! $book_query->have_posts() ) {
			return __( '[show-book] Error: No book found with this title.', 'novelist' );
		}

		ob_start();

		while ( $book_query->have_posts() ) : $book_query->the_post();

			do_action( 'novelist/shortcode/show-book/before-content', get_the_ID(), $atts );

			novelist_get_template_part( 'book', 'content' );

			do_action( 'novelist/shortcode/show-book/after-content', get_the_ID(), $atts );

		endwhile;

		wp_reset_postdata();

		return ob_get_clean();

	}

}

new Novelist_Shortcodes();
