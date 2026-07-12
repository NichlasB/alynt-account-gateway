<?php
/**
 * Privacy service tests.
 *
 * @package Alynt_Account_Gateway
 */

use PHPUnit\Framework\TestCase;

/**
 * Tests privacy exports, erasers, and consent records.
 */
class PrivacyServiceTest extends TestCase {

	protected function setUp(): void {
		parent::setUp();
		$GLOBALS['alynt_ag_test_db_inserts'] = array();
		$GLOBALS['alynt_ag_test_db_updates'] = array();
		$GLOBALS['alynt_ag_test_db_deletes'] = array();
		$GLOBALS['alynt_ag_test_db_results'] = array();
		$GLOBALS['alynt_ag_test_filters'] = array();
	}

	public function test_register_adds_exporter_and_eraser_filters() {
		$service = new ALYNT_AG_Privacy_Service();
		$service->register();

		$hooks = array_column( $GLOBALS['alynt_ag_test_filters'], 'hook' );

		$this->assertContains( 'wp_privacy_personal_data_exporters', $hooks );
		$this->assertContains( 'wp_privacy_personal_data_erasers', $hooks );
	}

	public function test_record_registration_consent_stores_paths_version_and_no_ip() {
		$service = new ALYNT_AG_Privacy_Service();
		$result  = $service->record_registration_consent(
			'Customer@Example.test',
			array(
				'terms_path'   => '/terms/',
				'privacy_path' => '/legal/privacy/',
			)
		);

		$this->assertTrue( $result );
		$this->assertCount( 1, $GLOBALS['alynt_ag_test_db_inserts'] );

		$row = $GLOBALS['alynt_ag_test_db_inserts'][0]['data'];

		$this->assertSame( 'customer@example.test', $row['email'] );
		$this->assertSame( '/terms/', $row['terms_path'] );
		$this->assertSame( '/legal/privacy/', $row['privacy_path'] );
		$this->assertSame( 'registration', $row['context'] );
		$this->assertSame( ALYNT_AG_VERSION, $row['consent_version'] );
		$this->assertArrayNotHasKey( 'ip_address', $row );
		$this->assertNotEmpty( $row['settings_hash'] );
	}

	public function test_attach_registration_consent_to_user_updates_pending_consent() {
		$service = new ALYNT_AG_Privacy_Service();

		$this->assertTrue( $service->attach_registration_consent_to_user( 'customer@example.test', 123 ) );
		$this->assertCount( 1, $GLOBALS['alynt_ag_test_db_updates'] );

		$update = $GLOBALS['alynt_ag_test_db_updates'][0];

		$this->assertSame( array( 'user_id' => 123 ), $update['data'] );
		$this->assertSame( 'customer@example.test', $update['where']['email'] );
		$this->assertSame( 0, $update['where']['user_id'] );
	}

	public function test_export_personal_data_returns_plugin_records() {
		$tables = ALYNT_AG_Database::tables();
		$GLOBALS['alynt_ag_test_db_results'][ $tables['consent_records'] ] = array(
			(object) array(
				'id'              => 1,
				'email'           => 'customer@example.test',
				'context'         => 'registration',
				'terms_path'      => '/terms/',
				'privacy_path'    => '/legal/privacy/',
				'consent_version' => '0.1.0',
				'created_at'      => '2026-07-03 12:00:00',
			),
		);
		$GLOBALS['alynt_ag_test_db_results'][ $tables['pending_registrations'] ] = array(
			(object) array(
				'id'         => 2,
				'email'      => 'customer@example.test',
				'first_name' => 'Damon',
				'last_name'  => 'Paulo',
				'status'     => 'pending',
				'created_at' => '2026-07-03 12:00:00',
				'expires_at' => '2026-07-04 12:00:00',
			),
		);
		$GLOBALS['alynt_ag_test_db_results'][ $tables['verification_logs'] ] = array(
			(object) array(
				'id'              => 3,
				'email'           => 'customer@example.test',
				'provider'        => 'reoon',
				'status'          => 'catch_all_flagged',
				'blocked'         => 0,
				'review_decision' => 'monitor',
				'reviewed_by'     => 7,
				'reviewed_at'     => '2026-07-03 12:30:00',
				'created_at'      => '2026-07-03 12:00:00',
			),
		);

		$service = new ALYNT_AG_Privacy_Service();
		$export  = $service->export_personal_data( 'customer@example.test' );

		$this->assertTrue( $export['done'] );
		$this->assertCount( 3, $export['data'] );
		$this->assertSame( 'Account Gateway Consent', $export['data'][0]['group_label'] );
		$this->assertSame( 'Pending Account Registration', $export['data'][1]['group_label'] );
		$this->assertSame( 'Email Verification Log', $export['data'][2]['group_label'] );
		$verification_values = array_column( $export['data'][2]['data'], 'value', 'name' );
		$this->assertSame( 'monitor', $verification_values['Review Decision'] );
		$this->assertSame( '2026-07-03 12:30:00', $verification_values['Reviewed At'] );
		$this->assertArrayNotHasKey( 'Reviewed By', $verification_values );
	}

	public function test_erase_personal_data_deletes_plugin_records() {
		$service = new ALYNT_AG_Privacy_Service();
		$result  = $service->erase_personal_data( 'customer@example.test' );

		$this->assertTrue( $result['done'] );
		$this->assertTrue( $result['items_removed'] );

		$tables = array_column( $GLOBALS['alynt_ag_test_db_deletes'], 'table' );

		$this->assertContains( 'wp_alynt_ag_pending_registrations', $tables );
		$this->assertContains( 'wp_alynt_ag_verification_logs', $tables );
		$this->assertContains( 'wp_alynt_ag_consent_records', $tables );
		$this->assertContains( 'wp_alynt_ag_webhook_logs', $tables );
		$this->assertContains( 'wp_alynt_ag_audit_logs', $tables );
	}
}
