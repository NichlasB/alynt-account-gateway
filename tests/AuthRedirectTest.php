<?php
/**
 * Authentication service tests.
 *
 * @package Alynt_Account_Gateway
 */

require_once __DIR__ . '/support/class-auth-service-test-case.php';

/**
 * Tests role-aware and safe login redirects.
 */
class AuthRedirectTest extends AuthServiceTestCase {

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

	public function test_login_redirect_uses_administrator_default_when_no_redirect_is_submitted() {
		$service  = new ALYNT_AG_Auth_Service();
		$user     = new WP_User( 'admin@example.test' );
		$user->roles = array( 'administrator' );
		$settings = array(
			'after_login_redirect'               => '/my-account/',
			'administrator_after_login_redirect' => '/wp-admin/',
		);

		$this->assertSame(
			'https://example.test/wp-admin/',
			$service->get_login_redirect_url( '', $settings, $user )
		);
	}

	public function test_login_redirect_uses_shop_manager_default_when_no_redirect_is_submitted() {
		$service  = new ALYNT_AG_Auth_Service();
		$user     = new WP_User( 'manager@example.test' );
		$user->roles = array( 'shop_manager' );
		$settings = array(
			'after_login_redirect'               => '/my-account/',
			'shop_manager_after_login_redirect'  => '/store-management/',
		);

		$this->assertSame(
			'https://example.test/store-management/',
			$service->get_login_redirect_url( '', $settings, $user )
		);
	}

	public function test_login_redirect_allows_a_safe_custom_operator_default() {
		$service  = new ALYNT_AG_Auth_Service();
		$user     = new WP_User( 'operator@example.test' );
		$user->roles = array( 'video_store_manager' );
		$settings = array(
			'after_login_redirect' => '/my-account/',
		);
		$callback = static function ( $default, $active_settings, $authenticated_user ) {
			unset( $active_settings );

			return in_array( 'video_store_manager', $authenticated_user->roles, true )
				? 'https://example.test/wp-admin/admin.php?page=video-sales'
				: $default;
		};

		add_filter( 'alynt_ag_default_login_redirect_url', $callback, 10, 3 );

		try {
			$this->assertSame(
				'https://example.test/wp-admin/admin.php?page=video-sales',
				$service->get_login_redirect_url( '', $settings, $user )
			);
		} finally {
			remove_filter( 'alynt_ag_default_login_redirect_url', $callback, 10 );
		}
	}

	public function test_safe_submitted_redirect_wins_over_role_default() {
		$service  = new ALYNT_AG_Auth_Service();
		$user     = new WP_User( 'admin@example.test' );
		$user->roles = array( 'administrator' );
		$settings = array(
			'after_login_redirect'               => '/my-account/',
			'administrator_after_login_redirect' => '/wp-admin/',
			'login_path'                        => '/login/',
			'account_action_base'               => '/account',
		);

		$this->assertSame(
			'https://example.test/wp-admin/profile.php',
			$service->get_login_redirect_url( 'https://example.test/wp-admin/profile.php', $settings, $user )
		);
	}

	public function test_rejected_redirect_falls_back_to_role_default() {
		$service  = new ALYNT_AG_Auth_Service();
		$user     = new WP_User( 'manager@example.test' );
		$user->roles = array( 'shop_manager' );
		$settings = array(
			'after_login_redirect'              => '/my-account/',
			'shop_manager_after_login_redirect' => '/wp-admin/',
		);

		$this->assertSame(
			'https://example.test/wp-admin/',
			$service->get_login_redirect_url( 'https://evil.example/phish', $settings, $user )
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

	public function test_login_redirect_allows_safe_internal_redirects() {
		$service  = new ALYNT_AG_Auth_Service();
		$settings = array(
			'after_login_redirect' => '/my-account/',
			'login_path'           => '/login/',
			'account_action_base'  => '/account',
		);

		$this->assertSame(
			'https://example.test/my-account/orders/',
			$service->get_login_redirect_url( 'https://example.test/my-account/orders/', $settings )
		);
	}

	public function test_login_redirect_rejects_auth_surface_redirects() {
		$service  = new ALYNT_AG_Auth_Service();
		$settings = array(
			'after_login_redirect' => '/my-account/',
			'login_path'           => '/login/',
			'account_action_base'  => '/account',
		);

		$expected = 'https://example.test/my-account/';

		$this->assertSame(
			$expected,
			$service->get_login_redirect_url( 'https://example.test/login/', $settings )
		);
		$this->assertSame(
			$expected,
			$service->get_login_redirect_url( 'https://example.test/account?action=lostpassword', $settings )
		);
		$this->assertSame(
			$expected,
			$service->get_login_redirect_url( 'https://example.test/wp-login.php', $settings )
		);
	}
}
