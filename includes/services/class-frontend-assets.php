<?php
/**
 * Frontend gateway asset loader.
 *
 * @package Alynt_Account_Gateway
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Enqueues frontend gateway assets.
 */
class ALYNT_AG_Frontend_Assets {

	/**
	 * Enqueue frontend assets for a resolved gateway screen.
	 *
	 * @param array<string,mixed> $settings Settings.
	 * @param string              $screen   Gateway screen.
	 * @return void
	 */
	public function enqueue( $settings, $screen ) {
		if ( empty( $settings['frontend_enabled'] ) || ! $screen ) {
			return;
		}

		$this->enqueue_frontend_style();
		$this->enqueue_frontend_script();

		if ( ! empty( $settings['turnstile_site_key'] ) && 'register' === $screen ) {
			$this->enqueue_turnstile_script();
		}
	}

	/**
	 * Enqueue the frontend stylesheet when built.
	 *
	 * @return void
	 */
	private function enqueue_frontend_style() {
		$asset_path = ALYNT_AG_PLUGIN_DIR . 'assets/dist/frontend/index.css';
		if ( ! file_exists( $asset_path ) ) {
			return;
		}

		wp_enqueue_style(
			'alynt-ag-frontend',
			ALYNT_AG_PLUGIN_URL . 'assets/dist/frontend/index.css',
			array(),
			filemtime( $asset_path )
		);
	}

	/**
	 * Enqueue the frontend script and localized labels when built.
	 *
	 * @return void
	 */
	private function enqueue_frontend_script() {
		$script_path = ALYNT_AG_PLUGIN_DIR . 'assets/dist/frontend/index.js';
		if ( ! file_exists( $script_path ) ) {
			return;
		}

		wp_enqueue_script(
			'alynt-ag-frontend',
			ALYNT_AG_PLUGIN_URL . 'assets/dist/frontend/index.js',
			array(),
			filemtime( $script_path ),
			true
		);

		wp_localize_script(
			'alynt-ag-frontend',
			'alyntAgFrontend',
			array(
				'labels' => array(
					'showPassword'    => __( 'Show password', 'alynt-account-gateway' ),
					'hidePassword'    => __( 'Hide password', 'alynt-account-gateway' ),
					'passwordVisible' => __( 'Password is visible.', 'alynt-account-gateway' ),
					'passwordHidden'  => __( 'Password is hidden.', 'alynt-account-gateway' ),
					'show'            => __( 'Show', 'alynt-account-gateway' ),
					'hide'            => __( 'Hide', 'alynt-account-gateway' ),
				),
			)
		);
	}

	/**
	 * Enqueue Cloudflare Turnstile for registration screens.
	 *
	 * @return void
	 */
	private function enqueue_turnstile_script() {
		wp_enqueue_script(
			'alynt-ag-turnstile',
			'https://challenges.cloudflare.com/turnstile/v0/api.js',
			array(),
			null, // phpcs:ignore WordPress.WP.EnqueuedResourceParameters.MissingVersion -- Cloudflare warns when API URL receives a WordPress version query.
			true
		);
	}
}
