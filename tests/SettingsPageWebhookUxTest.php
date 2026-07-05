<?php
/**
 * Settings page webhook UX tests.
 *
 * @package Alynt_Account_Gateway
 */

use PHPUnit\Framework\TestCase;

require_once ALYNT_AG_PLUGIN_DIR . 'admin/class-settings-page.php';

/**
 * Tests webhook delivery summary helpers on the settings page.
 */
class SettingsPageWebhookUxTest extends TestCase {

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

	public function test_webhook_summary_shows_latest_delivery_and_signing_guidance() {
		$settings_page = new ALYNT_AG_Settings_Page();
		$log           = (object) array(
			'event_name'       => 'account.created',
			'destination_host' => 'hooks.example.test',
			'http_status'      => 204,
			'success'          => 1,
			'error_message'    => '',
			'created_at'       => '2026-07-03 12:00:00 +0000',
		);

		ob_start();
		$this->invoke_helper(
			$settings_page,
			'render_webhook_delivery_summary',
			array(
				array( $log ),
				array( 'webhook_signing_secret' => 'shared-secret' ),
			)
		);
		$output = ob_get_clean();

		$this->assertStringContainsString( 'Delivery Status:', $output );
		$this->assertStringContainsString( 'Success for account.created to hooks.example.test with HTTP 204 at 2026-07-03 12:00.', $output );
		$this->assertStringContainsString( 'Signing:', $output );
		$this->assertStringContainsString( 'Enabled. Outgoing webhooks include timestamped HMAC verification headers.', $output );
		$this->assertStringContainsString( 'Signature Verification Reference', $output );
		$this->assertStringContainsString( 'sha256=HMAC_SHA256({X-Alynt-AG-Time}.{X-Alynt-AG-Event}.{raw_json_body}, signing_secret)', $output );
	}

	public function test_webhook_summary_handles_empty_logs_and_unsigned_state() {
		$settings_page = new ALYNT_AG_Settings_Page();

		ob_start();
		$this->invoke_helper(
			$settings_page,
			'render_webhook_delivery_summary',
			array(
				array(),
				array( 'webhook_signing_secret' => '' ),
			)
		);
		$output = ob_get_clean();

		$this->assertStringContainsString( 'No deliveries have been logged yet.', $output );
		$this->assertStringContainsString( 'Disabled. Add a webhook signing secret to send verification headers.', $output );
	}

	public function test_webhook_log_details_renders_delivery_metadata() {
		$settings_page = new ALYNT_AG_Settings_Page();
		$log           = (object) array(
			'event_name'       => 'account.created.test',
			'destination_host' => 'hooks.example.test',
			'http_status'      => 500,
			'success'          => 0,
			'error_message'    => 'Server Error',
			'created_at'       => '2026-07-03 12:00:00 +0000',
		);

		ob_start();
		$this->invoke_helper( $settings_page, 'render_webhook_log_details', array( $log ) );
		$output = ob_get_clean();

		$this->assertStringContainsString( '<summary>View</summary>', $output );
		$this->assertStringContainsString( 'account.created.test', $output );
		$this->assertStringContainsString( 'hooks.example.test', $output );
		$this->assertStringContainsString( 'HTTP Status:', $output );
		$this->assertStringContainsString( '500', $output );
		$this->assertStringContainsString( 'Failed', $output );
		$this->assertStringContainsString( 'Server Error', $output );
	}

	public function test_webhook_time_formatter_returns_original_invalid_timestamp() {
		$settings_page = new ALYNT_AG_Settings_Page();

		$this->assertSame(
			'not-a-date',
			$this->invoke_helper( $settings_page, 'format_webhook_log_time', array( 'not-a-date' ) )
		);
	}
}
