<?php
/**
 * Compatibility warning service.
 *
 * @package Alynt_Account_Gateway
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Detects likely account-gateway compatibility overlaps.
 */
class ALYNT_AG_Compatibility_Warnings {

	/**
	 * Return active compatibility warnings.
	 *
	 * @param array<string,mixed>|null $settings Optional settings.
	 * @return array<int,array<string,string>>
	 */
	public function warnings( $settings = null ) {
		$settings = is_array( $settings ) ? $settings : ALYNT_AG_Settings_Schema::get_settings();
		$warnings = array_merge(
			$this->known_plugin_warnings( $this->active_plugin_basenames(), $settings ),
			$this->hook_warnings( $settings )
		);

		return $this->deduplicate_warnings( $warnings );
	}

	/**
	 * Return warnings for known plugin overlap.
	 *
	 * @param array<int,string>        $active_plugins Active plugin basenames.
	 * @param array<string,mixed>|null $settings       Optional settings.
	 * @return array<int,array<string,string>>
	 */
	public function known_plugin_warnings( $active_plugins, $settings = null ) {
		$settings = is_array( $settings ) ? $settings : ALYNT_AG_Settings_Schema::get_settings();
		$warnings = array();
		$known    = $this->known_plugin_map();

		foreach ( $active_plugins as $plugin ) {
			if ( ! isset( $known[ $plugin ] ) ) {
				continue;
			}

			$entry = $known[ $plugin ];
			if ( ! $this->category_enabled( $entry['category'], $settings ) ) {
				continue;
			}

			$warnings[] = array(
				'id'       => 'plugin_' . sanitize_key( str_replace( array( '/', '.' ), '_', $plugin ) ),
				'category' => $entry['category'],
				'title'    => $entry['title'],
				'message'  => $entry['message'],
			);
		}

		return $warnings;
	}

	/**
	 * Return warnings for account-related hook overlap.
	 *
	 * @param array<string,mixed>|null $settings Optional settings.
	 * @return array<int,array<string,string>>
	 */
	public function hook_warnings( $settings = null ) {
		$settings = is_array( $settings ) ? $settings : ALYNT_AG_Settings_Schema::get_settings();
		$warnings = array();

		foreach ( $this->hook_categories() as $category => $hooks ) {
			if ( ! $this->category_enabled( $category, $settings ) ) {
				continue;
			}

			$callbacks = $this->third_party_callbacks_for_hooks( $hooks );
			if ( empty( $callbacks ) ) {
				continue;
			}

			$warnings[] = array(
				'id'       => 'hook_' . sanitize_key( $category ),
				'category' => $category,
				'title'    => $this->category_title( $category ),
				'message'  => sprintf(
					/* translators: %s: callback summary. */
					__( 'Other code is attached to related account hooks: %s. Review these integrations if gateway behavior looks unexpected.', 'alynt-account-gateway' ),
					implode( ', ', array_slice( $callbacks, 0, 6 ) )
				),
			);
		}

		return $warnings;
	}

	/**
	 * Return known active plugin basenames.
	 *
	 * @return array<int,string>
	 */
	private function active_plugin_basenames() {
		$active = get_option( 'active_plugins', array() );

		if ( ! is_array( $active ) ) {
			$active = array();
		}

		if ( is_multisite() ) {
			$network_active = get_site_option( 'active_sitewide_plugins', array() );
			if ( is_array( $network_active ) ) {
				$active = array_merge( $active, array_keys( $network_active ) );
			}
		}

		return array_values( array_unique( array_map( 'sanitize_text_field', $active ) ) );
	}

