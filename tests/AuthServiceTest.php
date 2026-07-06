<?php
/**
 * Auth service tests.
 *
 * @package Alynt_Account_Gateway
 */

use PHPUnit\Framework\TestCase;

/**
 * Tests branded authentication helpers.
 */
class AuthServiceTest extends TestCase {

	protected function setUp(): void {
		parent::setUp();
		$GLOBALS['alynt_ag_test_transients'] = array();
		$GLOBALS['alynt_ag_test_reset_password'] = null;
		$GLOBALS['alynt_ag_test_redirects'] = array();
		$GLOBALS['alynt_ag_test_signons'] = array();
		$GLOBALS['alynt_ag_test_retrieve_passwords'] = array();
		$GLOBALS['alynt_ag_test_db_inserts'] = array();
		$GLOBALS['alynt_ag_test_throw_on_redirect'] = false;
		unset(
			$GLOBALS['alynt_ag_test_existing_emails'],
			$GLOBALS['alynt_ag_test_retrieve_password_result'],
			$GLOBALS['alynt_ag_test_options']['alynt_ag_settings']
		);
		$_SERVER['REMOTE_ADDR'] = '203.0.113.30';
		$_SERVER['REQUEST_METHOD'] = 'GET';
		$_POST = array();
	}

	public function test_login_error_message_is_neutral() {
		$service = new ALYNT_AG_Auth_Service();

		$this->assertSame(
			'The email address or password is incorrect.',
			$service->get_login_error_message( 'invalid_email' )
		);
		$this->assertSame(
			'The email address or password is incorrect.',
			$service->get_login_error_message( 'incorrect_password' )
		);
	}

	public function test_lostpassword_sent_message_is_neutral() {
		$service = new ALYNT_AG_Auth_Service();

		$this->assertStringContainsString( 'If an account can receive password reset instructions', $service->get_lostpassword_sent_message() );
	}

	public function test_login_rate_limit_uses_configured_bucket() {
		$service = new ALYNT_AG_Auth_Service();
		$settings = array(
			'login_rate_limit_count'  => 1,
			'login_rate_limit_window' => 60,
		);

		$this->assertTrue( $service->validate_rate_limit( 'login', 'damon@example.test', $settings ) );

		$result = $service->validate_rate_limit( 'login', 'damon@example.test', $settings );

		$this->assertInstanceOf( WP_Error::class, $result );
		$this->assertSame( 'alynt_ag_rate_limited', $result->get_error_code() );
		$this->assertCount( 1, $GLOBALS['alynt_ag_test_db_inserts'] );
		$this->assertSame( 'rate_limit', $GLOBALS['alynt_ag_test_db_inserts'][0]['data']['provider'] );
		$this->assertSame( 'login_rate_limited', $GLOBALS['alynt_ag_test_db_inserts'][0]['data']['status'] );
		$this->assertSame( 1, $GLOBALS['alynt_ag_test_db_inserts'][0]['data']['blocked'] );
	}

	public function test_login_redirect_uses_default_when_no_redirect_is_submitted() {
		$service  = new ALYNT_AG_Auth_Service();
		$settings = array(
			'after_login_redirect' => '/my-account/',
		);

		$this->assertSame(
			'https://example.test/my-account/',
			$service->get_login_redirect_url( '', $settings )
		);
	}

	public function test_login_redirect_rejects_external_redirects() {
		$service  = new ALYNT_AG_Auth_Service();
		$settings = array(
			'after_login_redirect' => '/my-account/',
		);

		$this->assertSame(
			'https://example.test/my-account/',
			$service->get_login_redirect_url( 'https://evil.example/phish', $settings )
		);
	}

	public function test_login_submission_requires_email_identifier() {
		$service = new ALYNT_AG_Auth_Service();
		$GLOBALS['alynt_ag_test_throw_on_redirect'] = true;
		$_SERVER['REQUEST_METHOD'] = 'POST';
		$_POST = array(
			'alynt_ag_action' => 'login',
			'email'           => 'damon',
			'pwd'             => 'StrongPassword1!',
		);

		try {
			$service->maybe_handle_auth_request();
			$this->fail( 'Expected redirect exception.' );
		} catch ( RuntimeException $exception ) {
			$this->assertSame( 'redirect:https://example.test/login?login_error=failed', $exception->getMessage() );
		}

		$this->assertSame( array(), $GLOBALS['alynt_ag_test_signons'] );
	}

