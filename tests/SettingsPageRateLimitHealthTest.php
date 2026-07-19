<?php
/**
 * Focused structural test suite.
 *
 * @package Alynt_Account_Gateway
 */

require_once __DIR__ . '/support/class-settings-page-security-status-test-case.php';

/**
 * Tests rate-limit pressure and provider health.
 */
class SettingsPageRateLimitHealthTest extends SettingsPageSecurityStatusTestCase {

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

	public function test_security_active_rate_limit_bucket_items_count_current_lockouts() {
		$GLOBALS['alynt_ag_test_db_results']['wp_options'] = array(
			(object) array(
				'option_value' => serialize(
					array(
						'action'     => 'registration',
						'count'      => 5,
						'limit'      => 5,
						'locked'     => true,
						'expires_at' => time() + 300,
					)
				),
			),
			(object) array(
				'option_value' => serialize(
					array(
						'action'     => 'registration',
						'count'      => 2,
						'limit'      => 5,
						'locked'     => false,
						'expires_at' => time() + 300,
					)
				),
			),
			(object) array(
				'option_value' => serialize(
					array(
						'action'     => 'login',
						'count'      => 10,
						'limit'      => 10,
						'locked'     => true,
						'expires_at' => time() + 300,
					)
				),
			),
			(object) array(
				'option_value' => serialize(
					array(
						'action'     => 'lostpassword',
						'count'      => 5,
						'limit'      => 5,
						'locked'     => true,
						'expires_at' => time() - 1,
					)
				),
			),
		);

		$settings_page = new ALYNT_AG_Settings_Page();
		$items         = $this->invoke_helper( $settings_page, 'security_active_rate_limit_bucket_items' );

		$this->assertSame( 'Registration', $items[0]['label'] );
		$this->assertSame( 1, $items[0]['count'] );
		$this->assertSame( 'warning', $items[0]['status'] );
		$this->assertSame( 'active lockouts from 2 current registration buckets.', $items[0]['message'] );
		$this->assertSame( 'Confirmation Resends', $items[1]['label'] );
		$this->assertSame( 0, $items[1]['count'] );
		$this->assertSame( 'Login', $items[2]['label'] );
		$this->assertSame( 1, $items[2]['count'] );
		$this->assertSame( 'warning', $items[2]['status'] );
		$this->assertSame( 'Password Reset', $items[3]['label'] );
		$this->assertSame( 0, $items[3]['count'] );
		$this->assertSame( 'ready', $items[3]['status'] );
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
}
