<?php
/**
 * Registration service tests.
 *
 * @package Alynt_Account_Gateway
 */

require_once __DIR__ . '/support/class-registration-service-test-case.php';

/**
 * Tests registration protection, policy, consent, and rate limits.
 */
class RegistrationProtectionTest extends RegistrationServiceTestCase {

	public function test_registration_protection_allows_when_no_provider_is_configured() {
		$service = new ALYNT_AG_Registration_Service();

		$this->assertTrue(
			$service->validate_registration_protection(
				'damon@example.test',
				'',
				array()
			)
		);
	}

	public function test_registration_protection_allows_when_reoon_passes() {
		$service = new ALYNT_AG_Registration_Service();

		$this->assertTrue(
			$service->validate_registration_protection(
				'damon@example.test',
				'',
				array(
					'reoon_api_key'   => 'key',
					'reoon_mode'      => 'quick',
					'protection_mode' => 'turnstile_or_reoon',
				)
			)
		);

		$tables = ALYNT_AG_Database::tables();
		$this->assertCount( 1, $GLOBALS['alynt_ag_test_db_inserts'] );
		$this->assertSame( $tables['verification_logs'], $GLOBALS['alynt_ag_test_db_inserts'][0]['table'] );
		$this->assertSame( 'damon@example.test', $GLOBALS['alynt_ag_test_db_inserts'][0]['data']['email'] );
		$this->assertSame( 'reoon', $GLOBALS['alynt_ag_test_db_inserts'][0]['data']['provider'] );
		$this->assertSame( 'safe', $GLOBALS['alynt_ag_test_db_inserts'][0]['data']['status'] );
		$this->assertSame( 0, $GLOBALS['alynt_ag_test_db_inserts'][0]['data']['blocked'] );
	}

	public function test_verification_log_records_blocked_provider_errors() {
		$service = new ALYNT_AG_Registration_Service();
		$error   = new WP_Error( 'alynt_ag_reoon_blocked', 'Blocked.' );

		$this->assertTrue( $service->log_verification_result( 'spam@example.test', 'reoon', $error ) );

		$tables = ALYNT_AG_Database::tables();
		$this->assertCount( 1, $GLOBALS['alynt_ag_test_db_inserts'] );
		$this->assertSame( $tables['verification_logs'], $GLOBALS['alynt_ag_test_db_inserts'][0]['table'] );
		$this->assertSame( 'spam@example.test', $GLOBALS['alynt_ag_test_db_inserts'][0]['data']['email'] );
		$this->assertSame( 'reoon', $GLOBALS['alynt_ag_test_db_inserts'][0]['data']['provider'] );
		$this->assertSame( 'alynt_ag_reoon_blocked', $GLOBALS['alynt_ag_test_db_inserts'][0]['data']['status'] );
		$this->assertSame( 1, $GLOBALS['alynt_ag_test_db_inserts'][0]['data']['blocked'] );
	}

	public function test_verification_log_records_flagged_reoon_statuses() {
		$service = new ALYNT_AG_Registration_Service();

		$this->assertTrue(
			$service->log_verification_result(
				'role@example.test',
				'reoon',
				array(
					'status'  => 'role_account',
					'blocked' => false,
					'flagged' => true,
				)
			)
		);

		$this->assertSame( 'role_account_flagged', $GLOBALS['alynt_ag_test_db_inserts'][0]['data']['status'] );
		$this->assertSame( 0, $GLOBALS['alynt_ag_test_db_inserts'][0]['data']['blocked'] );
	}

	public function test_reoon_flagged_policy_allows_flagged_status_by_default() {
		$service = new ALYNT_AG_Registration_Service();
		$result  = $service->apply_reoon_flagged_policy(
			array(
				'status'  => 'role_account',
				'blocked' => false,
				'flagged' => true,
			),
			array()
		);

		$this->assertIsArray( $result );
		$this->assertSame( 'role_account', $result['status'] );
	}

	public function test_reoon_flagged_policy_can_block_flagged_status() {
		$service = new ALYNT_AG_Registration_Service();
		$result  = $service->apply_reoon_flagged_policy(
			array(
				'status'  => 'role_account',
				'blocked' => false,
				'flagged' => true,
			),
			array( 'reoon_flagged_policy' => 'block' )
		);

		$this->assertInstanceOf( WP_Error::class, $result );
		$this->assertSame( 'alynt_ag_reoon_flagged_blocked', $result->get_error_code() );
	}

