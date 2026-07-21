<?php
/**
 * Focused structural test suite.
 *
 * @package Alynt_Account_Gateway
 */

require_once __DIR__ . '/support/class-settings-page-security-status-test-case.php';

/**
 * Tests provider and registration security signals.
 */
class SettingsPageRegistrationSecuritySignalsTest extends SettingsPageSecurityStatusTestCase {

	public function test_shared_security_signal_cards_escape_content_and_render_optional_metadata() {
		$settings_page = new ALYNT_AG_Settings_Page();

		ob_start();
		$this->invoke_helper(
			$settings_page,
			'render_security_signal_cards',
			array(
				array(
					array(
						'label'   => '<b>Provider</b>',
						'status'  => 'action" onclick="alert(1)',
						'count'   => 2,
						'message' => '<script>alert(1)</script>',
						'latest'  => '<time>today</time>',
					),
				),
			)
		);
		$output = ob_get_clean();

		$this->assertStringContainsString( 'alynt-ag-security-card--action&quot; onclick=&quot;alert(1)', $output );
		$this->assertStringContainsString( '&lt;b&gt;Provider&lt;/b&gt;', $output );
		$this->assertStringContainsString( '&lt;script&gt;alert(1)&lt;/script&gt;', $output );
		$this->assertStringContainsString( 'Latest seen: &lt;time&gt;today&lt;/time&gt;.', $output );
		$this->assertStringNotContainsString( '<script>', $output );
	}

	public function test_security_provider_failure_triage_items_count_specific_failures() {
		$GLOBALS['alynt_ag_test_options']['date_format'] = 'Y-m-d';
		$GLOBALS['alynt_ag_test_options']['time_format'] = 'H:i:s';

		$settings_page = new ALYNT_AG_Settings_Page();
		$items         = $this->invoke_helper(
			$settings_page,
			'security_provider_failure_triage_items',
			array(
				array(
					(object) array(
						'provider' => 'turnstile',
						'status'   => 'alynt_ag_turnstile_missing',
						'created_at' => '2026-07-05 12:00:00',
					),
					(object) array(
						'provider' => 'turnstile',
						'status'   => 'alynt_ag_turnstile_missing',
						'created_at' => '2026-07-05 12:03:00',
					),
					(object) array(
						'provider' => 'turnstile',
						'status'   => 'alynt_ag_turnstile_request_failed',
						'created_at' => '2026-07-05 12:05:00',
					),
					(object) array(
						'provider' => 'turnstile',
						'status'   => 'alynt_ag_turnstile_failed',
						'created_at' => '2026-07-05 12:06:00',
					),
					(object) array(
						'provider' => 'reoon',
						'status'   => 'alynt_ag_reoon_missing',
						'created_at' => '2026-07-05 12:10:00',
					),
					(object) array(
						'provider' => 'reoon',
						'status'   => 'alynt_ag_reoon_request_failed',
						'created_at' => '2026-07-05 12:20:00',
					),
					(object) array(
						'provider' => 'reoon',
						'status'   => 'alynt_ag_reoon_invalid_response',
						'created_at' => '2026-07-05 12:30:00',
					),
					(object) array(
						'provider' => 'reoon',
						'status'   => 'alynt_ag_reoon_blocked',
						'created_at' => '2026-07-05 12:40:00',
					),
				),
			)
		);

		$this->assertSame( 'Turnstile Configuration', $items[0]['label'] );
		$this->assertSame( 2, $items[0]['count'] );
		$this->assertSame( 'action', $items[0]['status'] );
		$this->assertSame( '2026-07-05 12:03:00', $items[0]['latest'] );
		$this->assertSame( 'Turnstile Connectivity', $items[1]['label'] );
		$this->assertSame( 1, $items[1]['count'] );
		$this->assertSame( 'action', $items[1]['status'] );
		$this->assertSame( '2026-07-05 12:05:00', $items[1]['latest'] );
		$this->assertSame( 'Turnstile Challenge Rejections', $items[2]['label'] );
		$this->assertSame( 1, $items[2]['count'] );
		$this->assertSame( 'warning', $items[2]['status'] );
		$this->assertSame( '2026-07-05 12:06:00', $items[2]['latest'] );
		$this->assertSame( 'Reoon Configuration', $items[3]['label'] );
		$this->assertSame( 1, $items[3]['count'] );
		$this->assertSame( 'action', $items[3]['status'] );
		$this->assertSame( '2026-07-05 12:10:00', $items[3]['latest'] );
		$this->assertSame( 'Reoon Connectivity', $items[4]['label'] );
		$this->assertSame( 1, $items[4]['count'] );
		$this->assertSame( 'action', $items[4]['status'] );
		$this->assertSame( '2026-07-05 12:20:00', $items[4]['latest'] );
		$this->assertSame( 'Reoon Unexpected Responses', $items[5]['label'] );
		$this->assertSame( 1, $items[5]['count'] );
		$this->assertSame( 'action', $items[5]['status'] );
		$this->assertSame( '2026-07-05 12:30:00', $items[5]['latest'] );
	}

