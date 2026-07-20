<?php
/**
 * Registration and authentication collaboration tests.
 *
 * @package Alynt_Account_Gateway
 */

require_once __DIR__ . '/support/class-registration-service-test-case.php';

/**
 * Records facade delegation calls.
 */
class ALYNT_AG_Test_Service_Collaborator_Spy {

	/**
	 * Recorded calls.
	 *
	 * @var array<int,array<string,mixed>>
	 */
	public $calls = array();

	/**
	 * Handle a collaborator call.
	 *
	 * @param string           $name      Method name.
	 * @param array<int,mixed> $arguments Method arguments.
	 * @return string
	 */
	public function __call( $name, $arguments ) {
		$this->calls[] = array(
			'method'    => $name,
			'arguments' => $arguments,
		);

		return $name . '-result';
	}
}

/**
 * Locks public facade contracts and extracted collaborator boundaries.
 */
class RegistrationAuthCollaborationTest extends RegistrationServiceTestCase {

	/**
	 * Confirm registration retains its established public API.
	 */
	public function test_registration_facade_retains_public_api() {
		$expected = array(
			'__construct',
			'register',
			'maybe_handle_registration_request',
			'create_pending_registration',
			'validate_registration_protection',
			'log_verification_result',
			'log_registration_flow_result',
			'apply_reoon_flagged_policy',
			'validate_terms_acceptance',
			'validate_rate_limit',
			'send_confirmation_email',
			'find_pending_by_token',
			'find_resendable_pending_by_email',
			'renew_pending_confirmation',
			'resend_confirmation',
			'confirm_pending_token',
			'complete_pending_registration',
			'send_account_created_welcome_email',
			'dispatch_account_created_webhook',
			'validate_password_pair',
			'generate_username',
			'registration_complete_login_url',
			'generate_confirmation_token',
			'hash_token',
			'token_matches_hash',
			'build_confirmation_url',
			'validate_password',
		);
		$actual   = array_map(
			static function ( $method ) {
				return $method->getName();
			},
			( new ReflectionClass( ALYNT_AG_Registration_Service::class ) )->getMethods( ReflectionMethod::IS_PUBLIC )
		);

		$this->assertSame( $expected, $actual );
	}

	/**
	 * Confirm authentication retains its established public API.
	 */
	public function test_auth_facade_retains_public_api() {
		$expected = array(
			'__construct',
			'register',
			'maybe_handle_auth_request',
			'validate_rate_limit',
			'log_rate_limit_result',
			'log_auth_event',
			'get_login_error_message',
			'get_lostpassword_error_message',
			'get_lostpassword_sent_message',
			'validate_password_reset_key',
			'complete_password_reset',
			'get_login_redirect_url',
		);
		$actual   = array_map(
			static function ( $method ) {
				return $method->getName();
			},
			( new ReflectionClass( ALYNT_AG_Auth_Service::class ) )->getMethods( ReflectionMethod::IS_PUBLIC )
		);

		$this->assertSame( $expected, $actual );
	}

	/**
	 * Preserve the original constructor dependency and optional collaborator seam.
	 */
	public function test_facade_constructors_keep_destinations_first_and_optional_collaborators_second() {
		foreach ( array( ALYNT_AG_Registration_Service::class, ALYNT_AG_Auth_Service::class ) as $class_name ) {
			$parameters = ( new ReflectionMethod( $class_name, '__construct' ) )->getParameters();

			$this->assertCount( 2, $parameters );
			$this->assertSame( 'destinations', $parameters[0]->getName() );
			$this->assertTrue( $parameters[0]->isOptional() );
			$this->assertSame( 'collaborators', $parameters[1]->getName() );
			$this->assertTrue( $parameters[1]->isOptional() );
			$this->assertSame( array(), $parameters[1]->getDefaultValue() );
		}
	}

	/**
	 * Confirm each registration concern delegates through its injected collaborator.
	 */
	public function test_registration_facade_delegates_each_concern() {
		$keys  = array( 'request', 'protection', 'activity', 'pending', 'confirmation', 'completion', 'delivery', 'credentials' );
		$spies = array();

		foreach ( $keys as $key ) {
			$spies[ $key ] = new ALYNT_AG_Test_Service_Collaborator_Spy();
		}

		$service = new ALYNT_AG_Registration_Service( null, $spies );
		$service->maybe_handle_registration_request();
		$this->assertSame( 'run_validate_terms_acceptance-result', $service->validate_terms_acceptance( true ) );
		$this->assertSame( 'run_log_registration_flow_result-result', $service->log_registration_flow_result( 'person@example.test', 'accepted' ) );
		$this->assertSame( 'run_find_pending_by_token-result', $service->find_pending_by_token( 'token' ) );
		$this->assertSame( 'run_send_confirmation_email-result', $service->send_confirmation_email( (object) array(), array() ) );
		$this->assertSame( 'run_registration_complete_login_url-result', $service->registration_complete_login_url( array() ) );
		$this->assertSame( 'run_dispatch_account_created_webhook-result', $service->dispatch_account_created_webhook( 7, array() ) );
		$this->assertSame( 'run_validate_password-result', $service->validate_password( 'password' ) );

		$expected_methods = array(
			'request'      => 'run_maybe_handle_registration_request',
			'protection'   => 'run_validate_terms_acceptance',
			'activity'     => 'run_log_registration_flow_result',
			'pending'      => 'run_find_pending_by_token',
			'confirmation' => 'run_send_confirmation_email',
			'completion'   => 'run_registration_complete_login_url',
			'delivery'     => 'run_dispatch_account_created_webhook',
			'credentials'  => 'run_validate_password',
		);

		foreach ( $expected_methods as $key => $method ) {
			$this->assertSame( $method, $spies[ $key ]->calls[0]['method'] );
		}
	}

