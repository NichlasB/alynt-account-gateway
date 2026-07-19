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
class SettingsPageProviderStatusTest extends SettingsPageSecurityStatusTestCase {

	public function test_provider_connection_checks_render_without_credentials() {
		$settings                         = ALYNT_AG_Settings_Schema::defaults();
		$settings['turnstile_site_key']   = 'visible-site-key';
		$settings['turnstile_secret_key'] = 'private-turnstile-secret';
		$settings['reoon_api_key']        = 'private-reoon-key';
		$settings_page                    = new ALYNT_AG_Settings_Page();

		ob_start();
		$this->invoke_helper( $settings_page, 'render_security_provider_checks', array( $settings ) );
		$output = ob_get_clean();

		$this->assertStringContainsString( 'Provider Connection Checks', $output );
		$this->assertStringContainsString( 'Check Turnstile Connection', $output );
		$this->assertStringContainsString( 'Check Reoon Account', $output );
		$this->assertStringContainsString( 'name="provider" value="turnstile"', $output );
		$this->assertStringContainsString( 'name="provider" value="reoon"', $output );
		$this->assertStringContainsString( 'does not submit an email address', $output );
		$this->assertStringNotContainsString( 'visible-site-key', $output );
		$this->assertStringNotContainsString( 'private-turnstile-secret', $output );
		$this->assertStringNotContainsString( 'private-reoon-key', $output );
		$this->assertStringNotContainsString( 'disabled="disabled"', $output );
	}

	public function test_provider_connection_checks_disable_unconfigured_providers() {
		$settings_page = new ALYNT_AG_Settings_Page();

		ob_start();
		$this->invoke_helper(
			$settings_page,
			'render_security_provider_checks',
			array( ALYNT_AG_Settings_Schema::defaults() )
		);
		$output = ob_get_clean();

		$this->assertSame( 2, substr_count( $output, 'disabled="disabled"' ) );
	}

	public function test_provider_connection_check_notice_keys_are_fixed() {
		$settings_page = new ALYNT_AG_Settings_Page();

		$this->assertSame(
			'turnstile_check_ready',
			$this->invoke_helper( $settings_page, 'security_provider_check_notice_key', array( 'turnstile', true ) )
		);
		$this->assertSame(
			'turnstile_check_invalid_secret',
			$this->invoke_helper(
				$settings_page,
				'security_provider_check_notice_key',
				array( 'turnstile', new WP_Error( 'alynt_ag_turnstile_invalid_secret', 'Sensitive provider detail.' ) )
			)
		);
		$this->assertSame(
			'reoon_check_request_failed',
			$this->invoke_helper(
				$settings_page,
				'security_provider_check_notice_key',
				array( 'reoon', new WP_Error( 'alynt_ag_reoon_request_failed', 'Sensitive provider detail.' ) )
			)
		);
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
		$this->assertStringContainsString( 'Active Rate Limit Buckets', $output );
		$this->assertStringContainsString( 'Active buckets show privacy-preserving lockout pressure', $output );
		$this->assertStringContainsString( 'Recent Registration Verification Activity', $output );
		$this->assertStringContainsString( 'No verification activity has been logged yet.', $output );
		$this->assertStringContainsString( 'Recent Pending Registrations', $output );
		$this->assertStringContainsString( 'No pending registration records have been created yet.', $output );
	}

	public function test_security_launch_decision_items_warn_before_public_launch() {
		$settings      = ALYNT_AG_Settings_Schema::defaults();
		$settings_page = new ALYNT_AG_Settings_Page();
		$items         = $this->invoke_helper( $settings_page, 'security_launch_decision_items', array( $settings ) );

		$this->assertSame( 'Public Registration', $items[0]['label'] );
		$this->assertSame( 'action', $items[0]['status'] );
		$this->assertSame( 'Public account creation is disabled. Keep it disabled while configuring the gateway, then enable it only after provider and email checks are ready.', $items[0]['message'] );
		$this->assertSame( 'Anti-Spam Coverage', $items[1]['label'] );
		$this->assertSame( 'action', $items[1]['status'] );
		$this->assertSame( 'No fully configured anti-spam provider is available. Configure Turnstile or Reoon before public registration receives traffic.', $items[1]['message'] );
		$this->assertSame( 'Consent Links', $items[2]['label'] );
		$this->assertSame( 'ready', $items[2]['status'] );
		$this->assertSame( 'Flagged Email Policy', $items[3]['label'] );
		$this->assertSame( 'warning', $items[3]['status'] );
		$this->assertSame( 'Reoon is not configured, so flagged email policy decisions are inactive. Use Turnstile alone or add Reoon before relying on email-quality review.', $items[3]['message'] );
		$this->assertSame( 'Launch Evidence', $items[4]['label'] );
		$this->assertSame( 'warning', $items[4]['status'] );
	}

