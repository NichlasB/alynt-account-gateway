<?php
/**
 * Registration completion role assignment tests.
 *
 * @package Alynt_Account_Gateway
 */

require_once __DIR__ . '/support/class-registration-service-test-case.php';

/**
 * Tests safe role assignment for completed registrations.
 */
class RegistrationCompletionRoleTest extends RegistrationServiceTestCase {

	public function test_complete_pending_registration_uses_configured_safe_role() {
		$service  = new class() extends ALYNT_AG_Registration_Service {
			public function confirm_pending_token( $token ) {
				unset( $token );

				return (object) array(
					'id'         => 77,
					'email'      => 'subscriber@example.test',
					'first_name' => 'Sub',
					'last_name'  => 'Scriber',
					'status'     => 'email_confirmed',
				);
			}
		};
		$settings = array_merge(
			ALYNT_AG_Settings_Schema::defaults(),
			array( 'registration_user_role' => 'subscriber' )
		);

		$result = $service->complete_pending_registration( 'confirmed-token', 'StrongPassword1!', 'StrongPassword1!', $settings );

		$this->assertSame( 456, $result );
		$this->assertSame( 'subscriber', $GLOBALS['alynt_ag_test_user_updates'][0]['role'] );
	}

	public function test_complete_pending_registration_falls_back_when_customer_role_is_missing() {
		$GLOBALS['alynt_ag_test_roles'] = array(
			'subscriber' => array( 'read' => true ),
		);

		$service = new class() extends ALYNT_AG_Registration_Service {
			public function confirm_pending_token( $token ) {
				unset( $token );

				return (object) array(
					'id'         => 77,
					'email'      => 'fallback@example.test',
					'first_name' => 'Fallback',
					'last_name'  => 'User',
					'status'     => 'email_confirmed',
				);
			}
		};

		$result = $service->complete_pending_registration( 'confirmed-token', 'StrongPassword1!', 'StrongPassword1!', ALYNT_AG_Settings_Schema::defaults() );

		$this->assertSame( 456, $result );
		$this->assertSame( 'subscriber', $GLOBALS['alynt_ag_test_user_updates'][0]['role'] );
	}

	public function test_complete_pending_registration_rejects_unsafe_configured_role() {
		$GLOBALS['alynt_ag_test_roles'] = array(
			'customer'      => array( 'read' => true ),
			'subscriber'    => array( 'read' => true ),
			'administrator' => array(
				'read'           => true,
				'manage_options' => true,
			),
		);

		$service  = new class() extends ALYNT_AG_Registration_Service {
			public function confirm_pending_token( $token ) {
				unset( $token );

				return (object) array(
					'id'         => 77,
					'email'      => 'unsafe@example.test',
					'first_name' => 'Unsafe',
					'last_name'  => 'Role',
					'status'     => 'email_confirmed',
				);
			}
		};
		$settings = array_merge(
			ALYNT_AG_Settings_Schema::defaults(),
			array( 'registration_user_role' => 'administrator' )
		);

		$result = $service->complete_pending_registration( 'confirmed-token', 'StrongPassword1!', 'StrongPassword1!', $settings );

		$this->assertSame( 456, $result );
		$this->assertSame( 'customer', $GLOBALS['alynt_ag_test_user_updates'][0]['role'] );
	}

	public function test_complete_pending_registration_rolls_back_when_no_safe_role_is_available() {
		$GLOBALS['alynt_ag_test_roles'] = array(
			'customer'   => array(
				'read'           => true,
				'manage_options' => true,
			),
			'subscriber' => array(
				'read'       => true,
				'edit_posts' => true,
			),
		);

		$service = new class() extends ALYNT_AG_Registration_Service {
			public function confirm_pending_token( $token ) {
				unset( $token );

				return (object) array(
					'id'         => 77,
					'email'      => 'blocked@example.test',
					'first_name' => 'Blocked',
					'last_name'  => 'Role',
					'status'     => 'email_confirmed',
				);
			}
		};

		$result = $service->complete_pending_registration( 'confirmed-token', 'StrongPassword1!', 'StrongPassword1!', ALYNT_AG_Settings_Schema::defaults() );

		$this->assertInstanceOf( WP_Error::class, $result );
		$this->assertSame( 'registration_role_unavailable', $result->get_error_code() );
		$this->assertSame( array( 456 ), $GLOBALS['alynt_ag_test_deleted_users'] );
		$this->assertCount( 0, $GLOBALS['alynt_ag_test_user_updates'] );
	}
}