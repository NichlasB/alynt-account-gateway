<?php
/**
 * Client IP resolution service.
 *
 * @package Alynt_Account_Gateway
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Resolves a validated client IP without trusting spoofable headers by default.
 */
class ALYNT_AG_Client_IP {

	/**
	 * Supported proxy-provided server variables.
	 *
	 * @var string[]
	 */
	const SUPPORTED_PROXY_HEADERS = array(
		'HTTP_CF_CONNECTING_IP',
		'HTTP_X_FORWARDED_FOR',
	);

	/**
	 * Resolve the current client IP.
	 *
	 * Forwarded headers are considered only when the immediate peer is explicitly
	 * trusted through the alynt_ag_is_trusted_proxy filter.
	 *
	 * @return string Validated IP address, or an empty string when unavailable.
	 */
	public static function resolve() {
		$remote_addr = self::get_server_ip( 'REMOTE_ADDR' );

		if ( '' === $remote_addr ) {
			return '';
		}

		/**
		 * Filter whether the immediate peer is a trusted proxy.
		 *
		 * @param bool   $is_trusted  Whether the immediate peer is trusted.
		 * @param string $remote_addr Validated immediate peer IP address.
		 */
		$is_trusted = (bool) apply_filters( 'alynt_ag_is_trusted_proxy', false, $remote_addr );

		if ( ! $is_trusted ) {
			return $remote_addr;
		}

		/**
		 * Filter the ordered proxy headers used to resolve the original client.
		 *
		 * Only names listed in SUPPORTED_PROXY_HEADERS are accepted.
		 *
		 * @param string[] $headers     Ordered server variable names.
		 * @param string   $remote_addr Validated immediate peer IP address.
		 */
		$headers = apply_filters( 'alynt_ag_trusted_proxy_headers', self::SUPPORTED_PROXY_HEADERS, $remote_addr );
		$headers = is_array( $headers ) ? $headers : array();

		foreach ( $headers as $header ) {
			$header = is_string( $header ) ? strtoupper( $header ) : '';

			if ( ! in_array( $header, self::SUPPORTED_PROXY_HEADERS, true ) ) {
				continue;
			}

			$client_ip = self::get_server_ip( $header );

			if ( '' !== $client_ip ) {
				return $client_ip;
			}
		}

		return $remote_addr;
	}

	/**
	 * Read and validate the first IP from a server variable.
	 *
	 * @param string $key Server variable name.
	 * @return string
	 */
	private static function get_server_ip( $key ) {
		if ( empty( $_SERVER[ $key ] ) || ! is_string( $_SERVER[ $key ] ) ) {
			return '';
		}

		$value = sanitize_text_field( wp_unslash( $_SERVER[ $key ] ) );
		$parts = explode( ',', $value );
		$ip    = trim( $parts[0] );

		return false !== filter_var( $ip, FILTER_VALIDATE_IP ) ? $ip : '';
	}
}
