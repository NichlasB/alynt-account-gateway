<?php
/**
 * Settings page security status tests.
 *
 * @package Alynt_Account_Gateway
 */

require_once __DIR__ . '/support/class-settings-page-security-status-test-case.php';

/**
 * Tests a focused settings-page security status concern.
 */
class SettingsPagePendingRegistrationStatusTest extends SettingsPageSecurityStatusTestCase {

	public function test_security_manual_review_decision_items_describe_review_policy() {
		$settings_page = new ALYNT_AG_Settings_Page();
		$items         = $this->invoke_helper( $settings_page, 'security_manual_review_decision_items' );

		$this->assertSame( 'Role account', $items[0]['result_family'] );
		$this->assertSame( 'Allow and review when shared inboxes are acceptable for the site.', $items[0]['default_decision'] );
		$this->assertSame( 'Block when personal accountability, subscriptions, or fraud exposure matter more than shared access.', $items[0]['tighten_when'] );
		$this->assertSame( 'Check whether customers commonly use support, info, billing, or team inboxes.', $items[0]['review_first'] );
		$this->assertSame( 'Catch-all domain', $items[1]['result_family'] );
		$this->assertSame( 'Unknown or inbox full', $items[2]['result_family'] );
		$this->assertSame( 'Disposable, spamtrap, invalid, or disabled', $items[3]['result_family'] );
		$this->assertSame( 'Keep blocked; these are always treated as high-risk or unusable.', $items[3]['default_decision'] );
	}

	public function test_security_activity_omits_diagnostics_dependency_notice_when_enabled() {
		$GLOBALS['alynt_ag_test_options']['alynt_ag_settings'] = array(
			'diagnostics_enabled' => true,
		);

		$settings_page = new ALYNT_AG_Settings_Page();

		ob_start();
		$this->invoke_helper( $settings_page, 'render_security_verification_activity' );
		$output = ob_get_clean();

		$this->assertStringContainsString( 'Access Control Signals', $output );
		$this->assertStringNotContainsString( 'Diagnostics are disabled.', $output );
		$this->assertStringNotContainsString( 'only show complete evidence while diagnostics are enabled', $output );
	}

	public function test_security_pending_registrations_render_empty_state() {
		$settings_page = new ALYNT_AG_Settings_Page();

		ob_start();
		$this->invoke_helper( $settings_page, 'render_security_pending_registrations' );
		$output = ob_get_clean();

		$this->assertStringContainsString( 'Recent Pending Registrations', $output );
		$this->assertStringContainsString( 'No pending registration records have been created yet.', $output );
	}

	public function test_security_pending_registration_lifecycle_signals_count_recent_activity() {
		$settings_page = new ALYNT_AG_Settings_Page();
		$items         = $this->invoke_helper(
			$settings_page,
			'security_pending_registration_lifecycle_signal_items',
			array(
				array(
					(object) array(
						'status'       => 'pending',
						'expires_at'   => '2099-07-04 12:00:00',
						'confirmed_at' => null,
						'user_id'      => 0,
					),
					(object) array(
						'status'       => 'email_confirmed',
						'expires_at'   => '2099-07-04 12:00:00',
						'confirmed_at' => '2026-07-04 12:05:00',
						'user_id'      => 0,
					),
					(object) array(
						'status'       => 'pending',
						'expires_at'   => '2000-07-04 12:00:00',
						'confirmed_at' => null,
						'user_id'      => 0,
					),
					(object) array(
						'status'       => 'account_created',
						'expires_at'   => '2000-07-04 12:00:00',
						'confirmed_at' => '2026-07-04 12:05:00',
						'user_id'      => 123,
					),
				),
			)
		);

		$this->assertSame( 'Waiting For Confirmation', $items[0]['label'] );
		$this->assertSame( 1, $items[0]['count'] );
		$this->assertSame( 'warning', $items[0]['status'] );
		$this->assertSame( 'Confirmed, Not Completed', $items[1]['label'] );
		$this->assertSame( 1, $items[1]['count'] );
		$this->assertSame( 'warning', $items[1]['status'] );
		$this->assertSame( 'Expired Pending Records', $items[2]['label'] );
		$this->assertSame( 1, $items[2]['count'] );
		$this->assertSame( 'action', $items[2]['status'] );
		$this->assertSame( 'Completed Pending Records', $items[3]['label'] );
		$this->assertSame( 1, $items[3]['count'] );
		$this->assertSame( 'ready', $items[3]['status'] );
	}

