<?php
/**
 * Register Settings
 *
 * Based on register-settings.php in Easy Digital Downloads.
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
 * Get an Option
 *
 * Looks to see if the specified setting exists, returns the default if not.
 *
 * @param string $key     Key to retrieve
 * @param mixed  $default Default option
 *
 * @global       $novelist_options
 *
 * @since 1.0.0
 * @return mixed
 */
function novelist_get_option( $key = '', $default = false ) {
	global $novelist_options;

	$value = ! empty( $novelist_options[ $key ] ) ? $novelist_options[ $key ] : $default;
	$value = apply_filters( 'novelist/options/get', $value, $key, $default );

	return apply_filters( 'novelist/options/get/' . $key, $value, $key, $default );
}

/**
 * Update an Option
 *
 * Updates an existing setting value in both the DB and the global variable.
 * Passing in an empty, false, or null string value will remove the key from the novelist_settings array.
 *
 * @param string $key   Key to update
 * @param mixed  $value The value to set the key to
 *
 * @global       $novelist_options
 *
 * @since 1.0.0
 * @return bool True if updated, false if not
 */
function novelist_update_option( $key = '', $value = false ) {
	// If no key, exit
	if ( empty( $key ) ) {
		return false;
	}

	if ( empty( $value ) ) {
		$remove_option = novelist_delete_option( $key );

		return $remove_option;
	}

	// First let's grab the current settings
	$options = get_option( 'novelist_settings' );

	// Let's let devs alter that value coming in
	$value = apply_filters( 'novelist/options/update', $value, $key );

	// Next let's try to update the value
	$options[ $key ] = $value;
	$did_update      = update_option( 'novelist_settings', $options );

	// If it updated, let's update the global variable
	if ( $did_update ) {
		global $novelist_options;
		$novelist_options[ $key ] = $value;
	}

	return $did_update;
}

/**
 * Remove an Option
 *
 * Removes an setting value in both the DB and the global variable.
 *
 * @param string $key The key to delete.
 *
 * @global       $novelist_options
 *
 * @since 1.0.0
 * @return boolean True if updated, false if not.
 */
function novelist_delete_option( $key = '' ) {
	// If no key, exit
	if ( empty( $key ) ) {
		return false;
	}

	// First let's grab the current settings
	$options = get_option( 'novelist_settings' );

	// Next let's try to update the value
	if ( isset( $options[ $key ] ) ) {
		unset( $options[ $key ] );
	}

	$did_update = update_option( 'novelist_settings', $options );

	// If it updated, let's update the global variable
	if ( $did_update ) {
		global $novelist_options;
		$novelist_options = $options;
	}

	return $did_update;
}

/**
 * Get Settings
 *
 * Retrieves all plugin settings
 *
 * @since 1.0
 * @return array Novelist settings
 */
function novelist_get_settings() {
	$settings = get_option( 'novelist_settings' );

	if ( empty( $settings ) ) {
		// Update old settings with new single option
		$book_settings     = is_array( get_option( 'novelist_settings_book' ) ) ? get_option( 'novelist_settings_book' ) : array();
		$styles_settings   = is_array( get_option( 'novelist_settings_styles' ) ) ? get_option( 'novelist_settings_styles' ) : array();
		$misc_settings     = is_array( get_option( 'novelist_settings_misc' ) ) ? get_option( 'novelist_settings_misc' ) : array();
		$addon_settings    = is_array( get_option( 'novelist_settings_addons' ) ) ? get_option( 'novelist_settings_addons' ) : array();
		$licenses_settings = is_array( get_option( 'novelist_settings_licenses' ) ) ? get_option( 'novelist_settings_licenses' ) : array();
		$settings          = array_merge( $book_settings, $styles_settings, $misc_settings, $addon_settings, $licenses_settings );

		update_option( 'novelist_settings', $settings );
	}

	return apply_filters( 'novelist/get-settings', $settings );
}

/**
 * Add all settings sections and fields.
 *
 * @since 1.0.0
 * @return void
 */
function novelist_register_settings() {

	if ( false == get_option( 'novelist_settings' ) ) {
		add_option( 'novelist_settings' );
	}

	foreach ( novelist_get_registered_settings() as $tab => $sections ) {
		foreach ( $sections as $section => $settings ) {
			add_settings_section(
				'novelist_settings_' . $tab . '_' . $section,
				__return_null(),
				'__return_false',
				'novelist_settings_' . $tab . '_' . $section
			);

			foreach ( $settings as $option ) {
				// For backwards compatibility
				if ( empty( $option['id'] ) ) {
					continue;
				}

				$name = isset( $option['name'] ) ? $option['name'] : '';

				add_settings_field(
					'novelist_settings[' . $option['id'] . ']',
					$name,
					function_exists( 'novelist_' . $option['type'] . '_callback' ) ? 'novelist_' . $option['type'] . '_callback' : 'novelist_missing_callback',
					'novelist_settings_' . $tab . '_' . $section,
					'novelist_settings_' . $tab . '_' . $section,
					array(
						'section'     => $section,
						'id'          => isset( $option['id'] ) ? $option['id'] : null,
						'desc'        => ! empty( $option['desc'] ) ? $option['desc'] : '',
						'name'        => isset( $option['name'] ) ? $option['name'] : null,
						'size'        => isset( $option['size'] ) ? $option['size'] : null,
						'options'     => isset( $option['options'] ) ? $option['options'] : '',
						'std'         => isset( $option['std'] ) ? $option['std'] : '',
						'min'         => isset( $option['min'] ) ? $option['min'] : null,
						'max'         => isset( $option['max'] ) ? $option['max'] : null,
						'step'        => isset( $option['step'] ) ? $option['step'] : null,
						'chosen'      => isset( $option['chosen'] ) ? $option['chosen'] : null,
						'placeholder' => isset( $option['placeholder'] ) ? $option['placeholder'] : null
					)
				);
			}
		}
	}

	// Creates our settings in the options table
	register_setting( 'novelist_settings', 'novelist_settings', 'novelist_settings_sanitize' );

}

add_action( 'admin_init', 'novelist_register_settings' );

/**
 * Registered Settings
 *
 * Sets and returns the array of all plugin settings.
 * Developers can use the following filters to add their own settings or
 * modify existing ones:
 *
 *  + novelist/settings/{key} - Where {key} is a specific tab. Used to modify a single tab/section.
 *  + novelist/settings/registered-settings - Includes the entire array of all settings.
 *
 * @since 1.0.0
 * @return array
 */
