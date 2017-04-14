<?php
/**
 * Extensions Page
 *
 * @package   novelist
 * @copyright Copyright (c) 2016, Nose Graze Ltd.
 * @license   GPL2+
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Extensions Page
 *
 * Renders the extensions page content.
 *
 * @since 1.0.3
 * @return void
 */
function novelist_extensions_page() {
	?>
	<div id="novelist-extensions" class="wrap">
		<h1><?php _e( 'Extensions for Novelist', 'novelist' ); ?></h1>

		<div id="novelist-extensions-feed" class="theme-browser">
			<?php echo novelist_get_extensions_feed(); ?>
		</div>
	</div>
	<?php
}

/**
 * Get Extensions
 *
 * @since 1.0.3
 * @return string
 */
function novelist_get_extensions_feed() {

	$cache = get_transient( 'novelist_extensions_feed' );

	if ( $cache === false ) {
		$url = 'https://novelistplugin.com/?feed=extensions';

		$feed = wp_remote_get( esc_url_raw( $url ), array( 'sslverify' => false ) );

		if ( ! is_wp_error( $feed ) ) {
			if ( isset( $feed['body'] ) && strlen( $feed['body'] ) > 0 ) {
				$cache = wp_remote_retrieve_body( $feed );
				set_transient( 'novelist_extensions_feed', $cache, WEEK_IN_SECONDS );
			}
		} else {
			$cache = '<div class="error"><p>' . __( 'There was an error communicating with the novelistplugin.com server. Please try again later.', 'novelist' ) . '</p></div>';
		}
	}

	return $cache;

}