<?php
/**
 * Settings schema collaboration tests.
 *
 * @package Alynt_Account_Gateway
 */

require_once __DIR__ . '/support/class-settings-schema-test-case.php';

/**
 * Locks the settings facade contract and extracted collaborator boundaries.
 */
class SettingsSchemaCollaborationTest extends SettingsSchemaTestCase {

	/**
	 * Confirm the public facade retains its established static API.
	 */
	public function test_facade_retains_public_static_api() {
		$expected = array(
			'tabs',
			'schema',
			'defaults',
			'keys_for_tab',
			'defaults_for_tab',
			'restore_tab_defaults',
			'get_settings',
			'export_package',
			'inspect_import_package',
			'import_package',
			'filter_known_settings',
			'sanitize',
		);
		$actual   = array();

		foreach ( ( new ReflectionClass( ALYNT_AG_Settings_Schema::class ) )->getMethods( ReflectionMethod::IS_PUBLIC ) as $method ) {
			if ( $method->isStatic() ) {
				$actual[] = $method->getName();
			}
		}

		$this->assertSame( $expected, $actual );
	}

	/**
	 * Confirm extraction preserves exact order, metadata, and defaults.
	 */
	public function test_definition_and_defaults_preserve_baseline_fingerprints() {
		$this->assertSame(
			'70530449c004ad07db184561ed00ae270bb2abf24e7affeee36a31b30b031bc5',
			hash( 'sha256', serialize( ALYNT_AG_Settings_Schema::tabs() ) )
		);
		$this->assertSame(
			'a1b5089ea1b9a3057008dc678c7dc5d6c62d95820ee5c962056a69f4e779a5cc',
			hash( 'sha256', serialize( ALYNT_AG_Settings_Schema::schema() ) )
		);
		$this->assertSame(
			'29495cc8be7ab67a4512ab67ec2463a51d986e4be6bd9008a8688bd67b41f753',
			hash( 'sha256', serialize( ALYNT_AG_Settings_Schema::defaults() ) )
		);
	}

	/**
	 * Confirm definition providers remain ordered behind the facade.
	 */
	public function test_definition_aggregates_ordered_providers() {
		$provided = array_merge(
			ALYNT_AG_Settings_Definition_Core::fields(),
			ALYNT_AG_Settings_Definition_Security_Email::fields(),
			ALYNT_AG_Settings_Definition_Account_Data::fields()
		);

		$this->assertSame( $provided, ALYNT_AG_Settings_Definition::schema() );
		$this->assertSame( $provided, ALYNT_AG_Settings_Schema::schema() );
	}

	/**
	 * Confirm defaults and sanitization delegate without changing results.
	 */
	public function test_defaults_and_sanitizer_collaborators_match_facade_results() {
		$schema  = ALYNT_AG_Settings_Schema::schema();
		$current = ALYNT_AG_Settings_Schema::get_settings();
		$input   = array(
			'frontend_enabled'                 => '1',
			'login_path'                      => 'members?ignored=1',
			'reoon_flagged_policy'             => 'unexpected',
			'dashboard_custom_links'           => '[{"label":"Support","url":"/support/"}]',
			'woocommerce_hidden_menu_items'    => array( 'orders', 'orders', 'downloads' ),
			'email_password_reset_body'        => '<h2>Reset</h2><script>bad()</script>',
			'administrator_after_login_redirect' => '/wp-admin/',
		);

		$this->assertSame(
			ALYNT_AG_Settings_Defaults::defaults( $schema ),
			ALYNT_AG_Settings_Schema::defaults()
		);
		$this->assertSame(
			ALYNT_AG_Settings_Sanitizer::sanitize( $input, $schema, $current ),
			ALYNT_AG_Settings_Schema::sanitize( $input )
		);
		$this->assertSame(
			ALYNT_AG_Settings_Sanitizer::filter_known_settings( $input, $schema ),
			ALYNT_AG_Settings_Schema::filter_known_settings( $input )
		);
	}

	/**
	 * Keep each extracted settings responsibility within the review threshold.
	 */
	public function test_settings_collaborators_remain_under_300_lines() {
		$files = array(
			'includes/class-settings-definition-core.php',
			'includes/class-settings-definition-security-email.php',
			'includes/class-settings-definition-account-data.php',
			'includes/class-settings-definition.php',
			'includes/class-settings-defaults.php',
			'includes/class-settings-sanitizer.php',
			'includes/class-settings-schema.php',
		);

		foreach ( $files as $file ) {
			$lines = file( ALYNT_AG_PLUGIN_DIR . $file );

			$this->assertIsArray( $lines, $file );
			$this->assertLessThanOrEqual( 300, count( $lines ), $file );
		}
	}
}