	/**
	 * Return known plugins that commonly touch account surfaces.
	 *
	 * @return array<string,array<string,string>>
	 */
	private function known_plugin_map() {
		$plugins = array(
			array(
				'plugin'   => 'wps-hide-login/wps-hide-login.php',
				'category' => 'security_redirects',
				'title'    => __( 'WPS Hide Login may overlap with login URL routing', 'alynt-account-gateway' ),
				'message'  => __( 'Both plugins can change or protect login URLs. Keep the emergency bypass documented before enabling frontend output.', 'alynt-account-gateway' ),
			),
			array(
				'plugin'   => 'theme-my-login/theme-my-login.php',
				'category' => 'login_registration',
				'title'    => __( 'Theme My Login may overlap with gateway screens', 'alynt-account-gateway' ),
				'message'  => __( 'Theme My Login also replaces login, registration, and password screens. Avoid enabling both frontends for the same routes.', 'alynt-account-gateway' ),
			),
			array(
				'plugin'   => 'loginpress/loginpress.php',
				'category' => 'login_registration',
				'title'    => __( 'LoginPress may overlap with branded login output', 'alynt-account-gateway' ),
				'message'  => __( 'LoginPress customizes native login screens. Confirm whether native wp-login.php or Alynt Account Gateway should own the branded experience.', 'alynt-account-gateway' ),
			),
			array(
				'plugin'   => 'custom-login-page-customizer/custom-login-page-customizer.php',
				'category' => 'login_registration',
				'title'    => __( 'Custom Login Page Customizer may overlap with branded login output', 'alynt-account-gateway' ),
				'message'  => __( 'This plugin customizes the native login page while Alynt Account Gateway redirects users to branded routes.', 'alynt-account-gateway' ),
			),
			array(
				'plugin'   => 'user-registration/user-registration.php',
				'category' => 'login_registration',
				'title'    => __( 'User Registration may overlap with account creation', 'alynt-account-gateway' ),
				'message'  => __( 'Both plugins can own public registration. Confirm which registration flow should create users.', 'alynt-account-gateway' ),
			),
			array(
				'plugin'   => 'ultimate-member/ultimate-member.php',
				'category' => 'login_registration',
				'title'    => __( 'Ultimate Member may overlap with account pages', 'alynt-account-gateway' ),
				'message'  => __( 'Ultimate Member provides login, registration, and account pages that may compete with the gateway routes.', 'alynt-account-gateway' ),
			),
			array(
				'plugin'   => 'paid-memberships-pro/paid-memberships-pro.php',
				'category' => 'account_pages',
				'title'    => __( 'Paid Memberships Pro may overlap with account pages', 'alynt-account-gateway' ),
				'message'  => __( 'Membership account flows can add redirects or account screens. Verify member account links after enabling the gateway.', 'alynt-account-gateway' ),
			),
			array(
				'plugin'   => 'memberpress/memberpress.php',
				'category' => 'account_pages',
				'title'    => __( 'MemberPress may overlap with account pages', 'alynt-account-gateway' ),
				'message'  => __( 'MemberPress account routes and redirects may need explicit testing with the custom dashboard.', 'alynt-account-gateway' ),
			),
			array(
				'plugin'   => 'woocommerce-memberships/woocommerce-memberships.php',
				'category' => 'woocommerce_account',
				'title'    => __( 'WooCommerce Memberships may add account endpoints', 'alynt-account-gateway' ),
				'message'  => __( 'Plugin-added WooCommerce account endpoints are preserved when possible, but should be checked after dashboard takeover.', 'alynt-account-gateway' ),
			),
			array(
				'plugin'   => 'woocommerce-subscriptions/woocommerce-subscriptions.php',
				'category' => 'woocommerce_account',
				'title'    => __( 'WooCommerce Subscriptions may add account endpoints', 'alynt-account-gateway' ),
				'message'  => __( 'Subscription account endpoints are delegated through WooCommerce handlers. Verify subscription views after dashboard takeover.', 'alynt-account-gateway' ),
			),
			array(
				'plugin'   => 'woocommerce-payments/woocommerce-payments.php',
				'category' => 'woocommerce_account',
				'title'    => __( 'WooPayments may affect payment account sections', 'alynt-account-gateway' ),
				'message'  => __( 'Payment method screens are delegated to WooCommerce. Verify saved-payment management after dashboard takeover.', 'alynt-account-gateway' ),
			),
		);
		$map     = array();

		foreach ( $plugins as $plugin ) {
			$basename = $plugin['plugin'];
			unset( $plugin['plugin'] );
			$map[ $basename ] = $plugin;
		}

		return $map;
	}

	/**
	 * Return watched hook categories.
	 *
	 * @return array<string,array<int,string>>
	 */
	private function hook_categories() {
		return array(
			'login_registration'  => array(
				'login_init',
				'login_url',
				'lostpassword_url',
				'register_url',
				'logout_url',
				'registration_errors',
				'user_register',
			),
			'security_redirects'  => array(
				'template_redirect',
				'admin_init',
				'wp_login',
				'authenticate',
			),
			'woocommerce_account' => array(
				'woocommerce_account_menu_items',
				'woocommerce_get_endpoint_url',
				'woocommerce_account_dashboard',
				'woocommerce_account_orders_endpoint',
				'woocommerce_account_downloads_endpoint',
				'woocommerce_account_edit-address_endpoint',
				'woocommerce_account_edit-account_endpoint',
				'woocommerce_account_payment-methods_endpoint',
			),
		);
	}

	/**
	 * Whether a warning category matters for current settings.
	 *
	 * @param string              $category Category key.
	 * @param array<string,mixed> $settings Settings.
	 * @return bool
	 */
	private function category_enabled( $category, $settings ) {
		if ( 'woocommerce_account' === $category ) {
			return ! empty( $settings['dashboard_enabled'] ) && ! empty( $settings['woocommerce_takeover'] );
		}

		if ( 'account_pages' === $category ) {
			return ! empty( $settings['dashboard_enabled'] );
		}

		return ! empty( $settings['frontend_enabled'] ) || ! empty( $settings['registration_enabled'] );
	}

	/**
	 * Return a human title for one category.
	 *
	 * @param string $category Category key.
	 * @return string
	 */
	private function category_title( $category ) {
		$titles = array(
			'login_registration'  => __( 'Other login or registration hooks are active', 'alynt-account-gateway' ),
			'security_redirects'  => __( 'Other redirect or security hooks are active', 'alynt-account-gateway' ),
			'woocommerce_account' => __( 'Other WooCommerce account hooks are active', 'alynt-account-gateway' ),
			'account_pages'       => __( 'Other account-page integrations may be active', 'alynt-account-gateway' ),
		);

		return $titles[ $category ] ?? __( 'Potential compatibility overlap detected', 'alynt-account-gateway' );
	}

