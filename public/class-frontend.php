<?php
/**
 * Frontend gateway foundation.
 *
 * @package Alynt_Account_Gateway
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Registers frontend hooks.
 */
class ALYNT_AG_Frontend {

	/**
	 * Register hooks.
	 *
	 * @return void
	 */
	public function register() {
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_assets' ) );
		add_filter( 'show_admin_bar', array( $this, 'filter_admin_bar' ) );
		add_action( 'admin_init', array( $this, 'maybe_block_wp_admin' ) );
	}

	/**
	 * Enqueue frontend assets only when frontend output is enabled.
	 *
	 * @return void
	 */
	public function enqueue_assets() {
		$settings = ALYNT_AG_Settings_Schema::get_settings();

		if ( empty( $settings['frontend_enabled'] ) ) {
			return;
		}

		$asset_path = ALYNT_AG_PLUGIN_DIR . 'assets/dist/frontend/index.css';
		if ( file_exists( $asset_path ) ) {
			wp_enqueue_style(
				'alynt-ag-frontend',
				ALYNT_AG_PLUGIN_URL . 'assets/dist/frontend/index.css',
				array(),
				filemtime( $asset_path )
			);
		}

		$script_path = ALYNT_AG_PLUGIN_DIR . 'assets/dist/frontend/index.js';
		if ( file_exists( $script_path ) ) {
			wp_enqueue_script(
				'alynt-ag-frontend',
				ALYNT_AG_PLUGIN_URL . 'assets/dist/frontend/index.js',
				array(),
				filemtime( $script_path ),
				true
			);
		}
	}

	/**
	 * Restrict admin toolbar to administrators and shop managers.
	 *
	 * @param bool $show Whether to show toolbar.
	 * @return bool
	 */
	public function filter_admin_bar( $show ) {
		if ( ! is_user_logged_in() ) {
			return $show;
		}

		// phpcs:ignore WordPress.WP.Capabilities.Unknown -- WooCommerce registers this capability for shop managers.
		return current_user_can( 'manage_options' ) || current_user_can( 'manage_woocommerce' );
	}

	/**
	 * Block wp-admin access for customers and other non-admin roles.
	 *
	 * @return void
	 */
	public function maybe_block_wp_admin() {
		if ( wp_doing_ajax() || ! is_user_logged_in() ) {
			return;
		}

		// phpcs:ignore WordPress.WP.Capabilities.Unknown -- WooCommerce registers this capability for shop managers.
		if ( current_user_can( 'manage_options' ) || current_user_can( 'manage_woocommerce' ) ) {
			return;
		}

		$settings = ALYNT_AG_Settings_Schema::get_settings();
		wp_safe_redirect( home_url( $settings['after_login_redirect'] ) );
		exit;
	}
}
