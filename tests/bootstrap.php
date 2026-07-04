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
define( 'ALYNT_AG_VERSION', '0.1.0' );
define( 'HOUR_IN_SECONDS', 3600 );
define( 'MINUTE_IN_SECONDS', 60 );

$GLOBALS['alynt_ag_test_transients'] = array();
$GLOBALS['alynt_ag_test_mail'] = array();
$GLOBALS['alynt_ag_test_options'] = array();
$GLOBALS['alynt_ag_test_remote_posts'] = array();
$GLOBALS['alynt_ag_test_db_inserts'] = array();
$GLOBALS['alynt_ag_test_db_updates'] = array();
$GLOBALS['alynt_ag_test_db_deletes'] = array();
$GLOBALS['alynt_ag_test_db_results'] = array();
$GLOBALS['alynt_ag_test_db_queries'] = array();
$GLOBALS['alynt_ag_test_filters'] = array();
$GLOBALS['alynt_ag_test_deleted_options'] = array();
$GLOBALS['alynt_ag_test_scheduled_hooks'] = array();
$GLOBALS['alynt_ag_test_unscheduled_events'] = array();
$GLOBALS['alynt_ag_test_cleared_hooks'] = array();
$GLOBALS['alynt_ag_test_redirects'] = array();
$GLOBALS['alynt_ag_test_signons'] = array();
$GLOBALS['alynt_ag_test_deleted_user_meta'] = array();
$GLOBALS['alynt_ag_test_created_users'] = array();
$GLOBALS['alynt_ag_test_user_updates'] = array();
$GLOBALS['alynt_ag_test_enqueued_styles'] = array();
$GLOBALS['alynt_ag_test_enqueued_scripts'] = array();
$GLOBALS['alynt_ag_test_localized_scripts'] = array();
$GLOBALS['alynt_ag_test_attachment_urls'] = array();

class ALYNT_AG_Test_WPDB {
	public $prefix = 'wp_';
	public $options = 'wp_options';
	public $insert_id = 1;

	public function insert( $table, $data, $format = array() ) {
		$GLOBALS['alynt_ag_test_db_inserts'][] = array(
			'table'  => $table,
			'data'   => $data,
			'format' => $format,
		);

		return true;
	}

	public function update( $table, $data, $where, $format = array(), $where_format = array() ) {
		$GLOBALS['alynt_ag_test_db_updates'][] = array(
			'table'        => $table,
			'data'         => $data,
			'where'        => $where,
			'format'       => $format,
			'where_format' => $where_format,
		);

		return true;
	}

	public function delete( $table, $where, $where_format = array() ) {
		$GLOBALS['alynt_ag_test_db_deletes'][] = array(
			'table'        => $table,
			'where'        => $where,
			'where_format' => $where_format,
		);

		return 1;
	}

	public function get_results( $query ) {
		foreach ( $GLOBALS['alynt_ag_test_db_results'] as $table => $rows ) {
			if ( false !== strpos( $query, (string) $table ) ) {
				return $rows;
			}
		}

		return array();
	}

	public function prepare( $query, ...$args ) {
		foreach ( $args as $arg ) {
			$replacement = is_int( $arg ) ? (string) $arg : "'" . addslashes( (string) $arg ) . "'";
			$query = preg_replace( '/%[sd]/', $replacement, $query, 1 );
		}

		return $query;
	}

	public function query( $query ) {
		$GLOBALS['alynt_ag_test_db_queries'][] = $query;

		return true;
	}

	public function esc_like( $text ) {
		return addcslashes( (string) $text, '_%\\' );
	}

	public function get_charset_collate() {
		return 'DEFAULT CHARSET=utf8mb4';
	}
}

$GLOBALS['wpdb'] = new ALYNT_AG_Test_WPDB();

$autoload = ALYNT_AG_PLUGIN_DIR . 'vendor/autoload.php';
if ( file_exists( $autoload ) ) {
	require_once $autoload;
}

if ( ! function_exists( '__' ) ) {
	function __( $text, $domain = 'default' ) {
		return $text;
	}
}

if ( ! function_exists( 'esc_html' ) ) {
	function esc_html( $value ) {
		return htmlspecialchars( (string) $value, ENT_QUOTES, 'UTF-8' );
	}
}

