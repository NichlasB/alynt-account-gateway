<?php
/**
 * Focused structural test suite.
 *
 * @package Alynt_Account_Gateway
 */

require_once __DIR__ . '/support/class-registration-service-test-case.php';

/**
 * Tests the pending registration lifecycle.
 */
class RegistrationPendingLifecycleTest extends RegistrationServiceTestCase {

	public function test_confirmation_token_hash_does_not_store_raw_token() {
		$service = new ALYNT_AG_Registration_Service();
		$token   = 'sample-token';
		$hash    = $service->hash_token( $token );

		$this->assertNotSame( $token, $hash );
		$this->assertTrue( $service->token_matches_hash( $token, $hash ) );
		$this->assertFalse( $service->token_matches_hash( 'different-token', $hash ) );
	}

	public function test_confirmation_url_uses_account_action_base_and_token() {
		$service = new ALYNT_AG_Registration_Service();
		$url     = $service->build_confirmation_url(
			'abc123',
			array( 'account_action_base' => '/account' )
		);

		$this->assertStringStartsWith( 'https://example.test/account?', $url );
		$this->assertStringContainsString( 'action=setpassword', $url );
		$this->assertStringContainsString( 'alynt_ag_token=abc123', $url );
	}

	public function test_pending_registration_stores_only_valid_same_site_return_path() {
		$service = new ALYNT_AG_Registration_Service();
		$result  = $service->create_pending_registration(
			'Damon',
			'Paulo',
			'checkout@example.test',
			ALYNT_AG_Settings_Schema::defaults(),
			'https://example.test/checkout/?coupon=welcome'
		);

		$this->assertIsArray( $result );
		$this->assertSame( '/checkout/?coupon=welcome', $result['return_path'] );
		$this->assertSame( '/checkout/?coupon=welcome', $GLOBALS['alynt_ag_test_db_inserts'][0]['data']['return_path'] );
	}

	public function test_pending_registration_rejects_external_return_path() {
		$service = new ALYNT_AG_Registration_Service();
		$result  = $service->create_pending_registration(
			'Damon',
			'Paulo',
			'external@example.test',
			ALYNT_AG_Settings_Schema::defaults(),
			'https://evil.example/checkout/'
		);

		$this->assertIsArray( $result );
		$this->assertSame( '', $result['return_path'] );
		$this->assertSame( '', $GLOBALS['alynt_ag_test_db_inserts'][0]['data']['return_path'] );
	}

	public function test_confirm_pending_token_rejects_invalid_or_expired_token() {
		$service = new ALYNT_AG_Registration_Service();
		$result  = $service->confirm_pending_token( 'invalid-or-expired-token' );

		$this->assertInstanceOf( WP_Error::class, $result );
		$this->assertSame( 'invalid_or_expired_token', $result->get_error_code() );
		$this->assertCount( 0, $GLOBALS['alynt_ag_test_db_updates'] );
		$this->assertCount( 1, $GLOBALS['alynt_ag_test_db_queries'] );
		$this->assertStringContainsString( $service->hash_token( 'invalid-or-expired-token' ), $GLOBALS['alynt_ag_test_db_queries'][0] );
		$this->assertStringContainsString( "status IN ('pending', 'email_confirmed')", $GLOBALS['alynt_ag_test_db_queries'][0] );
		$this->assertStringContainsString( 'expires_at >=', $GLOBALS['alynt_ag_test_db_queries'][0] );
	}

	public function test_confirm_pending_token_marks_pending_registration_confirmed() {
		$GLOBALS['alynt_ag_test_db_rows'][] = (object) array(
			'id'         => 42,
			'email'      => 'customer@example.test',
			'first_name' => 'Damon',
			'last_name'  => 'Paulo',
			'status'     => 'pending',
		);

		$service = new ALYNT_AG_Registration_Service();
		$result  = $service->confirm_pending_token( 'valid-token' );

		$this->assertSame( 'email_confirmed', $result->status );
		$this->assertNotEmpty( $result->confirmed_at );
		$this->assertCount( 1, $GLOBALS['alynt_ag_test_db_updates'] );
		$this->assertSame( 'email_confirmed', $GLOBALS['alynt_ag_test_db_updates'][0]['data']['status'] );
		$this->assertSame( array( 'id' => 42 ), $GLOBALS['alynt_ag_test_db_updates'][0]['where'] );
	}