function novelist_get_registered_settings() {

	$novelist_settings = array(
		/* Book Settings */
		'book'     => apply_filters( 'novelist/settings/book', array(
			'main'           => array(
				'book_layout' => array(
					'id'   => 'book_layout',
					'name' => __( 'Book Layout', 'novelist' ),
					'desc' => __( 'Layout for your book.', 'novelist' ),
					'type' => 'book_layout',
					'std'  => novelist_get_default_book_layout_keys()
				)
			),
			'purchase-links' => array(
				'purchase_links'          => array(
					'id'   => 'purchase_links',
					'name' => __( 'Retail Outlets', 'novelist' ),
					'desc' => __( 'Create a new entry for each site you want to display a purchase link for. Each entry also gets a template, which is used to configure the text and HTML that gets displayed on the front-end. <br>The purchase links themselves are then entered on the Edit Book page when you create a book.', 'novelist' ),
					'type' => 'purchase_links',
					'std'  => array(
						array(
							'name'     => __( 'Amazon', 'novelist' ),
							'id'       => 'amazon',
							'template' => __( '<a href="[link]" target="_blank">Amazon</a>', 'novelist' )
						),
						array(
							'name'     => __( 'Barnes & Noble', 'novelist' ),
							'id'       => 'barnes-noble',
							'template' => __( '<a href="[link]" target="_blank">Barnes &amp; Noble</a>', 'novelist' )
						)
					)
				),
				'purchase_link_separator' => array(
					'id'   => 'purchase_link_separator',
					'name' => __( 'Separator', 'novelist' ),
					'desc' => __( 'Whatever you enter in this box will appear in between each purchase link. A space, comma, or line break is typically recommended.', 'novelist' ),
					'type' => 'text',
					'std'  => ',&nbsp;'
				)
			),
			'settings'       => array(
				'default_cover_image' => array(
					'id'   => 'default_cover_image',
					'name' => __( 'Default Cover Image', 'novelist' ),
					'desc' => __( 'This book cover will be used if you don\'t upload one to the book. This is a good place to use a "Cover Coming Soon" placeholder.', 'novelist' ),
					'type' => 'image',
					'std'  => '',
				),
				'cover_image_size'    => array(
					'id'      => 'cover_image_size',
					'name'    => __( 'Cover Image Size', 'novelist' ),
					'desc'    => __( 'Choose a size for your book covers. The images will be resized to your select on single book pages.', 'novelist' ),
					'type'    => 'select',
					'std'     => 'large',
					'options' => novelist_get_image_sizes()
				),
				'link_book_cover'     => array(
					'id'      => 'link_book_cover',
					'name'    => __( 'Link Book Cover', 'novelist' ),
					'desc'    => __( 'Choose where to link the book cover image on the single book page. Or set to "none" to not have a link at all.', 'novelist' ),
					'type'    => 'select',
					'std'     => 'none',
					'options' => novelist_get_cover_link_choices()
				),
				'series_books_layout' => array(
					'id'      => 'series_books_layout',
					'name'    => __( 'Other Books in the Series Layout', 'novelist' ),
					'desc'    => __( 'Choose how to display the list of other books in the series. The grid option will show book covers in a grid style. The link option will display a comma-separated list of text links to each book.', 'novelist' ),
					'type'    => 'select',
					'std'     => 'grid',
					'options' => array(
						'grid'  => __( 'Grid', 'novelist' ),
						'links' => __( 'Text Links', 'novelist' )
					)
				)
			)
		) ),
		/* Styles */
		'styles'   => apply_filters( 'novelist/settings/styles', array(
			'main' => array(
				'disable_styles' => array(
					'id'   => 'disable_styles',
					'name' => __( 'Disable Styles', 'novelist' ),
					'desc' => __( 'Check this to disable the Novelist stylesheet from being added to your site.', 'novelist' ),
					'type' => 'checkbox',
					'std'  => false
				),
				'button_bg'      => array(
					'id'   => 'button_bg',
					'name' => __( 'Button Background Colour', 'novelist' ),
					'desc' => __( 'Choose a background colour for Novelist buttons.', 'novelist' ),
					'type' => 'color',
					'std'  => '#333333'
				),
				'button_text'    => array(
					'id'   => 'button_text',
					'name' => __( 'Button Text Colour', 'novelist' ),
					'desc' => __( 'Choose a text colour for Novelist buttons.', 'novelist' ),
					'type' => 'color',
					'std'  => '#ffffff'
				)
			)
		) ),
		/* Misc */
		'misc'     => apply_filters( 'novelist/settings/misc', array(
			'main' => array(
				'uninstall_on_delete' => array(
					'id'   => 'uninstall_on_delete',
					'name' => __( 'Delete Data', 'novelist' ),
					'desc' => __( 'Check this to delete all Novelist data when deleting the plugin, including settings, books, series, and genres.', 'novelist' ),
					'type' => 'checkbox',
					'std'  => false
				)
			)
		) ),
		/* Add-Ons */
		'addons'   => apply_filters( 'novelist/settings/add-ons', array() ),
		'licenses' => apply_filters( 'novelist/settings/licenses', array() )
	);

	//var_dump($novelist_settings);wp_die();

	return apply_filters( 'novelist/settings/registered-settings', $novelist_settings );

}

/**
 * Sanitize Settings
 *
 * Adds a settings error for the updated message.
 *
 * @param array  $input            The value inputted in the field
 *
 * @global array $novelist_options Array of all the Novelist options
 *
 * @since 1.0.0
 * @return array New, sanitized settings.
 */
function novelist_settings_sanitize( $input = array() ) {

	global $novelist_options;

	if ( empty( $_POST['_wp_http_referer'] ) ) {
		return $input;
	}

	parse_str( $_POST['_wp_http_referer'], $referrer );

	$settings = novelist_get_registered_settings();
	$tab      = ( isset( $referrer['tab'] ) && $referrer['tab'] != 'import_export' ) ? $referrer['tab'] : 'book';
	$section  = isset( $referrer['section'] ) ? $referrer['section'] : 'main';

	// Use first add-on as the section instead of 'main'.
	if ( 'addons' === $tab && 'main' === $section ) {
		if ( function_exists( 'array_key_first' ) ) {
			$section = array_key_first( $settings[ $tab ] );
		} else {
			foreach ( $settings[ $tab ] as $section_key => $section_settings ) {
				$section = $section_key;
				break;
			}
		}
	}

	$input = $input ? $input : array();
	$input = apply_filters( 'novelist/settings/sanitize/' . $tab . '/' . $section, $input );

	// Loop through each setting being saved and pass it through a sanitization filter
	foreach ( $input as $key => $value ) {
		// Get the setting type (checkbox, select, etc)
		$type = isset( $settings[ $tab ][ $section ][ $key ]['type'] ) ? $settings[ $tab ][ $section ][ $key ]['type'] : false;
		if ( $type ) {
			// Field type specific filter
			$input[ $key ] = apply_filters( 'novelist/settings/sanitize/' . $type, $value, $key );
		}
		// General filter
		$input[ $key ] = apply_filters( 'novelist/settings/sanitize', $input[ $key ], $key );
	}

	// Loop through the whitelist and unset any that are empty for the tab being saved
	$main_settings    = $section == 'main' ? $settings[ $tab ] : array();
	$section_settings = ! empty( $settings[ $tab ][ $section ] ) ? $settings[ $tab ][ $section ] : array();
	$found_settings   = array_merge( $main_settings, $section_settings );

	if ( ! empty( $found_settings ) ) {
		foreach ( $found_settings as $key => $value ) {
			if ( empty( $input[ $key ] ) || ! array_key_exists( $key, $input ) ) {
				unset( $novelist_options[ $key ] );
			}
		}
	}

	// Merge our new settings with the existing
	$output = array_merge( $novelist_options, $input );

	add_settings_error( 'novelist-notices', '', __( 'Settings updated.', 'novelist' ), 'updated' );

	return $output;

}

