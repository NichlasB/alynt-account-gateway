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

	public function test_checkout_gate_warns_when_woocommerce_guest_checkout_is_enabled() {
		$GLOBALS['alynt_ag_test_options']['woocommerce_enable_guest_checkout'] = 'yes';
		$service = new ALYNT_AG_Compatibility_Warnings();
		$warnings = $service->woocommerce_checkout_warnings(
			array( 'woocommerce_require_login_checkout' => true )
		);

		$this->assertCount( 1, $warnings );
		$this->assertSame( 'woocommerce_guest_checkout', $warnings[0]['id'] );
	}

	public function test_checkout_gate_does_not_warn_when_its_setting_is_disabled() {
		$GLOBALS['alynt_ag_test_options']['woocommerce_enable_guest_checkout'] = 'yes';
		$service = new ALYNT_AG_Compatibility_Warnings();

		$this->assertSame(
			array(),
			$service->woocommerce_checkout_warnings(
				array( 'woocommerce_require_login_checkout' => false )
			)
		);
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

	public function test_facade_uses_injected_registry_and_hook_inspector() {
		$registry = new class() extends ALYNT_AG_Compatibility_Registry {
			public function known_plugins() {
				return array();
			}

			public function hook_categories() {
				return array( 'login_registration' => array( 'login_init' ) );
			}

			public function category_enabled( $category, $settings ) {
				return true;
			}

			public function category_title( $category ) {
				return 'Injected title';
			}
		};
		$inspector = new class() extends ALYNT_AG_Compatibility_Hook_Inspector {
			public function third_party_callbacks_for_hooks( $hooks ) {
				return array( 'login_init:Injected_Callback::run' );
			}
		};
		$service   = new ALYNT_AG_Compatibility_Warnings( $registry, $inspector );
		$warnings  = $service->hook_warnings( array( 'frontend_enabled' => true ) );

		$this->assertCount( 1, $warnings );
		$this->assertSame( 'Injected title', $warnings[0]['title'] );
		$this->assertStringContainsString( 'Injected_Callback::run', $warnings[0]['message'] );
	}

	public function test_compatibility_files_and_loader_order_stay_structurally_bounded() {
		$files = array(
			'class-compatibility-registry.php',
			'class-compatibility-hook-inspector.php',
			'class-compatibility-warnings.php',
		);

		foreach ( $files as $file ) {
			$this->assertLessThanOrEqual(
				300,
				count( file( ALYNT_AG_PLUGIN_DIR . 'includes/services/' . $file ) ),
				$file
			);
		}

		$loader    = file_get_contents( ALYNT_AG_PLUGIN_DIR . 'includes/class-loader.php' );
		$registry  = strpos( $loader, 'class-compatibility-registry.php' );
		$inspector = strpos( $loader, 'class-compatibility-hook-inspector.php' );
		$facade    = strpos( $loader, 'class-compatibility-warnings.php' );

		$this->assertIsInt( $registry );
		$this->assertIsInt( $inspector );
		$this->assertIsInt( $facade );
		$this->assertLessThan( $facade, $registry );
		$this->assertLessThan( $facade, $inspector );
	}
}
