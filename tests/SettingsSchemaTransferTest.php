<?php
/**
 * Settings schema tests.
 *
 * @package Alynt_Account_Gateway
 */

require_once __DIR__ . '/support/class-settings-schema-test-case.php';

/**
 * Tests settings export, import, inspection, and tab restoration.
 */
class SettingsSchemaTransferTest extends SettingsSchemaTestCase {

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
			array( 'login_path', 'account_action_base', 'after_login_redirect', 'administrator_after_login_redirect', 'shop_manager_after_login_redirect' ),
			array_keys( $defaults )
		);
		$this->assertSame( '/login', $defaults['login_path'] );
		$this->assertSame( '/account', $defaults['account_action_base'] );
		$this->assertSame( '/wp-admin/', $defaults['administrator_after_login_redirect'] );
		$this->assertSame( '/wp-admin/', $defaults['shop_manager_after_login_redirect'] );
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
}
