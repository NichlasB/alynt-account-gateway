<?php
/**
 * Focused structural test suite.
 *
 * @package Alynt_Account_Gateway
 */

require_once __DIR__ . '/support/class-settings-page-security-status-test-case.php';

/**
 * Tests recent security verification activity rendering.
 */
class SettingsPageVerificationActivityRenderingTest extends SettingsPageSecurityStatusTestCase {

	public function test_security_recent_verification_activity_renders_masked_rows() {
		$tables = ALYNT_AG_Database::tables();
		$GLOBALS['alynt_ag_test_db_results'][ $tables['verification_logs'] ] = array(
			(object) array(
				'email'      => 'damon@example.test',
				'provider'   => 'reoon',
				'status'     => 'safe',
				'blocked'    => 0,
				'created_at' => '2026-07-05 12:00:00',
			),
			(object) array(
				'email'      => 'spam@example.test',
				'provider'   => 'rate_limit',
				'status'     => 'registration_rate_limited',
				'blocked'    => 1,
				'created_at' => '2026-07-05 12:05:00',
			),
			(object) array(
				'email'      => 'resend-limit@example.test',
				'provider'   => 'rate_limit',
				'status'     => 'resend_confirmation_rate_limited',
				'blocked'    => 1,
				'created_at' => '2026-07-05 12:06:00',
			),
			(object) array(
				'email'      => 'review@example.test',
				'provider'   => 'reoon',
				'status'     => 'role_account_flagged',
				'blocked'    => 0,
				'created_at' => '2026-07-05 12:10:00',
			),
			(object) array(
				'email'      => 'strict@example.test',
				'provider'   => 'reoon',
				'status'     => 'role_account_flagged_blocked',
				'blocked'    => 1,
				'created_at' => '2026-07-05 12:12:00',
			),
			(object) array(
				'email'      => 'blocked@example.test',
				'provider'   => 'reoon',
				'status'     => 'alynt_ag_reoon_blocked',
				'blocked'    => 1,
				'created_at' => '2026-07-05 12:15:00',
			),
			(object) array(
				'email'      => 'challenge@example.test',
				'provider'   => 'turnstile',
				'status'     => 'alynt_ag_turnstile_failed',
				'blocked'    => 1,
				'created_at' => '2026-07-05 12:20:00',
			),
			(object) array(
				'email'      => 'reoon-missing@example.test',
				'provider'   => 'reoon',
				'status'     => 'alynt_ag_reoon_missing',
				'blocked'    => 1,
				'created_at' => '2026-07-05 12:21:00',
			),
			(object) array(
				'email'      => 'reoon-down@example.test',
				'provider'   => 'reoon',
				'status'     => 'alynt_ag_reoon_request_failed',
				'blocked'    => 1,
				'created_at' => '2026-07-05 12:22:00',
			),
			(object) array(
				'email'      => 'reoon-invalid@example.test',
				'provider'   => 'reoon',
				'status'     => 'alynt_ag_reoon_invalid_response',
				'blocked'    => 1,
				'created_at' => '2026-07-05 12:23:00',
			),
			(object) array(
				'email'      => 'turnstile-missing@example.test',
				'provider'   => 'turnstile',
				'status'     => 'alynt_ag_turnstile_missing',
				'blocked'    => 1,
				'created_at' => '2026-07-05 12:24:00',
			),
			(object) array(
				'email'      => 'turnstile-down@example.test',
				'provider'   => 'turnstile',
				'status'     => 'alynt_ag_turnstile_request_failed',
				'blocked'    => 1,
				'created_at' => '2026-07-05 12:24:30',
			),
			(object) array(
				'email'      => 'login@example.test',
				'provider'   => 'rate_limit',
				'status'     => 'login_rate_limited',
				'blocked'    => 1,
				'created_at' => '2026-07-05 12:25:00',
			),
			(object) array(
				'email'      => 'reset@example.test',
				'provider'   => 'rate_limit',
				'status'     => 'lostpassword_rate_limited',
				'blocked'    => 1,
				'created_at' => '2026-07-05 12:30:00',
			),
			(object) array(
				'email'      => 'terms@example.test',
				'provider'   => 'registration_flow',
				'status'     => 'terms_required',
				'blocked'    => 1,
				'created_at' => '2026-07-05 12:35:00',
			),
			(object) array(
				'email'      => 'resent@example.test',
				'provider'   => 'registration_flow',
				'status'     => 'confirmation_resent',
				'blocked'    => 0,
				'created_at' => '2026-07-05 12:40:00',
			),
			(object) array(
				'email'      => 'stored@example.test',
				'provider'   => 'registration_flow',
				'status'     => 'pending_registration_failed',
				'blocked'    => 1,
				'created_at' => '2026-07-05 12:45:00',
			),
			(object) array(
				'email'      => 'password@example.test',
				'provider'   => 'registration_flow',
				'status'     => 'password_mismatch',
				'blocked'    => 1,
				'created_at' => '2026-07-05 12:50:00',
			),
		);
		$GLOBALS['alynt_ag_test_db_results'][ $tables['diagnostics_logs'] ] = array(
			(object) array(
				'event_code' => 'wp_admin_access_blocked',
				'context'    => wp_json_encode(
					array(
						'path' => '/wp-admin/',
					)
				),
				'created_at' => '2026-07-05 12:55:00',
			),
			(object) array(
				'event_code' => 'native_login_redirected',
				'context'    => wp_json_encode(
					array(
						'preserved_query_keys' => array( 'key', 'login', 'redirect_to' ),
					)
				),
				'created_at' => '2026-07-05 12:56:00',
			),
			(object) array(
				'event_code' => 'branded_login_failed',
				'context'    => wp_json_encode(
					array(
						'reason' => 'invalid_request',
					)
				),
				'created_at' => '2026-07-05 12:56:10',
			),
			(object) array(
				'event_code' => 'branded_login_succeeded',
				'context'    => wp_json_encode(
					array(
						'destination_path' => '/my-account/',
					)
				),
				'created_at' => '2026-07-05 12:56:20',
			),
			(object) array(
				'event_code' => 'branded_password_reset_requested',
				'context'    => wp_json_encode(
					array(
						'delivery_attempted' => true,
					)
				),
				'created_at' => '2026-07-05 12:56:30',
			),
			(object) array(
				'event_code' => 'branded_password_reset_failed',
				'context'    => wp_json_encode(
					array(
						'reason' => 'invalid_or_expired_token',
					)
				),
				'created_at' => '2026-07-05 12:56:40',
			),
			(object) array(
				'event_code' => 'branded_password_reset_completed',
				'context'    => wp_json_encode(
					array(
						'user_id' => 42,
					)
				),
				'created_at' => '2026-07-05 12:56:50',
			),
			(object) array(
				'event_code' => 'account_created_welcome_failed',
				'context'    => wp_json_encode(
					array(
						'user_id' => 42,
						'error'   => 'welcome_email_failed',
					)
				),
				'created_at' => '2026-07-05 12:57:00',
			),
			(object) array(
				'event_code' => 'account_created_webhook_failed',
				'context'    => wp_json_encode(
					array(
						'user_id' => 42,
						'error'   => 'alynt_ag_webhook_http_error',
					)
				),
				'created_at' => '2026-07-05 12:58:00',
			),
		);
		$GLOBALS['alynt_ag_test_db_results'][ $tables['webhook_logs'] ] = array(
			(object) array(
				'id'               => 1,
				'event_name'       => 'account.created',
				'destination_host' => 'hooks.example.test',
				'http_status'      => 500,
				'success'          => 0,
				'error_message'    => 'Server error',
				'created_at'       => '2026-07-05 12:59:00',
			),
			(object) array(
				'id'               => 2,
				'event_name'       => 'account.created.test',
				'destination_host' => 'hooks.example.test',
				'http_status'      => 200,
				'success'          => 1,
				'error_message'    => '',
				'created_at'       => '2026-07-05 13:00:00',
			),
		);

		$settings_page = new ALYNT_AG_Settings_Page();

		ob_start();
		$this->invoke_helper( $settings_page, 'render_security_verification_activity' );
		$output = ob_get_clean();

		$expectations = require __DIR__ . '/fixtures/settings-page-verification-activity-expectations.php';
		foreach ( $expectations['contains'] as $expected ) {
			$this->assertStringContainsString( $expected, $output );
		}
		foreach ( $expectations['excludes'] as $unexpected ) {
			$this->assertStringNotContainsString( $unexpected, $output );
		}

	}
}