	public function test_confirm_pending_token_reports_lookup_database_failure() {
		global $wpdb;

		$original_wpdb = $wpdb;
		$wpdb          = new class() extends ALYNT_AG_Test_WPDB {
			public $last_error = 'Database unavailable.';
		};

		try {
			$service = new ALYNT_AG_Registration_Service();
			$result  = $service->confirm_pending_token( 'valid-token' );

			$this->assertInstanceOf( WP_Error::class, $result );
			$this->assertSame( 'pending_registration_lookup_failed', $result->get_error_code() );
		} finally {
			$wpdb = $original_wpdb;
		}
	}

	public function test_confirm_pending_token_reports_update_database_failure() {
		global $wpdb;

		$original_wpdb = $wpdb;
		$wpdb          = new class() extends ALYNT_AG_Test_WPDB {
			public function update( $table, $data, $where, $format = array(), $where_format = array() ) {
				unset( $table, $data, $where, $format, $where_format );

				return false;
			}
		};
		$GLOBALS['alynt_ag_test_db_rows'][] = (object) array(
			'id'         => 42,
			'email'      => 'customer@example.test',
			'first_name' => 'Damon',
			'last_name'  => 'Paulo',
			'status'     => 'pending',
		);

		try {
			$service = new ALYNT_AG_Registration_Service();
			$result  = $service->confirm_pending_token( 'valid-token' );

			$this->assertInstanceOf( WP_Error::class, $result );
			$this->assertSame( 'pending_confirmation_failed', $result->get_error_code() );
		} finally {
			$wpdb = $original_wpdb;
		}
	}

	public function test_confirm_pending_token_does_not_update_already_confirmed_registration() {
		$pending = (object) array(
			'id'           => 43,
			'email'        => 'customer@example.test',
			'first_name'   => 'Damon',
			'last_name'    => 'Paulo',
			'status'       => 'email_confirmed',
			'confirmed_at' => '2026-07-17 10:00:00',
		);

		$GLOBALS['alynt_ag_test_db_rows'][] = $pending;

		$service = new ALYNT_AG_Registration_Service();
		$result  = $service->confirm_pending_token( 'already-confirmed-token' );

		$this->assertSame( $pending, $result );
		$this->assertCount( 0, $GLOBALS['alynt_ag_test_db_updates'] );
	}

	public function test_completed_registration_token_cannot_be_replayed() {
		$service = new ALYNT_AG_Registration_Service();
		$result  = $service->complete_pending_registration(
			'account-created-token',
			'StrongPassword1!',
			'StrongPassword1!',
			ALYNT_AG_Settings_Schema::defaults()
		);

		$this->assertInstanceOf( WP_Error::class, $result );
		$this->assertSame( 'invalid_or_expired_token', $result->get_error_code() );
		$this->assertCount( 0, $GLOBALS['alynt_ag_test_created_users'] );
		$this->assertStringContainsString( "status IN ('pending', 'email_confirmed')", $GLOBALS['alynt_ag_test_db_queries'][0] );
		$this->assertStringNotContainsString( 'account_created', $GLOBALS['alynt_ag_test_db_queries'][0] );
	}

	public function test_generated_username_uses_format_and_collision_suffix() {
		$service = new ALYNT_AG_Registration_Service();

		$username = $service->generate_username(
			'Damon',
			'Paulo',
			array( 'username_format' => '@User_{first_name}_{last_name}' )
		);

		$this->assertSame( '@User_Damon_Paulo_2', $username );
	}

	public function test_password_confirmation_must_match() {
		$service = new ALYNT_AG_Registration_Service();
		$result  = $service->validate_password_pair( 'StrongPassword1!', 'DifferentPassword1!' );

		$this->assertInstanceOf( WP_Error::class, $result );
		$this->assertSame( 'password_mismatch', $result->get_error_code() );
	}

	public function test_password_pair_accepts_valid_matching_passwords() {
		$service = new ALYNT_AG_Registration_Service();

		$this->assertTrue( $service->validate_password_pair( 'StrongPassword1!', 'StrongPassword1!' ) );
	}
}
