<?php
/**
 * Internationalization.
 *
 * @package Alynt_Account_Gateway
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Loads translations.
 */
class ALYNT_AG_I18n {

	/**
	 * Register hooks.
	 *
	 * @return void
	 */
	public function register() {
		add_action( 'plugins_loaded', array( $this, 'load_textdomain' ) );
	}

	/**
	 * Load plugin textdomain.
	 *
	 * @return void
	 */
	public function load_textdomain() {
		load_plugin_textdomain(
			ALYNT_AG_TEXT_DOMAIN,
			false,
			dirname( ALYNT_AG_PLUGIN_BASENAME ) . '/languages/'
		);
	}
}
