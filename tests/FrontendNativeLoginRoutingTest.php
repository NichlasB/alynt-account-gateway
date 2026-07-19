<?php
/**
 * Frontend routing tests.
 *
 * @package Alynt_Account_Gateway
 */

require_once __DIR__ . '/support/class-frontend-routing-test-case.php';

/**
 * Tests native-login redirects and bypass behavior.
 */
class FrontendNativeLoginRoutingTest extends FrontendRoutingTestCase {

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
}
