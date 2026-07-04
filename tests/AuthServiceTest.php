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
		$GLOBALS['alynt_ag_test_throw_on_redirect'] = false;
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
