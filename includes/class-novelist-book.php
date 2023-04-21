<?php

/**
 * Novelist Book
 *
 * @package   novelist
 * @copyright Copyright (c) 2016, Nose Graze Ltd.
 * @license   GPL2+
 */
class Novelist_Book {

	/**
	 * The book ID
	 *
	 * @access public
	 * @since  1.0.0
	 */
	public $ID = 0;

	/**
	 * Title of the book
	 *
	 * @var string
	 * @access private
	 * @since  1.0.0
	 */
	private $title;

	/**
	 * ID of the attachment for the cover image
	 *
	 * @var int
	 * @access private
	 * @since  1.0.0
	 */
	private $cover_ID;

	/**
	 * Name of the book series
	 *
	 * @var string
	 * @access private
	 * @since  1.0.0
	 */
	private $series_name;

	/**
	 * Position in the series
	 *
	 * @var string
	 * @access private
	 * @since  1.0.0
	 */
	private $series_number;

	/**
	 * Name of the publisher
	 *
	 * @var string
	 * @access private
	 * @since  1.0.0
	 */
	private $publisher;

	/**
	 * Publication date of the book
	 *
	 * @var string
	 * @access private
	 * @since  1.0.0
	 */
	private $pub_date;

	/**
	 * Extra contributors
	 *
	 * @var string
	 * @access private
	 * @since  1.0.0
	 */
	private $contributors;

	/**
	 * Comma-separate list of genres
	 *
	 * @var string
	 * @access private
	 * @since  1.0.0
	 */
	private $genre;

	/**
	 * Number of pages in the book
	 *
	 * @var int
	 * @access private
	 * @since  1.0.0
	 */
	private $pages;

	/**
	 * ISBN13
	 *
	 * @var string
	 * @access private
	 * @since  1.0.0
	 */
	private $isbn13;

	/**
	 * ASIN
	 *
	 * @var string
	 * @access private
	 * @since  1.0.0
	 */
	private $asin;

	/**
	 * Synopsis
	 *
	 * @var string
	 * @access private
	 * @since  1.0.0
	 */
	private $synopsis;

	/**
	 * Raw URL to the Goodreads page
	 *
	 * @var string
	 * @access private
	 * @since  1.0.0
	 */
	private $goodreads_link;

	/**
	 * Array of raw purchase link URLs
	 *
	 * @var array
	 * @access private
	 * @since  1.0.0
	 */
	private $purchase_links;

	/**
	 * WP_Query for other books in the series
	 *
	 * @var WP_Query
	 * @access private
	 * @since  1.0.0
	 */
	private $series_books;

	/**
	 * Excerpt of the book
	 *
	 * @var string
	 * @access private
	 * @since  1.0.0
	 */
	private $excerpt;

	/**
	 * Extra Text
	 *
	 * @var string
	 * @access private
	 * @since  1.0.0
	 */
	private $extra_text;

	/**
	 * Declare the default properities in WP_Post as we can't extend it
	 * Anything we've delcared above has been removed.
	 */
	public $post_author = 0;
	public $post_date = '0000-00-00 00:00:00';
	public $post_date_gmt = '0000-00-00 00:00:00';
	public $post_content = '';
	public $post_title = '';
	public $post_excerpt = '';
	public $post_status = 'publish';
	public $comment_status = 'open';
	public $ping_status = 'open';
	public $post_password = '';
	public $post_name = '';
	public $to_ping = '';
	public $pinged = '';
	public $post_modified = '0000-00-00 00:00:00';
	public $post_modified_gmt = '0000-00-00 00:00:00';
	public $post_content_filtered = '';
	public $post_parent = 0;
	public $guid = '';
	public $menu_order = 0;
	public $post_mime_type = '';
	public $comment_count = 0;
	public $filter;

	/**
	 * Constructor
	 *
	 * @param int|bool $_id Post ID of the book to get
	 *
	 * @access public
	 * @since  1.0.0
	 * @return bool Whether or not the book was successfully set up
	 */
	public function __construct( $_id = false ) {

		$book = WP_Post::get_instance( $_id );

		return $this->setup_book( $book );

	}

