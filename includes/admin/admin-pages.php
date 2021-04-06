<?php
/**
 * Admin Pages
 *
 * Creates admin pages and loads any required assets on these pages.
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
 * Creates admin submenu pages under 'Books'.
 *
 * @since 1.0.0
 * @return void
 */
function novelist_add_options_link() {
	$novelist_settings_page   = add_submenu_page( 'edit.php?post_type=book', __( 'Novelist Settings', 'novelist' ), __( 'Settings', 'novelist' ), 'manage_novelist_settings', 'novelist-settings', 'novelist_options_page' );
	$novelist_tools_page      = add_submenu_page( 'edit.php?post_type=book', __( 'Novelist Tools', 'novelist' ), __( 'Tools', 'novelist' ), 'manage_novelist_settings', 'novelist-tools', 'novelist_tools_page' );
	$novelist_extensions_page = add_submenu_page( 'edit.php?post_type=book', __( 'Novelist Extensions', 'novelist' ), __( 'Extensions', 'novelist' ), 'manage_novelist_settings', 'novelist-extensions', 'novelist_extensions_page' );
}

add_action( 'admin_menu', 'novelist_add_options_link', 10 );

/**
 * Is Admin Page
 *
 * Checks whether or not the current page is a Novelist admin page.
 *
 * @since 1.0.0
 * @return bool
 */
function novelist_is_admin_page() {
	$screen           = get_current_screen();
	$is_novelist_page = false;

	if ( $screen->base == 'book_page_novelist-settings' ) {
		$is_novelist_page = true;
	}

	if ( $screen->base == 'post' && $screen->post_type == 'book' ) {
		$is_novelist_page = true;
	}

	if ( $screen->base == 'edit' && $screen->post_type == 'book' ) {
		$is_novelist_page = true;
	}

	if ( $screen->base == 'dashboard_page_novelist-getting-started' ) {
		$is_novelist_page = true;
	}

	if ( $screen->base == 'book_page_novelist-tools' ) {
		$is_novelist_page = true;
	}

	if ( $screen->base == 'book_page_novelist-extensions' ) {
		$is_novelist_page = true;
	}

	return apply_filters( 'novelist/is-admin-page', $is_novelist_page, $screen );
}

/**
 * Load Admin Scripts
 *
 * Adds all admin scripts and stylesheets to the admin panel.
 *
 * @param string $hook Currently loaded page
 *
 * @since 1.0.0
 * @return void
 */
function novelist_load_admin_scripts( $hook ) {
	if ( ! apply_filters( 'novelist/load-admin-scripts', novelist_is_admin_page(), $hook ) ) {
		return;
	}

	$js_dir  = NOVELIST_PLUGIN_URL . 'assets/js/';
	$css_dir = NOVELIST_PLUGIN_URL . 'assets/css/';

	/*
	 * JavaScript
	 */

	wp_register_script( 'jquery-recopy', $js_dir . 'jquery.recopy.min.js', array( 'jquery' ), NOVELIST_VERSION, true );
	wp_enqueue_script( 'jquery-recopy' );

	// Media Upload
	wp_enqueue_media();
	wp_register_script( 'novelist-media-upload', $js_dir . 'media-upload.min.js', array( 'jquery' ), NOVELIST_VERSION, true );
	wp_enqueue_script( 'novelist-media-upload' );

	$settings = array(
		'text_title'  => __( 'Upload or Select an Image', 'novelist' ),
		'text_button' => __( 'Insert Image', 'novelist' )
	);

	wp_localize_script( 'novelist-media-upload', 'NOVELIST_MEDIA', apply_filters( 'novelist/media-upload-js-settings', $settings ) );

	$admin_deps = array(
		'jquery',
		'jquery-ui-draggable',
		'jquery-ui-droppable',
		'jquery-ui-sortable',
		'jquery-recopy',
		'wp-color-picker'
	);

	wp_register_script( 'novelist-admin-scripts', $js_dir . 'admin-scripts.min.js', $admin_deps, NOVELIST_VERSION, true );
	wp_enqueue_script( 'novelist-admin-scripts' );

	$settings = array(
		'repeater_move_up'   => esc_attr__( 'Move Up', 'novelist' ),
		'repeater_move_down' => esc_attr__( 'Move Down', 'novelist' ),
		'text_remove'        => __( 'Remove', 'novelist' ),
		'confirm_reset'      => __( 'Are you sure you wish to revert all the settings in this tab to their default values? This cannot be undone.', 'novelist' )
	);

	wp_localize_script( 'novelist-admin-scripts', 'NOVELIST', apply_filters( 'novelist/admin-scripts-settings', $settings ) );

	/*
	 * Stylesheets
	 */

	// Color Picker
	wp_enqueue_style( 'wp-color-picker' );

	wp_register_style( 'novelist-admin', $css_dir . 'novelist-admin.css', array(), NOVELIST_VERSION );
	wp_enqueue_style( 'novelist-admin' );
}

add_action( 'admin_enqueue_scripts', 'novelist_load_admin_scripts', 100 );

/**
 * Load Widget Scripts
 *
 * Adds our JavaScripts to the widgets.php page in the admin area.
 *
 * @param string $hook Currently loaded page
 *
 * @since 1.0.0
 * @return void
 */
function novelist_load_widget_scripts( $hook ) {
	if ( ! apply_filters( 'novelist/load-widget-scripts', true ) || $hook != 'widgets.php' ) {
		return;
	}

	$js_dir  = NOVELIST_PLUGIN_URL . 'assets/js/';

	wp_enqueue_script( 'wp-color-picker' );
	wp_enqueue_style( 'wp-color-picker' );

	wp_enqueue_script( 'novelist-widget-scripts', $js_dir . 'widget-settings.min.js', array( 'jquery' ), NOVELIST_VERSION, true );
}

add_action( 'admin_enqueue_scripts', 'novelist_load_widget_scripts', 100 );
