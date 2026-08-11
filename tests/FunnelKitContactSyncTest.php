<?php
/**
 * FunnelKit contact sync tests.
 *
 * @package Alynt_Account_Gateway
 */

require_once __DIR__ . '/support/class-registration-service-test-case.php';

/**
 * Tests optional FunnelKit/Autonami contact synchronization.
 */
class FunnelKitContactSyncTest extends RegistrationServiceTestCase {

	public function test_missing_funnelkit_table_is_noop() {
		$GLOBALS['alynt_ag_test_db_var'] = null;
		$sync = new ALYNT_AG_FunnelKit_Contact_Sync();

		$result = $sync->sync_registration_contact(
			(object) array(
				'email'      => 'customer@example.test',
				'first_name' => 'Damon',
				'last_name'  => 'Paulo',
			),
			456
		);

		$this->assertTrue( $result );
		$this->assertCount( 1, $GLOBALS['alynt_ag_test_db_queries'] );
		$this->assertStringContainsString( 'SHOW TABLES LIKE', $GLOBALS['alynt_ag_test_db_queries'][0] );
	}

	public function test_table_existence_check_escapes_like_pattern() {
		$sync = new ALYNT_AG_FunnelKit_Contact_Sync();

		$result = $sync->sync_registration_contact(
			(object) array(
				'email'      => 'customer@example.test',
				'first_name' => 'Damon',
				'last_name'  => 'Paulo',
			),
			456
		);

		$this->assertTrue( $result );
		$this->assertStringContainsString( "SHOW TABLES LIKE 'wp\\_bwf\\_contact'", $GLOBALS['alynt_ag_test_db_queries'][0] );
	}

	public function test_matching_email_contact_receives_names_and_user_id() {
		$sync = new ALYNT_AG_FunnelKit_Contact_Sync();

		$result = $sync->sync_registration_contact(
			(object) array(
				'email'      => 'customer@example.test',
				'first_name' => 'Damon',
				'last_name'  => 'Paulo',
			),
			456
		);

		$this->assertTrue( $result );
		$update = end( $GLOBALS['alynt_ag_test_db_queries'] );
		$this->assertStringContainsString( 'UPDATE wp_bwf_contact SET wpid = 456, f_name = \'Damon\', l_name = \'Paulo\'', $update );
		$this->assertStringContainsString( "WHERE email = 'customer@example.test' OR wpid = 456", $update );
	}

	public function test_matching_user_id_contact_receives_names_when_email_is_missing() {
		$sync = new ALYNT_AG_FunnelKit_Contact_Sync();

		$result = $sync->sync_registration_contact(
			(object) array(
				'email'      => '',
				'first_name' => 'Damon',
				'last_name'  => 'Paulo',
			),
			456
		);

		$this->assertTrue( $result );
		$update = end( $GLOBALS['alynt_ag_test_db_queries'] );
		$this->assertStringContainsString( 'UPDATE wp_bwf_contact SET wpid = 456, f_name = \'Damon\', l_name = \'Paulo\'', $update );
		$this->assertStringContainsString( 'WHERE wpid = 456', $update );
	}

	public function test_empty_pending_names_do_not_clobber_contact_names() {
		$sync = new ALYNT_AG_FunnelKit_Contact_Sync();

		$result = $sync->sync_registration_contact(
			(object) array(
				'email'      => 'customer@example.test',
				'first_name' => '',
				'last_name'  => '',
			),
			456
		);

		$this->assertTrue( $result );
		$update = end( $GLOBALS['alynt_ag_test_db_queries'] );
		$this->assertStringContainsString( 'UPDATE wp_bwf_contact SET wpid = 456', $update );
		$this->assertStringNotContainsString( 'f_name', $update );
		$this->assertStringNotContainsString( 'l_name', $update );
	}

