<?php
/**
 * Settings page handoff tools tests.
 *
 * @package Alynt_Account_Gateway
 */

use PHPUnit\Framework\TestCase;

require_once ALYNT_AG_PLUGIN_DIR . 'admin/class-settings-page.php';

/**
 * Tests non-secret setup summary and handoff tools.
 */
class SettingsPageHandoffToolsTest extends TestCase {

	/**
	 * Invoke a settings page helper.
	 *
	 * @param ALYNT_AG_Settings_Page $settings_page Settings page instance.
	 * @param string                 $method        Method name.
	 * @param array<int,mixed>       $args          Method arguments.
	 * @return mixed
	 */
	private function invoke_helper( $settings_page, $method, $args = array() ) {
		return alynt_ag_test_invoke_settings_page_method( $settings_page, $method, $args );
	}

	public function test_configuration_summary_reports_non_secret_setup_state() {
		$settings = ALYNT_AG_Settings_Schema::defaults();
		$settings['frontend_enabled']            = true;
		$settings['turnstile_site_key']          = 'site-key';
		$settings['turnstile_secret_key']        = 'secret-key';
		$settings['reoon_api_key']               = 'reoon-secret';
		$settings['webhook_signing_secret']      = 'webhook-secret';
		$settings['account_created_webhook']     = 'https://receiver.example/hook';
		$settings['email_test_recipient']        = 'owner@example.test';
		$settings['emergency_bypass_key']        = 'bypass-secret';
		$settings['brand_logo_id']               = 123;
		$settings['background_image_id']         = 456;
		$settings['woocommerce_takeover']        = true;
		$settings['woocommerce_require_login_checkout'] = true;

		$settings_page = new ALYNT_AG_Settings_Page();
		$items         = $this->invoke_helper( $settings_page, 'configuration_summary_items', array( $settings ) );
		$output_values = wp_json_encode( $items );

		$this->assertStringContainsString( 'Frontend Output', $output_values );
		$this->assertStringContainsString( 'Enabled', $output_values );
		$this->assertStringContainsString( 'Turnstile', $output_values );
		$this->assertStringContainsString( 'Configured', $output_values );
		$this->assertStringNotContainsString( 'secret-key', $output_values );
		$this->assertStringNotContainsString( 'reoon-secret', $output_values );
		$this->assertStringNotContainsString( 'webhook-secret', $output_values );
		$this->assertStringNotContainsString( 'owner@example.test', $output_values );
		$this->assertStringNotContainsString( 'bypass-secret', $output_values );
	}

	public function test_configuration_summary_panel_renders_review_links() {
		$settings_page = new ALYNT_AG_Settings_Page();

		ob_start();
		$this->invoke_helper( $settings_page, 'render_configuration_summary_panel', array( ALYNT_AG_Settings_Schema::defaults() ) );
		$output = ob_get_clean();

		$this->assertStringContainsString( 'Current Configuration Summary', $output );
		$this->assertStringContainsString( 'Review the non-secret setup state', $output );
		$this->assertStringContainsString( 'Login URL Path', $output );
		$this->assertStringContainsString( 'tab=urls', $output );
		$this->assertStringContainsString( 'Emergency Bypass Key', $output );
		$this->assertStringContainsString( 'tab=advanced_tools', $output );
	}

	public function test_handoff_tools_render_setup_order_and_contextual_checks() {
		$settings = ALYNT_AG_Settings_Schema::defaults();
		$settings['registration_enabled']        = true;
		$settings['dashboard_enabled']           = true;
		$settings['woocommerce_takeover']        = true;
		$settings['account_created_webhook']     = 'https://receiver.example/hook';

		$settings_page = new ALYNT_AG_Settings_Page();

		ob_start();
		$this->invoke_helper( $settings_page, 'render_handoff_tools', array( $settings ) );
		$output = ob_get_clean();

		$this->assertStringContainsString( 'Site Handoff', $output );
		$this->assertStringContainsString( 'Recommended setup order', $output );
		$this->assertStringContainsString( 'Registration flow tested', $output );
		$this->assertStringContainsString( 'Dashboard tested', $output );
		$this->assertStringContainsString( 'WooCommerce account and checkout paths tested', $output );
		$this->assertStringContainsString( 'Webhook receiver tested', $output );
		$this->assertStringContainsString( 'Download Handoff Summary', $output );
		$this->assertStringContainsString( 'alynt_ag_export_handoff_summary', $output );
	}

	public function test_handoff_summary_excludes_secret_values() {
		$settings = ALYNT_AG_Settings_Schema::defaults();
		$settings['turnstile_site_key']          = 'site-key';
		$settings['turnstile_secret_key']        = 'turnstile-secret';
		$settings['reoon_api_key']               = 'reoon-secret';
		$settings['webhook_signing_secret']      = 'webhook-secret';
		$settings['account_created_webhook']     = 'https://receiver.example/hook';
		$settings['email_test_recipient']        = 'owner@example.test';
		$settings['emergency_bypass_key']        = 'bypass-secret';

		$settings_page = new ALYNT_AG_Settings_Page();
		$summary       = $this->invoke_helper( $settings_page, 'build_handoff_summary', array( $settings ) );

		$this->assertStringContainsString( 'Alynt Account Gateway Handoff Summary', $summary );
		$this->assertStringContainsString( 'Current Configuration', $summary );
		$this->assertStringContainsString( '- Turnstile: Configured', $summary );
		$this->assertStringContainsString( '- Reoon: Configured', $summary );
		$this->assertStringContainsString( 'Security Note: This summary intentionally excludes API keys', $summary );
		$this->assertStringNotContainsString( 'turnstile-secret', $summary );
		$this->assertStringNotContainsString( 'reoon-secret', $summary );
		$this->assertStringNotContainsString( 'webhook-secret', $summary );
		$this->assertStringNotContainsString( 'owner@example.test', $summary );
		$this->assertStringNotContainsString( 'bypass-secret', $summary );
	}
}