/**
 * Display "Default settings restored" message.
 * This gets displayed after the default settings have been restored and
 * the page has been redirected.
 *
 * @since 1.0.0
 * @return void
 */
function novelist_defaults_restored_message() {
	if ( ! isset( $_GET['page'] ) || $_GET['page'] !== 'novelist-settings' ) {
		return;
	}

	if ( ! isset( $_GET['defaults-restored'] ) || $_GET['defaults-restored'] !== 'true' ) {
		return;
	}

	add_settings_error( 'novelist-notices', '', __( 'Default settings restored.', 'novelist' ), 'updated' );
}

add_action( 'admin_init', 'novelist_defaults_restored_message' );

/**
 * Restore Defaults
 *
 * Ajax callback that restores the default settings for a specific tab.
 *
 * @since 1.0.0
 * @return void
 */
function novelist_restore_default_settings() {
	$tab     = isset($_POST['tab']) ? strip_tags( $_POST['tab'] ) : '';
	$section = isset($_POST['section']) ? strip_tags( $_POST['section'] ) : '';

	if (empty($tab) || empty($section)) {
		return;
	}

	// Nonce check.
	check_ajax_referer('novelist_reset_section_'.$tab.'_'.$section);

	// Permission check.
	if ( ! current_user_can( 'manage_novelist_settings' ) ) {
		wp_die( __( 'Bugger off! You don\'t have permission to do this.', 'novelist' ) );
	}

	global $novelist_options;
	$default_settings = novelist_get_registered_settings();

	// Tab is missing.
	if ( ! array_key_exists( $tab, $default_settings ) ) {
		wp_send_json_error( __( 'Error: Tab missing.', 'novelist' ) );
	}

	if ( ! array_key_exists( $section, $default_settings[ $tab ] ) || ! is_array( $default_settings[ $tab ][ $section ] ) ) {
		wp_send_json_error( __( 'Error: Section missing.', 'novelist' ) );
	}

	// Loop through each section.
	foreach ( $default_settings[ $tab ][ $section ] as $key => $options ) {
		// Special circumstances for the 'book_layout' field.
		if ( $key == 'book_layout' ) {
			$novelist_options[ $key ] = novelist_get_default_book_field_values();

			continue;
		}

		if ( ! array_key_exists( 'std', $options ) ) {
			continue;
		}

		$novelist_options[ $key ] = apply_filters( 'novelist/settings/restore-defaults/' . $key, $options['std'], $options );
	}

	// Update options.
	update_option( 'novelist_settings', apply_filters( 'novelist/settings/restore-defaults', $novelist_options ) );

	// Build our URL
	$url    = admin_url( 'edit.php' );
	$params = array(
		'post_type'         => 'book',
		'page'              => 'novelist-settings',
		'tab'               => urlencode( $tab ),
		'section'           => urlencode( $section ),
		'defaults-restored' => 'true'
	);
	$url    = add_query_arg( $params, $url );

	wp_send_json_success( $url );
}

add_action( 'wp_ajax_novelist_restore_default_settings', 'novelist_restore_default_settings' );

/**
 * Sanitize Text Field
 *
 * @param string $input
 *
 * @since 1.0.0
 * @return string
 */
function novelist_settings_sanitize_text_field( $input ) {
	return wp_kses_post( $input );
}

add_filter( 'novelist/settings/sanitize/text', 'novelist_settings_sanitize_text_field' );

/**
 * Sanitize Number Field
 *
 * @param int $input
 *
 * @since 1.0.0
 * @return int
 */
function novelist_settings_sanitize_number_field( $input ) {
	return intval( $input );
}

add_filter( 'novelist/settings/sanitize/number', 'novelist_settings_sanitize_number_field' );

/**
 * Sanitize Dimension Field
 *
 * @param array $input
 *
 * @since 1.0.0
 * @return array
 */
function novelist_settings_sanitize_dimensions_field( $input ) {
	$new_array = array(
		'width'  => 0,
		'height' => 0
	);

	if ( ! is_array( $input ) ) {
		return $new_array;
	}

	// Width
	if ( array_key_exists( 'width', $input ) ) {
		$new_array['width'] = absint( $input['width'] );
	}

	// Height
	if ( array_key_exists( 'height', $input ) ) {
		$new_array['height'] = absint( $input['height'] );
	}

	// Crop
	if ( array_key_exists( 'crop', $input ) ) {
		$new_array['crop'] = ( $input['crop'] == 'yes' ) ? true : false;
	}

	return $new_array;
}

add_filter( 'novelist/settings/sanitize/dimensions', 'novelist_settings_sanitize_dimensions_field' );

/**
 * Sanitize Select Field
 *
 * @param string $input
 *
 * @since 1.0.0
 * @return string
 */
function novelist_settings_sanitize_select_field( $input ) {
	return sanitize_text_field( $input );
}

add_filter( 'novelist/settings/sanitize/select', 'novelist_settings_sanitize_select_field' );

/**
 * Sanitize Color Field
 *
 * Return 3 or 6 hex digits, or an empty string.
 *
 * @param string $input
 *
 * @since 1.0.0
 * @return string
 */
function novelist_settings_sanitize_color_field( $input ) {
	if ( ! empty( $input ) && preg_match( '|^#([A-Fa-f0-9]{3}){1,2}$|', $input ) ) {
		return $input;
	}

	return '';
}

add_filter( 'novelist/settings/sanitize/color', 'novelist_settings_sanitize_color_field' );

/**
 * Sanitize Checkbox Field
 *
 * Returns either true or false.
 *
 * @param bool $input
 *
 * @since 1.0.0
 * @return bool
 */
function novelist_settings_sanitize_checkbox_field( $input ) {
	return ! empty( $input ) ? true : false;
}

add_filter( 'novelist/settings/sanitize/checkbox', 'novelist_settings_sanitize_checkbox_field' );

/**
 * Sanitize Image Field
 *
 * Returns a positive integer.
 *
 * @param int $input
 *
 * @since 1.1.0
 * @return int
 */
function novelist_settings_sanitize_image_field( $input ) {
	return absint( $input );
}

add_filter( 'novelist/settings/sanitize/image', 'novelist_settings_sanitize_image_field' );

/**
 * Sanitize Book Layout
 *
 * @param array $input
 *
 * @since 1.0.0
 * @return array
 */
function novelist_settings_sanitize_novelist_book_layout($input)
{
    // Check permissions.
    if (! current_user_can('manage_novelist_settings')) {
        return $input;
    }

    $new_input = array();

    foreach ($input as $key => $value) {
        if (! is_array($value) || (array_key_exists('disabled', $value) && $value['disabled'] == 'true')) {
            continue;
        }

        if (array_key_exists('disabled', $value)) {
            unset($value['disabled']);
        }

        foreach($value as $valueKey => $valueLabel) {
            if ($valueKey === 'label') {
                $value[$valueKey] = wp_kses_post($valueLabel);
            } else {
                $value[$valueKey] = wp_strip_all_tags($valueLabel);
            }
        }

        $new_input[$key] = $value;
    }

    return $new_input;
}

