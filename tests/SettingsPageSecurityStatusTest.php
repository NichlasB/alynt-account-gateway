<?php
/**
 * Settings page security status tests.
 *
 * @package Alynt_Account_Gateway
 */

use PHPUnit\Framework\TestCase;

require_once ALYNT_AG_PLUGIN_DIR . 'admin/class-settings-page.php';

/**
 * Tests security and spam status guidance on the settings page.
 */
class SettingsPageSecurityStatusTest extends TestCase {

	/**
	 * Invoke a private settings page helper.
	 *
	 * @param ALYNT_AG_Settings_Page $settings_page Settings page instance.
	 * @param string                 $method        Method name.
	 * @param array<int,mixed>       $args          Method arguments.
	 * @return mixed
	 */
	private function invoke_helper( $settings_page, $method, $args = array() ) {
		$reflection = new ReflectionMethod( $settings_page, $method );

		return $reflection->invokeArgs( $settings_page, $args );
	}

	public function test_security_status_panel_warns_when_providers_are_missing() {
		$settings      = ALYNT_AG_Settings_Schema::defaults();
		$settings_page = new ALYNT_AG_Settings_Page();

		ob_start();
		$this->invoke_helper( $settings_page, 'render_security_status_panel', array( $settings ) );
		$output = ob_get_clean();

		$this->assertStringContainsString( 'Security And Spam Status', $output );
		$this->assertStringContainsString( 'No anti-spam provider is fully configured.', $output );
		$this->assertStringContainsString( 'Provider Readiness', $output );
		$this->assertStringContainsString( 'Protection Mode', $output );
		$this->assertStringContainsString( 'Turnstile', $output );
		$this->assertStringContainsString( 'Reoon Email Verifier', $output );
		$this->assertStringContainsString( 'Reoon Default Policy', $output );
		$this->assertStringContainsString( 'Rate Limit Posture', $output );
		$this->assertStringContainsString( 'Registration Attempts', $output );
		$this->assertStringContainsString( 'Password Reset Attempts', $output );
	}

	public function test_security_status_panel_marks_configured_providers_and_policy() {
		$settings                         = ALYNT_AG_Settings_Schema::defaults();
		$settings['turnstile_site_key']   = 'site-key';
		$settings['turnstile_secret_key'] = 'secret-key';
		$settings['reoon_api_key']        = 'reoon-key';
		$settings['protection_mode']      = 'turnstile_and_reoon';

		$settings_page = new ALYNT_AG_Settings_Page();

		ob_start();
		$this->invoke_helper( $settings_page, 'render_security_status_panel', array( $settings ) );
		$output = ob_get_clean();

		$this->assertStringNotContainsString( 'No anti-spam provider is fully configured.', $output );
		$this->assertStringContainsString( 'Every configured provider must pass registration.', $output );
		$this->assertStringContainsString( 'Server-side verification can run', $output );
		$this->assertStringContainsString( 'Email quality verification can run', $output );
		$this->assertStringContainsString( 'Blocks invalid, disabled, disposable, and spamtrap statuses.', $output );
		$this->assertStringContainsString( 'Allows but flags catch-all, role account, unknown, and inbox-full statuses.', $output );
	}

	public function test_security_rate_limit_items_use_configured_values() {
		$settings                                            = ALYNT_AG_Settings_Schema::defaults();
		$settings['registration_rate_limit_count']            = 3;
		$settings['registration_rate_limit_window']           = 15;
		$settings['resend_confirmation_rate_limit_count']     = 2;
		$settings['resend_confirmation_rate_limit_window']    = 30;
		$settings['login_rate_limit_count']                   = 7;
		$settings['login_rate_limit_window']                  = 20;
		$settings['lostpassword_rate_limit_count']            = 4;
		$settings['lostpassword_rate_limit_window']           = 45;

		$settings_page = new ALYNT_AG_Settings_Page();
		$items         = $this->invoke_helper( $settings_page, 'security_rate_limit_items', array( $settings ) );

		$this->assertSame( 'Registration Attempts', $items[0]['label'] );
		$this->assertSame( 'Limit: 3 attempts in a 15-minute window.', $items[0]['message'] );
		$this->assertSame( 'Limit: 2 attempts in a 30-minute window.', $items[1]['message'] );
		$this->assertSame( 'Limit: 7 attempts in a 20-minute window.', $items[2]['message'] );
		$this->assertSame( 'Password Reset Attempts', $items[3]['label'] );
		$this->assertSame( 'Limit: 4 attempts in a 45-minute window.', $items[3]['message'] );
	}
}
