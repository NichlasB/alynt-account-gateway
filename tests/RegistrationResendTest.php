<?php
/**
 * Focused structural test suite.
 *
 * @package Alynt_Account_Gateway
 */

require_once __DIR__ . '/support/class-registration-service-test-case.php';

/**
 * Tests confirmation resend behavior.
 */
class RegistrationResendTest extends RegistrationServiceTestCase {

	public function test_resend_confirmation_rate_limit_uses_configured_bucket() {
		$GLOBALS['alynt_ag_test_transients'] = array();
		$_SERVER['REMOTE_ADDR'] = '203.0.113.21';
		$service = new ALYNT_AG_Registration_Service();
		$settings = array(
			'resend_confirmation_rate_limit_count' => 1,
			'resend_confirmation_rate_limit_window' => 60,
		);

		$this->assertTrue( $service->validate_rate_limit( 'resend_confirmation', 'damon@example.test', $settings ) );

		$result = $service->validate_rate_limit( 'resend_confirmation', 'damon@example.test', $settings );

		$this->assertInstanceOf( WP_Error::class, $result );
		$this->assertSame( 'alynt_ag_rate_limited', $result->get_error_code() );
	}

	public function test_resend_confirmation_rejects_invalid_email_before_lookup() {
		$service = new ALYNT_AG_Registration_Service();
		$result  = $service->resend_confirmation( 'not-an-email', array() );

		$this->assertInstanceOf( WP_Error::class, $result );
		$this->assertSame( 'invalid_email', $result->get_error_code() );
	}

	public function test_resend_confirmation_is_neutral_when_no_pending_registration_exists() {
		$service = new class() extends ALYNT_AG_Registration_Service {
			public function find_resendable_pending_by_email( $email ) {
				return null;
			}
		};

		$this->assertTrue( $service->resend_confirmation( 'missing@example.test', array() ) );
	}

	public function test_resend_confirmation_logs_success_when_pending_registration_is_renewed() {
		$service = new class() extends ALYNT_AG_Registration_Service {
			public function find_resendable_pending_by_email( $email ) {
				return (object) array(
					'id'         => 44,
					'email'      => $email,
					'first_name' => 'Damon',
					'last_name'  => 'Paulo',
				);
			}
		};

		$settings = ALYNT_AG_Settings_Schema::defaults();

		$this->assertTrue( $service->resend_confirmation( 'pending@example.test', $settings ) );
		$this->assertNotEmpty( $GLOBALS['alynt_ag_test_mail'] );
		$this->assertCount( 1, $GLOBALS['alynt_ag_test_db_inserts'] );
		$this->assertSame( 'registration_flow', $GLOBALS['alynt_ag_test_db_inserts'][0]['data']['provider'] );
		$this->assertSame( 'confirmation_resent', $GLOBALS['alynt_ag_test_db_inserts'][0]['data']['status'] );
		$this->assertSame( 0, $GLOBALS['alynt_ag_test_db_inserts'][0]['data']['blocked'] );
	}
}
