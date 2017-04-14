<?php
/**
 * Tools
 *
 * Functions for displaying the "Tools" page, including the Import/Export
 * of settings and system info.
 *
 * @package   novelist
 * @copyright Copyright (c) 2016, Nose Graze Ltd.
 * @license   GPL2+
 */

/**
 * Tools
 *
 * Display the layout of the Tools page.
 *
 * @since 1.0.0
 * @return void
 */
function novelist_tools_page() {
	$active_tab = isset( $_GET['tab'] ) ? $_GET['tab'] : 'import_export';
	?>
	<div class="wrap">
		<h1 class="nav-tab-wrapper">
			<?php
			foreach ( novelist_get_tools_tabs() as $tab_id => $tab_name ) {
				$tab_url = add_query_arg( array(
					'tab' => $tab_id
				) );

				$tab_url = remove_query_arg( array(
					'novelist-message'
				), $tab_url );

				$active = $active_tab == $tab_id ? ' nav-tab-active' : '';
				echo '<a href="' . esc_url( $tab_url ) . '" title="' . esc_attr( $tab_name ) . '" class="nav-tab' . $active . '">' . esc_html( $tab_name ) . '</a>';
			}
			?>
		</h1>

		<div class="metabox-holder">
			<?php do_action( 'novelist/tools/tab/' . $active_tab ); ?>
		</div>
	</div>
	<?php
}

/**
 * Get Tools Tabs
 *
 * @since 1.0.0
 * @return array
 */
function novelist_get_tools_tabs() {

	$tabs                  = array();
	$tabs['import_export'] = __( 'Import/Export', 'novelist' );
	$tabs['system_info']   = __( 'System Info', 'novelist' );

	return apply_filters( 'novelist/tools/tabs', $tabs );

}

/**
 * Display the tools import/export tab.
 *
 * @since 1.0.0
 * @return void
 */
function novelist_tools_import_export_display() {

	if ( ! current_user_can( 'manage_novelist_settings' ) ) {
		return;
	}

	do_action( 'novelist/tools/import_export/before' );

	?>
	<!-- Export -->
	<div class="postbox">
		<h3><span><?php _e( 'Export Settings', 'novelist' ); ?></span></h3>
		<div class="inside">
			<p><?php _e( 'Export the Novelist settings to a .json file. This allows you to backup your settings or import them to a different WordPress installation.', 'novelist' ); ?></p>
			<form method="POST" action="<?php echo esc_url( admin_url( 'edit.php?post_type=book&page=novelist-tools&tab=import_export' ) ); ?>">
				<p>
					<label for="novelist_export_type" class="screen-reader-text"><?php _e( 'Choose what settings to export', 'novelist' ); ?></label>
					<select id="novelist_export_type" name="novelist_export_type">
						<option value="all" selected><?php _e( 'All Settings', 'novelist' ); ?></option>
						<option value="book-layout"><?php _e( 'Book Layout Only', 'novelist' ); ?></option>
					</select>
				</p>
				<p><input type="hidden" name="novelist_action" value="export-settings"></p>
				<p>
					<?php wp_nonce_field( 'novelist_export_settings', 'novelist_export_nonce' ); ?>
					<?php submit_button( __( 'Export', 'novelist' ), 'secondary', 'submit', false ); ?>
				</p>
			</form>
		</div>
	</div>

	<!-- Import -->
	<div class="postbox">
		<h3><span><?php _e( 'Import Settings', 'novelist' ); ?></span></h3>
		<div class="inside">
			<p><?php _e( 'Import the Novelist settings from a .json file. You can obtain a .json file by exporting using the above function.', 'novelist' ); ?></p>
			<form method="POST" enctype="multipart/form-data" action="<?php echo esc_url( admin_url( 'edit.php?post_type=book&page=novelist-tools&tab=import_export' ) ); ?>">
				<p><input type="file" name="import_file"></p>
				<p>
					<input type="hidden" name="novelist_action" value="import-settings">
					<?php wp_nonce_field( 'novelist_import_settings', 'novelist_import_nonce' ); ?>
					<?php submit_button( __( 'Import', 'novelist' ), 'secondary', 'submit', false ); ?>
				</p>
			</form>
		</div>
	</div>
	<?php

	do_action( 'novelist/tools/import_export/after' );

}

