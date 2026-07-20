<?php
/**
 * WooCommerce account navigation.
 *
 * @package Alynt_Account_Gateway
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Builds WooCommerce account navigation metadata and URLs.
 */
class ALYNT_AG_WooCommerce_Navigation extends ALYNT_AG_Service_Collaborator {

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
		$labels = $this->service->standard_endpoint_labels();

		foreach ( $this->service->account_menu_items() as $endpoint => $label ) {
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
		$standard_items = $this->service->standard_account_menu_items();

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
	 * Return sanitized endpoint keys hidden from dashboard navigation.
	 *
	 * @param array<string,mixed> $settings Settings.
	 * @return array<int,string>
	 */
	public function hidden_account_menu_items( $settings ) {
		$items = isset( $settings['woocommerce_hidden_menu_items'] ) && is_array( $settings['woocommerce_hidden_menu_items'] )
			? $settings['woocommerce_hidden_menu_items']
			: array();

		return array_values( array_unique( array_filter( array_map( 'sanitize_key', $items ) ) ) );
	}

	/**
	 * Return whether an endpoint should appear in dashboard navigation.
	 *
	 * @param string              $endpoint Endpoint key.
	 * @param array<string,mixed> $settings Settings.
	 * @return bool
	 */
	public function is_account_menu_item_visible( $endpoint, $settings ) {
		$endpoint = sanitize_key( $endpoint );

		return $endpoint && ! in_array( $endpoint, $this->service->hidden_account_menu_items( $settings ), true );
	}

	/**
	 * Return account menu items that should appear in dashboard navigation.
	 *
	 * @param array<string,mixed> $settings Settings.
	 * @return array<string,string>
	 */
	public function visible_account_menu_items( $settings ) {
		return array_filter(
			$this->service->account_menu_items(),
			function ( $label, $endpoint ) use ( $settings ) {
				unset( $label );

				return $this->service->is_account_menu_item_visible( $endpoint, $settings );
			},
			ARRAY_FILTER_USE_BOTH
		);
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

		foreach ( $this->service->visible_account_menu_items( $settings ) as $endpoint => $label ) {
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
	 * Build an order-details URL inside the configured account area.
	 *
	 * @param int                 $order_id Order ID.
	 * @param array<string,mixed> $settings Settings.
	 * @return string
	 */
	public function order_url( $order_id, $settings ) {
		return trailingslashit( $this->service->endpoint_url( 'view-order', $settings ) ) . trailingslashit( absint( $order_id ) );
	}

	/**
	 * Build a billing or shipping address-editor URL.
	 *
	 * @param string              $type     Address type.
	 * @param array<string,mixed> $settings Settings.
	 * @return string
	 */
	public function address_url( $type, $settings ) {
		$type = sanitize_key( $type );
		$type = in_array( $type, array( 'billing', 'shipping' ), true ) ? $type : 'billing';

		return trailingslashit( $this->service->endpoint_url( 'edit-address', $settings ) ) . trailingslashit( $type );
	}

	/**
	 * Build a URL for a WooCommerce account endpoint from settings.
	 *
	 * @param string              $endpoint Endpoint key.
	 * @param array<string,mixed> $settings Settings.
	 * @return string
	 */
	public function endpoint_url( $endpoint, $settings ) {
		$base = ! empty( $settings['after_login_redirect'] ) ? $settings['after_login_redirect'] : '/my-account/';

		return $this->account_endpoint_url( $endpoint, $base, $settings );
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
	 * Merge required standard account items into WooCommerce-provided items.
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
