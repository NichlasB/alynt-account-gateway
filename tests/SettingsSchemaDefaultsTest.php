<?php
/**
 * Settings schema tests.
 *
 * @package Alynt_Account_Gateway
 */

require_once __DIR__ . '/support/class-settings-schema-test-case.php';

/**
 * Tests settings defaults and baseline sanitization.
 */
class SettingsSchemaDefaultsTest extends SettingsSchemaTestCase {

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
		$this->assertSame( 'allow', $defaults['reoon_flagged_policy'] );
	}

	public function test_checkout_authentication_is_opt_in_and_order_pay_is_separate() {
		$defaults = ALYNT_AG_Settings_Schema::defaults();

		$this->assertFalse( $defaults['woocommerce_require_login_checkout'] );
		$this->assertFalse( $defaults['woocommerce_require_login_order_pay'] );

		$sanitized = ALYNT_AG_Settings_Schema::sanitize(
			array(
				'woocommerce_require_login_checkout'  => '1',
				'woocommerce_require_login_order_pay' => '0',
			)
		);

		$this->assertTrue( $sanitized['woocommerce_require_login_checkout'] );
		$this->assertFalse( $sanitized['woocommerce_require_login_order_pay'] );
	}

	public function test_terms_path_and_login_instruction_use_updated_defaults() {
		$defaults = ALYNT_AG_Settings_Schema::defaults();

		$this->assertSame( '/legal/terms/', $defaults['terms_path'] );
		$this->assertSame(
			'Welcome back. Log in to manage your orders and account details.',
			$defaults['login_intro_text']
		);
	}

	public function test_reoon_flagged_policy_sanitizes_to_known_options() {
		$sanitized = ALYNT_AG_Settings_Schema::sanitize(
			array(
				'reoon_flagged_policy' => 'block',
			)
		);

		$this->assertSame( 'block', $sanitized['reoon_flagged_policy'] );

		$sanitized = ALYNT_AG_Settings_Schema::sanitize(
			array(
				'reoon_flagged_policy' => 'unexpected',
			)
		);

		$this->assertSame( 'allow', $sanitized['reoon_flagged_policy'] );
	}

	public function test_privacy_retention_defaults_exist() {
		$defaults = ALYNT_AG_Settings_Schema::defaults();

		$this->assertSame( 7, $defaults['success_log_retention'] );
		$this->assertSame( 30, $defaults['failed_log_retention'] );
		$this->assertSame( 30, $defaults['verification_log_retention'] );
		$this->assertSame( 365, $defaults['consent_record_retention'] );
		$this->assertSame( 180, $defaults['audit_log_retention'] );
	}

	public function test_webhook_signing_secret_defaults_to_empty_and_sanitizes() {
		$defaults = ALYNT_AG_Settings_Schema::defaults();

		$this->assertArrayHasKey( 'webhook_signing_secret', $defaults );
		$this->assertSame( '', $defaults['webhook_signing_secret'] );

		$sanitized = ALYNT_AG_Settings_Schema::sanitize(
			array(
				'webhook_signing_secret' => " shared-secret \n",
			)
		);

		$this->assertSame( 'shared-secret', $sanitized['webhook_signing_secret'] );
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
		$this->assertArrayHasKey( 'dashboard_offcanvas_enabled', $defaults );
		$this->assertArrayHasKey( 'dashboard_offcanvas_menu_id', $defaults );
		$this->assertArrayHasKey( 'dashboard_footer_menu_enabled', $defaults );
		$this->assertArrayHasKey( 'dashboard_footer_menu_id', $defaults );
		$this->assertArrayHasKey( 'woocommerce_hidden_menu_items', $defaults );
		$this->assertFalse( $defaults['dashboard_enabled'] );
		$this->assertSame( '[]', $defaults['dashboard_custom_links'] );
		$this->assertFalse( $defaults['dashboard_offcanvas_enabled'] );
		$this->assertSame( 0, $defaults['dashboard_offcanvas_menu_id'] );
		$this->assertFalse( $defaults['dashboard_footer_menu_enabled'] );
		$this->assertSame( 0, $defaults['dashboard_footer_menu_id'] );
		$this->assertSame( array(), $defaults['woocommerce_hidden_menu_items'] );
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
}
