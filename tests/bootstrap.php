<?php
/**
 * Test bootstrap.
 *
 * @package Alynt_Account_Gateway
 */

define( 'ALYNT_AG_TESTS', true );
define( 'ABSPATH', dirname( __DIR__ ) . '/' );
define( 'ALYNT_AG_PLUGIN_DIR', dirname( __DIR__ ) . '/' );
define( 'ALYNT_AG_PLUGIN_URL', 'https://example.test/wp-content/plugins/alynt-account-gateway/' );
define( 'ALYNT_AG_PLUGIN_BASENAME', 'alynt-account-gateway/alynt-account-gateway.php' );
define( 'ALYNT_AG_TEXT_DOMAIN', 'alynt-account-gateway' );
define( 'HOUR_IN_SECONDS', 3600 );

$autoload = ALYNT_AG_PLUGIN_DIR . 'vendor/autoload.php';
if ( file_exists( $autoload ) ) {
	require_once $autoload;
}

if ( ! function_exists( '__' ) ) {
	function __( $text, $domain = 'default' ) {
		return $text;
	}
}

if ( ! function_exists( 'absint' ) ) {
	function absint( $value ) {
		return abs( (int) $value );
	}
}

if ( ! function_exists( 'sanitize_text_field' ) ) {
	function sanitize_text_field( $value ) {
		return trim( (string) $value );
	}
}

if ( ! function_exists( 'sanitize_hex_color' ) ) {
	function sanitize_hex_color( $value ) {
		return preg_match( '/^#[a-fA-F0-9]{6}$/', (string) $value ) ? $value : '';
	}
}

if ( ! function_exists( 'wp_unslash' ) ) {
	function wp_unslash( $value ) {
		return $value;
	}
}

if ( ! function_exists( 'esc_url_raw' ) ) {
	function esc_url_raw( $value ) {
		return filter_var( $value, FILTER_SANITIZE_URL );
	}
}

if ( ! function_exists( 'get_option' ) ) {
	function get_option( $name, $default = false ) {
		return $default;
	}
}

if ( ! class_exists( 'WP_Error' ) ) {
	class WP_Error {
		public $code;
		public $message;

		public function __construct( $code = '', $message = '' ) {
			$this->code    = $code;
			$this->message = $message;
		}
	}
}

require_once ALYNT_AG_PLUGIN_DIR . 'includes/class-settings-schema.php';
require_once ALYNT_AG_PLUGIN_DIR . 'includes/class-diagnostics-logger.php';
require_once ALYNT_AG_PLUGIN_DIR . 'includes/services/class-registration-service.php';
require_once ALYNT_AG_PLUGIN_DIR . 'includes/services/class-reoon-client.php';