	/**
	 * Set the variables
	 *
	 * @param  WP_Post $book The post object
	 *
	 * @access private
	 * @since  1.0.0
	 * @return bool If the setup was successful or not
	 */
	private function setup_book( $book ) {

		if ( ! is_object( $book ) ) {
			return false;
		}

		if ( ! is_a( $book, 'WP_Post' ) ) {
			return false;
		}

		if ( 'book' !== $book->post_type ) {
			return false;
		}

		foreach ( $book as $key => $value ) {

			switch ( $key ) {

				default:
					$this->$key = $value;
					break;

			}

		}

		return true;

	}

	/**
	 * Magic __get function to dispatch a call to retrieve a private property
	 *
	 * @access public
	 * @since  1.0.0
	 * @return mixed
	 */
	public function __get( $key ) {

		if ( method_exists( $this, 'get_' . $key ) ) {

			return call_user_func( array( $this, 'get_' . $key ) );

		} else {

			return new WP_Error( 'novelist-book-invalid-property', sprintf( __( 'Can\'t get property %s', 'novelist' ), $key ) );

		}

	}

	/**
	 * Get ID
	 *
	 * @access public
	 * @since  1.0.0
	 * @return int
	 */
	public function get_ID() {

		return $this->ID;

	}

	/**
	 * Retrieve the name of the book, as entered in the 'post title'
	 * box. This is for internal/admin reference only.
	 *
	 * @access public
	 * @since  1.0.0
	 * @return string
	 */
	public function get_name() {

		return get_the_title( $this->ID );

	}

	/**
	 * Get Title
	 *
	 * @access public
	 * @since  1.0.0
	 * @return string
	 */
	public function get_title() {

		if ( ! isset( $this->title ) ) {
			$this->title = get_post_meta( $this->ID, 'novelist_title', true );

			if ( empty( $this->title ) && $this->post_status == 'publish' ) {
				$this->title = $this->post_title;
			}
		}

		return apply_filters( 'novelist/book/get/title', $this->title, $this->ID );

	}

	/**
	 * Get Cover ID
	 *
	 * @access public
	 * @since  1.0.0
	 * @return int|bool False if there's no image
	 */
	public function get_cover_ID() {

		if ( ! isset( $this->cover_ID ) ) {
			$this->cover_ID = get_post_meta( $this->ID, 'novelist_cover', true );

			if ( ! is_numeric( $this->cover_ID ) ) {
				$this->cover_ID = false;
			}
		}

		return apply_filters( 'novelist/book/get/cover_ID', $this->cover_ID, $this->ID );

	}

	/**
	 * Get Cover Image
	 *
	 * Returns an HTML formatted image.
	 *
	 * @param string $size  Desired size (thumbnail|medium|large|full)
	 * @param string $class Class name(s) to add to the <img> tag
	 * @param bool   $link  Whether or not to link the cover according to the settings
	 *
	 * @access public
	 * @since  1.0.0
	 * @return string|bool False on failure
	 */
	public function get_cover_image( $size = 'full', $class = '', $link = false ) {

		$cover_ID = $this->get_cover_ID();
		$cover    = ! empty( $cover_ID ) ? wp_get_attachment_image( intval( $cover_ID ), $size, false, array( 'class' => 'novelist-cover-image ' . $class ) ) : novelist_get_default_cover( $size, $class );
		$link_url = '';

		if ( $link === true ) {
			$link_location = novelist_get_option( 'link_book_cover', 'none' );

			switch ( $link_location ) {

				case 'goodreads' :
					$goodreads_url = $this->get_goodreads_link();
					if ( ! empty( $goodreads_url ) ) {
						$link_url = $goodreads_url;
						$cover    = '<a href="' . esc_url( $goodreads_url ) . '" target="_blank">' . $cover . '</a>';
					}
					break;

				case 'none' :
					// intentionally nothing here
					break;

				default :
					$purchase_links = $this->get_purchase_links();
					if ( array_key_exists( $link_location, $purchase_links ) && ! empty( $purchase_links[ $link_location ] ) ) {
						$link_url = $purchase_links[ $link_location ];
						$cover    = '<a href="' . esc_url( $purchase_links[ $link_location ] ) . '" target="_blank">' . $cover . '</a>';
					}
					break;

			}
		}

		return apply_filters( 'novelist/book/get/cover_image', $cover, $cover_ID, $size, $class, $link_url, $this->ID );

	}

