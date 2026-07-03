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
define( 'MINUTE_IN_SECONDS', 60 );

$GLOBALS['alynt_ag_test_transients'] = array();

$autoload = ALYNT_AG_PLUGIN_DIR . 'vendor/autoload.php';
if ( file_exists( $autoload ) ) {
	require_once $autoload;
}

if ( ! function_exists( '__' ) ) {
	function __( $text, $domain = 'default' ) {
		return $text;
	}
}

if ( ! function_exists( 'is_wp_error' ) ) {
	function is_wp_error( $thing ) {
		return $thing instanceof WP_Error;
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

if ( ! function_exists( 'sanitize_key' ) ) {
	function sanitize_key( $value ) {
		return strtolower( preg_replace( '/[^a-zA-Z0-9_-]/', '', (string) $value ) );
	}
}

if ( ! function_exists( 'sanitize_email' ) ) {
	function sanitize_email( $value ) {
		return trim( strtolower( (string) $value ) );
	}
}

if ( ! function_exists( 'sanitize_user' ) ) {
	function sanitize_user( $username, $strict = false ) {
		$username = preg_replace( '/\s+/', '_', (string) $username );
		return preg_replace( '/[^A-Za-z0-9_@.-]/', '', $username );
	}
}

if ( ! function_exists( 'is_email' ) ) {
	function is_email( $value ) {
		return false !== filter_var( $value, FILTER_VALIDATE_EMAIL );
	}
}

if ( ! function_exists( 'wp_salt' ) ) {
	function wp_salt( $scheme = 'auth' ) {
		return 'test-salt-' . $scheme;
	}
}

if ( ! function_exists( 'wp_generate_password' ) ) {
	function wp_generate_password( $length = 12, $special_chars = true, $extra_special_chars = false ) {
		return substr( str_repeat( 'Abc12345', 8 ), 0, $length );
	}
}

if ( ! function_exists( 'wp_remote_post' ) ) {
	function wp_remote_post( $url, $args = array() ) {
		return array( 'body' => '{"success":true}' );
	}
}

if ( ! function_exists( 'wp_remote_get' ) ) {
	function wp_remote_get( $url, $args = array() ) {
		return array( 'body' => '{"status":"safe"}' );
	}
}

if ( ! function_exists( 'wp_remote_retrieve_body' ) ) {
	function wp_remote_retrieve_body( $response ) {
		return isset( $response['body'] ) ? $response['body'] : '';
	}
}

if ( ! function_exists( 'add_query_arg' ) ) {
	function add_query_arg( $args, $url = '' ) {
		$separator = false === strpos( $url, '?' ) ? '?' : '&';
		return $url . $separator . http_build_query( $args );
	}
}

if ( ! function_exists( 'home_url' ) ) {
	function home_url( $path = '' ) {
		return 'https://example.test' . $path;
	}
}

if ( ! function_exists( 'email_exists' ) ) {
	function email_exists( $email ) {
		return false;
	}
}

if ( ! function_exists( 'username_exists' ) ) {
	function username_exists( $username ) {
		return in_array( $username, array( '@User_Damon_Paulo', 'User_Damon_Paulo' ), true ) ? 1 : false;
	}
}

if ( ! class_exists( 'WP_User' ) ) {
	class WP_User {
		public $ID;
		public $user_login;

		public function __construct( $login = 'damon@example.test' ) {
			$this->ID         = 123;
			$this->user_login = $login;
		}
	}
}

if ( ! function_exists( 'check_password_reset_key' ) ) {
	function check_password_reset_key( $key, $login ) {
		if ( 'bad-key' === $key || '' === $login ) {
			return new WP_Error( 'invalid_key', 'Invalid key.' );
		}

		return new WP_User( $login );
	}
}

if ( ! function_exists( 'reset_password' ) ) {
	function reset_password( $user, $new_pass ) {
		$GLOBALS['alynt_ag_test_reset_password'] = array(
			'user_login' => $user->user_login,
			'password'   => $new_pass,
		);
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

if ( ! function_exists( 'wp_kses_post' ) ) {
	function wp_kses_post( $value ) {
		return (string) $value;
	}
}

if ( ! function_exists( 'get_option' ) ) {
	function get_option( $name, $default = false ) {
		return $default;
	}
}

if ( ! function_exists( 'get_transient' ) ) {
	function get_transient( $name ) {
		return isset( $GLOBALS['alynt_ag_test_transients'][ $name ] ) ? $GLOBALS['alynt_ag_test_transients'][ $name ]['value'] : false;
	}
}

if ( ! function_exists( 'set_transient' ) ) {
	function set_transient( $name, $value, $expiration = 0 ) {
		$GLOBALS['alynt_ag_test_transients'][ $name ] = array(
			'value'      => $value,
			'expiration' => $expiration,
		);

		return true;
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

		public function get_error_code() {
			return $this->code;
		}

		public function get_error_message() {
			return $this->message;
		}
	}
}

require_once ALYNT_AG_PLUGIN_DIR . 'includes/class-settings-schema.php';
require_once ALYNT_AG_PLUGIN_DIR . 'includes/class-diagnostics-logger.php';
require_once ALYNT_AG_PLUGIN_DIR . 'includes/services/class-rate-limiter.php';
require_once ALYNT_AG_PLUGIN_DIR . 'includes/services/class-auth-service.php';
require_once ALYNT_AG_PLUGIN_DIR . 'includes/services/class-registration-service.php';
require_once ALYNT_AG_PLUGIN_DIR . 'includes/services/class-reoon-client.php';
require_once ALYNT_AG_PLUGIN_DIR . 'includes/services/class-turnstile-client.php';
