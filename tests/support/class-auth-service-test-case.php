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

/**
 * Shared setup for authentication service tests..
 */
abstract class AuthServiceTestCase extends TestCase {

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
			$GLOBALS['alynt_ag_test_signon_roles'],
			$GLOBALS['alynt_ag_test_options']['alynt_ag_settings']
		);
		$_SERVER['REMOTE_ADDR'] = '203.0.113.30';
		$_SERVER['REQUEST_METHOD'] = 'GET';
		$_POST = array();
	}
}
