<?php

/**
 * Welcome Page Class
 *
 * @package   novelist
 * @copyright Copyright (c) 2016, Nose Graze Ltd.
 * @license   GPL2+
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Novelist_Welcome {

	/**
	 * @var string The capability users should have to view the page
	 */
	public $minimum_capability = 'manage_options';

	/**
	 * Constructor
	 *
	 * Add our actions.
	 *
	 * @access public
	 * @since  1.0.0
	 * @return void
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'admin_menus' ) );
		add_action( 'admin_head', array( $this, 'admin_head' ) );
		add_action( 'admin_init', array( $this, 'welcome' ) );
	}

	/**
	 * Dashboard Pages
	 *
	 * Register our hidden dashboard pages.
	 *
	 * @access public
	 * @since  1.0.0
	 * @return void
	 */
	public function admin_menus() {
		// About Page
		add_dashboard_page(
			__( 'Welcome to Novelist', 'novelist' ),
			__( 'Welcome to Novelist', 'novelist' ),
			$this->minimum_capability,
			'novelist-about',
			array( $this, 'about_screen' )
		);

		// Changelog Page
		add_dashboard_page(
			__( 'Novelist Changelog', 'novelist' ),
			__( 'Novelist Changelog', 'novelist' ),
			$this->minimum_capability,
			'novelist-changelog',
			array( $this, 'changelog_screen' )
		);

		// Getting Started Page
		add_dashboard_page(
			__( 'Getting Started with Novelist', 'novelist' ),
			__( 'Getting Started with Novelist', 'novelist' ),
			$this->minimum_capability,
			'novelist-getting-started',
			array( $this, 'getting_started_screen' )
		);
	}

	/**
	 * Hide Dashboard Pages
	 *
	 * @access public
	 * @since  1.0.0
	 * @return void
	 */
	public function admin_head() {
		remove_submenu_page( 'index.php', 'novelist-about' );
		remove_submenu_page( 'index.php', 'novelist-changelog' );
		remove_submenu_page( 'index.php', 'novelist-getting-started' );

		?>
		<style type="text/css" media="screen">
			/*<![CDATA[*/
			.novelist-about-wrap .novelist-badge {
				float: right;
				border-radius: 4px;
				margin: 0 0 15px 15px;
				max-width: 100px;
			}

			.novelist-about-wrap #novelist-header {
				margin-bottom: 15px;
			}

			.novelist-about-wrap #novelist-header h1 {
				margin-bottom: 15px !important;
			}

			.novelist-about-wrap .about-text {
				margin: 0 0 15px;
				max-width: 670px;
			}

			.novelist-about-wrap .feature-section {
				margin-top: 20px;
			}

			.novelist-about-wrap .feature-section-content,
			.novelist-about-wrap .feature-section-media {
				width: 50%;
				box-sizing: border-box;
			}

			.novelist-about-wrap .feature-section-content {
				float: left;
				padding-right: 50px;
			}

			.novelist-about-wrap .feature-section-content h4 {
				margin: 0 0 1em;
			}

			.novelist-about-wrap .feature-section-media {
				float: right;
				text-align: right;
				margin-bottom: 20px;
			}

			.novelist-about-wrap .feature-section:not(.under-the-hood) .col {
				margin-top: 0;
			}

			.novelist-about-wrap .changelog ul {
				list-style: disc;
				margin: 1em 0 2em;
				padding-left: 2em;
			}

			.novelist-about-wrap .changelog ul li {
				margin-bottom: 1em;
			}

			/* responsive */
			@media all and ( max-width: 782px ) {
				.novelist-about-wrap .feature-section-content,
				.novelist-about-wrap .feature-section-media {
					float: none;
					padding-right: 0;
					width: 100%;
					text-align: left;
				}

				.novelist-about-wrap .feature-section-media img {
					float: none;
					margin: 0 0 20px;
				}
			}

			/*]]>*/
		</style>
		<?php
	}

	/**
	 * Display Welcome Message
	 *
	 * @access public
	 * @since  1.0.0
	 * @return void
	 */
	public function welcome_message() {
		?>
		<div id="novelist-header">
			<h1><?php printf( __( 'Welcome to Novelist %s', 'novelist' ), NOVELIST_VERSION ); ?></h1>
			<p class="about-text">
				<?php _e( 'Let\'s start building your awesome author website!', 'novelist' ); ?>
			</p>
		</div>
		<?php
	}

	/**
	 * Navigation Tabs
	 *
	 * @access public
	 * @since  1.0.0
	 * @return void
	 */
	public function tabs() {
		$selected = isset( $_GET['page'] ) ? $_GET['page'] : 'novelist-about';
		?>
		<h1 class="nav-tab-wrapper">
			<a class="nav-tab <?php echo $selected == 'novelist-about' ? 'nav-tab-active' : ''; ?>" href="<?php echo esc_url( admin_url( add_query_arg( array( 'page' => 'novelist-about' ), 'index.php' ) ) ); ?>">
				<?php _e( "What's New", 'novelist' ); ?>
			</a>
			<a class="nav-tab <?php echo $selected == 'novelist-getting-started' ? 'nav-tab-active' : ''; ?>" href="<?php echo esc_url( admin_url( add_query_arg( array( 'page' => 'novelist-getting-started' ), 'index.php' ) ) ); ?>">
				<?php _e( 'Getting Started', 'novelist' ); ?>
			</a>
		</h1>
		<?php
	}

	/**
	 * Render About Screen
	 *
	 * @access public
	 * @since  1.0.0
	 * @return void
	 */
	public function about_screen() {
		?>
		<div class="wrap about-wrap novelist-about-wrap">
			<?php
			// Load welcome message and tabs.
			$this->welcome_message();
			$this->tabs();
			?>

			<div class="changelog">
				<h3><?php _e( '1.1.0', 'novelist' ); ?></h3>
				<div class="feature-section">
					<div class="feature-content">
						<ul>
							<li>
								<?php printf( __( 'New Default Cover Image setting. <a href="%s">check it out!</a>.', 'novelist' ), esc_url( admin_url( 'edit.php?post_type=book&page=novelist-settings&tab=book&section=settings' ) ) ) ?>
							</li>
							<li>
								<?php printf(
									__( 'On the <a href="%s">export page</a> you can now choose between exporting all settings or only the book layout. This makes it super easy to move book layouts between sites while not affecting the other settings.', 'novelist' ),
									esc_url( admin_url( 'edit.php?post_type=book&page=novelist-tools' ) )
								); ?>
							</li>
							<li>
								<?php _e( 'New "Add Book Grid" button above the post/page editor to aid you in creating your grids.', 'novelist' ); ?>
							</li>
						</ul>
					</div>
				</div>
			</div>

			<div class="return-to-dashboard">
				<a href="<?php echo esc_url( admin_url( add_query_arg( array(
					'post_type' => 'book',
					'page'      => 'novelist-settings'
				), 'edit.php' ) ) ); ?>"><?php _e( 'Go to Novelist Settings', 'novelist' ); ?></a> &middot;
				<a href="<?php echo esc_url( admin_url( add_query_arg( array( 'page' => 'novelist-changelog' ), 'index.php' ) ) ); ?>"><?php _e( 'View the Full Changelog', 'novelist' ); ?></a>
			</div>
		</div>
		<?php
	}

	/**
	 * Render Changelog Screen
	 *
	 * @access public
	 * @since  1.0.0
	 * @return void
	 */
	public function changelog_screen() {
		?>
		<div class="wrap about-wrap novelist-about-wrap">
			<?php
			// load welcome message and content tabs
			$this->welcome_message();
			$this->tabs();
			?>
			<div class="changelog">
				<h3><?php _e( 'Full Changelog', 'novelist' ); ?></h3>

				<div class="feature-section">
					<?php echo $this->parse_readme(); ?>
				</div>
			</div>

			<div class="return-to-dashboard">
				<a href="<?php echo esc_url( admin_url( add_query_arg( array(
					'post_type' => 'book',
					'page'      => 'novelist-settings'
				), 'edit.php' ) ) ); ?>"><?php _e( 'Go to Novelist Settings', 'novelist' ); ?></a>
			</div>
		</div>
		<?php
	}

	/**
	 * Render Getting Started Screen
	 *
	 * @access public
	 * @since  1.0.0
	 * @return void
	 */
	public function getting_started_screen() {
		$demo_book = get_option( 'novelist_imported_demo_book' );
		?>
		<div class="wrap about-wrap novelist-about-wrap">
			<?php
			// Load welcome message and tabs.
			$this->welcome_message();
			$this->tabs();
			?>

			<p class="about-description"><?php _e( 'Let\'s get you started with Novelist and creating your first book.', 'novelist' ); ?></p>


			<div class="changelog">
				<?php if ( ! $demo_book ) : ?>
					<h3><?php _e( 'Want to See an Example?', 'novelist' ); ?></h3>
					<div class="feature-section">
						<div class="feature-section-content">
							<p>
								<button id="novelist-import-demo-book" class="button button-primary" data-nonce="<?php echo wp_create_nonce( 'novelist_import_demo_book' ); ?>"><?php _e( 'Import Demo Book', 'novelist' ); ?></button>
							</p>
						</div>
					</div>
				<?php endif; ?>

				<h3><?php _e( 'Creating Your First Book', 'novelist' ); ?></h3>
				<div class="feature-section">
					<div class="feature-section-media">
						<img src="<?php echo esc_url( NOVELIST_PLUGIN_URL . 'assets/images/screenshots/edit-book.png' ); ?>" class="novelist-welcome-screenshots">
					</div>
					<div class="feature-section-content">
						<h4>
							<a href="<?php echo admin_url( 'post-new.php?post_type=book' ) ?>"><?php _e( 'Books &rarr; Add New', 'novelist' ); ?></a>
						</h4>
						<p><?php _e( 'To create your first book, find to the "Books" menu on the left and click "Add New". This brings you to the book details form. All you have to do is fill out each field. You can enter information about your book and upload a cover image.', 'novelist' ); ?></p>
						<p><?php _e( 'When you\'re ready, click "Publish" on your book or click "Preview" to test it out first.', 'novelist' ); ?></p>
					</div>
				</div>

				<h3><?php _e( 'Customize the Book Template', 'novelist' ); ?></h3>
				<div class="feature-section">
					<div class="feature-section-media">
						<img src="<?php echo esc_url( NOVELIST_PLUGIN_URL . 'assets/images/screenshots/book-template.png' ); ?>" class="novelist-welcome-screenshots">
					</div>
					<div class="feature-section-content">
						<p><?php printf( __( 'Each book gets formatted according to a template. You can customize this template in the <a href="%s">Book Layout settings page</a>.', 'novelist' ), admin_url( 'edit.php?post_type=book&page=novelist-settings&tab=book' ) ); ?></p>
						<p><?php _e( 'Fields can be removed, rearranged, and the template associated with each piece of book information can be customized to your liking.', 'novelist' ); ?></p>
					</div>
				</div>

				<h3><?php _e( 'Display All Your Books', 'novelist' ); ?></h3>
				<div class="feature-section">
					<div class="feature-section-media">
						<img src="<?php echo esc_url( NOVELIST_PLUGIN_URL . 'assets/images/screenshots/book-grid.jpg' ); ?>" class="novelist-welcome-screenshots">
					</div>
					<div class="feature-section-content">
						<h4><?php _e( 'All your books on one page', 'novelist' ); ?></h4>
						<p><?php printf( __( 'Novelist helps you easily display a grid of all your books on one page. Simply go to <a href="%s">Pages &rarr; Add New</a> and create a new page. Then insert this shortcode to display all your books in one awesome grid:', 'novelist' ), admin_url( 'post-new.php?post-type=page' ) ); ?></p>
						<pre>[novelist-books]</pre>

						<h4><?php _e( 'Customize the grid', 'novelist' ); ?></h4>
						<p><?php _e( 'The grid shortcode is completely customizable. You can add extra parameters to change how it looks.', 'novelist' ); ?></p>
						<p><?php printf( __( 'Number of columns:<br>%s', 'novelist' ), '<pre>[novelist-books columns="2"]</pre>' ); ?></p>
						<p><?php printf( __( 'Number of books per page:<br>%s', 'novelist' ), '<pre>[novelist-books number="6"]</pre>' ); ?></p>
						<p><?php printf( __( 'For more options, check out <a href="%s">the documentation</a>.', 'novelist' ), esc_url( 'https://novelistplugin.com/docs/general/shortcodes/novelist-books/' ) ); ?></p>
					</div>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * Parse the Novelist readme.txt file
	 *
	 * @access public
	 * @since  1.0.0
	 * @return string $readme HTML formatted readme file
	 */
	public function parse_readme() {
		$file = file_exists( NOVELIST_PLUGIN_DIR . 'readme.txt' ) ? NOVELIST_PLUGIN_DIR . 'readme.txt' : null;
		if ( ! $file ) {
			$readme = '<p>' . __( 'No valid changelog was found.', 'novelist' ) . '</p>';
		} else {
			$readme = file_get_contents( $file );
			$readme = nl2br( esc_html( $readme ) );
			$readme = explode( '== Changelog ==', $readme );
			$readme = end( $readme );
			$readme = preg_replace( '/`(.*?)`/', '<code>\\1</code>', $readme );
			$readme = preg_replace( '/[\040]\*\*(.*?)\*\*/', ' <strong>\\1</strong>', $readme );
			$readme = preg_replace( '/[\040]\*(.*?)\*/', ' <em>\\1</em>', $readme );
			$readme = preg_replace( '/= (.*?) =/', '<h4>\\1</h4>', $readme );
			$readme = preg_replace( '/\[(.*?)\]\((.*?)\)/', '<a href="\\2">\\1</a>', $readme );
		}

		return $readme;
	}

	/**
	 * Sends user to the Welcome page on first activation of Novelist as well as each
	 * time Novelist is upgraded to a new version
	 *
	 * @access public
	 * @since  1.0.0
	 * @return void
	 */
	public function welcome() {
		// Bail if no activation redirect
		if ( ! get_transient( '_novelist_activation_redirect' ) ) {
			return;
		}
		// Delete the redirect transient
		delete_transient( '_novelist_activation_redirect' );

		// Bail if activating from network, or bulk
		if ( is_network_admin() || isset( $_GET['activate-multi'] ) ) {
			return;
		}

		$upgrade = get_option( 'novelist_version_upgraded_from' );

		if ( ! $upgrade ) { // First time install
			wp_safe_redirect( admin_url( 'index.php?page=novelist-getting-started' ) );
			exit;
		} else { // Update
			wp_safe_redirect( admin_url( 'index.php?page=novelist-about' ) );
			exit;
		}
	}

}

new Novelist_Welcome();