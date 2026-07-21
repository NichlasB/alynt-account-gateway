<?php
/**
 * Admin bootstrap.
 *
 * @package Alynt_Account_Gateway
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Registers admin hooks.
 */
class ALYNT_AG_Admin {

	/**
	 * Register hooks.
	 *
	 * @return void
	 */
	public function register() {
		$settings_page = new ALYNT_AG_Settings_Page();
		$settings_page->register();

		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );
	}

	/**
	 * Enqueue admin assets.
	 *
	 * @param string $hook_suffix Admin page hook.
	 * @return void
	 */
	public function enqueue_assets( $hook_suffix ) {
		if ( 'settings_page_alynt-account-gateway' !== $hook_suffix ) {
			return;
		}

		wp_enqueue_media();

		$asset_path = ALYNT_AG_PLUGIN_DIR . 'assets/dist/admin/index.css';
		if ( file_exists( $asset_path ) ) {
			wp_enqueue_style(
				'alynt-ag-admin',
				ALYNT_AG_PLUGIN_URL . 'assets/dist/admin/index.css',
				array(),
				filemtime( $asset_path )
			);
		}

		$script_path = ALYNT_AG_PLUGIN_DIR . 'assets/dist/admin/index.js';
		if ( file_exists( $script_path ) ) {
			wp_enqueue_script(
				'alynt-ag-admin',
				ALYNT_AG_PLUGIN_URL . 'assets/dist/admin/index.js',
				array(),
				filemtime( $script_path ),
				true
			);

			wp_localize_script(
				'alynt-ag-admin',
				'alyntAgAdmin',
				array(
					'selectImage'          => __( 'Select Image', 'alynt-account-gateway' ),
					'useImage'             => __( 'Use Image', 'alynt-account-gateway' ),
					'imageSelected'        => __( 'Image selected.', 'alynt-account-gateway' ),
					'imageRemoved'         => __( 'Image removed.', 'alynt-account-gateway' ),
					'dashboardLinkAdded'   => __( 'Dashboard link added.', 'alynt-account-gateway' ),
					'dashboardLinkRemoved' => __( 'Dashboard link removed.', 'alynt-account-gateway' ),
				)
			);
		}
	}
}
