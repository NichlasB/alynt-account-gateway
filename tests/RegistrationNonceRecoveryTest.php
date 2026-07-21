<?php
/**
 * Registration nonce recovery tests.
 *
 * @package Alynt_Account_Gateway
 */

require_once __DIR__ . '/support/class-registration-service-test-case.php';

/**
 * Tests expired registration requests return to the branded screen.
 */
class RegistrationNonceRecoveryTest extends RegistrationServiceTestCase {

	public function test_expired_registration_nonce_returns_to_branded_registration_screen() {
		$service = new ALYNT_AG_Registration_Service();
		$GLOBALS['alynt_ag_test_throw_on_redirect'] = true;
		$GLOBALS['alynt_ag_test_nonce_valid']       = false;
		$_SERVER['REQUEST_METHOD']                  = 'POST';
		$_POST = array(
			'alynt_ag_action'             => 'start_registration',
			'alynt_ag_registration_nonce' => 'expired',
			'first_name'                  => 'Damon',
			'last_name'                   => 'Paulo',
			'email'                       => 'damon@example.test',
		);

		try {
			$service->maybe_handle_registration_request();
			$this->fail( 'Expected redirect exception.' );
		} catch ( RuntimeException $exception ) {
			$this->assertSame( 'redirect:https://example.test/account?action=register&registration_error=session_expired', $exception->getMessage() );
		}

		$this->assertCount( 0, $GLOBALS['alynt_ag_test_db_inserts'] );
	}
}