	/**
	 * Return third-party callback summaries for hooks.
	 *
	 * @param array<int,string> $hooks Hook names.
	 * @return array<int,string>
	 */
	private function third_party_callbacks_for_hooks( $hooks ) {
		global $wp_filter;

		if ( empty( $wp_filter ) || ! is_array( $wp_filter ) ) {
			return array();
		}

		$callbacks = array();

		foreach ( $hooks as $hook ) {
			if ( empty( $wp_filter[ $hook ] ) ) {
				continue;
			}

			foreach ( $this->extract_callbacks( $wp_filter[ $hook ] ) as $callback ) {
				if ( $this->is_own_callback( $callback ) || $this->is_platform_callback( $callback ) ) {
					continue;
				}

				$callbacks[] = $hook . ':' . $this->callback_label( $callback );
			}
		}

		return array_values( array_unique( $callbacks ) );
	}

	/**
	 * Extract callbacks from WP_Hook or array-shaped hook storage.
	 *
	 * @param mixed $hook Hook storage.
	 * @return array<int,mixed>
	 */
	private function extract_callbacks( $hook ) {
		$callbacks = array();
		$groups    = array();

		if ( is_object( $hook ) && isset( $hook->callbacks ) && is_array( $hook->callbacks ) ) {
			$groups = $hook->callbacks;
		} elseif ( is_array( $hook ) ) {
			$groups = $hook;
		}

		foreach ( $groups as $priority_callbacks ) {
			if ( ! is_array( $priority_callbacks ) ) {
				continue;
			}

			foreach ( $priority_callbacks as $callback ) {
				if ( is_array( $callback ) && array_key_exists( 'function', $callback ) ) {
					$callbacks[] = $callback['function'];
				} else {
					$callbacks[] = $callback;
				}
			}
		}

		return $callbacks;
	}

	/**
	 * Whether callback belongs to this plugin.
	 *
	 * @param mixed $callback Callback.
	 * @return bool
	 */
	private function is_own_callback( $callback ) {
		if ( is_array( $callback ) && isset( $callback[0] ) ) {
			$target = is_object( $callback[0] ) ? get_class( $callback[0] ) : (string) $callback[0];
			return 0 === strpos( $target, 'ALYNT_AG_' );
		}

		if ( is_string( $callback ) ) {
			return 0 === strpos( $callback, 'alynt_ag_' ) || 0 === strpos( $callback, 'ALYNT_AG_' );
		}

		return false;
	}

	/**
	 * Whether callback is expected WordPress or WooCommerce platform behavior.
	 *
	 * @param mixed $callback Callback.
	 * @return bool
	 */
	private function is_platform_callback( $callback ) {
		if ( is_array( $callback ) && isset( $callback[0] ) ) {
			$target = is_object( $callback[0] ) ? get_class( $callback[0] ) : (string) $callback[0];

			return 0 === strpos( $target, 'WP_' )
				|| 0 === strpos( $target, 'Automattic\\WooCommerce\\' )
				|| 0 === strpos( $target, 'WC_' );
		}

		if ( ! is_string( $callback ) ) {
			return false;
		}

		if (
			0 === strpos( $callback, 'wp_' )
			|| 0 === strpos( $callback, '_wp_' )
			|| 0 === strpos( $callback, '_maybe_update_' )
			|| 0 === strpos( $callback, 'rest_' )
			|| 0 === strpos( $callback, 'wc_' )
			|| 0 === strpos( $callback, 'woocommerce_' )
		) {
			return true;
		}

		$core_callbacks = array(
			'_wp_admin_bar_init',
			'redirect_canonical',
			'send_frame_options_header',
			'wp_admin_headers',
			'wp_maybe_update_user_counts',
			'wp_old_slug_redirect',
		);

		return in_array( $callback, $core_callbacks, true );
	}

	/**
	 * Return a compact callback label.
	 *
	 * @param mixed $callback Callback.
	 * @return string
	 */
	private function callback_label( $callback ) {
		if ( is_string( $callback ) ) {
			return $callback;
		}

		if ( is_array( $callback ) && isset( $callback[0], $callback[1] ) ) {
			$target = is_object( $callback[0] ) ? get_class( $callback[0] ) : (string) $callback[0];
			return $target . '::' . (string) $callback[1];
		}

		if ( $callback instanceof Closure ) {
			return 'closure';
		}

		return 'callback';
	}

	/**
	 * Remove duplicate warnings.
	 *
	 * @param array<int,array<string,string>> $warnings Warnings.
	 * @return array<int,array<string,string>>
	 */
	private function deduplicate_warnings( $warnings ) {
		$seen = array();
		$out  = array();

		foreach ( $warnings as $warning ) {
			$id = $warning['id'] ?? md5( wp_json_encode( $warning ) );
			if ( isset( $seen[ $id ] ) ) {
				continue;
			}

			$seen[ $id ] = true;
			$out[]       = $warning;
		}

		return $out;
	}
}
