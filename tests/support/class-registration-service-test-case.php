<?php
/**
 * Shared registration service tests. support.
 *
 * @package Alynt_Account_Gateway
 */

use PHPUnit\Framework\TestCase;

/**
 * Tests pending registration helpers.
 */

/**
 * Shared setup for registration service tests..
 */
abstract class RegistrationServiceTestCase extends TestCase {

	protected function setUp(): void {
		parent::setUp();
		$GLOBALS['alynt_ag_test_mail'] = array();
		$GLOBALS['alynt_ag_test_options'] = array();
		$GLOBALS['alynt_ag_test_transients'] = array();
		$GLOBALS['alynt_ag_test_db_inserts'] = array();
		$GLOBALS['alynt_ag_test_created_users'] = array();
		$GLOBALS['alynt_ag_test_user_updates'] = array();
		$GLOBALS['alynt_ag_test_deleted_users'] = array();
		$GLOBALS['alynt_ag_test_db_updates'] = array();
		$GLOBALS['alynt_ag_test_db_rows'] = array();
		$GLOBALS['alynt_ag_test_db_queries'] = array();
		unset( $GLOBALS['alynt_ag_test_user_update_result'], $GLOBALS['alynt_ag_test_user_delete_result'] );
		unset( $GLOBALS['alynt_ag_test_remote_get_response'] );
	}
}