	/**
	 * Get Series Name
	 *
	 * Combines the name of the series with the number.
	 *
	 * @access public
	 * @since  1.0.0
	 * @return string
	 */
	public function get_series_name() {

		if ( ! isset( $this->series_name ) ) {
			$this->series_name = novelist_get_formatted_series( $this->ID, true, $this->get_series_number() );
		}

		return apply_filters( 'novelist/book/get/series_name', $this->series_name, $this->ID );

	}

	/**
	 * Get Series Number
	 *
	 * @access public
	 * @since  1.0.0
	 * @return string
	 */
	public function get_series_number() {

		if ( ! isset( $this->series_number ) ) {
			$this->series_number = get_post_meta( $this->ID, 'novelist_series', true );
		}

		return apply_filters( 'novelist/book/get/series_number', $this->series_number, $this->ID );

	}

	/**
	 * Get Publisher
	 *
	 * @access public
	 * @since  1.0.0
	 * @return string
	 */
	public function get_publisher() {

		if ( ! isset( $this->publisher ) ) {
			$this->publisher = get_post_meta( $this->ID, 'novelist_publisher', true );
		}

		return apply_filters( 'novelist/book/get/publisher', $this->publisher, $this->ID );

	}

	/**
	 * Get Publication Date
	 *
	 * @access public
	 * @since  1.0.0
	 * @return string
	 */
	public function get_pub_date() {

		if ( ! isset( $this->pub_date ) ) {
			$this->pub_date = get_post_meta( $this->ID, 'novelist_pub_date', true );
		}

		return apply_filters( 'novelist/book/get/pub_date', $this->pub_date, $this->ID );

	}

	/**
	 * Get Contributors
	 *
	 * @access public
	 * @since  1.0.0
	 * @return string
	 */
	public function get_contributors() {

		if ( ! isset( $this->contributors ) ) {
			$this->contributors = get_post_meta( $this->ID, 'novelist_contributors', true );
		}

		return apply_filters( 'novelist/book/get/contributors', $this->contributors, $this->ID );

	}

	/**
	 * Get Number of Pages
	 *
	 * @access public
	 * @since  1.0.0
	 * @return string
	 */
	public function get_pages() {

		if ( ! isset( $this->pages ) ) {
			$this->pages = get_post_meta( $this->ID, 'novelist_pages', true );

			if ( ! is_numeric( $this->pages ) ) {
				$this->pages = 0;
			}
		}

		return apply_filters( 'novelist/book/get/pages', $this->pages, $this->ID );

	}

	/**
	 * Get Genre List
	 *
	 * @access public
	 * @since  1.0.0
	 * @return string
	 */
	public function get_genre() {

		if ( ! isset( $this->genre ) ) {
			$this->genre = novelist_get_taxonomy_term_list($this->ID, 'novelist-genre');
		}

		return apply_filters( 'novelist/book/get/genre', $this->genre, $this->ID );

	}

	/**
	 * Get ISBN13
	 *
	 * @access public
	 * @since  1.0.0
	 * @return string
	 */
	public function get_isbn13() {

		if ( ! isset( $this->isbn13 ) ) {
			$this->isbn13 = get_post_meta( $this->ID, 'novelist_isbn', true );
		}

		return apply_filters( 'novelist/book/get/isbn13', $this->isbn13, $this->ID );

	}

	/**
	 * Get ASIN
	 *
	 * @access public
	 * @since  1.0.0
	 * @return string
	 */
	public function get_asin() {

		if ( ! isset( $this->asin ) ) {
			$this->asin = get_post_meta( $this->ID, 'novelist_asin', true );
		}

		return apply_filters( 'novelist/book/get/asin', $this->asin, $this->ID );

	}

	/**
	 * Get Goodreads URL
	 *
	 * @access public
	 * @since  1.0.0
	 * @return string
	 */
	public function get_goodreads_link() {

		if ( ! isset( $this->goodreads_link ) ) {
			$this->goodreads_link = get_post_meta( $this->ID, 'novelist_goodreads', true );
		}

		return apply_filters( 'novelist/book/get/goodreads_link', $this->goodreads_link, $this->ID );

	}

	/**
	 * Get Purchase Links
	 *
	 * @access public
	 * @since  1.0.0
	 * @return array
	 */
	public function get_purchase_links() {

		if ( ! isset( $this->purchase_links ) ) {
			$this->purchase_links = get_post_meta( $this->ID, 'novelist_purchase_links', true );

			if ( ! is_array( $this->purchase_links ) ) {
				$this->purchase_links = array();
			}
		}

		return apply_filters( 'novelist/book/get/purchase_links', $this->purchase_links, $this->ID );

	}

