<?php
/**
 * Gateway cache-control helper.
 *
 * @package Alynt_Account_Gateway
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Applies request-local cache bypass signals to branded gateway responses.
 */
class ALYNT_AG_Gateway_Cache_Control {

	/**
	 * Return cache-bypass constants used by common WordPress page-cache plugins.
	 *
	 * @return array<int,string>
	 */
	public static function cache_bypass_constants() {
		return array(
			'DONOTCACHEPAGE',
			'DONOTCACHEOBJECT',
			'DONOTCACHEDB',
		);
	}

	/**
	 * Return explicit HTTP headers for dynamic gateway responses.
	 *
	 * @return array<int,string>
	 */
	public static function header_lines() {
		return array(
			'do-not-cache: true',
			'Cache-Control: private, no-store, no-cache, must-revalidate, max-age=0',
			'Pragma: no-cache',
			'Expires: Wed, 11 Jan 1984 05:00:00 GMT',
		);
	}

	/**
	 * Prevent page, browser, proxy, and GridPane response caching for this request.
	 *
	 * @return void
	 */
	public static function prevent_caching() {
		foreach ( self::cache_bypass_constants() as $constant ) {
			if ( ! defined( $constant ) ) {
				define( $constant, true );
			}
		}

		if ( function_exists( 'nocache_headers' ) ) {
			nocache_headers();
		}

		if ( headers_sent() ) {
			return;
		}

		foreach ( self::header_lines() as $header ) {
			header( $header );
		}
	}
}