add_action( 'novelist/tools/tab/import_export', 'novelist_tools_import_export_display' );

/**
 * Process Settings Export
 *
 * JSON encodes the Novelist settings and saves them to a file.
 *
 * @since 1.0.0
 * @return void
 */
function novelist_process_settings_export() {
	if ( empty( $_POST['novelist_export_nonce'] ) ) {
		return;
	}

	if ( ! wp_verify_nonce( $_POST['novelist_export_nonce'], 'novelist_export_settings' ) ) {
		return;
	}

	if ( ! current_user_can( 'manage_novelist_settings' ) ) {
		return;
	}

	$export_type = $_POST['novelist_export_type'];

	if ( $export_type == 'book-layout' ) {
		$book_layout = novelist_get_option( 'book_layout', array() );
		$settings    = array( 'book_layout' => $book_layout );
	} else {
		$settings = get_option( 'novelist_settings', array() );
	}

	nocache_headers();
	header( 'Content-Type: application/json; charset=utf-8' );
	header( 'Content-Disposition: attachment; filename=' . apply_filters( 'novelist/tools/import_export/file-name', 'novelist-settings-export-' . date( 'm-d-Y' ) ) . '.json' );
	header( "Expires: 0" );

	echo json_encode( $settings );
	exit;
}

add_action( 'novelist/export-settings', 'novelist_process_settings_export' );

/**
 * Process Settings Import
 *
 * Decodes the JSON file, converts to an associative array, and updates the Novelist settings.
 *
 * @since 1.0.0
 * @return void
 */
function novelist_process_settings_import() {
	if ( empty( $_POST['novelist_import_nonce'] ) ) {
		return;
	}

	if ( ! wp_verify_nonce( $_POST['novelist_import_nonce'], 'novelist_import_settings' ) ) {
		return;
	}

	if ( ! current_user_can( 'manage_novelist_settings' ) ) {
		return;
	}

	// Validate the file extension.
	$parts     = explode( '.', $_FILES['import_file']['name'] );
	$extension = end( $parts );
	if ( $extension != 'json' ) {
		wp_die( __( 'Please upload a valid .json file', 'novelist' ), __( 'Error', 'novelist' ), array( 'response' => 400 ) );
	}

	$import_file = $_FILES['import_file']['tmp_name'];

	if ( empty( $import_file ) ) {
		wp_die( __( 'Please upload a file to import', 'novelist' ), __( 'Error', 'novelist' ), array( 'response' => 400 ) );
	}

	// Retrieve the settings from the file and convert the json object to an array.
	$old_settings = get_option( 'novelist_settings', array() );
	$new_settings = json_decode( file_get_contents( $import_file ), true );

	update_option( 'novelist_settings', array_merge( $old_settings, $new_settings ) );

	wp_safe_redirect( admin_url( 'edit.php?post_type=book&page=novelist-tools&tab=import_export&novelist-message=settings-imported' ) );

	exit;
}

add_action( 'novelist/import-settings', 'novelist_process_settings_import' );

/**
 * Display the System Info tab.
 *
 * @since 1.0.0
 * @return void
 */
function novelist_tools_system_info_display() {

	if ( ! current_user_can( 'manage_novelist_settings' ) ) {
		return;
	}

	do_action( 'novelist/tools/system_info/before' );
	?>
	<form action="<?php echo esc_url( admin_url( 'edit.php?post_type=book&page=novelist-tools&tab=system_info' ) ); ?>" method="POST">
		<textarea readonly="readonly" onclick="this.focus(); this.select();" id="system-info-textarea" name="novelist-system-info" title="<?php esc_attr_e( 'To copy the system info, click below then press CTRL + C (PC) or CMD + C (Mac).', 'novelist' ); ?>"><?php echo novelist_tools_get_system_info(); ?></textarea>
		<p class="submit">
			<input type="hidden" name="novelist_action" value="download-system-info">
			<?php submit_button( __( 'Download System Info File', 'novelist' ), 'primary', 'novelist-download-system-info', false ); ?>
		</p>
	</form>
	<?php
	do_action( 'novelist/tools/system_info/after' );

}