	/**
	 * Get Other Books in the Series
	 *
	 * @access public
	 * @since  1.0.0
	 * @return WP_Query
	 */
	public function get_series_books() {

		if ( ! isset( $this->series_books ) ) {
			$series = wp_get_post_terms( $this->ID, 'novelist-series', array( 'fields' => 'ids' ) );

			if ( is_array( $series ) && count( $series ) ) {
				$query_args = array(
					'post__not_in'   => array( $this->ID ),
					'post_type'      => 'book',
					'posts_per_page' => 200,
					'orderby'        => 'meta_value_num',
					'order'          => 'ASC',
					'meta_key'       => 'novelist_series',
					'tax_query'      => array(
						array(
							'taxonomy' => 'novelist-series',
							'terms'    => $series
						)
					)
				);

				$series_query       = new WP_Query( apply_filters( 'novelist/book/series-books-query-args', $query_args ) );
				$this->series_books = $series_query;
			}
		}

		return apply_filters( 'novelist/book/get/series_books', $this->series_books, $this->ID );

	}

	/**
	 * Get Purchase Links List
	 *
	 * Returns a list (ul or ol) of purchase links.
	 *
	 * @param string $list_type Type of list to use (ul or ol)
	 *
	 * @access public
	 * @since  1.0.0
	 * @return string|bool False if no links are set
	 */
	public function get_purchase_links_list( $list_type = 'ul' ) {

		if ( ! isset( $this->purchase_links ) ) {
			$this->purchase_links = $this->get_purchase_links();
		}

		if ( ! is_array( $this->purchase_links ) || ! count( $this->purchase_links ) ) {
			return false;
		}

		$list        = '';
		$saved_links = novelist_get_option( 'purchase_links', false );

		if ( ! is_array( $saved_links ) ) {
			return false;
		}

		if ( ! empty( $list_type ) ) {
			$list .= '<' . esc_attr( $list_type ) . '>';
		}

		foreach ( $saved_links as $link_info ) {
			$sanitized_key = ( array_key_exists( 'id', $link_info ) && $link_info['id'] ) ? $link_info['id'] : esc_attr( sanitize_title( $link_info['name'] ) );
			$url           = array_key_exists( $sanitized_key, $this->purchase_links ) ? $this->purchase_links[ $sanitized_key ] : '';
			$name          = $link_info['name'];

			// We need both a site name and URL to proceed.
			if ( empty( $url ) || empty( $name ) ) {
				continue;
			}

			// Add it to the list!
			$list .= '<li class="novelist-purchase-link-' . esc_attr( $sanitized_key ) . '"><a href="' . esc_url( $url ) . '">' . esc_html( $name ) . '</a></li>';
		}

		if ( ! empty( $list_type ) ) {
			$list .= '</' . esc_attr( $list_type ) . '>';
		}

		return $list;

	}

	/**
	 * Get Formatted Purchase Links
	 *
	 * Returns a formatted HTML string of purchase links, using the template and
	 * separator from the sttings panel.
	 *
	 * @param string|false $separator Text to use between each link. Set to `false` to use the option via the settings.
	 *
	 * @access public
	 * @since  1.0.5
	 * @return string
	 */
	public function get_formatted_purchase_links( $separator = false ) {

		$final_links = '';
		$links_array = $this->get_purchase_links();
		$saved_links = novelist_get_option( 'purchase_links', false );

		if ( is_array( $links_array ) && count( $links_array ) && is_array( $saved_links ) ) {
			$final_link_list = array();

			foreach ( $saved_links as $link_info ) {
				$sanitized_key = ( array_key_exists( 'id', $link_info ) && $link_info['id'] ) ? $link_info['id'] : esc_attr( sanitize_title( $link_info['name'] ) );
				$url           = array_key_exists( $sanitized_key, $links_array ) ? $links_array[ $sanitized_key ] : '';

				if ( empty( $url ) ) {
					continue;
				}

				$link_template     = str_replace( '[link]', esc_url( $url ), $link_info['template'] );
				$final_link_list[] = apply_filters( 'novelist-buy-buttons/formatted-purchase-links/template', $link_template, $url, $link_info );
			}

			$separator   = ( $separator === false ) ? novelist_get_option( 'purchase_link_separator', '' ) : $separator;
			$final_links = implode( apply_filters( 'novelist-buy-buttons/formatted-purchase-links/separator', $separator ), $final_link_list );
		}

		return apply_filters( 'novelist-buy-buttons/formatted-purchase-links', $final_links, $saved_links, $this );

	}