add_filter( 'novelist/settings/sanitize/book_layout', 'novelist_settings_sanitize_novelist_book_layout' );

/**
 * Sanitize Purchase Links
 *
 * @param array $input
 *
 * @since 1.0.0
 * @return array
 */
function novelist_settings_sanitize_purchase_links( $input ) {
	$new_input = array();

	if ( ! is_array( $input ) ) {
		return $new_input;
	}

	foreach ( $input as $settings ) {
		if ( ! is_array( $settings ) || ! array_key_exists( 'name', $settings ) || ! array_key_exists( 'template', $settings ) ) {
			continue;
		}

		$id = ( array_key_exists( 'id', $settings ) && $settings['id'] ) ? $settings['id'] : $settings['name'];

		$new_settings = apply_filters( 'novelist/settings/sanitize/purchase_links/new-settings', array(
			'name'     => trim( sanitize_text_field( $settings['name'] ) ),
			'id'       => trim( sanitize_title( $id ) ),
			'template' => wp_kses_post( $settings['template'] )
		), $settings );

		$new_input[] = $new_settings;
	}

	return $new_input;
}

add_filter( 'novelist/settings/sanitize/purchase_links', 'novelist_settings_sanitize_purchase_links' );

/**
 * Retrieve settings tabs
 *
 * @since 1.0.0
 * @return array $tabs
 */
function novelist_get_settings_tabs() {
	$settings = novelist_get_registered_settings();

	$tabs           = array();
	$tabs['book']   = __( 'Book Layout', 'novelist' );
	$tabs['styles'] = __( 'Styles', 'novelist' );
	$tabs['misc']   = __( 'Misc', 'novelist' );

	if ( ! empty( $settings['addons'] ) ) {
		$tabs['addons'] = __( 'Add-Ons', 'novelist' );
	}

	if ( ! empty( $settings['licenses'] ) ) {
		$tabs['licenses'] = __( 'Licenses', 'novelist' );
	}

	return apply_filters( 'novelist/settings/tabs', $tabs );
}


/**
 * Retrieve settings tabs
 *
 * @since 1.0.0
 * @return array|false
 */
function novelist_get_settings_tab_sections( $tab = false ) {
	$tabs     = false;
	$sections = novelist_get_registered_settings_sections();

	if ( $tab && ! empty( $sections[ $tab ] ) ) {
		$tabs = $sections[ $tab ];
	} else if ( $tab ) {
		$tabs = false;
	}

	return $tabs;
}

/**
 * Get the settings sections for each tab
 * Uses a static to avoid running the filters on every request to this function
 *
 * @since  1.0.0
 * @return array Array of tabs and sections
 */
function novelist_get_registered_settings_sections() {
	static $sections = false;

	if ( false !== $sections ) {
		return $sections;
	}

	$sections = array(
		'book'     => apply_filters( 'novelist/settings/sections/book', array(
			'main'           => __( 'Book Layout', 'novelist' ),
			'purchase-links' => __( 'Purchase Links', 'novelist' ),
			'settings'       => __( 'Settings', 'novelist' )
		) ),
		'styles'   => apply_filters( 'novelist/settings/sections/styles', array(
			'main' => __( 'Styles', 'novelist' )
		) ),
		'misc'     => apply_filters( 'novelist/settings/sections/misc', array(
			'main' => __( 'Misc', 'novelist' ),
		) ),
		'addons'   => apply_filters( 'novelist/settings/sections/addons', array() ),
		'licenses' => apply_filters( 'novelist/settings/sections/licenses', array() )
	);

	$sections = apply_filters( 'novelist/settings/sections', $sections );

	return $sections;
}

/**
 * Sanitizes a string key for Novelist Settings
 *
 * Keys are used as internal identifiers. Alphanumeric characters, dashes, underscores, stops, colons and slashes are
 * allowed
 *
 * @param  string $key String key
 *
 * @since 1.0.0
 * @return string Sanitized key
 */
function novelist_sanitize_key( $key ) {
	$raw_key = $key;
	$key     = preg_replace( '/[^a-zA-Z0-9_\-\.\:\/]/', '', $key );

	return apply_filters( 'novelist/sanitize-key', $key, $raw_key );
}


/*
 * Callbacks
 */

/**
 * Missing Callback
 *
 * If a function is missing for settings callbacks alert the user.
 *
 * @param array $args Arguments passed by the setting
 *
 * @since 1.0.0
 * @return void
 */
function novelist_missing_callback( $args ) {
	printf(
		__( 'The callback function used for the %s setting is missing.', 'novelist' ),
		'<strong>' . $args['id'] . '</strong>'
	);
}

/**
 * Text Callback
 *
 * Renders text fields.
 *
 * @param array  $args             Arguments passed by the setting
 *
 * @global array $novelist_options Array of all the Novelist settings
 *
 * @since 1.0.0
 * @return void
 */
function novelist_text_callback( $args ) {
	global $novelist_options;

	if ( isset( $novelist_options[ $args['id'] ] ) ) {
		$value = $novelist_options[ $args['id'] ];
	} else {
		$value = isset( $args['std'] ) ? $args['std'] : '';
	}

	if ( isset( $args['faux'] ) && true === $args['faux'] ) {
		$args['readonly'] = true;
		$value            = isset( $args['std'] ) ? $args['std'] : '';
		$name             = '';
	} else {
		$name = 'name="novelist_settings[' . esc_attr( $args['id'] ) . ']"';
	}

	$readonly = ( array_key_exists( 'readonly', $args ) && $args['readonly'] === true ) ? ' readonly="readonly"' : '';
	$size     = ( isset( $args['size'] ) && ! is_null( $args['size'] ) ) ? $args['size'] : 'regular';
	?>
	<input type="text" class="<?php echo sanitize_html_class( $size ); ?>-text" id="novelist_settings[<?php echo novelist_sanitize_key( $args['id'] ); ?>]" <?php echo $name; ?> value="<?php echo esc_attr( stripslashes( $value ) ); ?>"<?php echo $readonly; ?>>
	<label for="novelist_settings[<?php echo novelist_sanitize_key( $args['id'] ); ?>]" class="desc"><?php echo wp_kses_post( $args['desc'] ); ?></label>
	<?php
}

/**
 * Number Callback
 *
 * Renders number fields.
 *
 * @param array  $args             Arguments passed by the setting
 *
 * @global array $novelist_options Array of all the Novelist settings
 *
 * @since 1.0.0
 * @return void
 */
