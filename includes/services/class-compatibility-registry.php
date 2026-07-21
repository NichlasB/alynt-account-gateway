<?php
/**
 * Compatibility metadata registry.
 *
 * @package Alynt_Account_Gateway
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Provides known plugin and hook compatibility metadata.
 */
class ALYNT_AG_Compatibility_Registry {

	/**
	 * Return known plugins that commonly touch account surfaces.
	 *
	 * @return array<string,array<string,string>>
	 */
	public function known_plugins() {
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
	public function hook_categories() {
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
	public function category_enabled( $category, $settings ) {
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
	public function category_title( $category ) {
		$titles = array(
			'login_registration'  => __( 'Other login or registration hooks are active', 'alynt-account-gateway' ),
			'security_redirects'  => __( 'Other redirect or security hooks are active', 'alynt-account-gateway' ),
			'woocommerce_account' => __( 'Other WooCommerce account hooks are active', 'alynt-account-gateway' ),
			'account_pages'       => __( 'Other account-page integrations may be active', 'alynt-account-gateway' ),
		);

		return $titles[ $category ] ?? __( 'Potential compatibility overlap detected', 'alynt-account-gateway' );
	}
}
