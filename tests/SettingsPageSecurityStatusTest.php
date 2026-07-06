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

	protected function setUp(): void {
		parent::setUp();
		$GLOBALS['alynt_ag_test_db_results'] = array();
	}

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
		$this->assertStringContainsString( 'Reoon Blocked Statuses', $output );
		$this->assertStringContainsString( 'Reoon Flagged Statuses', $output );
		$this->assertStringContainsString( 'Reoon Flagged Status Guidance', $output );
		$this->assertStringContainsString( 'Rate Limit Posture', $output );
		$this->assertStringContainsString( 'Registration Attempts', $output );
		$this->assertStringContainsString( 'Password Reset Attempts', $output );
		$this->assertStringContainsString( 'Recent Registration Verification Activity', $output );
		$this->assertStringContainsString( 'No verification activity has been logged yet.', $output );
		$this->assertStringContainsString( 'Recent Pending Registrations', $output );
		$this->assertStringContainsString( 'No pending registration records have been created yet.', $output );
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
		$this->assertStringContainsString( 'Always blocks invalid, disabled, disposable, and spamtrap statuses.', $output );
		$this->assertStringContainsString( 'Allows but logs catch-all, role account, unknown, and inbox-full statuses for admin review.', $output );
		$this->assertStringContainsString( 'Current policy: Allow and log flagged statuses.', $output );
		$this->assertStringContainsString( 'For most stores, allow and log flagged statuses first.', $output );
		$this->assertStringContainsString( 'Catch-all domains, role accounts, unknown results, and full inboxes can include legitimate customers', $output );
		$this->assertStringContainsString( 'Use Recent Registration Verification Activity below to review allowed flagged results and blocked Reoon decisions', $output );
	}

	public function test_security_status_panel_describes_blocking_flagged_reoon_policy() {
		$settings                          = ALYNT_AG_Settings_Schema::defaults();
		$settings['reoon_api_key']         = 'reoon-key';
		$settings['reoon_flagged_policy']  = 'block';

		$settings_page = new ALYNT_AG_Settings_Page();

		ob_start();
		$this->invoke_helper( $settings_page, 'render_security_status_panel', array( $settings ) );
		$output = ob_get_clean();

		$this->assertStringContainsString( 'Reoon Flagged Statuses', $output );
		$this->assertStringContainsString( 'Blocks catch-all, role account, unknown, and inbox-full statuses before account creation.', $output );
		$this->assertStringContainsString( 'Current policy: Block flagged statuses.', $output );
		$this->assertStringContainsString( 'Switch to blocking when support volume, spam pressure, or fraud risk matters more', $output );
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

	public function test_security_rate_limit_pressure_counts_recent_activity() {
		$settings_page = new ALYNT_AG_Settings_Page();
		$items         = $this->invoke_helper(
			$settings_page,
			'security_rate_limit_pressure_items',
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
						'status'   => 'login_rate_limited',
					),
					(object) array(
						'provider' => 'reoon',
						'status'   => 'role_account_flagged',
					),
				),
			)
		);

		$this->assertSame( 'Registration', $items[0]['label'] );
		$this->assertSame( 2, $items[0]['count'] );
		$this->assertSame( 'warning', $items[0]['status'] );
		$this->assertSame( 'Confirmation Resends', $items[1]['label'] );
		$this->assertSame( 0, $items[1]['count'] );
		$this->assertSame( 'ready', $items[1]['status'] );
		$this->assertSame( 'Login', $items[2]['label'] );
		$this->assertSame( 1, $items[2]['count'] );
		$this->assertSame( 'Password Reset', $items[3]['label'] );
		$this->assertSame( 0, $items[3]['count'] );
	}

	public function test_security_provider_health_signals_count_recent_activity() {
		$settings_page = new ALYNT_AG_Settings_Page();
		$items         = $this->invoke_helper(
			$settings_page,
			'security_provider_health_signal_items',
			array(
				array(
					(object) array(
						'provider' => 'turnstile',
						'status'   => 'alynt_ag_turnstile_failed',
					),
					(object) array(
						'provider' => 'turnstile',
						'status'   => 'alynt_ag_turnstile_missing',
					),
					(object) array(
						'provider' => 'turnstile',
						'status'   => 'alynt_ag_turnstile_request_failed',
					),
					(object) array(
						'provider' => 'reoon',
						'status'   => 'alynt_ag_reoon_blocked',
					),
					(object) array(
						'provider' => 'reoon',
						'status'   => 'role_account_flagged_blocked',
					),
					(object) array(
						'provider' => 'reoon',
						'status'   => 'alynt_ag_reoon_request_failed',
					),
					(object) array(
						'provider' => 'rate_limit',
						'status'   => 'registration_rate_limited',
					),
				),
			)
		);

		$this->assertSame( 'Turnstile Challenges', $items[0]['label'] );
		$this->assertSame( 1, $items[0]['count'] );
		$this->assertSame( 'warning', $items[0]['status'] );
		$this->assertSame( 'Turnstile Connectivity', $items[1]['label'] );
		$this->assertSame( 2, $items[1]['count'] );
		$this->assertSame( 'action', $items[1]['status'] );
		$this->assertSame( 'Reoon Email Blocks', $items[2]['label'] );
		$this->assertSame( 2, $items[2]['count'] );
		$this->assertSame( 'warning', $items[2]['status'] );
		$this->assertSame( 'Reoon Provider Failures', $items[3]['label'] );
		$this->assertSame( 1, $items[3]['count'] );
		$this->assertSame( 'action', $items[3]['status'] );
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

	public function test_security_access_control_signals_count_recent_activity() {
		$settings_page = new ALYNT_AG_Settings_Page();
		$items         = $this->invoke_helper(
			$settings_page,
			'security_access_control_signal_items',
			array(
				array(
					(object) array(
						'provider' => 'rate_limit',
						'status'   => 'login_rate_limited',
					),
					(object) array(
						'provider' => 'rate_limit',
						'status'   => 'lostpassword_rate_limited',
					),
					(object) array(
						'provider' => 'rate_limit',
						'status'   => 'lostpassword_rate_limited',
					),
					(object) array(
						'provider' => 'registration_flow',
						'status'   => 'password_mismatch',
					),
				),
				array(
					(object) array(
						'event_code' => 'wp_admin_access_blocked',
					),
					(object) array(
						'event_code' => 'wp_admin_access_blocked',
					),
					(object) array(
						'event_code' => 'native_login_redirected',
					),
				),
			)
		);

		$this->assertSame( 'Login Lockouts', $items[0]['label'] );
		$this->assertSame( 1, $items[0]['count'] );
		$this->assertSame( 'warning', $items[0]['status'] );
		$this->assertSame( 'Password Reset Lockouts', $items[1]['label'] );
		$this->assertSame( 2, $items[1]['count'] );
		$this->assertSame( 'warning', $items[1]['status'] );
		$this->assertSame( 'Blocked Admin Access', $items[2]['label'] );
		$this->assertSame( 2, $items[2]['count'] );
		$this->assertSame( 'warning', $items[2]['status'] );
	}

	public function test_security_auth_redirect_signals_count_recent_activity() {
		$settings_page = new ALYNT_AG_Settings_Page();
		$items         = $this->invoke_helper(
			$settings_page,
			'security_auth_redirect_signal_items',
			array(
				array(
					(object) array(
						'event_code' => 'native_login_redirected',
						'context'    => wp_json_encode(
							array(
								'preserved_query_keys' => array( 'login', 'key' ),
							)
						),
					),
					(object) array(
						'event_code' => 'native_login_redirected',
						'context'    => wp_json_encode(
							array(
								'preserved_query_keys' => array( 'redirect_to' ),
							)
						),
					),
					(object) array(
						'event_code' => 'native_login_redirected',
						'context'    => wp_json_encode(
							array(
								'preserved_query_keys' => array( 'login' ),
							)
						),
					),
					(object) array(
						'event_code' => 'native_login_redirected',
						'context'    => '{invalid-json',
					),
					(object) array(
						'event_code' => 'wp_admin_access_blocked',
						'context'    => wp_json_encode(
							array(
								'preserved_query_keys' => array( 'redirect_to' ),
							)
						),
					),
				),
			)
		);

		$this->assertSame( 'Native Login Redirects', $items[0]['label'] );
		$this->assertSame( 4, $items[0]['count'] );
		$this->assertSame( 'warning', $items[0]['status'] );
		$this->assertSame( 'Reset Link Redirects', $items[1]['label'] );
		$this->assertSame( 1, $items[1]['count'] );
		$this->assertSame( 'warning', $items[1]['status'] );
		$this->assertSame( 'Redirect-To Preserved', $items[2]['label'] );
		$this->assertSame( 1, $items[2]['count'] );
		$this->assertSame( 'warning', $items[2]['status'] );
	}

	public function test_security_delivery_signals_count_recent_activity() {
		$settings_page = new ALYNT_AG_Settings_Page();
		$items         = $this->invoke_helper(
			$settings_page,
			'security_delivery_signal_items',
			array(
				array(
					(object) array(
						'event_code' => 'account_created_welcome_failed',
					),
					(object) array(
						'event_code' => 'account_created_webhook_failed',
					),
					(object) array(
						'event_code' => 'account_created_webhook_failed',
					),
					(object) array(
						'event_code' => 'native_login_redirected',
					),
				),
				array(
					(object) array(
						'event_name'  => 'account.created',
						'http_status' => 500,
						'success'     => 0,
					),
					(object) array(
						'event_name'  => 'account.created.test',
						'http_status' => 0,
						'success'     => '0',
					),
					(object) array(
						'event_name'  => 'account.created',
						'http_status' => 200,
						'success'     => 1,
					),
				),
			)
		);

		$this->assertSame( 'Welcome Email Failures', $items[0]['label'] );
		$this->assertSame( 1, $items[0]['count'] );
		$this->assertSame( 'action', $items[0]['status'] );
		$this->assertSame( 'Account Webhook Failures', $items[1]['label'] );
		$this->assertSame( 2, $items[1]['count'] );
		$this->assertSame( 'action', $items[1]['status'] );
		$this->assertSame( 'Failed Webhook Deliveries', $items[2]['label'] );
		$this->assertSame( 2, $items[2]['count'] );
		$this->assertSame( 'action', $items[2]['status'] );
	}

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

		$this->assertStringContainsString( 'Recent Registration Verification Activity', $output );
		$this->assertStringContainsString( 'Provider Health Signals', $output );
		$this->assertStringContainsString( 'recent challenge rejections. Confirm the site key matches the secret key and watch for bot traffic if this rises.', $output );
		$this->assertStringContainsString( 'recent configuration or network failures. Check both Turnstile keys and outbound HTTP connectivity.', $output );
		$this->assertStringContainsString( 'recent email-quality blocks. Review the policy if legitimate customers are affected.', $output );
		$this->assertStringContainsString( 'recent configuration, connectivity, or response failures. Test the API key and outbound HTTP connectivity.', $output );
		$this->assertStringContainsString( 'Rate Limit Pressure', $output );
		$this->assertStringContainsString( 'recent registration blocks. Review the limit if legitimate customers are affected.', $output );
		$this->assertStringContainsString( 'recent resend blocks. Repeated resends can indicate confused customers or automated retries.', $output );
		$this->assertStringContainsString( 'recent login blocks. Repeated login blocks can indicate credential stuffing or customers stuck at login.', $output );
		$this->assertStringContainsString( 'recent password-reset blocks. Check for repeated reset requests against the same account.', $output );
		$this->assertStringContainsString( 'Registration Abuse Signals', $output );
		$this->assertStringContainsString( 'recent registration attempts blocked before verification. Watch for bursts from the same campaign or customer support reports.', $output );
		$this->assertStringContainsString( 'recent confirmation resend attempts blocked by throttling. Repeated blocks can indicate inbox delivery delays or automated retries.', $output );
		$this->assertStringContainsString( 'recent Reoon policy blocks for low-quality or flagged addresses. Review if legitimate business domains appear in support tickets.', $output );
		$this->assertStringContainsString( 'recent password or email-availability blocks during account setup. Improve form guidance if legitimate customers abandon setup here.', $output );
		$this->assertStringContainsString( 'Access Control Signals', $output );
		$this->assertStringContainsString( 'recent login rate-limit blocks. Review for credential stuffing or customers stuck at login.', $output );
		$this->assertStringContainsString( 'recent password-reset rate-limit blocks. Watch for repeated reset requests against the same account.', $output );
		$this->assertStringContainsString( 'recent wp-admin redirects recorded by diagnostics. Repeated blocks can mean customers are following admin links or a role rule needs review.', $output );
		$this->assertStringContainsString( 'Gateway Routing Signals', $output );
		$this->assertStringContainsString( 'recent native wp-login.php redirects. If this rises, update menus, emails, and third-party links to use branded account routes.', $output );
		$this->assertStringContainsString( 'recent reset-link redirects preserved password setup keys. Confirm branded set-password handling stays healthy.', $output );
		$this->assertStringContainsString( 'recent login redirects preserved a destination. Review protected-page links if customers seem bounced through login often.', $output );
		$this->assertStringContainsString( 'Registration Flow Signals', $output );
		$this->assertStringContainsString( 'recent consent-related blocks. Check Terms and Privacy copy if legitimate customers are stopping here.', $output );
		$this->assertStringContainsString( 'recent pending-record or confirmation-email failures. Review database writes and email delivery before public launch.', $output );
		$this->assertStringContainsString( 'recent password or email-availability blocks. Review password guidance if customers struggle to complete setup.', $output );
		$this->assertStringContainsString( 'recent successful resends. Repeated resends can point to delivery delays or unclear confirmation instructions.', $output );
		$this->assertStringContainsString( 'Account Delivery Signals', $output );
		$this->assertStringContainsString( 'recent account-created welcome email failures. Check mail delivery before relying on account onboarding.', $output );
		$this->assertStringContainsString( 'recent account-created webhook dispatch failures. Review endpoint configuration and signing before relying on automation.', $output );
		$this->assertStringContainsString( 'recent failed webhook delivery rows. Open the Webhooks tab to review destinations, HTTP status, and error messages.', $output );
		$this->assertStringContainsString( 'd***@example.test', $output );
		$this->assertStringContainsString( 's***@example.test', $output );
		$this->assertStringContainsString( 'r***@example.test', $output );
		$this->assertStringContainsString( 'b***@example.test', $output );
		$this->assertStringContainsString( 'c***@example.test', $output );
		$this->assertStringContainsString( 'l***@example.test', $output );
		$this->assertStringContainsString( 'r***@example.test', $output );
		$this->assertStringContainsString( 't***@example.test', $output );
		$this->assertStringContainsString( 'Reoon Email Verifier', $output );
		$this->assertStringContainsString( 'Rate Limit', $output );
		$this->assertStringContainsString( 'Turnstile', $output );
		$this->assertStringContainsString( 'Registration Flow', $output );
		$this->assertStringContainsString( 'safe', $output );
		$this->assertStringContainsString( 'registration_rate_limited', $output );
		$this->assertStringContainsString( 'resend_confirmation_rate_limited', $output );
		$this->assertStringContainsString( 'role_account_flagged', $output );
		$this->assertStringContainsString( 'role_account_flagged_blocked', $output );
		$this->assertStringContainsString( 'alynt_ag_reoon_blocked', $output );
		$this->assertStringContainsString( 'alynt_ag_turnstile_failed', $output );
		$this->assertStringContainsString( 'alynt_ag_reoon_missing', $output );
		$this->assertStringContainsString( 'alynt_ag_reoon_request_failed', $output );
		$this->assertStringContainsString( 'alynt_ag_reoon_invalid_response', $output );
		$this->assertStringContainsString( 'alynt_ag_turnstile_missing', $output );
		$this->assertStringContainsString( 'alynt_ag_turnstile_request_failed', $output );
		$this->assertStringContainsString( 'login_rate_limited', $output );
		$this->assertStringContainsString( 'lostpassword_rate_limited', $output );
		$this->assertStringContainsString( 'terms_required', $output );
		$this->assertStringContainsString( 'confirmation_resent', $output );
		$this->assertStringContainsString( 'pending_registration_failed', $output );
		$this->assertStringContainsString( 'password_mismatch', $output );
		$this->assertStringContainsString( 'Passed', $output );
		$this->assertStringContainsString( 'Blocked', $output );
		$this->assertStringContainsString( 'Reoon accepted this email.', $output );
		$this->assertStringContainsString( 'Registration attempt was blocked by the rate limit.', $output );
		$this->assertStringContainsString( 'Confirmation resend was blocked by the rate limit. Ask the customer to wait for the configured resend window before trying again.', $output );
		$this->assertStringContainsString( 'Reoon allowed this email, but the status should be reviewed.', $output );
		$this->assertStringContainsString( 'Reoon blocked this flagged email because the flagged-status policy is set to block.', $output );
		$this->assertStringContainsString( 'Reoon blocked this email by policy.', $output );
		$this->assertStringContainsString( 'Reoon was not configured when verification ran. Confirm the API key before enabling public registration.', $output );
		$this->assertStringContainsString( 'Reoon could not be reached. Check outbound HTTP connectivity, API availability, and the saved API key.', $output );
		$this->assertStringContainsString( 'Reoon returned an unexpected response. Review provider availability and test the saved API key.', $output );
		$this->assertStringContainsString( 'Turnstile rejected the challenge response. Ask the customer to retry and confirm the site key matches the secret key.', $output );
		$this->assertStringContainsString( 'Turnstile was not configured when verification ran. Confirm both the site key and secret key before launch.', $output );
		$this->assertStringContainsString( 'Turnstile verification could not reach Cloudflare. Check outbound HTTP connectivity and the saved secret key.', $output );
		$this->assertStringContainsString( 'Login attempt was blocked by the rate limit.', $output );
		$this->assertStringContainsString( 'Password reset request was blocked by the rate limit.', $output );
		$this->assertStringContainsString( 'Registration was blocked because terms and privacy consent was not accepted.', $output );
		$this->assertStringContainsString( 'A fresh confirmation email was sent for an existing pending registration.', $output );
		$this->assertStringContainsString( 'The pending registration record could not be stored.', $output );
		$this->assertStringContainsString( 'Account creation was blocked because the password confirmation did not match.', $output );
		$this->assertStringNotContainsString( 'damon@example.test', $output );
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
						'status'       => 'completed',
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
				'status'       => 'completed',
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
}
