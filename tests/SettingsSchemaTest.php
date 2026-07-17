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

	protected function tearDown(): void {
		unset( $GLOBALS['alynt_ag_test_options']['alynt_ag_settings'] );

		parent::tearDown();
	}

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

	public function test_export_package_contains_plugin_metadata_and_settings() {
		$GLOBALS['alynt_ag_test_options']['alynt_ag_settings'] = array(
			'frontend_enabled'       => true,
			'login_path'             => '/member-login',
			'emergency_bypass_key'   => 'private-bypass',
			'turnstile_secret_key'   => 'private-turnstile',
			'reoon_api_key'          => 'private-reoon',
			'webhook_signing_secret' => 'private-webhook',
			'email_test_recipient'   => 'owner@example.test',
			'brand_logo_id'          => 123,
			'background_image_id'    => 456,
			'dashboard_footer_menu_id' => 789,
		);

		$package = ALYNT_AG_Settings_Schema::export_package();

		$this->assertSame( 'alynt-account-gateway', $package['plugin'] );
		$this->assertSame( ALYNT_AG_VERSION, $package['version'] );
		$this->assertArrayHasKey( 'exportedAt', $package );
		$this->assertTrue( $package['settings']['frontend_enabled'] );
		$this->assertSame( '/member-login', $package['settings']['login_path'] );
		$this->assertArrayNotHasKey( 'emergency_bypass_key', $package['settings'] );
		$this->assertArrayNotHasKey( 'turnstile_secret_key', $package['settings'] );
		$this->assertArrayNotHasKey( 'reoon_api_key', $package['settings'] );
		$this->assertArrayNotHasKey( 'webhook_signing_secret', $package['settings'] );
		$this->assertArrayNotHasKey( 'email_test_recipient', $package['settings'] );
		$this->assertArrayNotHasKey( 'brand_logo_id', $package['settings'] );
		$this->assertArrayNotHasKey( 'background_image_id', $package['settings'] );
		$this->assertArrayNotHasKey( 'dashboard_offcanvas_menu_id', $package['settings'] );
		$this->assertArrayNotHasKey( 'dashboard_footer_menu_id', $package['settings'] );
	}

	public function test_import_package_sanitizes_known_settings_and_discards_unknown_keys() {
		$GLOBALS['alynt_ag_test_options']['alynt_ag_settings'] = array(
			'frontend_enabled'     => false,
			'login_path'           => '/login',
			'reoon_api_key'        => 'destination-secret',
			'email_test_recipient' => 'destination@example.test',
			'brand_logo_id'        => 789,
		);

		$imported = ALYNT_AG_Settings_Schema::import_package(
			wp_json_encode(
				array(
					'settings' => array(
						'frontend_enabled' => '1',
						'login_path'       => 'members?bad=1',
						'primary_color'    => 'not-a-color',
						'unknown_setting'  => 'ignored',
					),
				)
			)
		);

		$this->assertIsArray( $imported );
		$this->assertTrue( $imported['frontend_enabled'] );
		$this->assertSame( '/members', $imported['login_path'] );
		$this->assertSame( '', $imported['primary_color'] );
		$this->assertSame( 'destination-secret', $imported['reoon_api_key'] );
		$this->assertSame( 'destination@example.test', $imported['email_test_recipient'] );
		$this->assertSame( 789, $imported['brand_logo_id'] );
		$this->assertArrayNotHasKey( 'unknown_setting', $imported );
	}

	public function test_inspect_import_package_reports_known_and_unknown_settings() {
		$inspection = ALYNT_AG_Settings_Schema::inspect_import_package(
			wp_json_encode(
				array(
					'plugin'     => 'alynt-account-gateway',
					'version'    => '0.1.54',
					'exportedAt' => '2026-07-06T12:00:00+00:00',
					'settings'   => array(
						'frontend_enabled' => '1',
						'login_path'       => '/members',
						'unknown_setting'  => 'ignored',
					),
				)
			)
		);

		$this->assertIsArray( $inspection );
		$this->assertSame( 'alynt-account-gateway', $inspection['plugin'] );
		$this->assertSame( '0.1.54', $inspection['version'] );
		$this->assertSame( '2026-07-06T12:00:00+00:00', $inspection['exported_at'] );
		$this->assertSame( array( 'frontend_enabled', 'login_path' ), $inspection['known_keys'] );
		$this->assertSame( array( 'unknown_setting' ), $inspection['unknown_keys'] );
		$this->assertSame( 2, $inspection['known_count'] );
		$this->assertSame( 1, $inspection['unknown_count'] );
	}

	public function test_import_package_rejects_invalid_json() {
		$imported = ALYNT_AG_Settings_Schema::import_package( '{invalid-json' );

		$this->assertInstanceOf( WP_Error::class, $imported );
		$this->assertSame( 'alynt_ag_invalid_settings_import', $imported->get_error_code() );
	}

	public function test_inspect_import_package_rejects_invalid_json() {
		$inspection = ALYNT_AG_Settings_Schema::inspect_import_package( '{invalid-json' );

		$this->assertInstanceOf( WP_Error::class, $inspection );
		$this->assertSame( 'alynt_ag_invalid_settings_import', $inspection->get_error_code() );
	}

	public function test_import_package_rejects_packages_without_known_settings() {
		$imported = ALYNT_AG_Settings_Schema::import_package(
			wp_json_encode(
				array(
					'settings' => array(
						'not_ours' => 'ignored',
					),
				)
			)
		);

		$this->assertInstanceOf( WP_Error::class, $imported );
		$this->assertSame( 'alynt_ag_empty_settings_import', $imported->get_error_code() );
	}

	public function test_defaults_for_tab_returns_only_tab_settings() {
		$defaults = ALYNT_AG_Settings_Schema::defaults_for_tab( 'urls' );

		$this->assertSame(
			array( 'login_path', 'account_action_base', 'after_login_redirect' ),
			array_keys( $defaults )
		);
		$this->assertSame( '/login', $defaults['login_path'] );
		$this->assertSame( '/account', $defaults['account_action_base'] );
	}

	public function test_restore_tab_defaults_resets_only_selected_tab() {
		$GLOBALS['alynt_ag_test_options']['alynt_ag_settings'] = array(
			'frontend_enabled'     => true,
			'login_path'           => '/custom-login',
			'account_action_base'  => '/custom-account',
			'primary_color'        => '#123456',
		);

		$restored = ALYNT_AG_Settings_Schema::restore_tab_defaults( 'urls' );

		$this->assertIsArray( $restored );
		$this->assertTrue( $restored['frontend_enabled'] );
		$this->assertSame( '/login', $restored['login_path'] );
		$this->assertSame( '/account', $restored['account_action_base'] );
		$this->assertSame( '#123456', $restored['primary_color'] );
	}

	public function test_restore_tab_defaults_rejects_invalid_tab() {
		$restored = ALYNT_AG_Settings_Schema::restore_tab_defaults( 'missing_tab' );

		$this->assertInstanceOf( WP_Error::class, $restored );
		$this->assertSame( 'alynt_ag_invalid_settings_tab', $restored->get_error_code() );
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

	public function test_dashboard_offcanvas_settings_are_sanitized() {
		$sanitized = ALYNT_AG_Settings_Schema::sanitize(
			array(
				'dashboard_offcanvas_enabled' => '1',
				'dashboard_offcanvas_menu_id' => '-42',
				'dashboard_footer_menu_enabled' => '1',
				'dashboard_footer_menu_id' => '-84',
			)
		);

		$this->assertTrue( $sanitized['dashboard_offcanvas_enabled'] );
		$this->assertSame( 42, $sanitized['dashboard_offcanvas_menu_id'] );
		$this->assertTrue( $sanitized['dashboard_footer_menu_enabled'] );
		$this->assertSame( 84, $sanitized['dashboard_footer_menu_id'] );
	}

	public function test_woocommerce_dashboard_visibility_is_sanitized_to_hidden_endpoint_keys() {
		$sanitized = ALYNT_AG_Settings_Schema::sanitize(
			array(
				'woocommerce_hidden_menu_items' => array(
					'orders'          => '1',
					'downloads'       => '0',
					'loyalty_points'  => '1',
					'<script>'        => '1',
				),
			)
		);

		$this->assertSame(
			array( 'orders', 'loyalty_points', 'script' ),
			$sanitized['woocommerce_hidden_menu_items']
		);
	}

	public function test_woocommerce_dashboard_visibility_accepts_indexed_import_values() {
		$sanitized = ALYNT_AG_Settings_Schema::sanitize(
			array(
				'woocommerce_hidden_menu_items' => array( 'orders', 'loyalty-points', 'orders' ),
			)
		);

		$this->assertSame(
			array( 'orders', 'loyalty-points' ),
			$sanitized['woocommerce_hidden_menu_items']
		);
	}

	public function test_dashboard_custom_links_are_sanitized_to_json() {
		$sanitized = ALYNT_AG_Settings_Schema::sanitize(
			array(
				'dashboard_custom_links' => wp_json_encode(
					array(
						array(
							'label'   => '<strong>Support</strong>',
							'url'     => '/support/',
							'icon'    => 'help<script>',
							'order'   => '-10',
							'target'  => '_blank',
							'roles'   => array( 'customer', '<bad>' ),
							'unknown' => 'discarded',
						),
						array(
							'label' => 'Missing URL',
							'url'   => '',
						),
					)
				),
			)
		);

		$links = json_decode( $sanitized['dashboard_custom_links'], true );

		$this->assertCount( 1, $links );
		$this->assertSame( 'Support', $links[0]['label'] );
		$this->assertSame( '/support/', $links[0]['url'] );
		$this->assertSame( 'helpscript', $links[0]['icon'] );
		$this->assertSame( 0, $links[0]['order'] );
		$this->assertSame( '_blank', $links[0]['target'] );
		$this->assertSame( array( 'customer', 'bad' ), $links[0]['roles'] );
		$this->assertArrayNotHasKey( 'unknown', $links[0] );
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
