<?php
/**
 * Deactivation tasks.
 *
 * @package Alynt_Account_Gateway
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Runs on plugin deactivation.
 */
class ALYNT_AG_Deactivator {

	/**
	 * Deactivate the plugin.
	 *
	 * @return void
	 */
	public static function deactivate() {
		$timestamp = wp_next_scheduled( 'alynt_ag_retention_cleanup' );

		if ( $timestamp ) {
			wp_unschedule_event( $timestamp, 'alynt_ag_retention_cleanup' );
		}

		wp_clear_scheduled_hook( ALYNT_AG_Webhook_Dispatcher::RETRY_HOOK );
		flush_rewrite_rules();
	}
}
