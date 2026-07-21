<?php
/**
 * Authentication service tests.
 *
 * @package Alynt_Account_Gateway
 */

require_once __DIR__ . '/support/class-auth-service-test-case.php';

/**
 * Tests branded login submissions and diagnostics.
 */
class AuthLoginSubmissionTest extends AuthServiceTestCase {

	public function test_expired_login_nonce_returns_to_branded_login_screen() {
		$service = new ALYNT_AG_Auth_Service();
		$GLOBALS['alynt_ag_test_throw_on_redirect'] = true;
		$GLOBALS['alynt_ag_test_nonce_valid']       = false;
		$_SERVER['REQUEST_METHOD']                  = 'POST';
		$_POST = array(
			'alynt_ag_action'    => 'login',
			'alynt_ag_auth_nonce' => 'expired',
			'email'              => 'damon@example.test',
			'pwd'                => 'StrongPassword1!',
		);

		try {
			$service->maybe_handle_auth_request();
			$this->fail( 'Expected redirect exception.' );
		} catch ( RuntimeException $exception ) {
			$this->assertSame( 'redirect:https://example.test/login?login_error=session_expired', $exception->getMessage() );
		}

		$this->assertSame( array(), $GLOBALS['alynt_ag_test_signons'] );
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

	public function test_failed_login_preserves_valid_same_site_return_destination() {
		$service = new ALYNT_AG_Auth_Service();
		$GLOBALS['alynt_ag_test_throw_on_redirect'] = true;
		$_SERVER['REQUEST_METHOD'] = 'POST';
		$_POST = array(
			'alynt_ag_action' => 'login',
			'email'           => 'not-an-email',
			'pwd'             => 'StrongPassword1!',
			'redirect_to'     => 'https://example.test/checkout/',
		);

		try {
			$service->maybe_handle_auth_request();
			$this->fail( 'Expected redirect exception.' );
		} catch ( RuntimeException $exception ) {
			$this->assertSame(
				'redirect:https://example.test/login?login_error=failed&redirect_to=https%253A%252F%252Fexample.test%252Fcheckout%252F',
				$exception->getMessage()
			);
		}
	}

	public function test_rate_limited_login_preserves_valid_same_site_return_destination() {
		$service  = new ALYNT_AG_Auth_Service();
		$settings = array(
			'login_rate_limit_count'  => 1,
			'login_rate_limit_window' => 60,
		);
		$GLOBALS['alynt_ag_test_options']['alynt_ag_settings'] = $settings;
		$GLOBALS['alynt_ag_test_throw_on_redirect']            = true;
		$_SERVER['REQUEST_METHOD']                             = 'POST';
		$_POST                                                = array(
			'alynt_ag_action' => 'login',
			'email'           => 'damon@example.test',
			'pwd'             => 'StrongPassword1!',
			'redirect_to'     => 'https://example.test/checkout/',
		);

		$this->assertTrue( $service->validate_rate_limit( 'login', 'damon@example.test', $settings ) );

		try {
			$service->maybe_handle_auth_request();
			$this->fail( 'Expected redirect exception.' );
		} catch ( RuntimeException $exception ) {
			$this->assertSame(
				'redirect:https://example.test/login?login_error=alynt_ag_rate_limited&redirect_to=https%253A%252F%252Fexample.test%252Fcheckout%252F',
				$exception->getMessage()
			);
		}
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

	public function test_login_submission_uses_authenticated_user_role_default() {
		$service = new ALYNT_AG_Auth_Service();
		$GLOBALS['alynt_ag_test_signon_roles'] = array( 'administrator' );
		$GLOBALS['alynt_ag_test_options']['alynt_ag_settings'] = array(
			'frontend_enabled'                   => true,
			'login_path'                         => '/login',
			'after_login_redirect'               => '/my-account/',
			'administrator_after_login_redirect' => '/wp-admin/',
		);
		$GLOBALS['alynt_ag_test_throw_on_redirect'] = true;
		$_SERVER['REQUEST_METHOD'] = 'POST';
		$_POST = array(
			'alynt_ag_action' => 'login',
			'email'           => 'admin@example.test',
			'pwd'             => 'StrongPassword1!',
		);

		try {
			$service->maybe_handle_auth_request();
			$this->fail( 'Expected redirect exception.' );
		} catch ( RuntimeException $exception ) {
			$this->assertSame( 'redirect:https://example.test/wp-admin/', $exception->getMessage() );
		}
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
}