	public function test_login_submission_passes_email_to_wordpress_signon() {
		$service = new ALYNT_AG_Auth_Service();
		$GLOBALS['alynt_ag_test_throw_on_redirect'] = true;
		$_SERVER['REQUEST_METHOD'] = 'POST';
		$_POST = array(
			'alynt_ag_action' => 'login',
			'email'           => 'Damon@Example.test',
			'pwd'             => 'StrongPassword1!',
			'rememberme'      => '1',
		);

		try {
			$service->maybe_handle_auth_request();
			$this->fail( 'Expected redirect exception.' );
		} catch ( RuntimeException $exception ) {
			$this->assertSame( 'redirect:https://example.test/my-account/', $exception->getMessage() );
		}

		$this->assertSame( 'damon@example.test', $GLOBALS['alynt_ag_test_signons'][0]['credentials']['user_login'] );
		$this->assertSame( 'StrongPassword1!', $GLOBALS['alynt_ag_test_signons'][0]['credentials']['user_password'] );
		$this->assertTrue( $GLOBALS['alynt_ag_test_signons'][0]['credentials']['remember'] );
	}

	public function test_login_submission_logs_success_without_submitted_credentials() {
		$service = new ALYNT_AG_Auth_Service();
		$GLOBALS['alynt_ag_test_options']['alynt_ag_settings'] = array(
			'diagnostics_enabled'   => true,
			'diagnostics_min_level' => 'debug',
			'frontend_enabled'      => true,
			'login_path'            => '/login',
			'after_login_redirect'  => '/my-account/',
		);
		$GLOBALS['alynt_ag_test_throw_on_redirect'] = true;
		$_SERVER['REQUEST_METHOD'] = 'POST';
		$_POST = array(
			'alynt_ag_action' => 'login',
			'email'           => 'Damon@Example.test',
			'pwd'             => 'StrongPassword1!',
			'redirect_to'     => 'https://example.test/my-account/orders/',
		);

		try {
			$service->maybe_handle_auth_request();
			$this->fail( 'Expected redirect exception.' );
		} catch ( RuntimeException $exception ) {
			$this->assertSame( 'redirect:https://example.test/my-account/orders/', $exception->getMessage() );
		}

		$tables = ALYNT_AG_Database::tables();
		$this->assertCount( 1, $GLOBALS['alynt_ag_test_db_inserts'] );
		$this->assertSame( $tables['diagnostics_logs'], $GLOBALS['alynt_ag_test_db_inserts'][0]['table'] );

		$row     = $GLOBALS['alynt_ag_test_db_inserts'][0]['data'];
		$context = json_decode( $row['context'], true );

		$this->assertSame( 'info', $row['level'] );
		$this->assertSame( 'security', $row['category'] );
		$this->assertSame( 'branded_login_succeeded', $row['event_code'] );
		$this->assertSame( '/my-account/orders/', $context['destination_path'] );
		$this->assertTrue( $context['redirect_to_present'] );
		$this->assertTrue( $context['redirect_to_accepted'] );
		$this->assertStringNotContainsString( 'Damon@Example.test', $row['context'] );
		$this->assertStringNotContainsString( 'damon@example.test', $row['context'] );
		$this->assertStringNotContainsString( 'StrongPassword1!', $row['context'] );
	}

	public function test_login_submission_logs_invalid_request_without_submitted_email() {
		$service = new ALYNT_AG_Auth_Service();
		$GLOBALS['alynt_ag_test_options']['alynt_ag_settings'] = array(
			'diagnostics_enabled'   => true,
			'diagnostics_min_level' => 'debug',
			'frontend_enabled'      => true,
			'login_path'            => '/login',
			'after_login_redirect'  => '/my-account/',
		);
		$GLOBALS['alynt_ag_test_throw_on_redirect'] = true;
		$_SERVER['REQUEST_METHOD'] = 'POST';
		$_POST = array(
			'alynt_ag_action' => 'login',
			'email'           => 'not-an-email',
			'pwd'             => '',
		);

		try {
			$service->maybe_handle_auth_request();
			$this->fail( 'Expected redirect exception.' );
		} catch ( RuntimeException $exception ) {
			$this->assertSame( 'redirect:https://example.test/login?login_error=failed', $exception->getMessage() );
		}

		$row     = $GLOBALS['alynt_ag_test_db_inserts'][0]['data'];
		$context = json_decode( $row['context'], true );

		$this->assertSame( 'warning', $row['level'] );
		$this->assertSame( 'branded_login_failed', $row['event_code'] );
		$this->assertSame( 'invalid_request', $context['reason'] );
		$this->assertTrue( $context['has_email'] );
		$this->assertFalse( $context['has_password'] );
		$this->assertStringNotContainsString( 'not-an-email', $row['context'] );
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
