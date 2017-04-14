<?php
/**
 * Misc Functions
 *
 * @package   novelist
 * @copyright Copyright (c) 2016, Nose Graze Ltd.
 * @license   GPL2+
 */

/**
 * Get IP Address
 *
 * @since 1.0.3
 * @return string
 */
function novelist_get_ip() {

	$ip = '127.0.0.1';

	if ( ! empty( $_SERVER['HTTP_CLIENT_IP'] ) ) {
		//check ip from share internet
		$ip = $_SERVER['HTTP_CLIENT_IP'];
	} elseif ( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
		//to check ip is pass from proxy
		$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
	} elseif ( ! empty( $_SERVER['REMOTE_ADDR'] ) ) {
		$ip = $_SERVER['REMOTE_ADDR'];
	}

	// Fix potential CSV returned from $_SERVER variables
	$ip_array = explode( ',', $ip );
	$ip_array = array_map( 'trim', $ip_array );

	return apply_filters( 'novelist/get-ip', $ip_array[0] );

}

/**
 * Is Spam?
 *
 * Integrates with Akismet to check for spam. If Akismet is not installed then
 * we automatically assume it's not spam.
 *
 * @uses  novelist_get_ip()
 *
 * @param array $data
 *
 * @since 1.0.3
 * @return bool True if spam, false if not.
 */
function novelist_is_spam( $data = array() ) {

	if ( ! class_exists( 'Akismet' ) ) {
		return false;
	}

	if ( ! method_exists( 'Akismet', 'http_post' ) ) {
		return false;
	}

	$default_args = array(
		'comment_content' => '',
		'user_ip'         => novelist_get_ip(),
		'user_agent'      => isset( $_SERVER['HTTP_USER_AGENT'] ) ? $_SERVER['HTTP_USER_AGENT'] : null,
		'referrer'        => isset( $_SERVER['HTTP_REFERER'] ) ? $_SERVER['HTTP_REFERER'] : null,
		'blog'            => get_option( 'home' ),
		'blog_lang'       => get_locale(),
		'blog_charset'    => get_option( 'blog_charset' ),
		'comment_type'    => 'contact-form'
	);

	if ( current_user_can( 'manage_options' ) ) {
		$default_args['user_role'] = 'administrator';
	}

	$args = wp_parse_args( $data, $default_args );

	$query_string = Akismet::build_query( $args );
	$response     = Akismet::http_post( $query_string, 'comment-check' );
	$result       = ( is_array( $response ) && isset( $response[1] ) && $response[1] == 'true' ) ? true : false;

	return $result;

}

/**
 * Change Spam Status
 *
 * Integrates with Akismet to submit content as definitely spam ('submit-spam'), or
 * definitely not spam ('submit-ham'). This is typically used in the UI when the user
 * is manually marking something as spam or not spam.
 *
 * @param array  $data
 * @param string $path
 *
 * @since 1.0.3
 * @return bool True on success, false on failure. Returns false if Akismet is not installed.
 */
function novelist_change_spam_status( $data, $path = 'submit-spam' ) {

	if ( ! class_exists( 'Akismet' ) ) {
		return false;
	}

	if ( ! method_exists( 'Akismet', 'http_post' ) ) {
		return false;
	}

	$allowed_paths = array(
		'submit-spam',
		'submit-ham'
	);

	if ( ! in_array( $path, $allowed_paths ) ) {
		return false;
	}

	$default_args = array(
		'comment_content' => '',
		'user_ip'         => novelist_get_ip(),
		'user_agent'      => isset( $_SERVER['HTTP_USER_AGENT'] ) ? $_SERVER['HTTP_USER_AGENT'] : null,
		'referrer'        => isset( $_SERVER['HTTP_REFERER'] ) ? $_SERVER['HTTP_REFERER'] : null,
		'blog'            => get_option( 'home' ),
		'blog_lang'       => get_locale(),
		'blog_charset'    => get_option( 'blog_charset' ),
		'comment_type'    => 'contact-form',
		//'is_test'         => true // uncomment to test
	);

	if ( current_user_can( 'manage_options' ) ) {
		$default_args['user_role'] = 'administrator';
	}

	$args = wp_parse_args( $data, $default_args );

	$query_string = Akismet::build_query( $args );
	$response     = Akismet::http_post( $query_string, $path );
	$result       = ( is_array( $response ) && isset( $response[1] ) && $response[1] == 'Thanks for making the web a better place.' ) ? true : false;

	return $result;

}