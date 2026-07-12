<?php
/**
 * Settings page portability tools tests.
 *
 * @package Alynt_Account_Gateway
 */

use PHPUnit\Framework\TestCase;

require_once ALYNT_AG_PLUGIN_DIR . 'admin/class-settings-page.php';

if ( ! function_exists( 'submit_button' ) ) {
	function submit_button( $text = null, $type = 'primary', $name = 'submit', $wrap = true, $other_attributes = null ) {
		unset( $type, $wrap, $other_attributes );

		echo '<input type="submit" name="' . esc_attr( $name ) . '" value="' . esc_attr( (string) $text ) . '">';
	}
}

/**
 * Tests settings import/export guidance on the settings page.
 */
class SettingsPageSettingsToolsTest extends TestCase {

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

	public function test_settings_tools_render_portability_guidance() {
		$settings_page = new ALYNT_AG_Settings_Page();

		ob_start();
		$this->invoke_helper( $settings_page, 'render_settings_tools' );
		$output = ob_get_clean();

		$this->assertStringContainsString( 'Configuration portability notes', $output );
		$this->assertStringContainsString( 'Media-library files, pending registrations, diagnostics, webhook delivery logs, and WordPress users are not included.', $output );
		$this->assertStringContainsString( 'Imports validate JSON before saving', $output );
		$this->assertStringContainsString( 'Use the restore button at the bottom of each tab', $output );
		$this->assertStringContainsString( 'Export Settings JSON', $output );
		$this->assertStringContainsString( 'Import Settings', $output );
	}

	public function test_diagnostics_operational_snapshot_groups_account_gateway_events() {
		$settings_page = new ALYNT_AG_Settings_Page();

		$diagnostics = array(
			(object) array(
				'level'      => 'warning',
				'category'   => 'security',
				'event_code' => 'native_login_redirected',
				'message'    => 'Redirected native login.',
				'created_at' => '2026-07-12 10:00:00',
			),
			(object) array(
				'level'      => 'warning',
				'category'   => 'security',
				'event_code' => 'wp_admin_access_blocked',
				'message'    => 'Blocked admin access.',
				'created_at' => '2026-07-12 10:01:00',
			),
			(object) array(
				'level'      => 'info',
				'category'   => 'security',
				'event_code' => 'branded_login_succeeded',
				'message'    => 'Completed login.',
				'created_at' => '2026-07-12 10:02:00',
			),
			(object) array(
				'level'      => 'error',
				'category'   => 'security',
				'event_code' => 'branded_password_reset_email_failed',
				'message'    => 'Reset email failed.',
				'created_at' => '2026-07-12 10:03:00',
			),
			(object) array(
				'level'      => 'warning',
				'category'   => 'external_api',
				'event_code' => 'account_created_welcome_failed',
				'message'    => 'Welcome email failed.',
				'created_at' => '2026-07-12 10:04:00',
			),
			(object) array(
				'level'      => 'warning',
				'category'   => 'external_api',
				'event_code' => 'account_created_webhook_failed',
				'message'    => 'Webhook failed.',
				'created_at' => '2026-07-12 10:05:00',
			),
		);
		$verification_logs = array(
			(object) array(
				'provider' => 'turnstile',
				'status'   => 'alynt_ag_turnstile_request_failed',
				'blocked'  => 1,
			),
			(object) array(
				'provider' => 'registration_flow',
				'status'   => 'confirmation_email_failed',
				'blocked'  => 1,
			),
		);
		$webhook_logs      = array(
			(object) array(
				'success' => 0,
			),
		);

		ob_start();
		$this->invoke_helper( $settings_page, 'render_diagnostics_operational_snapshot', array( $diagnostics, $verification_logs, $webhook_logs ) );
		$output = ob_get_clean();

		$this->assertStringContainsString( 'Operational Snapshot', $output );
		$this->assertStringContainsString( 'Summarizes recent diagnostics, verification, and delivery evidence', $output );
		$this->assertStringContainsString( 'Redirects and Admin Blocks', $output );
		$this->assertStringContainsString( 'recent native login redirects or blocked wp-admin visits', $output );
		$this->assertStringContainsString( 'Branded Auth Outcomes', $output );
		$this->assertStringContainsString( 'Provider Verification Failures', $output );
		$this->assertStringContainsString( 'Registration Flow Failures', $output );
		$this->assertStringContainsString( 'Account Email Failures', $output );
		$this->assertStringContainsString( 'Webhook Delivery Failures', $output );
	}
}
