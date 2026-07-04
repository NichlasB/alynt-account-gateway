<?php
/**
 * Settings schema tests.
 *
 * @package Alynt_Account_Gateway
 */

use PHPUnit\Framework\TestCase;

/**
 * Tests settings defaults and policies.
 */
class SettingsSchemaTest extends TestCase {

	public function test_frontend_output_is_disabled_by_default() {
		$defaults = ALYNT_AG_Settings_Schema::defaults();

		$this->assertArrayHasKey( 'frontend_enabled', $defaults );
		$this->assertFalse( $defaults['frontend_enabled'] );
	}

	public function test_registration_is_disabled_by_default() {
		$defaults = ALYNT_AG_Settings_Schema::defaults();

		$this->assertArrayHasKey( 'registration_enabled', $defaults );
		$this->assertFalse( $defaults['registration_enabled'] );
		$this->assertSame( '@User_{first_name}_{last_name}', $defaults['username_format'] );
		$this->assertSame( 5, $defaults['registration_rate_limit_count'] );
		$this->assertSame( 10, $defaults['login_rate_limit_count'] );
		$this->assertSame( 5, $defaults['lostpassword_rate_limit_count'] );
	}

	public function test_privacy_retention_defaults_exist() {
		$defaults = ALYNT_AG_Settings_Schema::defaults();

		$this->assertSame( 7, $defaults['success_log_retention'] );
		$this->assertSame( 30, $defaults['failed_log_retention'] );
		$this->assertSame( 30, $defaults['verification_log_retention'] );
		$this->assertSame( 365, $defaults['consent_record_retention'] );
		$this->assertSame( 180, $defaults['audit_log_retention'] );
	}

	public function test_branding_defaults_are_brand_agnostic_design_tokens() {
		$defaults = ALYNT_AG_Settings_Schema::defaults();

		$this->assertSame( '#3B5249', $defaults['primary_color'] );
		$this->assertSame( '#E1CDB5', $defaults['accent_color'] );
		$this->assertSame( '#281408', $defaults['text_color'] );
		$this->assertSame( '#EAE4D6', $defaults['page_background_color'] );
		$this->assertSame( '#FFFFFF', $defaults['surface_color'] );
		$this->assertSame( '#B3492E', $defaults['error_color'] );
		$this->assertSame( 'Georgia, serif', $defaults['heading_font_family'] );
		$this->assertSame( '-apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif', $defaults['body_font_family'] );
	}

	public function test_screen_copy_defaults_exist() {
		$defaults = ALYNT_AG_Settings_Schema::defaults();

		$this->assertArrayHasKey( 'login_intro_text', $defaults );
		$this->assertArrayHasKey( 'register_intro_text', $defaults );
		$this->assertArrayHasKey( 'lostpassword_intro_text', $defaults );
		$this->assertArrayHasKey( 'setpassword_intro_text', $defaults );
		$this->assertArrayHasKey( 'logout_intro_text', $defaults );
		$this->assertArrayHasKey( 'registration_disabled_text', $defaults );
		$this->assertArrayHasKey( 'invalid_link_text', $defaults );
	}

	public function test_dashboard_defaults_exist() {
		$defaults = ALYNT_AG_Settings_Schema::defaults();

		$this->assertArrayHasKey( 'dashboard_enabled', $defaults );
		$this->assertArrayHasKey( 'dashboard_custom_links', $defaults );
		$this->assertFalse( $defaults['dashboard_enabled'] );
		$this->assertSame( '[]', $defaults['dashboard_custom_links'] );
	}

	public function test_email_template_defaults_exist() {
		$defaults = ALYNT_AG_Settings_Schema::defaults();

		$this->assertArrayHasKey( 'email_registration_confirmation_subject', $defaults );
		$this->assertArrayHasKey( 'email_password_reset_subject', $defaults );
		$this->assertArrayHasKey( 'email_password_changed_subject', $defaults );
		$this->assertArrayHasKey( 'email_new_user_welcome_subject', $defaults );
		$this->assertArrayHasKey( 'email_change_confirmation_subject', $defaults );
		$this->assertFalse( $defaults['email_password_changed_disabled'] );
		$this->assertFalse( $defaults['email_new_user_welcome_disabled'] );
		$this->assertFalse( $defaults['email_change_confirmation_disabled'] );
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
