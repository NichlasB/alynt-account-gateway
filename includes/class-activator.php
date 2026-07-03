<?php
/**
 * Activation tasks.
 *
 * @package Alynt_Account_Gateway
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Runs on plugin activation.
 */
class ALYNT_AG_Activator {

	/**
	 * Activate the plugin.
	 *
	 * @return void
	 */
	public static function activate() {
		$defaults = ALYNT_AG_Settings_Schema::defaults();

		if ( false === get_option( 'alynt_ag_settings', false ) ) {
			add_option( 'alynt_ag_settings', $defaults );
		}

		ALYNT_AG_Database::install();

		if ( ! wp_next_scheduled( 'alynt_ag_retention_cleanup' ) ) {
			wp_schedule_event( time() + HOUR_IN_SECONDS, 'daily', 'alynt_ag_retention_cleanup' );
		}

		flush_rewrite_rules();
	}
}
