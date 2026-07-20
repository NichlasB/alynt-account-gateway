<?php
/**
 * Frontend request context.
 *
 * @package Alynt_Account_Gateway
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Builds privacy-conscious frontend request and diagnostics context.
 */
class ALYNT_AG_Frontend_Request_Context {

	/**
	 * Log a frontend routing diagnostics event.
	 *
	 * @param string              $event_code Event code.
	 * @param string              $message    Event message.
	 * @param array<string,mixed> $context    Event context.
	 * @return bool
	 */
	public function log_routing_event( $event_code, $message, $context ) {
		return ALYNT_AG_Diagnostics_Logger::log_event(
			'warning',
			'security',
			$event_code,
			$message,
			$context
		);
	}

	/**
	 * Return only the path portion of a URL.
	 *
	 * @param string $url URL.
	 * @return string
	 */
	public function path_from_url( $url ) {
		$path = wp_parse_url( $url, PHP_URL_PATH );

		return $path ? sanitize_text_field( $path ) : '';
	}

	/**
	 * Return a URL path relative to the site's home path.
	 *
	 * @param string $url URL.
	 * @return string
	 */
	public function relative_path_from_url( $url ) {
		$path = wp_parse_url( $url, PHP_URL_PATH );
		if ( ! $path ) {
			return '';
		}

		$home_path = wp_parse_url( home_url( '/' ), PHP_URL_PATH );
		$home_path = $home_path ? rtrim( $home_path, '/' ) : '';

		if ( $home_path && 0 === strpos( $path, $home_path ) ) {
			$path = substr( $path, strlen( $home_path ) );
		}

		return '/' . ltrim( sanitize_text_field( $path ), '/' );
	}

	/**
	 * Return the current request path without query values.
	 *
	 * @return string
	 */
	public function current_request_path() {
		$uri = isset( $_SERVER['REQUEST_URI'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '';
		if ( '' === $uri ) {
			return '';
		}

		$path = wp_parse_url( $uri, PHP_URL_PATH );

		return $path ? sanitize_text_field( $path ) : '';
	}

	/**
	 * Return the current request method.
	 *
	 * @return string
	 */
	public function current_request_method() {
		$method = isset( $_SERVER['REQUEST_METHOD'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REQUEST_METHOD'] ) ) : '';

		return strtoupper( $method );
	}

	/**
	 * Return current request query keys without their values.
	 *
	 * @return array<int,string>
	 */
	public function current_request_query_keys() {
		$keys = array();

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Diagnostics records query keys only, not values.
		foreach ( array_keys( $_GET ) as $key ) {
			if ( is_scalar( $key ) ) {
				$keys[] = sanitize_key( (string) $key );
			}
		}

		return array_values( array_filter( array_unique( $keys ) ) );
	}

	/**
	 * Return preserved native-login query argument names without values.
	 *
	 * @return array<int,string>
	 */
	public function preserved_login_query_keys() {
		$keys = array();

		foreach ( array( 'key', 'login', 'redirect_to' ) as $param ) {
			// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Diagnostics records keys only, not values.
			if ( isset( $_GET[ $param ] ) ) {
				$keys[] = $param;
			}
		}

		return $keys;
	}

	/**
	 * Determine whether the current request carries the emergency bypass.
	 *
	 * @param array<string,mixed> $settings Settings.
	 * @return bool
	 */
	public function is_emergency_bypass( $settings ) {
		if ( empty( $settings['emergency_bypass_key'] ) ) {
			return false;
		}

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Emergency bypass is a read-only routing check.
		$provided = isset( $_GET['alynt_ag_bypass'] ) ? sanitize_text_field( wp_unslash( $_GET['alynt_ag_bypass'] ) ) : '';

		return $provided && hash_equals( (string) $settings['emergency_bypass_key'], $provided );
	}
}
