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
	 * Standard account endpoint labels.
	 *
	 * @return array<string,string>
	 */
	public function standard_endpoint_labels() {
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
			'customer-logout'            => __( 'Log Out', 'alynt-account-gateway' ),
		);
	}

	/**
	 * Account endpoint labels, including plugin-added WooCommerce menu items.
	 *
	 * @return array<string,string>
	 */
	public function endpoint_labels() {
		$labels = $this->standard_endpoint_labels();

		foreach ( $this->account_menu_items() as $endpoint => $label ) {
			$endpoint = sanitize_key( $endpoint );
			if ( $endpoint ) {
				$labels[ $endpoint ] = sanitize_text_field( $label );
			}
		}

		return $labels;
	}

	/**
	 * Return WooCommerce account menu items when available.
	 *
	 * @return array<string,string>
	 */
	public function account_menu_items() {
		$standard_items = $this->standard_account_menu_items();

		if ( function_exists( 'wc_get_account_menu_items' ) ) {
			$items = wc_get_account_menu_items();

			return is_array( $items ) ? $this->merge_standard_account_menu_items( $items, $standard_items ) : $standard_items;
		}

		return $standard_items;
	}

	/**
	 * Return standard WooCommerce account menu items required by the gateway.
	 *
	 * @return array<string,string>
	 */
	public function standard_account_menu_items() {
		return array(
			'dashboard'       => __( 'Dashboard', 'alynt-account-gateway' ),
			'orders'          => __( 'Orders', 'alynt-account-gateway' ),
			'downloads'       => __( 'Downloads', 'alynt-account-gateway' ),
			'edit-address'    => __( 'Addresses', 'alynt-account-gateway' ),
			'payment-methods' => __( 'Payment Methods', 'alynt-account-gateway' ),
			'edit-account'    => __( 'Account Details', 'alynt-account-gateway' ),
			'customer-logout' => __( 'Log Out', 'alynt-account-gateway' ),
		);
	}

	/**
	 * Merge required standard account items into WooCommerce-provided menu items.
	 *
	 * @param array<string,string> $items          WooCommerce menu items.
	 * @param array<string,string> $standard_items Standard required menu items.
	 * @return array<string,string>
	 */
	private function merge_standard_account_menu_items( $items, $standard_items ) {
		$merged = array();

		foreach ( $standard_items as $endpoint => $label ) {
			if ( 'customer-logout' === $endpoint ) {
				foreach ( $items as $item_endpoint => $item_label ) {
					$item_endpoint = sanitize_key( $item_endpoint );
					if ( $item_endpoint && ! isset( $standard_items[ $item_endpoint ] ) ) {
						$merged[ $item_endpoint ] = sanitize_text_field( $item_label );
					}
				}
			}

			$merged[ $endpoint ] = isset( $items[ $endpoint ] ) ? sanitize_text_field( $items[ $endpoint ] ) : $label;
		}

		return $merged;
	}

	/**
	 * Build dashboard links from WooCommerce account menu items.
	 *
	 * @param array<string,mixed> $settings Settings.
	 * @return array<int,array<string,mixed>>
	 */
	public function account_menu_links( $settings ) {
		$base  = ! empty( $settings['after_login_redirect'] ) ? $settings['after_login_redirect'] : '/my-account/';
		$links = array();
		$order = 10;

		foreach ( $this->account_menu_items() as $endpoint => $label ) {
			$endpoint = sanitize_key( $endpoint );
			if ( ! $endpoint ) {
				continue;
			}

			$links[] = array(
				'label'  => sanitize_text_field( $label ),
				'url'    => $this->account_endpoint_url( $endpoint, $base, $settings ),
				'icon'   => $this->endpoint_icon( $endpoint ),
				'order'  => $order,
				'target' => '_self',
				'roles'  => array(),
			);

			$order += 10;
		}

		return $links;
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

	/**
	 * Build a URL for a WooCommerce account endpoint.
	 *
	 * @param string              $endpoint Endpoint key.
	 * @param string              $base     Account base path.
	 * @param array<string,mixed> $settings Settings.
	 * @return string
	 */
	private function account_endpoint_url( $endpoint, $base, $settings ) {
		if ( 'customer-logout' === $endpoint ) {
			return wp_logout_url( home_url( $settings['login_path'] ?? '/login' ) );
		}

		if ( 'dashboard' === $endpoint ) {
			return trailingslashit( $base );
		}

		return trailingslashit( $base ) . trailingslashit( $endpoint );
	}

	/**
	 * Return a dashboard icon key for an endpoint.
	 *
	 * @param string $endpoint Endpoint key.
	 * @return string
	 */
	private function endpoint_icon( $endpoint ) {
		$icons = array(
			'dashboard'                  => 'dashboard',
			'orders'                     => 'orders',
			'view-order'                 => 'orders',
			'downloads'                  => 'download',
			'edit-address'               => 'map',
			'edit-account'               => 'user',
			'payment-methods'            => 'card',
			'add-payment-method'         => 'card',
			'delete-payment-method'      => 'card',
			'set-default-payment-method' => 'card',
			'customer-logout'            => 'logout',
		);

		return $icons[ $endpoint ] ?? 'link';
	}
}
