<?php
/**
 * Authentication service tests.
 *
 * @package Alynt_Account_Gateway
 */

require_once __DIR__ . '/support/class-auth-service-test-case.php';

/**
 * Tests lost-password and password-reset behavior.
 */
class AuthPasswordRecoveryTest extends AuthServiceTestCase {

	public function test_lostpassword_sent_message_is_neutral() {
		$service = new ALYNT_AG_Auth_Service();

		$this->assertStringContainsString( 'If an account can receive password reset instructions', $service->get_lostpassword_sent_message() );
	}

	public function test_lostpassword_rate_limit_uses_configured_bucket() {
		$service = new ALYNT_AG_Auth_Service();
		$settings = array(
			'lostpassword_rate_limit_count'  => 1,
			'lostpassword_rate_limit_window' => 60,
		);

		$this->assertTrue( $service->validate_rate_limit( 'lostpassword', 'damon@example.test', $settings ) );

		$result = $service->validate_rate_limit( 'lostpassword', 'damon@example.test', $settings );

		$this->assertInstanceOf( WP_Error::class, $result );
		$this->assertSame( 'alynt_ag_rate_limited', $result->get_error_code() );
		$this->assertCount( 1, $GLOBALS['alynt_ag_test_db_inserts'] );
		$this->assertSame( 'rate_limit', $GLOBALS['alynt_ag_test_db_inserts'][0]['data']['provider'] );
		$this->assertSame( 'lostpassword_rate_limited', $GLOBALS['alynt_ag_test_db_inserts'][0]['data']['status'] );
		$this->assertSame( 1, $GLOBALS['alynt_ag_test_db_inserts'][0]['data']['blocked'] );
	}

	public function test_lostpassword_submission_logs_neutral_request_without_submitted_email() {
		$service = new ALYNT_AG_Auth_Service();
		$GLOBALS['alynt_ag_test_options']['alynt_ag_settings'] = array(
			'diagnostics_enabled'            => true,
			'diagnostics_min_level'          => 'debug',
			'frontend_enabled'               => true,
			'account_action_base'            => '/account',
			'after_login_redirect'           => '/my-account/',
			'lostpassword_rate_limit_count'  => 5,
			'lostpassword_rate_limit_window' => 15,
		);
		$GLOBALS['alynt_ag_test_existing_emails'] = array( 'damon@example.test' );
		$GLOBALS['alynt_ag_test_throw_on_redirect'] = true;
		$_SERVER['REQUEST_METHOD'] = 'POST';
		$_POST = array(
			'alynt_ag_action' => 'lostpassword',
			'user_login'      => 'damon@example.test',
		);

		try {
			$service->maybe_handle_auth_request();
			$this->fail( 'Expected redirect exception.' );
		} catch ( RuntimeException $exception ) {
			$this->assertSame( 'redirect:https://example.test/account?action=lostpassword&reset_sent=1', $exception->getMessage() );
		}

		$this->assertSame( array( 'damon@example.test' ), $GLOBALS['alynt_ag_test_retrieve_passwords'] );

		$row     = $GLOBALS['alynt_ag_test_db_inserts'][0]['data'];
		$context = json_decode( $row['context'], true );

		$this->assertSame( 'info', $row['level'] );
		$this->assertSame( 'branded_password_reset_requested', $row['event_code'] );
		$this->assertTrue( $context['has_valid_email'] );
		$this->assertTrue( $context['delivery_attempted'] );
		$this->assertStringNotContainsString( 'damon@example.test', $row['context'] );
	}

	public function test_password_reset_key_validation_returns_neutral_error_code() {
		$service = new ALYNT_AG_Auth_Service();
		$result  = $service->validate_password_reset_key( 'bad-key', 'damon@example.test' );

		$this->assertInstanceOf( WP_Error::class, $result );
		$this->assertSame( 'invalid_or_expired_token', $result->get_error_code() );
	}

	public function test_password_reset_requires_v1_password_policy() {
		$service = new ALYNT_AG_Auth_Service();
		$result  = $service->complete_password_reset( 'good-key', 'damon@example.test', 'weak', 'weak' );

		$this->assertInstanceOf( WP_Error::class, $result );
		$this->assertSame( 'alynt_ag_password_length', $result->get_error_code() );
	}

	public function test_password_reset_logs_failure_and_completion_without_login_value() {
		$service = new ALYNT_AG_Auth_Service();
		$GLOBALS['alynt_ag_test_options']['alynt_ag_settings'] = array(
			'diagnostics_enabled'   => true,
			'diagnostics_min_level' => 'debug',
		);

		$failed = $service->complete_password_reset( 'bad-key', 'damon@example.test', 'StrongPassword1!', 'StrongPassword1!' );
		$passed = $service->complete_password_reset( 'good-key', 'damon@example.test', 'StrongPassword1!', 'StrongPassword1!' );

		$this->assertInstanceOf( WP_Error::class, $failed );
		$this->assertTrue( $passed );
		$this->assertCount( 2, $GLOBALS['alynt_ag_test_db_inserts'] );

		$failure_row     = $GLOBALS['alynt_ag_test_db_inserts'][0]['data'];
		$failure_context = json_decode( $failure_row['context'], true );
		$success_row     = $GLOBALS['alynt_ag_test_db_inserts'][1]['data'];
		$success_context = json_decode( $success_row['context'], true );

		$this->assertSame( 'branded_password_reset_failed', $failure_row['event_code'] );
		$this->assertSame( 'invalid_or_expired_token', $failure_context['reason'] );
		$this->assertTrue( $failure_context['key_present'] );
		$this->assertTrue( $failure_context['login_present'] );
		$this->assertSame( 'branded_password_reset_completed', $success_row['event_code'] );
		$this->assertSame( 123, $success_context['user_id'] );
		$this->assertStringNotContainsString( 'damon@example.test', $failure_row['context'] );
		$this->assertStringNotContainsString( 'damon@example.test', $success_row['context'] );
	}

	public function test_password_reset_updates_password_when_key_and_password_are_valid() {
		$service = new ALYNT_AG_Auth_Service();
		$result  = $service->complete_password_reset( 'good-key', 'damon@example.test', 'StrongPassword1!', 'StrongPassword1!' );

		$this->assertTrue( $result );
		$this->assertSame(
			array(
				'user_login' => 'damon@example.test',
				'password'   => 'StrongPassword1!',
			),
			$GLOBALS['alynt_ag_test_reset_password']
		);
	}
}
