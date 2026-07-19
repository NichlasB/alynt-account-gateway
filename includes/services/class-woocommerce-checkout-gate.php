<?php
/**
 * WooCommerce checkout authentication gate.
 *
 * @package Alynt_Account_Gateway
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Redirects anonymous checkout visitors to the branded login screen.
 */
class ALYNT_AG_WooCommerce_Checkout_Gate {

	/**
	 * Return destination helper.
	 *
	 * @var ALYNT_AG_Return_Destination
	 */
	private $destinations;

	/**
	 * Route helper.
	 *
	 * @var ALYNT_AG_Frontend_Routes
	 */
	private $routes;

	/**
	 * Constructor.
	 *
	 * @param ALYNT_AG_Return_Destination|null $destinations Return destination helper.
	 * @param ALYNT_AG_Frontend_Routes|null    $routes       Route helper.
	 */
	public function __construct( $destinations = null, $routes = null ) {
		$this->destinations = $destinations ? $destinations : new ALYNT_AG_Return_Destination();
		$this->routes       = $routes ? $routes : new ALYNT_AG_Frontend_Routes();
	}

	/**
	 * Register hooks.
	 *
	 * @return void
	 */
	public function register() {
		add_action( 'template_redirect', array( $this, 'maybe_redirect_checkout' ), 0 );
	}

	/**
	 * Redirect an anonymous protected checkout request.
	 *
	 * @return void
	 */
	public function maybe_redirect_checkout() {
		$settings = ALYNT_AG_Settings_Schema::get_settings();

		if ( ! $this->should_redirect_current_request( $settings ) ) {
			return;
		}

		$destination = $this->current_checkout_url( $settings );
		if ( ! $destination ) {
			return;
		}

		wp_safe_redirect( $this->routes->login_url( $settings, $destination ) );
		exit;
	}

	/**
	 * Determine whether the current request should be gated.
	 *
	 * @param array<string,mixed> $settings Settings.
	 * @return bool
	 */
	public function should_redirect_current_request( $settings ) {
		if (
			empty( $settings['frontend_enabled'] )
			|| empty( $settings['woocommerce_require_login_checkout'] )
			|| is_user_logged_in()
			|| wp_doing_ajax()
			|| ( defined( 'REST_REQUEST' ) && REST_REQUEST )
			|| ! function_exists( 'is_checkout' )
			|| ! is_checkout()
		) {
			return false;
		}

		if ( function_exists( 'is_wc_endpoint_url' ) && is_wc_endpoint_url( 'order-received' ) ) {
			return false;
		}

		if ( function_exists( 'is_wc_endpoint_url' ) && is_wc_endpoint_url( 'order-pay' ) ) {
			return ! empty( $settings['woocommerce_require_login_order_pay'] );
		}

		return true;
	}

	/**
	 * Determine whether a validated destination is a protected checkout route.
	 *
	 * @param string              $destination Candidate destination.
	 * @param array<string,mixed> $settings    Settings.
	 * @return bool
	 */
	public function is_checkout_destination( $destination, $settings ) {
		if ( empty( $settings['woocommerce_require_login_checkout'] ) || ! function_exists( 'wc_get_checkout_url' ) ) {
			return false;
		}

		$candidate = $this->destinations->relative_path( $destination, $settings );
		$checkout  = $this->destinations->relative_path( wc_get_checkout_url(), $settings );

		if ( ! $candidate || ! $checkout ) {
			return false;
		}

		$candidate_path = $this->path_only( $candidate );
		$checkout_path  = $this->path_only( $checkout );

		if ( $this->paths_match( $candidate_path, $checkout_path ) ) {
			return true;
		}

		$order_received = $this->endpoint_path( 'order-received', $settings );
		if ( $order_received && $this->path_has_prefix( $candidate_path, $order_received ) ) {
			return false;
		}

		$order_pay = $this->endpoint_path( 'order-pay', $settings );

		return ! empty( $settings['woocommerce_require_login_order_pay'] )
			&& $order_pay
			&& $this->path_has_prefix( $candidate_path, $order_pay );
	}

	/**
	 * Return the current checkout request as a validated absolute URL.
	 *
	 * @param array<string,mixed> $settings Settings.
	 * @return string
	 */
	private function current_checkout_url( $settings ) {
		$request_uri = isset( $_SERVER['REQUEST_URI'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '';
		$destination = $this->destinations->absolute_url( $request_uri, $settings );

		if ( $destination ) {
			return $destination;
		}

		return function_exists( 'wc_get_checkout_url' )
			? $this->destinations->absolute_url( wc_get_checkout_url(), $settings )
			: '';
	}

	/**
	 * Return a WooCommerce checkout endpoint path.
	 *
	 * @param string              $endpoint Endpoint key.
	 * @param array<string,mixed> $settings Settings.
	 * @return string
	 */
	private function endpoint_path( $endpoint, $settings ) {
		if ( ! function_exists( 'wc_get_endpoint_url' ) || ! function_exists( 'wc_get_checkout_url' ) ) {
			return '';
		}

		$url      = wc_get_endpoint_url( $endpoint, '', wc_get_checkout_url() );
		$relative = $this->destinations->relative_path( $url, $settings );

		return $relative ? $this->path_only( $relative ) : '';
	}

	/**
	 * Return the path portion of a relative destination.
	 *
	 * @param string $destination Relative destination.
	 * @return string
	 */
	private function path_only( $destination ) {
		$path = wp_parse_url( $destination, PHP_URL_PATH );

		return is_string( $path ) ? $path : '';
	}

	/**
	 * Compare paths without trailing-slash sensitivity.
	 *
	 * @param string $left  Left path.
	 * @param string $right Right path.
	 * @return bool
	 */
	private function paths_match( $left, $right ) {
		return untrailingslashit( '/' . ltrim( $left, '/' ) ) === untrailingslashit( '/' . ltrim( $right, '/' ) );
	}

	/**
	 * Determine whether a path is an endpoint path or one of its descendants.
	 *
	 * @param string $path   Candidate path.
	 * @param string $prefix Endpoint path.
	 * @return bool
	 */
	private function path_has_prefix( $path, $prefix ) {
		$path   = untrailingslashit( '/' . ltrim( $path, '/' ) );
		$prefix = untrailingslashit( '/' . ltrim( $prefix, '/' ) );

		return $path === $prefix || 0 === strpos( $path, $prefix . '/' );
	}
}
