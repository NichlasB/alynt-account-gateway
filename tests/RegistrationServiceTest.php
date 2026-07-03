<?php
/**
 * Registration service tests.
 *
 * @package Alynt_Account_Gateway
 */

use PHPUnit\Framework\TestCase;

/**
 * Tests pending registration helpers.
 */
class RegistrationServiceTest extends TestCase {

	protected function setUp(): void {
		parent::setUp();
		$GLOBALS['alynt_ag_test_mail'] = array();
		$GLOBALS['alynt_ag_test_options'] = array();
		$GLOBALS['alynt_ag_test_transients'] = array();
	}

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
	}

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
}
