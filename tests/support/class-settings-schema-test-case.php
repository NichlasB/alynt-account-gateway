<?php
/**
 * Shared settings schema tests. support.
 *
 * @package Alynt_Account_Gateway
 */

use PHPUnit\Framework\TestCase;

/**
 * Tests settings defaults and policies.
 */

/**
 * Shared setup for settings schema tests..
 */
abstract class SettingsSchemaTestCase extends TestCase {

	protected function tearDown(): void {
		unset( $GLOBALS['alynt_ag_test_options']['alynt_ag_settings'] );

		parent::tearDown();
	}
}