	public function test_registration_protection_blocks_flagged_reoon_status_when_policy_requires_it() {
		$GLOBALS['alynt_ag_test_remote_get_response'] = array(
			'body'     => '{"status":"role_account"}',
			'response' => array( 'code' => 200 ),
		);

		$service = new ALYNT_AG_Registration_Service();
		$result  = $service->validate_registration_protection(
			'role@example.test',
			'',
			array(
				'reoon_api_key'         => 'key',
				'reoon_mode'            => 'quick',
				'protection_mode'       => 'turnstile_or_reoon',
				'reoon_flagged_policy'  => 'block',
			)
		);

		$this->assertInstanceOf( WP_Error::class, $result );
		$this->assertSame( 'alynt_ag_reoon_flagged_blocked', $result->get_error_code() );
		$this->assertCount( 1, $GLOBALS['alynt_ag_test_db_inserts'] );
		$this->assertSame( 'role_account_flagged_blocked', $GLOBALS['alynt_ag_test_db_inserts'][0]['data']['status'] );
		$this->assertSame( 1, $GLOBALS['alynt_ag_test_db_inserts'][0]['data']['blocked'] );
	}

	public function test_reoon_unexpected_status_fails_closed() {
		$client = new ALYNT_AG_Reoon_Client();
		$result = $client->interpret_response( array( 'status' => 'error' ) );

		$this->assertInstanceOf( WP_Error::class, $result );
		$this->assertSame( 'alynt_ag_reoon_invalid_response', $result->get_error_code() );
	}

	public function test_reoon_non_success_http_response_fails_closed() {
		$GLOBALS['alynt_ag_test_remote_get_response'] = array(
			'body'     => '{"status":"safe"}',
			'response' => array( 'code' => 429 ),
		);

		$result = ( new ALYNT_AG_Reoon_Client() )->verify( 'damon@example.test', 'key', 'quick' );
		$this->assertInstanceOf( WP_Error::class, $result );
		$this->assertSame( 'alynt_ag_reoon_request_failed', $result->get_error_code() );
	}

	public function test_registration_flow_log_records_blocked_form_outcomes() {
		$service = new ALYNT_AG_Registration_Service();

		$this->assertTrue( $service->log_registration_flow_result( 'customer@example.test', 'terms_required' ) );

		$tables = ALYNT_AG_Database::tables();
		$this->assertCount( 1, $GLOBALS['alynt_ag_test_db_inserts'] );
		$this->assertSame( $tables['verification_logs'], $GLOBALS['alynt_ag_test_db_inserts'][0]['table'] );
		$this->assertSame( 'customer@example.test', $GLOBALS['alynt_ag_test_db_inserts'][0]['data']['email'] );
		$this->assertSame( 'registration_flow', $GLOBALS['alynt_ag_test_db_inserts'][0]['data']['provider'] );
		$this->assertSame( 'terms_required', $GLOBALS['alynt_ag_test_db_inserts'][0]['data']['status'] );
		$this->assertSame( 1, $GLOBALS['alynt_ag_test_db_inserts'][0]['data']['blocked'] );
	}

	public function test_registration_flow_log_rejects_invalid_email_identifiers() {
		$service = new ALYNT_AG_Registration_Service();

		$this->assertFalse( $service->log_registration_flow_result( 'not-an-email', 'invalid_email' ) );
		$this->assertCount( 0, $GLOBALS['alynt_ag_test_db_inserts'] );
	}

	public function test_terms_acceptance_is_required() {
		$service = new ALYNT_AG_Registration_Service();
		$result  = $service->validate_terms_acceptance( '' );

		$this->assertInstanceOf( WP_Error::class, $result );
		$this->assertSame( 'terms_required', $result->get_error_code() );
		$this->assertTrue( $service->validate_terms_acceptance( '1' ) );
	}

	public function test_registration_rate_limit_uses_configured_bucket() {
		$GLOBALS['alynt_ag_test_transients'] = array();
		$_SERVER['REMOTE_ADDR'] = '203.0.113.20';
		$service = new ALYNT_AG_Registration_Service();
		$settings = array(
			'registration_rate_limit_count' => 1,
			'registration_rate_limit_window' => 60,
		);

		$this->assertTrue( $service->validate_rate_limit( 'registration', 'damon@example.test', $settings ) );

		$result = $service->validate_rate_limit( 'registration', 'damon@example.test', $settings );

		$this->assertInstanceOf( WP_Error::class, $result );
		$this->assertSame( 'alynt_ag_rate_limited', $result->get_error_code() );
		$this->assertCount( 1, $GLOBALS['alynt_ag_test_db_inserts'] );
		$this->assertSame( 'rate_limit', $GLOBALS['alynt_ag_test_db_inserts'][0]['data']['provider'] );
		$this->assertSame( 'registration_rate_limited', $GLOBALS['alynt_ag_test_db_inserts'][0]['data']['status'] );
		$this->assertSame( 1, $GLOBALS['alynt_ag_test_db_inserts'][0]['data']['blocked'] );
	}
}
