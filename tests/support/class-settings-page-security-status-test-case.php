<?php
/**
 * Shared settings-page security status test support.
 *
 * @package Alynt_Account_Gateway
 */

use PHPUnit\Framework\TestCase;

require_once ALYNT_AG_PLUGIN_DIR . 'admin/class-settings-page.php';

/**
 * Tests security and spam status guidance on the settings page.
 */

/**
 * Shared setup for settings-page security status tests.
 */
abstract class SettingsPageSecurityStatusTestCase extends TestCase {

	protected function setUp(): void {
		parent::setUp();
		$GLOBALS['alynt_ag_test_db_results'] = array();
		$GLOBALS['alynt_ag_test_db_updates'] = array();
		$GLOBALS['alynt_ag_test_db_inserts'] = array();
		$GLOBALS['alynt_ag_test_redirects']  = array();
		unset( $GLOBALS['alynt_ag_test_options']['alynt_ag_settings'] );
	}

	protected function tearDown(): void {
		$GLOBALS['alynt_ag_test_db_updates'] = array();
		$GLOBALS['alynt_ag_test_db_inserts'] = array();
		$GLOBALS['alynt_ag_test_redirects']  = array();
		unset(
			$GLOBALS['alynt_ag_test_options']['date_format'],
			$GLOBALS['alynt_ag_test_options']['time_format'],
			$GLOBALS['alynt_ag_test_user_caps'],
			$GLOBALS['alynt_ag_test_current_user_id'],
			$GLOBALS['alynt_ag_test_throw_on_redirect']
		);
		$_POST = array();

		parent::tearDown();
	}

	protected function invoke_helper( $settings_page, $method, $args = array() ) {
		return alynt_ag_test_invoke_settings_page_method( $settings_page, $method, $args );
	}
}