	/**
	 * Displays the list of purchase links
	 *
	 * @uses   Novelist_Book::get_purchase_links_list
	 *
	 * @param string $list_type
	 *
	 * @access public
	 * @since  1.0.0
	 * @return void
	 */
	public function purchase_links_list( $list_type = 'ul' ) {

		echo $this->get_purchase_links_list( $list_type );

	}

	/**
	 * Get Synopsis
	 *
	 * @access public
	 * @since  1.0.0
	 * @return string
	 */
	public function get_synopsis() {

		if ( ! isset( $this->synopsis ) ) {
			$this->synopsis = get_post_meta( $this->ID, 'novelist_synopsis', true );
		}

		return apply_filters( 'novelist/book/get/synopsis', $this->synopsis, $this->ID );

	}

	/**
	 * Get Excerpt
	 *
	 * @access public
	 * @since  1.0.0
	 * @return string
	 */
	public function get_excerpt() {

		if ( ! isset( $this->excerpt ) ) {
			$this->excerpt = get_post_meta( $this->ID, 'novelist_excerpt', true );
		}

		return apply_filters( 'novelist/book/get/excerpt', $this->excerpt, $this->ID );

	}

	/**
	 * Get Extra Text
	 *
	 * @access public
	 * @since  1.0.0
	 * @return string
	 */
	public function get_extra_text() {

		if ( ! isset( $this->extra_text ) ) {
			$this->extra_text = get_post_meta( $this->ID, 'novelist_extra', true );
		}

		return apply_filters( 'novelist/book/get/extra_text', $this->extra_text, $this->ID );

	}

	/**
	 * Get Value
	 *
	 * Returns any meta value for this Book object.
	 *
	 * @param string $key Post meta key to get the value of.
	 *
	 * @access public
	 * @since  1.0.0
	 * @return mixed
	 */
	public function get_value( $key ) {

		return get_post_meta( $this->ID, $key, true );

	}

	/**
	 * Update Value
	 *
	 * Updates a meta value for this book.
	 *
	 * @param string $key   Meta key without the novelist_ prefix
	 * @param string $value New value
	 *
	 * @access public
	 * @since  1.0.0
	 * @return bool|int
	 */
	public function update_value( $key, $value ) {

		$new_key = 'novelist_' . $key;

		return update_post_meta( $this->ID, $key, $new_key );

	}

	/**
	 * Get Series Books Grid
	 *
	 * Displays other books in this series in a photo grid.
	 *
	 * @uses   Novelist_Book::get_series_books()
	 *
	 * @access public
	 * @since  1.0.0
	 * @return string
	 */
	public function get_series_books_grid() {

		$series_query = $this->get_series_books();
		$output       = '';

		if ( ! $series_query || ! is_a( $series_query, 'WP_Query' ) || ! $series_query->have_posts() ) {
			return $output;
		}

		$output .= '<div class="novelist-series-books novelist-series-books-grid">';

		while ( $series_query->have_posts() ) : $series_query->the_post();

			$this_book = new Novelist_Book( get_the_ID() );
			$cover     = $this_book->get_cover_image( 'medium' );

			if ( empty( $cover ) ) {
				continue;
			}

			ob_start();

			?>
			<div class="novelist-series-book">
				<a href="<?php echo esc_url( get_permalink() ); ?>">
					<?php echo $cover; ?>
				</a>
			</div>
			<?php

			$output .= ob_get_clean();

		endwhile;

		wp_reset_postdata();

		$output .= '</div>';

		return $output;

	}