function novelist_number_callback( $args ) {
	global $novelist_options;

	if ( isset( $novelist_options[ $args['id'] ] ) ) {
		$value = $novelist_options[ $args['id'] ];
	} else {
		$value = isset( $args['std'] ) ? $args['std'] : '';
	}

	if ( isset( $args['faux'] ) && true === $args['faux'] ) {
		$args['readonly'] = true;
		$value            = isset( $args['std'] ) ? $args['std'] : '';
		$name             = '';
	} else {
		$name = 'name="novelist_settings[' . esc_attr( $args['id'] ) . ']"';
	}

	$readonly = ( array_key_exists( 'readonly', $args ) && $args['readonly'] === true ) ? ' readonly="readonly"' : '';
	?>
	<input type="number" id="novelist_settings[<?php echo novelist_sanitize_key( $args['id'] ); ?>]" <?php echo $name; ?> value="<?php echo esc_attr( stripslashes( $value ) ); ?>"<?php echo $readonly; ?>>
	<label for="novelist_settings[<?php echo novelist_sanitize_key( $args['id'] ); ?>]" class="desc"><?php echo wp_kses_post( $args['desc'] ); ?></label>
	<?php
}

/**
 * Dimensions Callback
 *
 * Renders the image dimensions field.
 *
 * @param array  $args             Arguments passed by the setting
 *
 * @global array $novelist_options Array of all the Novelist settings
 *
 * @since 1.0.0
 * @return void
 */
function novelist_dimensions_callback( $args ) {
	global $novelist_options;

	if ( isset( $novelist_options[ $args['id'] ] ) ) {
		$value = $novelist_options[ $args['id'] ];
	} else {
		$value = isset( $args['std'] ) ? $args['std'] : '';
	}

	$defaults = array(
		'width'  => 0,
		'height' => 0
	);

	$value = wp_parse_args( $value, $defaults );
	?>
	<div class="novelist-dimensions-wrap">
		<div class="novelist-dimensions-item novelist-dimensions-width">
			<input type="number" id="novelist_settings[<?php echo novelist_sanitize_key( $args['id'] ); ?>][width]" name="novelist_settings[<?php echo novelist_sanitize_key( $args['id'] ); ?>][width]" value="<?php echo esc_attr( stripslashes( $value['width'] ) ); ?>">
			<label for="novelist_settings[<?php echo novelist_sanitize_key( $args['id'] ); ?>][width]" class="desc"><?php esc_html_e( 'Width, in pixels.', 'novelist' ); ?></label>
		</div>

		<div class="novelist-dimensions-item novelist-dimensions-height">
			<input type="number" id="novelist_settings[<?php echo novelist_sanitize_key( $args['id'] ); ?>][height]" name="novelist_settings[<?php echo novelist_sanitize_key( $args['id'] ); ?>][height]" value="<?php echo esc_attr( stripslashes( $value['height'] ) ); ?>">
			<label for="novelist_settings[<?php echo novelist_sanitize_key( $args['id'] ); ?>][height]" class="desc"><?php esc_html_e( 'Height, in pixels.', 'novelist' ); ?></label>
		</div>

		<?php if ( array_key_exists( 'crop', $value ) ) : ?>
			<div class="novelist-dimensions-item novelist-dimensions-crop">
				<select id="novelist_settings[<?php echo novelist_sanitize_key( $args['id'] ); ?>][crop]" name="novelist_settings[<?php echo novelist_sanitize_key( $args['id'] ); ?>][crop]">
					<option value="yes" <?php selected( $value['crop'], true ); ?>><?php esc_html_e( 'Crop' ); ?></option>
					<option value="no" <?php selected( $value['crop'], false ); ?>><?php esc_html_e( 'Do not crop' ); ?></option>
				</select>
				<label for="novelist_settings[<?php echo novelist_sanitize_key( $args['id'] ); ?>][crop]" class="desc"><?php esc_html_e( 'If you crop to exact dimensions, some parts of the image may be cut off.', 'novelist' ); ?></label>
			</div>
		<?php endif; ?>
	</div>
	<?php
}

/**
 * Textarea Callback
 *
 * Renders textarea fields.
 *
 * @param array  $args             Arguments passed by the setting
 *
 * @global array $novelist_options Array of all the Novelist settings
 *
 * @since 1.0.0
 * @return void
 */
function novelist_textarea_callback( $args ) {
	global $novelist_options;

	if ( isset( $novelist_options[ $args['id'] ] ) ) {
		$value = $novelist_options[ $args['id'] ];
	} else {
		$value = isset( $args['std'] ) ? $args['std'] : '';
	}
	?>
	<textarea class="large-text" id="novelist_settings[<?php echo novelist_sanitize_key( $args['id'] ); ?>]" name="novelist_settings[<?php echo esc_attr( $args['id'] ); ?>]" rows="10" cols="50"><?php echo esc_textarea( $value ); ?></textarea>
	<label for="novelist_settings[<?php echo novelist_sanitize_key( $args['id'] ); ?>]" class="desc"><?php echo wp_kses_post( $args['desc'] ); ?></label>
	<?php
}

/**
 * Checkbox Callback
 *
 * Renders checkbox fields.
 *
 * @param array  $args             Arguments passed by the setting
 *
 * @global array $novelist_options Array of all the Novelist settings
 *
 * @since 1.0.0
 * @return void
 */
function novelist_checkbox_callback( $args ) {
	global $novelist_options;

	$checked = ( isset( $novelist_options[ $args['id'] ] ) && ! empty( $novelist_options[ $args['id'] ] ) ) ? checked( 1, $novelist_options[ $args['id'] ], false ) : '';
	?>
	<input type="checkbox" id="novelist_settings[<?php echo novelist_sanitize_key( $args['id'] ); ?>]" name="novelist_settings[<?php echo novelist_sanitize_key( $args['id'] ); ?>]" value="1" <?php echo $checked; ?>>
	<label for="novelist_settings[<?php echo novelist_sanitize_key( $args['id'] ); ?>]" class="desc"><?php echo wp_kses_post( $args['desc'] ); ?></label>
	<?php
}

/**
 * Callback: Book Layout
 *
 * @param array $args
 *
 * @since 1.0.0
 * @return void
 */
function novelist_book_layout_callback( $args ) {
	$all_fields     = novelist_get_book_fields();
	$enabled_fields = novelist_get_option( $args['id'], false );

	// If we don't have fields already saved, let's use the default values.
	if ( ! is_array( $enabled_fields ) && array_key_exists( 'std', $args ) && is_array( $args['std'] ) ) {

		$enabled_fields = novelist_get_default_book_field_values( $all_fields );

	} elseif ( ! is_array( $enabled_fields ) ) {
		$enabled_fields = array();
	}
	?>
	<div id="book-layout-builder">

		<div id="enabled-book-settings">
			<h3 class="novelist-no-sort"><?php _e( 'Your Layout', 'novelist' ); ?></h3>
			<div id="enabled-book-settings-inner" class="novelist-sortable novelist-sorter-enabled-column">
				<?php foreach ( $enabled_fields as $key => $options ) : ?>
					<?php novelist_format_book_layout_option( $key, $options, $all_fields, $enabled_fields, 'false' ); ?>
				<?php endforeach; ?>
			</div>
		</div>

		<div id="available-book-settings">
			<h3 class="novelist-no-sort"><?php _e( 'Available', 'novelist' ); ?></h3>
			<div id="available-book-settings-inner" class="novelist-sortable">
				<?php foreach ( $all_fields as $key => $options ) : ?>
					<?php
					if ( ! array_key_exists( $key, $enabled_fields ) ) {
						novelist_format_book_layout_option( $key, $options, $all_fields, $enabled_fields, 'true' );
					}
					?>
				<?php endforeach; ?>
			</div>
		</div>

	</div>

	<!--<button type="button" class="button button-secondary"><?php _e( 'Preview Book', 'novelist' ); ?></button>-->
	<?php
}

