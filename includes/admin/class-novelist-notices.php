<?php

/**
 * Admin Notices Class
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
 * Class Novelist_Notices
 *
 * @since 1.0.0
 */
class Novelist_Notices {

	/**
	 * Novelist_Notices constructor.
	 *
	 * @access public
	 * @since  1.0.0
	 * @return void
	 */
	public function __construct() {
		add_action( 'admin_notices', array( $this, 'show_notices' ) );
		add_action( 'novelist/dismiss/notices', array( $this, 'dismiss_notices' ) );
	}

	/**
	 * Show Notices
	 *
	 * @access public
	 * @since  1.0.0
	 * @return void
	 */
	public function show_notices() {
		$notices = array(
			'updated' => array(),
			'error'   => array()
		);

		if ( isset( $_GET['novelist-message'] ) ) {
			if ( current_user_can( 'manage_novelist_settings' ) ) {
				switch ( $_GET['novelist-message'] ) {
					case 'settings-imported' :
						$notices['updated']['novelist-settings-imported'] = __( 'The settings have been successfully imported.', 'novelist' );
						break;
				}
			}
		}

		if ( count( $notices['updated'] ) ) {
			foreach ( $notices['updated'] as $notice => $message ) {
				add_settings_error( 'novelist-notices', $notice, $message, 'updated' );
			}
		}

		if ( count( $notices['error'] ) ) {
			foreach ( $notices['error'] as $notice => $message ) {
				add_settings_error( 'novelist-notices', $notice, $message, 'error' );
			}
		}

		settings_errors( 'novelist-notices' );
	}

	/**
	 * Dismiss Notices
	 *
	 * Update current user's meta to mark this notice as dismissed.
	 *
	 * @access public
	 * @since  1.0.0
	 * @return void
	 */
	public function dismiss_notices() {
		if ( isset( $_GET['novelist_notice'] ) ) {
			update_user_meta( get_current_user_id(), '_novelist_' . $_GET['novelist_notice'] . '_dismissed', 1 );
			wp_redirect( remove_query_arg( array( 'novelist_action', 'novelist_notice' ) ) );
			exit;
		}
	}

}

new Novelist_Notices;