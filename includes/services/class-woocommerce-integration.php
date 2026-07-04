<?php
/**
 * WooCommerce integration placeholder.
 *
 * @package Alynt_Account_Gateway
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Coordinates WooCommerce account integration.
 */
class ALYNT_AG_WooCommerce_Integration {

	/**
	 * Account endpoint labels.
	 *
	 * @return array<string,string>
	 */
	public function endpoint_labels() {
		return array(
			'dashboard'                  => __( 'Dashboard', 'alynt-account-gateway' ),
			'orders'                     => __( 'Orders', 'alynt-account-gateway' ),
			'view-order'                 => __( 'Order Details', 'alynt-account-gateway' ),
			'downloads'                  => __( 'Downloads', 'alynt-account-gateway' ),
			'edit-address'               => __( 'Addresses', 'alynt-account-gateway' ),
			'edit-account'               => __( 'Account Details', 'alynt-account-gateway' ),
			'payment-methods'            => __( 'Payment Methods', 'alynt-account-gateway' ),
			'add-payment-method'         => __( 'Add Payment Method', 'alynt-account-gateway' ),
			'delete-payment-method'      => __( 'Delete Payment Method', 'alynt-account-gateway' ),
			'set-default-payment-method' => __( 'Default Payment Method', 'alynt-account-gateway' ),
		);
	}

	/**
	 * Register hooks.
	 *
	 * @return void
	 */
	public function register() {
		add_action( 'plugins_loaded', array( $this, 'detect' ), 20 );
	}

	/**
	 * Detect WooCommerce availability.
	 *
	 * @return bool
	 */
	public function detect() {
		return class_exists( 'WooCommerce' );
	}

	/**
	 * Whether WooCommerce account takeover can run.
	 *
	 * @param array<string,mixed> $settings Settings.
	 * @return bool
	 */
	public function takeover_enabled( $settings ) {
		return ! empty( $settings['dashboard_enabled'] ) && ! empty( $settings['woocommerce_takeover'] ) && $this->detect();
	}

	/**
	 * Return endpoint data for a dashboard request path.
	 *
	 * @param string              $path     Current relative path.
	 * @param array<string,mixed> $settings Settings.
	 * @return array<string,mixed>
	 */
	public function endpoint_from_path( $path, $settings ) {
		$base = untrailingslashit( '/' . ltrim( $settings['after_login_redirect'] ?? '/my-account/', '/' ) );
		$path = untrailingslashit( '/' . ltrim( $path, '/' ) );

		if ( $path === $base ) {
			return array(
				'endpoint' => 'dashboard',
				'value'    => '',
			);
		}

		if ( 0 !== strpos( $path, $base . '/' ) ) {
			return array(
				'endpoint' => '',
				'value'    => '',
			);
		}

		$relative = trim( substr( $path, strlen( $base ) ), '/' );
		$parts    = $relative ? explode( '/', $relative ) : array();
		$endpoint = isset( $parts[0] ) ? sanitize_key( $parts[0] ) : 'dashboard';
		$value    = isset( $parts[1] ) ? sanitize_text_field( rawurldecode( $parts[1] ) ) : '';

		if ( ! isset( $this->endpoint_labels()[ $endpoint ] ) ) {
			return array(
				'endpoint' => '',
				'value'    => '',
			);
		}

		return array(
			'endpoint' => $endpoint,
			'value'    => $value,
		);
	}

	/**
	 * Render WooCommerce account endpoint content through WooCommerce handlers.
	 *
	 * @param string $endpoint Endpoint key.
	 * @param string $value    Endpoint value.
	 * @return bool
	 */
	public function render_endpoint( $endpoint, $value = '' ) {
		if ( ! $this->detect() || ! $endpoint || 'dashboard' === $endpoint ) {
			return false;
		}

		/**
		 * WooCommerce registers these actions for My Account endpoint content.
		 */
		do_action( 'woocommerce_account_' . sanitize_key( $endpoint ) . '_endpoint', $value );

		return true;
	}
}
