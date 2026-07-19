<?php
/**
 * Settings schema tests.
 *
 * @package Alynt_Account_Gateway
 */

require_once __DIR__ . '/support/class-settings-schema-test-case.php';

/**
 * Tests email and provider security schema values.
 */
class SettingsSchemaEmailSecurityTest extends SettingsSchemaTestCase {

	public function test_email_body_fields_use_rich_text_schema_type() {
		$schema = ALYNT_AG_Settings_Schema::schema();
		$keys   = array(
			'email_registration_confirmation_body',
			'email_password_reset_body',
			'email_password_changed_body',
			'email_new_user_welcome_body',
			'email_change_confirmation_body',
		);

		foreach ( $keys as $key ) {
			$this->assertSame( 'rich_text', $schema[ $key ]['type'] );
		}
	}

	public function test_rich_text_email_body_keeps_formatting_and_removes_unsafe_markup() {
		$sanitized = ALYNT_AG_Settings_Schema::sanitize(
			array(
				'email_password_reset_body' => '<h2>Reset</h2><p onclick="bad()"><strong>Safe</strong> <a href="https://example.test/help">Help</a></p><script>alert(1)</script><a href="javascript:alert(2)">Bad link</a>',
			)
		);
		$body      = $sanitized['email_password_reset_body'];

		$this->assertStringContainsString( '<h2>Reset</h2>', $body );
		$this->assertStringContainsString( '<strong>Safe</strong>', $body );
		$this->assertStringContainsString( 'href="https://example.test/help"', $body );
		$this->assertStringNotContainsString( '<script', $body );
		$this->assertStringNotContainsString( 'onclick=', $body );
		$this->assertStringNotContainsString( 'javascript:', $body );
	}

	public function test_reoon_default_policy_blocks_expected_statuses() {
		$client = new ALYNT_AG_Reoon_Client();

		$this->assertTrue( $client->is_blocked_status( 'invalid' ) );
		$this->assertTrue( $client->is_blocked_status( 'disabled' ) );
		$this->assertTrue( $client->is_blocked_status( 'disposable' ) );
		$this->assertTrue( $client->is_blocked_status( 'spamtrap' ) );
		$this->assertTrue( $client->is_flagged_status( 'catch_all' ) );
		$this->assertTrue( $client->is_flagged_status( 'role_account' ) );
		$this->assertTrue( $client->is_flagged_status( 'unknown' ) );
		$this->assertTrue( $client->is_flagged_status( 'inbox_full' ) );
	}

	public function test_reoon_response_interpretation_blocks_disposable_email() {
		$client = new ALYNT_AG_Reoon_Client();
		$result = $client->interpret_response( array( 'status' => 'disposable' ) );

		$this->assertInstanceOf( WP_Error::class, $result );
		$this->assertSame( 'alynt_ag_reoon_blocked', $result->get_error_code() );
	}

	public function test_turnstile_response_interpretation_requires_success() {
		$client = new ALYNT_AG_Turnstile_Client();

		$this->assertTrue( $client->interpret_response( array( 'success' => true ) ) );
		$this->assertInstanceOf( WP_Error::class, $client->interpret_response( array( 'success' => false ) ) );
	}
}