	public function test_sync_failure_returns_error_without_throwing() {
		global $wpdb;

		$original_wpdb = $wpdb;
		$wpdb          = new class() extends ALYNT_AG_Test_WPDB {
			public $last_error = 'FunnelKit update failed.';

			public function query( $query ) {
				$GLOBALS['alynt_ag_test_db_queries'][] = $query;

				return false;
			}
		};

		try {
			$result = ( new ALYNT_AG_FunnelKit_Contact_Sync() )->sync_registration_contact(
				(object) array(
					'email'      => 'customer@example.test',
					'first_name' => 'Damon',
					'last_name'  => 'Paulo',
				),
				456
			);

			$this->assertInstanceOf( WP_Error::class, $result );
			$this->assertSame( 'funnelkit_sync_failed', $result->get_error_code() );
		} finally {
			$wpdb = $original_wpdb;
		}
	}

	public function test_backfill_copies_blank_names_from_linked_wordpress_user_meta() {
		$GLOBALS['alynt_ag_test_db_results']['wp_bwf_contact'] = array(
			(object) array(
				'id'     => 12,
				'wpid'   => 456,
				'f_name' => '',
				'l_name' => '',
			),
		);

		$stats = ( new ALYNT_AG_FunnelKit_Contact_Sync() )->backfill_linked_contacts();

		$this->assertSame(
			array(
				'table_exists' => true,
				'inspected'    => 1,
				'updated'      => 1,
				'skipped'      => 0,
				'failed'       => 0,
			),
			$stats
		);
		$this->assertSame( 'wp_bwf_contact', $GLOBALS['alynt_ag_test_db_updates'][0]['table'] );
		$this->assertSame( array( 'f_name' => 'Damon', 'l_name' => 'Paulo' ), $GLOBALS['alynt_ag_test_db_updates'][0]['data'] );
		$this->assertSame( array( 'id' => 12 ), $GLOBALS['alynt_ag_test_db_updates'][0]['where'] );
	}

	public function test_backfill_includes_null_name_rows() {
		global $wpdb;

		$original_wpdb = $wpdb;
		$wpdb          = new class() extends ALYNT_AG_Test_WPDB {
			public function get_results( $query ) {
				$GLOBALS['alynt_ag_test_db_queries'][] = $query;

				return array();
			}
		};

		try {
			$stats = ( new ALYNT_AG_FunnelKit_Contact_Sync() )->backfill_linked_contacts();

			$this->assertSame( 0, $stats['inspected'] );
			$select = end( $GLOBALS['alynt_ag_test_db_queries'] );
			$this->assertStringContainsString( "f_name IS NULL OR f_name = '' OR l_name IS NULL OR l_name = ''", $select );
		} finally {
			$wpdb = $original_wpdb;
		}
	}

	public function test_backfill_copies_null_names_from_linked_wordpress_user_meta() {
		$GLOBALS['alynt_ag_test_db_results']['wp_bwf_contact'] = array(
			(object) array(
				'id'     => 12,
				'wpid'   => 456,
				'f_name' => null,
				'l_name' => null,
			),
		);

		$stats = ( new ALYNT_AG_FunnelKit_Contact_Sync() )->backfill_linked_contacts();

		$this->assertSame( 1, $stats['updated'] );
		$this->assertSame( array( 'f_name' => 'Damon', 'l_name' => 'Paulo' ), $GLOBALS['alynt_ag_test_db_updates'][0]['data'] );
	}

	public function test_backfill_does_not_clobber_existing_non_empty_names() {
		$GLOBALS['alynt_ag_test_db_results']['wp_bwf_contact'] = array(
			(object) array(
				'id'     => 12,
				'wpid'   => 456,
				'f_name' => 'Existing',
				'l_name' => '',
			),
		);

		$stats = ( new ALYNT_AG_FunnelKit_Contact_Sync() )->backfill_linked_contacts();

		$this->assertSame( 1, $stats['updated'] );
		$this->assertSame( array( 'l_name' => 'Paulo' ), $GLOBALS['alynt_ag_test_db_updates'][0]['data'] );
	}
}
