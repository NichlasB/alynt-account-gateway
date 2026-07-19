<?php
/**
 * Focused structural test suite.
 *
 * @package Alynt_Account_Gateway
 */

require_once __DIR__ . '/support/class-registration-service-test-case.php';

/**
 * Tests account completion and welcome delivery.
 */
class RegistrationCompletionTest extends RegistrationServiceTestCase {

	public function test_account_created_welcome_email_sends_by_default() {
		$service = new ALYNT_AG_Registration_Service();
		$pending = (object) array(
			'email'      => 'customer@example.test',
			'first_name' => 'Damon',
			'last_name'  => 'Paulo',
		);

		$result = $service->send_account_created_welcome_email( $pending, 123, ALYNT_AG_Settings_Schema::defaults() );

		$this->assertTrue( $result );
		$this->assertCount( 1, $GLOBALS['alynt_ag_test_mail'] );
		$this->assertSame( 'customer@example.test', $GLOBALS['alynt_ag_test_mail'][0]['to'] );
		$this->assertStringContainsString( 'Welcome to Example Store', $GLOBALS['alynt_ag_test_mail'][0]['subject'] );
		$this->assertStringContainsString( 'View Account', $GLOBALS['alynt_ag_test_mail'][0]['message'] );
	}

	public function test_account_created_welcome_email_can_be_disabled() {
		$service  = new ALYNT_AG_Registration_Service();
		$pending  = (object) array(
			'email'      => 'customer@example.test',
			'first_name' => 'Damon',
			'last_name'  => 'Paulo',
		);
		$settings = array_merge(
			ALYNT_AG_Settings_Schema::defaults(),
			array( 'email_new_user_welcome_disabled' => true )
		);

		$this->assertTrue( $service->send_account_created_welcome_email( $pending, 123, $settings ) );
		$this->assertCount( 0, $GLOBALS['alynt_ag_test_mail'] );
	}

	public function test_complete_pending_registration_creates_user_and_marks_account_created() {
		$service = new class() extends ALYNT_AG_Registration_Service {
			public $welcome_calls = array();
			public $webhook_calls = array();

			public function confirm_pending_token( $token ) {
				return (object) array(
					'id'         => 77,
					'email'      => 'customer@example.test',
					'first_name' => 'Damon',
					'last_name'  => 'Paulo',
					'return_path' => '/checkout/',
					'status'     => 'email_confirmed',
				);
			}

			public function send_account_created_welcome_email( $pending, $user_id, $settings ) {
				$this->welcome_calls[] = array(
					'pending'  => $pending,
					'user_id'  => $user_id,
					'settings' => $settings,
				);

				return true;
			}

			public function dispatch_account_created_webhook( $user_id, $settings ) {
				$this->webhook_calls[] = array(
					'user_id'  => $user_id,
					'settings' => $settings,
				);

				return true;
			}
		};

		$settings = ALYNT_AG_Settings_Schema::defaults();
		$result   = $service->complete_pending_registration( 'confirmed-token', 'StrongPassword1!', 'StrongPassword1!', $settings );

		$this->assertSame( 456, $result );
		$this->assertCount( 1, $GLOBALS['alynt_ag_test_created_users'] );
		$this->assertSame( '@User_Damon_Paulo_2', $GLOBALS['alynt_ag_test_created_users'][0]['username'] );
		$this->assertSame( 'StrongPassword1!', $GLOBALS['alynt_ag_test_created_users'][0]['password'] );
		$this->assertSame( 'customer@example.test', $GLOBALS['alynt_ag_test_created_users'][0]['email'] );

		$this->assertCount( 1, $GLOBALS['alynt_ag_test_user_updates'] );
		$this->assertSame(
			array(
				'ID'           => 456,
				'first_name'   => 'Damon',
				'last_name'    => 'Paulo',
				'display_name' => 'Damon Paulo',
			),
			$GLOBALS['alynt_ag_test_user_updates'][0]
		);

		$account_created_update = null;
		$consent_update         = null;
		foreach ( $GLOBALS['alynt_ag_test_db_updates'] as $update ) {
			if ( isset( $update['data']['status'] ) && 'account_created' === $update['data']['status'] ) {
				$account_created_update = $update;
			}

			if ( isset( $update['data']['user_id'], $update['where']['context'] ) && 'registration' === $update['where']['context'] ) {
				$consent_update = $update;
			}
		}

		$this->assertNotNull( $account_created_update );
		$this->assertSame( array( 'id' => 77 ), $account_created_update['where'] );
		$this->assertSame( 456, $account_created_update['data']['user_id'] );

		$this->assertNotNull( $consent_update );
		$this->assertSame( 456, $consent_update['data']['user_id'] );
		$this->assertSame( 'customer@example.test', $consent_update['where']['email'] );

		$this->assertCount( 1, $service->welcome_calls );
		$this->assertSame( 456, $service->welcome_calls[0]['user_id'] );
		$this->assertCount( 1, $service->webhook_calls );
		$this->assertSame( 456, $service->webhook_calls[0]['user_id'] );
		$this->assertSame(
			'https://example.test/login?registration_complete=1&redirect_to=https%253A%252F%252Fexample.test%252Fcheckout%252F',
			$service->registration_complete_login_url( $settings )
		);
	}

	public function test_complete_pending_registration_logs_password_validation_failures() {
		$service = new class() extends ALYNT_AG_Registration_Service {
			public function confirm_pending_token( $token ) {
				return (object) array(
					'id'         => 77,
					'email'      => 'customer@example.test',
					'first_name' => 'Damon',
					'last_name'  => 'Paulo',
					'status'     => 'email_confirmed',
				);
			}
		};

		$result = $service->complete_pending_registration( 'confirmed-token', 'StrongPassword1!', 'DifferentPassword1!', ALYNT_AG_Settings_Schema::defaults() );

		$this->assertInstanceOf( WP_Error::class, $result );
		$this->assertSame( 'password_mismatch', $result->get_error_code() );
		$this->assertCount( 1, $GLOBALS['alynt_ag_test_db_inserts'] );
		$this->assertSame( 'registration_flow', $GLOBALS['alynt_ag_test_db_inserts'][0]['data']['provider'] );
		$this->assertSame( 'password_mismatch', $GLOBALS['alynt_ag_test_db_inserts'][0]['data']['status'] );
		$this->assertSame( 1, $GLOBALS['alynt_ag_test_db_inserts'][0]['data']['blocked'] );
	}
}
