<?php
/**
 * Compatibility warning tests.
 *
 * @package Alynt_Account_Gateway
 */

use PHPUnit\Framework\TestCase;

/**
 * Tests compatibility overlap detection.
 */
class CompatibilityWarningsTest extends TestCase {

	protected function tearDown(): void {
		unset( $GLOBALS['wp_filter'], $GLOBALS['alynt_ag_test_options']['active_plugins'] );

		parent::tearDown();
	}

	public function test_known_login_plugin_warns_when_frontend_output_is_enabled() {
		$service = new ALYNT_AG_Compatibility_Warnings();

		$warnings = $service->known_plugin_warnings(
			array( 'theme-my-login/theme-my-login.php' ),
			array(
				'frontend_enabled'     => true,
				'registration_enabled' => false,
				'dashboard_enabled'    => false,
				'woocommerce_takeover' => false,
			)
		);

		$this->assertCount( 1, $warnings );
		$this->assertSame( 'login_registration', $warnings[0]['category'] );
		$this->assertStringContainsString( 'Theme My Login', $warnings[0]['title'] );
	}

	public function test_known_woocommerce_plugin_warns_only_when_takeover_is_enabled() {
		$service = new ALYNT_AG_Compatibility_Warnings();

		$inactive = $service->known_plugin_warnings(
			array( 'woocommerce-subscriptions/woocommerce-subscriptions.php' ),
			array(
				'frontend_enabled'     => true,
				'registration_enabled' => true,
				'dashboard_enabled'    => true,
				'woocommerce_takeover' => false,
			)
		);

		$active = $service->known_plugin_warnings(
			array( 'woocommerce-subscriptions/woocommerce-subscriptions.php' ),
			array(
				'frontend_enabled'     => true,
				'registration_enabled' => true,
				'dashboard_enabled'    => true,
				'woocommerce_takeover' => true,
			)
		);

		$this->assertSame( array(), $inactive );
		$this->assertCount( 1, $active );
		$this->assertSame( 'woocommerce_account', $active[0]['category'] );
	}

	public function test_hook_warnings_ignore_own_callbacks_and_report_third_party_callbacks() {
		$GLOBALS['wp_filter'] = array(
			'login_init' => array(
				10 => array(
					array( 'function' => array( 'ALYNT_AG_Frontend', 'maybe_redirect_native_login' ) ),
					array( 'function' => array( 'Other_Login_Plugin', 'redirect' ) ),
				),
			),
		);

		$service = new ALYNT_AG_Compatibility_Warnings();
		$warnings = $service->hook_warnings(
			array(
				'frontend_enabled'     => true,
				'registration_enabled' => false,
				'dashboard_enabled'    => false,
				'woocommerce_takeover' => false,
			)
		);

		$this->assertCount( 1, $warnings );
		$this->assertSame( 'login_registration', $warnings[0]['category'] );
		$this->assertStringContainsString( 'Other_Login_Plugin::redirect', $warnings[0]['message'] );
		$this->assertStringNotContainsString( 'ALYNT_AG_Frontend', $warnings[0]['message'] );
	}

	public function test_hook_warnings_ignore_platform_callbacks() {
		$GLOBALS['wp_filter'] = array(
			'login_init' => array(
				10 => array(
					array( 'function' => 'send_frame_options_header' ),
					array( 'function' => 'wp_admin_headers' ),
				),
			),
			'woocommerce_account_orders_endpoint' => array(
				10 => array(
					array( 'function' => 'woocommerce_account_orders' ),
				),
			),
			'template_redirect' => array(
				10 => array(
					array( 'function' => array( 'WC_AJAX', 'do_wc_ajax' ) ),
					array( 'function' => array( 'WC_Form_Handler', 'save_account_details' ) ),
					array( 'function' => '_maybe_update_plugins' ),
					array( 'function' => 'redirect_canonical' ),
					array( 'function' => 'rest_output_link_header' ),
					array( 'function' => 'wc_disable_author_archives_for_customers' ),
					array( 'function' => 'wp_redirect_admin_locations' ),
				),
			),
		);

		$service = new ALYNT_AG_Compatibility_Warnings();
		$warnings = $service->hook_warnings(
			array(
				'frontend_enabled'     => true,
				'registration_enabled' => false,
				'dashboard_enabled'    => true,
				'woocommerce_takeover' => true,
			)
		);

		$this->assertSame( array(), $warnings );
	}

	public function test_warnings_reads_active_plugins_option() {
		$GLOBALS['alynt_ag_test_options']['active_plugins'] = array( 'wps-hide-login/wps-hide-login.php' );

		$service = new ALYNT_AG_Compatibility_Warnings();
		$warnings = $service->warnings(
			array(
				'frontend_enabled'     => true,
				'registration_enabled' => false,
				'dashboard_enabled'    => false,
				'woocommerce_takeover' => false,
			)
		);

		$this->assertCount( 1, $warnings );
		$this->assertSame( 'security_redirects', $warnings[0]['category'] );
	}
}
