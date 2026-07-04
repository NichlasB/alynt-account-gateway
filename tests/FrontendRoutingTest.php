<?php
/**
 * Frontend routing tests.
 *
 * @package Alynt_Account_Gateway
 */

use PHPUnit\Framework\TestCase;

/**
 * Tests frontend URL routing, bypass, and role access helpers.
 */
class FrontendRoutingTest extends TestCase {

	protected function setUp(): void {
		parent::setUp();

		$GLOBALS['alynt_ag_test_options'] = array(
			'alynt_ag_settings' => array(
				'frontend_enabled'     => true,
				'login_path'           => '/login',
				'account_action_base'  => '/account',
				'after_login_redirect' => '/my-account/',
				'emergency_bypass_key' => 'secret-bypass',
			),
		);
		$GLOBALS['alynt_ag_test_redirects'] = array();
		$GLOBALS['alynt_ag_test_throw_on_redirect'] = false;
		$GLOBALS['alynt_ag_test_user_caps'] = array();
		$GLOBALS['alynt_ag_test_user_logged_in'] = false;
		$_GET = array();
		$_SERVER['REQUEST_METHOD'] = 'GET';
	}

	protected function tearDown(): void {
		unset(
			$GLOBALS['alynt_ag_test_options'],
			$GLOBALS['alynt_ag_test_redirects'],
			$GLOBALS['alynt_ag_test_throw_on_redirect'],
			$GLOBALS['alynt_ag_test_user_caps'],
			$GLOBALS['alynt_ag_test_user_logged_in']
		);
		$_GET = array();

		parent::tearDown();
	}

	public function test_url_filters_respect_frontend_master_switch() {
		$frontend = new ALYNT_AG_Frontend();

		$this->assertSame(
			'https://example.test/login?redirect_to=https%253A%252F%252Fexample.test%252Fmy-account%252F&reauth=1',
			$frontend->filter_login_url( 'https://example.test/wp-login.php', 'https://example.test/my-account/', true )
		);
		$this->assertSame(
			'https://example.test/account?action=lostpassword',
			$frontend->filter_lostpassword_url( 'https://example.test/wp-login.php?action=lostpassword', '' )
		);
		$this->assertSame(
			'https://example.test/account?action=register',
			$frontend->filter_register_url( 'https://example.test/wp-login.php?action=register' )
		);

		$GLOBALS['alynt_ag_test_options']['alynt_ag_settings']['frontend_enabled'] = false;

		$this->assertSame(
			'https://example.test/wp-login.php',
			$frontend->filter_login_url( 'https://example.test/wp-login.php', '', false )
		);
		$this->assertSame(
			'https://example.test/wp-login.php?action=register',
			$frontend->filter_register_url( 'https://example.test/wp-login.php?action=register' )
		);
	}

	public function test_native_login_redirect_preserves_action_and_safe_query_args() {
		$frontend = new ALYNT_AG_Frontend();
		$GLOBALS['alynt_ag_test_throw_on_redirect'] = true;
		$_GET = array(
			'action'      => 'lostpassword',
			'redirect_to' => 'https://example.test/my-account/',
		);

		try {
			$frontend->maybe_redirect_native_login();
			$this->fail( 'Expected redirect exception.' );
		} catch ( RuntimeException $exception ) {
			$this->assertStringStartsWith( 'redirect:https://example.test/account?action=lostpassword', $exception->getMessage() );
		}

		$this->assertCount( 1, $GLOBALS['alynt_ag_test_redirects'] );
		$this->assertStringContainsString( 'redirect_to=https%253A%252F%252Fexample.test%252Fmy-account%252F', $GLOBALS['alynt_ag_test_redirects'][0]['location'] );
	}

	public function test_emergency_bypass_keeps_native_login_available() {
		$frontend = new ALYNT_AG_Frontend();
		$GLOBALS['alynt_ag_test_throw_on_redirect'] = true;
		$_GET = array( 'alynt_ag_bypass' => 'secret-bypass' );

		$frontend->maybe_redirect_native_login();

		$this->assertSame( array(), $GLOBALS['alynt_ag_test_redirects'] );
	}

	public function test_toolbar_is_limited_to_admins_and_shop_managers() {
		$frontend = new ALYNT_AG_Frontend();
		$GLOBALS['alynt_ag_test_user_logged_in'] = true;

		$this->assertFalse( $frontend->filter_admin_bar( true ) );

		$GLOBALS['alynt_ag_test_user_caps'] = array( 'manage_woocommerce' );
		$this->assertTrue( $frontend->filter_admin_bar( false ) );

		$GLOBALS['alynt_ag_test_user_caps'] = array( 'manage_options' );
		$this->assertTrue( $frontend->filter_admin_bar( false ) );
	}

	public function test_wp_admin_block_redirects_non_privileged_users() {
		$frontend = new ALYNT_AG_Frontend();
		$GLOBALS['alynt_ag_test_user_logged_in'] = true;
		$GLOBALS['alynt_ag_test_throw_on_redirect'] = true;

		try {
			$frontend->maybe_block_wp_admin();
			$this->fail( 'Expected redirect exception.' );
		} catch ( RuntimeException $exception ) {
			$this->assertSame( 'redirect:https://example.test/my-account/', $exception->getMessage() );
		}

		$this->assertSame( 'https://example.test/my-account/', $GLOBALS['alynt_ag_test_redirects'][0]['location'] );
	}
}