	public function test_security_provider_failure_triage_renders_latest_seen_metadata() {
		$GLOBALS['alynt_ag_test_options']['date_format'] = 'Y-m-d';
		$GLOBALS['alynt_ag_test_options']['time_format'] = 'H:i:s';

		$settings_page = new ALYNT_AG_Settings_Page();

		ob_start();
		$this->invoke_helper(
			$settings_page,
			'render_security_provider_failure_triage',
			array(
				array(
					(object) array(
						'provider'   => 'turnstile',
						'status'     => 'alynt_ag_turnstile_missing',
						'created_at' => '2026-07-05 12:00:00',
					),
					(object) array(
						'provider'   => 'turnstile',
						'status'     => 'alynt_ag_turnstile_missing',
						'created_at' => '2026-07-05 12:03:00',
					),
					(object) array(
						'provider'   => 'reoon',
						'status'     => 'alynt_ag_reoon_request_failed',
						'created_at' => '2026-07-05 12:20:00',
					),
				),
			)
		);
		$output = ob_get_clean();

		$this->assertStringContainsString( 'Provider Failure Triage', $output );
		$this->assertStringContainsString( 'Turnstile Configuration', $output );
		$this->assertStringContainsString( 'Reoon Connectivity', $output );
		$this->assertStringContainsString( 'alynt-ag-security-card__meta', $output );
		$this->assertStringContainsString( 'Latest seen: 2026-07-05 12:03:00.', $output );
		$this->assertStringContainsString( 'Latest seen: 2026-07-05 12:20:00.', $output );
	}

	public function test_security_registration_abuse_signals_count_recent_activity() {
		$settings_page = new ALYNT_AG_Settings_Page();
		$items         = $this->invoke_helper(
			$settings_page,
			'security_registration_abuse_signal_items',
			array(
				array(
					(object) array(
						'provider' => 'rate_limit',
						'status'   => 'registration_rate_limited',
					),
					(object) array(
						'provider' => 'rate_limit',
						'status'   => 'registration_rate_limited',
					),
					(object) array(
						'provider' => 'rate_limit',
						'status'   => 'resend_confirmation_rate_limited',
					),
					(object) array(
						'provider' => 'reoon',
						'status'   => 'role_account_flagged_blocked',
					),
					(object) array(
						'provider' => 'reoon',
						'status'   => 'alynt_ag_reoon_blocked',
					),
					(object) array(
						'provider' => 'registration_flow',
						'status'   => 'password_mismatch',
					),
					(object) array(
						'provider' => 'registration_flow',
						'status'   => 'email_unavailable',
					),
					(object) array(
						'provider' => 'turnstile',
						'status'   => 'alynt_ag_turnstile_failed',
					),
				),
			)
		);

		$this->assertSame( 'Registration Rate Limits', $items[0]['label'] );
		$this->assertSame( 2, $items[0]['count'] );
		$this->assertSame( 'warning', $items[0]['status'] );
		$this->assertSame( 'Resend Rate Limits', $items[1]['label'] );
		$this->assertSame( 1, $items[1]['count'] );
		$this->assertSame( 'warning', $items[1]['status'] );
		$this->assertSame( 'Flagged Email Blocks', $items[2]['label'] );
		$this->assertSame( 2, $items[2]['count'] );
		$this->assertSame( 'warning', $items[2]['status'] );
		$this->assertSame( 'Setup Friction Blocks', $items[3]['label'] );
		$this->assertSame( 2, $items[3]['count'] );
		$this->assertSame( 'warning', $items[3]['status'] );
	}

	public function test_security_registration_flow_signals_count_recent_activity() {
		$settings_page = new ALYNT_AG_Settings_Page();
		$items         = $this->invoke_helper(
			$settings_page,
			'security_registration_flow_signal_items',
			array(
				array(
					(object) array(
						'provider' => 'registration_flow',
						'status'   => 'terms_required',
					),
					(object) array(
						'provider' => 'registration_flow',
						'status'   => 'consent_record_failed',
					),
					(object) array(
						'provider' => 'registration_flow',
						'status'   => 'pending_registration_failed',
					),
					(object) array(
						'provider' => 'registration_flow',
						'status'   => 'confirmation_email_failed',
					),
					(object) array(
						'provider' => 'registration_flow',
						'status'   => 'password_mismatch',
					),
					(object) array(
						'provider' => 'registration_flow',
						'status'   => 'alynt_ag_password_complexity',
					),
					(object) array(
						'provider' => 'registration_flow',
						'status'   => 'confirmation_resent',
					),
					(object) array(
						'provider' => 'rate_limit',
						'status'   => 'registration_rate_limited',
					),
				),
			)
		);

		$this->assertSame( 'Consent Blocks', $items[0]['label'] );
		$this->assertSame( 2, $items[0]['count'] );
		$this->assertSame( 'warning', $items[0]['status'] );
		$this->assertSame( 'Registration System Failures', $items[1]['label'] );
		$this->assertSame( 2, $items[1]['count'] );
		$this->assertSame( 'action', $items[1]['status'] );
		$this->assertSame( 'Password Setup Blocks', $items[2]['label'] );
		$this->assertSame( 2, $items[2]['count'] );
		$this->assertSame( 'warning', $items[2]['status'] );
		$this->assertSame( 'Confirmation Resends Sent', $items[3]['label'] );
		$this->assertSame( 1, $items[3]['count'] );
		$this->assertSame( 'warning', $items[3]['status'] );
	}
}
