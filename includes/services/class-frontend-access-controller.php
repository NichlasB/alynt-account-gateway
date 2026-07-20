<?php
/**
 * Frontend access controller.
 *
 * @package Alynt_Account_Gateway
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Controls native login, wp-admin, toolbar, and Force Login access.
 */
class ALYNT_AG_Frontend_Access_Controller {

	/**
	 * Route helper.
	 *
	 * @var ALYNT_AG_Frontend_Routes
	 */
	private $routes;

	/**
	 * Request context.
	 *
	 * @var ALYNT_AG_Frontend_Request_Context
	 */
	private $context;

	/**
	 * Constructor.
	 *
	 * @param ALYNT_AG_Frontend_Routes          $routes  Route helper.
	 * @param ALYNT_AG_Frontend_Request_Context $context Request context.
	 */
	public function __construct( $routes, $context ) {
		$this->routes  = $routes;
		$this->context = $context;
	}

	/**
	 * Restrict admin toolbar to administrators and shop managers.
	 *
	 * @param bool $show Whether to show toolbar.
	 * @return bool
	 */
	public function filter_admin_bar( $show ) {
		$settings = ALYNT_AG_Settings_Schema::get_settings();
		if ( empty( $settings['frontend_enabled'] ) || ! is_user_logged_in() ) {
			return $show;
		}

		// phpcs:ignore WordPress.WP.Capabilities.Unknown -- WooCommerce registers this capability for shop managers.
		return current_user_can( 'manage_options' ) || current_user_can( 'manage_woocommerce' );
	}

	/**
	 * Block wp-admin access for non-privileged roles.
	 *
	 * @return void
	 */
	public function maybe_block_wp_admin() {
		$settings = ALYNT_AG_Settings_Schema::get_settings();
		if ( empty( $settings['frontend_enabled'] ) || wp_doing_ajax() || ! is_user_logged_in() ) {
			return;
		}

		// phpcs:ignore WordPress.WP.Capabilities.Unknown -- WooCommerce registers this capability for shop managers.
		if ( current_user_can( 'manage_options' ) || current_user_can( 'manage_woocommerce' ) ) {
			return;
		}

		$destination = home_url( $settings['after_login_redirect'] );

		$this->context->log_routing_event(
			'wp_admin_access_blocked',
			__( 'Blocked wp-admin access for a non-privileged user.', 'alynt-account-gateway' ),
			array(
				'destination_path'   => $this->context->path_from_url( $destination ),
				'user_id'            => function_exists( 'get_current_user_id' ) ? absint( get_current_user_id() ) : 0,
				'request_path'       => $this->context->current_request_path(),
				'request_method'     => $this->context->current_request_method(),
				'request_query_keys' => $this->context->current_request_query_keys(),
			)
		);

		wp_safe_redirect( $destination );
		exit;
	}

	/**
	 * Redirect native wp-login.php requests to branded routes.
	 *
	 * @return void
	 */
	public function maybe_redirect_native_login() {
		$settings = ALYNT_AG_Settings_Schema::get_settings();

		if ( empty( $settings['frontend_enabled'] ) || $this->context->is_emergency_bypass( $settings ) ) {
			return;
		}

		$request_method = $this->context->current_request_method();
		if ( 'POST' === $request_method ) {
			return;
		}

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only action routing.
		$action = isset( $_GET['action'] ) ? sanitize_key( wp_unslash( $_GET['action'] ) ) : 'login';
		$url    = $this->routes->action_url( $action, $settings );

		foreach ( array( 'key', 'login', 'redirect_to' ) as $param ) {
			// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Preserving core login query arguments.
			if ( isset( $_GET[ $param ] ) ) {
				// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Preserving core login query arguments.
				$value = sanitize_text_field( wp_unslash( $_GET[ $param ] ) );
				$url   = add_query_arg( $param, rawurlencode( $value ), $url );
			}
		}

		$this->context->log_routing_event(
			'native_login_redirected',
			__( 'Redirected a native wp-login.php request to the branded account gateway.', 'alynt-account-gateway' ),
			array(
				'action'               => $action,
				'destination_path'     => $this->context->path_from_url( $url ),
				'preserved_query_keys' => $this->context->preserved_login_query_keys(),
				'request_method'       => $request_method,
			)
		);

		wp_safe_redirect( $url );
		exit;
	}

	/**
	 * Let Force Login pass through public gateway routes.
	 *
	 * @param bool   $bypass Whether Force Login already intends to bypass.
	 * @param string $url    Visited URL.
	 * @return bool
	 */
	public function filter_force_login_bypass( $bypass, $url ) {
		if ( $bypass ) {
			return true;
		}

		$settings = ALYNT_AG_Settings_Schema::get_settings();
		if ( empty( $settings['frontend_enabled'] ) ) {
			return false;
		}

		$path = $this->context->relative_path_from_url( $url );
		if ( '' === $path ) {
			return false;
		}

		return $this->routes->paths_match( $path, $settings['login_path'] )
			|| $this->routes->paths_match( $path, $settings['account_action_base'] );
	}
}
