<?php
/**
 * Settings schema compatibility facade.
 *
 * @package Alynt_Account_Gateway
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Preserves the public static settings API while delegating focused concerns.
 */
class ALYNT_AG_Settings_Schema {

	/**
	 * Request-local immutable schema cache.
	 *
	 * @var array<string,array<string,mixed>>|null
	 */
	private static $schema_cache;

	/**
	 * Request-local immutable defaults cache.
	 *
	 * @var array<string,mixed>|null
	 */
	private static $defaults_cache;

	/**
	 * Return settings tabs.
	 *
	 * @return array<string,string>
	 */
	public static function tabs() {
		return ALYNT_AG_Settings_Definition::tabs();
	}

	/**
	 * Return settings schema.
	 *
	 * @return array<string,array<string,mixed>>
	 */
	public static function schema() {
		if ( null === self::$schema_cache ) {
			self::$schema_cache = ALYNT_AG_Settings_Definition::schema();
		}

		return self::$schema_cache;
	}

	/**
	 * Return settings defaults.
	 *
	 * @return array<string,mixed>
	 */
	public static function defaults() {
		if ( null === self::$defaults_cache ) {
			self::$defaults_cache = ALYNT_AG_Settings_Defaults::defaults( self::schema() );
		}

		return self::$defaults_cache;
	}

	/**
	 * Return schema keys for one tab.
	 *
	 * @param string $tab Settings tab key.
	 * @return array<int,string>
	 */
	public static function keys_for_tab( $tab ) {
		return ALYNT_AG_Settings_Defaults::keys_for_tab( $tab, self::schema() );
	}

	/**
	 * Return defaults for one tab.
	 *
	 * @param string $tab Settings tab key.
	 * @return array<string,mixed>
	 */
	public static function defaults_for_tab( $tab ) {
		return ALYNT_AG_Settings_Defaults::defaults_for_tab( $tab, self::schema() );
	}

	/**
	 * Restore one tab to defaults.
	 *
	 * @param string $tab Settings tab key.
	 * @return array<string,mixed>|WP_Error
	 */
	public static function restore_tab_defaults( $tab ) {
		return ALYNT_AG_Settings_Defaults::restore_tab_defaults(
			$tab,
			self::tabs(),
			self::schema(),
			self::get_settings()
		);
	}

	/**
	 * Return merged settings.
	 *
	 * @return array<string,mixed>
	 */
	public static function get_settings() {

		return ALYNT_AG_Settings_Defaults::get_settings( self::schema(), self::defaults() );
	}

	/**
	 * Create a portable settings export package.
	 *
	 * @return array<string,mixed>
	 */
	public static function export_package() {

		return array(
			'plugin'     => 'alynt-account-gateway',
			'version'    => defined( 'ALYNT_AG_VERSION' ) ? ALYNT_AG_VERSION : '',
			'exportedAt' => gmdate( 'c' ),
			'settings'   => self::portable_settings(),
		);
	}

	/**
	 * Return settings that are safe and useful to move between sites.
	 *
	 * @return array<string,mixed>
	 */
	private static function portable_settings() {
		$settings = self::get_settings();

		foreach ( self::schema() as $key => $field ) {
			$type = isset( $field['type'] ) ? (string) $field['type'] : '';

			if ( in_array( $type, array( 'secret', 'email', 'attachment_id', 'nav_menu' ), true ) ) {
				unset( $settings[ $key ] );
			}
		}

		return $settings;
	}

	/**
	 * Inspect a settings import package without saving it.
	 *
	 * @param string $json Raw JSON package.
	 * @return array<string,mixed>|WP_Error
	 */
	public static function inspect_import_package( $json ) {
		$package = json_decode( (string) $json, true );

		if ( ! is_array( $package ) ) {
			return new WP_Error(
				'alynt_ag_invalid_settings_import',
				__( 'The selected settings file is not valid JSON.', 'alynt-account-gateway' )
			);
		}

		$settings = isset( $package['settings'] ) && is_array( $package['settings'] ) ? $package['settings'] : $package;
		$known    = self::filter_known_settings( $settings );
		$unknown  = array();

		foreach ( $settings as $key => $value ) {
			unset( $value );

			if ( ! array_key_exists( $key, $known ) ) {
				$unknown[] = (string) $key;
			}
		}

		if ( empty( $known ) ) {
			return new WP_Error(
				'alynt_ag_empty_settings_import',
				__( 'The selected settings file does not contain any recognized plugin settings.', 'alynt-account-gateway' )
			);
		}

		return array(
			'plugin'        => isset( $package['plugin'] ) && is_scalar( $package['plugin'] ) ? sanitize_text_field( (string) $package['plugin'] ) : '',
			'version'       => isset( $package['version'] ) && is_scalar( $package['version'] ) ? sanitize_text_field( (string) $package['version'] ) : '',
			'exported_at'   => isset( $package['exportedAt'] ) && is_scalar( $package['exportedAt'] ) ? sanitize_text_field( (string) $package['exportedAt'] ) : '',
			'known_keys'    => array_keys( $known ),
			'unknown_keys'  => $unknown,
			'known_count'   => count( $known ),
			'unknown_count' => count( $unknown ),
		);
	}

	/**
	 * Parse and sanitize a settings import package.
	 *
	 * @param string $json Raw JSON package.
	 * @return array<string,mixed>|WP_Error
	 */
	public static function import_package( $json ) {
		$inspection = self::inspect_import_package( $json );

		if ( is_wp_error( $inspection ) ) {
			return $inspection;
		}

		$package = json_decode( (string) $json, true );

		$settings = isset( $package['settings'] ) && is_array( $package['settings'] ) ? $package['settings'] : $package;
		$settings = self::filter_known_settings( $settings );

		return self::sanitize( $settings );
	}

	/**
	 * Keep only schema-owned settings.
	 *
	 * @param array<string,mixed> $settings Candidate settings.
	 * @return array<string,mixed>
	 */
	public static function filter_known_settings( $settings ) {
		return ALYNT_AG_Settings_Sanitizer::filter_known_settings( $settings, self::schema() );
	}

	/**
	 * Sanitize settings.
	 *
	 * @param array<string,mixed> $input Raw settings.
	 * @return array<string,mixed>
	 */
	public static function sanitize( $input ) {
		return ALYNT_AG_Settings_Sanitizer::sanitize(
			$input,
			self::schema(),
			self::get_settings()
		);
	}
}
