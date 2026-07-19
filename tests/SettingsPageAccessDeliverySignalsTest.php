<?php
/**
 * Focused structural test suite.
 *
 * @package Alynt_Account_Gateway
 */

require_once __DIR__ . '/support/class-settings-page-security-status-test-case.php';

/**
 * Tests access, authentication, and delivery signals.
 */
class SettingsPageAccessDeliverySignalsTest extends SettingsPageSecurityStatusTestCase {

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
						'context'    => wp_json_encode(
							array(
								'request_path'       => '/wp-admin/admin.php',
								'destination_path'   => '/my-account/',
								'request_query_keys' => array( 'page', 'redirect_to' ),
							)
						),
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
		$this->assertStringContainsString( 'Latest blocked path: /wp-admin/admin.php -> /my-account/.', $items[2]['message'] );
		$this->assertStringContainsString( 'Query keys: page, redirect_to.', $items[2]['message'] );
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

	public function test_security_branded_auth_signals_count_recent_activity() {
		$settings_page = new ALYNT_AG_Settings_Page();
		$items         = $this->invoke_helper(
			$settings_page,
			'security_branded_auth_signal_items',
			array(
				array(
					(object) array(
						'event_code' => 'branded_login_failed',
					),
					(object) array(
						'event_code' => 'branded_login_rate_limited',
					),
					(object) array(
						'event_code' => 'branded_login_succeeded',
					),
					(object) array(
						'event_code' => 'branded_password_reset_requested',
					),
					(object) array(
						'event_code' => 'branded_password_reset_failed',
					),
					(object) array(
						'event_code' => 'branded_password_reset_email_failed',
					),
					(object) array(
						'event_code' => 'branded_password_reset_rate_limited',
					),
					(object) array(
						'event_code' => 'branded_password_reset_completed',
					),
					(object) array(
						'event_code' => 'native_login_redirected',
					),
				),
			)
		);

		$this->assertSame( 'Gateway Login Failures', $items[0]['label'] );
		$this->assertSame( 2, $items[0]['count'] );
		$this->assertSame( 'warning', $items[0]['status'] );
		$this->assertSame( 'Gateway Login Successes', $items[1]['label'] );
		$this->assertSame( 1, $items[1]['count'] );
		$this->assertSame( 'ready', $items[1]['status'] );
		$this->assertSame( 'Password Reset Requests', $items[2]['label'] );
		$this->assertSame( 1, $items[2]['count'] );
		$this->assertSame( 'warning', $items[2]['status'] );
		$this->assertSame( 'Password Reset Issues', $items[3]['label'] );
		$this->assertSame( 3, $items[3]['count'] );
		$this->assertSame( 'action', $items[3]['status'] );
		$this->assertSame( 'Password Reset Completions', $items[4]['label'] );
		$this->assertSame( 1, $items[4]['count'] );
		$this->assertSame( 'ready', $items[4]['status'] );
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
}
