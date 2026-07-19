<?php
/**
 * Shared email template service tests. support.
 *
 * @package Alynt_Account_Gateway
 */

use PHPUnit\Framework\TestCase;

/**
 * Tests branded email rendering.
 */

/**
 * Shared setup for email template service tests..
 */
abstract class EmailTemplateServiceTestCase extends TestCase {

	protected function setUp(): void {
		parent::setUp();
		$GLOBALS['alynt_ag_test_mail'] = array();
		$GLOBALS['alynt_ag_test_options'] = array();
		$GLOBALS['alynt_ag_test_deleted_user_meta'] = array();
		$GLOBALS['alynt_ag_test_attachment_urls'] = array();
		$_POST = array();
	}
}
