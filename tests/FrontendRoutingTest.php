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
		$GLOBALS['alynt_ag_test_db_inserts'] = array();
		$GLOBALS['alynt_ag_test_throw_on_redirect'] = false;
		$GLOBALS['alynt_ag_test_user_caps'] = array();
		$GLOBALS['alynt_ag_test_user_logged_in'] = false;
		$_GET = array();
		$_SERVER['REQUEST_METHOD'] = 'GET';
		$_SERVER['REQUEST_URI'] = '/wp-admin/';
	}

	protected function tearDown(): void {
		unset(
			$GLOBALS['alynt_ag_test_options'],
			$GLOBALS['alynt_ag_test_redirects'],
			$GLOBALS['alynt_ag_test_db_inserts'],
			$GLOBALS['alynt_ag_test_throw_on_redirect'],
			$GLOBALS['alynt_ag_test_user_caps'],
			$GLOBALS['alynt_ag_test_user_logged_in']
		);
		$_GET = array();
		unset( $_SERVER['REQUEST_URI'] );

		parent::tearDown();
	}

	public function test_gateway_render_hook_runs_before_canonical_redirects() {
		$GLOBALS['alynt_ag_test_actions'] = array();

		$frontend = new ALYNT_AG_Frontend();
		$frontend->register();

		$gateway_hooks = array_values(
			array_filter(
				$GLOBALS['alynt_ag_test_actions'],
				static function ( $hook ) {
					return 'template_redirect' === $hook['hook']
						&& is_array( $hook['callback'] )
						&& 'maybe_render_gateway' === $hook['callback'][1];
				}
			)
		);

		$this->assertCount( 1, $gateway_hooks );
		$this->assertSame( 1, $gateway_hooks[0]['priority'] );
	}

	public function test_auth_and_registration_post_handlers_run_before_gateway_render() {
		$GLOBALS['alynt_ag_test_actions'] = array();

		$auth         = new ALYNT_AG_Auth_Service();
		$registration = new ALYNT_AG_Registration_Service();
		$frontend     = new ALYNT_AG_Frontend();

		$auth->register();
		$registration->register();
		$frontend->register();

		$priorities = array();
		foreach ( $GLOBALS['alynt_ag_test_actions'] as $hook ) {
			if ( 'template_redirect' !== $hook['hook'] || ! is_array( $hook['callback'] ) ) {
				continue;
			}

			$priorities[ $hook['callback'][1] ] = $hook['priority'];
		}

		$this->assertSame( 0, $priorities['maybe_handle_auth_request'] );
		$this->assertSame( 0, $priorities['maybe_handle_registration_request'] );
		$this->assertSame( 1, $priorities['maybe_render_gateway'] );
		$this->assertLessThan( $priorities['maybe_render_gateway'], $priorities['maybe_handle_auth_request'] );
		$this->assertLessThan( $priorities['maybe_render_gateway'], $priorities['maybe_handle_registration_request'] );
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

	public function test_preview_assets_bypass_disabled_output_without_changing_public_enqueue() {
		$frontend = new ALYNT_AG_Frontend();
		$settings = $GLOBALS['alynt_ag_test_options']['alynt_ag_settings'];

		$GLOBALS['alynt_ag_test_options']['alynt_ag_settings']['frontend_enabled'] = false;
		$GLOBALS['alynt_ag_test_enqueued_styles']                                 = array();
		$GLOBALS['alynt_ag_test_enqueued_scripts']                                = array();
		$_SERVER['REQUEST_URI']                                                    = '/login';

		$frontend->enqueue_assets();

		$this->assertSame( array(), $GLOBALS['alynt_ag_test_enqueued_styles'] );
		$this->assertSame( array(), $GLOBALS['alynt_ag_test_enqueued_scripts'] );

		$settings['frontend_enabled'] = false;
		$frontend->enqueue_preview_assets( $settings, 'login' );

		$this->assertSame(
			array( 'alynt-ag-frontend' ),
			array_column( $GLOBALS['alynt_ag_test_enqueued_styles'], 'handle' )
		);
		$this->assertSame(
			array( 'alynt-ag-frontend' ),
			array_column( $GLOBALS['alynt_ag_test_enqueued_scripts'], 'handle' )
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

	public function test_native_login_redirect_logs_diagnostics_without_query_values() {
		$frontend = new ALYNT_AG_Frontend();
		$GLOBALS['alynt_ag_test_options']['alynt_ag_settings']['diagnostics_enabled'] = true;
		$GLOBALS['alynt_ag_test_options']['alynt_ag_settings']['diagnostics_min_level'] = 'debug';
		$GLOBALS['alynt_ag_test_throw_on_redirect'] = true;
		$_GET = array(
			'action'      => 'lostpassword',
			'login'       => 'damon@example.test',
			'redirect_to' => 'https://example.test/my-account/',
		);

		try {
			$frontend->maybe_redirect_native_login();
			$this->fail( 'Expected redirect exception.' );
		} catch ( RuntimeException $exception ) {
			$this->assertStringStartsWith( 'redirect:https://example.test/account?action=lostpassword', $exception->getMessage() );
		}

		$tables = ALYNT_AG_Database::tables();
		$this->assertCount( 1, $GLOBALS['alynt_ag_test_db_inserts'] );
		$this->assertSame( $tables['diagnostics_logs'], $GLOBALS['alynt_ag_test_db_inserts'][0]['table'] );

		$row     = $GLOBALS['alynt_ag_test_db_inserts'][0]['data'];
		$context = json_decode( $row['context'], true );

		$this->assertSame( 'warning', $row['level'] );
		$this->assertSame( 'security', $row['category'] );
		$this->assertSame( 'native_login_redirected', $row['event_code'] );
		$this->assertSame( 'lostpassword', $context['action'] );
		$this->assertSame( '/account', $context['destination_path'] );
		$this->assertSame( array( 'login', 'redirect_to' ), $context['preserved_query_keys'] );
		$this->assertSame( 'GET', $context['request_method'] );
		$this->assertStringNotContainsString( 'damon@example.test', $row['context'] );
		$this->assertStringNotContainsString( 'my-account', $row['context'] );
	}

	public function test_emergency_bypass_keeps_native_login_available() {
		$frontend = new ALYNT_AG_Frontend();
		$GLOBALS['alynt_ag_test_throw_on_redirect'] = true;
		$_GET = array( 'alynt_ag_bypass' => 'secret-bypass' );

		$frontend->maybe_redirect_native_login();

		$this->assertSame( array(), $GLOBALS['alynt_ag_test_redirects'] );
	}

	public function test_force_login_bypass_allows_gateway_routes_only_when_frontend_enabled() {
		$frontend = new ALYNT_AG_Frontend();

		$this->assertTrue( $frontend->filter_force_login_bypass( false, 'https://example.test/login/' ) );
		$this->assertTrue( $frontend->filter_force_login_bypass( false, 'https://example.test/account?action=lostpassword' ) );
		$this->assertFalse( $frontend->filter_force_login_bypass( false, 'https://example.test/legal/terms/' ) );
		$this->assertFalse( $frontend->filter_force_login_bypass( false, 'https://example.test/my-account/' ) );

		$GLOBALS['alynt_ag_test_options']['alynt_ag_settings']['frontend_enabled'] = false;

		$this->assertFalse( $frontend->filter_force_login_bypass( false, 'https://example.test/login/' ) );
		$this->assertFalse( $frontend->filter_force_login_bypass( false, 'https://example.test/account?action=lostpassword' ) );
	}

	public function test_force_login_bypass_preserves_existing_bypass_decision() {
		$frontend = new ALYNT_AG_Frontend();
		$GLOBALS['alynt_ag_test_options']['alynt_ag_settings']['frontend_enabled'] = false;

		$this->assertTrue( $frontend->filter_force_login_bypass( true, 'https://example.test/legal/terms/' ) );
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

	public function test_toolbar_filter_respects_frontend_master_switch() {
		$frontend = new ALYNT_AG_Frontend();
		$GLOBALS['alynt_ag_test_options']['alynt_ag_settings']['frontend_enabled'] = false;
		$GLOBALS['alynt_ag_test_user_logged_in'] = true;

		$this->assertTrue( $frontend->filter_admin_bar( true ) );
		$this->assertFalse( $frontend->filter_admin_bar( false ) );
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

	public function test_wp_admin_block_respects_frontend_master_switch() {
		$frontend = new ALYNT_AG_Frontend();
		$GLOBALS['alynt_ag_test_options']['alynt_ag_settings']['frontend_enabled'] = false;
		$GLOBALS['alynt_ag_test_user_logged_in'] = true;
		$GLOBALS['alynt_ag_test_throw_on_redirect'] = true;

		$frontend->maybe_block_wp_admin();

		$this->assertSame( array(), $GLOBALS['alynt_ag_test_redirects'] );
	}

	public function test_wp_admin_block_logs_diagnostics_when_enabled() {
		$frontend = new ALYNT_AG_Frontend();
		$GLOBALS['alynt_ag_test_options']['alynt_ag_settings']['diagnostics_enabled'] = true;
		$GLOBALS['alynt_ag_test_options']['alynt_ag_settings']['diagnostics_min_level'] = 'debug';
		$GLOBALS['alynt_ag_test_user_logged_in'] = true;
		$GLOBALS['alynt_ag_test_throw_on_redirect'] = true;
		$_GET = array(
			'page'        => 'orders',
			'redirect_to' => 'https://evil.example/path',
		);
		$_SERVER['REQUEST_URI'] = '/wp-admin/admin.php?page=orders&redirect_to=https%3A%2F%2Fevil.example%2Fpath';

		try {
			$frontend->maybe_block_wp_admin();
			$this->fail( 'Expected redirect exception.' );
		} catch ( RuntimeException $exception ) {
			$this->assertSame( 'redirect:https://example.test/my-account/', $exception->getMessage() );
		}

		$tables = ALYNT_AG_Database::tables();
		$this->assertCount( 1, $GLOBALS['alynt_ag_test_db_inserts'] );
		$this->assertSame( $tables['diagnostics_logs'], $GLOBALS['alynt_ag_test_db_inserts'][0]['table'] );

		$row     = $GLOBALS['alynt_ag_test_db_inserts'][0]['data'];
		$context = json_decode( $row['context'], true );

		$this->assertSame( 'warning', $row['level'] );
		$this->assertSame( 'security', $row['category'] );
		$this->assertSame( 'wp_admin_access_blocked', $row['event_code'] );
		$this->assertSame( '/my-account/', $context['destination_path'] );
		$this->assertSame( 0, $context['user_id'] );
		$this->assertSame( '/wp-admin/admin.php', $context['request_path'] );
		$this->assertSame( 'GET', $context['request_method'] );
		$this->assertSame( array( 'page', 'redirect_to' ), $context['request_query_keys'] );
		$this->assertStringNotContainsString( 'orders', $row['context'] );
		$this->assertStringNotContainsString( 'evil.example', $row['context'] );
	}
}
