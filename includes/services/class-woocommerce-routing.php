<?php
/**
 * WooCommerce account routing.
 *
 * @package Alynt_Account_Gateway
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Routes branded account requests into native WooCommerce handlers.
 */
class ALYNT_AG_WooCommerce_Routing extends ALYNT_AG_Service_Collaborator {

	/**
	 * Let WooCommerce process My Account form POSTs before the shell renders.
	 *
	 * @return void
	 */
	public function maybe_handle_account_form_post() {
		$method = isset( $_SERVER['REQUEST_METHOD'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REQUEST_METHOD'] ) ) : '';
		if ( 'POST' !== strtoupper( $method ) ) {
			return;
		}

		$settings = ALYNT_AG_Settings_Schema::get_settings();
		if ( ! $this->service->takeover_enabled( $settings ) || ! is_user_logged_in() || ! class_exists( 'WC_Form_Handler' ) ) {
			return;
		}

		$endpoint = $this->service->endpoint_from_path( $this->current_request_path(), $settings );
		if ( empty( $endpoint['endpoint'] ) ) {
			return;
		}

		if ( 'edit-address' === $endpoint['endpoint'] && $this->is_address_post() && method_exists( 'WC_Form_Handler', 'save_address' ) ) {
			$this->prime_account_endpoint_query_var( 'edit-address', $endpoint['value'] );
			WC_Form_Handler::save_address();
			return;
		}

		if ( 'edit-account' === $endpoint['endpoint'] && $this->is_account_details_post() && method_exists( 'WC_Form_Handler', 'save_account_details' ) ) {
			WC_Form_Handler::save_account_details();
		}
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

		if ( ! isset( $this->service->endpoint_labels()[ $endpoint ] ) ) {
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
	 * Whether the request looks like a WooCommerce address form POST.
	 *
	 * @return bool
	 */
	private function is_address_post() {
		// phpcs:ignore WordPress.Security.NonceVerification.Missing -- Delegated to WC_Form_Handler::save_address().
		return isset( $_POST['save_address'] ) || ( isset( $_POST['action'] ) && 'edit_address' === sanitize_key( wp_unslash( $_POST['action'] ) ) );
	}

	/**
	 * Whether the request looks like a WooCommerce account details form POST.
	 *
	 * @return bool
	 */
	private function is_account_details_post() {
		// phpcs:ignore WordPress.Security.NonceVerification.Missing -- Delegated to WC_Form_Handler::save_account_details().
		return isset( $_POST['save_account_details'] );
	}

	/**
	 * Return the current request path without query args.
	 *
	 * @return string
	 */
	private function current_request_path() {
		$request_uri = isset( $_SERVER['REQUEST_URI'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '/';
		$path        = wp_parse_url( $request_uri, PHP_URL_PATH );

		return is_string( $path ) && '' !== $path ? $path : '/';
	}

	/**
	 * Prime WooCommerce account endpoint query vars before POST handling.
	 *
	 * @param string $endpoint Endpoint key.
	 * @param string $value    Endpoint value.
	 * @return void
	 */
	private function prime_account_endpoint_query_var( $endpoint, $value ) {
		$value = sanitize_text_field( $value );
		if ( '' === $value ) {
			return;
		}

		if ( function_exists( 'set_query_var' ) ) {
			set_query_var( $endpoint, $value );
		}

		global $wp;
		if ( isset( $wp ) && is_object( $wp ) ) {
			if ( ! isset( $wp->query_vars ) || ! is_array( $wp->query_vars ) ) {
				$wp->query_vars = array();
			}

			$wp->query_vars[ $endpoint ] = $value;
		}
	}
}
