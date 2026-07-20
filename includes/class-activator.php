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
	 * @param bool $network_wide Whether WordPress requested network activation.
	 * @return void
	 */
	public static function activate( $network_wide = false ) {
		if ( $network_wide ) {
			wp_die(
				esc_html__( 'Alynt Account Gateway must be activated separately on each site in a multisite network.', 'alynt-account-gateway' )
			);
		}

		$defaults = ALYNT_AG_Settings_Schema::defaults();
		$created  = false;

		if ( false === get_option( 'alynt_ag_settings', false ) ) {
			$created = add_option( 'alynt_ag_settings', $defaults );
		}

		if ( ! ALYNT_AG_Database::install() ) {
			if ( $created ) {
				delete_option( 'alynt_ag_settings' );
			}
			wp_die(
				esc_html__( 'Alynt Account Gateway could not create or update its database tables. Check the database permissions and try again.', 'alynt-account-gateway' )
			);
		}

		if ( ! wp_next_scheduled( 'alynt_ag_retention_cleanup' ) ) {
			wp_schedule_event( time() + HOUR_IN_SECONDS, 'daily', 'alynt_ag_retention_cleanup' );
		}

		flush_rewrite_rules();
	}
}