add_action( 'novelist/tools/tab/system_info', 'novelist_tools_system_info_display' );

/**
 * Get System Info
 *
 * Taken from Easy Digital Downloads.
 *
 * @global $wpdb
 *
 * @since 1.0.0
 * @return string
 */
function novelist_tools_get_system_info() {
	global $wpdb;

	if ( ! class_exists( 'Browser' ) ) {
		require_once NOVELIST_PLUGIN_DIR . 'includes/libraries/browser.php';
	}

	$browser = new Browser();

	// Get theme info.
	$theme_data = wp_get_theme();
	$theme      = $theme_data->Name . ' ' . $theme_data->Version;

	$return = '### Begin System Info ###' . "\n\n";

	// Start with the basics...
	$return .= '-- Site Info' . "\n\n";
	$return .= 'Site URL:                 ' . site_url() . "\n";
	$return .= 'Home URL:                 ' . home_url() . "\n";
	$return .= 'Multisite:                ' . ( is_multisite() ? 'Yes' : 'No' ) . "\n";

	$return = apply_filters( 'novelist/system-info/after-site-info', $return );

	// The local users' browser information, handled by the Browser class
	$return .= "\n" . '-- User Browser' . "\n\n";
	$return .= $browser;

	$return = apply_filters( 'novelist/system-info/after-browser', $return );

	// WordPress configuration
	$return .= "\n" . '-- WordPress Configuration' . "\n\n";
	$return .= 'Version:                  ' . get_bloginfo( 'version' ) . "\n";
	$return .= 'Language:                 ' . ( defined( 'WPLANG' ) && WPLANG ? WPLANG : 'en_US' ) . "\n";
	$return .= 'Permalink Structure:      ' . ( get_option( 'permalink_structure' ) ? get_option( 'permalink_structure' ) : 'Default' ) . "\n";
	$return .= 'Active Theme:             ' . $theme . "\n";
	$return .= 'Show On Front:            ' . get_option( 'show_on_front' ) . "\n";

	// Only show page specs if frontpage is set to 'page'
	if ( get_option( 'show_on_front' ) == 'page' ) {
		$front_page_id = get_option( 'page_on_front' );
		$blog_page_id  = get_option( 'page_for_posts' );

		$return .= 'Page On Front:            ' . ( $front_page_id != 0 ? get_the_title( $front_page_id ) . ' (#' . $front_page_id . ')' : 'Unset' ) . "\n";
		$return .= 'Page For Posts:           ' . ( $blog_page_id != 0 ? get_the_title( $blog_page_id ) . ' (#' . $blog_page_id . ')' : 'Unset' ) . "\n";
	}

	$return .= 'ABSPATH:                  ' . ABSPATH . "\n";

	$return .= 'Table Prefix:             ' . 'Length: ' . strlen( $wpdb->prefix ) . '   Status: ' . ( strlen( $wpdb->prefix ) > 16 ? 'ERROR: Too long' : 'Acceptable' ) . "\n";
	$return .= 'WP_DEBUG:                 ' . ( defined( 'WP_DEBUG' ) ? WP_DEBUG ? 'Enabled' : 'Disabled' : 'Not set' ) . "\n";
	$return .= 'Memory Limit:             ' . WP_MEMORY_LIMIT . "\n";
	$return .= 'Registered Post Stati:    ' . implode( ', ', get_post_stati() ) . "\n";
	$return .= 'Registered Post Types:    ' . implode( ', ', get_post_types() ) . "\n";

	$return = apply_filters( 'novelist/system-info/after/wordpress-config', $return );

	// Novelist configuration
	$return .= "\n" . '-- Novelist Configuration' . "\n\n";
	$return .= 'Version:                  ' . NOVELIST_VERSION . "\n";
	$return .= 'Upgraded From:            ' . get_option( 'novelist_version_upgraded_from', 'None' ) . "\n";

	$return = apply_filters( 'novelist/system-info/after/novelist-config', $return );

	// Enabled Book Fields
	$return .= "\n" . '-- Enabled Book Fields' . "\n\n";

	$enabled_fields = novelist_get_option( 'book_layout', novelist_get_default_book_field_values() );
	foreach ( $enabled_fields as $key => $value ) {
		$return .= str_pad( $key, 26, ' ', STR_PAD_RIGHT ) . $value['label'] . "\n";
	}

	$return = apply_filters( 'novelist/system-info/after/enabled-book-fields', $return );

	// Purchase links
	$return .= "\n" . '-- Purchase Links' . "\n\n";

	$purchase_links = novelist_get_option( 'purchase_links', array() );
	foreach ( $purchase_links as $key => $link ) {
		if ( ! is_array( $link ) || ! array_key_exists( 'name', $link ) || ! array_key_exists( 'template', $link ) ) {
			continue;
		}
		$return .= str_pad( $link['name'] . ':', 26, ' ', STR_PAD_RIGHT ) . $link['template'] . "\n";
	}
	$return .= 'Separator:                ' . novelist_get_option( 'purchase_link_separator', ',&nbsp;' ) . "\n";

	$return = apply_filters( 'novelist/system-info/after/purchase-links', $return );

	// Get plugins that have an update
	$updates = get_plugin_updates();

	// Must-use plugins
	// NOTE: MU plugins can't show updates!
	$muplugins = get_mu_plugins();
	if ( count( $muplugins ) > 0 ) {
		$return .= "\n" . '-- Must-Use Plugins' . "\n\n";

		foreach ( $muplugins as $plugin => $plugin_data ) {
			$return .= $plugin_data['Name'] . ': ' . $plugin_data['Version'] . "\n";
		}

		$return = apply_filters( 'novelist/system-info/after/worpdress-mu-plugins', $return );
	}

	// WordPress active plugins
	$return .= "\n" . '-- WordPress Active Plugins' . "\n\n";

	$plugins        = get_plugins();
	$active_plugins = get_option( 'active_plugins', array() );

	foreach ( $plugins as $plugin_path => $plugin ) {
		if ( ! in_array( $plugin_path, $active_plugins ) ) {
			continue;
		}

		$update = ( array_key_exists( $plugin_path, $updates ) ) ? ' (needs update - ' . $updates[ $plugin_path ]->update->new_version . ')' : '';
		$return .= str_pad( $plugin['Name'] . ': ', 26, ' ', STR_PAD_RIGHT ) . $plugin['Version'] . $update . "\n";
	}

	$return = apply_filters( 'novelist/system-info/after/worpdress-plugins', $return );

	// WordPress inactive plugins
	$return .= "\n" . '-- WordPress Inactive Plugins' . "\n\n";

	foreach ( $plugins as $plugin_path => $plugin ) {
		if ( in_array( $plugin_path, $active_plugins ) ) {
			continue;
		}

		$update = ( array_key_exists( $plugin_path, $updates ) ) ? ' (needs update - ' . $updates[ $plugin_path ]->update->new_version . ')' : '';
		$return .= str_pad( $plugin['Name'] . ': ', 26, ' ', STR_PAD_RIGHT ) . $plugin['Version'] . $update . "\n";
	}

	$return = apply_filters( 'novelist/system-info/after/worpdress-plugins-inactive', $return );

	if ( is_multisite() ) {
		// WordPress Multisite active plugins
		$return .= "\n" . '-- Network Active Plugins' . "\n\n";

		$plugins        = wp_get_active_network_plugins();
		$active_plugins = get_site_option( 'active_sitewide_plugins', array() );

		foreach ( $plugins as $plugin_path ) {
			$plugin_base = plugin_basename( $plugin_path );

			if ( ! array_key_exists( $plugin_base, $active_plugins ) ) {
				continue;
			}

			$update = ( array_key_exists( $plugin_path, $updates ) ) ? ' (needs update - ' . $updates[ $plugin_path ]->update->new_version . ')' : '';
			$plugin = get_plugin_data( $plugin_path );
			$return .= str_pad( $plugin['Name'] . ': ', 26, ' ', STR_PAD_RIGHT ) . $plugin['Version'] . $update . "\n";
		}

		$return = apply_filters( 'novelist/system-info/after/worpdress-multisite-plugins', $return );
	}

	// Server versions
	$return .= "\n" . '-- Webserver Configuration' . "\n\n";
	$return .= 'PHP Version:              ' . PHP_VERSION . "\n";
	$return .= 'MySQL Version:            ' . $wpdb->db_version() . "\n";
	$return .= 'Webserver Info:           ' . $_SERVER['SERVER_SOFTWARE'] . "\n";

	$return = apply_filters( 'novelist/system-info/after/server-versions', $return );

	// PHP configs... now we're getting to the important stuff
	$return .= "\n" . '-- PHP Configuration' . "\n\n";
	$return .= 'Safe Mode:                ' . ( ini_get( 'safe_mode' ) ? 'Enabled' : 'Disabled' . "\n" );
	$return .= 'Memory Limit:             ' . ini_get( 'memory_limit' ) . "\n";
	$return .= 'Upload Max Size:          ' . ini_get( 'upload_max_filesize' ) . "\n";
	$return .= 'Post Max Size:            ' . ini_get( 'post_max_size' ) . "\n";
	$return .= 'Upload Max Filesize:      ' . ini_get( 'upload_max_filesize' ) . "\n";
	$return .= 'Time Limit:               ' . ini_get( 'max_execution_time' ) . "\n";
	$return .= 'Max Input Vars:           ' . ini_get( 'max_input_vars' ) . "\n";
	$return .= 'Display Errors:           ' . ( ini_get( 'display_errors' ) ? 'On (' . ini_get( 'display_errors' ) . ')' : 'N/A' ) . "\n";

	$return = apply_filters( 'novelist/system-info/after/php-config', $return );

	// PHP extensions and such
	$curl_info = function_exists( 'curl_version' ) ? curl_version() : array( 'version' => 'n/a' );
	$return .= "\n" . '-- PHP Extensions' . "\n\n";
	$return .= 'cURL:                     ' . ( function_exists( 'curl_init' ) ? sprintf( 'Supported - version %s', $curl_info['version'] ) : 'Not Supported' ) . "\n";
	$return .= 'fsockopen:                ' . ( function_exists( 'fsockopen' ) ? 'Supported' : 'Not Supported' ) . "\n";
	$return .= 'SOAP Client:              ' . ( class_exists( 'SoapClient' ) ? 'Installed' : 'Not Installed' ) . "\n";
	$return .= 'Suhosin:                  ' . ( extension_loaded( 'suhosin' ) ? 'Installed' : 'Not Installed' ) . "\n";

	$return = apply_filters( 'novelist/system-info/after/php-extensions', $return );

	$return .= "\n" . '### End System Info ###';

	return $return;
}

/**
 * Download System Info
 *
 * Adds all the system info to a .txt file for download.
 *
 * @since 1.0.0
 * @return void
 */
function novelist_tools_download_system_info() {
	if ( ! current_user_can( 'manage_novelist_settings' ) ) {
		return;
	}

	nocache_headers();
	header( 'Content-Type: text/plain' );
	header( 'Content-Disposition: attachment; filename="novelist-system-info.txt"' );

	echo wp_kses_post( $_POST['novelist-system-info'] );
	exit;
}

add_action( 'novelist/download-system-info', 'novelist_tools_download_system_info' );