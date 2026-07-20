<?php
/**
 * Settings page readiness tests.
 *
 * @package Alynt_Account_Gateway
 */

use PHPUnit\Framework\TestCase;

require_once ALYNT_AG_PLUGIN_DIR . 'admin/class-settings-page.php';

/**
 * Tests setup readiness checks on the settings page.
 */
class SettingsPageReadinessTest extends TestCase {

	/**
	 * Invoke a private settings page helper.
	 *
	 * @param ALYNT_AG_Settings_Page $settings_page Settings page instance.
	 * @param string                 $method        Method name.
	 * @param array<int,mixed>       $args          Method arguments.
	 * @return mixed
	 */
	private function invoke_helper( $settings_page, $method, $args = array() ) {
		return alynt_ag_test_invoke_settings_page_method( $settings_page, $method, $args );
	}

	public function test_readiness_checks_classify_safe_defaults() {
		$settings_page = new ALYNT_AG_Settings_Page();
		$checks        = $this->invoke_helper( $settings_page, 'setup_readiness_checks', array( ALYNT_AG_Settings_Schema::defaults() ) );
		$counts        = $this->invoke_helper( $settings_page, 'setup_readiness_counts', array( $checks ) );

		$this->assertSame( 0, $counts['action'] );
		$this->assertGreaterThan( 0, $counts['warning'] );
		$this->assertGreaterThan( 0, $counts['ready'] );
		$this->assertSame( 'Gateway URLs', $checks[1]['label'] );
		$this->assertSame( 'ready', $checks[1]['status'] );
		$this->assertSame( 'Frontend Output', $checks[0]['label'] );
		$this->assertSame( 'warning', $checks[0]['status'] );
	}

	public function test_readiness_checks_warn_for_public_registration_without_provider() {
		$settings = ALYNT_AG_Settings_Schema::defaults();
		$settings['registration_enabled'] = true;
		$settings['turnstile_site_key']   = '';
		$settings['turnstile_secret_key'] = '';
		$settings['reoon_api_key']        = '';

		$settings_page = new ALYNT_AG_Settings_Page();
		$checks        = $this->invoke_helper( $settings_page, 'setup_readiness_checks', array( $settings ) );
		$registration  = array_values(
			array_filter(
				$checks,
				static function ( $check ) {
					return 'Public Registration' === $check['label'];
				}
			)
		);

		$this->assertSame( 'warning', $registration[0]['status'] );
		$this->assertStringContainsString( 'without Turnstile or Reoon', $registration[0]['message'] );
		$this->assertSame( 'security', $registration[0]['tab'] );
	}

	public function test_readiness_checks_require_dashboard_for_woocommerce_takeover() {
		$settings = ALYNT_AG_Settings_Schema::defaults();
		$settings['dashboard_enabled']    = false;
		$settings['woocommerce_takeover'] = true;

		$settings_page = new ALYNT_AG_Settings_Page();
		$checks        = $this->invoke_helper( $settings_page, 'setup_readiness_checks', array( $settings ) );
		$woocommerce   = array_values(
			array_filter(
				$checks,
				static function ( $check ) {
					return 'WooCommerce Takeover' === $check['label'];
				}
			)
		);

		$this->assertSame( 'action', $woocommerce[0]['status'] );
		$this->assertStringContainsString( 'requires the custom dashboard', $woocommerce[0]['message'] );
		$this->assertSame( 'dashboard', $woocommerce[0]['tab'] );
	}

	public function test_readiness_panel_renders_summary_and_tab_links() {
		$settings = ALYNT_AG_Settings_Schema::defaults();
		$settings['registration_enabled'] = true;
		$settings['turnstile_site_key']   = '';
		$settings['turnstile_secret_key'] = '';
		$settings['reoon_api_key']        = '';

		$settings_page = new ALYNT_AG_Settings_Page();

		ob_start();
		$this->invoke_helper( $settings_page, 'render_setup_readiness_panel', array( $settings ) );
		$output = ob_get_clean();

		$this->assertStringContainsString( 'Setup Readiness', $output );
		$this->assertStringContainsString( 'Review these checks before enabling public account gateway output.', $output );
		$this->assertStringContainsString( 'Action Needed', $output );
		$this->assertStringContainsString( 'Public Registration', $output );
		$this->assertStringContainsString( 'without Turnstile or Reoon', $output );
		$this->assertStringContainsString( 'tab=security', $output );
	}
}