	/**
	 * Get Series Books Links
	 *
	 * Displays other books in this series in a comma-separated text list.
	 *
	 * @uses   Novelist_Book::get_series_books()
	 *
	 * @access public
	 * @since  1.0.0
	 * @return string
	 */
	public function get_series_books_links() {

		$series_query = $this->get_series_books();
		$output       = '';

		if ( ! $series_query || ! is_a( $series_query, 'WP_Query' ) || ! $series_query->have_posts() ) {
			return $output;
		}

		$links_array = array();

		while ( $series_query->have_posts() ) : $series_query->the_post();

			if ( get_the_ID() == $this->ID ) {
				continue;
			}

			$links_array[] = '<a href="' . esc_url( get_permalink() ) . '">' . esc_html( get_the_title() ) . '</a>';

		endwhile;

		wp_reset_postdata();

		$output = '<span class="novelist-series-books novelist-series-books-links">' . implode( ', ', $links_array ) . '</span>';

		return $output;

	}

	/**
	 * Render Book Information
	 *
	 * Renders each piece of book information on the front-end. Each piece of info
	 * has its placeholder converted to its actual value and the new template
	 * id displayed.
	 *
	 * @param string $key            Key of the field to render
	 * @param array  $enabled_fields Array of enabled fields
	 *
	 * @access public
	 * @since  1.0.0
	 * @return void
	 */
	public function render( $key = '', $enabled_fields = false ) {

		if ( empty( $enabled_fields ) || ! is_array( $enabled_fields ) ) {
			$enabled_fields = novelist_get_option( 'book_layout', array() );
		}

		$all_fields = novelist_get_book_fields();

		// Make sure the array key exists.
		if ( ! array_key_exists( $key, $enabled_fields ) || ! array_key_exists( $key, $all_fields ) ) {
			return;
		}

		$template = $enabled_fields[ $key ]['label']; // Value entered by the user as a template.
		$find     = $all_fields[ $key ]['placeholder']; // Thing we need to look for and replace with a value.
		$value    = '';

		switch ( $key ) {

			/*
			 * Cover Image
			 *
			 * Turn into HTML <img> markup.
			 */
			case 'cover' :
				//$cover_ID  = $this->get_cover_ID();
				$alignment = $enabled_fields['cover']['alignment'];
				$class     = 'align' . sanitize_html_class( $alignment );
				//$value     = ! empty( $cover_ID ) ? wp_get_attachment_image( intval( $cover_ID ), 'full', false, array( 'class' => 'novelist-cover-image ' . $class ) ) : '';
				$value = $this->get_cover_image( novelist_get_option( 'cover_image_size', 'large' ), $class, true );
				break;

			/*
			 * Series
			 *
			 * Combine the name of the series with the number.
			 */
			case 'series' :
				$value = novelist_get_formatted_series( $this->ID );
				break;

			/*
			 * Purchase Links
			 *
			 * Set up the HTML for each array key and implode it, baby.
			 */
			case 'purchase_links' :
				$value = $this->get_formatted_purchase_links();
				break;

			/*
			 * Series Books
			 *
			 * Format the books.
			 */
			case 'series_books' :
				$series_books_query = $this->get_series_books();

				// Bail if it's empty, not a WP_Query or there are no results.
				if ( ! $series_books_query || ! is_a( $series_books_query, 'WP_Query' ) || ! $series_books_query->have_posts() ) {
					break;
				}

				$format = novelist_get_option( 'series_books_layout', 'grid' );

				if ( $format == 'grid' ) {
					$value = $this->get_series_books_grid();
				} else {
					$value = $this->get_series_books_links();
				}
				break;

			/*
			 * For all others, just get the value.
			 */
			default :
				if ( method_exists( $this, 'get_' . $key ) ) {
					$value = call_user_func( array( $this, 'get_' . $key ) );
				}

		}

		$value = apply_filters( 'novelist/book/pre-render/' . $key, $value, $key, $all_fields, $enabled_fields, $this );

		if ( empty( $value ) ) {
			return;
		}

		// If they want to add a line break, add that in.
		if ( array_key_exists( 'linebreak', $enabled_fields[ $key ] ) && $enabled_fields[ $key ]['linebreak'] == 'on' ) {
			$template .= apply_filters( 'novelist/book/render/line-break', '<br>' );
		}

		// Replace the placeholder with the value.
		$final_value = str_replace( $find, $value, $template );

		// Filter all values.
		$final_value = apply_filters( 'novelist/book/render', $final_value, $key, $this );

		// Allow shortcodes.
		$final_value = do_shortcode( $final_value );

		echo apply_filters( 'novelist/book/render/' . $key, $final_value, $key, $all_fields, $enabled_fields, $this );

	}

}
