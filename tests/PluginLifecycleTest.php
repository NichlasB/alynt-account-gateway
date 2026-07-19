<?php
/**
 * Plugin lifecycle tests.
 *
 * @package Alynt_Account_Gateway
 */

use PHPUnit\Framework\TestCase;

/**
 * Tests plugin-wide lifecycle hooks.
 */
class PluginLifecycleTest extends TestCase {

	protected function setUp(): void {
		parent::setUp();
		$GLOBALS['alynt_ag_test_actions'] = array();
		$GLOBALS['alynt_ag_test_filters'] = array();
	}

	public function test_database_upgrade_check_runs_on_early_init() {
		$plugin = new ALYNT_AG_Plugin();
		$plugin->run();

		$database_hooks = array_values(
			array_filter(
				$GLOBALS['alynt_ag_test_actions'],
				static function ( $hook ) {
					return isset( $hook['callback'] )
						&& array( 'ALYNT_AG_Database', 'maybe_upgrade' ) === $hook['callback'];
				}
			)
		);

		$this->assertCount( 1, $database_hooks );
		$this->assertSame( 'init', $database_hooks[0]['hook'] );
		$this->assertSame( 1, $database_hooks[0]['priority'] );
	}
}