/**
 * Format Book Layout
 *
 * Formats the layout of each book information option used in the book layout.
 *
 * @see   novelist_book_layout_callback()
 *
 * @param string $key
 * @param array  $options
 * @param array  $all_fields
 * @param array  $enabled_fields
 * @param string $disabled
 *
 * @since 1.0.0
 * @return void
 */
function novelist_format_book_layout_option( $key = '', $options = array(), $all_fields = array(), $enabled_fields = array(), $disabled = 'false' ) {
	if ( ! array_key_exists( $key, $all_fields ) ) {
		return;
	}

	$classes = 'novelist-book-option';
	if ( $key == 'cover' && array_key_exists( 'alignment', $options ) ) {
		$classes .= ' novelist-book-cover-align-' . $options['alignment'];
	}

	$label          = ( array_key_exists( $key, $enabled_fields ) && array_key_exists( 'label', $enabled_fields[ $key ] ) ) ? $enabled_fields[ $key ]['label'] : $all_fields[ $key ]['label'];
	$displayed_text = ( $disabled == 'true' || empty( $label ) ) ? esc_html( $all_fields[ $key ]['name'] ) : $label;
	$newline        = ( array_key_exists( $key, $enabled_fields ) && array_key_exists( 'linebreak', $enabled_fields[ $key ] ) ) ? $enabled_fields[ $key ]['linebreak'] : false;
	$disable_edit   = ( array_key_exists( 'disable-edit', $all_fields[ $key ] ) && $all_fields[ $key ]['disable-edit'] ) ? true : false;
	?>
	<div id="novelist-book-option-<?php echo esc_attr( $key ); ?>" class="<?php echo esc_attr( $classes ); ?>">
		<span class="novelist-book-option-title"><?php echo strip_tags( $displayed_text, '<a><img><strong><b><em><i>' ); ?></span>
		<span class="novelist-book-option-name"><?php echo esc_html( $all_fields[ $key ]['name'] ); ?></span>
		<?php if ( $disable_edit === false ) : ?>
			<button type="button" class="novelist-book-option-toggle"><?php _e( 'Edit', 'novelist' ); ?></button>
		<?php endif; ?>

		<div class="novelist-book-option-fields">
			<label for="novelist_settings[book_layout][<?php echo esc_attr( $key ); ?>][label]"><?php printf( __( 'Use <mark>%1$s</mark> as a placeholder for the %2$s', 'novelist' ), $all_fields[ $key ]['placeholder'], strtolower( $all_fields[ $key ]['name'] ) ); ?></label>
			<textarea class="novelist-book-option-label" id="novelist_settings[book_layout][<?php echo esc_attr( $key ); ?>][label]" name="novelist_settings[book_layout][<?php echo esc_attr( $key ); ?>][label]"><?php echo esc_textarea( $label ); ?></textarea>
			<input type="hidden" class="novelist-book-option-disabled" name="novelist_settings[book_layout][<?php echo esc_attr( $key ); ?>][disabled]" value="<?php echo esc_attr( $disabled ); ?>">

			<?php if ( $key != 'cover' ) : ?>
				<div class="novelist-new-line-option">
					<input type="checkbox" id="novelist_settings[book_layout][<?php echo esc_attr( $key ); ?>][linebreak]" name="novelist_settings[book_layout][<?php echo esc_attr( $key ); ?>][linebreak]" value="on" <?php checked( $newline, 'on' ); ?>>
					<label for="novelist_settings[book_layout][<?php echo esc_attr( $key ); ?>][linebreak]"><?php _e( 'Add new line after this field', 'novelist' ); ?></label>
				</div>
			<?php endif; ?>

			<?php if ( $key == 'cover' ) : ?>
				<?php $alignment = ( array_key_exists( $key, $enabled_fields ) && array_key_exists( 'alignment', $enabled_fields[ $key ] ) ) ? $enabled_fields[ $key ]['alignment'] : $all_fields[ $key ]['alignment']; ?>
				<label for="novelist-book-layout-cover-changer"><?php _e( 'Cover Alignment', 'novelist' ); ?></label>
				<select id="novelist-book-layout-cover-changer" name="novelist_settings[book_layout][cover][alignment]">
					<?php foreach ( novelist_book_alignment_options() as $key => $value ) : ?>
						<option value="<?php echo esc_attr( $key ); ?>" <?php selected( $alignment, $key ); ?>><?php echo esc_html( $value ); ?></option>
					<?php endforeach; ?>
				</select>
			<?php endif; ?>
		</div>
	</div>
	<?php
}

/**
 * Callback: Purchase Links
 *
 * @param array  $args
 *
 * @global array $novelist_options
 *
 * @since 1.0.0
 * @return void
 */
