<?php
/**
 * WooCommerce integration.
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
	 * This does not disable the endpoint or its direct URL.
	 *
	 * @param string              $endpoint Endpoint key.
	 * @param array<string,mixed> $settings Settings.
	 * @return bool
	 */
	public function is_account_menu_item_visible( $endpoint, $settings ) {
		$endpoint = sanitize_key( $endpoint );

		return $endpoint && ! in_array( $endpoint, $this->hidden_account_menu_items( $settings ), true );
	}

	/**
	 * Return account menu items that should appear in dashboard navigation.
	 *
	 * @param array<string,mixed> $settings Settings.
	 * @return array<string,string>
	 */
	public function visible_account_menu_items( $settings ) {
		return array_filter(
			$this->account_menu_items(),
			function ( $label, $endpoint ) use ( $settings ) {
				unset( $label );

				return $this->is_account_menu_item_visible( $endpoint, $settings );
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

		foreach ( $this->visible_account_menu_items( $settings ) as $endpoint => $label ) {
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
		add_action( 'template_redirect', array( $this, 'maybe_handle_account_form_post' ), 0 );
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
	 * Let WooCommerce process My Account form POSTs before the branded shell renders.
	 *
	 * The gateway renders at template_redirect priority 1 to avoid canonical redirect
	 * interference on custom account routes. WooCommerce account form handlers normally
	 * run later, so delegated account POSTs need this early pass-through.
	 *
	 * @return void
	 */
	public function maybe_handle_account_form_post() {
		$method = isset( $_SERVER['REQUEST_METHOD'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REQUEST_METHOD'] ) ) : '';
		if ( 'POST' !== strtoupper( $method ) ) {
			return;
		}

		$settings = ALYNT_AG_Settings_Schema::get_settings();
		if ( ! $this->takeover_enabled( $settings ) || ! is_user_logged_in() || ! class_exists( 'WC_Form_Handler' ) ) {
			return;
		}

		$endpoint = $this->endpoint_from_path( $this->current_request_path(), $settings );
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
	 * Prime WooCommerce account endpoint query vars before delegated POST handling.
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
		ob_start();

		if ( function_exists( 'woocommerce_output_all_notices' ) ) {
			woocommerce_output_all_notices();
		}

		do_action( 'woocommerce_account_' . sanitize_key( $endpoint ) . '_endpoint', $value );

		$output        = ob_get_clean();
		$content_check = is_string( $output ) ? trim( $output ) : '';
		$content_check = preg_replace( '#<div\s+class=(["\'])woocommerce-notices-wrapper\1>\s*</div>#i', '', $content_check );

		if ( ! is_string( $output ) || '' === trim( (string) $content_check ) ) {
			return false;
		}

		echo $output; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- WooCommerce endpoint handlers render trusted account template output.
		return true;
	}

	/**
	 * Return a small normalized list of a customer's recent orders.
	 *
	 * @param int $user_id WordPress user ID.
	 * @param int $limit   Maximum orders to return.
	 * @return array<int,array<string,mixed>>
	 */
	public function recent_orders( $user_id, $limit = 3 ) {
		$user_id = absint( $user_id );
		$limit   = max( 1, min( 5, absint( $limit ) ) );

		if ( ! $user_id || ! function_exists( 'wc_get_orders' ) ) {
			return array();
		}

		$orders = wc_get_orders(
			array(
				'customer_id' => $user_id,
				'limit'       => $limit,
				'orderby'     => 'date',
				'order'       => 'DESC',
				'return'      => 'objects',
			)
		);

		if ( ! is_array( $orders ) ) {
			return array();
		}

		$normalized = array();
		foreach ( $orders as $order ) {
			if (
				! is_object( $order )
				|| ! method_exists( $order, 'get_id' )
				|| ! method_exists( $order, 'get_order_number' )
				|| ! method_exists( $order, 'get_status' )
			) {
				continue;
			}

			$order_id = absint( $order->get_id() );
			if ( ! $order_id ) {
				continue;
			}

			$status         = sanitize_key( $order->get_status() );
			$date           = method_exists( $order, 'get_date_created' ) ? $order->get_date_created() : null;
			$total          = method_exists( $order, 'get_formatted_order_total' )
				? html_entity_decode( wp_strip_all_tags( $order->get_formatted_order_total() ), ENT_QUOTES, 'UTF-8' )
				: '';
			$formatted_date = '';
			if ( is_object( $date ) && method_exists( $date, 'getTimestamp' ) ) {
				$formatted_date = function_exists( 'wc_format_datetime' )
					? wc_format_datetime( $date, get_option( 'date_format', 'F j, Y' ) )
					: date_i18n( get_option( 'date_format', 'F j, Y' ), $date->getTimestamp() );
			}

			$normalized[] = array(
				'id'     => $order_id,
				'number' => sanitize_text_field( $order->get_order_number() ),
				'status' => function_exists( 'wc_get_order_status_name' )
					? sanitize_text_field( wc_get_order_status_name( $status ) )
					: sanitize_text_field( ucfirst( str_replace( '-', ' ', $status ) ) ),
				'date'   => sanitize_text_field( $formatted_date ),
				'total'  => sanitize_text_field( $total ),
			);
		}

		return $normalized;
	}

	/**
	 * Return normalized available downloads for a customer.
	 *
	 * @param int $user_id WordPress user ID.
	 * @param int $limit   Maximum downloads to return.
	 * @return array<int,array<string,mixed>>
	 */
	public function available_downloads( $user_id, $limit = 3 ) {
		$user_id = absint( $user_id );
		$limit   = max( 1, min( 5, absint( $limit ) ) );

		if ( ! $user_id || ! function_exists( 'wc_get_customer_available_downloads' ) ) {
			return array();
		}

		$downloads = wc_get_customer_available_downloads( $user_id );
		if ( ! is_array( $downloads ) ) {
			return array();
		}

		$normalized = array();
		foreach ( $downloads as $download ) {
			if ( ! is_array( $download ) ) {
				continue;
			}

			$url          = isset( $download['download_url'] ) ? esc_url_raw( $download['download_url'] ) : '';
			$name         = isset( $download['download_name'] ) ? sanitize_text_field( $download['download_name'] ) : '';
			$product_name = isset( $download['product_name'] ) ? sanitize_text_field( $download['product_name'] ) : '';

			if ( '' === $url || ( '' === $name && '' === $product_name ) ) {
				continue;
			}

			$timestamp = ! empty( $download['access_expires'] )
				? strtotime( (string) $download['access_expires'] )
				: false;
			$expires   = $timestamp
				? date_i18n( get_option( 'date_format', 'F j, Y' ), $timestamp )
				: '';

			$normalized[] = array(
				'name'         => '' !== $name ? $name : $product_name,
				'product_name' => $product_name,
				'url'          => $url,
				'remaining'    => isset( $download['downloads_remaining'] ) && is_numeric( $download['downloads_remaining'] )
					? max( 0, (int) $download['downloads_remaining'] )
					: null,
				'expires'      => sanitize_text_field( $expires ),
			);

			if ( count( $normalized ) >= $limit ) {
				break;
			}
		}

		return $normalized;
	}

	/**
	 * Return normalized billing and shipping address lines for a customer.
	 *
	 * @param int $user_id WordPress user ID.
	 * @return array<string,array<int,string>>
	 */
	public function saved_addresses( $user_id ) {
		$addresses = array(
			'billing'  => array(),
			'shipping' => array(),
		);
		$user_id   = absint( $user_id );

		if ( ! $user_id || ! function_exists( 'wc_get_account_formatted_address' ) ) {
			return $addresses;
		}

		foreach ( array_keys( $addresses ) as $type ) {
			$formatted = wc_get_account_formatted_address( $type, $user_id );
			if ( ! is_string( $formatted ) || '' === trim( $formatted ) ) {
				continue;
			}

			$with_lines = preg_replace( '#<br\s*/?>#i', "\n", $formatted );
			$plain      = html_entity_decode( wp_strip_all_tags( (string) $with_lines ), ENT_QUOTES, 'UTF-8' );
			$plain      = str_replace( "\xc2\xa0", ' ', $plain );
			$lines      = preg_split( '/\r\n|\r|\n/', $plain );

			if ( ! is_array( $lines ) ) {
				continue;
			}

			$addresses[ $type ] = array_values(
				array_filter(
					array_map( 'sanitize_text_field', $lines ),
					static function ( $line ) {
						return '' !== $line;
					}
				)
			);
		}

		return $addresses;
	}

	/**
	 * Build an order-details URL inside the configured account area.
	 *
	 * @param int                 $order_id Order ID.
	 * @param array<string,mixed> $settings Settings.
	 * @return string
	 */
	public function order_url( $order_id, $settings ) {
		return trailingslashit( $this->endpoint_url( 'view-order', $settings ) ) . trailingslashit( absint( $order_id ) );
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

		return trailingslashit( $this->endpoint_url( 'edit-address', $settings ) ) . trailingslashit( $type );
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