	public function test_security_launch_decision_items_mark_ready_configuration() {
		$settings                         = ALYNT_AG_Settings_Schema::defaults();
		$settings['registration_enabled'] = true;
		$settings['turnstile_site_key']   = 'site-key';
		$settings['turnstile_secret_key'] = 'secret-key';
		$settings['reoon_api_key']        = 'reoon-key';
		$settings['reoon_flagged_policy'] = 'block';
		$settings['diagnostics_enabled']  = true;

		$settings_page = new ALYNT_AG_Settings_Page();
		$items         = $this->invoke_helper( $settings_page, 'security_launch_decision_items', array( $settings ) );

		$this->assertSame( 'ready', $items[0]['status'] );
		$this->assertSame( 'ready', $items[1]['status'] );
		$this->assertSame( 'ready', $items[2]['status'] );
		$this->assertSame( 'ready', $items[3]['status'] );
		$this->assertSame( 'Blocks catch-all, role account, unknown, and inbox-full statuses before account creation.', $items[3]['message'] );
		$this->assertSame( 'ready', $items[4]['status'] );
		$this->assertSame( 'Diagnostics are enabled, so launch and support signals can be collected during registration rollout.', $items[4]['message'] );
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
		$this->assertStringContainsString( 'Launch Decision Summary', $output );
		$this->assertStringContainsString( 'Use this quick checklist before making public registration available.', $output );
		$this->assertStringContainsString( 'Public Registration', $output );
		$this->assertStringContainsString( 'Anti-Spam Coverage', $output );
		$this->assertStringContainsString( 'Consent Links', $output );
		$this->assertStringContainsString( 'Flagged Email Policy', $output );
		$this->assertStringContainsString( 'Launch Evidence', $output );
		$this->assertStringContainsString( 'Every configured provider must pass registration.', $output );
		$this->assertStringContainsString( 'Server-side verification can run', $output );
		$this->assertStringContainsString( 'Email quality verification can run', $output );
		$this->assertStringContainsString( 'Always blocks invalid, disabled, disposable, and spamtrap statuses.', $output );
		$this->assertStringContainsString( 'Allows but logs catch-all, role account, unknown, and inbox-full statuses for admin review.', $output );
		$this->assertStringContainsString( 'Current policy: Allow and log flagged statuses.', $output );
		$this->assertStringContainsString( 'Reoon Result Group', $output );
		$this->assertStringContainsString( 'Always blocked', $output );
		$this->assertStringContainsString( 'invalid, disabled, disposable, spamtrap', $output );
		$this->assertStringContainsString( 'Configurable flagged statuses', $output );
		$this->assertStringContainsString( 'catch_all, role_account, unknown, inbox_full', $output );
		$this->assertStringContainsString( 'Allowed, logged, and shown for admin review.', $output );
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
		$this->assertStringContainsString( 'Configurable flagged statuses', $output );
		$this->assertStringContainsString( 'Blocked before account creation.', $output );
		$this->assertStringContainsString( 'Switch to blocking when support volume, spam pressure, or fraud risk matters more', $output );
	}

	public function test_reoon_policy_visibility_items_follow_selected_policy() {
		$settings_page = new ALYNT_AG_Settings_Page();

		$allow_items = $this->invoke_helper( $settings_page, 'security_reoon_policy_visibility_items', array( 'allow' ) );
		$block_items = $this->invoke_helper( $settings_page, 'security_reoon_policy_visibility_items', array( 'block' ) );

		$this->assertSame( 'Always blocked', $allow_items[0]['group'] );
		$this->assertSame( 'invalid, disabled, disposable, spamtrap', $allow_items[0]['statuses'] );
		$this->assertSame( 'Blocked before account creation.', $allow_items[0]['treatment'] );
		$this->assertSame( 'Configurable flagged statuses', $allow_items[1]['group'] );
		$this->assertSame( 'catch_all, role_account, unknown, inbox_full', $allow_items[1]['statuses'] );
		$this->assertSame( 'Allowed, logged, and shown for admin review.', $allow_items[1]['treatment'] );
		$this->assertSame( 'Blocked before account creation.', $block_items[1]['treatment'] );
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