function novelist_purchase_links_callback( $args ) {
	global $novelist_options;

	if ( isset( $novelist_options[ $args['id'] ] ) ) {
		$value = $novelist_options[ $args['id'] ];
	} else {
		$value = isset( $args['std'] ) ? $args['std'] : array();
	}

	if ( ! is_array( $value ) ) {
		return;
	}

	$i = 1;
	$j = 0;

	?>
	<table id="novelist-purchase-links" class="wp-list-table widefat fixed posts">
		<thead>
		<tr>
			<th id="novelist-retail-store-name"><?php _e( 'Retail Store Name', 'novelist' ); ?></th>
			<th id="novelist-retail-store-id"><?php _e( 'Store ID', 'novelist' ); ?>
				<a href="https://novelistplugin.com/docs/general/plugin-settings/store-id/" target="_blank" title="<?php esc_attr_e( 'Learn about store IDs', 'novelist' ); ?>"><span class="dashicons dashicons-editor-help"></span></a>
			</th>
			<th id="novelist-link-template"><?php _e( 'Template', 'novelist' ); ?></th>
			<?php do_action( 'novelist/settings/purchase-links-callback/before-remove-header', $args ); ?>
			<th id="novelist-link-remove"><?php _e( 'Remove', 'novelist' ); ?></th>
			<th id="novelist-link-order"><?php _e( 'Order', 'novelist' ); ?></th>
			<?php do_action( 'novelist/settings/purchase-links-callback/after-remove-header', $args ); ?>
		</tr>
		</thead>
		<tbody>
		<?php foreach ( $value as $link ) :
			$name = isset( $link['name'] ) ? $link['name'] : '';
			$id = isset( $link['id'] ) ? $link['id'] : esc_attr( sanitize_title( $name ) );
			$template = isset( $link['template'] ) ? $link['template'] : '';
			?>
			<tr id="novelist_purchase_links_<?php echo esc_attr( $i ); ?>" class="novelist-cloned">
				<td>
					<label for="novelist_settings[<?php echo esc_attr( $args['id'] ); ?>]_name_<?php echo $i; ?>" class="screen-reader-text"><?php _e( 'Enter the name of the retail outlet', 'novelist' ); ?></label>
					<input type="text" class="regular-text" id="novelist_settings[<?php echo esc_attr( $args['id'] ); ?>]_name_<?php echo $i; ?>" name="novelist_settings[<?php echo esc_attr( $args['id'] ); ?>][<?php echo $j; ?>][name]" value="<?php esc_attr_e( stripslashes( $name ) ); ?>">
				</td>
				<td>
					<label for="novelist_settings[<?php echo esc_attr( $args['id'] ); ?>]_id_<?php echo $i; ?>" class="screen-reader-text"><?php _e( 'Enter the ID', 'novelist' ); ?></label>
					<input type="text" class="regular-text" id="novelist_settings[<?php echo esc_attr( $args['id'] ); ?>]_id_<?php echo $i; ?>" name="novelist_settings[<?php echo esc_attr( $args['id'] ); ?>][<?php echo $j; ?>][id]" value="<?php esc_attr_e( stripslashes( $id ) ); ?>">
				</td>
				<td>
					<label for="novelist_settings[<?php echo esc_attr( $args['id'] ); ?>]_template_<?php echo $i; ?>" class="screen-reader-text"><?php _e( 'Template for this site', 'novelist' ); ?></label>
					<input type="text" class="regular-text" id="novelist_settings[<?php echo esc_attr( $args['id'] ); ?>]_template_<?php echo $i; ?>" name="novelist_settings[<?php echo esc_attr( $args['id'] ); ?>][<?php echo $j; ?>][template]" value="<?php esc_attr_e( stripslashes( $template ) ); ?>">
				</td>
				<?php do_action( 'novelist/settings/purchase-links-callback/before-remove-row', $args, $link, $i, $j ); ?>
				<td>
					<button class="button-secondary novelist-remove-link" onclick="<?php echo ( $i > 1 ) ? 'jQuery(this).parent().parent().remove(); return false' : 'return false'; ?>"><?php _e( 'Remove', 'novelist' ); ?></button>
				</td>
				<td>
					<a href="#" class="novelist-drag-handle"><span class="dashicons dashicons-move"></span></a>
				</td>
				<?php do_action( 'novelist/settings/purchase-links-callback/after-remove-row', $args, $link, $i, $j ); ?>
			</tr>
			<?php
			$i ++;
			$j ++;
			?>
		<?php endforeach; ?>
		</tbody>
	</table>

	<div id="novelist-clone-buttons">
		<button id="novelist-add-link" class="button button-secondary" rel=".novelist-cloned"><?php _e( 'Add Link', 'novelist' ); ?></button>
	</div>
	<?php
}

/**
 * Callback: Color
 *
 * @param array  $args
 *
 * @global array $novelist_options
 *
 * @since 1.0.0
 * @return void
 */
function novelist_color_callback( $args ) {
	global $novelist_options;

	if ( isset( $novelist_options[ $args['id'] ] ) ) {
		$value = $novelist_options[ $args['id'] ];
	} else {
		$value = isset( $args['std'] ) ? $args['std'] : '';
	}

	$default = isset( $args['std'] ) ? $args['std'] : '';
	?>
	<input type="text" class="novelist-color-picker" id="novelist_settings[<?php echo novelist_sanitize_key( $args['id'] ); ?>]" name="novelist_settings[<?php echo esc_attr( $args['id'] ); ?>]" value="<?php echo esc_attr( $value ); ?>" data-default-color="<?php echo esc_attr( $default ); ?>">
	<label for="novelist_settings[<?php echo novelist_sanitize_key( $args['id'] ); ?>]" class="desc"><?php echo wp_kses_post( $args['desc'] ); ?></label>
	<?php
}

/**
 * Callback: Select
 *
 * @param array  $args
 *
 * @global array $novelist_options
 *
 * @since 1.0.0
 * @return void
 */
function novelist_select_callback( $args ) {
	global $novelist_options;

	if ( isset( $novelist_options[ $args['id'] ] ) ) {
		$value = $novelist_options[ $args['id'] ];
	} else {
		$value = isset( $args['std'] ) ? $args['std'] : '';
	}

	if ( ! is_array( $args['options'] ) ) {
		?>
		<p><?php _e( 'Missing select options argument.', 'novelist' ); ?></p>
		<?php
		return;
	}
	?>
	<select name="novelist_settings[<?php echo novelist_sanitize_key( $args['id'] ); ?>]" id="novelist_settings[<?php echo novelist_sanitize_key( $args['id'] ); ?>]">
		<?php foreach ( $args['options'] as $key => $option ) : ?>
			<option value="<?php echo novelist_sanitize_key( $key ); ?>"<?php selected( $key, $value ); ?>><?php echo esc_html( $option ); ?></option>
		<?php endforeach; ?>
	</select>
	<label for="novelist_settings[<?php echo novelist_sanitize_key( $args['id'] ); ?>]" class="desc"><?php echo wp_kses_post( $args['desc'] ); ?></label>
	<?php
}

/**
 * Callback: Image
 *
 * @param array  $args
 *
 * @global array $novelist_options
 *
 * @since 1.1.0
 * @return void
 */
function novelist_image_callback( $args ) {
	global $novelist_options;

	if ( isset( $novelist_options[ $args['id'] ] ) ) {
		$value = $novelist_options[ $args['id'] ];
	} else {
		$value = isset( $args['std'] ) ? $args['std'] : '';
	}
	?>
	<?php
	if ( ! empty( $value ) && (int) $value !== 0 ) {
		$attr = array(
			'id'    => 'noveliset_settings_' . novelist_sanitize_key( $args['id'] ) . '_image',
			'style' => ''
		);

		echo wp_get_attachment_image( intval( $value ), 'medium', false, $attr );
	} else {
		?>
		<img id="novelist_settings_<?php echo novelist_sanitize_key( $args['id'] ); ?>_image" src="" style="display: none;">
		<?php
	}
	?>

	<div class="novelist-media-upload-fields">
		<input type="button" value="<?php _e( 'Upload Image', 'novelist' ); ?>" class="button novelist_upload_image_button" id="novelist_settings_<?php echo novelist_sanitize_key( $args['id'] ); ?>_upload" onclick="return novelist_open_uploader('novelist_settings_<?php echo novelist_sanitize_key( $args['id'] ); ?>', 'medium');">
		<input type="button" value="<?php _e( 'Remove Image', 'novelist' ); ?>" class="button novelist_image_remove_button" id="novelist_settings_<?php echo novelist_sanitize_key( $args['id'] ); ?>_remove" onclick="return novelist_clear_uploader('novelist_settings_<?php echo novelist_sanitize_key( $args['id'] ); ?>');" style="<?php echo empty( $value ) ? 'display: none;' : ''; ?>">
	</div>

	<input type="hidden" name="novelist_settings[<?php echo novelist_sanitize_key( $args['id'] ); ?>]" id="novelist_settings_<?php echo novelist_sanitize_key( $args['id'] ); ?>" value="<?php echo esc_attr( $value ); ?>">

	<label for="novelist_settings[<?php echo novelist_sanitize_key( $args['id'] ); ?>]" class="desc"><?php echo wp_kses_post( $args['desc'] ); ?></label>
	<?php
}

