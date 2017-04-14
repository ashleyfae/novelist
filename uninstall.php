<?php
/**
 * Uninstall Novelist
 *
 * Deletes the following plugin data:
 *      + Custom Post Types
 *      + Custom Taxonomies and Terms
 *      + Plugin Settings
 *      + Capabilities and Roles
 *      + Transients
 *
 * @package   novelist
 * @copyright Copyright (c) 2016, Nose Graze Ltd.
 * @license   GPL2+
 */

// Exit if accessed directly.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

// Include Novelist file.
include_once 'novelist.php';

global $wpdb, $wp_roles;

// Bail if they haven't opted to delete settings.
if ( ! novelist_get_option( 'uninstall_on_delete' ) ) {
	return;
}

/*
 * Delete all the custom post types.
 */
$novelist_taxonomies = array( 'novelist-series', 'novelist-genre', );
$novelist_post_types = array( 'book' );
foreach ( $novelist_post_types as $post_type ) {

	$novelist_taxonomies = array_merge( $novelist_taxonomies, get_object_taxonomies( $post_type ) );
	$args                = array(
		'post_type'   => $post_type,
		'post_status' => 'any',
		'numberposts' => - 1,
		'fields'      => 'ids'
	);
	$items               = get_posts( $args );

	if ( $items ) {
		foreach ( $items as $item ) {
			wp_delete_post( $item, true );
		}
	}
}

/*
 * Delete all terms and taxonomies.
 */
$get_terms_args = array(
	'taxonomy'   => $novelist_taxonomies,
	'hide_empty' => false
);
$terms          = get_terms( $get_terms_args );

if ( is_array( $terms ) ) {
	foreach ( $terms as $term ) {
		wp_delete_term( $term->term_id, $term->taxonomy );
	}
}

/*
 * Delete plugin settings.
 */
delete_option( 'novelist_settings' );
delete_option( 'novelist_version' );
delete_option( 'novelist_version_upgraded_from' );
delete_option( 'novelist_imported_demo_book' );

/*
 * Delete capabilities and roles.
 */
Novelist()->roles->remove_caps();
$novelist_roles = array( 'book_manager' );
foreach ( $novelist_roles as $role ) {
	remove_role( $role );
}

/*
 * Delete transients.
 */
$wpdb->query( "DELETE FROM $wpdb->options WHERE option_name LIKE '_transient_novelist_%'" );
$wpdb->query( "DELETE FROM $wpdb->options WHERE option_name LIKE '_transient_timeout_novelist_%'" );