<?php
/**
 * Admin Avtions
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
 * Processes all Novelist actions sent via POST and GET by looking for the 'novelist-action'
 * request and running do_action() to call the function.
 *
 * @since 1.0.0
 * @return void
 */
function novelist_process_actions() {
	if ( isset( $_POST['novelist_action'] ) ) {
		do_action( 'novelist/' . strip_tags( $_POST['novelist_action'] ), $_POST );
	}

	if ( isset( $_GET['novelist_action'] ) ) {
		do_action( 'novelist/' . strip_tags( $_GET['novelist_action'] ), $_GET );
	}
}

add_action( 'admin_init', 'novelist_process_actions' );