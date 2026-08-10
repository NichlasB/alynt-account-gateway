<?php
/**
 * Registration integration delivery tests.
 *
 * @package Alynt_Account_Gateway
 */

require_once __DIR__ . '/support/class-registration-service-test-case.php';

/**
 * Tests non-blocking registration integration delivery.
 */
class RegistrationIntegrationDeliveryTest extends RegistrationServiceTestCase {

	public function test_funnelkit_sync_failure_does_not_prevent_account_creation() {
		$service = new class() extends ALYNT_AG_Registration_Service {
			public $welcome_calls = array();
			public $webhook_calls = array();

			public function confirm_pending_token( $token ) {
				unset( $token );

				return (object) array(
					'id'         => 77,
					'email'      => 'customer@example.test',
					'first_name' => 'Damon',
					'last_name'  => 'Paulo',
					'return_path' => '',
					'status'     => 'email_confirmed',
				);
			}

			public function sync_funnelkit_registration_contact( $pending, $user_id ) {
				unset( $pending, $user_id );

				return new WP_Error( 'funnelkit_sync_failed', 'Nope.' );
			}

			public function send_account_created_welcome_email( $pending, $user_id, $settings ) {
				$this->welcome_calls[] = compact( 'pending', 'user_id', 'settings' );

				return true;
			}

			public function dispatch_account_created_webhook( $user_id, $settings ) {
				$this->webhook_calls[] = compact( 'user_id', 'settings' );

				return true;
			}
		};

		$settings                                      = ALYNT_AG_Settings_Schema::defaults();
		$settings['diagnostics_enabled']               = true;
		$settings['diagnostics_min_level']             = 'debug';
		$GLOBALS['alynt_ag_test_options']['alynt_ag_settings'] = $settings;

		$result = $service->complete_pending_registration( 'confirmed-token', 'StrongPassword1!', 'StrongPassword1!', $settings );

		$this->assertSame( 456, $result );
		$this->assertCount( 1, $service->welcome_calls );
		$this->assertCount( 1, $service->webhook_calls );

		$logged_failure = null;
		foreach ( $GLOBALS['alynt_ag_test_db_inserts'] as $insert ) {
			if ( isset( $insert['data']['event_code'] ) && 'funnelkit_contact_sync_failed' === $insert['data']['event_code'] ) {
				$logged_failure = $insert;
				break;
			}
		}

		$this->assertNotNull( $logged_failure );
		$this->assertSame( 'warning', $logged_failure['data']['level'] );
	}
}
