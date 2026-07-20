<?php
/**
 * Settings defaults and stored-value access.
 *
 * @package Alynt_Account_Gateway
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Derives defaults and resolves stored settings.
 */
class ALYNT_AG_Settings_Defaults {

	/**
	 * Return defaults for a schema.
	 *
	 * @param array<string,array<string,mixed>> $schema Settings schema.
	 * @return array<string,mixed>
	 */
	public static function defaults( $schema ) {
		$defaults = array();

		foreach ( $schema as $key => $field ) {
			$defaults[ $key ] = $field['default'];
		}

		if ( empty( $defaults['emergency_bypass_key'] ) && function_exists( 'wp_generate_password' ) ) {
			$defaults['emergency_bypass_key'] = wp_generate_password( 32, false, false );
		}

		return $defaults;
	}

	/**
	 * Return schema keys for one tab.
	 *
	 * @param string                            $tab    Settings tab key.
	 * @param array<string,array<string,mixed>> $schema Settings schema.
	 * @return array<int,string>
	 */
	public static function keys_for_tab( $tab, $schema ) {
		$keys = array();

		foreach ( $schema as $key => $field ) {
			if ( isset( $field['tab'] ) && $field['tab'] === $tab ) {
				$keys[] = $key;
			}
		}

		return $keys;
	}

	/**
	 * Return defaults for one tab.
	 *
	 * @param string                            $tab    Settings tab key.
	 * @param array<string,array<string,mixed>> $schema Settings schema.
	 * @return array<string,mixed>
	 */
	public static function defaults_for_tab( $tab, $schema ) {
		$defaults     = self::defaults( $schema );
		$tab_defaults = array();

		foreach ( self::keys_for_tab( $tab, $schema ) as $key ) {
			$tab_defaults[ $key ] = $defaults[ $key ];
		}

		return $tab_defaults;
	}

	/**
	 * Restore one tab to defaults.
	 *
	 * @param string                            $tab      Settings tab key.
	 * @param array<string,string>              $tabs     Available tabs.
	 * @param array<string,array<string,mixed>> $schema   Settings schema.
	 * @param array<string,mixed>               $settings Current settings.
	 * @return array<string,mixed>|WP_Error
	 */
	public static function restore_tab_defaults( $tab, $tabs, $schema, $settings ) {
		if ( ! isset( $tabs[ $tab ] ) ) {
			return new WP_Error(
				'alynt_ag_invalid_settings_tab',
				__( 'The selected settings tab is invalid.', 'alynt-account-gateway' )
			);
		}

		$tab_defaults = self::defaults_for_tab( $tab, $schema );

		if ( empty( $tab_defaults ) ) {
			return new WP_Error(
				'alynt_ag_empty_settings_tab',
				__( 'The selected settings tab does not contain restorable settings.', 'alynt-account-gateway' )
			);
		}

		return array_merge( $settings, $tab_defaults );
	}

	/**
	 * Merge stored settings over schema defaults.
	 *
	 * @param array<string,array<string,mixed>> $schema   Settings schema.
	 * @param array<string,mixed>|null          $defaults Prepared defaults.
	 * @return array<string,mixed>
	 */
	public static function get_settings( $schema, $defaults = null ) {
		$saved = get_option( 'alynt_ag_settings', array() );

		if ( ! is_array( $saved ) ) {
			$saved = array();
		}

		$defaults = is_array( $defaults ) ? $defaults : self::defaults( $schema );

		return array_merge( $defaults, $saved );
	}
}
