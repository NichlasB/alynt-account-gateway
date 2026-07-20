<?php
/**
 * Settings page settings-transfer component.
 *
 * @package Alynt_Account_Gateway
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Owns focused settings-transfer behavior for the settings page.
 */
class ALYNT_AG_Settings_Page_Settings_Transfer extends ALYNT_AG_Settings_Page_Component {

	/**
	 * Export plugin settings as JSON.
	 *
	 * @return void
	 */
	public function handle_export_settings() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You do not have permission to export settings.', 'alynt-account-gateway' ) );
		}

		check_admin_referer( 'alynt_ag_export_settings' );

		$package = ALYNT_AG_Settings_Schema::export_package();
		$json    = wp_json_encode( $package, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES );

		if ( ! is_string( $json ) ) {
			wp_die( esc_html__( 'Settings could not be encoded for export.', 'alynt-account-gateway' ) );
		}

		header( 'Content-Type: application/json; charset=utf-8' );
		header( 'Content-Disposition: attachment; filename=alynt-account-gateway-settings.json' );

		echo $json; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- JSON download generated from sanitized settings.
		exit;
	}

	/**
	 * Import plugin settings from JSON.
	 *
	 * @return void
	 */
	public function handle_import_settings() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You do not have permission to import settings.', 'alynt-account-gateway' ) );
		}

		check_admin_referer( 'alynt_ag_import_settings' );

		$status        = 'settings_import_upload_failed';
		$ignored_count = 0;
		$file          = isset( $_FILES['settings_file'] ) && is_array( $_FILES['settings_file'] ) ? $_FILES['settings_file'] : array(); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- File metadata is validated before use.

		if ( isset( $file['tmp_name'], $file['error'] ) && is_string( $file['tmp_name'] ) && UPLOAD_ERR_OK === (int) $file['error'] && is_uploaded_file( $file['tmp_name'] ) ) {
			$json       = file_get_contents( $file['tmp_name'] ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- Reading the PHP-uploaded temp file only.
			$json       = is_string( $json ) ? $json : '';
			$inspection = ALYNT_AG_Settings_Schema::inspect_import_package( $json );
			$imported   = is_wp_error( $inspection ) ? $inspection : ALYNT_AG_Settings_Schema::import_package( $json );

			if ( ! is_wp_error( $imported ) ) {
				$ignored_count = isset( $inspection['unknown_count'] ) ? absint( $inspection['unknown_count'] ) : 0;
				if ( $this->persist_settings( $imported ) ) {
					ALYNT_AG_Diagnostics_Logger::log(
						'settings_imported',
						array(
							'imported_keys'  => isset( $inspection['known_keys'] ) ? $inspection['known_keys'] : array_keys( ALYNT_AG_Settings_Schema::filter_known_settings( $imported ) ),
							'ignored_keys'   => isset( $inspection['unknown_keys'] ) ? $inspection['unknown_keys'] : array(),
							'source_plugin'  => isset( $inspection['plugin'] ) ? $inspection['plugin'] : '',
							'source_version' => isset( $inspection['version'] ) ? $inspection['version'] : '',
							'exported_at'    => isset( $inspection['exported_at'] ) ? $inspection['exported_at'] : '',
						),
						get_current_user_id()
					);
					$status = $ignored_count > 0 ? 'settings_imported_with_ignored_keys' : 'settings_imported';
				} else {
					$status = 'settings_import_failed';
				}
			} elseif ( 'alynt_ag_invalid_settings_import' === $imported->get_error_code() ) {
				$status = 'settings_import_invalid_json';
			} elseif ( 'alynt_ag_empty_settings_import' === $imported->get_error_code() ) {
				$status = 'settings_import_empty';
			} else {
				$status = 'settings_import_failed';
			}
		}

		wp_safe_redirect(
			add_query_arg(
				array(
					'page'                    => 'alynt-account-gateway',
					'tab'                     => 'advanced_tools',
					'alynt_ag_notice'         => $status,
					'alynt_ag_import_ignored' => $ignored_count,
				),
				admin_url( 'options-general.php' )
			)
		);
		exit;
	}

	/**
	 * Restore one settings tab to defaults.
	 *
	 * @return void
	 */
	public function handle_restore_tab_defaults() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You do not have permission to restore settings.', 'alynt-account-gateway' ) );
		}

		$tabs = ALYNT_AG_Settings_Schema::tabs();
		$tab  = isset( $_POST['tab'] ) ? sanitize_key( wp_unslash( $_POST['tab'] ) ) : 'general';
		$tab  = isset( $tabs[ $tab ] ) ? $tab : 'general';

		check_admin_referer( 'alynt_ag_restore_tab_defaults_' . $tab );

		$restored = ALYNT_AG_Settings_Schema::restore_tab_defaults( $tab );
		$status   = 'tab_defaults_failed';

		if ( ! is_wp_error( $restored ) ) {
			if ( $this->persist_settings( $restored ) ) {
				ALYNT_AG_Diagnostics_Logger::log(
					'tab_defaults_restored',
					array(
						'tab'           => $tab,
						'restored_keys' => ALYNT_AG_Settings_Schema::keys_for_tab( $tab ),
					),
					get_current_user_id()
				);
				$status = 'tab_defaults_restored';
			}
		}

		wp_safe_redirect(
			add_query_arg(
				array(
					'page'            => 'alynt-account-gateway',
					'tab'             => $tab,
					'alynt_ag_notice' => $status,
				),
				admin_url( 'options-general.php' )
			)
		);
		exit;
	}

	/**
	 * Store settings and verify the resulting option value.
	 *
	 * @param array<string,mixed> $settings Sanitized settings.
	 * @return bool
	 */
	private function persist_settings( $settings ) {
		$updated = update_option( 'alynt_ag_settings', $settings );

		return $updated || get_option( 'alynt_ag_settings' ) === $settings;
	}
}
