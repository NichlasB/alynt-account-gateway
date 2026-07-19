<?php
/**
 * Frontend gateway route helpers.
 *
 * @package Alynt_Account_Gateway
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Resolves branded account gateway URLs and route screens.
 */
class ALYNT_AG_Frontend_Routes {

	/**
	 * WooCommerce integration.
	 *
	 * @var ALYNT_AG_WooCommerce_Integration
	 */
	private $woocommerce;

	/**
	 * Constructor.
	 *
	 * @param ALYNT_AG_WooCommerce_Integration|null $woocommerce WooCommerce integration.
	 */
	public function __construct( $woocommerce = null ) {
		$this->woocommerce = $woocommerce ? $woocommerce : new ALYNT_AG_WooCommerce_Integration();
	}

	/**
	 * Build a branded gateway URL for a login action.
	 *
	 * @param string              $action   Login action.
	 * @param array<string,mixed> $settings Settings.
	 * @return string
	 */
	public function action_url( $action, $settings ) {
		if ( 'login' === $action ) {
			return home_url( $settings['login_path'] );
		}

		$mapped_action = in_array( $action, array( 'lostpassword', 'register', 'rp', 'resetpass', 'setpassword', 'logout', 'invalidlink' ), true ) ? $action : 'login';

		if ( 'login' === $mapped_action ) {
			return home_url( $settings['login_path'] );
		}

		return add_query_arg( 'action', $mapped_action, home_url( $settings['account_action_base'] ) );
	}

	/**
	 * Build a branded login URL.
	 *
	 * @param array<string,mixed> $settings     Settings.
	 * @param string              $redirect     Redirect URL.
	 * @param bool                $force_reauth Whether to force reauthentication.
	 * @return string
	 */
	public function login_url( $settings, $redirect = '', $force_reauth = false ) {
		$url = home_url( $settings['login_path'] );

		if ( $redirect ) {
			$url = add_query_arg( 'redirect_to', rawurlencode( $redirect ), $url );
		}

		if ( $force_reauth ) {
			$url = add_query_arg( 'reauth', '1', $url );
		}

		return $url;
	}

	/**
	 * Build a branded lost password URL.
	 *
	 * @param array<string,mixed> $settings Settings.
	 * @param string              $redirect Redirect URL.
	 * @return string
	 */
	public function lostpassword_url( $settings, $redirect = '' ) {
		$url = $this->action_url( 'lostpassword', $settings );

		if ( $redirect ) {
			$url = add_query_arg( 'redirect_to', rawurlencode( $redirect ), $url );
		}

		return $url;
	}

	/**
	 * Build a branded registration URL.
	 *
	 * @param array<string,mixed> $settings Settings.
	 * @param string              $redirect Redirect URL.
	 * @return string
	 */
	public function register_url( $settings, $redirect = '' ) {
		$url = $this->action_url( 'register', $settings );

		if ( $redirect ) {
			$url = add_query_arg( 'redirect_to', rawurlencode( $redirect ), $url );
		}

		return $url;
	}

	/**
	 * Build a branded logout URL.
	 *
	 * @param array<string,mixed> $settings Settings.
	 * @param string              $redirect Redirect URL.
	 * @return string
	 */
	public function logout_url( $settings, $redirect = '' ) {
		$url = wp_nonce_url( $this->action_url( 'logout', $settings ), 'log-out' );

		if ( $redirect ) {
			$url = add_query_arg( 'redirect_to', rawurlencode( $redirect ), $url );
		}

		return $url;
	}

	/**
	 * Return the gateway screen for the current request.
	 *
	 * @param array<string,mixed> $settings Settings.
	 * @return string
	 */
	public function screen( $settings ) {
		$current_path = $this->current_relative_path();

		if ( ! empty( $settings['dashboard_enabled'] ) && $this->paths_match( $current_path, $settings['after_login_redirect'] ) ) {
			return 'dashboard';
		}

		if ( $this->woocommerce->takeover_enabled( $settings ) && $this->woocommerce->endpoint_from_path( $current_path, $settings )['endpoint'] ) {
			return 'dashboard';
		}

		if ( $this->paths_match( $current_path, $settings['login_path'] ) ) {
			return 'login';
		}

		if ( ! $this->paths_match( $current_path, $settings['account_action_base'] ) ) {
			return '';
		}

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only screen routing.
		$action = isset( $_GET['action'] ) ? sanitize_key( wp_unslash( $_GET['action'] ) ) : 'login';

		switch ( $action ) {
			case 'register':
				return empty( $settings['registration_enabled'] ) ? 'registration_disabled' : 'register';
			case 'lostpassword':
				return 'lostpassword';
			case 'rp':
			case 'resetpass':
			case 'setpassword':
				return 'setpassword';
			case 'logout':
				return 'logout';
			case 'invalidlink':
				return 'invalidlink';
			default:
				return 'login';
		}
	}

	/**
	 * Get current request path relative to home URL.
	 *
	 * @return string
	 */
	public function current_relative_path() {
		$request_uri  = isset( $_SERVER['REQUEST_URI'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '/';
		$request_path = wp_parse_url( $request_uri, PHP_URL_PATH );
		$request_path = $request_path ? $request_path : '/';
		$home_path    = wp_parse_url( home_url( '/' ), PHP_URL_PATH );
		$home_path    = $home_path ? rtrim( $home_path, '/' ) : '';

		if ( $home_path && 0 === strpos( $request_path, $home_path ) ) {
			$request_path = substr( $request_path, strlen( $home_path ) );
		}

		return '/' . ltrim( $request_path, '/' );
	}

	/**
	 * Compare relative paths without trailing slash sensitivity.
	 *
	 * @param string $left  First path.
	 * @param string $right Second path.
	 * @return bool
	 */
	public function paths_match( $left, $right ) {
		return untrailingslashit( '/' . ltrim( $left, '/' ) ) === untrailingslashit( '/' . ltrim( $right, '/' ) );
	}
}
