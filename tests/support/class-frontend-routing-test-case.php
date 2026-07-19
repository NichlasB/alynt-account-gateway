<?php
/**
 * Shared frontend routing tests. support.
 *
 * @package Alynt_Account_Gateway
 */

use PHPUnit\Framework\TestCase;

/**
 * Tests frontend URL routing, bypass, and role access helpers.
 */

/**
 * Shared setup for frontend routing tests..
 */
abstract class FrontendRoutingTestCase extends TestCase {

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
}
