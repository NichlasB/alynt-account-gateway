<?php
/**
 * Activation edge-case tests.
 *
 * @package Alynt_Account_Gateway
 */

use PHPUnit\Framework\TestCase;

require_once ALYNT_AG_PLUGIN_DIR . 'includes/class-activator.php';

/**
 * Locks lifecycle failure behavior without executing WordPress activation.
 */
class ActivationEdgeCaseTest extends TestCase {

	protected function setUp(): void {
		parent::setUp();
		$GLOBALS['alynt_ag_test_options'] = array();
		$GLOBALS['alynt_ag_test_deleted_options'] = array();
		$GLOBALS['alynt_ag_test_scheduled_hooks'] = array();
		$GLOBALS['alynt_ag_test_recurring_events'] = array();
		$GLOBALS['alynt_ag_test_db_delta_statements'] = array();
		$GLOBALS['alynt_ag_test_db_queries'] = array();
		$GLOBALS['alynt_ag_test_wp_die_messages'] = array();
		$GLOBALS['alynt_ag_test_rewrite_rules_flushed'] = false;
		unset( $GLOBALS['alynt_ag_test_db_var'] );
	}

	public function test_activation_rejects_network_wide_install_before_state_changes() {
		try {
			ALYNT_AG_Activator::activate( true );
			$this->fail( 'Expected network activation to stop.' );
		} catch ( RuntimeException $exception ) {
			$this->assertStringContainsString( 'must be activated separately on each site', $exception->getMessage() );
		}

		$this->assertSame( array(), $GLOBALS['alynt_ag_test_options'] );
		$this->assertSame( array(), $GLOBALS['alynt_ag_test_db_delta_statements'] );
		$this->assertSame( array(), $GLOBALS['alynt_ag_test_recurring_events'] );
		$this->assertFalse( $GLOBALS['alynt_ag_test_rewrite_rules_flushed'] );
	}

	public function test_activation_rolls_back_new_settings_when_database_install_fails() {
		$GLOBALS['alynt_ag_test_db_var'] = null;

		try {
			ALYNT_AG_Activator::activate( false );
			$this->fail( 'Expected failed database installation to stop activation.' );
		} catch ( RuntimeException $exception ) {
			$this->assertStringContainsString( 'could not create or update its database tables', $exception->getMessage() );
		}

		$this->assertArrayNotHasKey( 'alynt_ag_settings', $GLOBALS['alynt_ag_test_options'] );
		$this->assertContains( 'alynt_ag_settings', $GLOBALS['alynt_ag_test_deleted_options'] );
		$this->assertCount( 6, $GLOBALS['alynt_ag_test_db_delta_statements'] );
		$this->assertSame( array(), $GLOBALS['alynt_ag_test_recurring_events'] );
		$this->assertFalse( $GLOBALS['alynt_ag_test_rewrite_rules_flushed'] );
	}

	public function test_single_site_activation_initializes_database_schedule_and_routes() {
		ALYNT_AG_Activator::activate( false );

		$this->assertArrayHasKey( 'alynt_ag_settings', $GLOBALS['alynt_ag_test_options'] );
		$this->assertSame( ALYNT_AG_Database::DB_VERSION, $GLOBALS['alynt_ag_test_options']['alynt_ag_db_version'] );
		$this->assertCount( 6, $GLOBALS['alynt_ag_test_db_delta_statements'] );
		$this->assertCount( 1, $GLOBALS['alynt_ag_test_recurring_events'] );
		$this->assertSame( 'daily', $GLOBALS['alynt_ag_test_recurring_events'][0]['recurrence'] );
		$this->assertSame( 'alynt_ag_retention_cleanup', $GLOBALS['alynt_ag_test_recurring_events'][0]['hook'] );
		$this->assertTrue( $GLOBALS['alynt_ag_test_rewrite_rules_flushed'] );
	}

	public function test_public_registration_serializes_pending_record_creation() {
		$source = file_get_contents( ALYNT_AG_PLUGIN_DIR . 'includes/services/class-registration-request-handler.php' );

		$this->assertIsString( $source );
		$this->assertStringContainsString( "ALYNT_AG_Operation_Lock::acquire( 'pending_registration'", $source );
		$this->assertStringContainsString( "ALYNT_AG_Operation_Lock::release( 'pending_registration'", $source );
	}
}
