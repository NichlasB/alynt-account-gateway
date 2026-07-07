<?php
/**
 * Cleanup lifecycle tests.
 *
 * @package Alynt_Account_Gateway
 */

use PHPUnit\Framework\TestCase;

/**
 * Tests scheduled retention, deactivation, and uninstall cleanup.
 */
class CleanupLifecycleTest extends TestCase {

	protected function setUp(): void {
		parent::setUp();

		$GLOBALS['alynt_ag_test_db_queries'] = array();
		$GLOBALS['alynt_ag_test_deleted_options'] = array();
		$GLOBALS['alynt_ag_test_scheduled_hooks'] = array();
		$GLOBALS['alynt_ag_test_unscheduled_events'] = array();
		$GLOBALS['alynt_ag_test_cleared_hooks'] = array();
		$GLOBALS['alynt_ag_test_rewrite_rules_flushed'] = false;
		$GLOBALS['alynt_ag_test_options'] = array(
			'alynt_ag_settings' => array(
				'success_log_retention'      => 3,
				'failed_log_retention'       => 14,
				'verification_log_retention' => 21,
				'diagnostics_retention'      => 5,
				'consent_record_retention'   => 365,
				'audit_log_retention'        => 180,
			),
		);
	}

	protected function tearDown(): void {
		unset(
			$GLOBALS['alynt_ag_test_db_queries'],
			$GLOBALS['alynt_ag_test_deleted_options'],
			$GLOBALS['alynt_ag_test_scheduled_hooks'],
			$GLOBALS['alynt_ag_test_unscheduled_events'],
			$GLOBALS['alynt_ag_test_cleared_hooks'],
			$GLOBALS['alynt_ag_test_rewrite_rules_flushed'],
			$GLOBALS['alynt_ag_test_options']
		);

		parent::tearDown();
	}

	public function test_retention_cleanup_deletes_expired_plugin_owned_records() {
		$cleanup = new ALYNT_AG_Retention_Cleanup();
		$cleanup->run();

		$queries = implode( "\n", $GLOBALS['alynt_ag_test_db_queries'] );

		$this->assertCount( 7, $GLOBALS['alynt_ag_test_db_queries'] );
		$this->assertStringContainsString( 'DELETE FROM wp_alynt_ag_pending_registrations WHERE expires_at <', $queries );
		$this->assertStringContainsString( 'DELETE FROM wp_alynt_ag_webhook_logs WHERE success = 1', $queries );
		$this->assertStringContainsString( 'INTERVAL 3 DAY', $queries );
		$this->assertStringContainsString( 'DELETE FROM wp_alynt_ag_webhook_logs WHERE success = 0', $queries );
		$this->assertStringContainsString( 'INTERVAL 14 DAY', $queries );
		$this->assertStringContainsString( 'DELETE FROM wp_alynt_ag_verification_logs', $queries );
		$this->assertStringContainsString( 'DELETE FROM wp_alynt_ag_diagnostics_logs', $queries );
		$this->assertStringContainsString( 'DELETE FROM wp_alynt_ag_consent_records', $queries );
		$this->assertStringContainsString( 'DELETE FROM wp_alynt_ag_audit_logs', $queries );
	}

	public function test_deactivation_unschedules_retention_cleanup() {
		$GLOBALS['alynt_ag_test_scheduled_hooks']['alynt_ag_retention_cleanup'] = 123456789;

		ALYNT_AG_Deactivator::deactivate();

		$this->assertSame(
			array(
				array(
					'timestamp' => 123456789,
					'hook'      => 'alynt_ag_retention_cleanup',
				),
			),
			$GLOBALS['alynt_ag_test_unscheduled_events']
		);
		$this->assertTrue( $GLOBALS['alynt_ag_test_rewrite_rules_flushed'] );
	}

	public function test_uninstall_removes_options_tables_schedule_and_rate_limit_transients() {
		if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
			define( 'WP_UNINSTALL_PLUGIN', true );
		}

		include ALYNT_AG_PLUGIN_DIR . 'uninstall.php';

		$queries = implode( "\n", $GLOBALS['alynt_ag_test_db_queries'] );

		$this->assertContains( 'alynt_ag_settings', $GLOBALS['alynt_ag_test_deleted_options'] );
		$this->assertContains( 'alynt_ag_db_version', $GLOBALS['alynt_ag_test_deleted_options'] );
		$this->assertContains( 'alynt_ag_retention_cleanup', $GLOBALS['alynt_ag_test_cleared_hooks'] );
		$this->assertStringContainsString( 'DROP TABLE IF EXISTS wp_alynt_ag_pending_registrations', $queries );
		$this->assertStringContainsString( 'DROP TABLE IF EXISTS wp_alynt_ag_webhook_logs', $queries );
		$this->assertStringContainsString( 'DROP TABLE IF EXISTS wp_alynt_ag_verification_logs', $queries );
		$this->assertStringContainsString( 'DROP TABLE IF EXISTS wp_alynt_ag_consent_records', $queries );
		$this->assertStringContainsString( 'DROP TABLE IF EXISTS wp_alynt_ag_audit_logs', $queries );
		$this->assertStringContainsString( 'DROP TABLE IF EXISTS wp_alynt_ag_diagnostics_logs', $queries );
		$this->assertStringContainsString( 'DELETE FROM wp_options WHERE option_name LIKE', $queries );
		$this->assertStringContainsString( '\\_transient\\_alynt\\_ag\\_rl\\_', $queries );
		$this->assertStringContainsString( '\\_transient\\_timeout\\_alynt\\_ag\\_rl\\_', $queries );
		$this->assertStringContainsString( '\\_transient\\_alynt\\_ag\\_rl\\_meta\\_', $queries );
		$this->assertStringContainsString( '\\_transient\\_timeout\\_alynt\\_ag\\_rl\\_meta\\_', $queries );
	}

	public function test_uninstall_drops_the_database_registry_tables_only() {
		if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
			define( 'WP_UNINSTALL_PLUGIN', true );
		}

		include ALYNT_AG_PLUGIN_DIR . 'uninstall.php';

		$drop_queries = array_values(
			array_filter(
				$GLOBALS['alynt_ag_test_db_queries'],
				static function ( $query ) {
					return 0 === strpos( $query, 'DROP TABLE IF EXISTS ' );
				}
			)
		);

		$expected = array_map(
			static function ( $table ) {
				return 'DROP TABLE IF EXISTS ' . $table;
			},
			array_values( ALYNT_AG_Database::tables() )
		);

		$this->assertSame( $expected, $drop_queries );
	}
}
