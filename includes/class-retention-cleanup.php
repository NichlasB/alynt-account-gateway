<?php
/**
 * Retention cleanup.
 *
 * @package Alynt_Account_Gateway
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Removes expired plugin-owned records.
 */
class ALYNT_AG_Retention_Cleanup {

	/**
	 * Register hooks.
	 *
	 * @return void
	 */
	public function register() {
		add_action( 'alynt_ag_retention_cleanup', array( $this, 'run' ) );
	}

	/**
	 * Run cleanup.
	 *
	 * @return void
	 */
	public function run() {
		global $wpdb;

		$settings = ALYNT_AG_Settings_Schema::get_settings();
		$tables   = ALYNT_AG_Database::tables();
		$now      = current_time( 'mysql', true );

		// phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Plugin-owned retention cleanup tables require dynamic table names.
		$wpdb->query(
			$wpdb->prepare(
				"DELETE FROM {$tables['pending_registrations']} WHERE expires_at < %s",
				$now
			)
		);

		$webhook_success_days = max( 1, absint( $settings['success_log_retention'] ) );
		$webhook_failed_days  = max( 1, absint( $settings['failed_log_retention'] ) );
		$verification_days    = max( 1, absint( $settings['verification_log_retention'] ) );
		$diagnostics_days     = max( 1, absint( $settings['diagnostics_retention'] ) );
		$consent_days         = max( 1, absint( $settings['consent_record_retention'] ) );
		$audit_days           = max( 1, absint( $settings['audit_log_retention'] ) );

		$wpdb->query(
			$wpdb->prepare(
				"DELETE FROM {$tables['webhook_logs']} WHERE success = 1 AND created_at < DATE_SUB(%s, INTERVAL %d DAY)",
				$now,
				$webhook_success_days
			)
		);

		$wpdb->query(
			$wpdb->prepare(
				"DELETE FROM {$tables['webhook_logs']} WHERE success = 0 AND created_at < DATE_SUB(%s, INTERVAL %d DAY)",
				$now,
				$webhook_failed_days
			)
		);

		$wpdb->query(
			$wpdb->prepare(
				"DELETE FROM {$tables['verification_logs']} WHERE created_at < DATE_SUB(%s, INTERVAL %d DAY)",
				$now,
				$verification_days
			)
		);

		$wpdb->query(
			$wpdb->prepare(
				"DELETE FROM {$tables['diagnostics_logs']} WHERE created_at < DATE_SUB(%s, INTERVAL %d DAY)",
				$now,
				$diagnostics_days
			)
		);

		$wpdb->query(
			$wpdb->prepare(
				"DELETE FROM {$tables['consent_records']} WHERE created_at < DATE_SUB(%s, INTERVAL %d DAY)",
				$now,
				$consent_days
			)
		);

		$wpdb->query(
			$wpdb->prepare(
				"DELETE FROM {$tables['audit_logs']} WHERE created_at < DATE_SUB(%s, INTERVAL %d DAY)",
				$now,
				$audit_days
			)
		);
		// phpcs:enable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
	}
}