/**
 * Callback: Header
 *
 * @param array  $args
 *
 * @global array $novelist_options
 *
 * @since 1.0.0
 * @return void
 */
function novelist_header_callback( $args ) {
	if ( array_key_exists( 'desc', $args ) && ! empty( $args['desc'] ) ) {
		echo '<div class="desc">' . wp_kses_post( $args['desc'] ) . '</div>';
	}
}

/**
 * Callback: Raw
 *
 * Displays the contents of a file (or the contents of `$args[std]`).
 *
 * @param array $args
 *
 * @since 1.1.0
 * @return void
 */
function novelist_raw_callback( $args ) {
	if ( file_exists( $args['std'] ) ) {
		include_once $args['std'];
	} else {
		echo $args['std'];
	}
}

/**
 * Callback: License Key
 *
 * @param array  $args
 *
 * @global array $novelist_options
 *
 * @since 1.0.0
 * @return void
 */
function novelist_license_key_callback( $args ) {
	global $novelist_options;

	$messages = array();
	$class    = '';
	$license  = get_option( $args['options']['is_valid_license_option'] );

	if ( isset( $novelist_options[ $args['id'] ] ) ) {
		$value = $novelist_options[ $args['id'] ];
	} else {
		$value = isset( $args['std'] ) ? $args['std'] : '';
	}

	if ( ! empty( $license ) && is_object( $license ) ) {

		if ( false === $license->success ) {

			switch ( $license->error ) {

				case 'expired' :

					$class      = 'error';
					$messages[] = sprintf(
						__( 'Your license key expired on %1$s. Please <a href="%2$s" target="_blank" title="Renew your license key">renew your license key</a>.', 'novelist' ),
						date_i18n( get_option( 'date_format' ), strtotime( $license->expires, current_time( 'timestamp' ) ) ),
						'https://novelistplugin.com/checkout/?edd_license_key=' . urlencode( $value ) . '&utm_campaign=admin&utm_source=licenses&utm_medium=expired'
					);

					$license_status = 'license-' . $class . '-notice';

					break;

				case 'missing' :

					$class      = 'error';
					$messages[] = sprintf(
						__( 'Invalid license. Please <a href="%s" target="_blank" title="Visit account page">visit your account page</a> and verify it.', 'novelist' ),
						'https://novelistplugin.com/account/?utm_campaign=admin&utm_source=licenses&utm_medium=missing'
					);

					$license_status = 'license-' . $class . '-notice';

					break;

				case 'invalid' :
				case 'site_inactive' :

					$class      = 'error';
					$messages[] = sprintf(
						__( 'Your %1$s is not active for this URL. Please <a href="%2$s" target="_blank" title="Visit account page">visit your account page</a> to manage your license key URLs.', 'novelist' ),
						$args['name'],
						'https://novelistplugin.com/account/?utm_campaign=admin&utm_source=licenses&utm_medium=invalid'
					);

					$license_status = 'license-' . $class . '-notice';

					break;

				case 'item_name_mismatch' :

					$class      = 'error';
					$messages[] = sprintf(
						__( 'This is not a %s.', 'novelist' ),
						$args['name']
					);

					$license_status = 'license-' . $class . '-notice';

					break;

				case 'no_activations_left' :

					$class      = 'error';
					$messages[] = sprintf(
						__( 'Your license key has reached its activation limit. <a href="%s" target="_blank" title="View upgrades">View possible upgrades.</a>', 'novelist' ),
						'https://novelistplugin.com/account/?utm_campaign=admin&utm_source=licenses&utm_medium=no_activations_left'
					);

					$license_status = 'license-' . $class . '-notice';

					break;

			}

		} else {

			$class      = 'valid';
			$now        = current_time( 'timestamp' );
			$expiration = strtotime( $license->expires, current_time( 'timestamp' ) );

			if ( 'lifetime' === $license->expires ) {

				$messages[]     = __( 'License key never expires.', 'novelist' );
				$license_status = 'license-lifetime-notice';

			} elseif ( $expiration > $now && $expiration - $now < ( DAY_IN_SECONDS * 30 ) ) {

				$messages[] = sprintf(
					__( 'Your license key is about to expire! It expires on %1$s. <a href="%2$s" target="_blank" title="Renew license key">Renew your license key</a> to continue getting updates and support.', 'novelist' ),
					date_i18n( get_option( 'date_format' ), strtotime( $license->expires, current_time( 'timestamp' ) ) ),
					'https://novelistplugin.com/checkout/?edd_license_key=' . urlencode( $value ) . '&utm_campaign=admin&utm_source=licenses&utm_medium=renew'
				);

				$license_status = 'license-expires-soon-notice';

			} else {

				$messages[] = sprintf(
					__( 'Your license key expires on %s.', 'novelist' ),
					date_i18n( get_option( 'date_format' ), strtotime( $license->expires, current_time( 'timestamp' ) ) )
				);

				$license_status = 'license-expiration-date-notice';

			}

		}

	} else {
		$license_status = null;
	}

	$size = ( isset( $args['size'] ) && ! is_null( $args['size'] ) ) ? $args['size'] : 'regular';

	$wrapper_class = isset( $license_status ) ? $license_status : 'license-null';
	?>
	<div class="<?php echo sanitize_html_class( $wrapper_class ); ?>">
		<input type="text" class="<?php echo sanitize_html_class( $size ); ?>-text" id="novelist_settings[<?php echo novelist_sanitize_key( $args['id'] ); ?>]" name="novelist_settings[<?php echo novelist_sanitize_key( $args['id'] ); ?>]" value="<?php echo esc_attr( $value ); ?>">
		<?php

		// License key is valid, so let's show a deactivate button.
		if ( ( is_object( $license ) && 'valid' == $license->license ) || 'valid' == $license ) {
			?>
			<input type="submit" class="button-secondary" name="<?php echo esc_attr( $args['id'] ); ?>_deactivate" value="<?php _e( 'Deactivate License', 'novelist' ); ?>">
			<?php
		}

		?>
		<label for="novelist_settings[<?php echo novelist_sanitize_key( $args['id'] ); ?>]" class="desc"><?php echo wp_kses_post( $args['desc'] ); ?></label>
		<?php

		if ( ! empty( $messages ) && is_array( $messages ) ) {
			foreach ( $messages as $message ) {
				?>
				<div class="novelist-license-data novelist-license-<?php echo sanitize_html_class( $class ); ?> desc">
					<p><?php echo $message; ?></p>
				</div>
				<?php
			}
		}

		wp_nonce_field( novelist_sanitize_key( $args['id'] ) . '-nonce', novelist_sanitize_key( $args['id'] ) . '-nonce' );
		?>
	</div>
	<?php
}

/**
 * Retrieve a list of all published pages
 *
 * @since 1.0.2
 * @return array $pages_options An array of the pages
 */
function novelist_get_pages() {
	$pages_options = array( '' => __( '- Select a Page -' ) ); // Blank option

	$pages = get_pages();

	if ( $pages ) {
		foreach ( $pages as $page ) {
			$pages_options[ $page->ID ] = $page->post_title;
		}
	}

	return $pages_options;
}
