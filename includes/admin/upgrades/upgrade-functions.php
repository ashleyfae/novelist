<?php
/**
 * Upgrade Functions
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
 * Perform Database Upgrades
 *
 * Also updates the version number.
 *
 * @since 1.0.3
 * @return void
 */
function novelist_do_automatic_upgrades() {

	$did_upgrade      = false;
	$novelist_version = get_option( 'novelist_version' );

	// We're not up to date!
	if ( version_compare( $novelist_version, NOVELIST_VERSION, '<' ) ) {
		$did_upgrade = true;
	}

	if ( $did_upgrade ) {

		update_option( 'novelist_version', NOVELIST_VERSION );

	}

}

add_action( 'admin_init', 'novelist_do_automatic_upgrades' );