if ( ! function_exists( 'esc_attr' ) ) {
	function esc_attr( $value ) {
		return htmlspecialchars( (string) $value, ENT_QUOTES, 'UTF-8' );
	}
}

if ( ! function_exists( 'esc_html_e' ) ) {
	function esc_html_e( $text, $domain = 'default' ) {
		echo esc_html( $text );
	}
}

if ( ! function_exists( 'esc_attr_e' ) ) {
	function esc_attr_e( $text, $domain = 'default' ) {
		echo esc_attr( $text );
	}
}

if ( ! function_exists( 'esc_url' ) ) {
	function esc_url( $value ) {
		return filter_var( $value, FILTER_SANITIZE_URL );
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

if ( ! function_exists( 'current_time' ) ) {
	function current_time( $type, $gmt = false ) {
		return '2026-07-03 12:00:00';
	}
}

if ( ! function_exists( 'wp_json_encode' ) ) {
	function wp_json_encode( $value, $flags = 0, $depth = 512 ) {
		return json_encode( $value, $flags, $depth );
	}
}

if ( ! function_exists( 'wp_enqueue_style' ) ) {
	function wp_enqueue_style( $handle, $src = '', $deps = array(), $ver = false, $media = 'all' ) {
		$GLOBALS['alynt_ag_test_enqueued_styles'][] = array(
			'handle' => $handle,
			'src'    => $src,
			'deps'   => $deps,
			'ver'    => $ver,
			'media'  => $media,
		);
	}
}

if ( ! function_exists( 'wp_enqueue_script' ) ) {
	function wp_enqueue_script( $handle, $src = '', $deps = array(), $ver = false, $in_footer = false ) {
		$GLOBALS['alynt_ag_test_enqueued_scripts'][] = array(
			'handle'    => $handle,
			'src'       => $src,
			'deps'      => $deps,
			'ver'       => $ver,
			'in_footer' => $in_footer,
		);
	}
}

if ( ! function_exists( 'wp_localize_script' ) ) {
	function wp_localize_script( $handle, $object_name, $l10n ) {
		$GLOBALS['alynt_ag_test_localized_scripts'][] = array(
			'handle'      => $handle,
			'object_name' => $object_name,
			'l10n'        => $l10n,
		);

		return true;
	}
}

if ( ! function_exists( 'get_bloginfo' ) ) {
	function get_bloginfo( $show = '' ) {
		if ( 'name' === $show ) {
			return 'Example Store';
		}

		return 'UTF-8';
	}
}

if ( ! function_exists( 'wp_specialchars_decode' ) ) {
	function wp_specialchars_decode( $value, $quote_style = ENT_QUOTES ) {
		return html_entity_decode( (string) $value, $quote_style, 'UTF-8' );
	}
}

if ( ! function_exists( 'wpautop' ) ) {
	function wpautop( $value ) {
		$paragraphs = preg_split( "/\n\s*\n/", trim( (string) $value ) );
		$paragraphs = array_map(
			static function ( $paragraph ) {
				return '<p>' . nl2br( $paragraph ) . '</p>';
			},
			$paragraphs ? $paragraphs : array()
		);

		return implode( "\n", $paragraphs );
	}
}

if ( ! function_exists( 'wp_strip_all_tags' ) ) {
	function wp_strip_all_tags( $value ) {
		return strip_tags( (string) $value );
	}
}

if ( ! function_exists( 'wp_get_attachment_image_url' ) ) {
	function wp_get_attachment_image_url( $attachment_id, $size = 'thumbnail' ) {
		$key = (int) $attachment_id . ':' . (string) $size;
		if ( isset( $GLOBALS['alynt_ag_test_attachment_urls'][ $key ] ) ) {
			return $GLOBALS['alynt_ag_test_attachment_urls'][ $key ];
		}

		return '';
	}
}

if ( ! function_exists( 'wp_mail' ) ) {
	function wp_mail( $to, $subject, $message, $headers = array() ) {
		$GLOBALS['alynt_ag_test_mail'][] = array(
			'to'      => $to,
			'subject' => $subject,
			'message' => $message,
			'headers' => $headers,
		);

		return true;
	}
}

if ( ! function_exists( 'wp_remote_post' ) ) {
	function wp_remote_post( $url, $args = array() ) {
		$GLOBALS['alynt_ag_test_remote_posts'][] = array(
			'url'  => $url,
			'args' => $args,
		);

		if ( isset( $GLOBALS['alynt_ag_test_remote_post_response'] ) ) {
			return $GLOBALS['alynt_ag_test_remote_post_response'];
		}

		return array(
			'body'     => '{"success":true}',
			'response' => array(
				'code'    => 200,
				'message' => 'OK',
			),
		);
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

if ( ! function_exists( 'wp_remote_retrieve_response_code' ) ) {
	function wp_remote_retrieve_response_code( $response ) {
		return isset( $response['response']['code'] ) ? (int) $response['response']['code'] : 0;
	}
}

if ( ! function_exists( 'wp_remote_retrieve_response_message' ) ) {
	function wp_remote_retrieve_response_message( $response ) {
		return isset( $response['response']['message'] ) ? $response['response']['message'] : '';
	}
}

if ( ! function_exists( 'add_query_arg' ) ) {
	function add_query_arg( $args, $value = '', $url = '' ) {
		if ( is_string( $args ) ) {
			$args = array( $args => $value );
		} else {
			$url = $value;
		}

		$separator = false === strpos( $url, '?' ) ? '?' : '&';
		return $url . $separator . http_build_query( $args );
	}
}

if ( ! function_exists( 'home_url' ) ) {
	function home_url( $path = '' ) {
		return 'https://example.test' . $path;
	}
}

if ( ! function_exists( 'trailingslashit' ) ) {
	function trailingslashit( $value ) {
		return rtrim( (string) $value, '/' ) . '/';
	}
}

if ( ! function_exists( 'untrailingslashit' ) ) {
	function untrailingslashit( $value ) {
		return rtrim( (string) $value, '/' );
	}
}

if ( ! function_exists( 'wp_logout_url' ) ) {
	function wp_logout_url( $redirect = '' ) {
		$url = 'https://example.test/wp-login.php?action=logout';

		return $redirect ? add_query_arg( 'redirect_to', $redirect, $url ) : $url;
	}
}

if ( ! function_exists( 'wp_nonce_url' ) ) {
	function wp_nonce_url( $actionurl, $action = -1, $name = '_wpnonce' ) {
		return add_query_arg( $name, 'test-nonce', $actionurl );
	}
}

if ( ! function_exists( 'wp_doing_ajax' ) ) {
	function wp_doing_ajax() {
		return ! empty( $GLOBALS['alynt_ag_test_doing_ajax'] );
	}
}

if ( ! function_exists( 'is_user_logged_in' ) ) {
	function is_user_logged_in() {
		return ! empty( $GLOBALS['alynt_ag_test_user_logged_in'] );
	}
}

if ( ! function_exists( 'current_user_can' ) ) {
	function current_user_can( $capability ) {
		return in_array( $capability, $GLOBALS['alynt_ag_test_user_caps'] ?? array(), true );
	}
}

if ( ! function_exists( 'wp_safe_redirect' ) ) {
	function wp_safe_redirect( $location, $status = 302, $x_redirect_by = 'WordPress' ) {
		$GLOBALS['alynt_ag_test_redirects'][] = array(
			'location'      => $location,
			'status'        => $status,
			'x_redirect_by' => $x_redirect_by,
		);

		if ( ! empty( $GLOBALS['alynt_ag_test_throw_on_redirect'] ) ) {
			throw new RuntimeException( 'redirect:' . $location );
		}

		return true;
	}
}

if ( ! function_exists( 'check_admin_referer' ) ) {
	function check_admin_referer( $action = -1, $query_arg = false ) {
		return true;
	}
}

if ( ! function_exists( 'is_ssl' ) ) {
	function is_ssl() {
		return false;
	}
}

if ( ! function_exists( 'wp_signon' ) ) {
	function wp_signon( $credentials = array(), $secure_cookie = '' ) {
		$GLOBALS['alynt_ag_test_signons'][] = array(
			'credentials'   => $credentials,
			'secure_cookie' => $secure_cookie,
		);

		return new WP_User( $credentials['user_login'] ?? 'customer@example.test' );
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

if ( ! function_exists( 'wp_create_user' ) ) {
	function wp_create_user( $username, $password, $email ) {
		$user_id = count( $GLOBALS['alynt_ag_test_created_users'] ) + 456;

		$GLOBALS['alynt_ag_test_created_users'][] = array(
			'ID'       => $user_id,
			'username' => $username,
			'password' => $password,
			'email'    => $email,
		);

		return $user_id;
	}
}

if ( ! function_exists( 'wp_update_user' ) ) {
	function wp_update_user( $userdata ) {
		$GLOBALS['alynt_ag_test_user_updates'][] = $userdata;

		return isset( $userdata['ID'] ) ? (int) $userdata['ID'] : 0;
	}
}

if ( ! class_exists( 'WP_User' ) ) {
	class WP_User {
		public $ID;
		public $user_login;
		public $user_email;
		public $display_name;
		public $roles;
		public $user_registered;

		public function __construct( $login = 'damon@example.test' ) {
			$this->ID         = 123;
			$this->user_login = $login;
			$this->user_email = $login;
			$this->display_name = 'Damon Paulo';
			$this->roles = array( 'customer' );
			$this->user_registered = '2026-07-03 10:00:00';
		}
	}
}

if ( ! function_exists( 'get_user_meta' ) ) {
	function get_user_meta( $user_id, $key = '', $single = false ) {
		$values = array(
			'first_name' => 'Damon',
			'last_name'  => 'Paulo',
		);

		return $values[ $key ] ?? '';
	}
}

if ( ! function_exists( 'delete_user_meta' ) ) {
	function delete_user_meta( $user_id, $meta_key ) {
		$GLOBALS['alynt_ag_test_deleted_user_meta'][] = array(
			'user_id'  => $user_id,
			'meta_key' => $meta_key,
		);

		return true;
	}
}

if ( ! function_exists( 'wp_get_current_user' ) ) {
	function wp_get_current_user() {
		return new WP_User( 'damon@example.test' );
	}
}

if ( ! function_exists( 'get_userdata' ) ) {
	function get_userdata( $user_id ) {
		if ( ! $user_id ) {
			return false;
		}

		$user = new WP_User( 'customer@example.test' );
		$user->ID = (int) $user_id;

		return $user;
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

if ( ! function_exists( 'wp_parse_url' ) ) {
	function wp_parse_url( $url, $component = -1 ) {
		return parse_url( $url, $component );
	}
}

if ( ! function_exists( 'wp_validate_redirect' ) ) {
	function wp_validate_redirect( $location, $fallback_url = '' ) {
		if ( '' === (string) $location ) {
			return $fallback_url;
		}

		$host = parse_url( $location, PHP_URL_HOST );

		return in_array( $host, array( 'example.test', null ), true ) ? $location : $fallback_url;
	}
}

if ( ! function_exists( 'wp_kses_post' ) ) {
	function wp_kses_post( $value ) {
		return (string) $value;
	}
}

if ( ! function_exists( 'get_option' ) ) {
	function get_option( $name, $default = false ) {
		if ( isset( $GLOBALS['alynt_ag_test_options'][ $name ] ) ) {
			return $GLOBALS['alynt_ag_test_options'][ $name ];
		}

		return $default;
	}
}

if ( ! function_exists( 'update_option' ) ) {
	function update_option( $name, $value, $autoload = null ) {
		$GLOBALS['alynt_ag_test_options'][ $name ] = $value;

		return true;
	}
}

if ( ! function_exists( 'delete_option' ) ) {
	function delete_option( $name ) {
		$GLOBALS['alynt_ag_test_deleted_options'][] = $name;
		unset( $GLOBALS['alynt_ag_test_options'][ $name ] );

		return true;
	}
}

if ( ! function_exists( 'wp_next_scheduled' ) ) {
	function wp_next_scheduled( $hook ) {
		return $GLOBALS['alynt_ag_test_scheduled_hooks'][ $hook ] ?? false;
	}
}

if ( ! function_exists( 'wp_unschedule_event' ) ) {
	function wp_unschedule_event( $timestamp, $hook ) {
		$GLOBALS['alynt_ag_test_unscheduled_events'][] = array(
			'timestamp' => $timestamp,
			'hook'      => $hook,
		);

		return true;
	}
}

if ( ! function_exists( 'wp_clear_scheduled_hook' ) ) {
	function wp_clear_scheduled_hook( $hook ) {
		$GLOBALS['alynt_ag_test_cleared_hooks'][] = $hook;

		return 1;
	}
}

if ( ! function_exists( 'flush_rewrite_rules' ) ) {
	function flush_rewrite_rules() {
		$GLOBALS['alynt_ag_test_rewrite_rules_flushed'] = true;
	}
}

if ( ! function_exists( 'get_site_option' ) ) {
	function get_site_option( $name, $default = false ) {
		if ( isset( $GLOBALS['alynt_ag_test_site_options'][ $name ] ) ) {
			return $GLOBALS['alynt_ag_test_site_options'][ $name ];
		}

		return $default;
	}
}

if ( ! function_exists( 'is_multisite' ) ) {
	function is_multisite() {
		return false;
	}
}

if ( ! function_exists( 'do_action' ) ) {
	function do_action( $hook_name, ...$args ) {
		$GLOBALS['alynt_ag_test_actions'][] = array(
			'hook' => $hook_name,
			'args' => $args,
		);
	}
}

if ( ! function_exists( 'add_action' ) ) {
	function add_action( $hook_name, $callback, $priority = 10, $accepted_args = 1 ) {
		$GLOBALS['alynt_ag_test_actions'][] = array(
			'hook'          => $hook_name,
			'callback'      => $callback,
			'priority'      => $priority,
			'accepted_args' => $accepted_args,
		);

		return true;
	}
}

if ( ! function_exists( 'add_filter' ) ) {
	function add_filter( $hook_name, $callback, $priority = 10, $accepted_args = 1 ) {
		$GLOBALS['alynt_ag_test_filters'][] = array(
			'hook'          => $hook_name,
			'callback'      => $callback,
			'priority'      => $priority,
			'accepted_args' => $accepted_args,
		);

		return true;
	}
}

if ( ! function_exists( 'get_user_by' ) ) {
	function get_user_by( $field, $value ) {
		if ( 'email' !== $field || ! is_email( $value ) ) {
			return false;
		}

		$user = new WP_User( $value );
		$user->ID = 123;

		return $user;
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
require_once ALYNT_AG_PLUGIN_DIR . 'includes/class-database.php';
require_once ALYNT_AG_PLUGIN_DIR . 'includes/class-deactivator.php';
require_once ALYNT_AG_PLUGIN_DIR . 'includes/class-diagnostics-logger.php';
require_once ALYNT_AG_PLUGIN_DIR . 'includes/class-retention-cleanup.php';
require_once ALYNT_AG_PLUGIN_DIR . 'includes/services/class-rate-limiter.php';
require_once ALYNT_AG_PLUGIN_DIR . 'includes/services/class-email-template-service.php';
require_once ALYNT_AG_PLUGIN_DIR . 'includes/services/class-auth-service.php';
require_once ALYNT_AG_PLUGIN_DIR . 'includes/services/class-registration-service.php';
require_once ALYNT_AG_PLUGIN_DIR . 'includes/services/class-reoon-client.php';
require_once ALYNT_AG_PLUGIN_DIR . 'includes/services/class-turnstile-client.php';
require_once ALYNT_AG_PLUGIN_DIR . 'includes/services/class-webhook-dispatcher.php';
require_once ALYNT_AG_PLUGIN_DIR . 'includes/services/class-dashboard-service.php';
require_once ALYNT_AG_PLUGIN_DIR . 'includes/services/class-woocommerce-integration.php';
require_once ALYNT_AG_PLUGIN_DIR . 'includes/services/class-frontend-routes.php';
require_once ALYNT_AG_PLUGIN_DIR . 'includes/services/class-frontend-assets.php';
require_once ALYNT_AG_PLUGIN_DIR . 'includes/services/class-frontend-branding.php';
require_once ALYNT_AG_PLUGIN_DIR . 'includes/services/class-frontend-components.php';
require_once ALYNT_AG_PLUGIN_DIR . 'includes/services/class-frontend-messages.php';
require_once ALYNT_AG_PLUGIN_DIR . 'includes/services/class-compatibility-warnings.php';
require_once ALYNT_AG_PLUGIN_DIR . 'includes/services/class-privacy-service.php';
require_once ALYNT_AG_PLUGIN_DIR . 'public/class-frontend.php';
