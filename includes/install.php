<?php
/**
 * Functions that run on install.
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
 * Install
 *
 * Registers post types, custom taxonomies, and flushes
 * rewrite rules.
 *
 * @since 1.0.0
 * @return void
 */
function novelist_install( $network_wide = false ) {
	global $wpdb;
	if ( is_multisite() && $network_wide ) {
		foreach ( $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs LIMIT 100" ) as $blog_id ) {
			switch_to_blog( $blog_id );
			novelist_run_install();
			restore_current_blog();
		}
	} else {
		novelist_run_install();
	}
}

register_activation_hook( NOVELIST_PLUGIN_FILE, 'novelist_install' );

/**
 * Run Installation
 *
 * @since 1.0.0
 * @return void
 */
function novelist_run_install() {
	global $novelist_options;

	// Set up Custom Post Type.
	novelist_setup_post_types();

	// Set up Taxonomies.
	novelist_setup_taxonomies();

	// Clear permalinks.
	flush_rewrite_rules( false );

	// Add Upgraded from Option
	$current_version = get_option( 'novelist_version' );
	if ( $current_version ) {
		update_option( 'novelist_version_upgraded_from', $current_version );
	}

	// Set up our default settings.
	$options         = array();
	$current_options = get_option( 'novelist_settings', array() );

	// Populate default values.
	foreach ( novelist_get_registered_settings() as $tab => $sections ) {
		foreach ( $sections as $section => $settings ) {
			// Check for backwards compatibility
			$tab_sections = novelist_get_settings_tab_sections( $tab );
			if ( ! is_array( $tab_sections ) || ! array_key_exists( $section, $tab_sections ) ) {
				$section  = 'main';
				$settings = $sections;
			}
			foreach ( $settings as $option ) {
				if ( 'checkbox' == $option['type'] && ! empty( $option['std'] ) ) {
					$options[ $option['id'] ] = '1';
				} elseif ( 'book_layout' == $option['type'] && ! array_key_exists( 'book_layout', $current_options ) ) {
					$options[ $option['id'] ] = novelist_get_book_fields();
				}
			}
		}
	}

	$merged_options   = array_merge( $novelist_options, $options );
	$novelist_options = $merged_options;

	update_option( 'novelist_settings', $merged_options );
	update_option( 'novelist_version', NOVELIST_VERSION );

	// Create book manager role.
	$roles = new Novelist_Roles;
	$roles->add_roles();
	$roles->add_caps();

	// Bail if activating from network, or bulk
	if ( is_network_admin() || isset( $_GET['activate-multi'] ) ) {
		return;
	}

	// Add the transient to redirect.
	set_transient( '_novelist_activation_redirect', true, 30 );
}

/**
 * When a new Blog is created in multisite, see if Novelist is network activated, and run the installer.
 *
 * @param  int    $blog_id The Blog ID created
 * @param  int    $user_id The User ID set as the admin
 * @param  string $domain  The URL
 * @param  string $path    Site Path
 * @param  int    $site_id The Site ID
 * @param  array  $meta    Blog Meta
 *
 * @since 1.0.0
 * @return void
 */
function novelist_new_blog_created( $blog_id, $user_id, $domain, $path, $site_id, $meta ) {
	if ( is_plugin_active_for_network( plugin_basename( NOVELIST_PLUGIN_FILE ) ) ) {
		switch_to_blog( $blog_id );
		novelist_install();
		restore_current_blog();
	}
}

add_action( 'wpmu_new_blog', 'novelist_new_blog_created', 10, 6 );