	/**
	 * Confirm each authentication concern delegates through its injected collaborator.
	 */
	public function test_auth_facade_delegates_each_concern() {
		$keys  = array( 'request', 'activity', 'messages', 'password_reset', 'redirects' );
		$spies = array();

		foreach ( $keys as $key ) {
			$spies[ $key ] = new ALYNT_AG_Test_Service_Collaborator_Spy();
		}

		$service = new ALYNT_AG_Auth_Service( null, $spies );
		$service->maybe_handle_auth_request();
		$this->assertSame( 'run_log_rate_limit_result-result', $service->log_rate_limit_result( 'person@example.test', 'accepted' ) );
		$this->assertSame( 'run_get_lostpassword_sent_message-result', $service->get_lostpassword_sent_message() );
		$this->assertSame( 'run_validate_password_reset_key-result', $service->validate_password_reset_key( 'key', 'login' ) );
		$this->assertSame( 'run_get_login_redirect_url-result', $service->get_login_redirect_url( '', array() ) );

		$expected_methods = array(
			'request'        => 'run_maybe_handle_auth_request',
			'activity'       => 'run_log_rate_limit_result',
			'messages'       => 'run_get_lostpassword_sent_message',
			'password_reset' => 'run_validate_password_reset_key',
			'redirects'      => 'run_get_login_redirect_url',
		);

		foreach ( $expected_methods as $key => $method ) {
			$this->assertSame( $method, $spies[ $key ]->calls[0]['method'] );
		}
	}

	/**
	 * Collaborators must continue to honor facade subclass overrides.
	 */
	public function test_registration_collaborator_forwards_override_sensitive_calls() {
		$service = new class() extends ALYNT_AG_Registration_Service {
			public $validated = array();

			public function validate_password( $password ) {
				$this->validated[] = $password;
				return 'override-result';
			}
		};

		$this->assertSame( 'override-result', $service->validate_password_pair( 'MatchingPassword1!', 'MatchingPassword1!' ) );
		$this->assertSame( array( 'MatchingPassword1!' ), $service->validated );
	}

	/**
	 * Authentication reset completion must honor key-validation overrides.
	 */
	public function test_auth_collaborator_forwards_override_sensitive_calls() {
		$service = new class() extends ALYNT_AG_Auth_Service {
			public $validated = array();

			public function validate_password_reset_key( $key, $login ) {
				$this->validated[] = compact( 'key', 'login' );
				return (object) array(
					'ID'         => 91,
					'user_login' => $login,
				);
			}
		};

		$this->assertTrue( $service->complete_password_reset( 'override-key', 'person@example.test', 'StrongPassword1!', 'StrongPassword1!' ) );
		$this->assertSame(
			array(
				array(
					'key'   => 'override-key',
					'login' => 'person@example.test',
				),
			),
			$service->validated
		);
	}

	/**
	 * Keep extracted concerns and their facades inside the agreed thresholds.
	 */
	public function test_registration_and_auth_files_stay_within_structure_thresholds() {
		$collaborators = array(
			'class-service-collaborator.php',
			'class-registration-request-handler.php',
			'class-registration-protection.php',
			'class-registration-activity.php',
			'class-registration-pending-store.php',
			'class-registration-confirmation.php',
			'class-registration-completion.php',
			'class-registration-delivery.php',
			'class-registration-credentials.php',
			'class-auth-request-handler.php',
			'class-auth-activity.php',
			'class-auth-messages.php',
			'class-auth-password-reset.php',
			'class-auth-redirects.php',
		);

		foreach ( $collaborators as $file ) {
			$this->assertLessThanOrEqual( 300, count( file( ALYNT_AG_PLUGIN_DIR . 'includes/services/' . $file ) ), $file );
		}

		$this->assertLessThanOrEqual( 400, count( file( ALYNT_AG_PLUGIN_DIR . 'includes/services/class-registration-service.php' ) ) );
		$this->assertLessThanOrEqual( 250, count( file( ALYNT_AG_PLUGIN_DIR . 'includes/services/class-auth-service.php' ) ) );
	}

	/**
	 * Production and test loaders must define collaborators before their facades.
	 */
	public function test_loaders_keep_collaborators_before_facades() {
		$loaders = array(
			ALYNT_AG_PLUGIN_DIR . 'includes/class-loader.php',
			ALYNT_AG_PLUGIN_DIR . 'tests/support/load-plugin.php',
		);

		foreach ( $loaders as $loader ) {
			$contents = file_get_contents( $loader );

			$this->assertIsString( $contents );
			$this->assertLessThan( strpos( $contents, 'class-registration-service.php' ), strpos( $contents, 'class-registration-completion.php' ) );
			$this->assertLessThan( strpos( $contents, 'class-auth-service.php' ), strpos( $contents, 'class-auth-password-reset.php' ) );
		}
	}
}