	public function test_security_pending_registrations_render_masked_rows_and_statuses() {
		$tables = ALYNT_AG_Database::tables();
		$GLOBALS['alynt_ag_test_db_results'][ $tables['pending_registrations'] ] = array(
			(object) array(
				'email'        => 'pending@example.test',
				'user_id'      => 0,
				'status'       => 'pending',
				'created_at'   => '2026-07-03 12:00:00',
				'confirmed_at' => null,
				'expires_at'   => '2099-07-04 12:00:00',
			),
			(object) array(
				'email'        => 'confirmed@example.test',
				'user_id'      => 0,
				'status'       => 'email_confirmed',
				'created_at'   => '2026-07-02 12:00:00',
				'confirmed_at' => '2026-07-02 12:10:00',
				'expires_at'   => '2099-07-04 12:00:00',
			),
			(object) array(
				'email'        => 'finished@example.test',
				'user_id'      => 123,
				'status'       => 'account_created',
				'created_at'   => '2026-07-01 12:00:00',
				'confirmed_at' => '2026-07-01 12:10:00',
				'expires_at'   => '2026-07-02 12:00:00',
			),
			(object) array(
				'email'        => 'expired@example.test',
				'user_id'      => 0,
				'status'       => 'pending',
				'created_at'   => '2026-07-01 10:00:00',
				'confirmed_at' => null,
				'expires_at'   => '2026-07-02 10:00:00',
			),
		);

		$settings_page = new ALYNT_AG_Settings_Page();

		ob_start();
		$this->invoke_helper( $settings_page, 'render_security_pending_registrations' );
		$output = ob_get_clean();

		$this->assertStringContainsString( 'Recent Pending Registrations', $output );
		$this->assertStringContainsString( 'Pending Registration Lifecycle Signals', $output );
		$this->assertStringContainsString( 'recent pending records still waiting for email confirmation. Watch resend activity and inbox-delivery support requests.', $output );
		$this->assertStringContainsString( 'recent records where email is confirmed but password setup is unfinished. Customers may need clearer next-step copy.', $output );
		$this->assertStringContainsString( 'recent pending records past their confirmation window. High counts can indicate missed emails or confusing confirmation instructions.', $output );
		$this->assertStringContainsString( 'recent pending records that reached account creation. This helps compare completed registrations against stalled ones.', $output );
		$this->assertStringContainsString( 'p***@example.test', $output );
		$this->assertStringContainsString( 'c***@example.test', $output );
		$this->assertStringContainsString( 'f***@example.test', $output );
		$this->assertStringContainsString( 'e***@example.test', $output );
		$this->assertStringContainsString( 'Pending', $output );
		$this->assertStringContainsString( 'Email Confirmed', $output );
		$this->assertStringContainsString( 'Completed', $output );
		$this->assertStringContainsString( 'Expired', $output );
		$this->assertStringContainsString( 'Next Step', $output );
		$this->assertStringContainsString( 'Waiting for email confirmation. Resend requests are throttled by the configured resend-confirmation limit.', $output );
		$this->assertStringContainsString( 'Email is confirmed. The customer still needs to set a password before the record expires.', $output );
		$this->assertStringContainsString( 'Account creation is complete. No resend action is needed.', $output );
		$this->assertStringContainsString( 'The confirmation window has expired. The customer can request a fresh confirmation email from the invalid-link screen.', $output );
		$this->assertStringContainsString( '>123<', $output );
		$this->assertStringNotContainsString( 'pending@example.test', $output );
	}

	public function test_pending_registration_expiry_uses_utc_database_time() {
		$GLOBALS['alynt_ag_test_current_time_utc']   = '2026-07-03 12:00:00';
		$GLOBALS['alynt_ag_test_current_time_local'] = '2026-07-03 14:00:00';

		$settings_page = new ALYNT_AG_Settings_Page();
		$status        = $this->invoke_helper(
			$settings_page,
			'security_pending_registration_status',
			array(
				(object) array(
					'status'     => 'pending',
					'expires_at' => '2026-07-03 13:00:00',
				),
			)
		);

		$this->assertSame( 'pending', $status['key'] );
	}

	public function test_legacy_completed_status_remains_supported() {
		$settings_page = new ALYNT_AG_Settings_Page();
		$status        = $this->invoke_helper(
			$settings_page,
			'security_pending_registration_status',
			array(
				(object) array(
					'status'     => 'completed',
					'expires_at' => '2000-01-01 00:00:00',
				),
			)
		);

		$this->assertSame( 'completed', $status['key'] );
	}
}
